<?php

namespace App\Services;

use App\DTOs\ScanResult;
use App\Enums\AiConfidence;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ReceiptScanService
{
    private string $apiKey;
    private string $endpoint;

    public function __construct()
    {
        $this->apiKey   = config('services.gemini.api_key');
        $this->endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';
    }

    public function scan(string $imagePath): ScanResult
    {
        try {
            $imageData = base64_encode(Storage::get($imagePath));
            $mimeType  = Storage::mimeType($imagePath);

            $response = Http::timeout(30)
                ->withQueryParameters(['key' => $this->apiKey])
                ->post($this->endpoint, [
                    'contents' => [[
                        'parts' => [
                            ['inline_data' => ['mime_type' => $mimeType, 'data' => $imageData]],
                            ['text' => $this->buildPrompt()],
                        ]
                    ]],
                    'generationConfig' => ['response_mime_type' => 'application/json'],
                ]);

            if ($response->failed()) {
                Log::error('Gemini API request failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);

                return new ScanResult(
                    totalAmount: null,
                    merchantName: null,
                    transactionDate: null,
                    confidence: AiConfidence::Low,
                    rawResponse: [],
                    error: 'api_error',
                );
            }

            return $this->parseResponse($response->json());
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Gemini API timeout', ['error' => $e->getMessage()]);

            return new ScanResult(
                totalAmount: null,
                merchantName: null,
                transactionDate: null,
                confidence: AiConfidence::Low,
                rawResponse: [],
                error: 'timeout',
            );
        } catch (\Throwable $e) {
            Log::error('Gemini scan unexpected error', ['error' => $e->getMessage()]);

            return new ScanResult(
                totalAmount: null,
                merchantName: null,
                transactionDate: null,
                confidence: AiConfidence::Low,
                rawResponse: [],
                error: 'unknown_error',
            );
        }
    }

    private function buildPrompt(): string
    {
        return <<<PROMPT
        Kamu adalah asisten pembaca struk belanja. Dari gambar yang diberikan, ekstrak:
        - total_price: total harga akhir (angka saja, tanpa simbol mata uang)
        - currency: mata uang (default "IDR")
        - transaction_date: tanggal format YYYY-MM-DD (null jika tidak ada)
        - merchant_name: nama toko (null jika tidak ada)
        - confidence: high / medium / low

        Kembalikan HANYA JSON valid. Jika bukan struk, kembalikan {"error": "not_a_receipt"}.
        PROMPT;
    }

    private function parseResponse(array $raw): ScanResult
    {
        try {
            $text   = $raw['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
            $parsed = json_decode($text, true) ?? [];

            if (isset($parsed['error'])) {
                return new ScanResult(
                    totalAmount: null,
                    merchantName: null,
                    transactionDate: null,
                    confidence: AiConfidence::Low,
                    rawResponse: $raw,
                    error: $parsed['error'],
                );
            }

            return new ScanResult(
                totalAmount:     isset($parsed['total_price']) ? (float) $parsed['total_price'] : null,
                merchantName:    $parsed['merchant_name'] ?? null,
                transactionDate: $parsed['transaction_date'] ?? null,
                confidence:      AiConfidence::tryFrom($parsed['confidence'] ?? '') ?? AiConfidence::Low,
                rawResponse:     $raw,
            );
        } catch (\Throwable $e) {
            Log::error('Gemini response parse error', [
                'error' => $e->getMessage(),
                'raw'   => $raw,
            ]);

            return new ScanResult(
                totalAmount: null,
                merchantName: null,
                transactionDate: null,
                confidence: AiConfidence::Low,
                rawResponse: $raw,
                error: 'parse_error',
            );
        }
    }
}
