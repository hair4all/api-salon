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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('client_id')->nullable();
            $table->foreign('client_id')->references('id')->on('clients')->nullOnDelete()->cascadeOnUpdate();
            $table->string('worker_id')->nullable();
            $table->foreign('worker_id')->references('id')->on('workers')->nullOnDelete()->cascadeOnUpdate();
            $table->date('transaction_date')->nullable();
            $table->decimal('total_price')->nullable();
            $table->string('payment_method_id')->nullable();
            $table->foreign('payment_method_id')->references('id')->on('payment__methods')->nullOnDelete()->cascadeOnUpdate();
            // $table->integer('discount')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
