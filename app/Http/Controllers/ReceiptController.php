<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScanReceiptRequest;
use App\Models\Transaction;
use App\Services\ReceiptScanService;
use Illuminate\Support\Facades\Storage;

class ReceiptController extends Controller
{
    public function scan(ScanReceiptRequest $request, ReceiptScanService $scanService)
    {
        $imagePath = $request->file('receipt_image')
            ->store('receipts/' . auth()->id(), 'local');

        $result = $scanService->scan($imagePath);

        return response()->json([
            'success'       => !$result->hasError(),
            'total_amount'  => $result->totalAmount,
            'merchant_name' => $result->merchantName,
            'transaction_date' => $result->transactionDate,
            'confidence'    => $result->confidence->value,
            'error'         => $result->error,
            'image_path'    => $imagePath,
        ]);
    }

    public function image(Transaction $transaction)
    {
        abort_if($transaction->user_id !== auth()->id(), 403);
        abort_if(!$transaction->receipt_image_path, 404);

        $path = $transaction->receipt_image_path;

        abort_if(!Storage::exists($path), 404);

        return response()->file(Storage::path($path));
    }
}
