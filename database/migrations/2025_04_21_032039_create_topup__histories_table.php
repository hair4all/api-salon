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
        Schema::create('topup__histories', function (Blueprint $table) {
            $table->id();
            $table->string('client_id')->nullable();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->string('amount')->nullable();
            $table->date('topup_date')->nullable();
            $table->string('payment_method_id')->nullable();
            $table->foreign('payment_method_id')->references('id')->on('payment__methods')->onDelete('cascade');
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topup__histories');
    }
};
