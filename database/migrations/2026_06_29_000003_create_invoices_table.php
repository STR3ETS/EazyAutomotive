<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('car_id')->nullable()->constrained()->nullOnDelete();

            $table->string('number')->nullable();           // assigned when finalised (sent)
            $table->unsignedInteger('sequence')->nullable(); // gap-free per company per year
            $table->unsignedSmallInteger('year')->nullable();

            $table->string('status', 16)->default('concept'); // concept, verzonden, betaald, deels_betaald, vervallen, geannuleerd
            $table->string('vat_scheme', 8)->default('btw');  // btw, marge
            $table->boolean('prices_include_vat')->default(true);

            $table->date('date');
            $table->date('due_date')->nullable();

            // All money in cents.
            $table->bigInteger('subtotal')->default(0);    // excl btw (btw) or gross (marge)
            $table->bigInteger('vat_amount')->default(0);
            $table->bigInteger('total')->default(0);
            $table->bigInteger('margin_base')->default(0); // marge grondslag (verkoop - inkoop)
            $table->bigInteger('amount_paid')->default(0);

            $table->text('notes')->nullable();
            $table->text('footer')->nullable();

            // Immutable snapshot of who the invoice was billed to.
            $table->string('bill_to_name')->nullable();
            $table->text('bill_to_address')->nullable();

            $table->timestamp('sent_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'year', 'sequence']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
