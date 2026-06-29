<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 12)->default('particulier'); // particulier, zakelijk
            $table->string('naam');
            $table->string('bedrijfsnaam')->nullable();
            $table->string('email')->nullable();
            $table->string('telefoon', 40)->nullable();
            $table->string('adres')->nullable();
            $table->string('postcode', 12)->nullable();
            $table->string('plaats')->nullable();
            $table->string('land')->default('Nederland');
            $table->string('kvk_nummer', 20)->nullable();
            $table->string('btw_nummer', 20)->nullable();
            $table->text('notities')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
