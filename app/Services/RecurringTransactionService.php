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

    public function processUserDue(\App\Models\User $user): int
    {
        $processed = 0;

        $recurrings = RecurringTransaction::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->where('next_run_date', '<=', today())
            ->where(fn($q) => $q
                ->whereNull('end_date')
                ->orWhereDate('end_date', '>=', today())
            )
            ->with('user', 'category')
            ->get();

        foreach ($recurrings as $recurring) {
            $this->processOne($recurring);
            $processed++;
        }

        return $processed;
    }

    private function processOne(RecurringTransaction $recurring): void
    {
        // Loop as long as the recurring transaction is active and next_run_date is today or in the past
        while ($recurring->is_active && $recurring->next_run_date->startOfDay()->lte(today())) {
            
            // Check end_date condition first
            if ($recurring->end_date && $recurring->next_run_date->startOfDay()->gt($recurring->end_date->startOfDay())) {
                $recurring->update(['is_active' => false]);
                break;
            }

            // Create the transaction using TransactionService so amount_idr is calculated
            app(\App\Services\TransactionService::class)->store($recurring->user, [
                'category_id'              => $recurring->category_id,
                'merchant_name'            => $recurring->merchant_name,
                'total_amount'             => $recurring->amount,
                'currency'                 => $recurring->currency,
                'transaction_date'         => $recurring->next_run_date,
                'notes'                    => $recurring->notes,
                'source'                   => TransactionSource::Recurring->value,
                'recurring_transaction_id' => $recurring->id,
            ]);

            // Calculate the next date
            $nextDate = $recurring->frequency->nextDate($recurring->next_run_date->copy());

            // Check if it should be deactivated after this run
            $shouldDeactivate = $recurring->end_date &&
                $nextDate->startOfDay()->gt($recurring->end_date->startOfDay());

            // Update recurring model
            $recurring->update([
                'next_run_date' => $nextDate,
                'is_active'     => ! $shouldDeactivate,
            ]);
            
            // Re-assign to model for the next loop condition
            $recurring->next_run_date = $nextDate;
            $recurring->is_active = ! $shouldDeactivate;
        }
    }
}
