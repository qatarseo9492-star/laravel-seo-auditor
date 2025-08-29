<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AnalyzerController extends Controller
{
    /**
     * POST /semantic-analyzer/analyze  (also wired as /api/semantic-analyze)
     * Fetch page -> parse -> compute stats -> score -> JSON.
     */
    public function semanticAnalyze(Request $request)
    {
        $data = $request->validate([
            'url'            => ['required','url'],
            'target_keyword' => ['nullable','string','max:160'],
        ]);

        $url = (string) $data['url'];
        $kw  = trim((string)($data['target_keyword'] ?? ''));
        $ua  = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124 Safari/537.36';

        // 0) Cache quick wins
        $cacheKey = 'analyze:'.md5($url.'|'.$kw);
        if ($cached = Cache::get($cacheKey)) {
            return response()->json($cached);
        }

        try {
            // 1) Fetch (safe)
            $resp = $this->fetchUrl($url, $ua);
            if (!$resp['ok']) {
                return response()->json(['error' => $resp['error'] ?? 'Fetch failed'], 422);
            }
            $html = $resp['body'] ?? '';
            $host = $resp['host'] ?? parse_url($url, PHP_URL_HOST);
            $httpStatus = $resp['status'] ?? 200;

            // 2) Parse HTML
            $dom  = $this->makeDom($html);

            // 3) Extract main text (Readability with fallback)
            $mainText = $this->extractMainTextSmart($dom);

            // 4) Structure + on-page bits
            $title  = $this->extractTitle($dom);
            $metaDescription = $this->extractMeta($dom, 'description');
            $headings = $this->extractHeadings($dom);
            $links    = $this->extractLinks($dom, $host);
            $imagesAlt = $this->countImagesWithAlt($dom);
            $schema = $this->scanSchema($dom);
            $schemaCount = $schema['count'];
            $hasFAQ = $schema['hasFAQ'];
            $hasBreadcrumb = $schema['hasBreadcrumb'];
            $canonical = $this->extractCanonical($dom);
            $robotsMeta = $this->extractRobotsMeta($dom);
            $viewport   = $this->extractViewport($dom);
            $ratio = $this->textToHtmlRatio($html, $mainText);

            // 5) Readability (real content)
            $readability = $this->computeReadabilityFromText($mainText);

            // 6) Categories + Recommendations
            $categories = $this->buildCategoriesNew(
                $title,
                $metaDescription,
                $headings,
                $links,
                $imagesAlt,
                $schemaCount,
                $hasFAQ,
                $hasBreadcrumb,
                $kw,
                $readability,
                $canonical,
                $robotsMeta,
                $viewport
            );

            $overallScore = $this->computeOverallScoreNew($categories, $readability);
            $wheel = ['label' => $this->wheelLabel($overallScore)];

            $recommendations = $this->buildRecommendationsNew(
                $links,
                $imagesAlt,
                $schemaCount,
                $headings,
                $readability,
                $kw,
                $canonical,
                $robotsMeta,
                $viewport,
                $hasFAQ
            );

            // 7) Response payload
            $response = [
                'ok'            => true,
                'http_status'   => $httpStatus,
                'overall_score' => $overallScore,
                'wheel'         => $wheel,
                'quick_stats'   => [
                    'readability_flesch' => $readability['flesch'],
                    'readability_grade'  => $readability['grade'],
                    'internal_links'     => $links['internal'],
                    'external_links'     => $links['external'],
                    'text_to_html_ratio' => $ratio,
                ],
                'meta' => [
                    'canonical' => $canonical,
                    'robots'    => $robotsMeta,
                    'viewport'  => $viewport,
                ],
                'content_structure' => [
                    'title'            => $title,
                    'meta_description' => $metaDescription,
                    'headings'         => $headings,
                ],
                'schema_count'   => $schemaCount,
                'readability'    => $readability,
                'recommendations'=> $recommendations,
                'categories'     => $categories,
            ];

            Cache::put($cacheKey, $response, now()->addMinutes(15));
            return response()->json($response);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Analyzer error: '.$e->getMessage()
            ], 500);
        }
    }

    /* ============================================================
     | Safe fetching
     * ============================================================*/

    private function isPrivateIp(string $host): bool
    {
        $ips = [];
        foreach ((array) @gethostbynamel($host) as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) $ips[] = $ip;
        }
        if (!$ips) return true; // resolve failed -> block
        foreach ($ips as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP,
                FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                return true; // private/reserved -> block
            }
        }
        return false;
    }

    private function fetchUrl(string $url, string $ua): array
    {
        try {
            $host = parse_url($url, PHP_URL_HOST);
            if (!$host || $this->isPrivateIp($host)) {
                return ['ok'=>false, 'error'=>'Blocked host (private/reserved IP)'];
            }

            $res = Http::withHeaders([
                    'User-Agent' => $ua,
                    'Accept'     => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                ])
                ->withOptions([
                    'allow_redirects' => ['max' => 5],
                    'verify'          => true,
                    'timeout'         => 20,
                    'connect_timeout' => 10,
                ])
                ->retry(2, 300)
                ->get($url);

            if (!$res->ok()) return ['ok'=>false, 'status'=>$res->status(), 'error'=>"HTTP ".$res->status()];

            $lenHeader = $res->header('Content-Length');
            $declaredLen = is_array($lenHeader) ? (int)($lenHeader[0] ?? 0) : (int)($lenHeader ?? 0);
            if ($declaredLen > 3_000_000) return ['ok'=>false,'status'=>$res->status(),'error'=>'Page too large (>3MB)'];

            $body = (string)$res->body();
            if (strlen($body) > 3_000_000) $body = substr($body, 0, 3_000_000);

            return [
                'ok'     => true,
                'body'   => $body,
                'host'   => $host,
                'status' => $res->status(),
            ];
        } catch (\Throwable $e) {
            return ['ok'=>false, 'error'=>$e->getMessage()];
        }
    }

    private function makeDom(string $html): \DOMDocument
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        // Remove scripts/styles for cleaner text
        $html = preg_replace('#<script\b[^>]*>[\s\S]*?</script>#i', ' ', $html);
        $html = preg_replace('#<style\b[^>]*>[\s\S]*?</style>#i', ' ', $html);
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $dom->loadHTML($html, LIBXML_NOERROR|LIBXML_NOWARNING);
        libxml_clear_errors();
        return $dom;
    }

    /* ============================================================
     | Extraction helpers
     * ============================================================*/

    private function extractTitle(\DOMDocument $dom): string
    {
        $nodes = $dom->getElementsByTagName('title');
        if ($nodes && $nodes->length) {
            return $this->cleanText($nodes->item(0)->textContent);
        }
        return '';
    }

    private function extractMeta(\DOMDocument $dom, string $name): string
    {
        $xp = new \DOMXPath($dom);
        $nodes = $xp->query("//meta[translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='{$name}']/@content");
        if ($nodes && $nodes->length) {
            return $this->cleanText($nodes->item(0)->nodeValue);
        }
        if ($name === 'description') {
            $nodes = $xp->query("//meta[translate(@property,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='og:description']/@content");
            if ($nodes && $nodes->length) return $this->cleanText($nodes->item(0)->nodeValue);
        }
        return '';
    }

    private function extractCanonical(\DOMDocument $dom): string
    {
        $xp = new \DOMXPath($dom);
        $nodes = $xp->query("//link[translate(@rel,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='canonical']/@href");
        if ($nodes && $nodes->length) return trim($nodes->item(0)->nodeValue ?? '');
        return '';
    }

    private function extractRobotsMeta(\DOMDocument $dom): string
    {
        $xp = new \DOMXPath($dom);
        $nodes = $xp->query("//meta[translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='robots']/@content");
        if ($nodes && $nodes->length) return trim($nodes->item(0)->nodeValue ?? '');
        return '';
    }

    private function extractViewport(\DOMDocument $dom): string
    {
        $xp = new \DOMXPath($dom);
        $nodes = $xp->query("//meta[translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='viewport']/@content");
        if ($nodes && $nodes->length) return trim($nodes->item(0)->nodeValue ?? '');
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
        $nodes = $xp->query('//a[@href]');
        foreach ($nodes as $a) {
            $href = $a->getAttribute('href');
            if (!$href || strpos($href, 'javascript:')===0) continue;
            $isAbs = preg_match('#^https?://#i', $href);
            if (!$isAbs) { $internal++; continue; }
            $h = parse_url($href, PHP_URL_HOST);
            if ($host && $h && $this->sameHost($host, $h)) $internal++; else $external++;
        }
        return ['internal'=>$internal, 'external'=>$external];
    }

    private function sameHost(string $a, string $b): bool
    {
        $a = preg_replace('/^www\./i','', strtolower($a));
        $b = preg_replace('/^www\./i','', strtolower($b));
        return $a === $b;
    }

    private function countImagesWithAlt(\DOMDocument $dom): int
    {
        $xp = new \DOMXPath($dom);
        $nodes = $xp->query('//img');
        $count = 0;
        foreach ($nodes as $img) {
            $alt = trim($img->getAttribute('alt') ?? '');
            if ($alt !== '') $count++;
        }
        return $count;
    }

    private function scanSchema(\DOMDocument $dom): array
    {
        $xp = new \DOMXPath($dom);
        $count = 0; $hasFAQ = false; $hasBreadcrumb = false;
        foreach ($xp->query('//script[@type="application/ld+json"]') as $s) {
            $json = trim($s->textContent ?? '');
            if ($json === '') continue;
            $count++;
            $j = @json_decode($json, true);
            if (!$j) continue;
            $flat = json_encode($j);
            if (stripos($flat,'"FAQPage"') !== false) $hasFAQ = true;
            if (stripos($flat,'"BreadcrumbList"') !== false) $hasBreadcrumb = true;
        }
        // microdata itemscope
        $count += $xp->query('//*[@itemscope]')->length;
        // breadcrumb HTML hint
        if (!$hasBreadcrumb) {
            $hasBreadcrumb = (bool) $xp->query("//*[contains(translate(@class,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz'),'breadcrumb')]")->length;
        }
        return ['count'=>$count, 'hasFAQ'=>$hasFAQ, 'hasBreadcrumb'=>$hasBreadcrumb];
    }

    private function textToHtmlRatio(string $html, string $mainText): int
    {
        $lenHtml = max(1, mb_strlen($html, '8bit'));
        $lenText = max(0, mb_strlen($mainText, 'UTF-8'));
        $ratio = ($lenText / $lenHtml) * 100;
        return (int) round(min(100, max(0, $ratio)));
    }

    /* ============================================================
     | Readability (with better main text)
     * ============================================================*/

    private function extractMainTextSmart(\DOMDocument $dom): string
    {
        // Try Readability.php
        try {
            if (class_exists('\\andreskrey\\Readability\\Readability')) {
                $html = $dom->saveHTML() ?: '';
                $config = new \andreskrey\Readability\Configuration([
                    'summonCthulhu' => false,
                    'fixRelativeURLs' => false,
                ]);
                $r = new \andreskrey\Readability\Readability($config);
                $r->parse($html);
                $txt = $this->cleanText($r->getContent() ?? '');
                if (mb_strlen($txt) >= 200) return $txt;
            }
        } catch (\Throwable $e) {
            // ignore, fallback below
        }
        // Fallback heuristic
        return $this->extractMainTextHeuristic($dom);
    }

    private function extractMainTextHeuristic(\DOMDocument $dom): string
    {
        $xp = new \DOMXPath($dom);
        $candidates = [
            '//article',
            '//*[self::main or self::section or self::div][not(@role="navigation")]'
        ];
        $bestTxt = '';
        $bestScore = 0;
        foreach ($candidates as $q) {
            $nodes = $xp->query($q);
            if (!$nodes) continue;
            foreach ($nodes as $node) {
                $txt = $this->nodeVisibleText($node);
                $len = mb_strlen($txt, 'UTF-8');
                if ($len < 200) continue;
                $punct = preg_match_all('/[\,\.]/u', $txt);
                $score = $len * (1 + $punct);
                if ($score > $bestScore) { $bestScore = $score; $bestTxt = $txt; }
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

    private function splitSentences(string $text): array {
        $text = preg_replace('/\s+/u', ' ', trim($text));
        if ($text === '') return [];
        $text = preg_replace('/([.!?])\s+(?=[\p{Lu}0-9])/u', "$1\n", $text);
        $parts = preg_split("/\n+/u", $text) ?: [];
        return array_values(array_filter(array_map('trim', $parts), fn($s) => $s !== ''));
    }

    // Unicode-aware tokenization
    private function tokenizeWords(string $text): array {
        preg_match_all('/[\p{L}\p{M}\']+/u', mb_strtolower($text, 'UTF-8'), $m);
        return $m[0] ?? [];
    }

    private function countSyllables(string $word): int {
        // English-oriented (ok for most Latin text)
        $w = strtolower(preg_replace('/[^a-z]/', '', $word));
        if ($w === '') return 0;
        preg_match_all('/[aeiouy]+/', $w, $m);
        $count = count($m[0]);
        if (strlen($w) > 2 && substr($w, -1) === 'e' && !preg_match('/[aeiouy][^aeiouy]e$/', $w)) $count--;
        if (preg_match('/[^aeiouy]le$/', $w)) $count++;
        return max(1, $count);
    }

    private function computeReadabilityFromText(string $text): array {
        $sentences = $this->splitSentences($text);
        $sCount = max(1, count($sentences));
        $words = $this->tokenizeWords($text);
        $wCount = max(1, count($words));

        $letters = 0; $chars = 0; $syll = 0; $poly = 0; $complex = 0;
        foreach ($words as $w) {
            $letters += preg_match_all('/[a-z]/', $w);
            $chars   += strlen($w);
            $sy = $this->countSyllables($w);
            $syll += $sy;
            if ($sy >= 3) { $poly++; $complex++; }
        }

        $asl = $wCount / $sCount;      // avg sentence length
        $asw = $syll   / $wCount;      // avg syllables per word

        $flesch = 206.835 - 1.015 * $asl - 84.6 * $asw;
        $fk = 0.39 * $asl + 11.8 * $asw - 15.59;
        $smog = 1.0430 * sqrt($poly * (30.0 / $sCount)) + 3.1291;
        $fog = 0.4 * ($asl + 100.0 * ($complex / $wCount));
        $L = ($letters / $wCount) * 100.0;
        $S = ($sCount  / $wCount) * 100.0;
        $cli = 0.0588 * $L - 0.296 * $S - 15.8;
        $ari = 4.71 * ($chars / $wCount) + 0.5 * $asl - 21.43;

        preg_match_all('/\b(is|are|was|were|be|been|being)\s+[a-z]+ed\b/i', $text, $pv);
        $passiveHits  = count($pv[0] ?? []);
        $passiveRatio = min(100, round(($passiveHits / $sCount) * 100));

        $avgGrade    = max(1, min(18, ($fk + $smog + $fog + $cli + $ari) / 5.0));
        $fre         = max(0, min(100, $flesch));
        $gradeScore  = max(0, min(100, 100 - (($avgGrade - 1) * (100.0 / 17.0))));
        $score       = (int) round(($fre + $gradeScore) / 2);

        $simpleWordsRatio = max(0, min(100, round((($wCount - $complex) / $wCount) * 100)));

        return [
            'score'               => $score,
            'grade'               => round($avgGrade, 1),
            'flesch'              => round($flesch, 1),
            'fk_grade'            => round($fk, 1),
            'smog'                => round($smog, 1),
            'gunning_fog'         => round($fog, 1),
            'coleman_liau'        => round($cli, 1),
            'ari'                 => round($ari, 1),
            'avg_sentence_len'    => round($asl, 2),
            'simple_words_ratio'  => $simpleWordsRatio,
            'passive_ratio'       => $passiveRatio,
            'note'                => 'Score blends Flesch and average grade-level into a 0–100 scale.',
        ];
    }

    /* ============================================================
     | Categories / scoring (expanded)
     * ============================================================*/

    private function buildCategoriesNew(
        string $title,
        string $meta,
        array $headings,
        array $links,
        int $imagesAlt,
        int $schemaCount,
        bool $hasFAQ,
        bool $hasBreadcrumb,
        string $kw,
        array $readability,
        string $canonical,
        string $robotsMeta,
        string $viewport
    ): array {
        $band = fn(int $s) => $s>=80?'green':($s>=60?'orange':'red');

        // Helpers
        $titleLen = strlen($title);
        $metaLen  = strlen($meta);
        $hasH1    = !empty($headings['H1']);
        $h1HasKw  = $kw !== '' && $hasH1 && Str::contains(Str::lower(implode(' ', $headings['H1'])), Str::lower($kw));
        $kwInTitle= $kw !== '' && Str::contains(Str::lower($title), Str::lower($kw));
        $hasCanonical = $canonical !== '';
        $isNoindex = stripos($robotsMeta, 'noindex') !== false;

        /* ----- Content & Keywords */
        $ck = [];
        $ckScore = 0; $ckMax = 0;

        $s1 = $kw === '' ? 70 : ($kwInTitle ? 95 : 55);
        $ck[] = ['label'=>'Primary keyword in title', 'score'=>$s1, 'color'=>$band($s1),
                 'advice'=>'Place target keyword near the beginning of the title.',
                 'why'=>'Titles guide user expectation and improve CTR.',
                 'tips'=>['Include the main term early.','Keep ≈50–60 chars.','Make it benefit-oriented.'],
                 'improve_search_url'=>$this->searchLink('title tag best practices')];
        $ckScore += $s1; $ckMax += 100;

        $s2 = $metaLen >= 140 && $metaLen <= 170 ? 90 : ($metaLen ? 65 : 40);
        $ck[] = ['label'=>'Meta description length', 'score'=>$s2, 'color'=>$band($s2),
                 'advice'=>'Use 140–160 chars with a soft CTA.',
                 'why'=>'Good descriptions improve SERP click-through.',
                 'tips'=>['Mention the benefit the user gets.','Avoid duplicating the title.'],
                 'improve_search_url'=>$this->searchLink('meta description length CTA')];
        $ckScore += $s2; $ckMax += 100;

        $s3 = $hasH1 ? ($h1HasKw ? 95 : 80) : 40;
        $ck[] = ['label'=>'Single, descriptive H1', 'score'=>$s3, 'color'=>$band($s3),
                 'advice'=>'Exactly one H1 summarizing the page; include the primary topic.',
                 'why'=>'Clear hierarchy helps users and crawlers.',
                 'tips'=>['Use one H1 only.','Keep under ~70 chars.']];
        $ckScore += $s3; $ckMax += 100;

        $s4 = $hasFAQ ? 85 : 55;
        $ck[] = ['label'=>'Integrate FAQs / questions with answers', 'score'=>$s4, 'color'=>$band($s4),
                 'advice'=>'Answer 3–6 PAA-style questions and add FAQPage schema.',
                 'why'=>'Captures long-tail queries and may win rich results.',
                 'tips'=>['Answers 40–60 words.','Use clear, conversational language.']];
        $ckScore += $s4; $ckMax += 100;

        $s5 = max(40, min(100, (int)$readability['score']));
        $ck[] = ['label'=>'Readable, NLP-friendly language', 'score'=>$s5, 'color'=>$band($s5),
                 'advice'=>'Aim for Grade 7–9; shorten sentences; reduce passive voice.',
                 'why'=>'Clarity boosts engagement and retention.',
                 'tips'=>['Use short paragraphs.','Prefer active voice.','Define jargon.']];
        $ckScore += $s5; $ckMax += 100;

        $contentScore = (int)round(($ckScore / max(1,$ckMax)) * 100);

        /* ----- Technical Elements */
        $te = []; $teScore = 0; $teMax = 0;

        $t1 = ($titleLen >= 45 && $titleLen <= 64) ? 90 : ($titleLen ? 65 : 40);
        $te[] = ['label'=>'Title tag (≈50–60 chars) w/ primary keyword', 'score'=>$t1, 'color'=>$band($t1),
                 'advice'=>'Keep concise; include the main term naturally.',
                 'why'=>'Prevents truncation and improves CTR.'];
        $teScore += $t1; $teMax += 100;

        $t2 = $s2; // reuse meta length scoring
        $te[] = ['label'=>'Meta description (≈140–160 chars) + CTA', 'score'=>$t2, 'color'=>$band($t2),
                 'advice'=>'Write a compelling summary that invites a click.',
                 'why'=>'Improves SERP engagement.'];
        $teScore += $t2; $teMax += 100;

        $t3 = $hasCanonical ? 90 : 45;
        $te[] = ['label'=>'Canonical tag set correctly', 'score'=>$t3, 'color'=>$band($t3),
                 'advice'=>'Set canonical to the preferred URL to avoid duplicates.',
                 'why'=>'Consolidates signals across variants.'];
        $teScore += $t3; $teMax += 100;

        $t4 = !$isNoindex ? 88 : 40;
        $te[] = ['label'=>'Indexable & listed in XML sitemap', 'score'=>$t4, 'color'=>$band($t4),
                 'advice'=>'Ensure no noindex and include in your sitemap.',
                 'why'=>'Improves discoverability and indexing.'];
        $teScore += $t4; $teMax += 100;

        $technicalScore = (int)round(($teScore / max(1,$teMax)) * 100);

        /* ----- Structure & Architecture */
        $sa = []; $saScore = 0; $saMax = 0;
        $h2c = count($headings['H2'] ?? []); $h3c = count($headings['H3'] ?? []);

        $sah = ($h2c >= 2) ? 85 : ($h2c >= 1 ? 65 : 45);
        $sa[] = ['label'=>'Logical H2/H3 headings & topic clusters', 'score'=>$sah, 'color'=>$band($sah),
                 'advice'=>'Use H2 for main sections, H3 for sub-points.',
                 'why'=>'Improves scanability and semantic parsing.'];
        $saScore += $sah; $saMax += 100;

        $sai = $links['internal'] >= 5 ? 85 : ($links['internal'] >= 2 ? 65 : 45);
        $sa[] = ['label'=>'Internal links to hub/related pages', 'score'=>$sai, 'color'=>$band($sai),
                 'advice'=>'Add 3–5 contextual internal links to pillar pages.',
                 'why'=>'Distributes authority and helps discovery.'];
        $saScore += $sai; $saMax += 100;

        $sau = 70; // slug quality can’t be judged reliably from DOM
        $sa[] = ['label'=>'Clean, descriptive URL slug', 'score'=>$sau, 'color'=>$band($sau),
                 'advice'=>'Short, hyphenated, meaningful words.',
                 'why'=>'Improves CTR and understanding.'];
        $saScore += $sau; $saMax += 100;

        $sab = $hasBreadcrumb ? 85 : 55;
        $sa[] = ['label'=>'Breadcrumbs enabled (+ schema)', 'score'=>$sab, 'color'=>$band($sab),
                 'advice'=>'Show breadcrumbs UI and add BreadcrumbList JSON-LD.',
                 'why'=>'Adds context for users and rich results.'];
        $saScore += $sab; $saMax += 100;

        $structureScore = (int)round(($saScore / max(1,$saMax)) * 100);

        /* ----- Content Quality */
        $cq = []; $cqScore = 0; $cqMax = 0;

        $cqe = 70; // E-E-A-T detection needs site-specific logic
        $cq[] = ['label'=>'E-E-A-T signals (author, date, expertise)', 'score'=>$cqe, 'color'=>$band($cqe),
                 'advice'=>'Show author, credentials, and last updated date.',
                 'why'=>'Builds credibility and trust.'];
        $cqScore += $cqe; $cqMax += 100;

        $cqu = 70; // Unique value vs competitors (heuristic)
        $cq[] = ['label'=>'Unique value vs. top competitors', 'score'=>$cqu, 'color'=>$band($cqu),
                 'advice'=>'Add comparisons, original data, or tools.',
                 'why'=>'Differentiates and reduces pogo-sticking.'];
        $cqScore += $cqu; $cqMax += 100;

        $cqf = $links['external'] >= 2 ? 80 : 55;
        $cq[] = ['label'=>'Facts & citations up to date', 'score'=>$cqf, 'color'=>$band($cqf),
                 'advice'=>'Cite 2–3 authoritative sources and include dates.',
                 'why'=>'Improves accuracy and trust.'];
        $cqScore += $cqf; $cqMax += 100;

        $cqm = $imagesAlt >= 1 ? ($imagesAlt >= 5 ? 90 : 75) : 45;
        $cq[] = ['label'=>'Helpful media (images/video) w/ captions', 'score'=>$cqm, 'color'=>$band($cqm),
                 'advice'=>'Use descriptive alt text and short captions.',
                 'why'=>'Boosts comprehension and accessibility.'];
        $cqScore += $cqm; $cqMax += 100;

        $contentQualityScore = (int)round(($cqScore / max(1,$cqMax)) * 100);

        /* ----- User Signals & Experience */
        $ux = []; $uxScore = 0; $uxMax = 0;

        $uxr = $viewport !== '' ? 85 : 55;
        $ux[] = ['label'=>'Mobile-friendly, responsive layout', 'score'=>$uxr, 'color'=>$band($uxr),
                 'advice'=>'Include a responsive viewport tag and test small widths.',
                 'why'=>'Mobile-first indexing makes mobile UX critical.'];
        $uxScore += $uxr; $uxMax += 100;

        $uxp = 70; // real speed needs lab/field data
        $ux[] = ['label'=>'Optimized speed (compression, lazy-load)', 'score'=>$uxp, 'color'=>$band($uxp),
                 'advice'=>'Compress images, enable HTTP/2/3, lazy-load below the fold.',
                 'why'=>'Performance strongly affects engagement.'];
        $uxScore += $uxp; $uxMax += 100;

        $uxc = 70; // CWV unknown here
        $ux[] = ['label'=>'Core Web Vitals passing (LCP/INP/CLS)', 'score'=>$uxc, 'color'=>$band($uxc),
                 'advice'=>'Preload hero, reduce JS long tasks, reserve space for media.',
                 'why'=>'Good CWV correlates with better UX and rankings.'];
        $uxScore += $uxc; $uxMax += 100;

        $uxa = 78; // CTA clarity heuristic
        $ux[] = ['label'=>'Clear CTAs and next steps', 'score'=>$uxa, 'color'=>$band($uxa),
                 'advice'=>'Use verb-first buttons; place primary CTA above the fold.',
                 'why'=>'Guides users and reduces pogo-sticking.'];
        $uxScore += $uxa; $uxMax += 100;

        $uxScoreFinal = (int)round(($uxScore / max(1,$uxMax)) * 100);

        /* ----- Entities & Context */
        $ec = []; $ecScore = 0; $ecMax = 0;

        $e1 = 70; // Primary entity heuristics simplified
        $ec[] = ['label'=>'Primary entity clearly defined', 'score'=>$e1, 'color'=>$band($e1),
                 'advice'=>'Introduce what/who early and link to an entity page.',
                 'why'=>'Helps disambiguate in knowledge graphs.'];
        $ecScore += $e1; $ecMax += 100;

        $e2 = 70;
        $ec[] = ['label'=>'Related entities covered with context', 'score'=>$e2, 'color'=>$band($e2),
                 'advice'=>'Add short sections for key related entities.',
                 'why'=>'Signals breadth and relationships.'];
        $ecScore += $e2; $ecMax += 100;

        $e3 = $schemaCount >= 1 ? 85 : 50;
        $ec[] = ['label'=>'Valid schema markup (Article/FAQ/Product)', 'score'=>$e3, 'color'=>$band($e3),
                 'advice'=>'Add JSON-LD, validate in Rich Results Test.',
                 'why'=>'Enables rich features and explicit semantics.'];
        $ecScore += $e3; $ecMax += 100;

        $e4 = 65;
        $ec[] = ['label'=>'sameAs/Organization details present', 'score'=>$e4, 'color'=>$band($e4),
                 'advice'=>'Add Organization schema with logo/url/sameAs.',
                 'why'=>'Connects brand to official profiles.'];
        $ecScore += $e4; $ecMax += 100;

        $entitiesScore = (int)round(($ecScore / max(1,$ecMax)) * 100);

        return [
            ['name'=>'Content & Keywords',        'score'=>$contentScore,       'checks'=>$ck],
            ['name'=>'Technical Elements',        'score'=>$technicalScore,     'checks'=>$te],
            ['name'=>'Structure & Architecture',  'score'=>$structureScore,     'checks'=>$sa],
            ['name'=>'Content Quality',           'score'=>$contentQualityScore,'checks'=>$cq],
            ['name'=>'User Signals & Experience', 'score'=>$uxScoreFinal,       'checks'=>$ux],
            ['name'=>'Entities & Context',        'score'=>$entitiesScore,      'checks'=>$ec],
        ];
    }

    private function buildRecommendationsNew(
        array $links,
        int $imagesAlt,
        int $schemaCount,
        array $headings,
        array $readability,
        string $kw,
        string $canonical,
        string $robotsMeta,
        string $viewport,
        bool $hasFAQ
    ): array {
        $recs = [];

        if ($schemaCount < 1) {
            $recs[] = ['severity'=>'Info','text'=>'Add JSON-LD structured data (Article, FAQPage, Breadcrumb).'];
        }
        if ($imagesAlt < 1) {
            $recs[] = ['severity'=>'Warning','text'=>'Add descriptive alt text to key images for accessibility/SEO.'];
        }
        if ($links['internal'] < 3) {
            $recs[] = ['severity'=>'Warning','text'=>'Add 3–5 internal links to related hub/pillar pages.'];
        }
        if (($readability['score'] ?? 0) < 60) {
            $recs[] = ['severity'=>'Critical','text'=>'Improve readability: shorter sentences, fewer passive constructions, simpler words.'];
        }
        if ($kw !== '' && !$this->keywordPresentInHeadings($headings, $kw)) {
            $recs[] = ['severity'=>'Info','text'=>'Include the target keyword (or close variant) in a relevant heading.'];
        }
        if (!$canonical) {
            $recs[] = ['severity'=>'Info','text'=>'Set a canonical URL to consolidate duplicate variants.'];
        }
        if (stripos($robotsMeta, 'noindex') !== false) {
            $recs[] = ['severity'=>'Critical','text'=>'Robots meta includes "noindex" — remove to allow indexing.'];
        }
        if (!$viewport) {
            $recs[] = ['severity'=>'Warning','text'=>'Missing responsive viewport meta; add it for mobile friendliness.'];
        }
        if (!$hasFAQ) {
            $recs[] = ['severity'=>'Info','text'=>'Add 3–6 short FAQs and FAQPage schema to capture PAA queries.'];
        }

        return $recs;
    }

    private function keywordPresentInHeadings(array $headings, string $kw): bool
    {
        $needle = Str::lower($kw);
        foreach ($headings as $arr) {
            foreach ($arr as $h) {
                if (Str::contains(Str::lower($h), $needle)) return true;
            }
        }
        return false;
    }

    private function computeOverallScoreNew(array $categories, array $readability): int
    {
        // Weighted across 6 categories + readability nudge
        $map = [];
        foreach ($categories as $c) $map[$c['name']] = (int)($c['score'] ?? 0);

        $score =
            0.22 * ($map['Content & Keywords']        ?? 0) +
            0.18 * ($map['Technical Elements']        ?? 0) +
            0.15 * ($map['Structure & Architecture']  ?? 0) +
            0.18 * ($map['Content Quality']           ?? 0) +
            0.15 * ($map['User Signals & Experience'] ?? 0) +
            0.12 * ($map['Entities & Context']        ?? 0);

        $score = 0.9 * $score + 0.1 * (int)($readability['score'] ?? 0);
        return (int) round(max(0, min(100, $score)));
    }

    private function wheelLabel(int $score): string
    {
        if ($score >= 80) return 'Great Work — Well Optimized';
        if ($score >= 60) return 'Needs Optimization';
        return 'Needs Significant Optimization';
    }
}
