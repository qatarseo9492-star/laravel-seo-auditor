<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Services\TechnicalSeoService;
use Illuminate\Support\Str;

class TechnicalSeoController extends Controller
{
    public function __construct(private TechnicalSeoService $svc) {}

    public function analyze(Request $req)
    {
        $req->validate(['url' => 'required|url']);
        $url = rtrim($req->input('url'));

        // cache per URL for 30 minutes
        $cacheKey = 'techseo:' . md5($url);
        $data = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($url) {
            return $this->svc->analyze($url);
        });

        return response()->json($data);
    }
}
