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
        Schema::create('mock_exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mock_exam_id')->constrained()->onDelete('cascade');
            $table->string('question_number')->nullable();
            // reference material
			$table->text('context')->nullable();
            $table->text('reference_material')->nullable();
            $table->json('question_text')->nullable();
            $table->integer('duration_minutes')->default(0);
            $table->integer('marks')->default(0);
            $table->integer('order')->default(0);
			//full json
			$table->json('full_json')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mock_exam_questions');
    }
};
