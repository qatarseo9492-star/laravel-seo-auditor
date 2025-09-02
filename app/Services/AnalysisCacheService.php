<?php

namespace App\Services;

use App\Models\AnalysisCache;

class AnalysisCacheService
{
    public function remember(string $feature, string $url, int $ttlMinutes, callable $callback): array
    {
        $cacheKey = hash('sha256', strtolower($feature.'|'.trim($url)));

        // Try existing cache
        $cached = AnalysisCache::where('cache_key', $cacheKey)
            ->where('feature', $feature)
            ->where(function($q){
                $q->whereNull('expires_at')->orWhere('expires_at','>', now());
            })
            ->first();

        if ($cached) {
            $cached->increment('hit_count');
            $cached->update(['last_accessed_at'=>now()]);
            return $cached->payload;
        }

        // Run expensive analysis
        $result = $callback();

        // Save result to cache
        AnalysisCache::create([
            'feature'        => $feature,
            'url'            => $url,
            'cache_key'      => $cacheKey,
            'ok'             => $result['ok'] ?? true,
            'status'         => $result['status'] ?? 200,
            'scores'         => $result['scores'] ?? null,
            'payload'        => $result,
            'hit_count'      => 0,
            'computed_at'    => now(),
            'expires_at'     => now()->addMinutes($ttlMinutes),
            'last_accessed_at'=> now(),
        ]);

        return $result;
    }
}
