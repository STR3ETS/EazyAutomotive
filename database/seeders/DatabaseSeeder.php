<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a test company
        $company = Company::create([
            'name' => 'Demo Autobedrijf',
            'email' => 'info@demo-auto.nl',
            'phone' => '020-1234567',
            'address' => 'Autoweg 1',
            'city' => 'Amsterdam',
            'postal_code' => '1012AB',
            'website' => 'https://demo-auto.nl',
            'kvk_number' => '12345678',
            'embed_settings' => [
                'primary_color' => '#4f46e5',
                'columns' => 3,
                'show_price' => true,
                'show_km' => true,
                'show_fuel' => true,
            ],
        ]);

        // Create test user (owner)
        User::create([
            'name' => 'Test Gebruiker',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'company_id' => $company->id,
            'role' => 'owner',
        ]);

        // Create some sample cars
        $cars = [
            [
                'kenteken' => '1ABC23',
                'merk' => 'BMW',
                'handelsbenaming' => '3-SERIE',
                'voertuigsoort' => 'Personenauto',
                'inrichting' => 'sedan',
                'eerste_kleur' => 'Zwart',
                'bouwjaar' => 2020,
                'brandstof_omschrijving' => 'Benzine',
                'aantal_zitplaatsen' => 5,
                'aantal_deuren' => 4,
                'cilinderinhoud' => 1998,
                'prijs' => 2999500, // €29.995
                'kilometerstand' => 45000,
                'status' => 'active',
                'beschrijving' => 'Nette BMW 3-Serie in goede staat. Volledig dealer onderhouden.',
                'extra_opties' => ['Navigatie', 'LED koplampen', 'Stoelverwarming', 'Parkeersensoren'],
            ],
            [
                'kenteken' => '2DEF45',
                'merk' => 'VOLKSWAGEN',
                'handelsbenaming' => 'GOLF',
                'voertuigsoort' => 'Personenauto',
                'inrichting' => 'hatchback',
                'eerste_kleur' => 'Wit',
                'bouwjaar' => 2021,
                'brandstof_omschrijving' => 'Diesel',
                'aantal_zitplaatsen' => 5,
                'aantal_deuren' => 5,
                'cilinderinhoud' => 1968,
                'prijs' => 2249500, // €22.495
                'kilometerstand' => 62000,
                'status' => 'active',
                'beschrijving' => 'Volkswagen Golf in topstaat. Zuinig in verbruik.',
                'extra_opties' => ['Airco', 'Cruise control', 'Bluetooth'],
            ],
            [
                'kenteken' => '3GHI67',
                'merk' => 'AUDI',
                'handelsbenaming' => 'A4',
                'voertuigsoort' => 'Personenauto',
                'inrichting' => 'stationwagen',
                'eerste_kleur' => 'Grijs',
                'bouwjaar' => 2019,
                'brandstof_omschrijving' => 'Benzine',
                'aantal_zitplaatsen' => 5,
                'aantal_deuren' => 5,
                'cilinderinhoud' => 1984,
                'prijs' => 2649900, // €26.499
                'kilometerstand' => 78000,
                'status' => 'active',
                'is_featured' => true,
                'beschrijving' => 'Audi A4 Avant met uitgebreide optielijst.',
                'extra_opties' => ['Leder', 'Navigatie', 'Panoramadak', 'Trekhaak', 'LED'],
            ],
            [
                'kenteken' => '4JKL89',
                'merk' => 'TOYOTA',
                'handelsbenaming' => 'YARIS',
                'voertuigsoort' => 'Personenauto',
                'inrichting' => 'hatchback',
                'eerste_kleur' => 'Rood',
                'bouwjaar' => 2022,
                'brandstof_omschrijving' => 'Hybride',
                'aantal_zitplaatsen' => 5,
                'aantal_deuren' => 5,
                'cilinderinhoud' => 1490,
                'prijs' => 1899500, // €18.995
                'kilometerstand' => 25000,
                'status' => 'active',
                'beschrijving' => 'Toyota Yaris Hybrid. Zeer zuinig!',
                'extra_opties' => ['Achteruitrijcamera', 'Apple CarPlay', 'Lane assist'],
            ],
            [
                'kenteken' => '5MNO12',
                'merk' => 'MERCEDES-BENZ',
                'handelsbenaming' => 'C-KLASSE',
                'voertuigsoort' => 'Personenauto',
                'inrichting' => 'sedan',
                'eerste_kleur' => 'Blauw',
                'bouwjaar' => 2018,
                'brandstof_omschrijving' => 'Diesel',
                'aantal_zitplaatsen' => 5,
                'aantal_deuren' => 4,
                'cilinderinhoud' => 2143,
                'prijs' => 2399500, // €23.995
                'kilometerstand' => 95000,
                'status' => 'reserved',
                'beschrijving' => 'Mercedes-Benz C-Klasse. Luxe uitstraling.',
                'extra_opties' => ['Leder', 'Navigatie', 'Stoelverwarming', 'Dodehoek assistent'],
            ],
        ];

        foreach ($cars as $carData) {
            $company->cars()->create($carData);
        }
    }
}
