<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Test-drive (proefrit) requests submitted by customers through the embeddable
 * widget on the dealer's own website. They land here as leads for the dealer.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proefrit_aanvragen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('car_id')->nullable()->constrained()->nullOnDelete();
            $table->string('naam');
            $table->string('email');
            $table->string('telefoon');
            $table->date('gewenste_datum')->nullable();
            $table->text('bericht')->nullable();
            $table->string('status')->default('nieuw');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'created_at']);
            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proefrit_aanvragen');
    }
};
