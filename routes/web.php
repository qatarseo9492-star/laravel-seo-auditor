<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('home');
})->name('home');

/*
|--------------------------------------------------------------------------
| Helpers (plain functions inside this file)
|--------------------------------------------------------------------------
*/

if (!function_exists('safe_url')) {
    function safe_url(string $u): string {
        $u = trim($u);
        if (!$u) return '';
        if (!preg_match('~^https?://~i', $u)) {
            $u = 'https://' . ltrim($u, '/');
        }
        return $u;
    }
}

if (!function_exists('fetch_html')) {
    function fetch_html(string $url, int $timeout = 12): array {
        try {
            $res = Http::timeout($timeout)
                ->withHeaders([
                    'User-Agent' => 'Semantic-SEO-Master/1.0 (+https://example.com/)',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                ])->get($url);

            return [
                'ok'     => $res->successful(),
                'status' => $res->status(),
                'html'   => $res->body() ?? '',
                'headers'=> $res->headers(),
            ];
        } catch (\Throwable $e) {
            return ['ok' => false, 'status' => 0, 'html' => '', 'error' => $e->getMessage()];
        }
    }
}

if (!function_exists('dom_xp')) {
    function dom_xp(string $html): array {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        @$dom->loadHTML($html);
        libxml_clear_errors();
        $xp = new DOMXPath($dom);
        return [$dom, $xp];
    }
}

if (!function_exists('node_text')) {
    function node_text(?DOMNode $n): string {
        return $n ? trim($n->textContent ?? '') : '';
    }
}

if (!function_exists('extract_full_text')) {
    function extract_full_text(DOMXPath $xp): string {
        // Prefer article / main > article
        $parts = [];
        foreach ([
            '//main//text()',
            '//article//text()',
            '//div[contains(@class,"content") or contains(@id,"content")]//text()',
            '//body//p//text()'
        ] as $q) {
            $nl = $xp->query($q);
            if ($nl && $nl->length) {
                foreach ($nl as $n) {
                    $t = trim($n->nodeValue ?? '');
                    if ($t !== '') $parts[] = $t;
                }
            }
            if (strlen(implode(' ', $parts)) > 3000) break; // enough text
        }
        $text = preg_replace('~\s+~', ' ', implode(' ', $parts));
        return trim($text);
    }
}

if (!function_exists('word_stats')) {
    function word_stats(string $text): array {
        $clean = mb_strtolower(preg_replace('~[^a-z0-9\s\.\!\?]~iu', ' ', $text));
        $words = preg_split('~\s+~u', $clean, -1, PREG_SPLIT_NO_EMPTY);
        $sentences = preg_split('~[\.!\?]+~u', $text, -1, PREG_SPLIT_NO_EMPTY);
        return [
            'words' => $words,
            'sentences' => $sentences
        ];
    }
}

