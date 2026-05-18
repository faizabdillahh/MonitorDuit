<?php

namespace App\DTOs;

use App\Enums\AiConfidence;

class ScanResult
{
    public function __construct(
        public readonly ?float $totalAmount,
        public readonly ?string $merchantName,
        public readonly ?string $transactionDate,
        public readonly AiConfidence $confidence,
        public readonly array $rawResponse,
        public readonly ?string $error = null,
    ) {}

    public function hasError(): bool
    {
        return $this->error !== null;
    }

    public function isValid(): bool
    {
        return !$this->hasError() && $this->totalAmount !== null;
    }
}
