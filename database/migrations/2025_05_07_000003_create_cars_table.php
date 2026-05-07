<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            // RDW Core Data
            $table->string('kenteken', 8)->index();
            $table->string('merk', 100)->nullable();
            $table->string('handelsbenaming', 100)->nullable();
            $table->string('voertuigsoort', 50)->nullable();
            $table->string('inrichting', 50)->nullable();
            $table->string('eerste_kleur', 50)->nullable();
            $table->string('tweede_kleur', 50)->nullable();
            $table->unsignedSmallInteger('aantal_cilinders')->nullable();
            $table->unsignedInteger('cilinderinhoud')->nullable();
            $table->unsignedInteger('vermogen')->nullable();
            $table->unsignedInteger('massa_rijklaar')->nullable();
            $table->unsignedInteger('massa_ledig_voertuig')->nullable();
            $table->unsignedInteger('toegestane_maximum_massa_voertuig')->nullable();
            $table->unsignedSmallInteger('aantal_zitplaatsen')->nullable();
            $table->unsignedSmallInteger('aantal_deuren')->nullable();
            $table->unsignedSmallInteger('aantal_wielen')->nullable();
            $table->unsignedInteger('wielbasis')->nullable();
            $table->date('datum_eerste_toelating')->nullable();
            $table->date('datum_eerste_tenaamstelling_in_nederland')->nullable();
            $table->date('vervaldatum_apk')->nullable();
            $table->unsignedInteger('catalogusprijs')->nullable();
            $table->string('europese_voertuigcategorie', 10)->nullable();
            $table->string('typegoedkeuringsnummer', 50)->nullable();
            $table->string('zuinigheidsclassificatie', 5)->nullable();
            $table->unsignedInteger('bruto_bpm')->nullable();

            // RDW Fuel Data
            $table->string('brandstof_omschrijving', 50)->nullable();
            $table->string('uitlaatemissieniveau', 20)->nullable();

            // RDW Status Flags
            $table->boolean('wam_verzekerd')->default(false);
            $table->boolean('export_indicator')->default(false);
            $table->string('tellerstandoordeel', 50)->nullable();
            $table->json('rdw_raw_data')->nullable();

            // Dealer-Added Fields
            $table->string('titel', 255)->nullable();
            $table->text('beschrijving')->nullable();
            $table->unsignedInteger('prijs')->nullable();
            $table->unsignedTinyInteger('prijs_type')->default(0);
            $table->unsignedInteger('kilometerstand')->nullable();
            $table->unsignedSmallInteger('bouwjaar')->nullable();
            $table->string('status', 20)->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->json('extra_opties')->nullable();
            $table->json('custom_fields')->nullable();

            // Stats
            $table->unsignedInteger('view_count')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'merk']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
