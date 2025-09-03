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
use Illuminate\Http\JsonResponse;

class AnalyzerController extends Controller
{
    /**
     * Checks user limits and logs the search action for a specific tool.
     */
    private function checkAndLogSearch(string $url, string $tool, Request $request): bool|JsonResponse
    {
        $user = Auth::user();
        if (!$user) return true;

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
     * Central AI analysis logic with caching.
     */
    private function performAiAnalysis(string $urlToAnalyze, string $prompt, string $cacheType, array $expectedKeys): JsonResponse
    {
        // ... This function remains unchanged ...
        $cached = AnalysisCache::where('url', $urlToAnalyze)->where('type', $cacheType)->where('created_at', '>=', now()->subDay())->latest()->first();
        if ($cached) return response()->json($cached->results);

        $apiKey = env('OPENAI_API_KEY');
        if (!$apiKey) return response()->json(['message' => 'OpenAI API key is not configured.'], 500);

        try {
            $response = Http::withToken($apiKey)->timeout(90)->post('https://api.openai.com/v1/chat/completions', [
                'model' => env('OPENAI_MODEL', 'gpt-4-turbo'),
                'messages' => [['role' => 'system', 'content' => 'You are a world-class SEO expert. Respond only with the requested JSON object.'], ['role' => 'user', 'content' => $prompt]],
                'response_format' => ['type' => 'json_object']
            ]);

            if ($response->failed()) return response()->json(['message' => "Failed to get a response from the AI service for {$cacheType} analysis."], 502);

            $result = json_decode($response->json('choices.0.message.content'), true);
            foreach ($expectedKeys as $key) {
                if (!isset($result[$key]) || !is_array($result[$key])) $result[$key] = [];
            }

            AnalysisCache::create(['url' => $urlToAnalyze, 'type' => $cacheType, 'results' => $result]);
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error("AI Analysis Exception for type: {$cacheType}", ['message' => $e->getMessage()]);
            return response()->json(['message' => "An unexpected error occurred during the {$cacheType} analysis.", 'detail' => $e->getMessage()], 500);
        }
    }

    /**
     * Handles only the local HTML parsing.
     */
    public function analyze(Request $request): JsonResponse
    {
        // This function is now much simpler and only does local parsing.
        // The limit check is now handled by the specific AI analysis endpoints.
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

    // ✅ UPDATED: All AI Endpoints now perform the limit check.
    public function analyzeContentOptimization(Request $request) {
        $validated = $request->validate(['url' => 'required|url']);
        $url = $validated['url'];
        if (($limitCheck = $this->checkAndLogSearch($url, 'content_optimization', $request)) !== true) return $limitCheck;
        $prompt = "Analyze content at '{$url}' for SEO. Return JSON with 'content_optimization' containing: 'nlp_score' (0-100), 'topic_coverage' {'percentage', 'total', 'covered'}, 'content_gaps' {'missing_topics':[{'term', 'severity'}]}, 'schema_suggestions' (array of strings), and 'readability_intent' {'intent', 'grade_level'}.";
        return $this->performAiAnalysis($url, $prompt, 'content_optimization', ['content_optimization']);
    }

    public function analyzeTechnicalSeo(Request $request) {
        $validated = $request->validate(['url' => 'required|url']);
        $url = $validated['url'];
        if (($limitCheck = $this->checkAndLogSearch($url, 'technical_seo', $request)) !== true) return $limitCheck;
        $prompt = "Analyze technical SEO of {$url}. Return JSON with: 'score' (0-100), 'internal_linking':[{'text','anchor'}], 'url_structure':{'clarity_score','suggestion'}, 'meta_optimization':{'title','description'}, 'alt_text_suggestions':[{'image_src','suggestion'}], 'site_structure_map' (HTML ul string), and 'suggestions':[{'text','type'}].";
        return $this->performAiAnalysis($url, $prompt, 'technical_seo', ['internal_linking', 'alt_text_suggestions', 'suggestions']);
    }

    public function analyzeKeywords(Request $request) {
        $validated = $request->validate(['url' => 'required|url']);
        $url = $validated['url'];
        if (($limitCheck = $this->checkAndLogSearch($url, 'keyword_intelligence', $request)) !== true) return $limitCheck;
        $prompt = "Perform a keyword intelligence analysis for {$url}. Return JSON with: 'semantic_research' (5-7 variations), 'intent_classification' ({'keyword','intent'}), 'related_terms' (5-7 terms), 'competitor_gaps' (3-5 opportunities), and 'long_tail_suggestions' (3-5 recommendations).";
        return $this->performAiAnalysis($url, $prompt, 'keyword_intelligence', ['semantic_research', 'intent_classification', 'related_terms', 'competitor_gaps', 'long_tail_suggestions']);
    }

    public function analyzeContentEngine(Request $request) {
        $validated = $request->validate(['url' => 'required|url']);
        $url = $validated['url'];
        if (($limitCheck = $this->checkAndLogSearch($url, 'content_engine', $request)) !== true) return $limitCheck;
        $prompt = "As a Content Analysis Engine, analyze {$url}. Return JSON with 'score' (0-100), 'topic_clusters', 'entities':[{'term', 'type'}], 'semantic_keywords', 'relevance_score' (0-100), and 'context_intent'.";
        return $this->performAiAnalysis($url, $prompt, 'content_engine', ['topic_clusters', 'entities', 'semantic_keywords']);
    }

    public function psiProxy(Request $request) { /* ... unchanged ... */ }
    public function analyzeWeb(Request $request) { return $this->analyze($request); }
    public function semanticAnalyze(Request $request) { return $this->analyze($request); }
    public function psi(Request $request) { return response()->json(['ok' => true, 'note' => 'PSI proxy not implemented here.']); }
    public function aiCheck(Request $request) { return response()->json(['ok' => true, 'note' => 'aiCheck stub']); }
    public function topicClusterAnalyze(Request $request) { return response()->json(['ok' => true, 'note' => 'topicClusterAnalyze stub']); }
}
