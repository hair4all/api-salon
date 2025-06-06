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
        Schema::create('item__solds', function (Blueprint $table) {
            $table->id();
            $table->string('inventory_id')->nullable();
            $table->foreign('inventory_id')->references('id')->on('inventories')->nullOnDelete()->cascadeOnUpdate();
            $table->integer('quantity')->nullable();
            $table->date('sold_date')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item__solds');
    }
};
