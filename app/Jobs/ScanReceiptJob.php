<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Services\ReceiptScanService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ScanReceiptJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(
        public readonly int $transactionId,
        public readonly string $imagePath,
    ) {}

    public function handle(ReceiptScanService $service): void
    {
        $result = $service->scan($this->imagePath);

        $transaction = Transaction::find($this->transactionId);
        if (!$transaction) {
            return;
        }

        $updateData = [
            'ai_raw_response' => $result->rawResponse,
            'source'          => 'ai',
        ];

        if ($result->isValid()) {
            $updateData['total_amount']     = $result->totalAmount;
            $updateData['merchant_name']    = $result->merchantName;
            $updateData['transaction_date'] = $result->transactionDate ?? now()->format('Y-m-d');
            $updateData['ai_confidence']    = $result->confidence->value;
        } else {
            $updateData['ai_confidence'] = 'low';
        }

        $transaction->update($updateData);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Gemini scan job failed', [
            'transaction_id' => $this->transactionId,
            'error'          => $exception->getMessage(),
        ]);
    }
}
