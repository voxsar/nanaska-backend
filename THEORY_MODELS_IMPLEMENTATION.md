# Theory Models Feature Implementation

## Overview

This document describes the complete implementation of the Theory Models feature, which allows students to apply business analysis frameworks to pre-seen case study materials using AI-powered analysis.

## Feature Requirements

The implementation addresses the following requirements from the issue:

1. ✅ Create models, migrations and Filament PHP resource for Theory Models
2. ✅ Theory Model has name, description, and analysis_prompt attributes
3. ✅ Create factory and seeder for all 10 hardcoded models with OpenAI prompts
4. ✅ Frontend option to select pre-seen document before the context
5. ✅ Apply model submits data to N8N analysis endpoints (configurable in services)

## Architecture

### Backend Components

#### 1. Database Schema

**Table: `theory_models`**
- `id` - Primary key
- `name` - Model name (e.g., "SWOT Analysis")
- `description` - Brief description of the model
- `analysis_prompt` - OpenAI prompt for conducting the analysis
- `created_at` - Timestamp
- `updated_at` - Timestamp

**Migration:** `database/migrations/2025_11_09_182110_create_theory_models_table.php`

#### 2. Model & Factory

**Model:** `app/Models/TheoryModel.php`
- Fillable fields: name, description, analysis_prompt
- Uses HasFactory trait

**Factory:** `database/factories/TheoryModelFactory.php`
- Default state with faker data
- 10 named factory states, one for each theory model:
  - `swotAnalysis()`
  - `pestAnalysis()`
  - `portersFiveForces()`
  - `ansoffMatrix()`
  - `bcgMatrix()`
  - `valueChainAnalysis()`
  - `mckinsey7s()`
  - `balancedScorecard()`
  - `pestleAnalysis()`
  - `stakeholderAnalysis()`

**Seeder:** `database/seeders/TheoryModelSeeder.php`
- Truncates existing theory models
- Seeds all 10 models using factory states
- Registered in `DatabaseSeeder.php`

#### 3. API Endpoints

**Controller:** `app/Http/Controllers/Api/TheoryModelController.php`

**Routes:** (registered in `routes/api.php`)

```php
GET  /api/theory-models           // List all theory models
GET  /api/theory-models/{id}      // Get specific model details
POST /api/theory-models/apply     // Apply model to case study
```

**Apply Endpoint Request:**
```json
{
  "theory_model_id": 1,                    // Required
  "pre_seen_document_id": 1,               // Optional
  "case_context": "Additional context",    // Optional
  "specific_questions": "Specific query"   // Optional
}
```

**Apply Endpoint Response:**
```json
{
  "success": true,
  "message": "Analysis request submitted to N8N",
  "data": {
    "theory_model": { /* model details */ },
    "pre_seen_document": { /* document details */ },
    "case_context": "...",
    "specific_questions": "..."
  },
  "n8n_responses": [
    { "url": "...", "status": 200 },
    { "url": "...", "status": 200 }
  ]
}
```

#### 4. Filament Admin Resource

**Resource:** `app/Filament/Resources/TheoryModelResource.php`

**Location in Admin Panel:** Configuration > Theory Models

**Features:**
- List view with name and description columns
- Create/Edit forms with:
  - Name (text input, required)
  - Description (textarea, required)
  - Analysis Prompt (large textarea, required, with helper text)
- Standard CRUD operations
- Navigation icon: Light bulb (heroicon-o-light-bulb)
- Navigation sort: 3

**Pages:**
- `ListTheoryModels` - Index page
- `CreateTheoryModel` - Create new model
- `EditTheoryModel` - Edit existing model

#### 5. N8N Integration

**Service Configuration:** `config/services.php`

```php
'n8n' => [
    // ... existing N8N URLs ...
    'analysis_model_url' => env('N8N_ANALYSIS_MODEL_URL'),
    'analysis_test_model_url' => env('N8N_ANALYSIS_TEST_MODEL_URL'),
],
```

**Environment Variables:** (`.env.example` updated)

```env
N8N_ANALYSIS_MODEL_URL=
N8N_ANALYSIS_TEST_MODEL_URL=
```

**Integration Flow:**
1. User applies theory model via frontend
2. Frontend POSTs to `/api/theory-models/apply`
3. Backend sends payload to both test and production N8N webhooks:
   - `theory_model_id`
   - `theory_model_name`
   - `analysis_prompt`
   - `case_context`
   - `specific_questions`
   - `pre_seen_document` (if selected)
4. N8N processes using AI with the provided prompt and context
5. Results returned in API response

