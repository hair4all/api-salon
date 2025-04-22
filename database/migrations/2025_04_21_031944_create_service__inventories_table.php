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
        Schema::create('service__inventories', function (Blueprint $table) {
            $table->id();
            $table->string('inventory_id')->nullable();
            $table->foreign('inventory_id')->references('id')->on('inventories')->nullOnDelete()->cascadeOnUpdate();
            $table->string('service_id')->nullable();
            $table->foreign('service_id')->references('id')->on('services')->nullOnDelete()->cascadeOnUpdate();
            $table->string('quantity')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service__inventories');
    }
};
