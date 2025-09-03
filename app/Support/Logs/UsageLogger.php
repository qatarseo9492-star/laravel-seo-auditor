<?php

namespace App\Support\Logs;

use App\Models\AnalyzeLog;
use App\Models\OpenAiUsage;
use App\Support\Costs\OpenAiCost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsageLogger
{
    public function logAnalysis(Request $request, string $tool, bool $successful): ?AnalyzeLog
    {
        if (!Auth::check()) {
            return null;
        }

        return AnalyzeLog::create([
            'user_id' => Auth::id(),
            'tool' => $tool,
            'url' => $request->input('url'),
            'ip_address' => $request->ip(),
            'country' => $this->getCountryFromIp($request->ip()),
            'successful' => $successful,
        ]);
    }

    public function logOpenAiUsage(AnalyzeLog $log, string $model, int $promptTokens, int $completionTokens)
    {
        $cost = (new OpenAiCost())->calculate($model, $promptTokens, $completionTokens);

        OpenAiUsage::create([
            'user_id' => $log->user_id,
            'analyze_log_id' => $log->id,
            'model' => $model,
            'prompt_tokens' => $promptTokens,
            'completion_tokens' => $completionTokens,
            'total_tokens' => $promptTokens + $completionTokens,
            'cost' => $cost,
        ]);
    }
    
    private function getCountryFromIp(string $ip): string
    {
        // In a real app, use a service like MaxMind GeoIP2 or an API.
        // For this example, we'll return a placeholder.
        return 'United States';
    }
}
