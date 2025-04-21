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
        Schema::create('booking__services', function (Blueprint $table) {
            $table->id();
            $table->string('client_id')->nullable();
            $table->foreign('client_id')
                ->references('id')
                ->on('clients')
                ->nullOnDelete()->cascadeOnUpdate();
            $table->string('service_id')->nullable();
            $table->foreign('service_id')
                ->references('id')
                ->on('services')
                ->nullOnDelete()->cascadeOnUpdate();
            $table->string('branch_id')->nullable();
            $table->foreign('branch_id')
                ->references('id')
                ->on('branches')
                ->nullOnDelete()->cascadeOnUpdate();
            $table->decimal('price')->nullable();
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
        Schema::dropIfExists('booking__services');
    }
};
