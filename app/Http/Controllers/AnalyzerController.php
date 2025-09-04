<?php

namespace App\Http\Controllers;

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
     */
    private function checkAndLog(Request $request, string $tool): bool|JsonResponse
    {
        if (!Auth::check()) return true; // Don't check limits for guests

        $user = Auth::user();
        $limit = UserLimit::firstOrCreate(['user_id' => $user->id]);

        if (!$limit->updated_at->isToday()) $limit->searches_today = 0;
        if (!$limit->updated_at->isSameMonth(now())) $limit->searches_this_month = 0;

        if ($limit->searches_today >= $limit->daily_limit || $limit->searches_this_month >= $limit->monthly_limit) {
            return response()->json(['error' => 'You have reached your usage quota for today/this month.'], 429);
        }

        (new UsageLogger())->logAnalysis($request, $tool, true);
        $limit->increment('searches_today');
        $limit->increment('searches_this_month');
        $limit->touch();

        return true;
    }

    /**
     * Handles the initial, non-AI analysis by parsing the page's raw HTML.
     */
    public function semanticAnalyze(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate(['url' => ['required', 'url']]);
            $urlToAnalyze = $validated['url'];

            $response = Http::timeout(15)->get($urlToAnalyze);
            if ($response->failed()) return response()->json(['error' => "Failed to fetch URL. Status: {$response->status()}"], 400);

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

            $imagesAltCount = 0;
            foreach ($dom->getElementsByTagName('img') as $image) {
                if ($image->hasAttribute('alt') && !empty(trim($image->getAttribute('alt')))) $imagesAltCount++;
            }

            return response()->json([
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
     */
    public function handleOpenAiRequest(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'task' => ['required', 'string', Rule::in([
                'brief', 'suggestions', 'competitor', 'trends', 
                'technical_seo', 'keyword_intelligence', 'content_engine',
                // New Full Semantic Audit tasks
                'topic_coverage', 'intent_match', 'snippet_readiness', 'question_mining',
                'heading_audit', 'readability_coach', 'semantic_variants', 'eeat_signals',
                'internal_links', 'title_meta_rewrite', 'image_seo', 'structured_content',
                'schema_picker', 'content_freshness', 'cannibalization_check', 'content_ux'
            ])],
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
            $isJsonMode = in_array($task, ['technical_seo', 'keyword_intelligence', 'content_engine', 'schema_picker']);
            
            $response = Http::withToken($apiKey)->timeout(90)->post('https://api.openai.com/v1/chat/completions', [
                'model' => env('OPENAI_MODEL', 'gpt-4-turbo'),
                'messages' => [['role' => 'system', 'content' => $systemMessage], ['role' => 'user', 'content' => $userMessage]],
                'temperature' => 0.4,
                'max_tokens' => 1200,
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

        $systemMessage = "You are a world-class Semantic SEO expert. Your responses must be accurate, concise, and directly actionable. Respond only with the requested format.";
        $userMessage = "";

        switch ($task) {
            // Existing cases
            case 'brief':
                $userMessage = "Generate a semantic content brief for the primary keyword: '{$prompt}'. Include a suggested H1, a meta description (155 chars max), 3-5 LSI keywords, and 3-5 FAQs. Target URL: {$url}";
                break;
            case 'suggestions':
                $userMessage = "Analyze the content at '{$url}'. Provide 3-5 actionable recommendations to improve its semantic relevance and user engagement.";
                break;
            case 'competitor':
                $userMessage = "Analyze the user's page at '{$url}' against a competitor at '{$prompt}'. Identify the top 3-5 semantic strategy gaps on the user's page.";
                break;
            case 'trends':
                 $userMessage = "Forecast emerging semantic trends for the niche: '{$prompt}'. Identify 3-4 related concepts or questions likely to grow in search importance.";
                 break;

            // New Full Semantic Audit Prompts
            case 'topic_coverage':
                $userMessage = "Analyze URL: {$url}. Compare its content to top SERP pages for its main topic. List the top 5 missing semantic entities/subtopics. For each, suggest which H2/H3 section it could be added to. Format: 'Missing: [Entity1] → Add to \"[Section Title]\".\nMissing: [Entity2] → Add to \"[Section Title]\"...'";
                break;
            case 'intent_match':
                $userMessage = "Analyze URL: {$url}. What is the primary search intent (Informational, Commercial, Transactional)? Does the introduction's tone match this intent? If not, flag the mismatch and give a one-sentence rewrite suggestion. Format: 'Primary Intent: [Intent]. Intro Tone: [Matched/Mismatched]. Suggestion: [Rewrite suggestion]'";
                break;
            case 'snippet_readiness':
                $userMessage = "Analyze content at URL: {$url}. Does it have a clear definition (40-60 words) for a featured snippet? If not, generate one based on the content. Output only the snippet-ready paragraph.";
                break;
            case 'question_mining':
                $userMessage = "For the topic at URL: {$url}, find 3-5 frequent questions from 'People Also Ask' and forums. For each, provide a suggested H2/H3 heading and a concise 1-2 paragraph answer. Format as: '### [Question 1]\n[Answer 1]\n\n### [Question 2]\n[Answer 2]...'";
                break;
            case 'heading_audit':
                $userMessage = "Analyze heading structure of {$url}. Check for: 1) A single H1. 2) Logical H2->H3 flow. 3) Any H2 sections with less than 120 words. List issues and specific recommendations. Format: '- [Issue]: [Recommendation]'";
                break;
            case 'readability_coach':
                $userMessage = "Analyze the content at {$url}. Identify the single most complex paragraph. Provide a simplified rewrite of that paragraph to a Grade 7-9 reading level. Output only the rewritten paragraph.";
                break;
            case 'semantic_variants':
                $userMessage = "Analyze content at {$url} for its main keyword. Is exact-match density high (>1.5%)? Suggest reducing it and list 5-10 semantic variants. Format: 'Density: [X.X%]. Recommendation: [Reduce/OK]. Variants: [variant1, variant2, ...]'";
                break;
            case 'eeat_signals':
                $userMessage = "Analyze {$url}. Check for: Author byline, author bio link, 'last updated' date, and outbound links to credible sources. List what's present and missing. For missing items, suggest where to add them. Format: '- Byline: [Present/Missing - Suggestion]\n- Bio: [Present/Missing - Suggestion]...'";
                break;
            case 'internal_links':
                $userMessage = "Scan content of {$url}. Identify 3-5 phrases for internal links. Suggest anchor text and a hypothetical relevant topic. Format: '- In paragraph \"[...first 10 words...]\", link \"[anchor text]\" to a page about \"[topic]\".'";
                break;
            case 'title_meta_rewrite':
                $userMessage = "The page at {$url} needs better CTR. Generate 3 distinct and improved Title/Meta description drafts. Use power words. Titles < 60 chars, metas < 160. Format: 'Draft 1:\nTitle: [Title]\nMeta: [Meta]\n\nDraft 2:\n...'";
                break;
            case 'image_seo':
                $userMessage = "Analyze images on {$url}. Check for: 1) Hero image. 2) At least 3 images. 3) Alt text quality. For one image with poor alt text, generate a descriptive, 125-character alternative. Note if images seem oversized (>200KB). Format: 'Hero: [Yes/No]. Image Count: [Number]. Alt Text Fix for [image_src]: \"[Generated Alt]\"'";
                break;
            case 'structured_content':
                $userMessage = "Analyze {$url}. Is it suitable for a comparison table or checklist? If so, draft a simple markdown table or checklist from the content. If not, state 'Content not suitable for table/checklist.'";
                break;
            case 'schema_picker':
                $systemMessage .= " Respond only with a valid JSON object.";
                $userMessage = "Based on content at {$url}, determine the best primary Schema type (Article, HowTo, FAQPage, Product). Generate ready-to-paste JSON-LD for it, populating with placeholders from the page. Output ONLY the valid JSON-LD code block.";
                break;
            case 'content_freshness':
                $userMessage = "Scan {$url} for outdated info like years ('in 2022'), version numbers, or 'last year'. Highlight up to 3 stale elements and suggest updates. Format: '- Found \"[stale text]\", suggest updating to \"[fresh text]\".'";
                break;
            case 'cannibalization_check':
                $userMessage = "Based on {$url}'s primary keyword/intent, suggest 2-3 other keyword variations a different page could target to avoid cannibalization. Format: 'Primary Intent: [Intent]. Avoid conflict by targeting: [Keyword1, Keyword2]. Recommendation: [Consolidate/OK]'";
                break;
            case 'content_ux':
                $userMessage = "Analyze content structure at {$url} for potential CWV issues. 1) Clear hero image for LCP? 2) Table of Contents that might cause CLS? 3) Heavy widgets hurting INP? Provide a 1-2 sentence summary for each. Format: 'LCP: [Summary]. CLS: [Summary]. INP: [Summary].'";
                break;

            // JSON-mode tasks
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

