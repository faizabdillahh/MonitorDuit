<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RecordingReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly int $idleDays) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Jangan lupa catat pengeluaran kamu! 📝')
            ->greeting("Hai, {$notifiable->name}!")
            ->line("Sudah {$this->idleDays} hari kamu tidak mencatat pengeluaran.")
            ->line('Yuk, luangkan 30 detik untuk foto struk terakhirmu.')
            ->action('Buka MonitorDuit', url('/dashboard'))
            ->line('Konsistensi kecil = kontrol keuangan yang besar.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'       => 'recording_reminder',
            'title'      => 'Yuk catat pengeluaran!',
            'message'    => "Sudah {$this->idleDays} hari tidak ada catatan baru.",
            'action_url' => '/transactions/create',
        ];
    }
}
