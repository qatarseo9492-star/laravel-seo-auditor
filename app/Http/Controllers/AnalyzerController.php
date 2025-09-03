<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use DOMDocument;
use DOMXPath;
use App\Models\User;
use App\Models\UserLimit;
use App\Models\AnalysisCache;
use App\Support\Logs\UsageLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class AnalyzerController extends Controller
{
    /**
     * Centralized function to check limits and log any analysis tool usage.
     */
    private function checkAndLog(Request $request, string $tool): bool|JsonResponse
    {
        if (!Auth::check()) return true;

        $user = Auth::user();
        $limit = UserLimit::firstOrCreate(['user_id' => $user->id]);

        if (!$limit->updated_at->isToday()) $limit->searches_today = 0;
        if (!$limit->updated_at->isSameMonth(now())) $limit->searches_this_month = 0;

        if ($limit->searches_today >= $limit->daily_limit || $limit->searches_this_month >= $limit->monthly_limit) {
            return response()->json(['error' => 'You have reached your usage quota.'], 429);
        }

        (new UsageLogger())->logAnalysis($request, $tool, true);

        $limit->increment('searches_today');
        $limit->increment('searches_this_month');
        $limit->touch();

        return true;
    }

    /**
     * Handles only the local HTML parsing. This does not count against AI quota.
     */
    public function analyze(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate(['url' => ['required', 'url']]);
            $urlToAnalyze = $validated['url'];

            $contentStructure = []; $pageSignals = []; $quickStats = [];
            $response = Http::timeout(15)->get($urlToAnalyze);
            if ($response->failed()) return response()->json(['ok' => false, 'error' => "Failed to fetch URL. Status: {$response->status()}"], 400);

            $html = $response->body();
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($html);
            libxml_clear_errors();
            $xpath = new DOMXPath($dom);

            $contentStructure['title'] = optional($xpath->query('//title')->item(0))->textContent;
            $contentStructure['meta_description'] = optional($xpath->query("//meta[@name='description']/@content")->item(0))->nodeValue;
            $contentStructure['headings'] = [];
            foreach (['h1', 'h2', 'h3', 'h4'] as $tag) {
                $headings = $xpath->query('//' . $tag);
                $tagUpper = strtoupper($tag);
                $contentStructure['headings'][$tagUpper] = [];
                foreach ($headings as $heading) { $contentStructure['headings'][$tagUpper][] = trim($heading->textContent); }
            }
            $pageSignals['canonical'] = optional($xpath->query("//link[@rel='canonical']/@href")->item(0))->nodeValue;
            $pageSignals['robots'] = optional($xpath->query("//meta[@name='robots']/@content")->item(0))->nodeValue;
            $pageSignals['has_viewport'] = $xpath->query("//meta[@name='viewport']")->length > 0;
            $links = $dom->getElementsByTagName('a');
            $internalLinks = 0;
            $host = parse_url($urlToAnalyze, PHP_URL_HOST) ?? '';
            foreach ($links as $link) {
                $href = $link->getAttribute('href');
                if (Str::startsWith($href, '/') || (Str::contains($href, $host) && Str::startsWith($href, 'http'))) {
                    $internalLinks++;
                }
            }
            $quickStats['internal_links'] = $internalLinks;

            return response()->json(["ok" => true, "content_structure" => $contentStructure, "page_signals" => $pageSignals, "quick_stats" => $quickStats]);
        } catch (\Exception $e) {
            Log::error('Local HTML Parsing Failed', ['message' => $e->getMessage()]);
            return response()->json(['ok' => false, 'error' => "Could not parse the URL's HTML.", 'detail' => $e->getMessage()], 500);
        }
    }

    /**
     * A single, robust handler for all OpenAI requests with improved error handling.
     */
    private function handleAiRequest(Request $request, string $toolName, string $promptTemplate, array $expectedKeys): JsonResponse
    {
        try {
            $validated = $request->validate(['url' => 'required|url']);
            $url = $validated['url'];

            if (($limitCheck = $this->checkAndLog($request, $toolName)) !== true) return $limitCheck;

            $cached = AnalysisCache::where('url', $url)->where('type', $toolName)->where('created_at', '>=', now()->subDay())->latest()->first();
            if ($cached) return response()->json($cached->results);

            $apiKey = env('OPENAI_API_KEY');
            if (!$apiKey) return response()->json(['message' => 'AI API key not configured.'], 500);

            $prompt = str_replace('{{URL}}', $url, $promptTemplate);

            $response = Http::withToken($apiKey)->timeout(90)->post('https://api.openai.com/v1/chat/completions', [
                'model' => env('OPENAI_MODEL', 'gpt-4-turbo'),
                'messages' => [['role' => 'system', 'content' => 'You are a world-class SEO expert. Respond only with the requested JSON object.'], ['role' => 'user', 'content' => $prompt]],
                'response_format' => ['type' => 'json_object']
            ]);

            if ($response->failed()) {
                Log::error("AI API Call Failed for {$toolName}", ['status' => $response->status(), 'body' => $response->body()]);
                return response()->json(['message' => "The AI service returned an error for the {$toolName} analysis.", 'detail' => $response->body()], 502);
            }

            $rawContent = $response->json('choices.0.message.content');
            if (empty($rawContent)) {
                Log::error("Empty content from AI for {$toolName}", ['response' => $response->json()]);
                return response()->json(['message' => "The AI service returned an empty response for the {$toolName} analysis."], 500);
            }

            $result = json_decode($rawContent, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error("Invalid JSON from AI for {$toolName}", ['body' => $rawContent]);
                return response()->json(['message' => "The AI service returned invalid JSON for the {$toolName} analysis."], 500);
            }

            foreach ($expectedKeys as $key) {
                if (!isset($result[$key])) $result[$key] = []; // Ensure root keys exist
            }

            AnalysisCache::create(['url' => $url, 'type' => $toolName, 'results' => $result]);
            
            return response()->json($result);

        } catch (\Exception $e) {
            Log::error("AI Analysis Exception for {$toolName}", ['message' => $e->getMessage()]);
            return response()->json(['message' => "A server error occurred during the {$toolName} analysis.", 'detail' => $e->getMessage()], 500);
        }
    }

    // All AI endpoints use the central handler
    public function analyzeContentOptimization(Request $request) {
        $prompt = "Analyze content at '{{URL}}' for SEO. Return JSON with 'content_optimization' containing: 'nlp_score' (0-100), 'topic_coverage' {'percentage', 'total', 'covered'}, 'content_gaps' {'missing_topics':[{'term', 'severity'}]}, 'schema_suggestions' (array of strings), and 'readability_intent' {'intent', 'grade_level'}.";
        return $this->handleAiRequest($request, 'content_optimization', $prompt, ['content_optimization']);
    }

    public function analyzeTechnicalSeo(Request $request) {
        $prompt = "Analyze technical SEO of {{URL}}. Return JSON with: 'score' (0-100), 'internal_linking':[{'text','anchor'}], 'url_structure':{'clarity_score','suggestion'}, 'meta_optimization':{'title','description'}, 'alt_text_suggestions':[{'image_src','suggestion'}], 'site_structure_map' (HTML ul string), and 'suggestions':[{'text','type'}].";
        return $this->handleAiRequest($request, 'technical_seo', $prompt, ['internal_linking', 'alt_text_suggestions', 'suggestions']);
    }

    public function analyzeKeywords(Request $request) {
        $prompt = "Perform a keyword intelligence analysis for {{URL}}. Return JSON with: 'semantic_research' (5-7 variations), 'intent_classification' ({'keyword','intent'}), 'related_terms' (5-7 terms), 'competitor_gaps' (3-5 opportunities), and 'long_tail_suggestions' (3-5 recommendations).";
        return $this->handleAiRequest($request, 'keyword_intelligence', $prompt, ['semantic_research', 'intent_classification', 'related_terms', 'competitor_gaps', 'long_tail_suggestions']);
    }

    public function analyzeContentEngine(Request $request) {
        $prompt = "As a Content Analysis Engine, analyze {{URL}}. Return JSON with 'score' (0-100), 'topic_clusters', 'entities':[{'term', 'type'}], 'semantic_keywords', 'relevance_score' (0-100), and 'context_intent'.";
        return $this->handleAiRequest($request, 'content_engine', $prompt, ['topic_clusters', 'entities', 'semantic_keywords']);
    }

    public function psiProxy(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate(['url' => 'required|url']);
            $url = $validated['url'];

            if (($limitCheck = $this->checkAndLog($request, 'psi')) !== true) return $limitCheck;

            $cfg = config('services.pagespeed', []);
            $key = $cfg['key'] ?? env('PAGESPEED_API_KEY');
            if (!$key) return response()->json(['ok' => false, 'error' => 'PageSpeed API key is not configured.'], 500);

            $fetch = function (string $strategy) use ($url, $key) {
                $cacheKey = "psi:{$strategy}:" . md5($url);
                return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($url, $key, $strategy) {
                    $res = Http::timeout(40)->get('https://www.googleapis.com/pagespeedonline/v5/runPagespeed', ['url' => $url, 'strategy' => $strategy, 'category' => 'performance', 'key' => $key]);
                    if (!$res->ok()) return ['ok' => false, 'score' => 0, 'opportunities' => ['Failed to fetch PSI data.']];
                    $j = $res->json() ?: []; $lr = $j['lighthouseResult'] ?? []; $audits = $lr['audits'] ?? []; $perfRaw = $lr['categories']['performance']['score'] ?? null;
                    $opportunities = collect($lr['audits'] ?? [])->filter(fn($audit) => ($audit['score'] ?? 1) < 0.9 && isset($audit['details']['overallSavingsMs']) && $audit['details']['overallSavingsMs'] > 100)->map(fn($audit) => $audit['title'])->values()->toArray();
                    return [
                        'ok' => true, 'score' => is_null($perfRaw) ? 0 : (int) round($perfRaw * 100),
                        'lcp_s' => round(($audits['largest-contentful-paint']['numericValue'] ?? 0) / 1000, 2),
                        'cls' => round($audits['cumulative-layout-shift']['numericValue'] ?? 0, 3),
                        'inp_ms' => (int) round($audits['interaction-to-next-paint']['numericValue'] ?? 0),
                        'opportunities' => $opportunities,
                    ];
                });
            };

            return response()->json(['ok' => true, 'url' => $url, 'mobile' => $fetch('mobile'), 'desktop' => $fetch('desktop')]);
        } catch (\Exception $e) {
            Log::error('PSI Proxy Failed', ['message' => $e->getMessage()]);
            return response()->json(['ok' => false, 'error' => 'An unexpected error occurred during the PageSpeed analysis.', 'detail' => $e->getMessage()], 500);
        }
    }
}
