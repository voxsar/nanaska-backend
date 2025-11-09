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
        Schema::table('marking_results', function (Blueprint $table) {
            // CIMA level and band
            $table->string('level')->nullable()->after('ai_response'); // OCS, MCS, SCS
            $table->integer('band_level')->nullable()->after('level'); // 1, 2, 3
            $table->text('band_explanation')->nullable()->after('band_level');
            
            // Question addressing
            $table->boolean('answered_specific_question')->nullable()->after('band_explanation');
            
            // Assumptions and summary
            $table->json('assumptions')->nullable()->after('answered_specific_question');
            $table->json('points_summary')->nullable()->after('assumptions');
            
            // Comments and improvement
            $table->text('genericity_comment')->nullable()->after('points_summary');
            $table->json('improvement_plan')->nullable()->after('genericity_comment');
            
            // Citations and extracts
            $table->json('citations')->nullable()->after('improvement_plan');
            $table->json('strengths_extracts')->nullable()->after('citations');
            $table->json('weaknesses_extracts')->nullable()->after('strengths_extracts');
            
            // Structure
            $table->boolean('structure_ok')->nullable()->after('weaknesses_extracts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marking_results', function (Blueprint $table) {
            $table->dropColumn([
                'level',
                'band_level',
                'band_explanation',
                'answered_specific_question',
                'assumptions',
                'points_summary',
                'genericity_comment',
                'improvement_plan',
                'citations',
                'strengths_extracts',
                'weaknesses_extracts',
                'structure_ok',
            ]);
        });
    }
};
