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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete()->cascadeOnUpdate();
            $table->string('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->nullOnDelete()->cascadeOnUpdate();
            $table->decimal('price')->nullable();
            $table->integer('stock')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
