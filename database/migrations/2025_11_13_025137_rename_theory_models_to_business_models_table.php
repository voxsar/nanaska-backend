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
        Schema::rename('theory_models', 'business_models');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('business_models', 'theory_models');
    }
};