### Frontend Components

#### Updated Component: `resources/js/views/TheoryModels.vue`

**Key Changes:**
1. **Dynamic Model Loading:**
   - Fetches models from `/api/theory-models` on mount
   - Displays loading state during fetch
   - Error handling for failed API calls

2. **Pre-Seen Document Selection:**
   - Fetches available documents from `/api/pre-seen-documents`
   - Dropdown selection (optional)
   - Placed before case context and specific questions inputs

3. **Form Submission:**
   - POSTs to `/api/theory-models/apply`
   - Sends all four parameters to backend
   - Displays N8N response status
   - Shows success/error messages

4. **UI/UX Maintained:**
   - All existing styling preserved
   - Icon mapping for each model
   - Card-based grid layout
   - Hover effects and animations
   - Dark mode support

## Ten Theory Models

Each model includes a comprehensive CIMA-focused OpenAI prompt:

### 1. SWOT Analysis
**Description:** Analyze Strengths, Weaknesses, Opportunities, and Threats

**Prompt Focus:**
- Internal Strengths (competitive advantages, capabilities, resources)
- Internal Weaknesses (limitations, gaps, improvements needed)
- External Opportunities (market trends, growth, favorable conditions)
- External Threats (competition, challenges, risks)

### 2. PEST Analysis
**Description:** Examine Political, Economic, Social, and Technological factors

**Prompt Focus:**
- Political factors (policies, regulations, stability)
- Economic factors (growth, rates, inflation)
- Social factors (demographics, culture, lifestyle)
- Technological factors (innovation, automation, R&D)

### 3. Porter's Five Forces
**Description:** Assess competitive intensity and industry attractiveness

**Prompt Focus:**
- Threat of New Entrants
- Bargaining Power of Suppliers
- Bargaining Power of Buyers
- Threat of Substitutes
- Competitive Rivalry

### 4. Ansoff Matrix
**Description:** Evaluate growth strategies and market penetration

**Prompt Focus:**
- Market Penetration
- Market Development
- Product Development
- Diversification

### 5. BCG Matrix
**Description:** Analyze product portfolio and business units

**Prompt Focus:**
- Stars (high growth, high share)
- Cash Cows (low growth, high share)
- Question Marks (high growth, low share)
- Dogs (low growth, low share)

### 6. Value Chain Analysis
**Description:** Identify value-adding activities in the organization

**Prompt Focus:**
- Primary Activities (inbound logistics, operations, outbound, marketing, service)
- Support Activities (infrastructure, HR, technology, procurement)

### 7. McKinsey 7S Framework
**Description:** Assess organizational design and effectiveness

**Prompt Focus:**
- Strategy, Structure, Systems
- Shared Values, Style, Staff, Skills
- Alignment analysis

### 8. Balanced Scorecard
**Description:** Measure performance across multiple perspectives

**Prompt Focus:**
- Financial Perspective
- Customer Perspective
- Internal Process Perspective
- Learning & Growth Perspective

### 9. PESTLE Analysis
**Description:** Extended PEST including Legal and Environmental factors

**Prompt Focus:**
- PEST factors plus:
- Legal factors (employment, health & safety, data protection)
- Environmental factors (sustainability, climate, resources)

### 10. Stakeholder Analysis
**Description:** Identify and analyze key stakeholders and their interests

**Prompt Focus:**
- Stakeholder identification
- Interests and expectations
- Power-interest grid categorization
- Engagement strategies

## Testing

### Database Testing
```bash
php artisan migrate:fresh --seed
php artisan tinker --execute="echo App\Models\TheoryModel::count() . ' models seeded'"
# Output: 10 models seeded
```

### API Testing
```bash
# List all models
curl http://localhost:8000/api/theory-models

# Get specific model
curl http://localhost:8000/api/theory-models/1

# Apply model
curl -X POST http://localhost:8000/api/theory-models/apply \
  -H "Content-Type: application/json" \
  -d '{
    "theory_model_id": 1,
    "pre_seen_document_id": 1,
    "case_context": "Test context",
    "specific_questions": "Test questions"
  }'
```

### Frontend Testing
```bash
npm install
npm run build
php artisan serve
# Visit http://localhost:8000/theory-models
```

## Files Created/Modified

