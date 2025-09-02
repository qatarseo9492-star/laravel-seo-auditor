<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use DOMDocument;
use DOMXPath;

class AnalyzerController extends Controller
{
    /**
     * Handles Content Optimization and local on-page parsing.
     */
    public function analyze(Request $request)
    {
        $validated = $request->validate([
            'url' => ['required', 'url'],
            'language' => ['nullable', 'string', 'max:10'],
        ]);

        $urlToAnalyze = $validated['url'];
        $language = $validated['language'] ?? 'en';
        $contentStructure = [];
        $pageSignals = [];
        $quickStats = [];

        // --- 1. Local HTML Fetching & Parsing ---
        try {
            $response = Http::timeout(15)->get($urlToAnalyze);
            if ($response->failed()) {
                return response()->json(['ok' => false, 'error' => 'Failed to fetch the provided URL.'], 400);
            }
            $html = $response->body();

            $dom = new DOMDocument();
            @$dom->loadHTML($html); // Suppress warnings from malformed HTML
            $xpath = new DOMXPath($dom);

            // Extract Title, Meta Description, Headings
            $contentStructure['title'] = optional($xpath->query('//title')->item(0))->textContent;
            $contentStructure['meta_description'] = optional($xpath->query("//meta[@name='description']/@content")->item(0))->nodeValue;
            $contentStructure['headings'] = [];
            foreach (['h1', 'h2', 'h3', 'h4'] as $tag) {
                $headings = $xpath->query('//' . $tag);
                $tagUpper = strtoupper($tag);
                $contentStructure['headings'][$tagUpper] = [];
                foreach ($headings as $heading) {
                    $contentStructure['headings'][$tagUpper][] = trim($heading->textContent);
                }
            }

            // Extract other page signals
            $pageSignals['canonical'] = optional($xpath->query("//link[@rel='canonical']/@href")->item(0))->nodeValue;
            $pageSignals['robots'] = optional($xpath->query("//meta[@name='robots']/@content")->item(0))->nodeValue;
            $pageSignals['has_viewport'] = $xpath->query("//meta[@name='viewport']")->length > 0;

            // Extract link counts
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
            $quickStats['schema_types'] = []; // Placeholder

        } catch (\Exception $e) {
            Log::error('Local HTML Parsing Failed', ['message' => $e->getMessage()]);
        }

        // --- 2. OpenAI API Call for Content Optimization ---
        $apiKey = env('OPENAI_API_KEY');
        if (empty($apiKey)) {
            return response()->json(['ok' => false, 'error' => 'Server is not configured for AI analysis.'], 500);
        }

        try {
            $prompt = "Analyze the content at '{$urlToAnalyze}' for SEO in {$language}. Return JSON with a root key 'content_optimization' containing: 'nlp_score' (0-100), 'topic_coverage' {'percentage', 'total', 'covered'}, 'content_gaps' {'missing_topics':[{'term', 'severity'}]}, 'schema_suggestions' (array of strings), and 'readability_intent' {'intent', 'grade_level'}.";
            $aiResponse = Http::withToken($apiKey)->timeout(60)->post('https://api.openai.com/v1/chat/completions', [
                'model' => env('OPENAI_MODEL', 'gpt-4-turbo'),
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an SEO expert. Return only the requested JSON.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'response_format' => ['type' => 'json_object']
            ]);

            if ($aiResponse->failed()) {
                return response()->json(['ok' => false, 'error' => 'Could not get AI analysis.'], 502);
            }

            $analysisData = json_decode($aiResponse->json('choices.0.message.content'), true);

            // --- 3. Combine and Return Final Response ---
            $co = $analysisData['content_optimization'] ?? [];
            return response()->json([
                "ok" => true,
                "overall_score" => $co['nlp_score'] ?? 75,
                "content_optimization" => $co,
                "content_structure" => $contentStructure,
                "page_signals" => $pageSignals,
                "quick_stats" => $quickStats,
                "score_source" => 'ai',
            ]);

        } catch (\Exception $e) {
            Log::error('Content Analysis Exception', ['message' => $e->getMessage()]);
            return response()->json(['ok' => false, 'error' => 'Server error during content analysis.'], 500);
        }
    }

