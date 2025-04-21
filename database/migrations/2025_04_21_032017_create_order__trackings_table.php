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
        Schema::create('order__trackings', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();
            $table->foreign('order_id')->references('id')->on('orders')->nullOnDelete()->cascadeOnUpdate();
            $table->string('tracking_number')->unique();
            $table->string('courier')->nullable();
            $table->string('status')->nullable();
            $table->string('shipping_address')->nullable();
            $table->string('estimated_delivery_date')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order__trackings');
    }
};
