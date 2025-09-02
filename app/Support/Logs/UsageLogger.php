<?php

namespace App\Support\Logs;

use App\Models\AnalyzeLog;
use App\Models\OpenAiUsage;
use Illuminate\Http\Request;
use App\Support\Costs\OpenAiCost;

trait UsageLogger
{
    /**
     * Try to resolve a 2-letter country code from common proxy/CDN headers.
     */
    protected function getClientCountry(Request $request): ?string
    {
        $h = $request->headers;
        $c = $h->get('CF-IPCountry') ?: $h->get('X-Country-Code') ?: $h->get('X-App-Country') ?: null;
        return $c ? strtoupper(substr($c, 0, 2)) : null;
    }

    /**
     * Write a row to analyze_logs.
     *
     * $data = [
     *   'analyzer'    => 'semantic' | 'psi' | 'ai' | 'other',
     *   'url'         => 'https://example.com',
     *   'tokens_used' => 123,            // optional
     *   'success'     => true|false,     // default true
     * ]
     */
    protected function logAnalyze(Request $request, array $data): AnalyzeLog
    {
        /** @var \App\Models\User|null $user */
        $user = $request->user();

        return AnalyzeLog::create([
            'user_id'     => $user?->id,
            'analyzer'    => $data['analyzer'] ?? 'semantic',
            'url'         => $data['url']      ?? (string) $request->input('url', ''),
            'ip'          => $request->ip(),
            'country'     => $this->getClientCountry($request),
            'tokens_used' => $data['tokens_used'] ?? null,
            'success'     => array_key_exists('success', $data) ? (bool)$data['success'] : true,
        ]);
    }

    /**
     * Write a row to openai_usage with automatic cost estimation when not provided.
     *
     * $usage = [
     *   'model'             => 'gpt-4o-mini',
     *   'prompt_tokens'     => 100,
     *   'completion_tokens' => 200,
     *   'total_tokens'      => 300,
     *   'cost_usd'          => null,         // leave null to auto-estimate
     *   'meta'              => ['endpoint' => 'semantic.analyzeWeb'],
     * ]
     */
    protected function logOpenAiUsage(Request $request, array $usage): OpenAiUsage
    {
        /** @var \App\Models\User|null $user */
        $user = $request->user();

        // Auto-estimate cost if not provided
        $cost = $usage['cost_usd'] ?? null;
        if ($cost === null) {
            $cost = OpenAiCost::estimate(
                $usage['model']             ?? '',
                $usage['prompt_tokens']     ?? null,
                $usage['completion_tokens'] ?? null
            );
        }

        return OpenAiUsage::create([
            'user_id'           => $user?->id,
            'model'             => $usage['model']             ?? null,
            'prompt_tokens'     => $usage['prompt_tokens']     ?? null,
            'completion_tokens' => $usage['completion_tokens'] ?? null,
            'total_tokens'      => $usage['total_tokens']      ?? null,
            'cost_usd'          => $cost,
            'meta'              => $usage['meta']              ?? null,
        ]);
    }
}
