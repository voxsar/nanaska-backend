<?php

use App\Http\Controllers\Api\PastPaperController;
use App\Http\Controllers\Api\StudentAnswerController;
use App\Http\Controllers\Api\MarkingResultController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\MockExamController;
use App\Http\Controllers\Api\MockExamQuestionController;
use App\Http\Controllers\Api\PreSeenDocumentController;
use App\Http\Controllers\Api\PracticeExamController;
use App\Http\Controllers\Api\PracticeMarkingResultController;
use App\Http\Controllers\Api\StudentQuestionController;
use App\Http\Controllers\Api\BusinessModelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/past-papers', [PastPaperController::class, 'index']);
Route::get('/past-papers/{id}', [PastPaperController::class, 'show']);
Route::get('/past-papers/{id}/questions', [PastPaperController::class, 'questions']);

Route::post('/students/submit-answer', [StudentAnswerController::class, 'submit']);

Route::post('/marking-results', [MarkingResultController::class, 'receive']);
Route::get('/marking-results/student/{studentId}', [MarkingResultController::class, 'studentResults']);
Route::get('/marking-results/answer/{answerId}', [MarkingResultController::class, 'answerResult']);
Route::get('/marking-results/attempt/{attemptId}', [MarkingResultController::class, 'attemptResults']);

// Student public auth routes
Route::post('/students/login', [StudentController::class, 'login']);
Route::post('/students/register', [StudentController::class, 'register']);
Route::post('/students/forgot-password', [StudentController::class, 'forgotPassword']);
Route::post('/students/reset-password', [StudentController::class, 'resetPassword']);

// Student protected routes
Route::middleware('auth:student-api')->group(function () {
    Route::post('/students/logout', [StudentController::class, 'logout']);
    Route::get('/students/me', [StudentController::class, 'me']);
});

// Mock Exam API Routes (CSRF protected via Sanctum tokens)
Route::post('/mock-exams/{mockExam}/questions', [MockExamQuestionController::class, 'store']);
Route::post('/mock-exams/upload', [MockExamController::class, 'upload'])->name('mock-exams.upload');
Route::get('/mock-exams', [MockExamController::class, 'index']);
Route::get('/mock-exams/{id}', [MockExamController::class, 'show']);
Route::get('/mock-exams/{id}/questions', [MockExamController::class, 'questions']);
Route::post('/mock-exams/submit-answer', [MockExamController::class, 'submitAnswer']);
Route::get('/mock-exams/attempts/{studentId}', [MockExamController::class, 'studentAttempts']);

// Pre-Seen Documents API Routes
Route::get('/pre-seen-documents', [PreSeenDocumentController::class, 'index']);
Route::get('/pre-seen-documents/{id}', [PreSeenDocumentController::class, 'show']);

// Practice Questions API Routes (separate from mock exams)
Route::get('/practice-questions', [PracticeExamController::class, 'index']);
Route::get('/practice-questions/{id}', [PracticeExamController::class, 'show']);
Route::post('/practice-questions/submit-answer', [PracticeExamController::class, 'submitAnswer']);
Route::get('/practice-questions/attempts/{studentId}', [PracticeExamController::class, 'studentAttempts']);

// Practice Marking Results API Routes (separate from mock exam marking)
Route::post('/practice-marking-results', [PracticeMarkingResultController::class, 'receive']);
Route::get('/practice-marking-results/student/{studentId}', [PracticeMarkingResultController::class, 'studentResults']);
Route::get('/practice-marking-results/attempt/{attemptId}', [PracticeMarkingResultController::class, 'attemptResult']);
Route::get('/practice-marking-results/question/{questionId}', [PracticeMarkingResultController::class, 'questionResults']);

// Student Questions (Ask AI) Routes
Route::get('/student-questions', [StudentQuestionController::class, 'index']);
Route::post('/student-questions', [StudentQuestionController::class, 'store']);
Route::get('/student-questions/{id}', [StudentQuestionController::class, 'show']);
Route::post('/student-questions/{id}/response', [StudentQuestionController::class, 'response']);

// Business Models Routes
Route::get('/business-models', [BusinessModelController::class, 'index']);
Route::get('/business-models/{id}', [BusinessModelController::class, 'show']);
Route::post('/business-models/apply', [BusinessModelController::class, 'apply']);
