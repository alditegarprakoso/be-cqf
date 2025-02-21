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
        Schema::create('donature_lists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('donation_id');
            $table->string('donature_name');
            $table->string('email');
            $table->string('phone');
            $table->string('total_donation');
            $table->string('info')->nullable();
            $table->string('status')->nullable();
            $table->string('attachment')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('donation_id')->references('id')->on('donations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donature_lists', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
