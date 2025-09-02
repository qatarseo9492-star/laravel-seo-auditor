<?php

namespace App\Support\Costs;

class OpenAiCost
{
    /**
     * Estimate USD cost given model + token counts.
     *
     * @param string $model           e.g. "gpt-4o-mini"
     * @param int|null $promptTokens  number of input tokens
     * @param int|null $completionTokens number of output tokens
     * @return float|null
     */
    public static function estimate(string $model, ?int $promptTokens, ?int $completionTokens): ?float
    {
        if ($promptTokens === null && $completionTokens === null) {
            return null;
        }

        $m = strtolower(trim($model));

        // Pricing (USD per 1K tokens) as of Sept 2025
        $price = [
            'gpt-4o'        => ['in' => 0.0025,  'out' => 0.0100],
            'gpt-4o-mini'   => ['in' => 0.00015, 'out' => 0.00060],
            'gpt-4-turbo'   => ['in' => 0.0100,  'out' => 0.0300],
            'gpt-3.5-turbo' => ['in' => 0.0005,  'out' => 0.0015],
            // fallback
            'default'       => ['in' => 0.0010,  'out' => 0.0030],
        ];

        $tier = $price['default'];
        foreach ($price as $key => $val) {
            if ($key !== 'default' && str_contains($m, $key)) {
                $tier = $val;
                break;
            }
        }

        $pt = max(0, (int)($promptTokens ?? 0));
        $ct = max(0, (int)($completionTokens ?? 0));

        $cost = ($pt / 1000.0) * $tier['in'] + ($ct / 1000.0) * $tier['out'];

        return round($cost, 6);
    }
}
