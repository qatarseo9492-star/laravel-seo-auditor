<?php

namespace App\Traits;

use App\Models\AnalyzeLog;
use Illuminate\Support\Facades\Auth;

trait LogsAnalyzes
{
    protected function logAnalyze(string $analyzer, string $url, bool $success = true, ?int $tokens = null): void
    {
        if (!Auth::check()) return;

        $user = Auth::user();

        $ip = request()->headers->get('CF-Connecting-IP')
            ?? request()->headers->get('X-Forwarded-For')
            ?? request()->ip();

        $country = request()->headers->get('CF-IPCountry')
            ?? request()->headers->get('X-Country')
            ?? null;

        AnalyzeLog::create([
            'user_id'     => $user->id,
            'analyzer'    => $analyzer,
            'url'         => $url,
            'ip'          => $ip,
            'country'     => $country,
            'tokens_used' => $tokens,
            'success'     => $success,
        ]);
    }
}
