<?php

namespace App\Services;

use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    private const SUPPORTED = ['IDR', 'USD', 'EUR', 'SGD', 'MYR', 'JPY', 'GBP', 'AUD'];

    public function getRate(string $from, string $to = 'IDR'): ?float
    {
        if ($from === $to) return 1.0;

        // Check database cache (valid for 1 hour)
        $cached = ExchangeRate::where('from_currency', $from)
            ->where('to_currency', $to)
            ->where('fetched_at', '>=', now()->subHour())
            ->first();

        if ($cached) return (float) $cached->rate;

        // Fetch from API
        try {
            $rate = $this->fetchFromApi($from, $to);
            ExchangeRate::updateOrCreate(
                ['from_currency' => $from, 'to_currency' => $to],
                ['rate' => $rate, 'fetched_at' => now()]
            );
            return $rate;
        } catch (\Exception $e) {
            Log::warning("Exchange rate fetch failed: {$from} to {$to}", [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    private function fetchFromApi(string $from, string $to): float
    {
        $apiKey = config('services.exchange_rate.api_key');

        if (empty($apiKey)) {
            throw new \RuntimeException('Exchange rate API key not configured');
        }

        $response = Http::timeout(10)->get(
            "https://v6.exchangerate-api.com/v6/{$apiKey}/pair/{$from}/{$to}"
        );

        if ($response->failed()) {
            throw new \RuntimeException('Exchange rate API failed');
        }

        return (float) $response->json('conversion_rate');
    }

    public function convertToIdr(float $amount, string $currency, ?float $rate = null): array
    {
        if ($currency === 'IDR') {
            return ['amount_idr' => $amount, 'rate' => 1.0];
        }

        $rate = $rate ?? $this->getRate($currency);

        return [
            'amount_idr' => $rate ? round($amount * $rate, 2) : null,
            'rate'       => $rate,
        ];
    }

    public function getSupportedCurrencies(): array
    {
        return self::SUPPORTED;
    }
}