if (!function_exists('readability')) {
    // Simple Flesch Reading Ease + Flesch-Kincaid Grade
    function readability(string $text): array {
        if (!$text) return ['fre' => 0, 'fk' => 0];
        $words = preg_split('~\s+~u', trim($text), -1, PREG_SPLIT_NO_EMPTY);
        $sents = preg_split('~[\.!\?]+~u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $w = max(1, count($words));
        $s = max(1, count($sents));

        // naive syllable count
        $syll = 0;
        foreach ($words as $wrd) {
            $v = preg_match_all('~[aeiouy]+~i', $wrd);
            $syll += max(1, (int)$v);
        }
        $fre = 206.835 - 1.015 * ($w / $s) - 84.6 * ($syll / $w);
        $fk  = 0.39 * ($w / $s) + 11.8 * ($syll / $w) - 15.59;
        return ['fre' => round($fre,1), 'fk' => round($fk,1)];
    }
}

if (!function_exists('score_from_bool')) {
    function score_from_bool(bool $ok): int { return $ok ? 100 : 0; }
}

if (!function_exists('ai_human_detect')) {
    /**
     * Heuristic detector (no external service): returns label, likelihood and snippets.
     * NOTE: No detector can be 100% accurate; this is a best-effort heuristic.
     */
    function ai_human_detect(string $text): array {
        $text = trim($text);
        if ($text === '') {
            return [
                'label' => 'unknown',
                'likelihood' => 0,
                'ai_pct' => 0,
                'human_pct' => 0,
                'reasons' => ['No content extracted'],
                'ai_sentences' => [],
                'human_sentences' => [],
                'full_text' => ''
            ];
        }
        $sents = preg_split('~(?<=[\.!\?])\s+~u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $ai_like = [];
        $human_like = [];
        foreach ($sents as $s) {
            $len = mb_strlen($s);
            $comma = substr_count($s, ',');
            $perp  = substr_count($s, '(') + substr_count($s, ')');
            $passive = preg_match('~\b(is|was|were|be|been|being)\s+(?:\w+ed)\b~i', $s);
            $hedge  = preg_match('~\b(may|might|could|seems?|appears?|in general|overall)\b~i', $s);
            $aiCue  = preg_match('~\b(as an ai|language model|cannot|i am unable)\b~i', mb_strtolower($s));

            $aiScore = ($len > 160) + ($comma >= 3) + ($perp >= 2) + $passive + $hedge + (2*$aiCue);
            if ($aiScore >= 2) $ai_like[] = trim($s);
            else $human_like[] = trim($s);
        }
        $total = max(1, count($sents));
        $aiPct = (int) round(count($ai_like) / $total * 100);
        $humanPct = 100 - $aiPct;
        $label = $aiPct >= 60 ? 'likely_ai' : ($aiPct >= 35 ? 'mixed' : 'likely_human');
        $reasons = [
            'Heuristics: sentence length, passive voice, hedging, punctuation density',
            'This is indicative, not definitive'
        ];
        return [
            'label' => $label,
            'likelihood' => abs(50 - abs(50 - $aiPct)), // playful "confidence" 0..50
            'ai_pct' => $aiPct,
            'human_pct' => $humanPct,
            'reasons' => $reasons,
            'ai_sentences' => array_slice($ai_like, 0, 40),
            'human_sentences' => array_slice($human_like, 0, 40),
            'full_text' => $text
        ];
    }
}

if (!function_exists('analyze_document')) {
    function analyze_document(string $url, string $html): array {
        [$dom, $xp] = dom_xp($html);

        // Title
        $titleNode = $xp->query('//title')->item(0);
        $title = node_text($titleNode);

        // Meta description (fix for your earlier XPath string error)
        $metaDescNode = $xp->query(
            '//meta[translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="description"]'
        )->item(0);
        $metaDesc = $metaDescNode ? $metaDescNode->getAttribute('content') : '';
        $metaDescLen = mb_strlen($metaDesc);

        // Canonical
        $canon = $xp->query('//link[translate(@rel,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="canonical"]/@href')->item(0);
        $canonical = $canon ? $canon->nodeValue : null;

        // robots
        $robotsNode = $xp->query('//meta[translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="robots"]/@content')->item(0);
        $robots = $robotsNode ? $robotsNode->nodeValue : null;

        // viewport
        $viewportNode = $xp->query('//meta[translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="viewport"]')->item(0);
        $viewport = (bool)$viewportNode;

        // headings count
        $h1 = $xp->query('//h1')->length;
        $h2 = $xp->query('//h2')->length;
        $h3 = $xp->query('//h3')->length;

        // internal links
        $a = $xp->query('//a[@href]');
        $internal = 0;
        if ($a) {
            foreach ($a as $node) {
                $href = $node->getAttribute('href');
                if (!$href) continue;
                if (Str::startsWith($href, ['#','mailto:','tel:'])) continue;
                $isInternal = false;
                if (Str::startsWith($href, '/')) $isInternal = true;
                else {
                    try {
                        $u = parse_url($href);
                        $host = $u['host'] ?? '';
                        $baseHost = parse_url($url, PHP_URL_HOST) ?: '';
                        if ($host && $baseHost && Str::endsWith($host, $baseHost)) $isInternal = true;
                    } catch (\Throwable $e) {}
                }
                if ($isInternal) $internal++;
            }
        }

        // schema detection
        $types = [];
        $ld = $xp->query('//script[@type="application/ld+json"]');
        if ($ld && $ld->length) {
            foreach ($ld as $sn) {
                $json = trim($sn->textContent ?? '');
                if ($json) {
                    try {
                        $obj = json_decode($json, true);
                        if (is_array($obj)) {
                            $cand = [];
                            $iter = function ($x) use (&$cand, &$iter) {
                                if (is_array($x)) {
                                    if (isset($x['@type'])) $cand[] = $x['@type'];
                                    foreach ($x as $v) $iter($v);
                                }
                            };
                            $iter($obj);
                            foreach ($cand as $t) {
                                $types[] = is_string($t) ? $t : (is_array($t) ? implode(',', $t) : '');
                            }
                        }
                    } catch (\Throwable $e) {}
                }
            }
        }
        $types = array_values(array_filter(array_unique($types)));

        // sitemap presence (very light)
        $xmlMap = false;
        try {
            $host = parse_url($url, PHP_URL_SCHEME) . '://' . parse_url($url, PHP_URL_HOST);
            $sitemapUrl = rtrim($host, '/') . '/sitemap.xml';
            $sm = Http::timeout(6)->get($sitemapUrl);
            $xmlMap = $sm->ok();
        } catch (\Throwable $e) {}

        // full text for AI/human + readability
        $fullText = extract_full_text($xp);
        $ai = ai_human_detect($fullText);
        $read = readability($fullText);

        // Checklist scoring (very simple heuristics for demo)
        $scores = [];
        $autoCheck = [];

        // 1 Intent/topic → if h1 or title exists
        $scores['ck-1'] = $title ? 80 : 30;
        if ($scores['ck-1'] >= 70) $autoCheck[] = 'ck-1';

        // 2 Keywords mapping (proxy via at least one H2/H3)
        $scores['ck-2'] = ($h2 + $h3) > 0 ? 75 : 40;
        if ($scores['ck-2'] >= 70) $autoCheck[] = 'ck-2';

        // 3 H1 includes topic (proxy: H1 exists and has 20–80 chars)
        $scores['ck-3'] = $h1 > 0 ? 80 : 20;
        if ($scores['ck-3'] >= 70) $autoCheck[] = 'ck-3';

        // 4 FAQs presence (look for FAQ schema)
        $hasFAQ = in_array('FAQPage', $types, true);
        $scores['ck-4'] = $hasFAQ ? 90 : 40;
        if ($hasFAQ) $autoCheck[] = 'ck-4';

        // 5 Readable language (FRE > 50)
        $scores['ck-5'] = $read['fre'] >= 50 ? 85 : 55;

        // 6 Title len
        $scores['ck-6'] = (mb_strlen($title) >= 50 && mb_strlen($title) <= 65) ? 90 : (mb_strlen($title) ? 65 : 20);
        if ($scores['ck-6'] >= 70) $autoCheck[] = 'ck-6';

        // 7 Meta description len
        $scores['ck-7'] = ($metaDescLen >= 140 && $metaDescLen <= 170) ? 90 : ($metaDescLen ? 65 : 20);
        if ($scores['ck-7'] >= 70) $autoCheck[] = 'ck-7';

        // 8 Canonical
        $scores['ck-8'] = score_from_bool((bool)$canonical);
        if ($scores['ck-8'] >= 70) $autoCheck[] = 'ck-8';

        // 9 Indexable + in sitemap
        $idx = !preg_match('~noindex~i', $robots ?? '');
        $scores['ck-9'] = $idx && $xmlMap ? 90 : ($idx ? 70 : 10);
        if ($scores['ck-9'] >= 70) $autoCheck[] = 'ck-9';

        // 10 E-E-A-T (author/date hints)
        $author = $xp->query('//*[@itemprop="author"]|//*[@rel="author"]|//*[contains(@class,"author")]')->length > 0;
        $date   = $xp->query('//*[@datetime or contains(@class,"date")]')->length > 0;
        $scores['ck-10'] = ($author || $date) ? 80 : 50;

        // 11 Unique value vs competitors (unknown here)
        $scores['ck-11'] = 60;

        // 12 Facts & citations (look for external links)
        $extLinks = 0;
        if ($a) {
            foreach ($a as $node) {
                $href = $node->getAttribute('href');
                if (preg_match('~^https?://~i', $href)) $extLinks++;
            }
        }
        $scores['ck-12'] = $extLinks >= 3 ? 85 : ($extLinks ? 65 : 40);

        // 13 Media
        $media = $xp->query('//img|//video')->length;
        $scores['ck-13'] = $media >= 2 ? 85 : ($media ? 70 : 40);

        // 14 Logical headings
        $scores['ck-14'] = ($h2 + $h3) >= 4 ? 85 : (($h2 + $h3) ? 70 : 45);

        // 15 Internal links
        $scores['ck-15'] = $internal >= 5 ? 85 : ($internal ? 65 : 40);
        if ($scores['ck-15'] >= 70) $autoCheck[] = 'ck-15';

        // 16 Clean slug
        $path = parse_url($url, PHP_URL_PATH) ?: '/';
        $scores['ck-16'] = (strlen($path) <= 80 && !preg_match('~[A-Z]|[%]~', $path)) ? 90 : 60;

        // 17 Breadcrumbs
        $hasBreadcrumb = $xp->query('//*[contains(@class,"breadcrumb")]')->length > 0 || in_array('BreadcrumbList', $types, true);
        $scores['ck-17'] = $hasBreadcrumb ? 90 : 50;

        // 18 Mobile-friendly (viewport present)
        $scores['ck-18'] = $viewport ? 90 : 40;
        if ($viewport) $autoCheck[] = 'ck-18';

        // 19 Speed (placeholder)
        $scores['ck-19'] = 60;

        // 20 CWV (placeholder)
        $scores['ck-20'] = 60;

        // 21 CTAs
        $cta = $xp->query('//a[contains(@class,"btn") or contains(@class,"button") or contains(@class,"cta")]')->length > 0;
        $scores['ck-21'] = $cta ? 85 : 55;

        // 22 Primary entity (look for main entity of page)
        $hasMainEntity = strpos(strtolower(implode(',', $types)), 'article') !== false || strpos(strtolower(implode(',', $types)), 'product') !== false;
        $scores['ck-22'] = $hasMainEntity ? 80 : 55;

        // 23 Related entities (counts of proper nouns ~ proxy)
        $proper = preg_match_all('~\b[A-Z][a-z]{3,}\b~', $fullText);
        $scores['ck-23'] = $proper >= 6 ? 85 : ($proper ? 65 : 45);

        // 24 Valid schema
        $scores['ck-24'] = count($types) ? 85 : 40;
        if ($scores['ck-24'] >= 70) $autoCheck[] = 'ck-24';

        // 25 sameAs/org (look for Organization or sameAs)
        $hasSameAs = false;
        if ($ld && $ld->length) {
            foreach ($ld as $sn) {
                $json = trim($sn->textContent ?? '');
                if ($json) {
                    $obj = @json_decode($json, true);
                    if (is_array($obj)) {
                        $same = data_get($obj, 'sameAs');
                        if (is_array($same) && count($same)) { $hasSameAs = true; break; }
                    }
                }
            }
        }
        $scores['ck-25'] = $hasSameAs ? 88 : 50;

        // Overall & suggestions
        $per = array_values($scores);
        $overall = (int) round(array_sum($per) / max(1, count($per))); // backend base score
        $suggestions = [
            'ck-6' => ['Keep title 50–65 chars, include primary term early, avoid truncation.'],
            'ck-7' => ['Write a compelling 150–160 char meta with primary + benefit + CTA.'],
            'ck-12'=> ['Cite 2–3 authoritative sources (.gov, .edu, high‑trust publications).'],
            'ck-15'=> ['Add 3–5 contextual internal links to hubs and related pages.'],
            'ck-24'=> ['Add JSON‑LD (Article/FAQ/Product) and validate in Rich Results Test.'],
            'ck-20'=> ['Use PageSpeed Insights & fix LCP/INP/CLS regressions.']
        ];

        return [
            'title' => $title,
            'meta_description_len' => $metaDescLen,
            'canonical' => !!$canonical,
            'robots' => $robots,
            'viewport' => $viewport,
            'counts' => [
                'h1' => $h1, 'h2' => $h2, 'h3' => $h3,
                'internal_links' => $internal
            ],
            'schema' => [
                'found_types' => $types
            ],
            'ai_detection' => $ai,
            'readability' => $read,
            'scores' => $scores,
            'auto_check_ids' => $autoCheck,
            'overall_score' => $overall
        ];
    }
}

/*
|--------------------------------------------------------------------------
| Analyze JSON (POST /analyze.json)
|--------------------------------------------------------------------------
*/
Route::post('/analyze.json', function (Request $req) {
    $url = safe_url((string)$req->input('url', ''));
    if (!$url) {
        return response()->json(['ok' => false, 'error' => 'Missing URL'], 422);
    }
    $res = fetch_html($url);
    if (!$res['ok']) {
        return response()->json(['ok' => false, 'error' => 'Fetch failed', 'detail' => $res['error'] ?? null], 502);
    }
    $data = analyze_document($url, $res['html']);
    $data['status'] = $res['status'];

    // Make sure no "count(): null" errors — always arrays:
    $data['schema']['found_types'] = $data['schema']['found_types'] ?? [];
    $data['auto_check_ids'] = $data['auto_check_ids'] ?? [];
    $data['counts'] = $data['counts'] ?? ['h1'=>0,'h2'=>0,'h3'=>0,'internal_links'=>0];

    return response()->json(['ok' => true] + $data);
})->name('analyze.json');

/*
|--------------------------------------------------------------------------
| PDF Report (POST /report.pdf) — uses barryvdh/laravel-dompdf if present
|--------------------------------------------------------------------------
*/
Route::post('/report.pdf', function (Request $req) {
    $payload = $req->all();
    if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
        /** @var \Barryvdh\DomPDF\Facade\Pdf $pdf */
        $html = view('pdf.report', ['r' => $payload])->render();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('A4', 'portrait');
        return $pdf->download('SEO-Report.pdf');
    }
    // Fallback: return simple text/pdf stream
    $txt = "Semantic SEO Master Report\n\n" . json_encode($payload, JSON_PRETTY_PRINT);
    return Response::make($txt, 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'attachment; filename="SEO-Report.txt.pdf"'
    ]);
})->name('report.pdf');