### New Files
- `app/Models/TheoryModel.php`
- `app/Http/Controllers/Api/TheoryModelController.php`
- `app/Filament/Resources/TheoryModelResource.php`
- `app/Filament/Resources/TheoryModelResource/Pages/CreateTheoryModel.php`
- `app/Filament/Resources/TheoryModelResource/Pages/EditTheoryModel.php`
- `app/Filament/Resources/TheoryModelResource/Pages/ListTheoryModels.php`
- `database/migrations/2025_11_09_182110_create_theory_models_table.php`
- `database/factories/TheoryModelFactory.php`
- `database/seeders/TheoryModelSeeder.php`
- `THEORY_MODELS_IMPLEMENTATION.md` (this file)

### Modified Files
- `routes/api.php` - Added theory model routes
- `config/services.php` - Added N8N analysis URLs
- `database/seeders/DatabaseSeeder.php` - Added TheoryModelSeeder
- `resources/js/views/TheoryModels.vue` - Complete rewrite for API integration
- `.env.example` - Added N8N analysis URL placeholders
- `API.md` - Added Theory Models documentation

## Usage Instructions

### For Administrators

1. Access admin panel at `/admin`
2. Navigate to Configuration > Theory Models
3. View/edit existing theory models
4. Modify analysis prompts as needed
5. Create custom theory models if desired

### For Students

1. Navigate to Theory Models page
2. Select a business analysis framework
3. (Optional) Choose a pre-seen document
4. (Optional) Provide additional context
5. (Optional) Ask specific questions
6. Click "Apply Model"
7. View analysis results

### For Developers

1. Configure N8N webhook URLs in `.env`:
   ```env
   N8N_ANALYSIS_MODEL_URL=https://your-n8n.com/webhook/analysis
   N8N_ANALYSIS_TEST_MODEL_URL=https://your-n8n.com/webhook/analysis-test
   ```

2. Run migrations and seed database:
   ```bash
   php artisan migrate
   php artisan db:seed --class=TheoryModelSeeder
   ```

3. Build frontend assets:
   ```bash
   npm run build
   ```

## N8N Webhook Expected Payload

The N8N webhooks should expect the following JSON payload:

```json
{
  "theory_model_id": 1,
  "theory_model_name": "SWOT Analysis",
  "analysis_prompt": "You are a CIMA business analyst...",
  "case_context": "User-provided context",
  "specific_questions": "User-specific questions",
  "pre_seen_document": {
    "id": 1,
    "name": "Document Name",
    "file_path": "path/to/document.pdf"
  }
}
```

The N8N workflow should:
1. Retrieve the pre-seen document content (if provided)
2. Construct a comprehensive prompt using the analysis_prompt as the base
3. Include the case_context and specific_questions
4. Send to OpenAI or similar LLM
5. Return the analysis results

## Future Enhancements

Potential improvements for future iterations:

1. **Result Storage:** Store analysis results in database for history
2. **Comparison Feature:** Compare multiple theory model analyses side-by-side
3. **Export Options:** PDF/Word export of analysis results
4. **Collaborative Analysis:** Share and discuss analyses with peers
5. **Custom Models:** Allow instructors to create custom theory models
6. **Analysis Templates:** Pre-filled templates for common case study types
7. **Progress Tracking:** Track which models students have used
8. **AI Feedback:** Get AI suggestions on which models to use

## Support & Maintenance

### Common Issues

**Issue:** Theory models not appearing in frontend
- **Solution:** Ensure database is seeded with `php artisan db:seed --class=TheoryModelSeeder`

**Issue:** Apply model returns empty N8N responses
- **Solution:** Configure N8N_ANALYSIS_MODEL_URL and N8N_ANALYSIS_TEST_MODEL_URL in `.env`

**Issue:** Pre-seen documents not loading
- **Solution:** Ensure pre-seen documents are created in Filament admin panel

### Database Maintenance

To reset theory models:
```bash
php artisan db:seed --class=TheoryModelSeeder
```

To add a new theory model via Tinker:
```bash
php artisan tinker
>>> App\Models\TheoryModel::create([
...   'name' => 'New Model',
...   'description' => 'Description',
...   'analysis_prompt' => 'AI prompt here'
... ]);
```

## Conclusion

The Theory Models feature is fully implemented and tested, providing students with 10 professional business analysis frameworks to apply to their CIMA case studies. The integration with N8N allows for flexible AI-powered analysis, and the Filament admin panel enables easy management of theory models by administrators.

All requirements from the original issue have been successfully implemented:
- ✅ Database models, migrations, and Filament resource
- ✅ Complete attributes (name, description, analysis_prompt)
- ✅ Factory and seeder with 10 models and OpenAI prompts
- ✅ Frontend pre-seen document selection
- ✅ N8N integration for analysis processing

The feature is production-ready and fully documented.
