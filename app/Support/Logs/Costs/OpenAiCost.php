<?php

namespace App\Support\Costs;

class OpenAiCost
{
    /**
     * Return estimated USD cost for a given model and token counts.
     * Prices are per 1K tokens. Override via env if needed.
     */
    public static function estimate(string $model, ?int $promptTokens, ?int $completionTokens): ?float
    {
        if ($promptTokens === null && $completionTokens === null) {
            return null;
        }

        $m = strtolower(trim($model));

        // Baseline price map (USD / 1K tokens). Adjust as needed.
        $price = [
            'gpt-4o'       => ['in' => env('PRICE_GPT4O_IN', 0.0025), 'out' => env('PRICE_GPT4O_OUT', 0.0100)],
            'gpt-4-turbo'  => ['in' => env('PRICE_GPT4T_IN', 0.0100), 'out' => env('PRICE_GPT4T_OUT', 0.0300)],
            'gpt-4o-mini'  => ['in' => env('PRICE_4OMINI_IN', 0.000150), 'out' => env('PRICE_4OMINI_OUT', 0.000600)],
            // fallback for unknown models
            'default'      => ['in' => env('PRICE_DEFAULT_IN', 0.0010), 'out' => env('PRICE_DEFAULT_OUT', 0.0030)],
        ];

        $tier = $price['default'];
        foreach ($price as $key => $val) {
            if ($key !== 'default' && str_contains($m, $key)) {
                $tier = $val; break;
            }
        }

        $pt = max(0, (int)($promptTokens ?? 0));
        $ct = max(0, (int)($completionTokens ?? 0));

        $cost = ($pt/1000.0) * $tier['in'] + ($ct/1000.0) * $tier['out'];

        return round($cost, 6); // nice granularity for tiny calls
    }
}
