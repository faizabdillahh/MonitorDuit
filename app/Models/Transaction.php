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
        'currency',
        'transaction_date',
        'notes',
        'source',
        'ai_confidence',
        'ai_raw_response',
        'receipt_image_path',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'total_amount'     => 'decimal:2',
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
}
