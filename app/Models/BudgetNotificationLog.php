<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetNotificationLog extends Model
{
    protected $fillable = [
        'budget_id',
        'type',
        'month',
        'year',
    ];

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }
}
