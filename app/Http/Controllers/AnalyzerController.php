<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class AnalyzerController extends Controller
{
    /**
     * POST: /api/semantic-analyze  (or named web route if you use it)
     */
    public function semanticAnalyze(Request $request)
    {
        $data = $request->validate([
            'url'            => ['required','url'],
            'target_keyword' => ['nullable','string','max:160'],
        ]);

        $url   = (string) $data['url'];
        $kw    = trim((string)($data['target_keyword'] ?? ''));
        $ua    = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124 Safari/537.36';

        try {
            // 1) Fetch
            $resp = $this->fetchUrl($url, $ua);
            if (!$resp['ok']) {
                return response()->json(['ok'=>false,'error'=>$resp['error'] ?? 'Fetch failed'], 422);
            }
            $html = $resp['body'] ?? '';
            $host = $resp['host'] ?? parse_url($url, PHP_URL_HOST);

            // 2) DOM
            $dom  = $this->makeDom($html);

            // 3) Main text (content to score)
            $mainText = $this->extractMainText($dom);

            // 4) Structure & quick stats
            $title           = $this->extractTitle($dom);
            $metaDescription = $this->extractMeta($dom, 'description');
            $headings        = $this->extractHeadings($dom);
            $links           = $this->extractLinks($dom, $host);
            $imagesAlt       = $this->countImagesWithAlt($dom);
            $schemaCount     = $this->countSchemaBlocks($dom);
            $ratio           = $this->textToHtmlRatio($html, $mainText);

            // 5) Readability (real)
            $readability = $this->computeReadabilityFromText($mainText);

            // 6) Categories (FULL list you requested)
            $categories   = $this->buildCategories(
                $title,$metaDescription,$headings,$links,$imagesAlt,$schemaCount,$kw,$readability,$dom,$url,$mainText
            );
            $overallScore = $this->computeOverallScore($categories, $readability);
            $wheel        = ['label' => $this->wheelLabel($overallScore)];
            $recommendations = $this->buildRecommendations($links, $imagesAlt, $schemaCount, $headings, $readability, $kw);

            // 7) Payload
            return response()->json([
                'ok'            => true,
                'overall_score' => $overallScore,
                'wheel'         => $wheel,
                'quick_stats'   => [
                    'readability_flesch' => $readability['flesch'],
                    'readability_grade'  => $readability['grade'],
                    'internal_links'     => $links['internal'],
                    'external_links'     => $links['external'],
                    'text_to_html_ratio' => $ratio,
                ],
                'content_structure' => [
                    'title'            => $title,
                    'meta_description' => $metaDescription,
                    'headings'         => $headings,
                ],
                'readability'     => $readability,
                'recommendations' => $recommendations,
                'categories'      => $categories,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['ok'=>false,'error'=>'Analyzer error: '.$e->getMessage()], 500);
        }
    }

    /* ========================= Fetch & DOM ========================= */

    private function fetchUrl(string $url, string $ua): array
    {
        try {
            $res = Http::withHeaders([
                    'User-Agent' => $ua,
                    'Accept'     => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                ])->timeout(20)->retry(2, 300)->get($url);

            if (!$res->ok()) return ['ok'=>false, 'error'=>"HTTP ".$res->status()];
            return ['ok'=>true, 'body'=>(string)$res->body(), 'host'=>parse_url($url, PHP_URL_HOST)];
        } catch (\Throwable $e) {
            return ['ok'=>false, 'error'=>$e->getMessage()];
        }
    }

    private function makeDom(string $html): \DOMDocument
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $html = preg_replace('#<script\b[^>]*>[\s\S]*?</script>#i', ' ', $html);
        $html = preg_replace('#<style\b[^>]*>[\s\S]*?</style>#i', ' ', $html);
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $dom->loadHTML($html, LIBXML_NOERROR|LIBXML_NOWARNING);
        libxml_clear_errors();
        return $dom;
    }

    /* ========================= Extractors ========================= */

    private function extractTitle(\DOMDocument $dom): string
    {
        $nodes = $dom->getElementsByTagName('title');
        return ($nodes && $nodes->length) ? $this->cleanText($nodes->item(0)->textContent) : '';
    }

    private function extractMeta(\DOMDocument $dom, string $name): string
    {
        $xp = new \DOMXPath($dom);
        $nodes = $xp->query("//meta[translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='{$name}']/@content");
        if ($nodes && $nodes->length) return $this->cleanText($nodes->item(0)->nodeValue);

        if ($name === 'description') {
            $nodes = $xp->query("//meta[translate(@property,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='og:description']/@content");
            if ($nodes && $nodes->length) return $this->cleanText($nodes->item(0)->nodeValue);
        }
        return '';
    }

    private function extractHeadings(\DOMDocument $dom): array
    {
        $out = ['H1'=>[], 'H2'=>[], 'H3'=>[], 'H4'=>[], 'H5'=>[], 'H6'=>[]];
        foreach (['h1','h2','h3','h4','h5','h6'] as $h) {
            foreach ($dom->getElementsByTagName($h) as $n) {
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

    private function textToHtmlRatio(string $html, string $mainText): int
    {
        $lenHtml = max(1, mb_strlen($html, '8bit'));
        $lenText = max(0, mb_strlen($mainText, 'UTF-8'));
        $ratio = ($lenText / $lenHtml) * 100;
        return (int) round(min(100, max(0, $ratio)));
    }

    private function extractMainText(\DOMDocument $dom): string
    {
        $xp = new \DOMXPath($dom);
        $candidates = [
            '//article',
            '//*[self::main or self::section or self::div][not(@role="navigation")]'
        ];

        $bestTxt = ''; $bestScore = 0;
        foreach ($candidates as $q) {
            $nodes = $xp->query($q); if (!$nodes) continue;
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

    /* ========================= Readability ========================= */

    private function splitSentences(string $text): array {
        $text = preg_replace('/\s+/u', ' ', trim($text));
        if ($text === '') return [];
        $text = preg_replace('/([.!?])\s+(?=[A-Z0-9])/u', "$1\n", $text);
        $parts = preg_split("/\n+/u", $text) ?: [];
        return array_values(array_filter(array_map('trim', $parts), fn($s) => $s !== ''));
    }

    private function tokenizeWords(string $text): array {
        preg_match_all('/[A-Za-z\']+/u', mb_strtolower($text, 'UTF-8'), $m);
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

    private function trigramRepetition(array $tokens): float
    {
        $n = count($tokens);
        if ($n < 3) return 0.0;
        $total = $n - 2;
        $counts = [];
        for ($i=0; $i<$n-2; $i++) {
            $tri = $tokens[$i].' '.$tokens[$i+1].' '.$tokens[$i+2];
            $counts[$tri] = ($counts[$tri] ?? 0) + 1;
        }
        $repeated = 0;
        foreach ($counts as $c) if ($c > 1) $repeated += ($c - 1);
        return $total > 0 ? $repeated / $total : 0.0;
    }

    private function computeReadabilityFromText(string $text): array
    {
        $sentences = $this->splitSentences($text);
        $sCount    = max(1, count($sentences));
        $tokens    = $this->tokenizeWords($text);
        $wCount    = max(1, count($tokens));

        $letters = 0; $chars = 0; $syll = 0; $poly = 0; $complex = 0;
        foreach ($tokens as $w) {
            $letters += preg_match_all('/[a-z]/', $w);
            $chars   += strlen($w);
            $sy = $this->countSyllables($w);
            $syll += $sy;
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

        $unique = count(array_unique($tokens));
        $ttr    = $wCount > 0 ? $unique / $wCount : 0.0;
        $triRep = $this->trigramRepetition($tokens);
        $digits = preg_match_all('/\d/u', $text, $m);
        $digitsPer100 = round(($digits / $wCount) * 100, 1);

        preg_match_all('/\b(is|are|was|were|be|been|being)\s+[a-z]+ed\b/i', $text, $pv);
        $passiveRatio = min(100, round((count($pv[0] ?? []) / $sCount) * 100));

        $displayScore = (int) max(0, min(100, round($flesch)));
        $avgGrade = max(1, min(18, ($fk + $smog + $fog + $cli + $ari) / 5.0));

        return [
            'score'               => $displayScore,
            'grade'               => round($avgGrade, 1),
            'flesch'              => round($flesch, 1),

            'word_count'          => $wCount,
            'sentence_count'      => $sCount,
            'syllable_count'      => $syll,

            'avg_sentence_len'    => round($asl, 1),
            'syllables_per_word'  => round($asw, 2),
            'lexical_diversity'   => round($ttr, 4),
            'repetition_trigram'  => round($triRep, 4),
            'digits_per_100_words'=> $digitsPer100,

            'fk_grade'            => round($fk, 1),
            'smog'                => round($smog, 1),
            'gunning_fog'         => round($fog, 1),
            'coleman_liau'        => round($cli, 1),
            'ari'                 => round($ari, 1),

            'passive_ratio'       => $passiveRatio,
            'note'                => 'Score uses clamped Flesch (0–100) for intuitive wheel; grade is an averaged index.',
        ];
    }

    /* ========================= Categories (FULL) ========================= */

    private function buildCategories(
        string $title,
        string $meta,
        array $headings,
        array $links,
        int $imagesAlt,
        int $schemaCount,
        string $kw,
        array $readability,
        \DOMDocument $dom,
        string $url,
        string $mainText
    ): array
    {
        $band = fn(int $s) => $s>=80?'green':($s>=60?'orange':'red');

        $len = mb_strlen($title);
        $titleScore = ($len>=50 && $len<=60) ? 100 : (($len>=35 && $len<=70)?70:40);
        $metaLen = mb_strlen($meta);
        $metaScore = ($metaLen>=140 && $metaLen<=160) ? 80 : (($metaLen>=110 && $metaLen<=180)?70:45);

        $hasCanonical   = $this->hasCanonical($dom);
        $isIndexable    = $this->isIndexable($dom);
        $hasBreadcrumbs = $this->breadcrumbsEnabled($dom);
        $viewportOK     = $this->hasViewportMeta($dom);
        $faqPresent     = $this->schemaHasTypes($dom, ['FAQPage']);
        $schemaGood     = $schemaCount>=1 || $this->schemaHasTypes($dom, ['Article','BlogPosting','Product','NewsArticle']);
        $orgSameAs      = $this->schemaHasOrganizationSameAs($dom);
        $slugScore      = $this->cleanUrlSlugScore($url);
        $h23Count       = $this->countHeadings($headings, ['H2','H3']);
        $h2h3Score      = $h23Count>=6 ? 90 : ($h23Count>=3 ? 80 : 55);
        $internalScore  = $links['internal']>=6 ? 85 : ($links['internal']>=3 ? 70 : 45);
        $breadcrumbsScore = $hasBreadcrumbs ? 85 : 55;

        $ctaScore       = $this->ctaScore($mainText);
        $speedScore     = $this->hasLazyLoading($dom) ? 70 : 60;
        $webVitalsScore = 60; // neutral default

        $h1HasKw        = $this->h1HasKeyword($headings, $kw);
        $kwInIntro      = $this->kwInFirstPara($mainText, $kw);
        $readScore      = (int)($readability['score'] ?? 0);

        $extLinksScore  = $links['external']>=3 ? 80 : ($links['external']>=1 ? 65 : 45);
        $mediaScore     = $imagesAlt>=5 ? 80 : ($imagesAlt>=1 ? 60 : 40);

        // Content & Keywords (5)
        $ck = [
            ['label'=>'Define search intent & primary topic','score'=>$kwInIntro?65:45],
            ['label'=>'Map target & related keywords (synonyms/PAA)','score'=>$h23Count>=3?66:50],
            ['label'=>'H1 includes primary topic naturally','score'=>$h1HasKw?90:40],
            ['label'=>'Integrate FAQs / questions with answers','score'=>$faqPresent?85:50],
            ['label'=>'Readable, NLP-friendly language','score'=>$readScore>=80?85:($readScore>=60?65:45)],
        ];
        $ckScore = (int)round(array_sum(array_column($ck,'score'))/count($ck));

        // Technical Elements (4)
        $te = [
            ['label'=>'Title tag (≈50–60 chars) w/ primary keyword','score'=>min($titleScore, ($h1HasKw||$kwInIntro)?$titleScore:70)],
            ['label'=>'Meta description (≈140–160 chars) + CTA','score'=>$metaScore],
            ['label'=>'Canonical tag set correctly','score'=>$hasCanonical?95:50],
            ['label'=>'Indexable & listed in XML sitemap','score'=>$isIndexable?80:45],
        ];
        $teScore = (int)round(array_sum(array_column($te,'score'))/count($te));

        // Structure & Architecture (4)
        $sa = [
            ['label'=>'Logical H2/H3 headings & topic clusters','score'=>$h2h3Score],
            ['label'=>'Internal links to hub/related pages','score'=>$internalScore],
            ['label'=>'Clean, descriptive URL slug','score'=>$slugScore],
            ['label'=>'Breadcrumbs enabled (+ schema)','score'=>$breadcrumbsScore],
        ];
        $saScore = (int)round(array_sum(array_column($sa,'score'))/count($sa));

        // Content Quality (4)
        $eeatScore = $this->hasEEATSignals($dom,$mainText) ? 80 : 40;
        $cq = [
            ['label'=>'E-E-A-T signals (author, date, expertise)','score'=>$eeatScore],
            ['label'=>'Unique value vs. top competitors','score'=>60],
            ['label'=>'Facts & citations up to date','score'=>$extLinksScore],
            ['label'=>'Helpful media (images/video) w/ captions','score'=>$mediaScore],
        ];
        $cqScore = (int)round(array_sum(array_column($cq,'score'))/count($cq));

        // User Signals & Experience (4)
        $ux = [
            ['label'=>'Mobile-friendly, responsive layout','score'=>$viewportOK?90:60],
            ['label'=>'Optimized speed (compression, lazy-load)','score'=>$speedScore],
            ['label'=>'Core Web Vitals passing (LCP/INP/CLS)','score'=>$webVitalsScore],
            ['label'=>'Clear CTAs and next steps','score'=>$ctaScore],
        ];
        $uxScore = (int)round(array_sum(array_column($ux,'score'))/count($ux));

        // Entities & Context (4)
        $ec = [
            ['label'=>'Primary entity clearly defined','score'=>$h1HasKw?75:55],
            ['label'=>'Related entities covered with context','score'=>$extLinksScore>=75?75:($h2h3Score>=80?70:55)],
            ['label'=>'Valid schema markup (Article/FAQ/Product)','score'=>$schemaGood?80:40],
            ['label'=>'sameAs/Organization details present','score'=>$orgSameAs?55:40],
        ];
        $ecScore = (int)round(array_sum(array_column($ec,'score'))/count($ec));

        return [
            ['name'=>'Content & Keywords','icon'=>'✍️','score'=>$ckScore,'checks'=>$this->decorate($ck,$band)],
            ['name'=>'Technical Elements','icon'=>'</>','score'=>$teScore,'checks'=>$this->decorate($te,$band)],
            ['name'=>'Structure & Architecture','icon'=>'🧱','score'=>$saScore,'checks'=>$this->decorate($sa,$band)],
            ['name'=>'Content Quality','icon'=>'⭐','score'=>$cqScore,'checks'=>$this->decorate($cq,$band)],
            ['name'=>'User Signals & Experience','icon'=>'👤','score'=>$uxScore,'checks'=>$this->decorate($ux,$band)],
            ['name'=>'Entities & Context','icon'=>'🗂️','score'=>$ecScore,'checks'=>$this->decorate($ec,$band)],
        ];
    }

    private function decorate(array $checks, callable $band): array {
        return array_map(function($c) use ($band){
            $c['color'] = $band((int)$c['score']);
            return $c + [
                'advice'=>'Tap “Improve” for tips.',
                'improve_search_url'=>$this->searchLink($c['label'].' SEO')
            ];
        }, $checks);
    }

    /* ========================= Helpers used by categories ========================= */

    private function hasViewportMeta(\DOMDocument $dom): bool {
        $xp = new \DOMXPath($dom);
        return $xp->query("//meta[translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='viewport']")->length > 0;
    }
    private function hasCanonical(\DOMDocument $dom): bool {
        $xp = new \DOMXPath($dom);
        return $xp->query("//link[translate(@rel,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='canonical' and @href]")->length > 0;
    }
    private function isIndexable(\DOMDocument $dom): bool {
        $xp = new \DOMXPath($dom);
        $nodes = $xp->query("//meta[translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='robots']/@content");
        if ($nodes && $nodes->length) {
            $v = strtolower($nodes->item(0)->nodeValue);
            if (str_contains($v,'noindex')) return false;
        }
        return true;
    }
    private function breadcrumbsEnabled(\DOMDocument $dom): bool {
        if ($this->schemaHasTypes($dom, ['BreadcrumbList'])) return true;
        $xp = new \DOMXPath($dom);
        return $xp->query("//*[@itemtype and contains(translate(@itemtype,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz'),'breadcrumb')]")->length > 0;
    }
    private function schemaHasTypes(\DOMDocument $dom, array $types): bool {
        $xp = new \DOMXPath($dom);
        foreach ($xp->query("//script[@type='application/ld+json']") as $n) {
            $json = strtolower($n->nodeValue ?? '');
            foreach ($types as $t) if (str_contains($json, strtolower($t))) return true;
        }
        return false;
    }
    private function schemaHasOrganizationSameAs(\DOMDocument $dom): bool {
        $xp = new \DOMXPath($dom);
        foreach ($xp->query("//script[@type='application/ld+json']") as $n) {
            $json = strtolower($n->nodeValue ?? '');
            if ((str_contains($json,'"@type":"organization"') || str_contains($json,'"organization"'))
                && str_contains($json,'"sameas"')) return true;
        }
        return false;
    }
    private function hasLazyLoading(\DOMDocument $dom): bool {
        $xp = new \DOMXPath($dom);
        foreach ($xp->query("//img[@loading]") as $n) {
            if (strtolower($n->getAttribute('loading')) === 'lazy') return true;
        }
        return false;
    }
    private function cleanUrlSlugScore(string $url): int {
        $path = trim(parse_url($url, PHP_URL_PATH) ?? '', '/');
        if ($path === '') return 70;
        $seg  = explode('/', $path);
        $slug = end($seg);
        $okHyphen = substr_count($slug,'-')>=1 && substr_count($slug,'-')<=8;
        $tooNumeric = preg_match('/\d{4,}/', $slug);
        return ($okHyphen && !$tooNumeric) ? 85 : 55;
    }
    private function countHeadings(array $headings, array $levels): int {
        $c=0; foreach ($levels as $L) $c += count($headings[$L] ?? []);
        return $c;
    }
    private function h1HasKeyword(array $headings, string $kw): bool {
        if ($kw==='') return false;
        $needle = Str::lower($kw);
        foreach (($headings['H1'] ?? []) as $h) {
            if (Str::contains(Str::lower($h), $needle)) return true;
        }
        return false;
    }
    private function kwInFirstPara(string $text, string $kw): bool {
        if ($kw==='') return false;
        $intro = mb_substr($text, 0, 400);
        return Str::contains(Str::lower($intro), Str::lower($kw));
    }
    private function ctaScore(string $text): int {
        $t = strtolower($text);
        $hits = preg_match_all('/(buy now|add to cart|contact us|sign up|get started|learn more|download)/i', $t);
        return $hits>=2 ? 85 : ($hits>=1 ? 70 : 55);
    }
    private function hasEEATSignals(\DOMDocument $dom, string $text): bool {
        $xp = new \DOMXPath($dom);
        $author = $xp->query("//meta[translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='author']");
        if (($author && $author->length) || preg_match('/\bby\s+[A-Z][a-z]+/u', $text)) return true;
        if (preg_match('/last updated|reviewed|medically reviewed/i', $text)) return true;
        return false;
    }

    /* ========================= Score & recs ========================= */

    private function computeOverallScore(array $categories, array $readability): int
    {
        $map = [];
        foreach ($categories as $c) $map[$c['name']] = (int)$c['score'];

        $content   = $map['Content & Keywords']   ?? 0;
        $technical = $map['Technical Elements']   ?? 0;
        $links     = $map['Structure & Architecture'] ?? 0; // using architecture as nav quality
        $quality   = $map['Content Quality']      ?? 0;
        $ux        = $map['User Signals & Experience'] ?? 0;
        $entities  = $map['Entities & Context']   ?? 0;

        $score = 0.22*$content + 0.20*$technical + 0.15*$links + 0.18*$quality + 0.15*$ux + 0.10*$entities;
        $score = 0.9*$score + 0.1*($readability['score'] ?? 0);
        return (int) round(max(0, min(100, $score)));
    }

    private function wheelLabel(int $score): string
    {
        if ($score >= 80) return 'Great Work — Well Optimized';
        if ($score >= 60) return 'Needs Optimization';
        return 'Needs Significant Optimization';
    }

    private function buildRecommendations(array $links, int $imagesAlt, int $schemaCount, array $headings, array $readability, string $kw): array
    {
        $recs = [];
        if ($schemaCount < 1) $recs[] = ['severity'=>'Info','text'=>'Add JSON-LD schema (FAQPage, Article, Breadcrumb) for richer SERPs.'];
        if ($imagesAlt < 1)  $recs[] = ['severity'=>'Warning','text'=>'Add descriptive alt text to key images.'];
        if ($links['internal'] < 3) $recs[] = ['severity'=>'Warning','text'=>'Add 3–5 internal links to related pillar pages.'];
        if (($readability['score'] ?? 0) < 60) $recs[] = ['severity'=>'Critical','text'=>'Improve readability: shorter sentences, simpler vocabulary, reduce passive voice.'];
        if ($kw !== '' && !$this->keywordPresentInHeadings($headings, $kw)) {
            $recs[] = ['severity'=>'Info','text'=>'Include the target keyword (or variant) in one or two headings where natural.'];
        }
        return $recs;
    }

    private function keywordPresentInHeadings(array $headings, string $kw): bool
    {
        $needle = Str::lower($kw);
        foreach ($headings as $arr) foreach ($arr as $h)
            if (Str::contains(Str::lower($h), $needle)) return true;
        return false;
    }

    private function countSchemaBlocks(\DOMDocument $dom): int
    {
        $xp = new \DOMXPath($dom);
        $jsonLd = $xp->query('//script[@type="application/ld+json"]')->length;
        $micro  = $xp->query('//*[@itemscope]')->length;
        return $jsonLd + $micro;
    }

    private function searchLink(string $q): string
    {
        return 'https://www.google.com/search?q='.rawurlencode($q);
    }
}
