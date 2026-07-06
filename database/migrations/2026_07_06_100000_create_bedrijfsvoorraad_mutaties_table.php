<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Audit-trail van RDW-registermutaties (vrijwaring / bedrijfsvoorraad). Bewust
 * GEEN tenaamstellingscode opgeslagen: die is gevoelig als een pincode en wordt
 * alleen tijdens de transactie gebruikt, nooit bewaard.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bedrijfsvoorraad_mutaties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('car_id')->nullable()->constrained()->nullOnDelete();

            $table->string('type', 20);          // vrijwaring | uitvoorraad
            $table->string('kenteken', 12);
            $table->string('status', 20);         // geslaagd | mislukt
            $table->string('mode', 20);           // sandbox | soap (welke omgeving deed de mutatie)

            $table->string('vrijwaringsbewijs')->nullable();
            $table->timestamp('bewijs_datum')->nullable();
            $table->string('referentie')->nullable(); // RDW-transactie/kenmerk
            $table->text('foutmelding')->nullable();

            $table->timestamps();

            $table->index(['company_id', 'created_at']);
            $table->index('kenteken');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bedrijfsvoorraad_mutaties');
    }
};
