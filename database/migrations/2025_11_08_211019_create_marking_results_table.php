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
        Schema::create('marking_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_answer_id')->constrained('mock_exam_answers')->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->nullable()->constrained('mock_exam_questions')->onDelete('cascade');
            $table->decimal('marks_obtained', 5, 2);
            $table->decimal('total_marks', 5, 2);
            $table->text('feedback')->nullable();
            $table->json('ai_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marking_results');
    }
};
