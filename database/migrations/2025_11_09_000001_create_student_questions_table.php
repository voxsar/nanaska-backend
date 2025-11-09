<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('pre_seen_document_id')->nullable();
            $table->text('question_text');
            $table->string('status')->default('pending'); // pending, answered, failed
            $table->json('bullet_point_answers')->nullable();
            $table->json('quoted_snippets')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('set null');
            $table->foreign('pre_seen_document_id')->references('id')->on('pre_seen_documents')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_questions');
    }
};
