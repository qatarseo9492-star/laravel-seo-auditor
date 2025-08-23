<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('home'); // resources/views/home.blade.php
})->name('home');

if (!function_exists('analyzeCore')) {
    function analyzeCore(string $inputUrl): array
    {
        // helpers (no arrow functions for PHP 7.x)
        $clamp = function ($v, $min, $max) {
            $v = (float)$v;
            return max($min, min($max, $v));
        };

        // Normalize scheme
        $url = trim($inputUrl);
        if (!preg_match('~^https?://~i', $url)) {
            $url = 'https://' . ltrim($url, '/');
        }

        // --- Fetch -----------------------------------------------------------
        $html = '';
        $httpStatus = 0;

        // Try Laravel HTTP client first
        try {
            $resp = Http::timeout(15)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; SemanticSEOMaster/1.0; +https://example.com/bot)',
                    'Accept'     => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
                ])->get($url);

            $httpStatus = $resp->status();
            if ($resp->successful()) {
                $html = (string)$resp->body();
            }
        } catch (\Throwable $e) {}

        // Fallback: cURL (for hosts blocking Guzzle or file_get_contents)
        if (trim($html) === '' && function_exists('curl_init')) {
            try {
                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_MAXREDIRS => 5,
                    CURLOPT_TIMEOUT => 15,
                    CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; SemanticSEOMaster/1.0)',
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_HTTPHEADER => ['Accept: text/html']
                ]);
                $body = curl_exec($ch);
                if ($body !== false) {
                    $html = (string)$body;
                    $httpStatus = $httpStatus ?: (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
                }
                curl_close($ch);
            } catch (\Throwable $e) {}
        }

        // Fallback: file_get_contents
        if (trim($html) === '') {
            try {
                $ctx = stream_context_create([
                    'http' => [
                        'method'  => 'GET',
                        'timeout' => 15,
                        'header'  => "User-Agent: Mozilla/5.0 (compatible; SemanticSEOMaster/1.0)\r\nAccept: text/html\r\n",
                    ],
                    'ssl' => [
                        'verify_peer'      => false,
                        'verify_peer_name' => false,
                    ],
                ]);
                $html = @file_get_contents($url, false, $ctx) ?: '';
                $httpStatus = $httpStatus ?: 200;
            } catch (\Throwable $e) {}
        }

        if (trim($html) === '') {
            return [
                'overall'       => 0,
                'contentScore'  => 0,
                'humanPct'      => 0,
                'aiPct'         => 100,
                'httpStatus'    => $httpStatus ?: '—',
                'titleLen'      => 0,
                'metaLen'       => 0,
                'canonical'     => '—',
                'robots'        => '—',
                'viewport'      => '—',
                'headings'      => '—',
                'internalLinks' => '—',
                'schema'        => '—',
                'itemScores'    => [],
                'error'         => 'Empty or unreachable URL',
            ];
        }

        // --- Parse -----------------------------------------------------------
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        // make encoding safe-ish for older pages
        $htmlUtf8 = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        @$dom->loadHTML($htmlUtf8);
        $xp  = new \DOMXPath($dom);

        // Small helpers
        $textOf = function ($query) use ($xp) {
            $nodes = $xp->query($query);
            $buf = [];
            if ($nodes) {
                foreach ($nodes as $n) { $buf[] = trim($n->textContent ?: ''); }
            }
            return trim(implode(' ', array_filter($buf)));
        };
        $attrOfAll = function ($query, $attrName) use ($xp) {
            $nodes = $xp->query($query);
            $out = [];
            if ($nodes) {
                foreach ($nodes as $n) {
                    if ($n instanceof \DOMElement && $n->hasAttribute($attrName)) {
                        $out[] = trim($n->getAttribute($attrName));
                    }
                }
            }
            return $out;
        };
        $firstAttr = function ($query, $attrName) use ($attrOfAll) {
            $all = $attrOfAll($query, $attrName);
            return isset($all[0]) ? $all[0] : null;
        };
        $exists = function ($query) use ($xp) {
            $nodes = $xp->query($query);
            return $nodes && $nodes->length > 0;
        };

        // Core extracts
        $title    = $textOf('//title');
        $metaDesc = $firstAttr('//meta[translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="description"]', 'content');
        if (!$metaDesc) $metaDesc = $firstAttr('//meta[translate(@property,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="og:description"]', 'content');
        $metaDesc = $metaDesc ?: '';

        $canonical = $firstAttr('//link[translate(@rel,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="canonical"]', 'href') ?: '';
        $robots    = $firstAttr('//meta[translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="robots"]', 'content') ?: '';
        $viewport  = $firstAttr('//meta[translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="viewport"]', 'content') ?: '';

        $h1Query = $xp->query('//h1'); $h1Count = $h1Query ? $h1Query->length : 0;
        $h2Query = $xp->query('//h2'); $h2Count = $h2Query ? $h2Query->length : 0;
        $h3Query = $xp->query('//h3'); $h3Count = $h3Query ? $h3Query->length : 0;

        // Host for internal links
        $host = parse_url($url, PHP_URL_HOST);
        $allLinks = $attrOfAll('//a[@href]', 'href');
        $internalCount = 0;
        foreach ($allLinks as $href) {
            $hrefTrim = trim($href);
            if ($hrefTrim === '' || Str::startsWith($hrefTrim, ['mailto:', 'tel:', '#'])) {
                continue;
            }
            if (Str::startsWith($hrefTrim, '/')) {
                $internalCount++;
            } else {
                $linkHost = parse_url($hrefTrim, PHP_URL_HOST);
                if ($linkHost && $host && Str::endsWith($linkHost, $host)) {
                    $internalCount++;
                }
            }
        }

        // JSON-LD schema detection
        $schemaTypes = [];
        $scripts = $xp->query('//script[@type="application/ld+json"]');
        if ($scripts) {
            foreach ($scripts as $script) {
                $json = trim($script->textContent ?: '');
                if ($json === '') continue;
                $json = preg_replace('/\/\*.*?\*\//s', '', $json);
                try {
                    $data = json_decode($json, true, 512, JSON_INVALID_UTF8_IGNORE);
                    $stack = [$data];
                    while (!empty($stack)) {
                        $node = array_pop($stack);
                        if (is_array($node)) {
                            if (isset($node['@type'])) {
                                $t = is_array($node['@type']) ? implode(',', $node['@type']) : (string)$node['@type'];
                                $schemaTypes[] = $t;
                            }
                            foreach ($node as $v) { if (is_array($v)) $stack[] = $v; }
                        }
                    }
                } catch (\Throwable $e) {}
            }
        }

        // Visible text amalgam
        $textNodes = $xp->query('//p|//li|//article//text()[normalize-space()]');
        $bodyText = '';
        if ($textNodes) {
            $frags = []; $max = 5000; $len = 0;
            foreach ($textNodes as $n) {
                $t = trim($n->textContent ?: '');
                if ($t === '') continue;
                $frags[] = $t; $len += strlen($t);
                if ($len > $max) break;
            }
            $bodyText = trim(implode(' ', $frags));
        }

        // Simple NLP-ish helpers
        $lower = Str::lower($bodyText);
        $words = preg_split('/[^a-zA-Z0-9\']+/u', Str::ascii($lower));
        $words = array_values(array_filter($words, function ($w) { return $w !== '' && !preg_match('/^\d+$/', $w); }));
        $stop = [
            'the','and','for','that','with','your','you','are','was','were','this','from','have','has','had','but','not','all','any','can','will','about','into','over','than','then','they','them','their','our','out','his','her','its','what','when','where','how','why','which','who','whom','on','in','to','of','a','an','is','it','as','by','or'
        ];
        $freq = [];
        foreach ($words as $w) {
            if (strlen($w) < 4 || in_array($w, $stop, true)) continue;
            $freq[$w] = isset($freq[$w]) ? $freq[$w] + 1 : 1;
        }
        arsort($freq);
        $mainKeyword = $freq ? array_keys($freq)[0] : '';

        // Sentences
        $sentences = preg_split('/(?<=[\.!\?])\s+/u', $bodyText);
        $sentences = array_values(array_filter($sentences, function ($s) { return trim($s) !== ''; }));
        $sentLens = array_map('str_word_count', $sentences);
        $avgSent = count($sentLens) ? array_sum($sentLens)/count($sentLens) : 0.0;
        $varSent = 0.0;
        if (count($sentLens) > 1) {
            $m = $avgSent; $acc = 0.0;
            foreach ($sentLens as $sl) { $acc += ($sl - $m) * ($sl - $m); }
            $varSent = sqrt($acc / (count($sentLens)-1));
        }

        // Title/Meta lengths
        $titleLen = mb_strlen($title);
        $metaLen  = mb_strlen($metaDesc);

        // Headings chip
        $headingsChip = "H1:{$h1Count} H2:{$h2Count} H3:{$h3Count}";

        // Robots indexability
        $isNoindex = Str::contains(Str::lower($robots), 'noindex');
        $indexable = !$isNoindex;

        // Viewport presence
        $hasViewport = $viewport !== null && trim($viewport) !== '';

        // Images / media
        $imgQuery = $xp->query('//img');   $imgCount   = $imgQuery ? $imgQuery->length : 0;
        $vidQuery = $xp->query('//video|//iframe[contains(@src,"youtube") or contains(@src,"vimeo")]');
        $videoCount = $vidQuery ? $vidQuery->length : 0;

        // Canonical ok if present
        $canonicalOk = $canonical ? true : false;

        // Author/date heuristics
        $hasAuthorMeta = $firstAttr('//meta[translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="author"]', 'content') ? true : false;
        $hasPublished  = $firstAttr('//meta[translate(@property,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="article:published_time"]', 'content')
                         || $exists('//time[@datetime]');

        // Breadcrumbs
        $hasBreadcrumb = $exists('//*[@aria-label="breadcrumb"]')
                      || $exists('//nav[contains(translate(@class,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz"),"breadcrumb")]');
        if (!$hasBreadcrumb && !empty($schemaTypes)) {
            foreach ($schemaTypes as $t) {
                if (Str::contains(Str::lower($t), 'breadcrumb')) { $hasBreadcrumb = true; break; }
            }
        }

        // FAQ presence
        $hasFAQSchema = false;
        if (!empty($schemaTypes)) {
            foreach ($schemaTypes as $t) {
                if (Str::contains(Str::lower($t), 'faq')) { $hasFAQSchema = true; break; }
            }
        }

        // Schema / entities
        $hasSchema = !empty($schemaTypes);
        $hasArticleLike = false;
        if (!empty($schemaTypes)) {
            foreach ($schemaTypes as $t) {
                $tl = Str::lower($t);
                if (Str::contains($tl, 'article') || Str::contains($tl, 'newsarticle') || Str::contains($tl, 'blogposting')
                    || Str::contains($tl, 'product') || Str::contains($tl, 'organization') || Str::contains($tl, 'webpage')) {
                    $hasArticleLike = true; break;
                }
            }
        }

        // sameAs links
        $sameAsCount = 0;
        if ($scripts) {
            foreach ($scripts as $script) {
                $json = trim($script->textContent ?: '');
                if ($json === '') continue;
                try {
                    $data = json_decode($json, true, 512, JSON_INVALID_UTF8_IGNORE);
                    if (isset($data['sameAs']) && is_array($data['sameAs'])) {
                        $sameAsCount += count($data['sameAs']);
                    }
                } catch (\Throwable $e) {}
            }
        }

        // URL slug quality
        $path = parse_url($url, PHP_URL_PATH) ?: '/';
        $slug = trim($path, '/');
        $slugScore = 70;
        if ($slug === '') { $slugScore = 65; }
        else {
            $slugScore = 80;
            if (strlen($slug) > 100) $slugScore -= 20;
            if (!Str::contains($slug, '-')) $slugScore -= 10;
            if (preg_match('/[^\pL\pN\-\/]/u', $slug)) $slugScore -= 15;
            $slugScore = $clamp($slugScore, 40, 95);
        }

        // Readability score
        $readScore = 70;
        if ($avgSent >= 12 && $avgSent <= 24) $readScore = 90;
        elseif ($avgSent >= 8 && $avgSent <= 30) $readScore = 80;
        else $readScore = 58;
        if ($titleLen > 65) $readScore -= 5;

        // Content coverage / entities heuristic
        $properNouns = 0;
        if (preg_match_all('/\b[A-Z][a-z]{2,}\b/u', $bodyText, $m)) {
            $properNouns = count(array_unique($m[0]));
        }
        $entityScore = $clamp(60 + min(30, $properNouns * 2), 50, 95);

        // Human vs AI heuristic
        $human = 50;
        if ($avgSent >= 12 && $avgSent <= 24) $human += 15;
        if ($varSent >= 6)  $human += 10;
        if (preg_match('/\b(I|we|my|our|me|us)\b/i', $bodyText)) $human += 8;
        if (preg_match("/\\b(as an ai|i am an ai|language model)\\b/i", $bodyText)) $human -= 35;

        // Repetition penalty
        $trigrams = [];
        $tokens = preg_split('/\s+/', Str::lower(strip_tags($bodyText)));
        $tokCount = is_array($tokens) ? count($tokens) : 0;
        for ($i=0; $i < max(0, $tokCount-2); $i++) {
            $tri = $tokens[$i].' '.$tokens[$i+1].' '.$tokens[$i+2];
            $trigrams[$tri] = isset($trigrams[$tri]) ? $trigrams[$tri] + 1 : 1;
        }
        $repMax = empty($trigrams) ? 1 : max($trigrams);
        if ($repMax >= 6)      $human -= 15;
        elseif ($repMax >= 4)  $human -= 8;

        $humanPct = (int)$clamp($human, 0, 100);
        $aiPct    = 100 - $humanPct;

        // --- Score checklist items (1..25) -----------------------------------
        $item = [];
        $scoreBool = function ($cond, $good=90, $bad=50) { return $cond ? $good : $bad; };
        $scoreBand = function ($value, $min, $max) use ($clamp) {
            $value = (int)$value;
            if ($value <= 0) return 50;
            $ratio = ($value - $min) / max(1, ($max - $min));
            return (int)$clamp(50 + $ratio * 45, 50, 95);
        };

        $h1Text = $textOf('//h1');

        $item[1]  = $scoreBool( $mainKeyword !== '' && (Str::contains(Str::lower($title), $mainKeyword) || Str::contains(Str::lower($h1Text), $mainKeyword)) );
        $item[2]  = $scoreBand((int)min(12, count(array_slice(array_keys($freq), 0, 12))), 4, 12);
        $item[3]  = $scoreBool( $mainKeyword !== '' && Str::contains(Str::lower($h1Text), $mainKeyword) );
        $item[4]  = $scoreBool( $hasFAQSchema || preg_match('/\b(FAQ|Frequently Asked|How|What|Why|When|Where)\b/i', $bodyText) );
        $item[5]  = $readScore;
        $item[6]  = ($titleLen >= 50 && $titleLen <= 60) ? 92 : (($titleLen >= 35 && $titleLen <= 65) ? 80 : 58);
        $item[7]  = ($metaLen >= 140 && $metaLen <= 160) ? 92 : (($metaLen >= 120 && $metaLen <= 180) ? 80 : ($metaLen > 0 ? 60 : 50));
        $item[8]  = $scoreBool($canonicalOk);
        $item[9]  = $indexable ? 88 : 50;
        $item[10] = $scoreBool($hasAuthorMeta || $hasPublished, 86, 58);
        $item[11] = (int)$entityScore;
        $item[12] = $scoreBool(preg_match('/\b(2022|2023|2024|2025)\b/', $bodyText) || $hasPublished, 84, 62);
        $item[13] = $scoreBand($imgCount + $videoCount*2, 1, 8);
        $item[14] = $scoreBand($h2Count + (int)floor($h3Count/2), 2, 12);
        $item[15] = $scoreBand($internalCount, 3, 20);
        $item[16] = $slugScore;
        $item[17] = $scoreBool($hasBreadcrumb);
        $item[18] = $scoreBool($hasViewport, 90, 55);
        $item[19] = $clamp(85 - max(0, $imgCount - 12) * 2, 55, 90);
        $item[20] = 65;
        $item[21] = $scoreBool($exists('//button') || preg_match('/\b(contact|buy|shop|subscribe|sign up|download|get started|try now)\b/i', $bodyText), 86, 62);
        $item[22] = $scoreBool($mainKeyword !== '' && Str::contains(Str::lower($textOf('(//p)[1]')), $mainKeyword), 88, 60);
        $item[23] = $entityScore;
        $item[24] = $scoreBool($hasSchema && $hasArticleLike, 90, 60);
        $item[25] = $scoreBand($sameAsCount, 1, 6);

        // Overall & Content
        $allScores = array_values($item);
        $overall = (int) round(array_sum($allScores) / max(1, count($allScores)));

        $contentKeys = [1,2,3,4,5,10,11,12,13];
        $sum=0; $cnt=0; foreach ($contentKeys as $k){ if (isset($item[$k])) { $sum += $item[$k]; $cnt++; } }
        $contentScore = (int) round($sum / max(1, $cnt));

        // Build item map with string keys 1..25
        $itemMap = [];
        for ($i=1;$i<=25;$i++){ $itemMap[(string)$i] = isset($item[$i]) ? $item[$i] : 0; }

        return [
            'overall'       => $overall,
            'contentScore'  => $contentScore,
            'humanPct'      => (int)$humanPct,
            'aiPct'         => (int)$aiPct,

            'httpStatus'    => $httpStatus ?: '—',
            'titleLen'      => $titleLen ?: 0,
            'metaLen'       => $metaLen ?: 0,
            'canonical'     => $canonical ?: '—',
            'robots'        => $robots ?: '—',
            'viewport'      => $hasViewport ? 'yes' : 'no',
            'headings'      => $headingsChip,
            'internalLinks' => $internalCount,
            'schema'        => empty($schemaTypes) ? 'none' : implode(',', array_unique($schemaTypes)),

            'itemScores'    => $itemMap,
        ];
    }
}