/*
|--------------------------------------------------------------------------
| Compare (POST /compare.run) — basic DOM diffs of headings & word overlap
|--------------------------------------------------------------------------
*/
Route::post('/compare.run', function (Request $req) {
    $urls = (array)$req->input('urls', []);
    $urls = array_values(array_filter(array_map('safe_url', $urls)));
    if (count($urls) < 2) return response()->json(['ok'=>false,'error'=>'Need 2 URLs'], 422);

    $out = [];
    foreach ($urls as $u) {
        $res = fetch_html($u);
        if (!$res['ok']) { $out[$u] = ['ok'=>false,'status'=>$res['status']]; continue; }
        [$dom, $xp] = dom_xp($res['html']);
        $h2 = []; foreach ($xp->query('//h2') as $n) $h2[] = trim($n->textContent ?? '');
        $h3 = []; foreach ($xp->query('//h3') as $n) $h3[] = trim($n->textContent ?? '');
        $text = extract_full_text($xp);
        $words = array_slice(array_count_values(preg_split('~\W+~u', mb_strtolower($text), -1, PREG_SPLIT_NO_EMPTY)), 0, 0);
        arsort($words);
        $top = array_slice(array_keys($words), 0, 40);
        $out[$u] = ['ok'=>true,'h2'=>$h2,'h3'=>$h3,'top_words'=>$top];
    }
    // overlap
    $overlap = [];
    if (count($out) >= 2) {
        $keys = array_keys($out);
        $a = $out[$keys[0]]['top_words'] ?? [];
        $b = $out[$keys[1]]['top_words'] ?? [];
        $overlap = array_values(array_intersect($a, $b));
    }

    return response()->json(['ok'=>true,'compare'=>$out,'overlap'=>$overlap]);
})->name('compare.run');

