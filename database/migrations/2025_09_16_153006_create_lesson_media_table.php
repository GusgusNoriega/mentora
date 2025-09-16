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
        Schema::create('lesson_media', function (Blueprint $table) {
            $table->unsignedBigInteger('lesson_id');
            $table->unsignedBigInteger('media_id');

            $table->primary(['lesson_id', 'media_id']);
            $table->foreign('lesson_id')->references('id')->on('course_lessons')->onDelete('cascade');
            $table->foreign('media_id')->references('id')->on('media_assets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_media');
    }
};
