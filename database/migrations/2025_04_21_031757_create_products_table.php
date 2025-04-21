<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('inventory_id')->unique();
            $table->foreign('inventory_id')->references('id')->on('inventories')->nullOnDelete()->cascadeOnUpdate();
            $table->string('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->nullOnDelete()->cascadeOnUpdate();
            $table->string('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete()->cascadeOnUpdate();
            $table->integer('discount')->nullable();
            $table->date('expiry_discount_date')->nullable();
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
        Schema::dropIfExists('products');
    }
};
