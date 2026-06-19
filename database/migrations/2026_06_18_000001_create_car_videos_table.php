<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('car_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('queued'); // queued, in_progress, completed, failed
            $table->text('prompt');
            $table->string('model', 40)->default('dop-turbo');
            $table->string('motion_id')->nullable();
            $table->string('source_image_url', 1024);
            $table->string('request_id')->nullable();
            $table->string('video_url', 1024)->nullable();
            $table->string('thumbnail_url', 1024)->nullable();
            $table->text('error')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'car_id']);
            $table->index('request_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_videos');
    }
};
