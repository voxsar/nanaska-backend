<?php

namespace Database\Seeders;

use App\Models\TheoryModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TheoryModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing theory models
        TheoryModel::truncate();

        // Create the 10 theory models using factory states
        TheoryModel::factory()->swotAnalysis()->create();
        TheoryModel::factory()->pestAnalysis()->create();
        TheoryModel::factory()->portersFiveForces()->create();
        TheoryModel::factory()->ansoffMatrix()->create();
        TheoryModel::factory()->bcgMatrix()->create();
        TheoryModel::factory()->valueChainAnalysis()->create();
        TheoryModel::factory()->mckinsey7s()->create();
        TheoryModel::factory()->balancedScorecard()->create();
        TheoryModel::factory()->pestleAnalysis()->create();
        TheoryModel::factory()->stakeholderAnalysis()->create();
    }
}
