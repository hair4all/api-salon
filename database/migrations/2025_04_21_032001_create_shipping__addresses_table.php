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
        Schema::create('shipping__addresses', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('members')->nullOnDelete()->cascadeOnUpdate();
            $table->string('recipient_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('province_id')->nullable();
            $table->foreign('province_id')->references('id')->on('provinces')->nullOnDelete()->cascadeOnUpdate();
            $table->string('city_id')->nullable();
            $table->foreign('city_id')->references('id')->on('cities')->nullOnDelete()->cascadeOnUpdate();
            $table->string('district_id')->nullable();
            $table->foreign('district_id')->references('id')->on('districts')->nullOnDelete()->cascadeOnUpdate();
            $table->text('address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping__addresses');
    }
};
