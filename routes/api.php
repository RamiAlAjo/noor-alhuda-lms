<?php

use App\Http\Controllers\Api\AIPredictionController;
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

// AI Prediction Routes with rate limiting
Route::prefix('ai')->middleware(['throttle:30,1'])->group(function () {
    // Performance prediction (10 requests per minute)
    Route::post('/predict/performance', [AIPredictionController::class, 'predictPerformance'])
        ->middleware('throttle:10,1');

    // Batch prediction (5 requests per minute)
    Route::post('/predict/batch', [AIPredictionController::class, 'batchPredict'])
        ->middleware('throttle:5,1');

    // Model information (30 requests per minute)
    Route::get('/model/info', [AIPredictionController::class, 'getModelInfo']);

    // Health check (60 requests per minute)
    Route::get('/health', [AIPredictionController::class, 'healthCheck'])
        ->middleware('throttle:60,1');
});
