<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('car_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 20)->default('contact');   // proefrit, contact, inruil, financiering, overig
            $table->string('naam');
            $table->string('email')->nullable();
            $table->string('telefoon')->nullable();
            $table->text('bericht')->nullable();
            $table->string('status', 20)->default('nieuw');    // nieuw, contact, afspraak, gewonnen, verloren
            $table->string('source', 40)->nullable();          // widget, handmatig, proefrit, website
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('follow_up_at')->nullable();
            $table->dateTime('last_contacted_at')->nullable();
            $table->text('notes')->nullable();
            $table->json('data')->nullable();                  // type-specific extras (gewenste_datum, kenteken, ...)
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
