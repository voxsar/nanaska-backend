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
        Schema::create('student_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->foreignId('past_paper_id')->constrained()->onDelete('cascade');
            $table->text('answer_text');
            $table->string('file_path')->nullable();
            $table->enum('status', ['pending', 'submitted', 'marking', 'marked'])->default('pending');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
        Schema::table('marking_results', function (Blueprint $table) {
            $table->foreignId('student_answer_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_answers');
    }
};
