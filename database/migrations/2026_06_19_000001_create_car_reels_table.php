<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_reels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('car_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('processing'); // processing, completed, failed
            $table->string('path', 1024)->nullable();            // storage path of the stitched mp4
            $table->unsignedSmallInteger('clip_count')->default(0);
            $table->text('error')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'car_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_reels');
    }
};
