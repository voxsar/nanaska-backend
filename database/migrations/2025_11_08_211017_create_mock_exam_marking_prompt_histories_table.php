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
        Schema::create('mock_exam_marking_prompt_histories', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('mock_exam_marking_prompt_id');
			$table->foreign('mock_exam_marking_prompt_id', 'mock_prompt_hist_fk')
			->references('id')
			->on('mock_exam_marking_prompts')
			->onDelete('cascade');

            $table->text('prompt_text');
            $table->integer('version');
            $table->string('changed_by')->nullable();
            $table->text('change_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mock_exam_marking_prompt_histories');
    }
};
