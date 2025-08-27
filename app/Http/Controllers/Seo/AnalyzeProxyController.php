<?php

namespace App\Http\Controllers\Seo;

use App\Http\Controllers\Controller;
use App\Services\UrlAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AnalyzeProxyController extends Controller
{
    public function __invoke(Request $request, UrlAnalysisService $svc)
    {
        $data = $request->validate([ 'url' => ['required','url'] ]);
        try {
            $out = $svc->analyzeUrl($data['url'], Auth::id());
            return response()->json($out);
        } catch (\Throwable $e) {
            Log::error('AnalyzeProxy error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Failed to analyze URL.'], 422);
        }
    }
}
