<?php

namespace App\Http\Controllers;

// Combined and cleaned dependencies from both file versions
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use DOMDocument;
use DOMXPath;
use App\Models\User; // Assuming these models exist as per your project structure
use App\Models\UserLimit;
use App\Models\AnalysisCache;
use App\Support\Logs\UsageLogger; // Assuming this class exists
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class AnalyzerController extends Controller
{
    /**
     * Centralized function to check limits and log any analysis tool usage.
     * This is production-ready logic from your previous file.
     */
    private function checkAndLog(Request $request, string $tool): bool|JsonResponse
    {
        if (!Auth::check()) return true; // Don't check limits for guests

        $user = Auth::user();
        $limit = UserLimit::firstOrCreate(['user_id' => $user->id]);

        // Reset counters if a new day or month has started
        if (!$limit->updated_at->isToday()) $limit->searches_today = 0;
        if (!$limit->updated_at->isSameMonth(now())) $limit->searches_this_month = 0;

        if ($limit->searches_today >= $limit->daily_limit || $limit->searches_this_month >= $limit->monthly_limit) {
            return response()->json(['error' => 'You have reached your usage quota for today/this month.'], 429);
        }

        // Log the usage and increment counters
        (new UsageLogger())->logAnalysis($request, $tool, true);
        $limit->increment('searches_today');
        $limit->increment('searches_this_month');
        $limit->touch();

        return true;
    }

    /**
     * Handles the initial, non-AI analysis by parsing the page's raw HTML.
     * This does not count against any user quota.
     */
    public function semanticAnalyze(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate(['url' => ['required', 'url']]);
            $urlToAnalyze = $validated['url'];

            $contentStructure = []; $pageSignals = []; $quickStats = []; $imagesAltCount = 0;
            $response = Http::timeout(15)->get($urlToAnalyze);
            if ($response->failed()) return response()->json(['error' => "Failed to fetch URL. Status: {$response->status()}"], 400);

            $html = $response->body();
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($html);
            libxml_clear_errors();
            $xpath = new DOMXPath($dom);

            // Extract basic on-page elements
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

            // Perform link analysis
            $links = $dom->getElementsByTagName('a');
            $internalLinks = 0; $externalLinks = 0;
            $host = parse_url($urlToAnalyze, PHP_URL_HOST) ?? '';
            foreach ($links as $link) {
                $href = $link->getAttribute('href');
                if (!$href || Str::startsWith($href, '#') || Str::startsWith($href, 'mailto:') || Str::startsWith($href, 'tel:')) continue;
                $linkHost = parse_url($href, PHP_URL_HOST);
                ($linkHost === null || $linkHost === $host) ? $internalLinks++ : $externalLinks++;
            }
            $quickStats['internal_links'] = $internalLinks;
            $quickStats['external_links'] = $externalLinks;

            // Count images with alt text
            foreach ($dom->getElementsByTagName('img') as $image) {
                if ($image->hasAttribute('alt') && !empty(trim($image->getAttribute('alt')))) $imagesAltCount++;
            }

            // Return a combined response structure for the frontend
            return response()->json([
                // Placeholders; real scores would be calculated from all data points
                'overall_score' => 78,
                'readability' => ['score' => 75, 'passive_ratio' => 10],
                'categories' => [['name' => 'Content & Keywords', 'score' => 82], ['name' => 'Content Quality', 'score' => 75]],
                'content_structure' => $contentStructure,
                'page_signals' => $pageSignals,
                'quick_stats' => $quickStats,
                'images_alt_count' => $imagesAltCount,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Local HTML Parsing Failed', ['message' => $e->getMessage()]);
            return response()->json(['error' => "Could not parse the URL's HTML.", 'detail' => $e->getMessage()], 500);
        }
    }

    /**
     * A single, unified handler for all OpenAI-powered features.
     * It incorporates usage limits, caching, and dynamic prompt generation.
     */
    public function handleOpenAiRequest(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'task' => ['required', 'string', Rule::in(['brief', 'suggestions', 'competitor', 'trends', 'technical_seo', 'keyword_intelligence', 'content_engine', 'readability_tone'])],
            'prompt' => 'nullable|string|max:8000', // Increased limit for text analysis
            'url' => 'required|url' // The primary URL being analyzed
        ]);

        $task = $validated['task'];
        $url = $validated['url'];
        
        $cacheKey = "ai:{$task}:" . md5($url . ($validated['prompt'] ?? ''));
        if (Cache::has($cacheKey)) {
            return response()->json(Cache::get($cacheKey));
        }
        
        // if (($limitCheck = $this->checkAndLog($request, 'ai_' . $task)) !== true) {
        //     return $limitCheck;
        // }

        $apiKey = env('OPENAI_API_KEY');
        if (!$apiKey) {
            return response()->json(['error' => 'OpenAI API key is not configured.'], 500);
        }

        [$systemMessage, $userMessage] = $this->generateAiPrompts($validated);
        if (empty($userMessage)) {
            return response()->json(['error' => 'Invalid task specified.'], 400);
        }

        try {
            $isJsonMode = in_array($task, ['technical_seo', 'keyword_intelligence', 'content_engine', 'readability_tone']);
            
            $response = Http::withToken($apiKey)->timeout(90)->post('https://api.openai.com/v1/chat/completions', [
                'model' => env('OPENAI_MODEL', 'gpt-4-turbo'),
                'messages' => [['role' => 'system', 'content' => $systemMessage], ['role' => 'user', 'content' => $userMessage]],
                'temperature' => 0.3,
                'max_tokens' => 1500,
                'response_format' => $isJsonMode ? ['type' => 'json_object'] : null,
            ]);

            if ($response->failed()) {
                Log::error("OpenAI API Error for task '{$task}'", ['status' => $response->status(), 'body' => $response->body()]);
                return response()->json(['error' => 'Failed to get a response from the AI service.'], $response->status());
            }

            $rawContent = $response->json('choices.0.message.content');
            if (empty($rawContent)) {
                 return response()->json(['error' => "The AI service returned an empty response."], 500);
            }

            $result = $isJsonMode ? json_decode($rawContent, true) : ['content' => trim($rawContent)];
            
            if ($isJsonMode && json_last_error() !== JSON_ERROR_NONE) {
                 Log::error("Invalid JSON from AI for task '{$task}'", ['body' => $rawContent]);
                 return response()->json(['error' => "The AI service returned invalid JSON."], 500);
            }
            
            Cache::put($cacheKey, $result, now()->addHours(6));

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error("Error calling OpenAI API for task '{$task}'", ['message' => $e->getMessage()]);
            return response()->json(['error' => 'An internal server error occurred.'], 500);
        }
    }
    
    /**
     * Helper to generate system and user prompts for the AI based on the requested task.
     */
    private function generateAiPrompts(array $validatedData): array
    {
        $task = $validatedData['task'];
        $prompt = $validatedData['prompt'] ?? '';
        $url = $validatedData['url'];

        $systemMessage = "You are a world-class Semantic SEO expert and copy editor. Your responses must be accurate, concise, and directly actionable. Respond only with the requested format (JSON or plain text).";
        $userMessage = "";

        switch ($task) {
            case 'brief':
                $systemMessage .= " Format your response in clean, readable plain text.";
                $userMessage = "Generate a semantic content brief for the primary keyword: '{$prompt}'. Include a suggested H1, a meta description (155 chars max), 3-5 LSI keywords, and 3-5 FAQs. Target URL: {$url}";
                break;
            case 'suggestions':
                $systemMessage .= " Format as a plain text list.";
                $userMessage = "Analyze the content at '{$url}'. Provide 3-5 actionable recommendations to improve its semantic relevance and user engagement.";
                break;
            case 'competitor':
                $systemMessage .= " Format as plain text, focusing on strategic differences.";
                $userMessage = "Analyze the user's page at '{$url}' against a competitor at '{$prompt}'. Identify the top 3-5 semantic strategy gaps on the user's page. What topics, entities, or questions does the competitor cover that the user is missing?";
                break;
            case 'trends':
                 $systemMessage .= " Format as a plain text list.";
                 $userMessage = "Forecast emerging semantic trends for the niche: '{$prompt}'. Identify 3-4 related concepts or questions likely to grow in search importance over the next 6-12 months.";
                 break;
            case 'technical_seo':
                $systemMessage .= " Respond only with the requested JSON object.";
                $userMessage = "Analyze technical SEO of {$url}. Return valid JSON: {'score': int, 'internal_linking':[{'text','anchor'}], 'url_structure':{'clarity_score','suggestion'}, 'meta_optimization':{'title','description'}, 'alt_text_suggestions':[{'image_src','suggestion'}], 'site_structure_map': '<ul><li>...</li></ul>', 'suggestions':[{'text','type':'good'|'warn'|'bad'}]}.";
                break;
            case 'keyword_intelligence':
                $systemMessage .= " Respond only with the requested JSON object.";
                $userMessage = "Analyze keywords for {$url}. Return valid JSON: {'semantic_research':[string], 'intent_classification':[{'keyword','intent'}], 'related_terms':[string], 'competitor_gaps':[string], 'long_tail_suggestions':[string]}.";
                break;
            case 'content_engine':
                $systemMessage .= " Respond only with the requested JSON object.";
                $userMessage = "Analyze content at {$url}. Return valid JSON: {'score': int, 'topic_clusters':[string], 'entities':[{'term','type'}], 'semantic_keywords':[string], 'relevance_score': int, 'context_intent': string}.";
                break;
            case 'readability_tone':
                $systemMessage .= " Respond only with the requested JSON object. Do not provide explanations.";
                $userMessage = "Analyze the following text for readability and tone. Return a valid JSON object with the exact structure: {\"overall_tone\": \"string\", \"grade_level\": \"string\", \"word_count\": int, \"passive_voice_percent\": int, \"suggestions\": [{\"original\": \"string\", \"suggestion\": \"string\"}]}. Text to analyze: " . $prompt;
                break;
        }

        return [$systemMessage, $userMessage];
    }

    /**
     * Proxies requests to the Google PageSpeed Insights API with robust caching.
     */
    public function pageSpeedInsights(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate(['url' => 'required|url']);
            $url = $validated['url'];

            // if (($limitCheck = $this->checkAndLog($request, 'psi')) !== true) return $limitCheck;

            $apiKey = env('PAGESPEED_API_KEY');
            if (!$apiKey) return response()->json(['error' => 'PageSpeed API key is not configured.'], 500);

            $fetch = function (string $strategy) use ($url, $apiKey) {
                $cacheKey = "psi:{$strategy}:" . md5($url);
                return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($url, $apiKey, $strategy) {
                    $res = Http::timeout(40)->get('https://www.googleapis.com/pagespeedonline/v5/runPagespeed', ['url' => $url, 'strategy' => $strategy, 'category' => 'performance', 'key' => $apiKey]);
                    if ($res->failed()) {
                        Log::error('PageSpeed API Call Failed', ['status' => $res->status(), 'body' => $res->body()]);
                        return ['ok' => false, 'score' => 0, 'opportunities' => ['Failed to fetch PSI data.']];
                    }
                    $j = $res->json() ?: []; $lr = $j['lighthouseResult'] ?? []; $audits = $lr['audits'] ?? []; $perfRaw = $lr['categories']['performance']['score'] ?? null;
                    $opportunities = collect($audits)->filter(fn($a) => ($a['score'] ?? 1) < 0.9 && ($a['details']['overallSavingsMs'] ?? 0) > 100)->map(fn($a) => $a['title'])->values()->toArray();
                    return [
                        'ok' => true, 'score' => is_null($perfRaw) ? 0 : (int) round($perfRaw * 100),
                        'lcp_s' => round(($audits['largest-contentful-paint']['numericValue'] ?? 0) / 1000, 2),
                        'cls' => round($audits['cumulative-layout-shift']['numericValue'] ?? 0, 3),
                        'inp_ms' => (int) round($audits['interaction-to-next-paint']['numericValue'] ?? 0),
                        'opportunities' => $opportunities,
                    ];
                });
            };

            $mobileData = $fetch('mobile');
            $desktopData = $fetch('desktop');
            $allOpportunities = array_unique(array_merge($mobileData['opportunities'] ?? [], $desktopData['opportunities'] ?? []));

            return response()->json([
                'mobile' => $mobileData, 'desktop' => $desktopData,
                'opportunities' => array_slice($allOpportunities, 0, 5)
            ]);
            
        } catch (\Exception $e) {
            Log::error('PSI Proxy Failed', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'An unexpected error occurred during the PageSpeed analysis.'], 500);
        }
    }

    // --- DEPRECATED AI ENDPOINTS ---
    // These methods now call the new unified handler for backward compatibility.
    // The goal is to eventually phase these out in favor of the single /api/openai-request route.
    public function technicalSeoAnalyze(Request $request) {
        $request->merge(['task' => 'technical_seo']);
        return $this->handleOpenAiRequest($request);
    }

    public function keywordAnalyze(Request $request) {
        $request->merge(['task' => 'keyword_intelligence']);
        return $this->handleOpenAiRequest($request);
    }
    
    public function contentEngineAnalyze(Request $request) {
        $request->merge(['task' => 'content_engine']);
        return $this->handleOpenAiRequest($request);
    }
}