/*
|--------------------------------------------------------------------------
| Crawl Sitemap (POST /crawl.sitemap)
|--------------------------------------------------------------------------
*/
Route::post('/crawl.sitemap', function (Request $req) {
    $site = safe_url((string)$req->input('site',''));
    if (!$site) return response()->json(['ok'=>false,'error'=>'Missing site'], 422);
    try {
        $base = rtrim(parse_url($site, PHP_URL_SCHEME).'://'.parse_url($site, PHP_URL_HOST), '/');
        $smUrl = $base . '/sitemap.xml';
        $res = Http::timeout(10)->get($smUrl);
        if (!$res->ok()) return response()->json(['ok'=>false,'error'=>'No sitemap'], 404);

        $xml = @simplexml_load_string($res->body());
        $urls = [];
        if ($xml) {
            // urlset
            foreach ($xml->url ?? [] as $u) {
                $loc = (string)$u->loc;
                if ($loc) $urls[] = $loc;
            }
            // sitemaps index (basic)
            foreach ($xml->sitemap ?? [] as $sm) {
                $loc = (string)$sm->loc;
                if ($loc) $urls[] = $loc;
            }
        }
        return response()->json(['ok'=>true,'sitemap'=>$smUrl,'items'=>$urls]);
    } catch (\Throwable $e) {
        return response()->json(['ok'=>false,'error'=>$e->getMessage()], 500);
    }
})->name('crawl.sitemap');

