<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class TransactionService
{
    public function getFilteredTransactions(User $user, array $filters): LengthAwarePaginator
    {
        return Transaction::query()
            ->where('user_id', $user->id)
            ->with(['category'])
            ->when($filters['category'] ?? null, fn($q, $v) => $q->where('category_id', $v))
            ->when($filters['search'] ?? null, fn($q, $v) => $q->where(function ($sq) use ($v) {
                $sq->where('merchant_name', 'like', "%{$v}%")
                   ->orWhere('notes', 'like', "%{$v}%");
            }))
            ->when($filters['date_from'] ?? null, fn($q, $v) => $q->whereDate('transaction_date', '>=', $v))
            ->when($filters['date_to'] ?? null, fn($q, $v) => $q->whereDate('transaction_date', '<=', $v))
            ->when($filters['amount_min'] ?? null, fn($q, $v) => $q->where('total_amount', '>=', $v))
            ->when($filters['amount_max'] ?? null, fn($q, $v) => $q->where('total_amount', '<=', $v))
            ->orderByDesc('transaction_date')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();
    }

    public function store(User $user, array $data): Transaction
    {
        // Handle currency conversion
        $currency = $data['currency'] ?? 'IDR';
        $amountIdr = $data['total_amount'];
        $exchangeRate = 1.0;

        if ($currency !== 'IDR') {
            $currencyService = app(CurrencyService::class);
            $converted = $currencyService->convertToIdr(
                amount:   $data['total_amount'],
                currency: $currency,
                rate:     $data['manual_rate'] ?? null,
            );
            $amountIdr = $converted['amount_idr'] ?? $data['total_amount'];
            $exchangeRate = $converted['rate'] ?? 1.0;
        }

        $transaction = $user->transactions()->create([
            'category_id'        => $data['category_id'],
            'merchant_name'      => $data['merchant_name'] ?? null,
            'total_amount'       => $data['total_amount'],
            'amount_idr'         => $amountIdr,
            'exchange_rate'      => $exchangeRate,
            'rate_fetched_at'    => $currency !== 'IDR' ? now() : null,
            'currency'           => $currency,
            'transaction_date'   => $data['transaction_date'],
            'notes'              => $data['notes'] ?? null,
            'source'             => $data['source'] ?? 'manual',
            'ai_confidence'      => $data['ai_confidence'] ?? null,
            'ai_raw_response'    => $data['ai_raw_response'] ?? null,
            'receipt_image_path' => $data['receipt_image_path'] ?? null,
            'recurring_transaction_id' => $data['recurring_transaction_id'] ?? null,
        ]);

        // Reset reminder
        $user->update(['last_reminded_at' => null]);

        // Check budget notifications
        app(BudgetNotificationService::class)->checkAfterTransaction($transaction);

        return $transaction->load('category');
    }

    public function update(Transaction $transaction, array $data): Transaction
    {
        $updates = [
            'category_id'      => $data['category_id'] ?? $transaction->category_id,
            'merchant_name'    => $data['merchant_name'] ?? $transaction->merchant_name,
            'total_amount'     => $data['total_amount'] ?? $transaction->total_amount,
            'transaction_date' => $data['transaction_date'] ?? $transaction->transaction_date,
            'notes'            => $data['notes'] ?? $transaction->notes,
        ];

        // If total amount changes, recalculate amount_idr based on existing exchange_rate
        if (isset($data['total_amount']) && $data['total_amount'] != $transaction->total_amount) {
            $updates['amount_idr'] = $data['total_amount'] * ($transaction->exchange_rate ?? 1.0);
        }

        $transaction->update($updates);

        return $transaction->fresh('category');
    }

    public function destroy(Transaction $transaction): void
    {
        if ($transaction->receipt_image_path) {
            Storage::delete($transaction->receipt_image_path);
        }

        $transaction->delete();
    }

    public function getRecentTransactions(User $user, int $limit = 5)
    {
        return Transaction::query()
            ->where('user_id', $user->id)
            ->with(['category'])
            ->orderByDesc('transaction_date')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }
}
