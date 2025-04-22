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
        Schema::create('payment__gateway__responses', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->nullable();
            $table->foreign('transaction_id')->references('id')->on('transactions')->nullOnDelete()->cascadeOnUpdate();
            $table->string('gateway')->nullable();
            $table->string('metadata')->nullable();
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
        Schema::dropIfExists('payment__gateway__responses');
    }
};
