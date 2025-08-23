<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

Route::get('/', function () {
    // return your landing view
    return view('home');
});

Route::post('/analyze.json', function (Request $request) {
    $url = trim($request->input('url', ''));
    if (!$url) {
        return response()->json(['ok' => false, 'error' => 'Missing URL'], 400);
    }

    // Normalize URL
    if (!preg_match('~^https?://~i', $url)) {
        $url = 'https://' . ltrim($url, '/');
    }

    try {
        $resp = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (compatible; SemanticSEOMaster/1.0; +https://example.com/bot)',
            'Accept'     => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        ])->timeout(12)->get($url);
    } catch (\Throwable $e) {
        return response()->json(['ok' => false, 'error' => 'Fetch failed: ' . $e->getMessage()], 500);
    }

    $status = $resp->status();
    $html   = (string) $resp->body();

    // Minimal HTML guard
    if ($status >= 400 || !trim($html)) {
        return response()->json([
            'ok' => true,
            'status' => $status,
            'overall_score' => 0,
            'scores' => [],
            'counts' => ['h1' => 0, 'h2' => 0, 'h3' => 0, 'internal_links' => 0],
            'title' => '',
            'meta_description_len' => 0,
            'canonical' => false,
            'robots' => '',
            'viewport' => false,
            'schema' => ['found_types' => []],
            'auto_check_ids' => [],
            'suggestions' => [],
            'ai_detection' => ['label' => 'mixed', 'ai_pct' => 0, 'human_pct' => 0, 'likelihood' => 0, 'reasons' => []],
        ]);
    }

    // DOM parsing
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    libxml_clear_errors();
    $xpath = new DOMXPath($dom);

    // Helpers
    $text = function(string $query) use($xpath) {
        $n = $xpath->query($query);
        $s = [];
        foreach ($n as $node) $s[] = trim($node->textContent ?? '');
        return $s;
    };
    $attr = function(string $query, string $attr) use($xpath) {
        $n = $xpath->query($query);
        $s = [];
        foreach ($n as $node) {
            if ($node instanceof DOMElement && $node->hasAttribute($attr)) {
                $s[] = trim($node->getAttribute($attr));
            }
        }
        return $s;
    };
    $firstAttr = function(string $query, string $attr) use($attr) {
        $a = $attr($query, $attr);
        return $a[0] ?? null;
    };

    // Extract basics
    $title = ($text('//title')[0] ?? '');
    $metaDesc = $firstAttr('//meta[translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="description"]', 'content') ?? '';
    $viewport = (bool) $firstAttr('//meta[translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="viewport"]', 'content');
    $robots = $firstAttr('//meta[translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="robots"]', 'content') ?? '';
    $canon  = $firstAttr('//link[translate(@rel,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="canonical"]', 'href') ?? '';

    // Headings & links
    $h1s = $text('//h1');
    $h2s = $text('//h2');
    $h3s = $text('//h3');
    $links = $attr('//a[@href]', 'href');
    $host = parse_url($url, PHP_URL_HOST) ?: '';
    $internalCount = 0;
    foreach ($links as $href) {
        if (strpos($href, '#') === 0) continue;
        if (!preg_match('~^https?://~i', $href)) { $internalCount++; continue; }
        $h = parse_url($href, PHP_URL_HOST) ?: '';
        if ($h === $host) $internalCount++;
    }

    // JSON-LD schema detection
    $schemaTypes = [];
    foreach ($xpath->query('//script[@type="application/ld+json"]') as $node) {
        $json = trim($node->textContent ?? '');
        if (!$json) continue;
        $data = json_decode($json, true);
        if (!$data) continue;
        $walk = function($n) use (&$walk, &$schemaTypes) {
            if (is_array($n)) {
                if (isset($n['@type'])) {
                    $t = is_array($n['@type']) ? $n['@type'] : [$n['@type']];
                    foreach ($t as $x) $schemaTypes[] = (string)$x;
                }
                foreach ($n as $k => $v) $walk($v);
            } elseif (is_list($n)) {
                foreach ($n as $v) $walk($v);
            }
        };
        $walk($data);
    }
    $schemaTypes = array_values(array_unique(array_filter(array_map('strval', $schemaTypes))));

    // Content text for heuristics
    $bodyText = trim(preg_replace('~\s+~', ' ', implode(' ', $text('//body//*[not(self::script or self::style)]'))));
    $sentences = preg_split('~(?<=[.!?])\s+~', $bodyText) ?: [];
    $avgSentenceLen = 0;
    if (count($sentences) > 0) {
        $lens = array_map(fn($s)=>strlen($s), $sentences);
        $avgSentenceLen = array_sum($lens)/max(1,count($lens));
    }
    $hasFAQBlock = stripos($html, '"@type":"FAQPage"') !== false || preg_match('~<details|faq~i', $html);
    $hasBreadcrumb = in_array('BreadcrumbList', $schemaTypes, true);
    $hasArticle = in_array('Article', $schemaTypes, true) || in_array('BlogPosting', $schemaTypes, true) || in_array('NewsArticle', $schemaTypes, true);
    $hasOrg = in_array('Organization', $schemaTypes, true) || in_array('Brand', $schemaTypes, true) || in_array('LocalBusiness', $schemaTypes, true);

    // Slug quality
    $path = parse_url($url, PHP_URL_PATH) ?: '/';
    $slug = trim($path, '/');
    $slugScore = 70;
    if ($slug === '') { $slugScore = 70; }
    else {
        $len = strlen($slug);
        $hyph = substr_count($slug, '-');
        $slugScore = 90;
        if ($len > 80 || $hyph > 10) $slugScore = 55;
        if (preg_match('~[A-Z]~', $slug)) $slugScore -= 10;
    }

    // Title/meta scoring
    $titleLen = strlen($title);
    $titleScore = $titleLen >= 45 && $titleLen <= 65 ? 92 : ($titleLen >= 30 && $titleLen <= 72 ? 78 : 45);
    $metaLen = strlen($metaDesc);
    $metaScore = $metaLen >= 120 && $metaLen <= 170 ? 90 : ($metaLen >= 80 ? 70 : 40);

    // H1 keyword alignment
    $tokens = array_filter(explode('-', strtolower($slug)));
    $primary = $tokens[0] ?? '';
    $h1Score = 50;
    if (count($h1s)) {
        $h1txt = strtolower(implode(' ', $h1s));
        if ($primary && str_contains($h1txt, $primary)) $h1Score = 90; else $h1Score = 70;
    }

    // Internal links
    $internalScore = $internalCount >= 8 ? 88 : ($internalCount >= 3 ? 70 : 40);

    // E-E-A-T signals (author/date)
    $authorLike = preg_match('~by\s+[A-Z][a-z]+~', $bodyText) || stripos($html, 'itemprop="author"') !== false;
    $dateLike   = preg_match('~\b20(1\d|2\d)\b~', $bodyText) || stripos($html, 'datePublished') !== false;
    $eeatScore  = ($authorLike ? 10 : 0) + ($dateLike ? 10 : 0) + ($hasArticle ? 20 : 0);
    $eeatScore  = 60 + $eeatScore; // 60–100

    // Media presence
    $imgCount = count($xpath->query('//img'));
    $videoCount = count($xpath->query('//video')) + count($xpath->query('//iframe[contains(@src,"youtube") or contains(@src,"vimeo")]'));
    $mediaScore = ($imgCount >= 3 ? 75 : 55) + ($videoCount ? 10 : 0);

    // Readability proxy: avg sentence length
    $readScore = $avgSentenceLen > 220 ? 45 : ($avgSentenceLen > 150 ? 65 : 85);

    // Robots/indexable
    $indexable = stripos($robots, 'noindex') === false;
    $indexScore = $indexable ? 82 : 20;

    // Canonical
    $canonScore = $canon ? 90 : 40;

    // Breadcrumb/schema
    $breadScore = $hasBreadcrumb ? 85 : 45;
    $schemaScore = count($schemaTypes) ? 85 : 45;

    // FAQ
    $faqScore = $hasFAQBlock ? 88 : 55;

    // Mobile
    $mobileScore = $viewport ? 88 : 40;

    // CTA detection
    $ctaScore = preg_match('~\b(contact|buy|pricing|subscribe|get started|book|download)\b~i', $bodyText) ? 82 : 60;

    // External citations
    $externalLinks = 0;
    foreach ($links as $href) {
        if (preg_match('~^https?://~i', $href)) {
            $h = parse_url($href, PHP_URL_HOST) ?: '';
            if ($h && $h !== $host) $externalLinks++;
        }
    }
    $citeScore = $externalLinks >= 3 ? 80 : ($externalLinks >= 1 ? 70 : 50);

    // Entities (very rough): unique proper-cased words
    preg_match_all('~\b[A-Z][a-z]{2,}\b~', $bodyText, $m);
    $proper = array_values(array_unique($m[0] ?? []));
    $entityScore = count($proper) >= 10 ? 80 : (count($proper) >= 5 ? 70 : 55);

    // Related entities via H2/H3 variety
    $h2h3 = array_map('strtolower', array_merge($h2s, $h3s));
    $uniqueH = count(array_unique(array_filter($h2h3)));
    $relatedScore = $uniqueH >= 6 ? 82 : ($uniqueH >= 3 ? 70 : 55);

    // Speed/Vitals (heuristics only)
    $lazyImgs = count($xpath->query('//img[@loading="lazy"]'));
    $hasPreload = stripos($html, 'rel="preload"') !== false;
    $speedScore = 60 + min(25, $lazyImgs*3) + ($hasPreload ? 8 : 0);
    $vitalsScore = 62; // unknown; neutral-ish

    // Organization sameAs
    $sameAsScore = 40;
    if (preg_match('~"@type"\s*:\s*"(Organization|Brand|LocalBusiness)".+?"sameAs"\s*:\s*\[~is', $html)) {
        $sameAsScore = 82;
    }

    // Slug quality already computed -> $slugScore

    // Map the 25 checklist items
    $scores = [
        'ck-1'  => max(60, $titleScore - 5),                // Search intent & topic proxy
        'ck-2'  => 65,                                      // Keyword mapping (unknown)
        'ck-3'  => $h1Score,                                // H1 has primary topic
        'ck-4'  => $faqScore,                               // FAQs present
        'ck-5'  => $readScore,                              // Readable/NLP-friendly
        'ck-6'  => $titleScore,                             // Title length
        'ck-7'  => $metaScore,                              // Meta description length
        'ck-8'  => $canonScore,                             // Canonical
        'ck-9'  => $indexScore,                             // Indexable
        'ck-10' => $eeatScore,                              // E-E-A-T
        'ck-11' => 65,                                      // Unique value (unknown)
        'ck-12' => $citeScore,                              // Citations
        'ck-13' => min(95, $mediaScore),                    // Media
        'ck-14' => $uniqueH >= 5 ? 80 : ($uniqueH >= 3 ? 70 : 55), // Logical headings
        'ck-15' => $internalScore,                          // Internal links
        'ck-16' => $slugScore,                              // Clean URL
        'ck-17' => $breadScore,                             // Breadcrumbs
        'ck-18' => $mobileScore,                            // Mobile-friendly
        'ck-19' => min(95, $speedScore),                    // Speed hints
        'ck-20' => $vitalsScore,                            // CWV (unknown)
        'ck-21' => $ctaScore,                               // CTAs present
        'ck-22' => $hasArticle || $hasOrg ? 80 : 60,        // Primary entity via schema
        'ck-23' => $relatedScore,                           // Related entities
        'ck-24' => $schemaScore,                            // Valid schema present
        'ck-25' => $sameAsScore,                            // sameAs/Org details
    ];

    // Overall score (weighted average leaning a bit on content + technical)
    $weights = [
        1=>1,2=>1,3=>1.2,4=>1,5=>1.2,6=>1.3,7=>1.1,8=>1.2,9=>1.1,10=>1.2,11=>1,
        12=>1,13=>1,14=>1,15=>1.1,16=>1,17=>0.9,18=>1.2,19=>1.1,20=>1,21=>1,22=>1,23=>1,24=>1.1,25=>0.9
    ];
    $wSum = 0; $acc = 0; $i = 1;
    foreach ($scores as $k=>$v) { $w = $weights[$i] ?? 1; $acc += $v*$w; $wSum += $w; $i++; }
    $overall = $wSum ? round($acc/$wSum) : 0;

    // Suggestions (only a few targeted examples)
    $sugs = [];
    $push = function($id, $txt) use (&$sugs) { $sugs[$id][] = $txt; };

    if ($titleScore < 80)  $push('ck-6', 'Keep title ≈50–60 chars and include the primary keyword once.');
    if ($metaScore < 80)   $push('ck-7', 'Write a meta description around 140–160 chars with a clear CTA.');
    if (!$canon)           $push('ck-8', 'Add a canonical link to avoid duplicate content signals.');
    if (!$viewport)        $push('ck-18', 'Add `<meta name="viewport" content="width=device-width, initial-scale=1">`.');
    if (!$hasBreadcrumb)   $push('ck-17', 'Add breadcrumbs and BreadcrumbList schema.');
    if (!count($schemaTypes)) $push('ck-24', 'Add JSON-LD (Article/FAQ/Product) with @id and sameAs if applicable.');
    if ($internalCount < 3) $push('ck-15', 'Add internal links to related hubs and parent category pages.');

    // AI/Human very rough heuristic
    $aiLike = 0;
    if ($avgSentenceLen >= 160) $aiLike += 20;
    if (preg_match('~\bIn conclusion,|Overall,|Firstly,|Secondly,|Additionally,~', $bodyText)) $aiLike += 10;
    $aiLike = min(100, $aiLike);
    $aiPct = $aiLike;
    $humanPct = max(0, 100 - $aiPct);
    $label = $aiPct >= 66 ? 'likely_ai' : ($aiPct >= 33 ? 'mixed' : 'likely_human');

    // Auto check IDs (>=80)
    $auto = [];
    foreach ($scores as $id=>$val) if ($val >= 80) $auto[] = $id;

    return response()->json([
        'ok' => true,
        'status' => $status,
        'overall_score' => $overall,
        'scores' => $scores,
        'title' => $title,
        'meta_description_len' => $metaLen,
        'canonical' => (bool)$canon,
        'robots' => $robots ?: '',
        'viewport' => $viewport,
        'counts' => [
            'h1' => count($h1s), 'h2' => count($h2s), 'h3' => count($h3s),
            'internal_links' => $internalCount,
        ],
        'schema' => ['found_types' => $schemaTypes],
        'auto_check_ids' => $auto,
        'suggestions' => $sugs,
        'ai_detection' => [
            'label' => $label,
            'ai_pct' => $aiPct,
            'human_pct' => $humanPct,
            'likelihood' => $aiPct,
            'reasons' => $aiPct >= 66 ? ['Long average sentences', 'Common connective phrases'] : ['Natural sentence variety'],
            'ai_sentences' => [],
            'human_sentences' => [],
            'full_text' => mb_substr($bodyText, 0, 5000),
        ],
    ]);
})->name('analyze.json');
