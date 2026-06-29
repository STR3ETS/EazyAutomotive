<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('iban', 34)->nullable()->after('btw_number');
            $table->string('invoice_prefix', 12)->nullable()->after('iban');
            $table->unsignedSmallInteger('invoice_payment_terms')->default(14)->after('invoice_prefix');
            $table->text('invoice_footer')->nullable()->after('invoice_payment_terms');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['iban', 'invoice_prefix', 'invoice_payment_terms', 'invoice_footer']);
        });
    }
};
