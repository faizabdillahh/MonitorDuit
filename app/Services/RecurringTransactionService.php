<?php

namespace App\Services;

use App\Enums\RecurringFrequency;
use App\Enums\TransactionSource;
use App\Models\RecurringTransaction;
use App\Models\Transaction;

class RecurringTransactionService
{
    public function processAllDue(): int
    {
        $processed = 0;

        RecurringTransaction::query()
            ->where('is_active', true)
            ->where('next_run_date', '<=', today())
            ->where(fn($q) => $q
                ->whereNull('end_date')
                ->orWhereDate('end_date', '>=', today())
            )
            ->with('user', 'category')
            ->chunk(50, function ($recurrings) use (&$processed) {
                foreach ($recurrings as $recurring) {
                    $this->processOne($recurring);
                    $processed++;
                }
            });

        return $processed;
    }

    private function processOne(RecurringTransaction $recurring): void
    {
        Transaction::create([
            'user_id'                  => $recurring->user_id,
            'category_id'              => $recurring->category_id,
            'merchant_name'            => $recurring->merchant_name,
            'total_amount'             => $recurring->amount,
            'currency'                 => $recurring->currency,
            'transaction_date'         => $recurring->next_run_date,
            'notes'                    => $recurring->notes,
            'source'                   => TransactionSource::Recurring->value,
            'recurring_transaction_id' => $recurring->id,
        ]);

        $nextDate = $recurring->frequency->nextDate($recurring->next_run_date->copy());

        $shouldDeactivate = $recurring->end_date &&
            $nextDate->isAfter($recurring->end_date);

        $recurring->update([
            'next_run_date' => $nextDate,
            'is_active'     => ! $shouldDeactivate,
        ]);
    }
}
