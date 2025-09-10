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
use Illuminate\Validation\Rule;
use App\Http\Controllers\AiReadabilityController;

class AnalyzerController extends Controller
{
    private function checkAndLog(Request $request, string $tool): bool|JsonResponse
    {
        if (!Auth::check()) return true;

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
     * Calculates dynamic SEO scores based on parsed page data.
     * @param array $data Parsed data from the URL.
     * @return array An array containing overall_score and categories.
     */
    private function calculateScores(array $data): array
    {
        $scores = [];
        $contentStructure = $data['content_structure'] ?? [];
        $pageSignals = $data['page_signals'] ?? [];
        $quickStats = $data['quick_stats'] ?? [];

        // 1. Title Score (Weight: 15)
        $titleLength = isset($contentStructure['title']) ? mb_strlen($contentStructure['title']) : 0;
        if ($titleLength >= 50 && $titleLength <= 65) {
            $scores['title'] = 100;
        } elseif ($titleLength > 0) {
            $scores['title'] = 60;
        } else {
            $scores['title'] = 10;
        }

        // 2. Meta Description Score (Weight: 10)
        $metaLength = isset($contentStructure['meta_description']) ? mb_strlen($contentStructure['meta_description']) : 0;
        if ($metaLength >= 120 && $metaLength <= 160) {
            $scores['meta'] = 100;
        } elseif ($metaLength > 0) {
            $scores['meta'] = 60;
        } else {
            $scores['meta'] = 10;
        }

        // 3. Headings Score (Weight: 20)
        $h1Count = isset($contentStructure['headings']['H1']) ? count($contentStructure['headings']['H1']) : 0;
        $h2Count = isset($contentStructure['headings']['H2']) ? count($contentStructure['headings']['H2']) : 0;
        $h1Score = ($h1Count === 1) ? 100 : (($h1Count > 1) ? 20 : 10);
        $h2Score = min(100, $h2Count * 15);
        $scores['headings'] = ($h1Score * 0.6) + ($h2Score * 0.4);

        // 4. Internal Links Score (Weight: 15)
        $internalLinks = $quickStats['internal_links'] ?? 0;
        $scores['internal_links'] = min(100, $internalLinks * 8);

        // 5. Technical SEO Score (Weight: 20)
        $canonicalScore = !empty($pageSignals['canonical']) ? 100 : 20;
        $viewportScore = !empty($pageSignals['has_viewport']) ? 100 : 20;
        $robotsContent = $pageSignals['robots'] ?? '';
        $robotsScore = (stripos($robotsContent, 'noindex') === false) ? 100 : 10;
        $scores['technical'] = ($canonicalScore + $viewportScore + $robotsScore) / 3;

        // 6. Image Alt Text Score (Weight: 10)
        $totalImages = $quickStats['total_images'] ?? 0;
        $imagesWithAlt = $quickStats['images_alt_count'] ?? 0;
        $scores['alt_text'] = ($totalImages > 0) ? round(($imagesWithAlt / $totalImages) * 100) : 100;

        // Calculate weighted average for overall score
        $weights = [
            'title' => 15, 'meta' => 10, 'headings' => 20,
            'internal_links' => 15, 'technical' => 20, 'alt_text' => 10,
        ];
        
        $totalScore = 0;
        $totalWeight = array_sum($weights);
        foreach ($scores as $key => $score) {
            $totalScore += $score * ($weights[$key] ?? 0);
        }
        $overallScore = ($totalWeight > 0) ? round($totalScore / $totalWeight) : 0;

        // Define categories based on individual scores
        $contentKeywordsScore = round(($scores['title'] + $scores['meta'] + $scores['headings']) / 3);
        $contentQualityScore = round(($scores['internal_links'] + $scores['alt_text'] + $scores['technical']) / 3);

        return [
            'overall_score' => (int) $overallScore,
            'categories' => [
                ['name' => 'Content & Keywords', 'score' => (int)$contentKeywordsScore],
                ['name' => 'Content Quality', 'score' => (int)$contentQualityScore]
            ]
        ];
    }

    /**
     * Extracts clean text content from a DOMXPath object.
     * @param DOMXPath $xpath The DOMXPath object of the page.
     * @return string The clean text content.
     */
    private function extractTextFromDom(DOMXPath $xpath): string
    {
        // Remove script and style tags to avoid including their content
        foreach ($xpath->query('//script | //style') as $node) {
            $node->parentNode->removeChild($node);
        }

        $bodyNode = $xpath->query('//body')->item(0);
        if (!$bodyNode) {
            return '';
        }
        
        // Get text content and normalize whitespace
        $textContent = $bodyNode->textContent;
        return trim(preg_replace('/\s+/', ' ', $textContent));
    }

    public function semanticAnalyze(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate(['url' => ['required', 'url']]);
            $urlToAnalyze = $validated['url'];

            $response = Http::timeout(20)->get($urlToAnalyze);
            if ($response->failed()) {
                return response()->json(['error' => "Failed to fetch URL. Status: {$response->status()}"], 400);
            }

            $html = $response->body();
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            @$dom->loadHTML($html);
            libxml_clear_errors();
            $xpath = new DOMXPath($dom);
            
            $textContent = $this->extractTextFromDom($xpath);
            
            // Get all humanizer data from the new dedicated controller
            $humanizerData = AiReadabilityController::getHumanizerDataForText($textContent);
            $readabilityData = ['score' => $humanizerData['human_score']]; // For compatibility with other parts

            $contentStructure = [];
            $contentStructure['title'] = optional($xpath->query('//title')->item(0))->textContent;
            $contentStructure['meta_description'] = optional($xpath->query("//meta[@name='description']/@content")->item(0))->nodeValue;
            $contentStructure['headings'] = [];
            foreach (['h1', 'h2', 'h3', 'h4'] as $tag) {
                $headings = $xpath->query('//' . $tag);
                $tagUpper = strtoupper($tag);
                $contentStructure['headings'][$tagUpper] = [];
                foreach ($headings as $heading) { $contentStructure['headings'][$tagUpper][] = trim($heading->textContent); }
            }
            
            $pageSignals = [];
            $pageSignals['canonical'] = optional($xpath->query("//link[@rel='canonical']/@href")->item(0))->nodeValue;
            $pageSignals['robots'] = optional($xpath->query("//meta[@name='robots']/@content")->item(0))->nodeValue;
            $pageSignals['has_viewport'] = $xpath->query("//meta[@name='viewport']")->length > 0;

            $quickStats = [];
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

            $images = $dom->getElementsByTagName('img');
            $imagesAltCount = 0;
            foreach ($images as $image) {
                if ($image->hasAttribute('alt') && !empty(trim($image->getAttribute('alt')))) {
                    $imagesAltCount++;
                }
            }
            $quickStats['total_images'] = $images->length;
            $quickStats['images_alt_count'] = $imagesAltCount;
            
            $parsedData = [
                'content_structure' => $contentStructure,
                'page_signals' => $pageSignals,
                'quick_stats' => $quickStats,
            ];

            $scores = $this->calculateScores($parsedData);

            return response()->json([
                'overall_score' => $scores['overall_score'],
                'readability' => $readabilityData,
                'humanizer' => $humanizerData, // Use the data from the new controller
                'categories' => $scores['categories'],
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


    public function handleOpenAiRequest(Request $request): JsonResponse
    {
        $validTasks = [
            'brief', 'suggestions', 'competitor', 'trends', 'technical_seo', 'keyword_intelligence', 'content_engine',
            'topic_coverage', 'intent_alignment', 'snippet_readiness', 'question_mining', 'heading_hierarchy',
            'readability_simplification', 'semantic_variants', 'eeat_signals', 'internal_links', 'title_meta_rewrite',
            'image_seo', 'tables_checklists', 'schema_picker', 'content_freshness', 'cannibalization_check', 'ux_impact'
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
                'technical_seo', 'keyword_intelligence', 'content_engine', 'title_meta_rewrite', 'schema_picker', 'image_seo'
            ]);
            
            $response = Http::withToken($apiKey)->timeout(90)->post('https://api.openai.com/v1/chat/completions', [
                'model' => env('OPENAI_MODEL', 'gpt-4-turbo'),
                'messages' => [['role' => 'system', 'content' => $systemMessage], ['role' => 'user', 'content' => $userMessage]],
                'temperature' => 0.5,
                'max_tokens' => 2048,
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
    
    private function generateAiPrompts(array $validatedData): array
    {
        $task = $validatedData['task'];
        $prompt = $validatedData['prompt'] ?? '';
        $url = $validatedData['url'];

        $systemMessage = "You are a world-class Semantic SEO expert. Analyze the content from the provided URL. Your responses must be accurate, concise, and directly actionable. Respond only with the requested format. IMPORTANT: Detect the primary language of the content on the URL (e.g., English, Arabic, Portuguese) and write your entire response in that same language.";
        $userMessage = "";


        switch ($task) {
            case 'brief':
                $userMessage = "Generate a semantic content brief for the primary keyword: '{$prompt}'. Include a suggested H1, a meta description (155 chars max), 3-5 LSI keywords, and 3-5 FAQs. Target URL for context: {$url}.";
                break;
            case 'suggestions':
                $userMessage = "Analyze the content at '{$url}'. Provide the top 3-5 most impactful recommendations to improve its semantic relevance and user engagement.";
                break;
            case 'competitor':
                $userMessage = "Analyze the user's page at '{$url}' against a competitor at '{$prompt}'. Identify the top 3-5 semantic strategy gaps on the user's page. What topics, entities, or questions does the competitor cover that the user is missing?";
                break;
            case 'trends':
                 $userMessage = "Forecast emerging semantic trends for the niche: '{$prompt}'. Identify 3-4 related concepts or questions likely to grow in search importance over the next 6-12 months, using {$url} as context.";
                 break;
            case 'technical_seo':
                $userMessage = "Analyze technical SEO of {$url}. Return valid JSON: {'score': int, 'internal_linking':[{'text','anchor'}], 'url_structure':{'clarity_score','suggestion'}, 'meta_optimization':{'title','description'}, 'alt_text_suggestions':[{'image_src','suggestion'}], 'site_structure_map': '<ul><li>...</li></ul>', 'suggestions':[{'text','type':'good'|'warn'|'bad'}]}.";
                break;
            case 'keyword_intelligence':
                $userMessage = "Analyze keywords for {$url}. Return valid JSON: {'semantic_research':[string], 'intent_classification':[{'keyword','intent'}], 'related_terms':[string], 'competitor_gaps':[string], 'long_tail_suggestions':[string]}.";
                break;
            case 'content_engine':
                $userMessage = "Analyze content at {$url}. Return valid JSON: {'score': int, 'topic_clusters':[string], 'entities':[{'term','type'}], 'semantic_keywords':[string], 'relevance_score': int, 'context_intent': string}.";
                break;
            case 'topic_coverage':
                $userMessage = "For the content at {$url}, extract the main entities/subtopics. List the top 5-7 entities that are clearly missing compared to what a top-ranking page for this topic should have. For each missing entity, write 1-2 sentences explaining why it's important to add.";
                break;
            case 'intent_alignment':
                $userMessage = "Analyze the search intent (Informational, Commercial, Transactional, Navigational) for the likely query that leads to {$url}. Then, analyze the tone of the page's introduction, body, and conclusion. Flag any sections that misalign with the primary user intent and suggest a brief fix. Example: 'Intro is commercial; query is informational → add a clear definition first.'";
                break;
            case 'snippet_readiness':
                $userMessage = "Analyze the content at {$url} for Featured Snippet readiness. Check for a concise 40-60 word definition block (like a 'What is...' section), a numbered list for steps, or a simple bulleted list. If missing, generate a ready-to-paste, optimized 50-word definition block based on the content.";
                break;
            case 'question_mining':
                $userMessage = "Based on the topic of the content at {$url}, suggest 3-5 highly relevant, unanswered questions that would make excellent H2/H3 sections. Source ideas from 'People Also Ask' and common forum questions for this topic. Output as a list of questions.";
                break;
            case 'heading_hierarchy':
                $userMessage = "Audit the heading hierarchy (H1-H4) of the page at {$url}. Check for a single H1, logical H2->H3 flow, and thin H2 sections (under 120 words). Provide a single, most important recommendation for improvement. E.g., 'H2 'Setup' is too thin; expand it with bullet points.'";
                break;
            case 'readability_simplification':
                $userMessage = "Analyze the content at {$url}. Identify the single most complex or difficult-to-read paragraph. Provide a one-click 'simplified' rewrite of that paragraph, aiming for a Grade 7-9 reading level, reducing passive voice and sentence length. Output as plain text, starting with 'Original:' and then 'Simplified:'.";
                break;
            case 'semantic_variants':
                $userMessage = "Analyze the content at {$url}. Detect the primary keyword. If it's over-optimized (stuffed), suggest reducing its density. Then, suggest 5-7 semantic variants or LSI keywords that are missing and should be naturally integrated.";
                break;
            case 'eeat_signals':
                $userMessage = "Check the page at {$url} for E-E-A-T signals. Look for an author byline, author bio, last updated date, and outbound citations to authoritative sources. List which of these four signals are present and which are missing. For missing signals, suggest a specific fix, like 'Add a 'Last Updated' timestamp.' Output as a list.";
                break;
            case 'internal_links':
                $userMessage = "Analyze the content at {$url}. Suggest 3-5 specific internal link opportunities from this page to other relevant pages that would likely exist on the same website. For each, provide the suggested anchor text. Output as a list.";
                break;
            case 'title_meta_rewrite':
                $userMessage = "Analyze the title and meta description for {$url}. Generate 3 improved, CTR-aware options for the title and meta description. The response must be valid JSON: {'suggestions': [{'title': string (max 60 chars), 'meta': string (max 155 chars)}, {'title': string, 'meta': string}, {'title': string, 'meta': string}]}.";
                break;
            case 'image_seo':
                $userMessage = "Analyze the image SEO for {$url}. Check for a hero image, alt text quality, and oversized images. Provide a JSON response: {'hero_image_present': boolean, 'alt_text_suggestions': [{'image_src': string, 'suggestion': 'A descriptive alt text suggestion'}], 'optimization_targets': [{'image_src': string, 'suggestion': 'Convert to WebP and resize to 800px width.'}]}. Limit suggestions to the top 3 most important images.";
                break;
            case 'tables_checklists':
                $userMessage = "Analyze the content at {$url}. Determine if the topic would benefit from a data table, checklist, or side-by-side comparison. If so, auto-draft a simple, ready-to-paste HTML block for a comparison table or a checklist based on the content. If not needed, state that. Output as plain text (with HTML if applicable).";
                break;
            case 'schema_picker':
                $userMessage = "Analyze the content at {$url} and determine the most appropriate and impactful Schema.org type (e.g., Article, FAQPage, HowTo, Product). Provide a valid, ready-to-paste JSON-LD script for that schema, populated with details from the page. The response must be valid JSON: {'schema_type': string, 'json_ld': { ... }}.";
                break;
            case 'content_freshness':
                $userMessage = "Scan the content at {$url} for signs of being outdated. Look for old years (e.g., 'in 2022'), outdated version numbers, or references to old UI/events. Highlight the top 2-3 most stale elements and suggest an update. Output as a list.";
                break;
            case 'cannibalization_check':
                $userMessage = "Assume you are analyzing the entire website that {$url} belongs to. Based on the primary keyword/intent of this page, identify 1-2 other potential keywords that could cause content cannibalization if separate pages were created for them. Recommend either consolidating them into this page or creating a distinct angle.";
                break;
            case 'ux_impact':
                $userMessage = "Analyze the content structure at {$url} from a UX perspective that impacts rankings. Check for a clear hero element (likely LCP), the potential for CLS from late-loading images or ads within the article body, and the presence of heavy widgets that could increase INP. Provide one key recommendation.";
                break;
        }

        return [$systemMessage, $userMessage];
    }

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
