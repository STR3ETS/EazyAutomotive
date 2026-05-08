<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_publications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained()->cascadeOnDelete();
            $table->foreignId('platform_connection_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('pending');
            $table->string('external_id')->nullable();
            $table->string('external_url')->nullable();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('unpublished_at')->nullable();
            $table->timestamps();

            $table->unique(['car_id', 'platform_connection_id']);
            $table->index(['company_id', 'status']);
            $table->index(['car_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_publications');
    }
};
