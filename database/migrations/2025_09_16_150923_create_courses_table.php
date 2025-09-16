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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary')->nullable();
            $table->text('description')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->string('level')->nullable();
            $table->string('language')->nullable();
            $table->string('status')->default('draft');
            $table->string('access_mode')->default('free');
            $table->integer('price_cents')->default(0);
            $table->char('currency', 3)->default('USD');
            $table->unsignedBigInteger('created_by');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