/** POST /analyze — JSON body: { url: "..." } (CSRF might block; we also have GET) */
Route::post('/analyze', function (Request $request) {
    $request->validate(['url' => ['required', 'string', 'min:4']]);
    return response()->json(analyzeCore($request->input('url', '')), 200);
})->name('analyze');

/** GET /analyze — Query: ?url=https://... (fallback) */
Route::get('/analyze', function (Request $request) {
    $u = $request->query('url', '');
    if (!is_string($u) || trim($u) === '') {
        return response()->json([
            'overall'=>0,'contentScore'=>0,'humanPct'=>0,'aiPct'=>100,
            'httpStatus'=>'—','titleLen'=>0,'metaLen'=>0,'canonical'=>'—','robots'=>'—','viewport'=>'—',
            'headings'=>'—','internalLinks'=>'—','schema'=>'—','itemScores'=>[],
            'error'=>'Missing ?url parameter',
        ], 200);
    }
    return response()->json(analyzeCore($u), 200);
})->name('analyze.get');

/** ANY /analyze-json — safest alias and supports GET without CSRF */
Route::any('/analyze-json', function (Request $request) {
    $u = $request->input('url', $request->query('url', ''));
    if (!is_string($u) || trim($u) === '') {
        return response()->json([
            'overall'=>0,'contentScore'=>0,'humanPct'=>0,'aiPct'=>100,
            'httpStatus'=>'—','titleLen'=>0,'metaLen'=>0,'canonical'=>'—','robots'=>'—','viewport'=>'—',
            'headings'=>'—','internalLinks'=>'—','schema'=>'—','itemScores'=>[],
            'error'=>'Missing url',
        ], 200);
    }
    return response()->json(analyzeCore($u), 200);
})->name('analyze.alias');
