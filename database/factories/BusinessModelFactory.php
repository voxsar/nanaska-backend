<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BusinessModel>
 */
class BusinessModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'analysis_prompt' => $this->faker->paragraph,
        ];
    }

    public function swotAnalysis(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'SWOT Analysis',
            'description' => 'Analyze Strengths, Weaknesses, Opportunities, and Threats',
            'analysis_prompt' => 'You are a CIMA business analyst. Analyze the provided pre-seen document using SWOT Analysis framework. Identify and categorize: 1) Internal Strengths - competitive advantages, unique capabilities, strong resources; 2) Internal Weaknesses - limitations, resource gaps, areas needing improvement; 3) External Opportunities - market trends, growth potential, favorable conditions; 4) External Threats - competition, market challenges, external risks. Provide specific examples from the document for each category with detailed explanations relevant to CIMA case study context.',
        ]);
    }

    public function pestAnalysis(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'PEST Analysis',
            'description' => 'Examine Political, Economic, Social, and Technological factors',
            'analysis_prompt' => 'You are a CIMA business analyst. Conduct a PEST Analysis of the provided pre-seen document. Examine: 1) Political factors - government policies, regulations, political stability, tax policies; 2) Economic factors - economic growth, interest rates, inflation, exchange rates; 3) Social factors - demographics, cultural trends, consumer attitudes, lifestyle changes; 4) Technological factors - innovation, automation, R&D, technological changes. For each factor, identify specific impacts on the organization from the pre-seen material and their strategic implications for CIMA case studies.',
        ]);
    }

    public function portersFiveForces(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => "Porter's Five Forces",
            'description' => 'Assess competitive intensity and industry attractiveness',
            'analysis_prompt' => 'You are a CIMA business analyst. Apply Porter\'s Five Forces framework to analyze the competitive environment in the pre-seen document. Evaluate: 1) Threat of New Entrants - barriers to entry, capital requirements, economies of scale; 2) Bargaining Power of Suppliers - supplier concentration, switching costs, uniqueness of inputs; 3) Bargaining Power of Buyers - buyer concentration, price sensitivity, switching costs; 4) Threat of Substitute Products - alternative solutions, price-performance trade-offs; 5) Competitive Rivalry - number of competitors, market growth, differentiation. Assess each force\'s intensity and overall industry attractiveness for CIMA strategic analysis.',
        ]);
    }

    public function ansoffMatrix(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Ansoff Matrix',
            'description' => 'Evaluate growth strategies and market penetration',
            'analysis_prompt' => 'You are a CIMA business analyst. Apply the Ansoff Matrix to identify growth strategies from the pre-seen document. Analyze opportunities for: 1) Market Penetration - selling more existing products to current markets through increased marketing, competitive pricing; 2) Market Development - entering new markets with existing products through geographic expansion, new segments; 3) Product Development - creating new products for existing markets through innovation, product line extensions; 4) Diversification - new products in new markets, assessing risks and synergies. Evaluate each strategy\'s feasibility, risks, and potential returns relevant to CIMA case study scenarios.',
        ]);
    }

    public function bcgMatrix(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'BCG Matrix',
            'description' => 'Analyze product portfolio and business units',
            'analysis_prompt' => 'You are a CIMA business analyst. Use the BCG Matrix to analyze the product portfolio or business units in the pre-seen document. Classify products/units into: 1) Stars - high growth, high market share, requiring investment; 2) Cash Cows - low growth, high market share, generating cash; 3) Question Marks - high growth, low market share, requiring decisions; 4) Dogs - low growth, low market share, candidates for divestment. For each category, identify specific products/units from the document, assess their position, and recommend strategic actions for optimal portfolio management in CIMA strategic context.',
        ]);
    }

    public function valueChainAnalysis(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Value Chain Analysis',
            'description' => 'Identify value-adding activities in the organization',
            'analysis_prompt' => 'You are a CIMA business analyst. Conduct a Value Chain Analysis of the organization in the pre-seen document. Examine: Primary Activities - 1) Inbound Logistics, 2) Operations, 3) Outbound Logistics, 4) Marketing & Sales, 5) Service. Support Activities - 1) Firm Infrastructure, 2) Human Resource Management, 3) Technology Development, 4) Procurement. For each activity, identify how value is created, sources of competitive advantage, cost drivers, and areas for improvement. Link findings to financial performance and strategic positioning relevant to CIMA case analysis.',
        ]);
    }

    public function mckinsey7s(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'McKinsey 7S Framework',
            'description' => 'Assess organizational design and effectiveness',
            'analysis_prompt' => 'You are a CIMA business analyst. Apply the McKinsey 7S Framework to assess organizational effectiveness from the pre-seen document. Analyze alignment of: 1) Strategy - plan to achieve competitive advantage; 2) Structure - organizational hierarchy and reporting lines; 3) Systems - processes and procedures; 4) Shared Values - core beliefs and culture; 5) Style - leadership approach; 6) Staff - human resources and capabilities; 7) Skills - organizational competencies. Identify strengths, gaps, misalignments, and recommendations for improving organizational effectiveness in CIMA case study context.',
        ]);
    }

    public function balancedScorecard(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Balanced Scorecard',
            'description' => 'Measure performance across multiple perspectives',
            'analysis_prompt' => 'You are a CIMA business analyst. Develop a Balanced Scorecard framework for the organization in the pre-seen document. Analyze performance across four perspectives: 1) Financial Perspective - profitability, revenue growth, cost efficiency, ROI; 2) Customer Perspective - satisfaction, retention, market share, value proposition; 3) Internal Process Perspective - operational efficiency, quality, innovation, cycle times; 4) Learning & Growth Perspective - employee capabilities, information systems, organizational culture. For each perspective, identify current performance indicators, gaps, and strategic objectives aligned with CIMA performance management principles.',
        ]);
    }

    public function pestleAnalysis(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'PESTLE Analysis',
            'description' => 'Extended PEST including Legal and Environmental factors',
            'analysis_prompt' => 'You are a CIMA business analyst. Conduct a comprehensive PESTLE Analysis of the pre-seen document. Examine: 1) Political factors - government policies, stability, trade regulations; 2) Economic factors - growth, inflation, employment, exchange rates; 3) Social factors - demographics, lifestyle, education, culture; 4) Technological factors - innovation, automation, digital transformation; 5) Legal factors - employment law, health & safety, data protection, competition law; 6) Environmental factors - sustainability, climate change, carbon footprint, resource management. For each factor, identify specific impacts, risks, opportunities, and strategic implications for CIMA case analysis.',
        ]);
    }

    public function stakeholderAnalysis(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Stakeholder Analysis',
            'description' => 'Identify and analyze key stakeholders and their interests',
            'analysis_prompt' => 'You are a CIMA business analyst. Perform a comprehensive Stakeholder Analysis based on the pre-seen document. Identify key stakeholders: shareholders, employees, customers, suppliers, government, community, creditors. For each stakeholder group: 1) Analyze their interests, expectations, and concerns; 2) Assess their power and influence (high/low); 3) Evaluate their level of interest (high/low); 4) Categorize using power-interest grid (Keep Satisfied, Manage Closely, Monitor, Keep Informed); 5) Recommend engagement strategies and communication approaches. Consider conflicts of interest and prioritization for CIMA strategic decision-making context.',
        ]);
    }
}
