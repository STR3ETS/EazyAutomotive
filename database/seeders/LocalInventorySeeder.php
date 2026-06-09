<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\Company;
use App\Services\RdwService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

/**
 * Fills the local inventory with REAL vehicles pulled from the RDW open data
 * (genuine kentekens/specs), enriched with realistic dealer fields. Run with:
 *   php artisan db:seed --class=LocalInventorySeeder
 */
class LocalInventorySeeder extends Seeder
{
    /** RDW vehicle dataset (voertuigen). */
    private const RDW_VOERTUIGEN = 'https://opendata.rdw.nl/resource/m9d7-ebf2.json';

    /** Brands to pull, with how many of each. */
    private const BRANDS = [
        'VOLKSWAGEN' => 3, 'BMW' => 3, 'AUDI' => 3, 'TOYOTA' => 2, 'VOLVO' => 2,
        'RENAULT' => 2, 'PEUGEOT' => 2, 'KIA' => 2, 'FORD' => 2, 'MERCEDES-BENZ' => 3,
        'SKODA' => 2, 'OPEL' => 2,
    ];

    private const OPTIONS_POOL = [
        'Navigatiesysteem', 'Airconditioning', 'Climate control', 'Cruise control',
        'Adaptieve cruise control', 'Parkeersensoren', 'Achteruitrijcamera',
        'Stoelverwarming', 'Lederen bekleding', 'Panoramadak', 'LED-koplampen',
        'Lichtmetalen velgen', 'Apple CarPlay', 'Android Auto', 'Trekhaak',
        'Keyless entry', 'Dodehoekdetectie', 'Stop/start-systeem',
    ];

    public function run(): void
    {
        $company = Company::first();

        if (!$company) {
            $this->command->error('Geen bedrijf gevonden. Registreer eerst een account.');
            return;
        }

        $rdw = app(RdwService::class);
        $index = 0;
        $created = 0;

        foreach (self::BRANDS as $brand => $count) {
            $kentekens = $this->fetchKentekens($brand, $count);

            foreach ($kentekens as $kenteken) {
                $data = $rdw->fetchByKenteken($kenteken);
                if (!$data) {
                    continue;
                }

                $attrs = $rdw->mapToCarAttributes($data);
                if (empty($attrs['merk'])) {
                    continue;
                }

                $attrs = array_merge($attrs, $this->dealerFields($attrs, $index));

                Car::updateOrCreate(
                    ['company_id' => $company->id, 'kenteken' => $attrs['kenteken']],
                    array_merge($attrs, ['company_id' => $company->id]),
                );

                $created++;
                $index++;
                $this->command->info("  + {$attrs['merk']} {$attrs['handelsbenaming']} ({$attrs['kenteken']}) [{$attrs['status']}]");
            }
        }

        $this->command->info("Klaar: {$created} auto's in de voorraad van \"{$company->name}\".");
    }

    /**
     * Pull a few real kentekens for a brand from the RDW open data.
     */
    private function fetchKentekens(string $brand, int $count): array
    {
        try {
            $response = Http::timeout(15)->get(self::RDW_VOERTUIGEN, [
                'voertuigsoort' => 'Personenauto',
                'merk' => $brand,
                '$select' => 'kenteken',
                '$where' => "datum_eerste_toelating between '20180101' and '20230101'",
                '$limit' => $count,
                '$order' => 'datum_eerste_toelating DESC',
            ]);

            if ($response->failed()) {
                return [];
            }

            return collect($response->json())
                ->pluck('kenteken')
                ->filter()
                ->values()
                ->all();
        } catch (\Throwable $e) {
            $this->command->warn("  ! Kon geen {$brand} ophalen: {$e->getMessage()}");
            return [];
        }
    }

    /**
     * Realistic dealer-side fields derived from the vehicle's RDW data.
     */
    private function dealerFields(array $attrs, int $index): array
    {
        $year = $attrs['bouwjaar'] ?: 2020;
        $age = max(0, (int) date('Y') - $year);

        // Price: depreciate the catalogue price, or fall back to an age heuristic.
        if (!empty($attrs['catalogusprijs'])) {
            $value = $attrs['catalogusprijs'] * (0.85 ** $age);
        } else {
            $value = 32000 - ($age * 2600);
        }
        $value = max(2950, $value);
        $euros = (int) (round($value / 250) * 250);   // round to nice steps

        // Mileage: ~15k/year with some noise.
        $km = max(500, ($age * 15000) + mt_rand(-6000, 14000));
        $km = (int) (round($km / 500) * 500);

        // Status spread: mostly active, with a few reserved/sold/draft.
        $status = match (true) {
            $index % 11 === 5 => 'reserved',
            $index % 11 === 9 => 'sold',
            $index % 11 === 10 => 'draft',
            default => 'active',
        };

        // A handful of options.
        $pool = self::OPTIONS_POOL;
        shuffle($pool);
        $opties = array_slice($pool, 0, mt_rand(4, 8));

        $merk = $attrs['merk'];
        $model = $attrs['handelsbenaming'] ?: '';
        $brandstof = strtolower($attrs['brandstof_omschrijving'] ?? 'benzine');

        $beschrijving = "Nette {$merk} {$model} uit {$year}, uitgevoerd met een {$brandstof}motor. "
            . "Met " . number_format($km, 0, ',', '.') . " km op de teller en een complete onderhoudshistorie. "
            . "Voorzien van o.a. " . implode(', ', array_slice($opties, 0, 3)) . ". "
            . "Direct rijklaar en inclusief beurt en APK. Bel of mail ons voor een vrijblijvende proefrit!";

        return [
            'titel' => trim("{$merk} {$model}"),
            'prijs' => $euros * 100,            // stored in cents
            'kilometerstand' => $km,
            'status' => $status,
            'is_featured' => $index < 3 && $status === 'active',
            'extra_opties' => array_values($opties),
            'beschrijving' => $beschrijving,
        ];
    }
}
