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
use App\Models\Search;
use App\Models\UserLimit;
use App\Models\AnalysisCache;
use App\Models\AnalyzeLog;
use App\Models\OpenAiUsage;
use App\Support\Logs\UsageLogger;
use App\Support\Costs\OpenAiCost;
use Illuminate\Support\Facades\Auth;

class AnalyzerController extends Controller
{
    /**
     * Checks user limits and logs the search action.
     */
    private function checkAndLogSearch(string $url, Request $request)
    {
        $user = Auth::user();
        if (!$user) return true; // Don't block non-logged-in users if that's the desired behavior

        $limit = UserLimit::firstOrCreate(['user_id' => $user->id]);
        
        // Reset daily counter if the last update was not today
        if (!$limit->updated_at->isToday()) {
            $limit->searches_today = 0;
        }
        // Reset monthly counter if the last update was not this month
        if (!$limit->updated_at->isSameMonth(now())) {
            $limit->searches_this_month = 0;
        }
        
        if ($limit->searches_today >= $limit->daily_limit || $limit->searches_this_month >= $limit->monthly_limit) {
            return response()->json(['error' => 'You have reached your usage quota.'], 429);
        }

        // Log the analysis attempt
        (new UsageLogger())->logAnalysis($request, 'analyzer', true);

        // Increment counters and update the timestamp
        $limit->increment('searches_today');
        $limit->increment('searches_this_month');
        $limit->touch();

        return true;
    }

    /**
     * Central AI analysis logic with caching.
     */
    private function performAiAnalysis(string $urlToAnalyze, string $prompt, string $cacheType, array $expectedKeys)
    {
        $cached = AnalysisCache::where('url', $urlToAnalyze)
            ->where('type', $cacheType)
            ->where('created_at', '>=', now()->subDay())
            ->latest()
            ->first();

        if ($cached) {
            return response()->json($cached->results);
        }

        $apiKey = env('OPENAI_API_KEY');
        if (!$apiKey) return response()->json(['message' => 'OpenAI API key is not configured.'], 500);

        try {
            $response = Http::withToken($apiKey)->timeout(90)->post('https://api.openai.com/v1/chat/completions', [
                'model' => env('OPENAI_MODEL', 'gpt-4-turbo'),
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a world-class SEO expert. Respond only with the requested JSON object.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'response_format' => ['type' => 'json_object']
            ]);

            if ($response->failed()) {
                return response()->json(['message' => "Failed to get a response from the AI service for {$cacheType} analysis."], 502);
            }

            $result = json_decode($response->json('choices.0.message.content'), true);

            foreach ($expectedKeys as $key) {
                if (!isset($result[$key]) || !is_array($result[$key])) {
                    $result[$key] = [];
                }
            }

            AnalysisCache::create([
                'url' => $urlToAnalyze,
                'type' => $cacheType,
                'results' => $result,
            ]);
            
            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json(['message' => "An unexpected error occurred during the {$cacheType} analysis."], 500);
        }
    }

    /**
     * Handles the initial local HTML parsing.
     */
    public function analyze(Request $request)
    {
        $validated = $request->validate(['url' => ['required', 'url']]);
        $urlToAnalyze = $validated['url'];

        $limitCheck = $this->checkAndLogSearch($urlToAnalyze, $request);
        if ($limitCheck !== true) return $limitCheck;

        $contentStructure = []; $pageSignals = []; $quickStats = [];

        try {
            $response = Http::timeout(15)->get($urlToAnalyze);
            if ($response->failed()) {
                return response()->json(['ok' => false, 'error' => "Failed to fetch the provided URL. Status: {$response->status()}"], 400);
            }
            $html = $response->body();
            $dom = new DOMDocument();
            @$dom->loadHTML($html);
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
            $quickStats['schema_types'] = [];
        } catch (\Exception $e) {
            Log::error('Local HTML Parsing Failed', ['message' => $e->getMessage()]);
            return response()->json(['ok' => false, 'error' => "Could not parse the URL's HTML. It may be malformed or protected."], 500);
        }

        return response()->json([
            "ok" => true,
            "content_structure" => $contentStructure,
            "page_signals" => $pageSignals,
            "quick_stats" => $quickStats,
        ]);
    }

