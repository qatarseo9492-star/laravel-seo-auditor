<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use DOMDocument;
use DOMXPath;
use App\Support\Logs\UsageLogger; // 🔹 add

class AnalyzerController extends Controller
{
    use UsageLogger; // 🔹 add

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
            @$dom->loadHTML($html);
            $xpath = new DOMXPath($dom);

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

        } catch (\Exception $e) {
            Log::error('Local HTML Parsing Failed', ['message' => $e->getMessage()]);
        }

        // --- 2. OpenAI API Call for Content Optimization ---
        $apiKey = env('OPENAI_API_KEY');
        if (empty($apiKey)) {
            return response()->json(['ok' => false, 'error' => 'Server not configured for AI analysis.'], 500);
        }

        try {
            $model = env('OPENAI_MODEL', 'gpt-4-turbo'); // keep your default
            $prompt = "Analyze content at '{$urlToAnalyze}' for SEO in {$language}. Return JSON with 'content_optimization' containing: 'nlp_score' (0-100), 'topic_coverage' {'percentage', 'total', 'covered'}, 'content_gaps' {'missing_topics':[{'term', 'severity'}]}, 'schema_suggestions' (array of strings), and 'readability_intent' {'intent', 'grade_level'}.";
            $aiResponse = Http::withToken($apiKey)->timeout(60)->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'messages' => [['role' => 'user', 'content' => $prompt]],
                'response_format' => ['type' => 'json_object']
            ]);

            $analysisData = json_decode($aiResponse->json('choices.0.message.content'), true);
            $co = $analysisData['content_optimization'] ?? [];

            // 🔹 Safe logging (does not change your response)
            try {
                $usage = $aiResponse->json('usage') ?? [];
                $pt = $usage['prompt_tokens'] ?? null;
                $ct = $usage['completion_tokens'] ?? null;
                $tt = $usage['total_tokens'] ?? (($pt && $ct) ? ($pt + $ct) : null);

                // OpenAI usage row
                $this->logOpenAiUsage($request, [
                    'model'             => $model,
                    'prompt_tokens'     => $pt,
                    'completion_tokens' => $ct,
                    'total_tokens'      => $tt,
                    'cost_usd'          => null,             // optional: map to pricing if you want
                    'meta'              => ['endpoint' => 'semantic.analyzeWeb'],
                ]);

                // Analyze action row
                $this->logAnalyze($request, [
                    'analyzer'    => $request->route()?->getName() ?? 'semantic', // e.g., semantic.analyze
                    'url'         => $urlToAnalyze,
                    'tokens_used' => $tt,
                    'success'     => true,
                ]);
            } catch (\Throwable $t) {
                Log::warning('Analyze logging failed', ['err' => $t->getMessage()]);
            }

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

            // 🔹 Log failure to analyze_logs only (no OpenAI usage if request failed)
            try {
                $this->logAnalyze($request, [
                    'analyzer'    => $request->route()?->getName() ?? 'semantic',
                    'url'         => $urlToAnalyze,
                    'tokens_used' => null,
                    'success'     => false,
                ]);
            } catch (\Throwable $t) {
                Log::warning('Analyze logging (error path) failed', ['err' => $t->getMessage()]);
            }

            return response()->json(['ok' => false, 'error' => 'Server error during analysis.'], 500);
        }
    }

    /**
     * Handles Technical SEO analysis.
     */
    public function analyzeTechnicalSeo(Request $request)
    {
        return $this->proxyOpenAiRequest($request,
            "Analyze technical SEO of {{URL}}. Return JSON with: 'score' (0-100), 'internal_linking':[{'text','anchor'}], 'url_structure':{'clarity_score','suggestion'}, 'meta_optimization':{'title','description'}, 'alt_text_suggestions':[{'image_src','suggestion'}], 'site_structure_map' (HTML ul string), and 'suggestions':[{'text','type'}].",
            ['internal_linking', 'alt_text_suggestions', 'suggestions']
        );
    }

    /**
     * Handles Keyword Intelligence analysis.
     */
    public function analyzeKeywords(Request $request)
    {
        return $this->proxyOpenAiRequest($request,
            "Perform keyword intelligence for {{URL}}. Return JSON with: 'semantic_research' (5-7 variations), 'intent_classification' ({'keyword','intent'}), 'related_terms' (5-7 terms), 'competitor_gaps' (3-5 opportunities), and 'long_tail_suggestions' (3-5 recommendations).",
            ['semantic_research', 'intent_classification', 'related_terms', 'competitor_gaps', 'long_tail_suggestions']
        );
    }

    /**
     * NEW: Handles Content Analysis Engine.
     */
    public function analyzeContentEngine(Request $request)
    {
        return $this->proxyOpenAiRequest($request,
            "Analyze the content at {{URL}}. Return JSON with: 'score' (0-100), 'topic_clusters' (array of strings), 'entities' (array of {'term', 'type'}), 'semantic_keywords' (array of LSI terms), 'relevance_score' (0-100), and 'context_intent' (string).",
            ['topic_clusters', 'entities', 'semantic_keywords']
        );
    }

    /**
     * Reusable helper for OpenAI API calls.
     * NOTE: Logic unchanged; only logs are added safely after a successful response.
     */
    private function proxyOpenAiRequest(Request $request, string $promptTemplate, array $arrayKeysToSanitize = [])
    {
        $request->validate(['url' => 'required|url']);
        $urlToAnalyze = $request->input('url');
        $apiKey = env('OPENAI_API_KEY');

        if (!$apiKey) {
            return response()->json(['message' => 'OpenAI API key is not configured.'], 500);
        }

        try {
            $model = env('OPENAI_MODEL', 'gpt-4-turbo');
            $prompt = str_replace('{{URL}}', $urlToAnalyze, $promptTemplate);
            $response = Http::withToken($apiKey)->timeout(60)->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'messages' => [['role' => 'user', 'content' => $prompt]],
                'response_format' => ['type' => 'json_object']
            ]);

            if ($response->failed()) {
                // 🔹 Log failed attempt
                try {
                    $this->logAnalyze($request, [
                        'analyzer'    => $request->route()?->getName() ?? 'semantic',
                        'url'         => $urlToAnalyze,
                        'tokens_used' => null,
                        'success'     => false,
                    ]);
                } catch (\Throwable $t) {
                    Log::warning('Proxy logging (failure) failed', ['err' => $t->getMessage()]);
                }

                return response()->json(['message' => 'Failed to get a response from OpenAI.'], 502);
            }

            $result = json_decode($response->json('choices.0.message.content'), true);

            foreach ($arrayKeysToSanitize as $key) {
                if (!isset($result[$key]) || !is_array($result[$key])) {
                   $result[$key] = [];
                }
            }

            // 🔹 Safe logging (OpenAI usage + analyze action)
            try {
                $usage = $response->json('usage') ?? [];
                $pt = $usage['prompt_tokens'] ?? null;
                $ct = $usage['completion_tokens'] ?? null;
                $tt = $usage['total_tokens'] ?? (($pt && $ct) ? ($pt + $ct) : null);

                $this->logOpenAiUsage($request, [
                    'model'             => $model,
                    'prompt_tokens'     => $pt,
                    'completion_tokens' => $ct,
                    'total_tokens'      => $tt,
                    'cost_usd'          => null,
                    'meta'              => ['endpoint' => ($request->route()?->getName() ?? 'semantic.proxy')],
                ]);

                $this->logAnalyze($request, [
                    'analyzer'    => $request->route()?->getName() ?? 'semantic',
                    'url'         => $urlToAnalyze,
                    'tokens_used' => $tt,
                    'success'     => true,
                ]);
            } catch (\Throwable $t) {
                Log::warning('Proxy logging failed', ['err' => $t->getMessage()]);
            }

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('OpenAI Proxy Failed: ' . $e->getMessage());

            // 🔹 Log exception path
            try {
                $this->logAnalyze($request, [
                    'analyzer'    => $request->route()?->getName() ?? 'semantic',
                    'url'         => $urlToAnalyze,
                    'tokens_used' => null,
                    'success'     => false,
                ]);
            } catch (\Throwable $t) {
                Log::warning('Proxy logging (exception) failed', ['err' => $t->getMessage()]);
            }

            return response()->json(['message' => 'An unexpected error occurred.'], 500);
        }
    }

    /** Legacy and proxy methods */
    public function analyzeWeb(Request $request) { return $this->analyze($request); }
    public function semanticAnalyze(Request $request) { return $this->analyze($request); }
    public function psi(Request $request) { return response()->json(['ok' => true, 'note' => 'PSI proxy not implemented here.']); }
    public function aiCheck(Request $request) { return response()->json(['ok' => true, 'note' => 'aiCheck stub']); }
    public function topicClusterAnalyze(Request $request) { return response()->json(['ok' => true, 'note' => 'topicClusterAnalyze stub']); }
}
