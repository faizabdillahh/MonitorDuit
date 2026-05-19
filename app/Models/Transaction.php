<?php

namespace App\Models;

use App\Enums\AiConfidence;
use App\Enums\TransactionSource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'merchant_name',
        'total_amount',
        'amount_idr',
        'exchange_rate',
        'rate_fetched_at',
        'currency',
        'transaction_date',
        'notes',
        'source',
        'ai_confidence',
        'ai_raw_response',
        'receipt_image_path',
        'recurring_transaction_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'total_amount'     => 'decimal:2',
        'amount_idr'       => 'decimal:2',
        'exchange_rate'    => 'decimal:6',
        'rate_fetched_at'  => 'datetime',
        'ai_raw_response'  => 'array',
        'source'           => TransactionSource::class,
        'ai_confidence'    => AiConfidence::class,
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recurringTransaction(): BelongsTo
    {
        return $this->belongsTo(RecurringTransaction::class);
    }
}
