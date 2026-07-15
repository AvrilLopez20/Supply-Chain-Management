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
        Schema::create('historical_sales', function (Blueprint $table) {
            $table->id();
            $table->string('order_id'); // e.g. ORD-001
            $table->string('product_name');
            $table->string('sku');
            $table->string('category');
            $table->string('date');
            $table->integer('qty');
            $table->decimal('revenue', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historical_sales');
    }
};
