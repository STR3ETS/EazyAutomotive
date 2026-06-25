<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('studio_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('queued'); // queued, in_progress, completed, failed
            $table->text('prompt');
            $table->string('model', 40)->default('seedance-2.0');
            $table->unsignedTinyInteger('image_count')->default(0);
            $table->string('request_id')->nullable();
            $table->string('result_url', 512)->nullable();
            $table->string('video_url', 1024)->nullable();
            $table->string('thumbnail_url', 1024)->nullable();
            $table->text('error')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('studio_videos');
    }
};
