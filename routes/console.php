<?php

use App\Jobs\CheckRecordingReminderJob;
use App\Services\RecurringTransactionService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Process recurring transactions — setiap hari jam 00:05 WIB
Schedule::call(fn() => app(RecurringTransactionService::class)->processAllDue())
    ->dailyAt('00:05')
    ->timezone('Asia/Jakarta')
    ->name('process-recurring-transactions')
    ->withoutOverlapping();

// Cek reminder idle — setiap hari jam 09.00 WIB
Schedule::job(new CheckRecordingReminderJob)->dailyAt('09:00')->timezone('Asia/Jakarta');

// Cleanup notifikasi lama — setiap minggu
Schedule::call(function () {
    \Illuminate\Notifications\DatabaseNotification::where('created_at', '<', now()->subDays(30))->delete();
})->weekly();
