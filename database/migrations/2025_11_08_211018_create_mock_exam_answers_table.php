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
        Schema::create('mock_exam_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mock_exam_attempt_id')->constrained()->onDelete('cascade');
            $table->foreignId('mock_exam_question_id')->constrained()->onDelete('cascade');
            $table->foreignId('mock_exam_sub_question_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->text('answer_text');
            $table->string('file_path')->nullable();
            $table->decimal('marks_obtained', 8, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->json('ai_response')->nullable();
            $table->enum('status', ['pending', 'submitted', 'marking', 'marked'])->default('pending');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mock_exam_answers');
    }
};
