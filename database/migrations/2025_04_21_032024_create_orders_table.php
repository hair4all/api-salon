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
            $table->string('order_number')->unique()->nullable();
            $table->string('cart_id')->nullable();
            $table->string('shipping_address_id')->nullable();
            $table->foreign('shipping_address_id')->references('id')->on('shipping__addresses')->nullOnDelete()->cascadeOnUpdate();
            $table->integer('total_price')->nullable();
            $table->string('courier')->nullable();
            $table->string('shipping_cost')->nullable();
            $table->string('status')->nullable();
            $table->boolean('is_deleted')->default(false);
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
