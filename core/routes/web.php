<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/company/edit', [CompanyController::class, 'edit'])->name('company.edit');
    Route::put('/company/update', [CompanyController::class, 'update'])->name('company.update');
    Route::post('/company/upload-file', [CompanyController::class, 'uploadFile'])->name('company.uploadFile');
    Route::get('/company/create', [CompanyController::class, 'create'])->name('company.create');
    Route::post('/company/store', [CompanyController::class, 'store'])->name('company.store');
    Route::get('/company/virtual-numbers', [CompanyController::class, 'editVirtualNumbers'])
        ->name('company.virtual-numbers.edit');

    Route::delete('/company/delete-file/{id}', [CompanyController::class, 'deleteFile'])->name('company.deleteFile');
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::get('/wallet/fund', [WalletController::class, 'fund'])->name('wallet.fund');
    Route::post('/flutterwave/pay', [WalletController::class, 'flutterwavePay'])->name('flutterwave.pay');
    Route::get('/flutterwave/callback', [WalletController::class, 'flutterwaveCallback'])->name('flutterwave.callback');

});

    Route::post('/company/virtual-numbers', [CompanyController::class, 'requestNumber'])
        ->name('company.virtual-numbers.update');

            // Edit AI Assistant info page
    Route::get('/company/ai-assistant', [CompanyController::class, 'editAIAssistant'])
        ->name('company.ai-assistant.edit');

    // Handle AI Assistant info update
    Route::post('/company/ai-assistant', [CompanyController::class, 'updateAIAssistant'])
        ->name('company.ai-assistant.update');


require __DIR__.'/auth.php';
