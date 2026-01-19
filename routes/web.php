<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\DistributionController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecipientController;
use App\Http\Controllers\ZakatController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Assets
    Route::resource('assets', AssetController::class);
    
    // Debts
    Route::resource('debts', DebtController::class);
    
    // Zakat
    Route::get('/zakat/summary', [ZakatController::class, 'summary'])->name('zakat.summary');
    Route::post('/zakat/recalculate', [ZakatController::class, 'recalculate'])->name('zakat.recalculate');
    
    // Recipients
    Route::resource('recipients', RecipientController::class);
    
    // Distributions
    Route::resource('distributions', DistributionController::class);
    
    // History
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
    Route::get('/history/{year}', [HistoryController::class, 'show'])->name('history.show');
    
    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::get('/nisab-settings', [AdminController::class, 'nisabSettings'])->name('nisab-settings');
        Route::post('/nisab-settings', [AdminController::class, 'updateNisab'])->name('nisab-settings.update');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::post('/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggle-status');
        Route::get('/exchange-rates', [AdminController::class, 'exchangeRates'])->name('exchange-rates');
        Route::post('/exchange-rates', [AdminController::class, 'updateExchangeRate'])->name('exchange-rates.update');
    });
});

require __DIR__.'/auth.php';