/*
|--------------------------------------------------------------------------
| PageSpeed Insights (POST /psi.run) — needs API key (env PSI_KEY)
|--------------------------------------------------------------------------
*/
Route::post('/psi.run', function (Request $req) {
    $url = safe_url((string)$req->input('url',''));
    $key = env('PSI_KEY');
    if (!$url) return response()->json(['ok'=>false,'error'=>'Missing URL'], 422);
    if (!$key) return response()->json(['ok'=>false,'error'=>'Set PSI_KEY in .env'], 400);

    try {
        $r = Http::timeout(30)->get('https://www.googleapis.com/pagespeedonline/v5/runPagespeed', [
            'url' => $url, 'key' => $key, 'strategy' => 'mobile'
        ]);
        return response()->json(['ok'=>$r->ok(), 'data'=>$r->json()]);
    } catch (\Throwable $e) {
        return response()->json(['ok'=>false,'error'=>$e->getMessage()], 500);
    }
})->name('psi.run');

/*
|--------------------------------------------------------------------------
| Schema Generate (POST /schema.generate)
|--------------------------------------------------------------------------
*/
Route::post('/schema.generate', function (Request $req) {
    $url  = safe_url((string)$req->input('url',''));
    $type = (string)$req->input('type','Article');

    $tpls = [
        'Article' => [
'@context'=>'https://schema.org','@type'=>'Article',
'headline'=>'Your Article Headline',
'description'=>'150–160 character compelling summary.',
'author'=>['@type'=>'Person','name'=>'Author Name'],
'datePublished'=>date('c'),
'mainEntityOfPage'=>['@type'=>'WebPage','@id'=>$url]
        ],
        'FAQPage' => [
'@context'=>'https://schema.org','@type'=>'FAQPage',
'mainEntity'=>[
 ['@type'=>'Question','name'=>'Question 1','acceptedAnswer'=>['@type'=>'Answer','text'=>'Answer 1']],
 ['@type'=>'Question','name'=>'Question 2','acceptedAnswer'=>['@type'=>'Answer','text'=>'Answer 2']]
]
        ],
        'Product' => [
'@context'=>'https://schema.org','@type'=>'Product',
'name'=>'Product Name','description'=>'Your product description',
'sku'=>'SKU123','brand'=>['@type'=>'Brand','name'=>'Brand'],
'offers'=>['@type'=>'Offer','priceCurrency'=>'USD','price'=>'99.99','availability'=>'https://schema.org/InStock','url'=>$url]
        ],
        'HowTo' => [
'@context'=>'https://schema.org','@type'=>'HowTo','name'=>'How to Do Something',
'description'=>'Short introduction',
'step'=>[
 ['@type'=>'HowToStep','name'=>'Step 1','text'=>'Do something'],
 ['@type'=>'HowToStep','name'=>'Step 2','text'=>'Do the next thing']
]
        ],
    ];
    $json = $tpls[$type] ?? $tpls['Article'];
    return response()->json(['ok'=>true,'schema'=>$json]);
})->name('schema.generate');

