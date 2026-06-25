<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('car_videos', function (Blueprint $table) {
            // fal returns the canonical response_url (base for status + result).
            // We store it verbatim because the queue path differs from the submit path.
            $table->string('result_url', 512)->nullable()->after('request_id');
        });
    }

    public function down(): void
    {
        Schema::table('car_videos', function (Blueprint $table) {
            $table->dropColumn('result_url');
        });
    }
};
