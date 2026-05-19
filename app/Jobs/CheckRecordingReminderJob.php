<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Models\User;
use App\Notifications\RecordingReminderNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckRecordingReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        User::where('notif_reminder_email', true)
            ->chunk(100, function ($users) {
                foreach ($users as $user) {
                    $this->checkAndNotify($user);
                }
            });
    }

    private function checkAndNotify(User $user): void
    {
        $lastTransaction = Transaction::where('user_id', $user->id)
            ->latest('transaction_date')
            ->value('transaction_date');

        $idleDays = $lastTransaction
            ? now()->diffInDays($lastTransaction)
            : null;

        if ($idleDays === null || $idleDays >= $user->reminder_idle_days) {
            $alreadyReminded = $user->last_reminded_at &&
                now()->diffInDays($user->last_reminded_at) < $user->reminder_idle_days;

            if (! $alreadyReminded) {
                $user->notify(new RecordingReminderNotification($idleDays ?? 0));
                $user->update(['last_reminded_at' => now()]);
            }
        }
    }
}
