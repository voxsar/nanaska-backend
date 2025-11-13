# Nanaska Backend - CIMA Exam Preparation Platform

Laravel backend API with Filament admin panel for CIMA exam preparation, featuring AI-powered marking, mock exams, and practice questions.

## Features

- **Mock Exams**: Full exam papers with questions, sub-questions, and timed attempts
- **Practice Questions**: Individual questions based on pre-seen documents for targeted practice
- **AI-Powered Marking**: Automated marking via n8n workflows
- **Pre-Seen Documents**: Upload and manage CIMA pre-seen materials
- **Student Management**: Student authentication and progress tracking
- **Filament Admin Panel**: Modern admin interface for content management

## Recent Updates

### Mock Exams vs Practice Questions Separation

Mock exams and practice questions are now **completely separate** features with:

- ✅ Separate database tables and models
- ✅ Separate API endpoints
- ✅ Separate n8n webhook URLs
- ✅ Separate admin interfaces
- ✅ No data mixing or confusion

**Key Differences:**

| Feature | Mock Exams | Practice Questions |
|---------|-----------|-------------------|
| Structure | Full exam paper | Individual questions |
| Context | Complete mock exam | Linked to pre-seen documents |
| API | `/api/mock-exams/*` | `/api/practice-questions/*` |
| Admin | Mock Exams section | Practice Questions section |

See [SEPARATION_IMPLEMENTATION.md](SEPARATION_IMPLEMENTATION.md) for details.

## Quick Start

### Installation

```bash
# Clone repository
git clone https://github.com/voxsar/nanaska-backend.git
cd nanaska-backend

# Install dependencies
composer install
npm install

# Configure environment
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate --seed

# Build assets
npm run build

# Start server
php artisan serve
```

### Access Points

- **Frontend**: http://localhost:8000
- **Admin Panel**: http://localhost:8000/admin
  - Email: admin@nanaska.com
  - Password: admin@nanaska.com@123

## Environment Configuration

Add these to your `.env` file:

### Mock Exam URLs
```env
N8N_QUESTION_URL=https://automation.artslabcreatives.com/webhook/mock-exams
N8N_MARKING_URL=https://automation.artslabcreatives.com/webhook/marking
```

### Practice Question URLs (separate from mock exams)
```env
N8N_PRACTICE_QUESTION_URL=https://automation.artslabcreatives.com/webhook/practice-questions
N8N_PRACTICE_MARKING_URL=https://automation.artslabcreatives.com/webhook/practice-marking
```

See [.env.example](.env.example) for all configuration options.

## Documentation

- **[API Documentation](API.md)** - Complete API reference
- **[Mock Exam API](MOCK_EXAM_API.md)** - Mock exam specific endpoints
- **[Practice Questions API](PRACTICE_QUESTIONS_API.md)** - Practice question endpoints
- **[Separation Implementation](SEPARATION_IMPLEMENTATION.md)** - Architecture decisions
- **[Implementation Complete](IMPLEMENTATION_COMPLETE.md)** - Summary of changes
- **[Frontend Integration](FRONTEND.md)** - Vue.js frontend guide
- **[Testing Guide](TESTING_GUIDE.md)** - How to run tests

## API Endpoints

### Mock Exams
```
GET  /api/mock-exams
GET  /api/mock-exams/{id}
GET  /api/mock-exams/{id}/questions
POST /api/mock-exams/submit-answer
GET  /api/mock-exams/attempts/{studentId}
```

### Practice Questions (NEW)
```
GET  /api/practice-questions
GET  /api/practice-questions/{id}
POST /api/practice-questions/submit-answer
GET  /api/practice-questions/attempts/{studentId}
```

### Pre-Seen Documents
```
GET /api/pre-seen-documents
GET /api/pre-seen-documents/{id}
```

## Admin Panel Features

Access at `/admin`:

### Mock Exams Section
- Mock Exams management
- Mock Exam Questions
- Mock Exam Sub Questions
- Mock Exam Attempts
- Mock Exam Marking Prompts

### Practice Questions Section (NEW)
- Practice Questions management
- Linked to pre-seen documents
- Student attempt tracking
- Separate from mock exams

### Documents Section
- Pre-Seen Documents
- Past Papers

### Users Section
- Students management

## Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test tests/Feature/PracticeQuestionSeparationTest.php
php artisan test tests/Feature/MockExamApiTest.php

# Run with coverage
php artisan test --coverage
```

## Tech Stack

- **Framework**: Laravel 10
- **Admin Panel**: Filament v3
- **Authentication**: Laravel Sanctum
- **Database**: MySQL
- **Frontend**: Vue.js 3 + Vite
- **Styling**: Tailwind CSS
- **Automation**: n8n webhooks

## Project Structure

```
app/
├── Models/
│   ├── MockExam.php
│   ├── MockExamQuestion.php
│   ├── PracticeQuestion.php         # NEW
│   ├── PracticeQuestionAttempt.php  # NEW
│   └── ...
├── Http/Controllers/Api/
│   ├── MockExamController.php
│   ├── PracticeExamController.php   # Rewritten
│   ├── PracticeMarkingResultController.php  # NEW
│   └── ...
├── Filament/Resources/
│   ├── MockExamResource.php
│   ├── PracticeQuestionResource.php # NEW
│   └── ...
database/migrations/
├── 2025_11_08_*_create_mock_exam_*.php
├── 2025_11_09_*_create_practice_question*.php  # NEW
tests/Feature/
├── MockExamApiTest.php
├── PracticeQuestionSeparationTest.php  # NEW
```

## Development

```bash
# Run development server with hot reload
npm run dev

# Watch for file changes
php artisan serve

# Clear caches
php artisan optimize:clear

# Run queue worker (for background jobs)
php artisan queue:work
```

## Security Notes

⚠️ **Important**: Student passwords are stored in plain text by design (per requirements). This is NOT recommended for production systems.

## Contributing

1. Create feature branch from `main`
2. Make your changes
3. Run tests: `php artisan test`
4. Submit pull request

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
