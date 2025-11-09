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
        Schema::create('mock_exam_marking_prompts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mock_exam_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('prompt_text');
            $table->boolean('is_active')->default(true);
            $table->integer('version')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mock_exam_marking_prompts');
    }
};
