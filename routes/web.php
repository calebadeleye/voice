<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/company/edit', [CompanyController::class, 'edit'])->name('company.edit');
    Route::put('/company/update', [CompanyController::class, 'update'])->name('company.update');
    Route::post('/company/upload-file', [CompanyController::class, 'uploadFile'])->name('company.uploadFile');
    Route::get('/company/create', [CompanyController::class, 'create'])->name('company.create');
    Route::post('/company/store', [CompanyController::class, 'store'])->name('company.store');

    Route::delete('/company/delete-file/{id}', [CompanyController::class, 'deleteFile'])->name('company.deleteFile');
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::get('/wallet/fund', [PaymentController::class, 'fundWallet'])->name('wallet.fund');
    Route::post('/flutterwave/pay', [PaymentController::class, 'initialize'])->name('flutterwave.pay');
    Route::get('/flutterwave/callback', [PaymentController::class, 'callback'])->name('flutterwave.callback');

});


require __DIR__.'/auth.php';
