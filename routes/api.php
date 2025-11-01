<?php

use App\Http\Controllers\Api\PastPaperController;
use App\Http\Controllers\Api\StudentAnswerController;
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

Route::post('/marking-results', [\App\Http\Controllers\Api\MarkingResultController::class, 'receive']);
