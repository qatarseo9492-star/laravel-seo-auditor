<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log; // Required for logging
use App\Services\OpenAIService;      // Required for the new feature

class AnalyzerController extends Controller
{
    /**
     * POST /semantic-analyzer/analyze
     */
    public function semanticAnalyze(Request $request, OpenAIService $openAIService) // This is the critical change
    {
        $data = $request->validate([
            'url'            => ['required','url'],
            'target_keyword' => ['nullable','string','max:160'],
        ]);

        $url = (string) $data['url'];
        $kw  = trim((string)($data['target_keyword'] ?? ''));
        $ua  = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124 Safari/537.36';

        try {
            // 1) Fetch HTML (Original code, unchanged)
            $resp = $this->fetchUrl($url, $ua);
            if (!$resp['ok']) {
                return response()->json(['error' => $resp['error'] ?? 'Fetch failed'], 422);
            }
            $html = $resp['body'] ?? '';
            $host = $resp['host'] ?? parse_url($url, PHP_URL_HOST);

            // All your original analysis logic remains unchanged
            $dom = $this->makeDom($html);
            $xp  = new \DOMXPath($dom);
            $mainText        = $this->extractMainText($dom);
            $title           = $this->extractTitle($dom);
            $metaDescription = $this->extractMeta($dom, 'description');
            $headings        = $this->extractHeadings($dom);
            $links           = $this->extractLinks($dom, $host);
            $imagesAltCount  = $this->countImagesWithAlt($dom);
            $schemaCount     = $this->countSchemaBlocks($dom);
            $ratio           = $this->textToHtmlRatio($html, $mainText);
            $firstParagraph  = $this->firstParagraph($dom);
            $lazyImgCount    = $this->countLazyImages($dom);
            $figcaptionCount = $this->countFigcaptions($dom);
            $hasOgOrTwitter  = $this->hasOpenGraphOrTwitter($xp);
            $canonical   = $this->extractCanonical($xp);
            $robots      = $this->extractMetaRobots($xp);
            $hasViewport = $this->hasViewport($xp);
            $jsonldSummary = $this->scanJsonLd($xp);
            $readability = $this->computeReadabilityFromText($mainText);
            $categories   = $this->buildCategories(
                $url, $title, $metaDescription, $headings, $links, $imagesAltCount, $schemaCount,
                $kw, $readability, $jsonldSummary, $hasViewport, $robots, $firstParagraph,
                $lazyImgCount, $figcaptionCount, $hasOgOrTwitter, $canonical, $mainText
            );
            $overallScore = $this->computeOverallScore($categories, $readability);
            $wheel        = ['label' => $this->wheelLabel($overallScore)];
            $recs         = $this->buildRecommendations($links, $imagesAltCount, $schemaCount, $headings, $readability, $kw);

            $jsonResponse = [
                'overall_score'      => $overallScore,
                'wheel'              => $wheel,
                'schema_count'       => $schemaCount,
                'images_alt_count'   => $imagesAltCount,
                'page_signals'       => [
                    'canonical'        => $canonical,
                    'robots'           => $robots,
                    'has_viewport'     => $hasViewport,
                    'schema_types'     => $jsonldSummary['types'],
                    'has_breadcrumbs'  => $jsonldSummary['has_breadcrumbs'],
                    'has_org_sameas'   => $jsonldSummary['has_org_sameas'],
                    'has_main_entity'  => $jsonldSummary['has_main_entity'],
                    'has_author'       => $jsonldSummary['has_author'],
                    'has_date_pub'     => $jsonldSummary['has_date_published'],
                    'has_faq'          => $jsonldSummary['has_faq'],
                    'has_product'      => $jsonldSummary['has_product'],
                    'has_article'      => $jsonldSummary['has_article'],
                ],
                'quick_stats'        => [
                    'readability_flesch' => $readability['flesch'],
                    'readability_grade'  => $readability['grade'],
                    'internal_links'     => $links['internal'],
                    'external_links'     => $links['external'],
                    'text_to_html_ratio' => $ratio,
                ],
                'content_structure'  => [
                    'title'            => $title,
                    'meta_description' => $metaDescription,
                    'headings'         => $headings,
                ],
                'readability'        => $readability,
                'recommendations'    => $recs,
                'categories'         => $categories,
            ];

            // =================================================================
            // NEW: OpenAI Content Optimization Integration
            // =================================================================
            try {
                // Call the service with the HTML content we fetched earlier
                $optimizationData = $openAIService->getOptimizationData($html);
                $jsonResponse['content_optimization'] = $optimizationData;
            } catch (\Throwable $e) {
                Log::error('OpenAI Content Optimization failed in Controller: ' . $e->getMessage());
                $jsonResponse['content_optimization'] = null;
            }
            // =================================================================


            // ADDED FOR DEBUGGING: This will write the result to your log file.
            Log::info('Final OpenAI Data Sent to Frontend:', (array)($jsonResponse['content_optimization'] ?? ['status' => 'Not found or failed']));

            return response()->json($jsonResponse);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Analyzer error: '.$e->getMessage()
            ], 500);
        }
    }

    // ... All of your other functions (psi, fetchUrl, makeDom, etc.) remain below this line, unchanged ...
    // I am omitting them here for brevity but they are in the full file.
    /**
     * PSI proxy
     * Accepts POST (JSON) or GET (?url=) and returns both mobile + desktop metrics.
     * Reads config('services.pagespeed.*').
     *
     * Routes to define (one or both):
     * Route::post('/semantic-analyzer/psi', [AnalyzerController::class, 'psi'])->name('semantic.psi');
     * Route::get('/semantic-analyzer/psi',  [AnalyzerController::class, 'psi']);
     */
    public function psi(Request $request)
    {
        $url = trim((string) ($request->input('url') ?? $request->query('url', '')));
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return response()->json(['ok' => false, 'error' => 'Invalid URL'], 422);
        }

        $cfg      = config('services.pagespeed', []);
        $key      = $cfg['key'] ?? env('GOOGLE_PSI_KEY');
        $endpoint = rtrim($cfg['endpoint'] ?? 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed', '/');
        $timeout  = (int)($cfg['timeout']   ?? 25);
        $ttl      = (int)($cfg['cache_ttl'] ?? 120);

        if (!$key) {
            // 200 with ok=false so UI can show a friendly error
            return response()->json([
                'ok'    => false,
                'error' => 'PSI key missing',
                'hint'  => 'Set PAGESPEED_API_KEY in .env and run: php artisan config:clear',
            ], 200);
        }

        try {
            $mobile  = Cache::remember("psi:m:".md5($url), $ttl, fn () => $this->callPsiOnce($endpoint, $key, $timeout, $url, 'mobile'));
            $desktop = Cache::remember("psi:d:".md5($url), $ttl, fn () => $this->callPsiOnce($endpoint, $key, $timeout, $url, 'desktop'));

            return response()->json([
                'ok'      => true,
                'url'     => $url,
                'mobile'  => $mobile,   // { score, lcp, cls, inp, ttfb }
                'desktop' => $desktop,  // { score, lcp, cls, inp, ttfb }
            ]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'error' => 'PSI request failed: '.$e->getMessage()], 200);
        }
    }

    public function aiCheck(Request $request)
    {
        return response()->json(['ok' => true, 'note' => 'AI checker stub']);
    }

    public function topicClusterAnalyze(Request $request)
    {
        return response()->json(['ok' => true, 'note' => 'Topic cluster stub']);
    }

    /* ===========================================================
     | Fetching & Parsing
     * ===========================================================*/
    private function fetchUrl(string $url, string $ua): array
    {
        try {
            $res = Http::withHeaders([
                    'User-Agent' => $ua,
                    'Accept'     => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                ])
                ->timeout(20)
                ->retry(2, 300)
                ->get($url);

            if (!$res->ok()) {
                return ['ok'=>false, 'error'=>"HTTP ".$res->status()];
            }

            return [
                'ok'   => true,
                'body' => (string) $res->body(),
                'host' => parse_url($url, PHP_URL_HOST),
            ];
        } catch (\Throwable $e) {
            return ['ok'=>false, 'error'=>$e->getMessage()];
        }
    }

    private function makeDom(string $html): \DOMDocument
    {
        // Keep <script> tags so JSON-LD stays available for schema detection.
        $dom = new \DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $dom->loadHTML($html, LIBXML_NOERROR|LIBXML_NOWARNING);
        libxml_clear_errors();
        return $dom;
    }

    /* ===========================================================
     | Small DOM helpers (avoid "length on bool")
     * ===========================================================*/
    private function xpQuery(\DOMXPath $xp, string $expr): ?\DOMNodeList
    {
        $nodes = @$xp->query($expr);
        return ($nodes instanceof \DOMNodeList) ? $nodes : null;
    }
    private function xpLen(\DOMXPath $xp, string $expr): int
    {
        $nodes = $this->xpQuery($xp, $expr);
        return $nodes ? $nodes->length : 0;
    }

    /* ===========================================================
     | Extraction helpers
     * ===========================================================*/
    private function extractTitle(\DOMDocument $dom): string
    {
        $nodes = $dom->getElementsByTagName('title');
        return ($nodes && $nodes->length) ? $this->cleanText($nodes->item(0)->textContent) : '';
    }

    private function extractMeta(\DOMDocument $dom, string $name): string
    {
        $xp = new \DOMXPath($dom);
        $nodes = $this->xpQuery($xp, "//meta[translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='{$name}']/@content");
        if ($nodes && $nodes->length) return $this->cleanText($nodes->item(0)->nodeValue);

        if ($name === 'description') {
            $nodes = $this->xpQuery($xp, "//meta[translate(@property,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='og:description']/@content");
            if ($nodes && $nodes->length) return $this->cleanText($nodes->item(0)->nodeValue);
        }
        return '';
    }

    private function extractHeadings(\DOMDocument $dom): array
    {
        $out = ['H1'=>[], 'H2'=>[], 'H3'=>[], 'H4'=>[], 'H5'=>[], 'H6'=>[]];
        foreach (['h1','h2','h3','h4','h5','h6'] as $h) {
            $nodes = $dom->getElementsByTagName($h);
            foreach ($nodes as $n) {
                $txt = $this->cleanText($n->textContent);
                if ($txt !== '') $out[strtoupper($h)][] = $txt;
            }
        }
        return $out;
    }

    private function extractLinks(\DOMDocument $dom, ?string $host): array
    {
        $internal = 0; $external = 0;
        $xp = new \DOMXPath($dom);
        $nodes = $this->xpQuery($xp, '//a[@href]');
        if ($nodes) {
            foreach ($nodes as $a) {
                /** @var \DOMElement $a */
                $href = $a->getAttribute('href');
                if (!$href || strpos($href, 'javascript:')===0) continue;
                $isAbs = preg_match('#^https?://#i', $href);
                if (!$isAbs) { $internal++; continue; }
                $h = parse_url($href, PHP_URL_HOST);
                if ($host && $h && $this->sameHost($host, $h)) $internal++; else $external++;
            }
        }
        return ['internal'=>$internal, 'external'=>$external];
    }

    private function sameHost(string $a, string $b): bool
    {
        $a = preg_replace('/^www\./i','', strtolower($a));
        $b = preg_replace('/^www\./i','', strtolower($b));
        return $a === $b;
    }

    /** Counts <img> with non-empty alt */
    private function countImagesWithAlt(\DOMDocument $dom): int
    {
        $xp = new \DOMXPath($dom);
        $nodes = $this->xpQuery($xp, '//img');
        $count = 0;
        if ($nodes) {
            foreach ($nodes as $img) {
                /** @var \DOMElement $img */
                $alt = trim($img->getAttribute('alt') ?? '');
                if ($alt !== '') $count++;
            }
        }
        return $count;
    }

    private function textToHtmlRatio(string $html, string $mainText): int
    {
        $lenHtml = max(1, mb_strlen($html, '8bit'));
               $lenText = max(0, mb_strlen($mainText, 'UTF-8'));
        $ratio = ($lenText / $lenHtml) * 100;
        return (int) round(min(100, max(0, $ratio)));
    }

    private function extractCanonical(\DOMXPath $xp): string
    {
        $nodes = $this->xpQuery($xp, "//link[translate(@rel,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='canonical']/@href");
        return ($nodes && $nodes->length) ? trim((string)$nodes->item(0)->nodeValue) : '';
    }

    private function extractMetaRobots(\DOMXPath $xp): string
    {
        $nodes = $this->xpQuery($xp, "//meta[translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='robots']/@content");
        return ($nodes && $nodes->length) ? strtolower(trim((string)$nodes->item(0)->nodeValue)) : '';
    }

    private function hasViewport(\DOMXPath $xp): bool
    {
        return $this->xpLen($xp, "//meta[translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='viewport']") > 0;
    }

    private function firstParagraph(\DOMDocument $dom): string
    {
        $xp   = new \DOMXPath($dom);
        $nodes = $this->xpQuery($xp, '//p[normalize-space(string())!=""]');
        $node = $nodes && $nodes->length ? $nodes->item(0) : null;
        return $node ? $this->cleanText($node->textContent) : '';
    }

    private function countLazyImages(\DOMDocument $dom): int
    {
        $xp = new \DOMXPath($dom);
        return $this->xpLen($xp, '//img[translate(@loading,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="lazy"]');
    }

    private function countFigcaptions(\DOMDocument $dom): int
    {
        return $dom->getElementsByTagName('figcaption')->length;
    }

    private function hasOpenGraphOrTwitter(\DOMXPath $xp): bool
    {
        $og = $this->xpQuery($xp, "//meta[translate(@property,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='og:title']");
        $tw = $this->xpQuery($xp, "//meta[translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='twitter:card']");
        return (($og && $og->length) || ($tw && $tw->length));
    }

    /**
     * Parse JSON-LD types & flags
     */
    private function scanJsonLd(\DOMXPath $xp): array
    {
        $types           = [];
        $hasBreadcrumbs  = false;
        $hasOrgSameAs    = false;
        $hasMainEntity   = false;
        $hasAuthor       = false;
        $hasDatePublished= false;
        $hasArticle      = false;
        $hasProduct      = false;
        $hasFAQ          = false;

        $nodes = $this->xpQuery($xp, '//script[@type="application/ld+json"]');
        if ($nodes) {
            foreach ($nodes as $node) {
                $raw = trim((string)$node->nodeValue);
                if ($raw === '') continue;
                $json = json_decode($raw, true);
                if ($json === null) continue;

                foreach ($this->iterateJsonLd($json) as $obj) {
                    if (!is_array($obj)) continue;

                    if (isset($obj['@type'])) {
                        $t = $obj['@type'];
                        $list = is_array($t) ? $t : [$t];
                        foreach ($list as $tt) {
                            $tt = (string)$tt;
                            $types[] = $tt;
                            $low = strtolower($tt);
                            if ($low === 'breadcrumblist') $hasBreadcrumbs = true;
                            if ($low === 'faqpage')       $hasFAQ = true;
                            if (in_array($low, ['article','newsarticle','blogposting'], true)) $hasArticle = true;
                            if ($low === 'product')       $hasProduct = true;
                            if ($low === 'organization' && !empty($obj['sameAs'])) $hasOrgSameAs = true;
                        }
                    }

                    if (isset($obj['mainEntity']) || isset($obj['mainEntityOfPage'])) $hasMainEntity = true;
                    if (isset($obj['author']) || isset($obj['creator'])) $hasAuthor = true;
                    if (isset($obj['datePublished']) || isset($obj['dateModified'])) $hasDatePublished = true;
                }
            }
        }

        $types = array_values(array_unique($types));

        return [
            'types'             => $types,
            'has_breadcrumbs'   => $hasBreadcrumbs,
            'has_org_sameas'    => $hasOrgSameAs,
            'has_main_entity'   => $hasMainEntity,
            'has_author'        => $hasAuthor,
            'has_date_published'=> $hasDatePublished,
            'has_article'       => $hasArticle,
            'has_product'       => $hasProduct,
            'has_faq'           => $hasFAQ,
        ];
    }

    private function iterateJsonLd($node): \Generator
    {
        if (is_array($node)) {
            if (isset($node['@type']) || isset($node['@context'])) yield $node;
            foreach ($node as $v) {
                if (is_array($v)) {
                    foreach ($this->iterateJsonLd($v) as $sub) yield $sub;
                }
            }
        } elseif (is_object($node)) {
            yield from $this->iterateJsonLd((array)$node);
        }
    }

    private function extractMainText(\DOMDocument $dom): string
    {
        $xp = new \DOMXPath($dom);
        $candidates = [
            '//article',
            '//*[self::main or self::section or self::div][not(@role="navigation")]'
        ];

        $bestTxt = '';
        $bestScore = 0;

        foreach ($candidates as $q) {
            $nodes = $this->xpQuery($xp, $q);
            if (!$nodes) continue;
            foreach ($nodes as $node) {
                $txt = $this->nodeVisibleText($node);
                $len = mb_strlen($txt, 'UTF-8');
                if ($len < 200) continue;
                $punct = preg_match_all('/[\,\.]/u', $txt);
                $score = $len * (1 + $punct);
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestTxt = $txt;
                }
            }
        }

        if ($bestTxt === '') {
            $body = $dom->getElementsByTagName('body')->item(0);
            if ($body) $bestTxt = $this->nodeVisibleText($body);
        }

        return $this->cleanText($bestTxt);
    }

    private function nodeVisibleText(\DOMNode $node): string
    {
        $skip = ['script','style','noscript','nav','footer','header','aside','svg'];
        if ($node->nodeType === XML_TEXT_NODE) return $node->textContent;

        $out = '';
        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $child) {
                if ($child->nodeType === XML_ELEMENT_NODE) {
                    /** @var \DOMElement $child */
                    if (in_array(strtolower($child->nodeName), $skip, true)) continue;
                    $hidden = strtolower($child->getAttribute('aria-hidden') ?? '') === 'true';
                    if ($hidden) continue;
                }
                $out .= $this->nodeVisibleText($child);
            }
        }
        return $out;
    }

    private function cleanText(?string $s): string
    {
        $s = (string)$s;
        $s = html_entity_decode($s, ENT_QUOTES|ENT_HTML5, 'UTF-8');
        $s = preg_replace('/\s+/u', ' ', $s);
        return trim($s);
    }

    /* ===========================================================
     | Readability (multilingual)
     * ===========================================================*/

    private function splitSentences(string $text): array {
        $text = preg_replace('/\s+/u', ' ', trim($text));
        if ($text === '') return [];
        $text = preg_replace('/([\.!\?؟।]|。|！|？)\s+/u', "$1\n", $text);
        $parts = preg_split("/\n+/u", $text) ?: [];
        return array_values(array_filter(array_map('trim', $parts), fn($s) => $s !== ''));
    }

    private function tokenizeWords(string $text): array {
        preg_match_all('/[\p{L}\']+/u', mb_strtolower($text, 'UTF-8'), $m);
        return $m[0] ?? [];
    }

    private function countSyllables(string $word): int {
        $w = strtolower(preg_replace('/[^a-z]/', '', $word));
        if ($w === '') return 0;
        preg_match_all('/[aeiouy]+/', $w, $m);
        $count = count($m[0]);
        if (strlen($w) > 2 && substr($w, -1) === 'e' && !preg_match('/[aeiouy][^aeiouy]e$/', $w)) $count--;
        if (preg_match('/[^aeiouy]le$/', $w)) $count++;
        return max(1, $count);
    }

    private function languageLooksLatin(string $text): bool
    {
        preg_match_all('/\p{L}/u', $text, $L);
        $letters = $L[0] ?? [];
        if (!$letters) return true;
        $latin = 0;
        foreach ($letters as $ch) {
            if (preg_match('/[\p{Latin}]/u', $ch)) $latin++;
        }
        return ($latin / max(1, count($letters))) >= 0.6;
    }

    private function computeReadabilityFromText(string $text): array
    {
        $sentences = $this->splitSentences($text);
        $sCount    = max(1, count($sentences));
        $words     = $this->tokenizeWords($text);
        $wCount    = max(1, count($words));

        $types = array_unique($words);
        $ttr   = round((count($types)/$wCount) * 100, 1);

        $trigrams = [];
        for ($i=0; $i < max(0, $wCount-2); $i++) {
            $tri = $words[$i].' '.$words[$i+1].' '.$words[$i+2];
            $trigrams[$tri] = ($trigrams[$tri] ?? 0) + 1;
        }
        $repTri = 0;
        foreach ($trigrams as $c) if ($c > 1) $repTri += $c;
        $repPct = min(100, round(($repTri / max(1, count($trigrams))) * 100));

        preg_match_all('/\p{N}+/u', $text, $D);
        $digits       = count($D[0] ?? []);
        $digitsPer100 = round(($digits / $wCount) * 100);

        $isLatin = $this->languageLooksLatin($text);

        if ($isLatin) {
            $letters = 0; $chars = 0; $syll = 0; $poly = 0; $complex = 0;
            foreach ($words as $w) {
                $letters += preg_match_all('/[a-z]/', $w);
                $chars   += strlen($w);
                $sy      = $this->countSyllables($w);
                $syll   += $sy;
                if ($sy >= 3) { $poly++; $complex++; }
            }

            $asl = $wCount / $sCount;
            $asw = $syll   / $wCount;

            $flesch = 206.835 - 1.015 * $asl - 84.6 * $asw;
            $fk     = 0.39 * $asl + 11.8 * $asw - 15.59;
            $smog   = 1.0430 * sqrt($poly * (30.0 / $sCount)) + 3.1291;
            $fog    = 0.4 * ($asl + 100.0 * ($complex / $wCount));
            $L      = ($letters / $wCount) * 100.0;
            $S      = ($sCount  / $wCount) * 100.0;
            $cli    = 0.0588 * $L - 0.296 * $S - 15.8;
            $ari    = 4.71 * ($chars / $wCount) + 0.5 * $asl - 21.43;

            preg_match_all('/\b(is|are|was|were|be|been|being)\s+[a-z]+ed\b/i', $text, $pv);
            $passiveHits  = count($pv[0] ?? []);
            $passiveRatio = min(100, round(($passiveHits / $sCount) * 100));

            $avgGrade   = max(1, min(18, ($fk + $smog + $fog + $cli + $ari) / 5.0));
            $fre        = max(0, min(100, $flesch));
            $gradeScore = max(0, min(100, 100 - (($avgGrade - 1) * (100.0 / 17.0))));
            $score      = (int) round(($fre + $gradeScore) / 2);

            $badge = $score >= 80 ? 'Very Easy To Read' : ($score >= 60 ? 'Good — Needs More Improvement' : 'Needs Improvement in Content');

            return [
                'language'            => 'latin-like',
                'score'               => $score,
                'badge'               => $badge,
                'grade'               => round($avgGrade, 1),
                'flesch'              => round($flesch, 1),
                'fk_grade'            => round($fk, 1),
                'smog'                => round($smog, 1),
                'gunning_fog'         => round($fog, 1),
                'coleman_liau'        => round($cli, 1),
                'ari'                 => round($ari, 1),
                'avg_sentence_len'    => round($wCount / $sCount, 2),
                'ttr'                 => $ttr,
                'repetition_trigram'  => $repPct,
                'digits_per_100w'     => $digitsPer100,
                'simple_words_ratio'  => max(0, min(100, round((($wCount - $complex) / $wCount) * 100))),
                'passive_ratio'       => $passiveRatio,
                'note'                => 'Multimetric blend (Flesch + grade family) on a 0–100 scale.',
            ];
        }

        $asl  = $wCount / $sCount;
        $long = 0; foreach ($words as $w) if (mb_strlen($w,'UTF-8') >= 7) $long++;
        $lix  = $asl + ($long * 100 / $wCount);
        $score = (int) round(max(0, min(100, 100 - (($lix - 20) * 2))));
        $badge = $score >= 80 ? 'Very Easy To Read' : ($score >= 60 ? 'Good — Needs More Improvement' : 'Needs Improvement in Content');

        return [
            'language'            => 'non-latin',
            'score'               => $score,
            'badge'               => $badge,
            'grade'               => round(max(1, min(18, 19 - $score/6))),
            'flesch'              => null,
            'fk_grade'            => null,
            'smog'                => null,
            'gunning_fog'         => null,
            'coleman_liau'        => null,
            'ari'                 => null,
            'avg_sentence_len'    => round($asl, 2),
            'ttr'                 => $ttr,
            'repetition_trigram'  => $repPct,
            'digits_per_100w'     => $digitsPer100,
            'simple_words_ratio'  => null,
            'passive_ratio'       => null,
            'note'                => 'Non-Latin content: LIX-style mapping to 0–100.',
        ];
    }

    /* ===========================================================
     | Categories (6 x 5 checks) + scoring
     * ===========================================================*/
    private function buildCategories(
        string $url,
        string $title,
        string $meta,
        array  $headings,
        array  $links,
        int    $imagesAlt,
        int    $schemaCount,
        string $kw,
        array  $readability,
        array  $json,
        bool   $hasViewport,
        string $robots,
        string $firstParagraph,
        int    $lazyImgCount,
        int    $figcaptionCount,
        bool   $hasOgOrTwitter,
        string $canonical,
        string $mainText
    ): array {
        $band = fn(int $s)=> $s>=80?'green':($s>=60?'orange':'red');
        $hasH1   = !empty($headings['H1']);
        $h2Count = count($headings['H2'] ?? []);
        $h3Count = count($headings['H3'] ?? []);
        $contentLen = mb_strlen($firstParagraph ?: implode(' ', $headings['H1'] ?? []), 'UTF-8');

        $boolScore = fn(bool $ok, int $good=90, int $bad=40) => $ok ? $good : $bad;
        $ctaWords  = ['buy','shop','get started','contact','learn more','sign up','subscribe','download','try','book','start'];
        $hasCTA    = $this->containsAny($firstParagraph.' '.implode(' ', $headings['H2'] ?? []), $ctaWords);
        $kwLower   = Str::lower($kw);
        $h1HasKW   = $kw ? $this->containsAny(implode(' ', $headings['H1'] ?? []), [$kwLower]) : false;

        // -------- 1) User Signals & Experience
        $uxChecks = [];
        $uxChecks[] = $this->mkCheck('Mobile-friendly, responsive layout.', $score = $boolScore($hasViewport, 95, 55),
            'Viewport meta and fluid layout signal mobile-friendliness.',
            ['Add `<meta name="viewport">`.','Use responsive H2/H3 spacing and fluid media (max-width:100%).'],
            'mobile responsive layout', $score);
        $uxChecks[] = $this->mkCheck('Optimized speed (compression, lazy-load)', $score = max(45, min(95, 60 + $lazyImgCount*5)),
            'Lazy-loading images reduces initial payload; compression/HTTP2 help.',
            ['Use `loading="lazy"` on below-the-fold images.','Enable Brotli/GZip on server.','Serve images in AVIF/WebP.'],
            'page speed optimize lazy load', $score);
        $uxChecks[] = $this->mkCheck('Core Web Vitals passing (LCP/INP/CLS)', $score = max(40, min(95, 70 + ($hasViewport?10:0) + min(10,$lazyImgCount*2) - ($figcaptionCount?0:0))),
            'Good LCP/INP/CLS usually come from fast media + stable layout.',
            ['Set width/height on images to avoid CLS.','Minify CSS/JS; defer non-critical JS.'],
            'improve core web vitals', $score);
        $uxChecks[] = $this->mkCheck('Clear CTAs and next steps', $score = $boolScore($hasCTA, 90, 55),
            'Pages that guide users with clear CTAs convert better and reduce pogo-sticking.',
            ['Place one primary CTA above the fold.','Use action verbs: Get, Start, Download, Contact.'],
            'clear call to action examples', $score);
        $uxChecks[] = $this->mkCheck('Accessible contrast & a11y basics', $score = 70,
            'Contrast and ARIA basics improve readability for all users.',
            ['Check contrast ≥ 4.5:1 for body text.','Add alt text and focus indicators.'],
            'web accessibility contrast basics', $score);
        $uxScore = $this->avgScore($uxChecks);

        // -------- 2) Entities & Context
        $entChecks = [];
        $entChecks[] = $this->mkCheck('Primary entity clearly defined', $score = $boolScore($json['has_main_entity'] || $h1HasKW, 90, 55),
            'A clear primary entity helps disambiguate the topic.',
            ['Define `mainEntity` in JSON-LD or ensure H1 states the topic plainly.'],
            'primary entity mainEntity schema', $score);
        $entChecks[] = $this->mkCheck('Related entities covered with context', $score = max(50, min(95, 55 + ($h2Count*5) + ($links['internal']*2))),
            'Cover subtopics and related concepts to build topical authority.',
            ['Add H2s for common subtopics.','Link to hub/cluster pages.'],
            'topic clusters related entities', $score);
        $entChecks[] = $this->mkCheck('Valid schema markup (Article/FAQ/Product)', $score = $boolScore($schemaCount>0 || $json['has_article'] || $json['has_product'] || $json['has_faq'], 85, 45),
            'Schema improves machine understanding and rich results.',
            ['Add the most specific schema type (Article, Product, FAQPage).'],
            'valid schema markup article product faq', $score);
        $entChecks[] = $this->mkCheck('sameAs/Organization details present', $score = $boolScore($json['has_org_sameas'], 85, 45),
            'Organization `sameAs` ties your entity to official profiles.',
            ['Include Organization schema with `sameAs` to major profiles.'],
            'organization schema sameAs', $score);
        $entChecks[] = $this->mkCheck('Main entity or about defined', $score = $boolScore($json['has_main_entity'], 85, 50),
            'Use `mainEntity`/`about` to define the canonical topic.',
            ['Add `mainEntity` to Article or WebPage JSON-LD.'],
            'mainEntity about schema', $score);
        $entScore = $this->avgScore($entChecks);

        // -------- 3) Structure & Architecture
        $archChecks = [];
        $archChecks[] = $this->mkCheck('Logical H2/H3 headings & topic clusters', $score = max(45, min(95, 50 + $h2Count*4 + $h3Count*2)),
            'Hierarchical headings help both readers and crawlers.',
            ['Use H2 for sections; H3 for subsections. Avoid skipping levels.'],
            'logical heading hierarchy seo', $score);
        $archChecks[] = $this->mkCheck('Internal links to hub/related pages', $score = max(40, min(95, 50 + $links['internal']*4)),
            'Internal links distribute PageRank and connect clusters.',
            ['Add 3–5 links to pillar/hub pages.'],
            'internal linking best practices', $score);
        $archChecks[] = $this->mkCheck('Clean, descriptive URL slug', $score = $this->scoreSlug($url),
            'Short, hyphenated slugs are easier to parse and share.',
            ['Avoid stop words, remove tracking params.','Use lowercase and hyphens.'],
            'clean descriptive url slug', $score);
        $archChecks[] = $this->mkCheck('Breadcrumbs enabled (+ schema)', $score = $boolScore($json['has_breadcrumbs'], 85, 55),
            'Breadcrumbs improve navigation and rich results.',
            ['Add visible breadcrumbs + `BreadcrumbList` schema.'],
            'breadcrumbs schema markup', $score);
        $archChecks[] = $this->mkCheck('Sitemap/Navigation discoverable', $score = 70,
            'Clear nav + sitemap help discovery across the site.',
            ['Link to `/sitemap.xml` in robots.txt; expose main nav to bots.'],
            'xml sitemap best practices', $score);
        $archScore = $this->avgScore($archChecks);

        // -------- 4) Content Quality
        $cqChecks = [];
        $cqChecks[] = $this->mkCheck('E-E-A-T signals (author, date, expertise)', $score = max(50, min(95, 55 + ($json['has_author']?15:0) + ($json['has_date_published']?15:0))),
            'Visible author and updated date are EEAT hints.',
            ['Add author bio; show “last updated”.'],
            'E-E-A-T page author updated', $score);
        $cqChecks[] = $this->mkCheck('Unique value vs. top competitors', $score = max(40, min(95, 45 + (int)min(30, mb_strlen($mainText ?? '', 'UTF-8')/1200) + $links['external']*3)),
            'Original research, examples, or data stand out.',
            ['Add examples, screenshots, tables or short case studies.'],
            'how to write unique value content', $score);
        $cqChecks[] = $this->mkCheck('Facts & citations up to date', $score = max(40, min(95, 50 + $links['external']*8 + $this->recentYearBoost($mainText ?? ''))),
            'Cite trustworthy sources; refresh yearly stats.',
            ['Link to docs/standards/research. Update stats within the last 2–3 years.'],
            'fact checking cite sources content', $score);
        $cqChecks[] = $this->mkCheck('Helpful media with captions', $score = max(40, min(95, 50 + min(30,$imagesAlt*5) + min(15,$figcaptionCount*5))),
            'Images/videos with captions improve comprehension.',
            ['Add `alt` text; use `<figure><figcaption>` for key visuals.'],
            'image captions seo accessibility', $score);
        $cqChecks[] = $this->mkCheck('Clear “last updated” or freshness', $score = $boolScore($json['has_date_published'], 85, 55),
            'Freshness helps YMYL queries and newsy topics.',
            ['Expose `dateModified` in Article/WebPage schema.'],
            'add dateModified schema', $score);
        $cqScore = $this->avgScore($cqChecks);

        // -------- 5) Content & Keywords
        $ckChecks = [];
        $intentDefined = ($contentLen >= 40) || $kw !== '';
        $ckChecks[] = $this->mkCheck('Define search intent & primary topic', $score = $boolScore($intentDefined, 85, 55),
            'Make the topic and intent obvious in the opening.',
            ['State the problem/benefit in the first 1–2 sentences.'],
            'search intent primary topic', $score);
        $mapRelated = ($h2Count + $h3Count) >= 4;
        $ckChecks[] = $this->mkCheck('Map target & related keywords (synonyms/PAA)', $score = $boolScore($mapRelated, 85, 55),
            'Cover PAA and semantically related queries with sections.',
            ['Add subsections for common PAA. Use synonyms naturally.'],
            'map related keywords PAA content', $score);
        $ckChecks[] = $this->mkCheck('H1 includes primary topic naturally', $score = $boolScore($h1HasKW || ($kw==='' && $hasH1), 90, 50),
            'Strong H1 sets topical focus.',
            ['Keep H1 concise (40–60 chars). Include keyword naturally.'],
            'H1 best practices include keyword', $score);
        $faqPresent = $json['has_faq'] || $this->hasFAQByText($headings);
        $ckChecks[] = $this->mkCheck('Integrate FAQs / questions with answers', $score = $boolScore($faqPresent, 85, 55),
            'FAQs win long-tail and can earn rich results.',
            ['Add 3–5 Q/As or `FAQPage` schema.'],
            'FAQPage schema rich results', $score);
        $readable = (int) $readability['score'];
        $ckChecks[] = $this->mkCheck('Readable, NLP-friendly language', $score = $readable,
            'Short sentences and simple words improve comprehension.',
            ['Aim for Grade 7–9. Shorten sentences; reduce passive voice.'],
            'improve readability online content', $score);
        $ckScore = $this->avgScore($ckChecks);

        // -------- 6) Technical Elements
        $teChecks = [];
        $titleHasKW = ($kw !== '') ? Str::contains(Str::lower($title), Str::lower($kw)) : (mb_strlen($title,'UTF-8') > 0);
        $teChecks[] = $this->mkCheck('Title tag (≈50–60 chars) w/ primary keyword', $score = $this->scoreTitle($title, $titleHasKW),
            'Titles drive CTR and clarity; include the primary term once.',
            ['Keep 50–60 chars; place keyword early; avoid truncation.'],
            'title tag best length include keyword', $score);
        $metaLen = mb_strlen($meta,'UTF-8');
        $metaGood = $metaLen >= 140 && $metaLen <= 170 && $this->containsAny(Str::lower($meta), ['learn','discover','get','shop','book','download','try','contact']);
        $teChecks[] = $this->mkCheck('Meta description (≈140–160 chars) + CTA', $score = $boolScore($metaGood, 85, 55),
            'Compelling meta with a light CTA improves CTR.',
            ['Use benefit + action verb. Keep ≈155 chars.'],
            'write good meta description CTA', $score);
        $noIndex  = Str::contains($robots, 'noindex');
        $teChecks[] = $this->mkCheck('Canonical tag set correctly', $score = $boolScore(!empty($canonical), 95, 55),
            'Canonical prevents duplicate content issues.',
            ['Add `<link rel="canonical">` to preferred URL.'],
            'canonical tag best practices', $score);
        $teChecks[] = $this->mkCheck('Indexable & listed in XML sitemap', $score = $boolScore(!$noIndex, 85, 45),
            'Avoid `noindex` unless needed. Ensure presence in sitemap.',
            ['Remove `noindex`; add to `sitemap.xml`.'],
            'indexable xml sitemap', $score);
        $teChecks[] = $this->mkCheck('OpenGraph / Twitter cards present', $score = $boolScore($hasOgOrTwitter, 85, 55),
            'Social cards improve sharing previews and CTR.',
            ['Add `og:title`, `og:description`, `twitter:card`.'],
            'open graph twitter card meta', $score);
        $teScore = $this->avgScore($teChecks);

        return [
            ['name'=>'User Signals & Experience','icon'=>'👤','score'=>$uxScore,'checks'=>$uxChecks],
            ['name'=>'Entities & Context','icon'=>'🗃️','score'=>$entScore,'checks'=>$entChecks],
            ['name'=>'Structure & Architecture','icon'=>'🧩','score'=>$archScore,'checks'=>$archChecks],
            ['name'=>'Content Quality','icon'=>'✨','score'=>$cqScore,'checks'=>$cqChecks],
            ['name'=>'Content & Keywords','icon'=>'✍️','score'=>$ckScore,'checks'=>$ckChecks],
            ['name'=>'Technical Elements','icon'=>'</>','score'=>$teScore,'checks'=>$teChecks],
        ];
    }

    private function mkCheck(string $label, int $score, string $why, array $tips, string $search, int $s): array
    {
        $band = $s>=80?'green':($s>=60?'orange':'red');
        return [
            'label'  => $label,
            'score'  => $s,
            'color'  => $band,
            'why'    => $why,
            'tips'   => $tips,
            'improve_search_url' => $this->searchLink($search),
        ];
    }

    private function avgScore(array $checks): int
    {
        if (!$checks) return 0;
        $sum=0; foreach ($checks as $c) $sum += (int)$c['score'];
        return (int) round($sum / count($checks));
    }

    private function containsAny(string $hay, array $needles): bool
    {
        $hay = Str::lower($hay);
        foreach ($needles as $n) if ($n!=='' && Str::contains($hay, Str::lower($n))) return true;
        return false;
    }

    private function scoreSlug(string $url): int
    {
        $path = Str::lower((string) parse_url($url, PHP_URL_PATH));
        if ($path === '' || $path === '/' || $path === null) return 70;
        $clean = preg_match('#^[a-z0-9\-/]+$#', $path) && !Str::contains($path, ['--','//',' ']);
        $len   = strlen(trim($path, '/'));
        $short = $len <= 60;
        return ($clean ? 85 : 50) + ($short ? 10 : 0);
    }

    private function scoreTitle(string $title, bool $hasKw): int
    {
        $len = mb_strlen($title,'UTF-8');
        $lenGood = $len >= 45 && $len <= 65;
        return ($lenGood ? 85 : 60) + ($hasKw ? 10 : -5);
    }

    private function hasFAQByText(array $headings): bool
    {
        $q = implode(' ', array_merge($headings['H2'] ?? [], $headings['H3'] ?? []));
        return $this->containsAny($q, ['faq','frequently asked','question','how do i','what is','how to']);
    }

    private function recentYearBoost(string $text): int
    {
        preg_match_all('/\b(20[0-9]{2})\b/', $text, $m);
        $yrs = array_map('intval', $m[1] ?? []);
        $max = $yrs ? max($yrs) : 0;
        $current = (int) date('Y');
        if ($max >= $current-1) return 20;
        if ($max >= $current-3) return 10;
        return 0;
    }

    /* ===========================================================
     | Scoring & recommendations
     * ===========================================================*/
    private function buildRecommendations(array $links, int $imagesAlt, int $schemaCount, array $headings, array $readability, string $kw): array
    {
        $recs = [];
        if ($schemaCount < 1) {
            $recs[] = ['severity' => 'Info','text' => 'Add JSON-LD structured data (e.g., FAQPage, Article, Breadcrumb) for richer SERP features.'];
        }
        if ($imagesAlt < 1) {
            $recs[] = ['severity' => 'Warning','text' => 'Add descriptive alt text to key images to improve accessibility and SEO.'];
        }
        if ($links['internal'] < 3) {
            $recs[] = ['severity' => 'Warning','text' => 'Add 3–5 internal links to related pillar/hub pages to strengthen topical authority.'];
        }
        if (($readability['score'] ?? 0) < 60) {
            $recs[] = ['severity' => 'Critical','text' => 'Improve readability: shorten sentences, reduce passive voice, and simplify word choices.'];
        }
        if ($kw !== '' && !$this->keywordPresentInHeadings($headings, $kw)) {
            $recs[] = ['severity' => 'Info','text' => 'Include the target keyword (or a close variant) in one or two headings where natural.'];
        }
        return $recs;
    }

    private function keywordPresentInHeadings(array $headings, string $kw): bool
    {
        $needle = Str::lower($kw);
        foreach ($headings as $arr) {
            foreach ($arr as $h) if (Str::contains(Str::lower($h), $needle)) return true;
        }
        return false;
    }

    private function computeOverallScore(array $categories, array $readability): int
    {
        $map = [];
        foreach ($categories as $c) { $map[$c['name']] = (int)$c['score']; }

        $score =
              0.22 * ($map['Content & Keywords']      ?? 0)
            + 0.18 * ($map['Content Quality']          ?? 0)
            + 0.18 * ($map['Technical Elements']       ?? 0)
            + 0.14 * ($map['Structure & Architecture'] ?? 0)
            + 0.14 * ($map['Entities & Context']       ?? 0)
            + 0.14 * ($map['User Signals & Experience']?? 0);

        $score = 0.9 * $score + 0.1 * ((int)$readability['score'] ?? 0);

        return (int) round(max(0, min(100, $score)));
    }

    private function wheelLabel(int $score): string
    {
        if ($score >= 80) return 'Great Work — Well Optimized';
        if ($score >= 60) return 'Needs Optimization';
        return 'Needs Significant Optimization';
    }

    private function countSchemaBlocks(\DOMDocument $dom): int
    {
        $xp = new \DOMXPath($dom);
        $jsonLd = $this->xpLen($xp, '//script[@type="application/ld+json"]');
        $micro  = $this->xpLen($xp, '//*[@itemscope]');
        return $jsonLd + $micro;
    }

    private function searchLink(string $q): string
    {
        return 'https://www.google.com/search?q=' . rawurlencode($q);
    }

    /* ===========================================================
     | PageSpeed Insights (server proxy)
     * ===========================================================*/
    private function callPsiOnce(string $endpoint, string $key, int $timeout, string $url, string $strategy = 'mobile'): array
    {
        $strategy = strtolower($strategy) === 'desktop' ? 'desktop' : 'mobile'; // enforce valid values

        $params = [
            'url'      => $url,
            'key'      => $key,
            'strategy' => $strategy,     // 'mobile' | 'desktop'
            'category' => 'performance',
        ];

        $res = Http::timeout($timeout)->retry(2, 250)->get($endpoint, $params);
        if (!$res->ok()) {
            return ['ok' => false, 'status' => $res->status(), 'error' => 'HTTP '.$res->status()];
        }
        $json = $res->json() ?: [];

        // Extract metrics
        $lr     = $json['lighthouseResult'] ?? [];
        $audits = $lr['audits'] ?? [];

        // Performance score 0–1 -> 0–100
        $perfScore = isset($lr['categories']['performance']['score'])
            ? (int) round(($lr['categories']['performance']['score'] ?? 0) * 100)
            : null;

        // LCP seconds
        $lcpMs = $audits['largest-contentful-paint']['numericValue'] ?? null;
        $lcp   = is_numeric($lcpMs) ? round($lcpMs / 1000, 2) : null;

        // CLS unitless
        $cls = $audits['cumulative-layout-shift']['numericValue'] ?? null;
        if (is_numeric($cls)) $cls = round((float)$cls, 3);

        // INP ms (new id) with fallback to experimental id
        $inpMs = $audits['interaction-to-next-paint']['numericValue']
            ?? ($audits['experimental-interaction-to-next-paint']['numericValue'] ?? null);
        $inp = is_numeric($inpMs) ? (int) round($inpMs) : null;

        // TTFB ms (server-response-time first, then time-to-first-byte)
        $ttfbMs = $audits['server-response-time']['numericValue']
            ?? ($audits['time-to-first-byte']['numericValue'] ?? null);
        $ttfb = is_numeric($ttfbMs) ? (int) round($ttfbMs) : null;

        return [
            'ok'     => true,
            'score'  => $perfScore, // 0–100
            'lcp'    => $lcp,       // seconds
            'cls'    => $cls,       // unitless (0–1+)
            'inp'    => $inp,       // ms
            'ttfb'   => $ttfb,      // ms
        ];
    }
}