/*
|--------------------------------------------------------------------------
| Social Preview (POST /social.preview) — reads OG tags
|--------------------------------------------------------------------------
*/
Route::post('/social.preview', function (Request $req) {
    $url = safe_url((string)$req->input('url',''));
    if (!$url) return response()->json(['ok'=>false,'error'=>'Missing URL'], 422);

    $res = fetch_html($url);
    if (!$res['ok']) return response()->json(['ok'=>false,'error'=>'Fetch failed'], 502);
    [$dom, $xp] = dom_xp($res['html']);

    $get = function($prop) use ($xp) {
        $n = $xp->query('//meta[@property="'.$prop.'"]/@content | //meta[@name="'.$prop.'"]/@content')->item(0);
        return $n ? (string)$n->nodeValue : null;
    };
    $og = [
        'og:title'       => $get('og:title'),
        'og:description' => $get('og:description'),
        'og:image'       => $get('og:image'),
        'twitter:card'   => $get('twitter:card'),
        'twitter:title'  => $get('twitter:title'),
        'twitter:description' => $get('twitter:description'),
        'twitter:image'  => $get('twitter:image'),
    ];
    return response()->json(['ok'=>true,'tags'=>$og]);
})->name('social.preview');

/*
|--------------------------------------------------------------------------
| Entities Gap (POST /entities.gap) — very simple noun-ish frequency diff
|--------------------------------------------------------------------------
*/
Route::post('/entities.gap', function (Request $req) {
    $url = safe_url((string)$req->input('url',''));
    $competitors = (array)$req->input('competitors', []);
    if (!$url) return response()->json(['ok'=>false,'error'=>'Missing URL'], 422);

    $extract = function(string $u){
        $res = fetch_html($u);
        if (!$res['ok']) return ['ok'=>false,'text'=>''];
        [$dom,$xp] = dom_xp($res['html']);
        $txt = extract_full_text($xp);
        $words = preg_split('~\W+~u', mb_strtolower($txt), -1, PREG_SPLIT_NO_EMPTY);
        $counts = array_count_values($words);
        arsort($counts);
        // Keep top alpha tokens 4+ chars
        $top = [];
        foreach ($counts as $w => $c) {
            if (mb_strlen($w) >= 4 && preg_match('~[a-z]~i', $w)) $top[$w] = $c;
            if (count($top) >= 200) break;
        }
        return ['ok'=>true,'text'=>$txt,'top'=>$top];
    };

    $base = $extract($url);
    $compTops = [];
    foreach ($competitors as $c) {
        $c = safe_url($c);
        if (!$c) continue;
        $compTops[] = $extract($c);
    }
    $baseSet = array_keys($base['top'] ?? []);
    $compSet = [];
    foreach ($compTops as $ct) { $compSet = array_merge($compSet, array_keys($ct['top'] ?? [])); }
    $compSet = array_unique($compSet);

    $missing = array_values(array_diff($compSet, $baseSet));
    $unique  = array_values(array_diff($baseSet, $compSet));
    return response()->json(['ok'=>true,'base'=>$baseSet,'competitors'=>$compSet,'missing'=>$missing,'unique'=>$unique]);
})->name('entities.gap');

