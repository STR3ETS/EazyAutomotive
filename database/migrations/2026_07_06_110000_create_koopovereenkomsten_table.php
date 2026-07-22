<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Koop-/verkoopovereenkomsten. Koper- en voertuiggegevens worden als momentopname
 * (snapshot) opgeslagen, zodat het document stabiel blijft ook als de auto of de
 * klant later gewijzigd wordt.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('koopovereenkomsten', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('car_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();

            $table->string('nummer')->nullable();
            $table->unsignedBigInteger('verkoopprijs')->default(0); // centen
            $table->string('btw_type', 10)->default('marge');       // marge | btw

            $table->string('inruil_omschrijving')->nullable();
            $table->unsignedBigInteger('inruil_bedrag')->nullable(); // centen

            $table->date('leverdatum')->nullable();
            $table->string('garantie')->nullable();
            $table->text('bijzonderheden')->nullable();

            $table->json('koper')->nullable();     // snapshot koper
            $table->json('voertuig')->nullable();  // snapshot voertuig

            $table->string('status', 20)->default('definitief');
            $table->timestamps();

            $table->index(['company_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('koopovereenkomsten');
    }
};
