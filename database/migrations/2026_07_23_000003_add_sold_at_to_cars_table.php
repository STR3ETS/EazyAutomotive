<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->timestamp('sold_at')->nullable()->after('status');
        });

        // Reeds verkochte auto's een benaderende verkoopdatum geven (laatste
        // wijziging), zodat de doorlooptijd-analyse meteen iets kan tonen.
        DB::table('cars')->where('status', 'sold')->whereNull('sold_at')
            ->update(['sold_at' => DB::raw('updated_at')]);
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn('sold_at');
        });
    }
};
