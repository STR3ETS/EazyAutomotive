<?php

namespace App\Services\Rdw\Orv;

/**
 * Uitkomst van een RDW ORV-mutatie. Bevat nooit de tenaamstellingscode.
 */
final class OrvResult
{
    public function __construct(
        public readonly bool $geslaagd,
        public readonly ?string $vrijwaringsbewijs = null,
        public readonly ?string $datum = null,       // ISO 8601
        public readonly ?string $referentie = null,  // RDW-transactiekenmerk
        public readonly ?string $foutmelding = null,
    ) {}

    public static function ok(?string $vrijwaringsbewijs, ?string $datum, ?string $referentie = null): self
    {
        return new self(true, $vrijwaringsbewijs, $datum, $referentie);
    }

    public static function fout(string $foutmelding): self
    {
        return new self(false, foutmelding: $foutmelding);
    }
}
