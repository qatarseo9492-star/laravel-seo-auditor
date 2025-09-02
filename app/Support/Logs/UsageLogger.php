<?php

namespace App\Support\Logs;

use App\Models\AnalyzeLog;
use App\Models\OpenAiUsage;
use Illuminate\Http\Request;

trait UsageLogger
{
    protected function getClientCountry(Request $request): ?string
    {
        // common headers set by Cloudflare / proxies
        $h = $request->headers;
        $c = $h->get('CF-IPCountry') ?: $h->get('X-Country-Code') ?: null;
        return $c ? strtoupper(substr($c, 0, 2)) : null;
    }

    protected function logAnalyze(Request $request, array $data): AnalyzeLog
    {
        /** @var \App\Models\User|null $user */
        $user = $request->user();

        return AnalyzeLog::create([
            'user_id'     => $user?->id,
            'analyzer'    => $data['analyzer'] ?? 'semantic',   // semantic|psi|ai|other
            'url'         => $data['url'] ?? ($request->input('url') ?? ''),
            'ip'          => $request->ip(),
            'country'     => $this->getClientCountry($request),
            'tokens_used' => $data['tokens_used'] ?? null,
            'success'     => $data['success'] ?? true,
        ]);
    }

    /**
     * $usage = [
     *   'model' => 'gpt-4o-mini',
     *   'prompt_tokens' => 123,
     *   'completion_tokens' => 456,
     *   'total_tokens' => 579,
     *   'cost_usd' => 0.0123,
     *   'meta' => ['endpoint' => 'semantic.analyze'],
     * ];
     */
    protected function logOpenAiUsage(Request $request, array $usage): OpenAiUsage
    {
        /** @var \App\Models\User|null $user */
        $user = $request->user();

        return OpenAiUsage::create([
            'user_id'          => $user?->id,
            'model'            => $usage['model']            ?? null,
            'prompt_tokens'    => $usage['prompt_tokens']    ?? null,
            'completion_tokens'=> $usage['completion_tokens']?? null,
            'total_tokens'     => $usage['total_tokens']     ?? null,
            'cost_usd'         => $usage['cost_usd']         ?? null,
            'meta'             => $usage['meta']             ?? null,
        ]);
    }
}
