<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\BudgetNotificationLog;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\BudgetExceededNotification;
use App\Notifications\BudgetWarningNotification;

class BudgetNotificationService
{
    public function checkAfterTransaction(Transaction $transaction): void
    {
        $user  = $transaction->user;
        $month = $transaction->transaction_date->month;
        $year  = $transaction->transaction_date->year;

        $allBudgets = app(BudgetService::class)
            ->getBudgetsWithSpending($user, $month, $year);

        $budgetData = $allBudgets->firstWhere('budget.category_id', $transaction->category_id);

        if (! $budgetData) return;

        $this->maybeNotifyWarning($user, $budgetData, $month, $year);
        $this->maybeNotifyExceeded($user, $budgetData, $month, $year);
    }

    private function maybeNotifyWarning(User $user, array $data, int $month, int $year): void
    {
        if (! $user->notif_budget_warning) return;
        if ($data['status'] !== 'warning')  return;

        $alreadySent = BudgetNotificationLog::where([
            'budget_id' => $data['budget']->id,
            'type'      => 'warning',
            'month'     => $month,
            'year'      => $year,
        ])->exists();

        if (! $alreadySent) {
            $user->notify(new BudgetWarningNotification(
                $data['budget'], $data['percentage'], $data['spent']
            ));
            BudgetNotificationLog::create([
                'budget_id' => $data['budget']->id,
                'type'      => 'warning',
                'month'     => $month,
                'year'      => $year,
            ]);
        }
    }

    private function maybeNotifyExceeded(User $user, array $data, int $month, int $year): void
    {
        if (! $user->notif_budget_exceeded) return;
        if ($data['status'] !== 'exceeded')  return;

        $alreadySent = BudgetNotificationLog::where([
            'budget_id' => $data['budget']->id,
            'type'      => 'exceeded',
            'month'     => $month,
            'year'      => $year,
        ])->exists();

        if (! $alreadySent) {
            $user->notify(new BudgetExceededNotification(
                $data['budget'], $data['percentage'], $data['spent']
            ));
            BudgetNotificationLog::create([
                'budget_id' => $data['budget']->id,
                'type'      => 'exceeded',
                'month'     => $month,
                'year'      => $year,
            ]);
        }
    }
}
