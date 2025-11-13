<?php

namespace Database\Seeders;

use App\Models\BusinessModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BusinessModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing business models
        BusinessModel::truncate();

        // Create the 10 business models using factory states
        BusinessModel::factory()->swotAnalysis()->create();
        BusinessModel::factory()->pestAnalysis()->create();
        BusinessModel::factory()->portersFiveForces()->create();
        BusinessModel::factory()->ansoffMatrix()->create();
        BusinessModel::factory()->bcgMatrix()->create();
        BusinessModel::factory()->valueChainAnalysis()->create();
        BusinessModel::factory()->mckinsey7s()->create();
        BusinessModel::factory()->balancedScorecard()->create();
        BusinessModel::factory()->pestleAnalysis()->create();
        BusinessModel::factory()->stakeholderAnalysis()->create();
    }
}
