<?php

use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\RecurringTransactionController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Transactions
    Route::resource('transactions', TransactionController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

    // Receipt scan
    Route::post('/receipts/scan', [ReceiptController::class, 'scan'])
        ->middleware('throttle:50,1440')
        ->name('receipts.scan');

    Route::get('/receipts/{transaction}/image', [ReceiptController::class, 'image'])
        ->name('receipts.image');

    // Statistics
    Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics');

    // Export
    Route::get('/export', [ExportController::class, 'download'])->name('export');

    // Categories
    Route::resource('categories', CategoryController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    // Budgets
    Route::resource('budgets', BudgetController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/',           [NotificationController::class, 'index'])->name('index');
        Route::patch('{id}/read', [NotificationController::class, 'markRead'])->name('read');
        Route::patch('read-all',  [NotificationController::class, 'markAllRead'])->name('read-all');
        Route::delete('{id}',     [NotificationController::class, 'destroy'])->name('destroy');
    });

    // Recurring Transactions
    Route::resource('recurring', RecurringTransactionController::class)
        ->only(['index', 'store', 'update', 'destroy']);
    Route::patch('recurring/{recurring}/toggle', [RecurringTransactionController::class, 'toggle'])
        ->name('recurring.toggle');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Profile (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // Static Pages
    Route::inertia('/about', 'Static/About')->name('about');
    Route::inertia('/privacy', 'Static/Privacy')->name('privacy');
    Route::inertia('/terms', 'Static/Terms')->name('terms');
    Route::inertia('/help', 'Static/Help')->name('help');
});

require __DIR__.'/auth.php';
