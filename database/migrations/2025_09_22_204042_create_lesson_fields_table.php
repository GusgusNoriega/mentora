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
        Schema::create('lesson_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('course_lessons')->onDelete('cascade');
            $table->string('field_type'); // e.g., 'video', 'title', 'text', 'image', 'file'
            $table->string('field_key'); // e.g., 'main_video', 'title', 'description'
            $table->text('field_value')->nullable(); // for direct values like text or title
            $table->foreignId('media_id')->nullable()->constrained('media_assets')->onDelete('set null');
            $table->integer('position')->default(0); // for ordering fields within the lesson
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_fields');
    }
};
