<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TechnicalSeoService;
use App\Services\AnalysisCacheService;
use App\Models\AnalysisCache;

class TechnicalSeoController extends Controller
{
    public function __construct(
        private TechnicalSeoService $svc,
        private AnalysisCacheService $cache
    ) {}

    public function analyze(Request $req)
    {
        $req->validate(['url' => 'required|url']);
        $url = rtrim($req->input('url'));

        // Optional: allow force refresh with ?refresh=1
        if ($req->boolean('refresh')) {
            AnalysisCache::where('feature', 'techseo.analyze')
                ->where('url', $url)
                ->delete();
        }

        // Cache for 12 hours (720 minutes)
        $data = $this->cache->remember('techseo.analyze', $url, 720, function () use ($url) {
            return $this->svc->analyze($url);
        });

        return response()->json($data);
    }
}
