<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('car_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description');
            $table->decimal('quantity', 8, 2)->default(1);
            $table->bigInteger('unit_price')->default(0);          // cents, as entered
            $table->unsignedSmallInteger('vat_rate')->default(21); // 21, 9, 0
            $table->bigInteger('purchase_price')->nullable();      // cents, for marge margin calc
            $table->bigInteger('line_total')->default(0);          // cents, net (btw) or gross (marge)
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();

            $table->index('invoice_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_lines');
    }
};