    /**
     * Handles the PageSpeed Insights API proxy with caching.
     */
    public function psiProxy(Request $request)
    {
        $validated = $request->validate(['url' => 'required|url']);
        $url = $validated['url'];

        $cfg = config('services.pagespeed', []);
        $key = $cfg['key'] ?? env('PAGESPEED_API_KEY');
        $endpoint = $cfg['endpoint'] ?? 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';
        $timeout = (int)($cfg['timeout'] ?? 40);
        $ttl = (int)($cfg['cache_ttl'] ?? 60 * 15);

        if (!$key) {
            return response()->json(['ok' => false, 'error' => 'The PageSpeed Insights API key is not configured on the server.'], 500);
        }
        
        $fetch = function (string $strategy) use ($endpoint, $url, $key, $timeout, $ttl) {
            $cacheKey = "psi:{$strategy}:" . md5($url);
            return Cache::remember($cacheKey, $ttl, function () use ($endpoint, $url, $key, $timeout, $strategy) {
                $res = Http::timeout($timeout)->get($endpoint, [ 'url' => $url, 'strategy' => $strategy, 'category' => 'performance', 'key' => $key ]);
                if (!$res->ok()) return ['ok' => false, 'score' => 0];
                $j = $res->json() ?: []; $lr = $j['lighthouseResult'] ?? []; $audits = $lr['audits'] ?? []; $perfRaw = $lr['categories']['performance']['score'] ?? null;
                return [
                    'ok' => true, 'score' => is_null($perfRaw) ? 0 : (int) round($perfRaw * 100),
                    'lcp_s' => round(($audits['largest-contentful-paint']['numericValue'] ?? 0) / 1000, 2),
                    'cls' => round($audits['cumulative-layout-shift']['numericValue'] ?? 0, 3),
                    'inp_ms' => (int) round($audits['interaction-to-next-paint']['numericValue'] ?? 0),
                    'ttfb_ms' => (int) round($audits['server-response-time']['numericValue'] ?? 0),
                ];
            });
        };

        return response()->json([ 'ok' => true, 'url' => $url, 'mobile' => $fetch('mobile'), 'desktop' => $fetch('desktop') ]);
    }
    
    // AI analysis methods
    public function analyzeContentOptimization(Request $request) {
        $validated = $request->validate(['url' => 'required|url']);
        $url = $validated['url'];
        $prompt = "Analyze the content at '{$url}' for SEO. Return JSON with a root key 'content_optimization' containing: 'nlp_score' (0-100), 'topic_coverage' {'percentage', 'total', 'covered'}, 'content_gaps' {'missing_topics':[{'term', 'severity'}]}, 'schema_suggestions' (array of strings), and 'readability_intent' {'intent', 'grade_level'}.";
        return $this->performAiAnalysis($url, $prompt, 'content_optimization', ['content_optimization']);
    }

    public function analyzeTechnicalSeo(Request $request) {
        $validated = $request->validate(['url' => 'required|url']);
        $url = $validated['url'];
        $prompt = "Analyze technical SEO of {$url}. Return JSON with: 'score' (0-100), 'internal_linking':[{'text','anchor'}], 'url_structure':{'clarity_score','suggestion'}, 'meta_optimization':{'title','description'}, 'alt_text_suggestions':[{'image_src','suggestion'}], 'site_structure_map' (HTML ul string), and 'suggestions':[{'text','type'}].";
        return $this->performAiAnalysis($url, $prompt, 'technical_seo', ['internal_linking', 'alt_text_suggestions', 'suggestions']);
    }

    public function analyzeKeywords(Request $request) {
        $validated = $request->validate(['url' => 'required|url']);
        $url = $validated['url'];
        $prompt = "Perform a keyword intelligence analysis for {$url}. Return JSON with: 'semantic_research' (array of 5-7 variations), 'intent_classification' (array of {'keyword','intent'}), 'related_terms' (array of 5-7 terms), 'competitor_gaps' (array of 3-5 opportunities), and 'long_tail_suggestions' (array of 3-5 recommendations).";
        return $this->performAiAnalysis($url, $prompt, 'keyword_intelligence', ['semantic_research', 'intent_classification', 'related_terms', 'competitor_gaps', 'long_tail_suggestions']);
    }

    public function analyzeContentEngine(Request $request) {
        $validated = $request->validate(['url' => 'required|url']);
        $url = $validated['url'];
        $prompt = "As a Content Analysis Engine, analyze {$url}. Return JSON with 'score' (0-100), 'topic_clusters' (array of strings), 'entities' (array of {'term', 'type'}), 'semantic_keywords' (array of strings), 'relevance_score' (0-100), and 'context_intent' (string).";
        return $this->performAiAnalysis($url, $prompt, 'content_engine', ['topic_clusters', 'entities', 'semantic_keywords']);
    }

    // Legacy and placeholder methods
    public function analyzeWeb(Request $request) { return $this->analyze($request); }
    public function semanticAnalyze(Request $request) { return $this->analyze($request); }
    public function psi(Request $request) { return response()->json(['ok' => true, 'note' => 'PSI proxy not implemented here.']); }
    public function aiCheck(Request $request) { return response()->json(['ok' => true, 'note' => 'aiCheck stub']); }
    public function topicClusterAnalyze(Request $request) { return response()->json(['ok' => true, 'note' => 'topicClusterAnalyze stub']); }
}
