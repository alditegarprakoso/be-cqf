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
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id'); // unsignedBigInteger() untuk foreign key
            $table->string('title');
            $table->string('description');
            $table->string('target_amount');
            $table->string('bank_account');
            $table->string('status');
            $table->string('thumbnail');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('category_id')->references('id')->on('donation_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
