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
     * Handles only the local HTML parsing.
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
     * ✅ UPDATED: A single, robust handler for all OpenAI requests with improved response validation.
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
                'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
                'messages' => [['role' => 'system', 'content' => 'You are a world-class SEO expert. Respond only with the requested JSON object.'], ['role' => 'user', 'content' => $prompt]],
                'response_format' => ['type' => 'json_object']
            ]);

            if ($response->failed()) {
                Log::error("AI API Call Failed for {$toolName}", ['status' => $response->status(), 'body' => $response->body()]);
                return response()->json(['message' => "The AI service returned an error for the {$toolName} analysis.", 'detail' => $response->body()], 502);
            }

            $rawContent = $response->json('choices.0.message.content');
            Log::info("Raw AI Response for {$toolName}", ['content' => $rawContent]); // Log the raw response for debugging

            if (empty($rawContent)) {
                return response()->json(['message' => "The AI service returned an empty response for the {$toolName} analysis."], 500);
            }

            $result = json_decode($rawContent, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['message' => "The AI service returned invalid JSON for the {$toolName} analysis."], 500);
            }

            // ✅ THE FIX: Stricter validation of the AI's response structure.
            foreach ($expectedKeys as $key) {
                if (!isset($result[$key])) {
                    Log::error("Missing expected key '{$key}' from AI for {$toolName}", ['result' => $result]);
                    return response()->json(['message' => "The AI response was missing the expected '{$key}' data structure."], 500);
                }
            }
            
            if ($toolName === 'content_optimization' && !isset($result['content_optimization']['nlp_score'])) {
                 Log::error("Missing 'nlp_score' key from AI for {$toolName}", ['result' => $result]);
                 return response()->json(['message' => "The AI response was missing the critical 'nlp_score' data."], 500);
            }

            AnalysisCache::create(['url' => $url, 'type' => $toolName, 'results' => $result]);
            
            return response()->json($result);

        } catch (\Exception $e) {
            Log::error("AI Analysis Exception for {$toolName}", ['message' => $e->getMessage()]);
            return response()->json(['message' => "A server error occurred during the {$toolName} analysis.", 'detail' => $e->getMessage()], 500);
        }
    }

    // AI endpoints
    public function analyzeContentOptimization(Request $request) {
        $prompt = "Analyze content at '{{URL}}' for SEO. Return JSON with a root key 'content_optimization' containing: 'nlp_score' (integer 0-100), 'topic_coverage' (object with 'percentage', 'total', 'covered' integers), 'content_gaps' (object with 'missing_topics' array of objects), 'schema_suggestions' (array of strings), and 'readability_intent' (object with 'intent' and 'grade_level' strings).";
        return $this->handleAiRequest($request, 'content_optimization', $prompt, ['content_optimization']);
    }

    public function analyzeTechnicalSeo(Request $request) {
        $prompt = "Analyze technical SEO of {{URL}}. Return JSON with: 'score' (integer 0-100), 'internal_linking' (array of objects), 'url_structure' (object), 'meta_optimization' (object), 'alt_text_suggestions' (array), 'site_structure_map' (string), and 'suggestions' (array).";
        return $this->handleAiRequest($request, 'technical_seo', $prompt, ['score', 'internal_linking']);
    }

    public function analyzeKeywords(Request $request) {
        $prompt = "Perform keyword intelligence for {{URL}}. Return JSON with: 'semantic_research' (array), 'intent_classification' (array), 'related_terms' (array), 'competitor_gaps' (array), and 'long_tail_suggestions' (array).";
        return $this->handleAiRequest($request, 'keyword_intelligence', $prompt, ['semantic_research', 'intent_classification']);
    }

    public function analyzeContentEngine(Request $request) {
        $prompt = "As a Content Analysis Engine, analyze {{URL}}. Return JSON with 'score' (integer 0-100), 'topic_clusters' (array), 'entities' (array), 'semantic_keywords' (array), 'relevance_score' (integer 0-100), and 'context_intent' (string).";
        return $this->handleAiRequest($request, 'content_engine', $prompt, ['score', 'topic_clusters']);
    }

    public function psiProxy(Request $request): JsonResponse { /* ... unchanged ... */ }
}
