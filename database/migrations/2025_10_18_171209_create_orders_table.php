<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            // Foreign key to link with the currencies table
            $table->foreignId('currency_id')->constrained('currencies');
            $table->decimal('foreign_currency_amount', 15, 4);
            $table->decimal('exchange_rate_at_purchase', 15, 8);
            $table->decimal('surcharge_percentage_at_purchase', 5, 2);
            $table->decimal('discount_percentage_at_purchase', 5, 2)->default(0.00);
            $table->decimal('surcharge_amount_in_zar', 15, 4);
            $table->decimal('discount_amount_in_zar', 15, 4)->default(0.00);
            $table->decimal('total_zar_amount_paid', 15, 4);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
