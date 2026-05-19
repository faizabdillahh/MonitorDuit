<?php

namespace App\Notifications;

use App\Models\Budget;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class BudgetExceededNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Budget $budget,
        public readonly float  $percentage,
        public readonly float  $spent,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'        => 'budget_exceeded',
            'title'       => 'Budget terlampaui!',
            'message'     => "Budget {$this->budget->category->name} sudah {$this->percentage}% — melebihi batas.",
            'budget_id'   => $this->budget->id,
            'category'    => $this->budget->category->name,
            'percentage'  => $this->percentage,
            'spent'       => $this->spent,
            'limit'       => $this->budget->amount,
            'action_url'  => '/budgets',
        ];
    }
}
