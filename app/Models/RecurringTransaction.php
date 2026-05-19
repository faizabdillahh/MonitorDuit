<?php

namespace App\Models;

use App\Enums\RecurringFrequency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecurringTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'merchant_name',
        'amount',
        'currency',
        'notes',
        'frequency',
        'day_of_month',
        'start_date',
        'end_date',
        'next_run_date',
        'is_active',
    ];

    protected $casts = [
        'amount'        => 'decimal:2',
        'is_active'     => 'boolean',
        'start_date'    => 'date',
        'end_date'      => 'date',
        'next_run_date' => 'date',
        'frequency'     => RecurringFrequency::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