/*
|--------------------------------------------------------------------------
| Readability (POST /readability.run)
|--------------------------------------------------------------------------
*/
Route::post('/readability.run', function (Request $req) {
    $url = safe_url((string)$req->input('url',''));
    if (!$url) return response()->json(['ok'=>false,'error'=>'Missing URL'], 422);
    $res = fetch_html($url);
    if (!$res['ok']) return response()->json(['ok'=>false,'error'=>'Fetch failed'], 502);
    [$dom,$xp] = dom_xp($res['html']);
    $txt = extract_full_text($xp);
    $r = readability($txt);
    return response()->json(['ok'=>true,'readability'=>$r]);
})->name('readability.run');

/*
|--------------------------------------------------------------------------
| Hreflang Audit (POST /hreflang.audit)
|--------------------------------------------------------------------------
*/
Route::post('/hreflang.audit', function (Request $req) {
    $url = safe_url((string)$req->input('url',''));
    if (!$url) return response()->json(['ok'=>false,'error'=>'Missing URL'], 422);
    $res = fetch_html($url);
    if (!$res['ok']) return response()->json(['ok'=>false,'error'=>'Fetch failed'], 502);
    [$dom,$xp] = dom_xp($res['html']);

    $links = $xp->query('//link[translate(@rel,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="alternate" and @hreflang]/@href');
    $langs = $xp->query('//link[translate(@rel,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="alternate" and @hreflang]/@hreflang');

    $out = [];
    for ($i=0; $links && $i < $links->length; $i++) {
        $out[] = ['hreflang' => $langs->item($i)->nodeValue, 'href' => $links->item($i)->nodeValue];
    }

    // Check return tags quickly (fetch first 5 alternates)
    $issues = [];
    foreach (array_slice($out, 0, 5) as $alt) {
        try {
            $r = Http::timeout(10)->get($alt['href']);
            if (!$r->ok()) { $issues[] = "Alternate not reachable: {$alt['href']}"; continue; }
            [$d,$x] = dom_xp($r->body());
            $ret = $x->query('//link[@rel="alternate" and translate(@hreflang,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="'.strtolower($alt['hreflang']).'" and @href]')->length > 0;
            if (!$ret) $issues[] = "No return hreflang from {$alt['href']} back to {$alt['hreflang']}";
        } catch (\Throwable $e) {
            $issues[] = "Error checking {$alt['href']}: ".$e->getMessage();
        }
    }

    return response()->json(['ok'=>true,'alternates'=>$out,'issues'=>$issues]);
})->name('hreflang.audit');

