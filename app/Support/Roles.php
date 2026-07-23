<?php

namespace App\Support;

/**
 * Central definition of the team roles and what each role may reach. Access is
 * organised per functional AREA (voorraad, marketing, ...). A role grants a set
 * of areas; every authenticated route belongs to at most one area (resolved from
 * its route name). Routes without an area (dashboard, profiel, AI) are open to
 * every team member.
 */
class Roles
{
    /** The role that owns the company. Created on registration, never assignable. */
    public const OWNER = 'owner';

    /**
     * Role => presentation. Order is the order shown in the UI.
     *
     * @var array<string,array{label:string,description:string,icon:string,color:string}>
     */
    public const ROLES = [
        'owner' => [
            'label' => 'Eigenaar',
            'description' => 'Volledige toegang, inclusief team en bedrijfsinstellingen.',
            'icon' => 'fa-crown',
            'color' => 'amber',
        ],
        'admin' => [
            'label' => 'Beheerder',
            'description' => 'Toegang tot alles en kan het team beheren.',
            'icon' => 'fa-user-shield',
            'color' => 'violet',
        ],
        'sales' => [
            'label' => 'Verkoper',
            'description' => 'Voorraad, marketing, verkoop en klanten.',
            'icon' => 'fa-handshake',
            'color' => 'blue',
        ],
        'accountant' => [
            'label' => 'Boekhouder',
            'description' => 'Facturen, kosten, boekhouding en klanten.',
            'icon' => 'fa-calculator',
            'color' => 'emerald',
        ],
    ];

    /** @var array<string,string> area key => label */
    public const AREAS = [
        'voorraad' => 'Voorraad',
        'marketing' => 'Marketing',
        'verkoop' => 'Verkoop',
        'klanten' => 'Klanten',
        'administratie' => 'Administratie',
        'team' => 'Team',
        'instellingen' => 'Instellingen',
    ];

    /** @var array<string,list<string>> role => granted areas */
    private const GRANTS = [
        'owner' => ['voorraad', 'marketing', 'verkoop', 'klanten', 'administratie', 'team', 'instellingen'],
        'admin' => ['voorraad', 'marketing', 'verkoop', 'klanten', 'administratie', 'team', 'instellingen'],
        'sales' => ['voorraad', 'marketing', 'verkoop', 'klanten'],
        'accountant' => ['administratie', 'klanten'],
    ];

    /**
     * First route-name segment => area. Anything not listed here is open to all.
     *
     * @var array<string,string>
     */
    private const ROUTE_AREAS = [
        // Voorraad
        'cars' => 'voorraad',
        'import' => 'voorraad',
        'onderzoek' => 'voorraad',
        'bedrijfsvoorraad' => 'voorraad',
        'rdw' => 'voorraad',
        // Marketing
        'publiceren' => 'marketing',
        'widgets' => 'marketing',
        'studio' => 'marketing',
        'ontwerpen' => 'marketing',
        'integratie' => 'marketing',
        // Verkoop
        'leads' => 'verkoop',
        'proefritten' => 'verkoop',
        'koopovereenkomsten' => 'verkoop',
        // Klanten
        'customers' => 'klanten',
        // Administratie
        'invoices' => 'administratie',
        'expenses' => 'administratie',
        'bookkeeping' => 'administratie',
        // Team & instellingen
        'team' => 'team',
        'settings' => 'instellingen',
    ];

    /**
     * AI-collega tools mapped to a functional area, so the assistant is bound by
     * the same role permissions as the human. Tools not listed are open to all.
     *
     * @var array<string,string>
     */
    private const TOOL_AREAS = [
        // Voorraad
        'zoek_voorraad' => 'voorraad',
        'bekijk_auto' => 'voorraad',
        'voorraad_statistieken' => 'voorraad',
        'rdw_opzoeken' => 'voorraad',
        'taxeer_kenteken' => 'voorraad',
        'voeg_auto_toe' => 'voorraad',
        'wijzig_auto' => 'voorraad',
        'verwijder_auto' => 'voorraad',
        'genereer_advertentietekst' => 'voorraad',
        'importeer_kentekens' => 'voorraad',
        'vrijwaar_auto' => 'voorraad',
        // Marketing
        'pas_huisstijl_toe' => 'marketing',
        'publiceer_auto' => 'marketing',
        'depubliceer_auto' => 'marketing',
        // Verkoop
        'zoek_leads' => 'verkoop',
        'wijzig_lead' => 'verkoop',
        'zoek_proefritten' => 'verkoop',
        'maak_koopovereenkomst' => 'verkoop',
        // Klanten
        'zoek_klanten' => 'klanten',
        'voeg_klant_toe' => 'klanten',
        // Administratie
        'voeg_kosten_toe' => 'administratie',
        'zoek_facturen' => 'administratie',
        'maak_factuur' => 'administratie',
        'verstuur_factuur' => 'administratie',
        'registreer_betaling' => 'administratie',
    ];

    /** Which area an AI tool belongs to, or null when it is open to every role. */
    public static function areaForTool(string $toolName): ?string
    {
        return self::TOOL_AREAS[$toolName] ?? null;
    }

    /** The roles an owner/admin may hand out (never owner). */
    public static function assignableRoles(): array
    {
        return array_filter(
            self::ROLES,
            fn (string $key) => $key !== self::OWNER,
            ARRAY_FILTER_USE_KEY,
        );
    }

    /** @return list<string> the areas a role may reach (unknown roles fall back to verkoper). */
    public static function areasFor(?string $role): array
    {
        return self::GRANTS[$role] ?? self::GRANTS['sales'];
    }

    public static function roleHasArea(?string $role, string $area): bool
    {
        return in_array($area, self::areasFor($role), true);
    }

    /** Which area a route belongs to, or null when it is open to every member. */
    public static function areaForRoute(?string $routeName): ?string
    {
        if (! $routeName) {
            return null;
        }

        $first = explode('.', $routeName)[0];

        return self::ROUTE_AREAS[$first] ?? null;
    }

    public static function label(?string $role): string
    {
        return self::ROLES[$role]['label'] ?? ucfirst((string) $role);
    }

    public static function meta(?string $role): array
    {
        return self::ROLES[$role] ?? self::ROLES['sales'];
    }

    public static function isValid(string $role): bool
    {
        return array_key_exists($role, self::ROLES);
    }
}