    /**
     * Handles the Technical SEO analysis using OpenAI.
     */
    public function analyzeTechnicalSeo(Request $request)
    {
        $request->validate(['url' => 'required|url']);
        $urlToAnalyze = $request->input('url');
        $apiKey = env('OPENAI_API_KEY');

        if (!$apiKey) {
            return response()->json(['message' => 'OpenAI API key is not configured.'], 500);
        }

        try {
            $prompt = "Analyze technical SEO of {$urlToAnalyze}. Return JSON with: 'score' (0-100), 'internal_linking':[{'text','anchor'}], 'url_structure':{'clarity_score','suggestion'}, 'meta_optimization':{'title','description'}, 'alt_text_suggestions':[{'image_src','suggestion'}], 'site_structure_map' (HTML ul string), and 'suggestions':[{'text','type'}].";
            $response = Http::withToken($apiKey)->timeout(60)->post('https://api.openai.com/v1/chat/completions', [
                'model' => env('OPENAI_MODEL', 'gpt-4-turbo'),
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a Technical SEO expert. Respond only with JSON.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'response_format' => ['type' => 'json_object']
            ]);

            if ($response->failed()) {
                return response()->json(['message' => 'Failed to get a response from OpenAI.'], 502);
            }

            $decodedResult = json_decode($response->json('choices.0.message.content'), true);
            
            // Sanitize arrays to prevent frontend errors
            $decodedResult['internal_linking'] = $decodedResult['internal_linking'] ?? [];
            $decodedResult['alt_text_suggestions'] = $decodedResult['alt_text_suggestions'] ?? [];
            $decodedResult['suggestions'] = $decodedResult['suggestions'] ?? [];

            return response()->json($decodedResult);

        } catch (\Exception $e) {
            Log::error('Technical SEO Analysis Failed: ' . $e->getMessage());
            return response()->json(['message' => 'An unexpected error occurred during analysis.'], 500);
        }
    }

    /**
     * NEW: Handles Keyword Intelligence analysis using OpenAI.
     */
    public function analyzeKeywords(Request $request)
    {
        $request->validate(['url' => 'required|url']);
        $urlToAnalyze = $request->input('url');
        $apiKey = env('OPENAI_API_KEY');

        if (!$apiKey) {
            return response()->json(['message' => 'OpenAI API key is not configured.'], 500);
        }

        try {
            $prompt = "Perform a keyword intelligence analysis for {$urlToAnalyze}. Return JSON with: 'semantic_research' (array of 5-7 variations), 'intent_classification' (array of {'keyword','intent'}), 'related_terms' (array of 5-7 terms), 'competitor_gaps' (array of 3-5 opportunities), and 'long_tail_suggestions' (array of 3-5 recommendations).";
            $response = Http::withToken($apiKey)->timeout(60)->post('https://api.openai.com/v1/chat/completions', [
                'model' => env('OPENAI_MODEL', 'gpt-4-turbo'),
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a Keyword Research specialist. Respond only with JSON.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'response_format' => ['type' => 'json_object']
            ]);

            if ($response->failed()) {
                return response()->json(['message' => 'Failed to get keyword analysis from OpenAI.'], 502);
            }

            $result = json_decode($response->json('choices.0.message.content'), true);

            // Sanitize arrays to ensure they exist
            $keys = ['semantic_research', 'intent_classification', 'related_terms', 'competitor_gaps', 'long_tail_suggestions'];
            foreach ($keys as $key) {
                $result[$key] = $result[$key] ?? [];
            }

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Keyword Analysis Failed: ' . $e->getMessage());
            return response()->json(['message' => 'An unexpected error occurred during keyword analysis.'], 500);
        }
    }

    /** CSRF-protected proxy for the Blade */
    public function analyzeWeb(Request $request) { return $this->analyze($request); }

    /** Legacy alias for older routes */
    public function semanticAnalyze(Request $request) { return $this->analyze($request); }

    /** PSI placeholder */
    public function psi(Request $request) { return response()->json(['ok' => true, 'note' => 'PSI proxy not implemented here.']); }

    public function aiCheck(Request $request) { return response()->json(['ok' => true, 'note' => 'aiCheck stub']); }
    public function topicClusterAnalyze(Request $request) { return response()->json(['ok' => true, 'note' => 'topicClusterAnalyze stub']); }
}