/*
|--------------------------------------------------------------------------
| Share Save (POST /share.save) — cache for 24h and return an id
|--------------------------------------------------------------------------
*/
Route::post('/share.save', function (Request $req) {
    $report = $req->input('report', []);
    $id = Str::uuid()->toString();
    Cache::put('share:'.$id, $report, now()->addHours(24));
    return response()->json(['ok'=>true,'id'=>$id,'view'=>url('/share/'.$id)]);
})->name('share.save');

Route::get('/share/{id}', function (string $id) {
    $data = Cache::get('share:'.$id);
    if (!$data) abort(404);
    return view('shared', ['report' => $data, 'id'=>$id]);
});

/*
|--------------------------------------------------------------------------
| Export CSV (POST /export.csv)
|--------------------------------------------------------------------------
*/
Route::post('/export.csv', function (Request $req) {
    $rows = (array)$req->input('rows', []);
    $csv = fopen('php://temp', 'r+');
    fputcsv($csv, ['Item','Score']);
    foreach ($rows as $r) {
        fputcsv($csv, [$r['item'] ?? '', $r['score'] ?? '']);
    }
    rewind($csv);
    $content = stream_get_contents($csv);
    fclose($csv);
    return Response::make($content, 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="seo-export.csv"'
    ]);
})->name('export.csv');
