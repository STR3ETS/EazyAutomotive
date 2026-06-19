<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Raw market listings used by the valuation engine to derive real used-car
 * values from comparable cars (asking prices aggregated from marketplaces or
 * imported via CSV). The engine queries this table for comparables.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_listings', function (Blueprint $table) {
            $table->id();
            $table->string('merk')->index();
            $table->string('model')->nullable();
            $table->unsignedSmallInteger('bouwjaar')->nullable();
            $table->string('brandstof')->nullable();
            $table->unsignedInteger('kilometerstand')->nullable();
            $table->unsignedInteger('prijs'); // in cents
            $table->string('source')->default('csv');
            $table->string('external_id')->nullable();
            $table->timestamp('captured_at')->nullable();
            $table->timestamps();

            $table->index(['merk', 'bouwjaar']);
            $table->index(['merk', 'model', 'bouwjaar']);
            $table->unique(['source', 'external_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_listings');
    }
};
