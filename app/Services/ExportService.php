<?php

namespace App\Services;

use App\Models\Export;
use App\Models\Transaction;
use App\Models\User;

class ExportService
{
    public function getExportData(User $user, array $filters): \Illuminate\Support\Collection
    {
        return Transaction::query()
            ->where('user_id', $user->id)
            ->with('category')
            ->when($filters['date_from'] ?? null, fn($q, $v) => $q->whereDate('transaction_date', '>=', $v))
            ->when($filters['date_to'] ?? null, fn($q, $v) => $q->whereDate('transaction_date', '<=', $v))
            ->when($filters['category'] ?? null, fn($q, $v) => $q->where('category_id', $v))
            ->orderByDesc('transaction_date')
            ->get();
    }

    public function createExportRecord(User $user, array $filters): Export
    {
        return $user->exports()->create([
            'status'  => 'pending',
            'filters' => $filters,
        ]);
    }
}
