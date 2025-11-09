<?php

use App\Http\Controllers\Api\PastPaperController;
use App\Http\Controllers\Api\StudentAnswerController;
use App\Http\Controllers\Api\MarkingResultController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\MockExamController;
use App\Http\Controllers\Api\MockExamQuestionController;
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

// Student authentication routes
Route::post('/students/login', [StudentController::class, 'login']);
Route::post('/students/register', [StudentController::class, 'register']);
Route::post('/students/logout', [StudentController::class, 'logout']);
Route::post('/students/forgot-password', [StudentController::class, 'forgotPassword']);
Route::post('/students/reset-password', [StudentController::class, 'resetPassword']);

// Mock Exam API Routes (CSRF protected via Sanctum tokens)
Route::post('/mock-exams/{mockExam}/questions', [MockExamQuestionController::class, 'store']);
Route::post('/mock-exams/upload', [MockExamController::class, 'upload'])->name('mock-exams.upload');
Route::get('/mock-exams', [MockExamController::class, 'index']);
Route::get('/mock-exams/{id}', [MockExamController::class, 'show']);
Route::get('/mock-exams/{id}/questions', [MockExamController::class, 'questions']);
Route::post('/mock-exams/submit-answer', [MockExamController::class, 'submitAnswer']);
Route::get('/mock-exams/attempts/{studentId}', [MockExamController::class, 'studentAttempts']);