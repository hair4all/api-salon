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
        Schema::create('service__solds', function (Blueprint $table) {
            $table->id();
            $table->string('service_id')->nullable();
            $table->foreign('service_id')->references('id')->on('services')->nullOnDelete()->cascadeOnUpdate();

            
            $table->string('client_id')->nullable();
            $table->foreign('client_id')->references('id')->on('clients')->nullOnDelete()->cascadeOnUpdate();
            
            $table->string('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete()->cascadeOnUpdate();

            $table->string('worker_id')->nullable();
            $table->foreign('worker_id')->references('id')->on('workers')->nullOnDelete()->cascadeOnUpdate();

            $table->string('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders')->nullOnDelete()->cascadeOnUpdate();
            
            $table->date('date')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service__solds');
    }
};
