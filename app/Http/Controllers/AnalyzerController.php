<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use DOMDocument;
use DOMXPath;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class AnalyzerController extends Controller
{
    /**
     * Handles the initial, non-AI analysis by parsing the page's raw HTML.
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
            
            // Basic Schema Check
            $schemaScripts = $xpath->query('//script[@type="application/ld+json"]');
            $schemaTypes = [];
            foreach($schemaScripts as $script) {
                $json = json_decode($script->textContent, true);
                if (isset($json['@type'])) {
                    $types = is_array($json['@type']) ? $json['@type'] : [$json['@type']];
                    $schemaTypes = array_merge($schemaTypes, $types);
                }
            }
            $pageSignals['schema_types'] = array_values(array_unique($schemaTypes));


            // Return a combined response structure for the frontend
            return response()->json([
                'overall_score' => 78, // Placeholder, client-side calculates real score
                'readability' => ['score' => 75, 'passive_ratio' => 10], // Placeholder
                'categories' => [['name' => 'Content & Keywords', 'score' => 82], ['name' => 'Content Quality', 'score' => 75]], // Placeholders
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
     */
    public function handleOpenAiRequest(Request $request): JsonResponse
    {
        $validTasks = [
            'brief', 'suggestions', 'competitor', 'trends', 'technical_seo', 'keyword_intelligence', 'content_engine',
            // --- All 16 new tasks are now validated ---
            'topic_coverage', 'intent_alignment', 'snippet_readiness', 'question_mining',
            'heading_hierarchy', 'readability_simplification', 'semantic_variants', 'eeat_signals',
            'internal_links', 'title_meta_rewrite', 'image_seo', 'tables_checklists',
            'schema_picker', 'content_freshness', 'cannibalization_check', 'ux_impact'
        ];

        $validated = $request->validate([
            'task' => ['required', 'string', Rule::in($validTasks)],
            'prompt' => 'nullable|string|max:2000',
            'url' => 'required|url'
        ]);

        $task = $validated['task'];
        $url = $validated['url'];
        
        $cacheKey = "ai:{$task}:" . md5($url . ($validated['prompt'] ?? ''));
        if (Cache::has($cacheKey)) {
            return response()->json(Cache::get($cacheKey));
        }

        $apiKey = env('OPENAI_API_KEY');
        if (!$apiKey) {
            return response()->json(['error' => 'OpenAI API key is not configured.'], 500);
        }

        [$systemMessage, $userMessage] = $this->generateAiPrompts($validated);
        if (empty($userMessage)) {
            return response()->json(['error' => 'Invalid task specified.'], 400);
        }

        try {
            $isJsonMode = in_array($task, [
                'technical_seo', 'keyword_intelligence', 'content_engine',
                'title_meta_rewrite', 'image_seo', 'schema_picker'
            ]);
            
            $response = Http::withToken($apiKey)->timeout(90)->post('https://api.openai.com/v1/chat/completions', [
                'model' => env('OPENAI_MODEL', 'gpt-4-turbo'),
                'messages' => [['role' => 'system', 'content' => $systemMessage], ['role' => 'user', 'content' => $userMessage]],
                'temperature' => 0.5,
                'max_tokens' => 1024,
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

        $systemMessage = "You are a world-class Semantic SEO expert. Your responses must be accurate, concise, and directly actionable. Respond only with the requested format (JSON or plain text). VERY IMPORTANT: Analyze the provided content and respond in the same language as the content (e.g., if the content is in Portuguese, your analysis and suggestions must also be in Portuguese).";
        $userMessage = "Analyze the content at {$url}.";

        switch ($task) {
            // Original tasks
            case 'brief': $userMessage = "Generate a semantic content brief for the primary keyword: '{$prompt}'. Include a suggested H1, a meta description (155 chars max), 3-5 LSI keywords, and 3-5 FAQs. Target URL: {$url}"; break;
            case 'suggestions': $userMessage .= " Provide 3-5 actionable recommendations to improve its semantic relevance and user engagement."; break;
            case 'competitor': $userMessage = "Analyze the user's page at '{$url}' against a competitor at '{$prompt}'. Identify the top 3-5 semantic strategy gaps on the user's page."; break;
            case 'trends': $userMessage = "Forecast emerging semantic trends for the niche: '{$prompt}'. Identify 3-4 related concepts or questions likely to grow in search importance."; break;
            case 'technical_seo': $systemMessage = "You are a technical SEO expert. Respond only with the requested JSON object."; $userMessage .= " Return valid JSON: {'score': int, 'internal_linking':[{'text','anchor'}], 'url_structure':{'clarity_score','suggestion'}, 'meta_optimization':{'title','description'}, 'alt_text_suggestions':[{'image_src','suggestion'}], 'site_structure_map': '<ul><li>...</li></ul>', 'suggestions':[{'text','type':'good'|'warn'|'bad'}]}."; break;
            case 'keyword_intelligence': $systemMessage = "You are a keyword research expert. Respond only with the requested JSON object."; $userMessage .= " Return valid JSON: {'semantic_research':[string], 'intent_classification':[{'keyword','intent'}], 'related_terms':[string], 'competitor_gaps':[string], 'long_tail_suggestions':[string]}."; break;
            case 'content_engine': $systemMessage = "You are a content strategist. Respond only with the requested JSON object."; $userMessage .= " Return valid JSON: {'score': int, 'topic_clusters':[string], 'entities':[{'term','type'}], 'semantic_keywords':[string], 'relevance_score': int, 'context_intent': string}."; break;
            
            // New tasks
            case 'topic_coverage': $userMessage .= " Extract the main entities/subtopics. List the top 5-7 entities that are clearly missing compared to what a comprehensive article on this topic should contain. Output as a plain text list."; break;
            case 'intent_alignment': $userMessage .= " Determine the primary search intent (Informational, Commercial, Transactional, Navigational). Then, flag any sections (like intro, specific H2s) whose tone or content mismatches this primary intent and briefly suggest how to align it. Output as plain text."; break;
            case 'snippet_readiness': $userMessage .= " Check if there is a concise (40-60 word) definition, a numbered list for steps, or a simple table suitable for a featured snippet. If not, generate one from the article's content. Output as plain text."; break;
            case 'question_mining': $userMessage .= " Based on the topic, suggest 3-5 frequently asked questions (like from People Also Ask or forums) that are NOT answered in the text. Phrase them as H2/H3 headings. Output as a plain text list of questions."; break;
            case 'heading_hierarchy': $userMessage .= " Audit the heading structure (H1-H4). Note any issues like multiple H1s, skipped levels (H2 to H4), or very thin sections (<120 words under an H2). Provide brief, actionable advice. Output as plain text."; break;
            case 'readability_simplification': $userMessage .= " Analyze the readability. Identify the most complex paragraph and provide a simplified, rewritten version of it. Output only the rewritten paragraph as plain text."; break;
            case 'semantic_variants': $userMessage .= " Find the primary keyword/topic. Suggest 5-7 semantic variants, synonyms, or LSI keywords that are missing and could be naturally integrated. Output as a plain text list."; break;
            case 'eeat_signals': $userMessage .= " Check for E-E-A-T signals. Note the presence/absence of: author byline/bio, last updated date, external citations to authoritative sources, and clear about/contact information. Provide simple text-based suggestions for what's missing."; break;
            case 'internal_links': $userMessage .= " Suggest 3-5 internal linking opportunities from within the article's text to other potential pages, including suitable anchor text. Format as: 'Anchor Text' -> [potential_page_topic]. Output as a plain text list."; break;
            case 'tables_checklists': $userMessage .= " Does the content lack structured data like tables or checklists? If so, auto-draft a simple comparison table or a checklist block that would be helpful for the reader based on the content. Output as clean HTML (using <table> or <ul>)."; break;
            case 'content_freshness': $userMessage .= " Scan the text for outdated information, like old years ('in 2022'), version numbers, or stale references. Highlight 2-3 specific phrases that need updating. Output as a plain text list."; break;
            case 'cannibalization_check': $userMessage .= " Based on the primary keyword and topics, what other keywords might this page be competing with on the same website? Suggest 1-2 potential cannibalization issues to investigate. Output as plain text."; break;
            case 'ux_impact': $userMessage .= " From a content perspective, identify elements that could negatively impact UX and Core Web Vitals. Check for things like a very large, un-optimized hero image, an intrusive popup mentioned in the text, or a very long paragraph without breaks. Provide brief, text-based warnings."; break;
            
            // New JSON tasks
            case 'title_meta_rewrite': $systemMessage = "You are a CTR optimization expert. Respond only with the requested JSON object."; $userMessage .= " Generate 3 improved Title & Meta Description options. Return valid JSON: {'suggestions': [{'title': string, 'meta': string}, {'title': string, 'meta': string}, {'title': string, 'meta': string}]}."; break;
            case 'image_seo': $systemMessage = "You are an Image SEO expert. Respond only with the requested JSON object."; $userMessage .= " Check for a hero image and generate alt text for up to 3 images lacking it. Return valid JSON: {'hero_image_present': boolean, 'alt_text_suggestions': [{'image_src': string, 'suggestion': string}]}."; break;
            case 'schema_picker': $systemMessage = "You are a Schema Markup expert. Respond only with the requested JSON object."; $userMessage .= " Pick the best schema (Article, FAQPage, HowTo, etc.) and generate the JSON-LD. Return valid JSON: {'schema_type': string, 'json_ld': object}."; break;
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
                    $opportunities = collect($audits)->filter(fn($a) => ($a['score'] ?? 1) < 0.9 && isset($a['details']['overallSavingsMs']) && $a['details']['overallSavingsMs'] > 100)->map(fn($a) => $a['title'])->values()->toArray();
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

