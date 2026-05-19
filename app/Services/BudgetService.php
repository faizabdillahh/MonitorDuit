<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Collection;

class BudgetService
{
    /**
     * Ambil semua budget aktif user di bulan tertentu,
     * beserta total pengeluaran aktual per kategori.
     */
    public function getBudgetsWithSpending(User $user, int $month, int $year): Collection
    {
        $budgets = Budget::query()
            ->where('user_id', $user->id)
            ->where(function ($q) use ($month, $year) {
                $q->where('is_recurring', true)
                  ->orWhere(fn($q2) => $q2
                      ->where('month', $month)
                      ->where('year', $year)
                  );
            })
            ->with('category')
            ->get();

        return $budgets->map(function (Budget $budget) use ($month, $year) {
            $spent = Transaction::where('user_id', $budget->user_id)
                ->where('category_id', $budget->category_id)
                ->whereMonth('transaction_date', $month)
                ->whereYear('transaction_date', $year)
                ->sum('amount_idr');

            $percentage = $budget->amount > 0
                ? round(($spent / $budget->amount) * 100, 1)
                : 0;

            return [
                'budget'     => $budget,
                'spent'      => (float) $spent,
                'remaining'  => max(0, $budget->amount - $spent),
                'percentage' => $percentage,
                'status'     => match(true) {
                    $percentage >= 100 => 'exceeded',
                    $percentage >= 80  => 'warning',
                    default            => 'normal',
                },
            ];
        });
    }

    public function createBudget(User $user, array $data): Budget
    {
        if ($data['is_recurring'] ?? true) {
            $data['month'] = null;
            $data['year']  = null;
        }

        return Budget::updateOrCreate(
            [
                'user_id'     => $user->id,
                'category_id' => $data['category_id'],
                'month'       => $data['month'] ?? null,
                'year'        => $data['year'] ?? null,
            ],
            ['amount' => $data['amount'], 'is_recurring' => $data['is_recurring'] ?? true]
        );
    }
}
