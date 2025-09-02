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
     * ✅ FIXED: This method now performs both local HTML parsing and the OpenAI API call.
     * This ensures that the "Meta & Heading Structure" and other on-page details are
     * fetched correctly, along with the AI-driven "Content Optimization" analysis.
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

            // Use DOMDocument to parse the HTML
            $dom = new DOMDocument();
            // Suppress warnings from malformed HTML
            libxml_use_internal_errors(true);
            $dom->loadHTML($html);
            libxml_clear_errors();
            $xpath = new DOMXPath($dom);

            // Extract Title
            $titleNode = $xpath->query('//title')->item(0);
            $contentStructure['title'] = $titleNode ? trim($titleNode->textContent) : 'Not found';

            // Extract Meta Description
            $metaDescNode = $xpath->query("//meta[@name='description']/@content")->item(0);
            $contentStructure['meta_description'] = $metaDescNode ? trim($metaDescNode->nodeValue) : 'Not found';

            // Extract Headings (H1-H4)
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
            $parsedUrl = parse_url($urlToAnalyze);
            $host = $parsedUrl['host'] ?? '';

            foreach ($links as $link) {
                $href = $link->getAttribute('href');
                if (Str::startsWith($href, '/') || (Str::contains($href, $host) && Str::startsWith($href, 'http'))) {
                    $internalLinks++;
                }
            }
            $quickStats['internal_links'] = $internalLinks;
            $quickStats['schema_types'] = []; // Placeholder, requires more complex parsing


        } catch (\Exception $e) {
            Log::error('Local HTML Parsing Failed', ['message' => $e->getMessage()]);
            // Don't fail the whole request, AI part can still work
        }


        // --- 2. OpenAI API Call for Content Optimization ---
        $apiKey = env('OPENAI_API_KEY');
        if (empty($apiKey)) {
            Log::error('OPENAI_API_KEY missing in config/env');
            return response()->json(['ok' => false, 'error' => 'Server is not configured for AI analysis.'], 500);
        }

        $model = env('OPENAI_MODEL', 'gpt-4-turbo');
        $timeout = (int)env('OPENAI_TIMEOUT', 60);

        try {
            $prompt = "Analyze the content at the URL '{$urlToAnalyze}' for SEO. The primary language of the content is '{$language}'. Return a JSON object with a single root key 'content_optimization'. This key should contain: 'nlp_score' (integer 0-100), 'topic_coverage' (object with 'percentage', 'total', 'covered' integers), 'content_gaps' (object with 'missing_topics' array of objects, each with 'term' and 'severity' strings), 'schema_suggestions' (an array of strings), and 'readability_intent' (object with 'intent' string and 'grade_level' string).";

            $aiResponse = Http::withToken($apiKey)
                ->timeout($timeout)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are an SEO content optimization expert. Return only the requested JSON object.'],
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'response_format' => ['type' => 'json_object']
                ]);

            if ($aiResponse->failed()) {
                Log::error('OpenAI Content Analysis Failed', ['status' => $aiResponse->status(), 'body' => $aiResponse->body()]);
                return response()->json(['ok' => false, 'error' => 'Could not get analysis from AI service.'], 502);
            }

            $rawContent = $aiResponse->json('choices.0.message.content');
            $analysisData = json_decode($rawContent, true);

            if (json_last_error() !== JSON_ERROR_NONE || !isset($analysisData['content_optimization'])) {
                 return response()->json(['ok' => false, 'error' => 'AI service returned an invalid format.'], 500);
            }

            // --- 3. Combine and Return Final Response ---
            $co = $analysisData['content_optimization'];
            $finalResponse = [
                "ok" => true,
                "overall_score" => $co['nlp_score'] ?? 75,
                "content_optimization" => $co,
                "content_structure" => $contentStructure, // ✅ ADDED BACK
                "page_signals" => $pageSignals,           // ✅ ADDED BACK
                "quick_stats" => $quickStats,             // ✅ ADDED BACK
                "score_source" => 'ai',
            ];

            return response()->json($finalResponse);

        } catch (\Exception $e) {
            Log::error('Content Analysis Exception', ['message' => $e->getMessage()]);
            return response()->json(['ok' => false, 'error' => 'An unexpected server error occurred during content analysis.'], 500);
        }
    }


    /**
     * Handles the Technical SEO analysis using OpenAI.
     */
    public function analyzeTechnicalSeo(Request $request)
    {
        $request->validate([
            'url' => 'required|url'
        ]);

        $urlToAnalyze = $request->input('url');
        $apiKey = env('OPENAI_API_KEY');

        if (!$apiKey) {
            return response()->json(['message' => 'OpenAI API key is not configured.'], 500);
        }

        try {
            $prompt = "Analyze the technical SEO of the page at {$urlToAnalyze}. Provide a detailed analysis in JSON format. The JSON object must include: a 'score' (0-100), 'internal_linking' suggestions as an array of objects each with 'text' and 'anchor' keys, 'url_structure' analysis as an object with 'clarity_score' and 'suggestion', 'meta_optimization' as an object with 'title' and 'description', 'alt_text_suggestions' as an array of objects with 'image_src' and 'suggestion', a 'site_structure_map' as a simple HTML ul list string, and a final list of 'suggestions' as an array of objects each with 'text' and 'type' keys.";

            $response = Http::withToken($apiKey)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a world-class Technical SEO expert.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'response_format' => ['type' => 'json_object']
            ]);

            if ($response->failed()) {
                 return response()->json(['message' => 'Failed to get a response from OpenAI.', 'details' => $response->body()], 502);
            }

            $analysisResult = $response->json('choices.0.message.content');
            $decodedResult = json_decode($analysisResult, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['message' => 'OpenAI returned invalid JSON.', 'raw_response' => $analysisResult], 500);
            }

            if (!isset($decodedResult['internal_linking']) || !is_array($decodedResult['internal_linking'])) {
                $decodedResult['internal_linking'] = [];
            }
            if (!isset($decodedResult['alt_text_suggestions']) || !is_array($decodedResult['alt_text_suggestions'])) {
                $decodedResult['alt_text_suggestions'] = [];
            }
            if (!isset($decodedResult['suggestions']) || !is_array($decodedResult['suggestions'])) {
                $decodedResult['suggestions'] = [];
            }

            return response()->json($decodedResult);

        } catch (\Exception $e) {
            Log::error('Technical SEO Analysis Failed: ' . $e->getMessage());
            return response()->json(['message' => 'An unexpected error occurred during the analysis.'], 500);
        }
    }

    /** CSRF-protected proxy for the Blade */
    public function analyzeWeb(Request $request) { return $this->analyze($request); }

    /** Legacy alias for older routes */
    public function semanticAnalyze(Request $request) { return $this->analyze($request); }

    /** PSI placeholder */
    public function psi(Request $request) { return response()->json(['ok'=>true,'note'=>'PSI proxy not implemented here.']); }

    public function aiCheck(Request $request) { return response()->json(['ok'=>true,'note'=>'aiCheck stub']); }
    public function topicClusterAnalyze(Request $request) { return response()->json(['ok'=>true,'note'=>'topicClusterAnalyze stub']); }
}

