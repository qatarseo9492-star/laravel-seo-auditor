<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('home'); // resources/views/home.blade.php
})->name('home');

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

if (!function_exists('safe_url')) {
    function safe_url(string $u): string {
        $u = trim($u);
        if ($u === '') return '';
        if (!preg_match('~^https?://~i', $u)) $u = 'https://' . ltrim($u, '/');
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
            if (strlen(implode(' ', $parts)) > 3000) break;
        }
        $text = preg_replace('~\s+~', ' ', implode(' ', $parts));
        return trim($text);
    }
}

if (!function_exists('readability')) {
    function readability(string $text): array {
        if (!$text) return ['fre' => 0, 'fk' => 0];
        $words = preg_split('~\s+~u', trim($text), -1, PREG_SPLIT_NO_EMPTY);
        $sents = preg_split('~[\.!\?]+~u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $w = max(1, count($words));
        $s = max(1, count($sents));
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

if (!function_exists('ai_human_detect')) {
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
            if ($aiScore >= 2) $ai_like[] = trim($s); else $human_like[] = trim($s);
        }
        $total = max(1, count($sents));
        $aiPct = (int) round(count($ai_like) / $total * 100);
        $humanPct = 100 - $aiPct;
        $label = $aiPct >= 60 ? 'likely_ai' : ($aiPct >= 35 ? 'mixed' : 'likely_human');
        return [
            'label' => $label,
            'likelihood' => abs(50 - abs(50 - $aiPct)),
            'ai_pct' => $aiPct,
            'human_pct' => $humanPct,
            'reasons' => [
                'Heuristics: sentence length, passive voice, hedging, punctuation density',
                'Indicative only'
            ],
            'ai_sentences' => array_slice($ai_like, 0, 40),
            'human_sentences' => array_slice($human_like, 0, 40),
            'full_text' => $text
        ];
    }
}

/*
|--------------------------------------------------------------------------
| Core analyzer (strict 25 checks)
|--------------------------------------------------------------------------
*/

if (!function_exists('analyze_document')) {
    function analyze_document(string $url, string $html): array {
        [$dom, $xp] = dom_xp($html);

        // basic fields
        $title = node_text($xp->query('//title')->item(0));
        $metaDescNode = $xp->query(
            '//meta[translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="description"]'
        )->item(0);
        $metaDesc = $metaDescNode ? $metaDescNode->getAttribute('content') : '';
        $metaDescLen = mb_strlen($metaDesc);

        $canon = $xp->query('//link[translate(@rel,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="canonical"]/@href')->item(0);
        $canonical = $canon ? $canon->nodeValue : null;

        $robotsNode = $xp->query('//meta[translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="robots"]/@content')->item(0);
        $robots = $robotsNode ? $robotsNode->nodeValue : null;

        $viewportNode = $xp->query('//meta[translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="viewport"]')->item(0);
        $viewport = (bool)$viewportNode;

        $h1 = $xp->query('//h1')->length;
        $h2 = $xp->query('//h2')->length;
        $h3 = $xp->query('//h3')->length;

        // internal links
        $internal = 0;
        $a = $xp->query('//a[@href]');
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

        // schema types
        $types = [];
        $ld = $xp->query('//script[@type="application/ld+json"]');
        if ($ld && $ld->length) {
            foreach ($ld as $sn) {
                $json = trim($sn->textContent ?? '');
                if ($json) {
                    $obj = @json_decode($json, true);
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
                }
            }
        }
        $types = array_values(array_filter(array_unique($types)));

        // sitemap probe
        $xmlMap = false;
        try {
            $host = parse_url($url, PHP_URL_SCHEME) . '://' . parse_url($url, PHP_URL_HOST);
            $sitemapUrl = rtrim($host, '/') . '/sitemap.xml';
            $sm = Http::timeout(6)->get($sitemapUrl);
            $xmlMap = $sm->ok();
        } catch (\Throwable $e) {}

        // text + analyses
        $fullText = extract_full_text($xp);
        $ai = ai_human_detect($fullText);
        $read = readability($fullText);

        $path = parse_url($url, PHP_URL_PATH) ?: '/';

        // --- 25 checks ---
        $scores = [];
        $autoCheck = [];
        $suggestions = [];

        $lenBetween = function (?string $s, int $min, int $max): bool {
            $l = mb_strlen((string)$s);
            return $l >= $min && $l <= $max;
        };
        $hasQMarks = function (string $s): int { return substr_count($s, '?'); };
        $textHas = function (string $needle) use ($fullText): bool {
            return stripos($fullText, $needle) !== false;
        };
        $hasSchemaType = function (string $type) use ($types): bool {
            foreach ($types as $t) if (is_string($t) && stripos($t, $type) !== false) return true;
            return false;
        };

        $wc       = max(0, str_word_count($fullText));
        $faqType  = $hasSchemaType('FAQPage');
        $articleT = $hasSchemaType('Article');
        $productT = $hasSchemaType('Product');
        $howtoT   = $hasSchemaType('HowTo');

        $imgCount = $xp->query('//img')->length;
        $videoCnt = $xp->query('//video|//iframe[contains(@src,"youtube") or contains(@src,"vimeo")]')->length;
        $mediaCnt = $imgCount + $videoCnt;

        $breadcrumbs = $xp->query('//*[contains(@class,"breadcrumb") or contains(@class,"breadcrumbs")]')->length > 0 || $hasSchemaType('BreadcrumbList');

        $slugClean = function (string $p): bool {
            if ($p === '' || $p === '/') return false;
            if (preg_match('~[A-Z]~', $p)) return false;
            if (strlen($p) > 100) return false;
            if (preg_match('~(%[0-9A-Fa-f]{2})~', $p)) return false;
            return true;
        };

        $indexable = !preg_match('~noindex~i', (string)$robots);
        $hasCTA = $xp->query('//a[contains(translate(@class,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz"),"btn") or contains(translate(@class,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz"),"button") or contains(translate(@class,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz"),"cta")]')->length > 0;

        // 1
        $intentOk = ($h1 > 0 || (mb_strlen($title) >= 20 && $wc >= 300));
        $scores['ck-1'] = $intentOk ? 80 : ($wc >= 150 ? 55 : 25);
        if ($intentOk) $autoCheck[] = 'ck-1';
        if (!$intentOk) $suggestions['ck-1'][] = 'State the primary intent in H1 and intro paragraph.';

        // 2
        $subheads = $h2 + $h3;
        $variety = preg_match_all('~\b\w{5,}\b~u', $fullText);
        $kmap = ($subheads >= 4 && $variety >= 200);
        $scores['ck-2'] = $kmap ? 82 : ($subheads >= 2 ? 65 : 40);
        if ($kmap) $autoCheck[] = 'ck-2';
        if (!$kmap) $suggestions['ck-2'][] = 'Add related subtopics as H2/H3; address PAA questions.';

        // 3
        $h1Nodes = $xp->query('//h1');
        $h1Text  = $h1Nodes->length ? trim($h1Nodes->item(0)->textContent ?? '') : '';
        $h1Ok    = ($h1 > 0 && mb_strlen($h1Text) >= 12);
        $scores['ck-3'] = $h1Ok ? 85 : ($h1 > 0 ? 60 : 20);
        if ($h1Ok) $autoCheck[] = 'ck-3';
        if (!$h1Ok) $suggestions['ck-3'][] = 'Write a descriptive H1 (~6–12 words) with the primary topic.';

        // 4
        $faqOk = ($faqType || $hasQMarks($fullText) >= 3);
        $scores['ck-4'] = $faqOk ? 88 : 45;
        if ($faqType) $autoCheck[] = 'ck-4';
        if (!$faqOk) $suggestions['ck-4'][] = 'Add a short FAQ section and FAQPage schema.';

        // 5
        $fre = $read['fre'] ?? 0;
        $scores['ck-5'] = ($fre >= 60 ? 88 : ($fre >= 50 ? 75 : 55));
        if ($fre >= 60 && $wc >= 300) $autoCheck[] = 'ck-5';
        if ($fre < 60) $suggestions['ck-5'][] = 'Shorten sentences, use simpler words, add subheads.';

        // 6
        $titleOk = $lenBetween($title, 50, 65);
        $scores['ck-6'] = $titleOk ? 90 : ($title ? 65 : 10);
        if ($titleOk) $autoCheck[] = 'ck-6';
        if (!$titleOk) $suggestions['ck-6'][] = 'Keep title around 50–60 chars; lead with the primary term.';

        // 7
        $metaOk = ($metaDescLen >= 140 && $metaDescLen <= 170);
        $scores['ck-7'] = $metaOk ? 90 : ($metaDescLen ? 65 : 15);
        if ($metaOk) $autoCheck[] = 'ck-7';
        if (!$metaOk) $suggestions['ck-7'][] = 'Write a compelling 150–160 char meta with benefit + CTA.';

        // 8
        $scores['ck-8'] = $canonical ? 100 : 20;
        if ($canonical) $autoCheck[] = 'ck-8';
        if (!$canonical) $suggestions['ck-8'][] = 'Add a canonical link to the preferred URL.';

        // 9
        $scores['ck-9'] = ($indexable && $xmlMap) ? 92 : ($indexable ? 72 : 15);
        if ($indexable && $xmlMap) $autoCheck[] = 'ck-9';
        if (!$indexable) $suggestions['ck-9'][] = 'Remove “noindex” if this page should rank.';
        if (!$xmlMap)   $suggestions['ck-9'][] = 'Add page to XML sitemap and resubmit in Search Console.';

        // 10
        $hasAuthor = $xp->query('//*[@itemprop="author"]|//*[@rel="author"]|//*[contains(translate(@class,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz"),"author")]')->length > 0;
        $hasDate   = $xp->query('//*[@datetime or contains(translate(@class,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz"),"date")]')->length > 0;
        $eeatOk    = ($hasAuthor || $hasDate);
        $scores['ck-10'] = $eeatOk ? 82 : 55;
        if ($eeatOk) $autoCheck[] = 'ck-10';
        if (!$eeatOk) $suggestions['ck-10'][] = 'Show author name/credentials and publish/update dates.';

        // 11
        $uniqueSignals = ($wc >= 900) + ($mediaCnt >= 2) + (int)$textHas(' vs ') + (int)$textHas('comparison');
        $scores['ck-11'] = $uniqueSignals >= 2 ? 80 : ($wc >= 600 ? 68 : 50);
        if ($uniqueSignals >= 2) $autoCheck[] = 'ck-11';
        if ($uniqueSignals < 2) $suggestions['ck-11'][] = 'Add unique insights, original data, or comparisons.';

        // 12
        $externalLinks = 0;
        if ($a) {
            foreach ($a as $node) {
                $href = $node->getAttribute('href');
                if (preg_match('~^https?://~i', $href)) $externalLinks++;
            }
        }
        $scores['ck-12'] = $externalLinks >= 3 ? 86 : ($externalLinks ? 66 : 40);
        if ($externalLinks >= 3) $autoCheck[] = 'ck-12';
        if ($externalLinks < 3) $suggestions['ck-12'][] = 'Cite 2–3 authoritative sources with descriptive anchors.';

        // 13
        $mediaOk = $mediaCnt >= 2;
        $scores['ck-13'] = $mediaOk ? 85 : ($mediaCnt ? 70 : 45);
        if ($mediaOk) $autoCheck[] = 'ck-13';
        if (!$mediaOk) $suggestions['ck-13'][] = 'Add descriptive images/charts or a short explainer video.';

        // 14
        $structureOk = ($subheads >= 4);
        $scores['ck-14'] = $structureOk ? 86 : ($subheads ? 70 : 48);
        if ($structureOk) $autoCheck[] = 'ck-14';
        if (!$structureOk) $suggestions['ck-14'][] = 'Use H2 for major subtopics, H3 for subsections.';

        // 15
        $scores['ck-15'] = $internal >= 5 ? 85 : ($internal ? 65 : 40);
        if ($internal >= 5) $autoCheck[] = 'ck-15';
        if ($internal < 5) $suggestions['ck-15'][] = 'Add 3–5 contextual internal links to pillar/related pages.';

        // 16
        $slugOk = $slugClean($path ?: '/');
        $scores['ck-16'] = $slugOk ? 90 : 60;
        if ($slugOk) $autoCheck[] = 'ck-16';
        if (!$slugOk) $suggestions['ck-16'][] = 'Shorten/simplify slug (lowercase, hyphens, ≤100 chars).';

        // 17
        $scores['ck-17'] = $breadcrumbs ? 92 : 55;
        if ($breadcrumbs) $autoCheck[] = 'ck-17';
        if (!$breadcrumbs) $suggestions['ck-17'][] = 'Add breadcrumb nav and BreadcrumbList schema.';

        // 18
        $scores['ck-18'] = $viewport ? 95 : 40;
        if ($viewport) $autoCheck[] = 'ck-18';
        if (!$viewport) $suggestions['ck-18'][] = 'Add meta viewport and ensure responsive layout.';

        // 19
        $lazyImgs = $xp->query('//img[@loading="lazy"]')->length;
        $webpImgs = $xp->query('//img[contains(translate(@src,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz"),".webp")]')->length;
        $perfOk = ($lazyImgs >= 3 || $webpImgs >= 2);
        $scores['ck-19'] = $perfOk ? 78 : 58;
        if ($perfOk) $autoCheck[] = 'ck-19';
        if (!$perfOk) $suggestions['ck-19'][] = 'Use WebP/AVIF, lazy‑load images, defer non‑critical JS.';

        // 20
        $scores['ck-20'] = 60;
        $suggestions['ck-20'][] = 'Run PSI (mobile); fix LCP<2.5s, INP<200ms, CLS<0.1.';

        // 21
        $scores['ck-21'] = $hasCTA ? 84 : 56;
        if ($hasCTA) $autoCheck[] = 'ck-21';
        if (!$hasCTA) $suggestions['ck-21'][] = 'Add a clear CTA (“Try”, “Download”, “Get a quote”).';

        // 22
        $entityOk = ($articleT || $productT || $howtoT || ($h1Ok && $wc >= 300));
        $scores['ck-22'] = $entityOk ? 82 : 58;
        if ($entityOk) $autoCheck[] = 'ck-22';
        if (!$entityOk) $suggestions['ck-22'][] = 'Define the main entity in intro and add matching schema.';

        // 23
        $proper = preg_match_all('~\b[A-Z][a-z]{3,}\b~u', $fullText);
        $scores['ck-23'] = $proper >= 6 ? 84 : ($proper ? 66 : 48);
        if ($proper >= 6) $autoCheck[] = 'ck-23';
        if ($proper < 6) $suggestions['ck-23'][] = 'Mention related entities (brands, places, standards) with context.';

        // 24
        $schemaOk = ($articleT || $productT || $howtoT || $faqType);
        $scores['ck-24'] = $schemaOk ? 90 : 45;
        if ($schemaOk) $autoCheck[] = 'ck-24';
        if (!$schemaOk) $suggestions['ck-24'][] = 'Add JSON‑LD (Article/FAQ/Product/HowTo) and validate.';

        // 25
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
        $scores['ck-25'] = $hasSameAs ? 88 : 52;
        if ($hasSameAs) $autoCheck[] = 'ck-25';
        if (!$hasSameAs) $suggestions['ck-25'][] = 'Add Organization/Person schema with “sameAs” profile links.';

        $overall = (int) round(array_sum($scores) / max(1, count($scores)));

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
            'schema' => ['found_types' => $types],
            'ai_detection' => $ai,
            'readability' => $read,
            'scores' => $scores,
            'auto_check_ids' => array_values(array_unique($autoCheck)),
            'overall_score' => $overall,
            'suggestions' => $suggestions
        ];
    }
}

/*
|--------------------------------------------------------------------------
| API: Analyze (POST) — used by Blade via route('analyze.json')
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

    // safety: ensure arrays
    $data['schema']['found_types'] = $data['schema']['found_types'] ?? [];
    $data['auto_check_ids'] = $data['auto_check_ids'] ?? [];
    $data['counts'] = $data['counts'] ?? ['h1'=>0,'h2'=>0,'h3'=>0,'internal_links'=>0];

    return response()->json(['ok' => true] + $data);
})->name('analyze.json');
