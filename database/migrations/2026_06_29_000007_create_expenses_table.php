<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('car_id')->nullable()->constrained()->nullOnDelete();
            $table->date('date');
            $table->string('supplier')->nullable();
            $table->string('description');
            $table->string('category', 40)->default('overig');

            // All money in cents.
            $table->bigInteger('amount_excl')->default(0);
            $table->unsignedSmallInteger('vat_rate')->default(21);
            $table->bigInteger('vat_amount')->default(0);
            $table->bigInteger('amount_incl')->default(0);

            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'date']);
            $table->index(['company_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
