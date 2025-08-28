<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class AnalyzerController extends Controller
{
    /**
     * POST analyzer endpoint
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
                return response()->json(['ok'=>false,'error' => $resp['error'] ?? 'Fetch failed'], 422);
            }
            $html = $resp['body'] ?? '';
            $host = $resp['host'] ?? parse_url($url, PHP_URL_HOST);

            // 2) Parse DOM
            $dom  = $this->makeDom($html);

            // 3) Extract content
            $mainText = $this->extractMainText($dom);

            // 4) Structure & quick counts
            $title           = $this->extractTitle($dom);
            $metaDescription = $this->extractMeta($dom, 'description');
            $headings        = $this->extractHeadings($dom);
            $links           = $this->extractLinks($dom, $host);
            $imagesAlt       = $this->countImagesWithAlt($dom);
            $schemaCount     = $this->countSchemaBlocks($dom);
            $ratio           = $this->textToHtmlRatio($html, $mainText);

            // 5) Readability (real metrics from text)
            $readability = $this->computeReadabilityFromText($mainText);

            // 6) Categories & overall
            $categories    = $this->buildCategories($title, $metaDescription, $headings, $links, $imagesAlt, $schemaCount, $kw, $readability);
            $overallScore  = $this->computeOverallScore($categories, $readability);
            $wheel         = ['label' => $this->wheelLabel($overallScore)];
            $recommendations = $this->buildRecommendations($links, $imagesAlt, $schemaCount, $headings, $readability, $kw);

            // 7) Payload
            return response()->json([
                'ok'            => true,
                'overall_score' => $overallScore,
                'wheel'         => $wheel,

                'quick_stats'   => [
                    'readability_flesch' => $readability['flesch'], // real Flesch (can be <0/>100)
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

                // Rich readability block consumed by the Blade
                'readability'    => $readability,

                'recommendations'=> $recommendations,
                'categories'     => $categories,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['ok'=>false,'error' => 'Analyzer error: '.$e->getMessage()], 500);
        }
    }

    /* ============================================================
     | Fetching & DOM
     * ============================================================*/

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
            $body = (string)$res->body();
            return ['ok'=>true, 'body'=>$body, 'host'=>parse_url($url, PHP_URL_HOST)];
        } catch (\Throwable $e) {
            return ['ok'=>false, 'error'=>$e->getMessage()];
        }
    }

    private function makeDom(string $html): \DOMDocument
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        // strip <script>/<style> to get cleaner text
        $html = preg_replace('#<script\b[^>]*>[\s\S]*?</script>#i', ' ', $html);
        $html = preg_replace('#<style\b[^>]*>[\s\S]*?</style>#i', ' ', $html);
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $dom->loadHTML($html, LIBXML_NOERROR|LIBXML_NOWARNING);
        libxml_clear_errors();
        return $dom;
    }

    /* ============================================================
     | Extraction
     * ============================================================*/

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

        if ($name === 'description') { // fallback og:description
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

    /**
     * Lightweight "readability" content pick: use the sizeable node with punctuation.
     */
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

    /* ============================================================
     | Readability (real metrics + extras for UI)
     * ============================================================*/

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
        return $total > 0 ? $repeated / $total : 0.0; // 0..1
    }

    private function computeReadabilityFromText(string $text): array
    {
        $sentences = $this->splitSentences($text);
        $sCount    = count($sentences);
        $tokens    = $this->tokenizeWords($text);
        $wCount    = count($tokens);

        // Guard against empty
        $sCount = max(1, $sCount);
        $wCount = max(1, $wCount);

        // Syllables & stats
        $letters = 0; $chars = 0; $syll = 0; $poly = 0; $complex = 0;
        foreach ($tokens as $w) {
            $letters += preg_match_all('/[a-z]/', $w);
            $chars   += strlen($w);
            $sy = $this->countSyllables($w);
            $syll += $sy;
            if ($sy >= 3) { $poly++; $complex++; }
        }

        $asl = $wCount / $sCount;          // avg sentence length
        $asw = $syll   / $wCount;          // avg syllables per word

        // Flesch / Grades
        $flesch = 206.835 - 1.015 * $asl - 84.6 * $asw;
        $fk     = 0.39 * $asl + 11.8 * $asw - 15.59;
        $smog   = 1.0430 * sqrt($poly * (30.0 / $sCount)) + 3.1291;
        $fog    = 0.4 * ($asl + 100.0 * ($complex / $wCount));
        $L      = ($letters / $wCount) * 100.0;
        $S      = ($sCount  / $wCount) * 100.0;
        $cli    = 0.0588 * $L - 0.296 * $S - 15.8;
        $ari    = 4.71 * ($chars / $wCount) + 0.5 * $asl - 21.43;

        // Extras for tiles
        $unique = count(array_unique($tokens));
        $ttr    = $wCount > 0 ? $unique / $wCount : 0.0;                 // 0..1
        $triRep = $this->trigramRepetition($tokens);                      // 0..1
        $digits = preg_match_all('/\d/u', $text, $m);
        $digitsPer100 = round(($digits / $wCount) * 100, 1);

        // Passive voice heuristic
        preg_match_all('/\b(is|are|was|were|be|been|being)\s+[a-z]+ed\b/i', $text, $pv);
        $passiveHits  = count($pv[0] ?? []);
        $passiveRatio = min(100, round(($passiveHits / $sCount) * 100));

        // Normalize to 0..100 for wheel (clamped Flesch is intuitive)
        $displayScore = (int) max(0, min(100, round($flesch)));

        // Aggregate grade (for Quick Stats grade label)
        $avgGrade = max(1, min(18, ($fk + $smog + $fog + $cli + $ari) / 5.0));

        return [
            // wheel/labels
            'score'               => $displayScore,         // 0..100 for the wheel
            'grade'               => round($avgGrade, 1),
            'flesch'              => round($flesch, 1),

            // raw counts (UI can recompute)
            'word_count'          => $wCount,
            'sentence_count'      => $sCount,
            'syllable_count'      => $syll,

            // convenience
            'avg_sentence_len'    => round($asl, 1),
            'syllables_per_word'  => round($asw, 2),
            'lexical_diversity'   => round($ttr, 4),        // 0..1
            'repetition_trigram'  => round($triRep, 4),     // 0..1
            'digits_per_100_words'=> $digitsPer100,

            // other readability indices (optional to show later)
            'fk_grade'            => round($fk, 1),
            'smog'                => round($smog, 1),
            'gunning_fog'         => round($fog, 1),
            'coleman_liau'        => round($cli, 1),
            'ari'                 => round($ari, 1),

            'passive_ratio'       => $passiveRatio,         // %
            'note'                => 'Score uses clamped Flesch (0–100) for an intuitive wheel; grade is an average of common indices.',
        ];
    }

    /* ============================================================
     | Scoring & recommendations
     * ============================================================*/

    private function buildCategories(string $title, string $meta, array $headings, array $links, int $imagesAlt, int $schemaCount, string $kw, array $readability): array
    {
        $band = fn(int $s) => $s>=80?'green':($s>=60?'orange':'red');

        // Content & Keywords
        $ck = []; $ckScore = 0; $ckMax = 0;
        $hasKwTitle = ($kw !== '' && Str::contains(Str::lower($title), Str::lower($kw)));
        $ck[] = ['label'=>'Primary keyword in title', 'score'=>$hasKwTitle?100:50, 'color'=>$band($hasKwTitle?100:50),
                 'advice'=>'Place target keyword near the beginning of the title.',
                 'improve_search_url'=>$this->searchLink('keyword in title best practices')];
        $ckScore += $hasKwTitle?100:50; $ckMax += 100;

        $hasMeta = strlen($meta) >= 120 && strlen($meta) <= 180;
        $ck[] = ['label'=>'Meta description length', 'score'=>$hasMeta?90:50, 'color'=>$band($hasMeta?90:50),
                 'advice'=>'Keep meta description around 150–160 characters.'];
        $ckScore += $hasMeta?90:50; $ckMax += 100;

        $hasH1 = !empty($headings['H1']);
        $ck[] = ['label'=>'Single, descriptive H1', 'score'=>$hasH1?90:30, 'color'=>$band($hasH1?90:30),
                 'advice'=>'Ensure one clear H1 summarizing the page.'];
        $ckScore += $hasH1?90:30; $ckMax += 100;

        $contentScore = (int)round(($ckScore / max(1,$ckMax)) * 100);

        // Technical Elements
        $te = []; $teScore = 0; $teMax = 0;
        $imgsScore = $imagesAlt >= 5 ? 90 : ($imagesAlt >= 1 ? 60 : 30);
        $te[] = ['label'=>'Images have alt text', 'score'=>$imgsScore, 'color'=>$band($imgsScore),
                 'advice'=>'Add descriptive alt text to important images.'];
        $teScore += $imgsScore; $teMax += 100;

        $schemaScore = $schemaCount >= 1 ? 85 : 40;
        $te[] = ['label'=>'Structured data (JSON-LD/Microdata)', 'score'=>$schemaScore, 'color'=>$band($schemaScore),
                 'advice'=>'Add appropriate schema (Breadcrumb, Article, FAQPage).'];
        $teScore += $schemaScore; $teMax += 100;

        $techScore = (int)round(($teScore / max(1,$teMax)) * 100);

        // Links & Navigation
        $ln = []; $lnScore = 0; $lnMax = 0;
        $intScore = $links['internal'] >= 5 ? 85 : ($links['internal'] >= 2 ? 65 : 40);
        $ln[] = ['label'=>'Internal links to related hubs', 'score'=>$intScore, 'color'=>$band($intScore),
                 'advice'=>'Add 3–5 relevant internal links to pillar pages.'];
        $lnScore += $intScore; $lnMax += 100;

        $extScore = $links['external'] >= 2 ? 80 : 45;
        $ln[] = ['label'=>'External authoritative references', 'score'=>$extScore, 'color'=>$band($extScore),
                 'advice'=>'Cite 2–3 trusted sources (docs, standards, studies).'];
        $lnScore += $extScore; $lnMax += 100;

        $linksScore = (int)round(($lnScore / max(1,$lnMax)) * 100);

        // Experience & Trust
        $et = []; $etScore = 0; $etMax = 0;
        $readScore = (int)$readability['score'];
        $et[] = ['label'=>'Readable for target audience', 'score'=>$readScore, 'color'=>$band($readScore),
                 'advice'=>'Target Grade 8–10 and reduce passive voice.'];
        $etScore += $readScore; $etMax += 100;

        $authorScore = 70;
        $et[] = ['label'=>'Author / Last updated visible', 'score'=>$authorScore, 'color'=>$band($authorScore),
                 'advice'=>'Show author bio and “last updated” date on the page.'];
        $etScore += $authorScore; $etMax += 100;

        $expScore = (int)round(($etScore / max(1,$etMax)) * 100);

        return [
            ['name'=>'Content & Keywords', 'score'=>$contentScore, 'checks'=>$ck],
            ['name'=>'Technical Elements', 'score'=>$techScore,   'checks'=>$te],
            ['name'=>'Links & Navigation', 'score'=>$linksScore,  'checks'=>$ln],
            ['name'=>'Experience & Trust', 'score'=>$expScore,    'checks'=>$et],
        ];
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
        foreach ($headings as $arr) {
            foreach ($arr as $h) {
                if (Str::contains(Str::lower($h), $needle)) return true;
            }
        }
        return false;
    }

    private function computeOverallScore(array $categories, array $readability): int
    {
        // Weighted: Content 30%, Technical 25%, Links 20%, Experience 25% (includes readability)
        $map = [];
        foreach ($categories as $c) $map[$c['name']] = (int)$c['score'];

        $content   = $map['Content & Keywords']   ?? 0;
        $technical = $map['Technical Elements']   ?? 0;
        $links     = $map['Links & Navigation']   ?? 0;
        $exp       = $map['Experience & Trust']   ?? 0;

        $score = 0.30*$content + 0.25*$technical + 0.20*$links + 0.25*$exp;
        $score = 0.9*$score + 0.1*($readability['score'] ?? 0); // slight nudge from readability

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
        $jsonLd = $xp->query('//script[@type="application/ld+json"]')->length;
        $micro  = $xp->query('//*[@itemscope]')->length;
        return $jsonLd + $micro;
    }

    private function searchLink(string $q): string
    {
        return 'https://www.google.com/search?q='.rawurlencode($q);
    }
}
