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
        Schema::create('workers', function (Blueprint $table) {
            $table->id();
            $table->string('member_id')->nullable();
            $table->foreign('member_id')->references('id')->on('members')->nullOnDelete()->cascadeOnUpdate();
            $table->string('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete()->cascadeOnUpdate();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('position_id')->nullable();
            $table->foreign('position_id')->references('id')->on('positions')->nullOnDelete()->cascadeOnUpdate();
            $table->string('status')->nullable();
            $table->string('salary')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workers');
    }
};
