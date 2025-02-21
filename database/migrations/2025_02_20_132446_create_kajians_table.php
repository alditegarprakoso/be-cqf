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
        Schema::create('kajians', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('description')->nullable();
            $table->dateTime('datetime');
            $table->string('thumbnail')->nullable();
            $table->boolean('is_live')->nullable();
            $table->string('url')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('category_id')->references('id')->on('kajian_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kajians', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
