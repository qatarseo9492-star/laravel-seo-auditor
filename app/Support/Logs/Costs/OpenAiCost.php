<?php

namespace App\Support\Costs;

class OpenAiCost
{
    // Pricing per 1 Million tokens (as of late 2024/early 2025 estimates)
    // Input = prompt tokens, Output = completion tokens
    protected const MODEL_PRICING = [
        'gpt-4o' => ['input' => 5.00, 'output' => 15.00],
        'gpt-4o-mini' => ['input' => 0.15, 'output' => 0.60],
        'gpt-4-turbo' => ['input' => 10.00, 'output' => 30.00],
        'gpt-3.5-turbo' => ['input' => 0.50, 'output' => 1.50],
    ];

    public function calculate(string $model, int $promptTokens, int $completionTokens): float
    {
        if (!isset(self::MODEL_PRICING[$model])) {
            // Fallback to a common model if the provided one is not in the list
            $model = 'gpt-4o-mini';
        }

        $pricing = self::MODEL_PRICING[$model];

        $inputCost = ($promptTokens / 1_000_000) * $pricing['input'];
        $outputCost = ($completionTokens / 1_000_000) * $pricing['output'];

        return $inputCost + $outputCost;
    }
}
