<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AIChatController;
use App\Http\Controllers\VoiceController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/ai/chat', [AIChatController::class, 'chat']);
    Route::get('/ai/history', [AIChatController::class, 'history']);
    Route::delete('/ai/clear', [AIChatController::class, 'clearHistory']);
});


Route::post('/voice/callback', [VoiceController::class, 'callback']);
Route::post('/vapi/callback', [VoiceController::class, 'handleWebhook']);

