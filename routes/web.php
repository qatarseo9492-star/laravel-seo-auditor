<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

Route::get('/', fn () => view('home'));

Route::post('/analyze-json', function (\Illuminate\Http\Request $req) {
    $url = trim($req->input('url', ''));
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return response()->json(['ok' => false, 'error' => 'Invalid URL'], 422);
    }

    try {
        $resp = Http::timeout(14)->withHeaders([
            'User-Agent' => 'SemanticSEO-MasterAnalyzer/2.0'
        ])->get($url);

        $status = $resp->status();
        if ($status >= 400) return response()->json(['ok'=>false,'error'=>"Request failed ($status)"], 502);

        $html = $resp->body() ?? '';
        $host = parse_url($url, PHP_URL_HOST) ?: '';
        $scheme = preg_match('#^https://#',$url)?'https://':'http://';

        // DOM
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);
        $xp = new DOMXPath($dom);
        $q = fn($x) => iterator_to_array($xp->query($x) ?? []);
        $attr = fn($n,$a) => $n?->attributes?->getNamedItem($a)?->nodeValue ?? '';

        // Basic pulls
        $titleNode = $q('//title')[0] ?? null;
        $titleText = trim($titleNode?->textContent ?? '');

        $metaDescNode = $q('//meta[translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="description"]')[0] ?? null;
        $metaDesc = trim($attr($metaDescNode, 'content'));

        $canonicalNode = $q('//link[translate(@rel,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="canonical"]')[0] ?? null;
        $canonicalHref = trim($attr($canonicalNode, 'href'));

        $robotsNode = $q('//meta[translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="robots"]')[0] ?? null;
        $robots = strtolower(trim($attr($robotsNode, 'content')));

        $viewportNode = $q('//meta[translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="viewport"]')[0] ?? null;
        $viewport = strtolower(trim($attr($viewportNode, 'content')));

        $bodyNode = $q('//body')[0] ?? null;
        $bodyText = trim(preg_replace('/\s+/u', ' ', $bodyNode?->textContent ?? ''));

        // Headings
        $h1s = $q('//h1'); $h2s = $q('//h2'); $h3s = $q('//h3');
        $h1Text = trim($h1s[0]?->textContent ?? '');
        $firstP = trim(($q('//p[normalize-space()][1]')[0]?->textContent) ?? '');

        // Links
        $anchors = $q('//a[@href]');
        $internalLinks = 0; $externalLinks = 0; $externalTrusted = 0; $externalYears = 0; $keywordyAnchors = 0;
        $trustedTlds = ['.gov','.edu'];
        $trustedHosts = ['wikipedia.org','who.int','data.gov','nih.gov','un.org','oecd.org','worldbank.org'];
        foreach ($anchors as $a) {
            $href = trim($attr($a,'href'));
            if (!$href || str_starts_with($href,'mailto:') || str_starts_with($href,'tel:') || str_starts_with($href,'javascript:')) continue;
            $abs = $href;
            if (!str_starts_with($href,'http')) {
                $abs = str_starts_with($href,'/') ? $scheme.$host.$href : rtrim($scheme.$host,'/').'/'.$href;
            }
            $hHost = parse_url($abs, PHP_URL_HOST) ?: '';
            $txt = strtolower(trim($a->textContent ?? ''));
            if ($hHost === $host) {
                $internalLinks++;
            } else {
                $externalLinks++;
                foreach ($trustedTlds as $tld) if (str_ends_with($hHost, $tld)) { $externalTrusted++; break; }
                foreach ($trustedHosts as $th) if (str_ends_with($hHost, $th)) { $externalTrusted++; break; }
            }
            if ($txt && !in_array($txt, ['click here','here','learn more'])) $keywordyAnchors++;
            if (preg_match('/\b(2023|2024|2025)\b/', $txt)) $externalYears++;
        }

        // Images
        $imgs = $q('//img'); $imgsWithAlt = array_filter($imgs, fn($n) => strlen(trim($attr($n,'alt'))) > 0);
        $imgsLazy = array_filter($imgs, fn($n) => strtolower($attr($n,'loading')) === 'lazy' || strtolower($attr($n,'decoding')) === 'async');

        // Buttons / CTAs
        $ctaWords = ['buy','signup','sign up','contact','get started','start now','download','subscribe','add to cart','learn more','try','join','register'];
        $ctaFound = false;
        foreach ($anchors as $a) {
            $t = strtolower(trim($a->textContent ?? ''));
            if (!$t) continue;
            foreach ($ctaWords as $w) { if (str_contains($t,$w)) { $ctaFound = true; break 2; } }
        }
        if (!$ctaFound) foreach ($q('//button') as $b) {
            $t = strtolower(trim($b->textContent ?? ''));
            foreach ($ctaWords as $w) { if (str_contains($t,$w)) { $ctaFound = true; break 2; } }
        }

        // JSON-LD
        $jsonLdScripts = $q('//script[@type="application/ld+json"]');
        $schemaTypes = []; $hasFAQ=false; $hasArticle=false; $hasProduct=false; $hasBreadcrumb=false; $orgSameAs=false; $hasOrganization=false;
        foreach ($jsonLdScripts as $sc) {
            $txt = trim($sc->textContent ?? ''); if (!$txt) continue;
            try {
                $data = json_decode($txt, true, 512, JSON_INVALID_UTF8_IGNORE);
                $nodes = (is_array($data) && array_keys($data) === range(0,count($data)-1)) ? $data : [$data];
                foreach ($nodes as $node) {
                    $type = $node['@type'] ?? ($node['type'] ?? null);
                    if (is_array($type)) { foreach ($type as $t) $schemaTypes[] = $t; }
                    elseif (is_string($type)) { $schemaTypes[] = $type; }
                    $ct = strtolower(json_encode($node));
                    $hasFAQ        = $hasFAQ        || str_contains($ct, '"@type":"faqpage"');
                    $hasArticle    = $hasArticle    || str_contains($ct, '"@type":"article"');
                    $hasProduct    = $hasProduct    || str_contains($ct, '"@type":"product"');
                    $hasBreadcrumb = $hasBreadcrumb || str_contains($ct, '"@type":"breadcrumblist"');
                    if (($node['@type'] ?? '') === 'Organization') {
                        $hasOrganization = true;
                        if (!empty($node['sameAs']) && is_array($node['sameAs'])) $orgSameAs = true;
                    }
                }
            } catch (\Throwable $e) { /* ignore */ }
        }

        // Breadcrumb UI breadcrumb aria
        $hasBreadcrumbUI = count($q('//*[@aria-label="breadcrumb"] | //nav[contains(@class,"breadcrumb") or contains(@aria-label,"breadcrumb")]')) > 0;

        // Slug quality
        $path = parse_url($url, PHP_URL_PATH) ?: '/';
        $slugOk = false; $slug = '';
        if ($path !== '/') {
            $segments = array_values(array_filter(explode('/', $path)));
            $slug = end($segments) ?: '';
            $slugOk = (strlen($slug) > 1) &&
                      (strtolower($slug) === $slug) &&
                      !str_contains($slug,' ') &&
                      !preg_match('/[_%]/', $slug) &&
                      (substr_count($slug,'-') <= 8);
        }

        // E-E-A-T hints
        $authorMeta = $q('//meta[contains(translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz"),"author") or contains(translate(@property,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz"),"author")]');
        $timeTags = $q('//time[@datetime] | //meta[@itemprop="datePublished" or @itemprop="dateModified"] | //span[contains(@class,"date") or contains(@class,"updated")]');

        // Perf hints
        $scripts = $q('//script');
        $deferred = 0; $asyncd = 0; $blockingScripts = 0;
        foreach ($scripts as $s){
            $d = strtolower($attr($s,'defer'))==='defer'; if ($d) $deferred++;
            $a = strtolower($attr($s,'async'))==='async'; if ($a) $asyncd++;
            if (!$d && !$a && $attr($s,'src')) $blockingScripts++;
        }
        $hasPreload = count($q('//link[@rel="preload" or @rel="preconnect" or @rel="dns-prefetch"]'))>0;
        $responsiveImgs = count($q('//img[@srcset or @sizes]'))>0;

        // Text analytics (very light)
        $wc = max(1, preg_match_all('/\b[\p{L}\p{N}’\'\-]+\b/u', $bodyText, $m) ? count($m[0]) : 0);
        $sentences = max(1, preg_split('/[.!?]+[\s]+/u', $bodyText, -1, PREG_SPLIT_NO_EMPTY));
        $scount = max(1, count($sentences));
        $avgSentLen = $wc / $scount;
        $uniqueWords = count(array_unique(array_map('mb_strtolower', $m[0] ?? [])));
        $ttr = $uniqueWords / max(1,$wc); // lexical diversity

        // helpers
        $clamp = fn($v) => max(0, min(100, (int)round($v)));
        $inRangeScore = function($n, $min, $max, $softMin, $softMax) use ($clamp){
            if ($n <= 0) return 0;
            if ($n >= $min && $n <= $max) return 100;
            if ($n >= $softMin && $n <= $softMax) return 70;
            // otherwise taper
            $d = min(abs($n-$min), abs($n-$max));
            return $clamp(max(0, 70 - $d));
        };
        $jaccard = function($a, $b){
            $wa = array_unique(preg_split('/\W+/u', mb_strtolower($a), -1, PREG_SPLIT_NO_EMPTY));
            $wb = array_unique(preg_split('/\W+/u', mb_strtolower($b), -1, PREG_SPLIT_NO_EMPTY));
            if (!$wa || !$wb) return 0;
            $i = count(array_intersect($wa,$wb));
            $u = count(array_unique(array_merge($wa,$wb)));
            return $u ? $i/$u : 0;
        };

        // === Scoring per checklist ===
        $S = [];

        // 1 Intent & primary topic
        $intentHints = 0;
        $patterns = ['how to','what is','guide','best','vs','compare','price','buy','review','download'];
        $tLower = mb_strtolower($titleText.' '.$h1Text);
        foreach($patterns as $p){ if (str_contains($tLower,$p)) $intentHints+=10; }
        $sim = $jaccard($titleText, $h1Text);
        $S['ck-1'] = $clamp( min(100, 40*$sim + min(40,$intentHints) + (strlen($firstP)>40?20:0)) );

        // 2 Related keywords / coverage (proxy: questions in H2/H3, diversity, FAQ schema)
        $qHeads = 0; foreach (array_merge($h2s,$h3s) as $h){ if (str_contains(mb_strtolower($h->textContent),'?')) $qHeads++; }
        $S['ck-2'] = $clamp( min(100, ($qHeads*12) + ($ttr*100*0.25) + ($hasFAQ?30:0)) );

        // 3 H1 natural
        $lenH1 = mb_strlen($h1Text);
        $S['ck-3'] = $clamp( min(100, ($sim*100*0.6) + ($inRangeScore($lenH1, 20, 80, 10, 100)*0.4)) );

        // 4 FAQs / questions
        $qsInContent = preg_match_all('/\?/', $bodyText, $tmp);
        $S['ck-4'] = $clamp( min(100, ($hasFAQ?80:0) + min(20,$qsInContent*2)) );

        // 5 Readability (avg sentence len 12–22 ideal)
        $readScore = 100 - abs($avgSentLen - 17) * 6; // bell-ish
        $S['ck-5'] = $clamp($readScore);

        // 6 Title tag
        $S['ck-6'] = $clamp( $inRangeScore(mb_strlen($titleText), 50, 60, 35, 70) );

        // 7 Meta description
        $S['ck-7'] = $clamp( $inRangeScore(mb_strlen($metaDesc), 140, 160, 110, 180) );

        // 8 Canonical
        $S['ck-8'] = $canonicalHref ? 100 : 0;

        // 9 Indexable
        $S['ck-9'] = (!str_contains($robots,'noindex')) ? 100 : 0;

        // 10 E-E-A-T hints
        $aboutContact = 0;
        foreach ($anchors as $a) {
            $t = mb_strtolower(trim($a->textContent ?? ''));
            if (in_array($t, ['about','about us','contact','editorial policy','privacy','team','reviewed'])) $aboutContact += 10;
        }
        $S['ck-10'] = $clamp( min(100, (count($authorMeta)?40:0) + (count($timeTags)?30:0) + min(30,$aboutContact)) );

        // 11 Unique value (proxy: long content, tables, lists, code, keywords like example/template)
        $tables = count($q('//table')); $pres = count($q('//pre | //code'));
        $lists = count($q('//ol/li')) + count($q('//ul/li'));
        $uvWords = preg_match('/\b(example|template|case study|dataset|calculator|tool)\b/i', $bodyText) ? 1 : 0;
        $S['ck-11'] = $clamp( min(100, min(40, (int)floor($wc/600)*10) + min(20,$tables*10) + min(15,$pres*7) + min(15, (int)floor($lists/8)*5) + ($uvWords?20:0)) );

        // 12 Facts & citations up to date
        $S['ck-12'] = $clamp( min(100, min(60,$externalTrusted*20) + min(40,$externalYears*10)) );

        // 13 Helpful media (images + alt ratio)
        $altRatio = (count($imgs) ? (count($imgsWithAlt)/max(1,count($imgs))) : 0);
        $S['ck-13'] = $clamp( min(100, min(60, count($imgs)*10) + (int)round($altRatio*40)) );

        // 14 Structure (H2/H3)
        $S['ck-14'] = $clamp( min(100, min(60, count($h2s)*15) + min(40, count($h3s)*10)) );

        // 15 Internal links (and anchor quality)
        $S['ck-15'] = $clamp( min(100, min(70, $internalLinks*10) + min(30, $keywordyAnchors*3)) );

        // 16 URL slug
        $S['ck-16'] = $clamp( ($slugOk ? 100 : (strlen($slug)?50:30)) );

        // 17 Breadcrumbs
        $S['ck-17'] = $clamp( ($hasBreadcrumb || $hasBreadcrumbUI) ? 100 : 0 );

        // 18 Mobile‑friendly
        $S['ck-18'] = $clamp( min(100, ($viewport && str_contains($viewport,'width=device-width') ? 70:0) + ($responsiveImgs?30:0)) );

        // 19 Speed hints
        $S['ck-19'] = $clamp( min(100, min(40, count($imgsLazy)*8) + min(30, $deferred*5 + $asyncd*5) + ($hasPreload?20:0) - min(30, max(0,$blockingScripts-2)*10)) );

        // 20 Web Vitals (very rough proxy: fewer blocking scripts, responsive images, viewport present)
        $S['ck-20'] = $clamp( min(100, 50 + ($responsiveImgs?15:0) + ($viewport?15:0) - min(30, $blockingScripts*7)) );

        // 21 CTAs
        $S['ck-21'] = $clamp( $ctaFound ? 100 : 20 );

        // 22 Primary entity defined (H1 + first paragraph overlap; Article schema)
        $eSim = $jaccard($h1Text, $firstP);
        $S['ck-22'] = $clamp( min(100, ($eSim*100*0.7) + ($hasArticle?30:0)) );

        // 23 Related entities (links to Wikipedia/knowledge sites or many proper nouns in H2s)
        $wikiLinks = 0; foreach ($anchors as $a){ $href = strtolower($attr($a,'href') ?? ''); if (str_contains($href,'wikipedia.org')) $wikiLinks++; }
        $properH2s = 0; foreach ($h2s as $h){ if (preg_match('/\b[A-Z][a-z]{2,}/', trim($h->textContent ?? ''))) $properH2s++; }
        $S['ck-23'] = $clamp( min(100, min(60,$wikiLinks*20) + min(40,$properH2s*10)) );

        // 24 Schema markup
        $validTypes = array_values(array_unique($schemaTypes));
        $S['ck-24'] = $clamp( min(100, min(100, count($validTypes)*20)) );

        // 25 sameAs/Organization
        // Also look for footer social links as proxy
        $footer = $q('//footer')[0] ?? null;
        $footerSocial = 0;
        if ($footer){
            $links = $footer->getElementsByTagName('a');
            foreach ($links as $a) {
                $h = strtolower($a->getAttribute('href'));
                if (preg_match('#(facebook|twitter|x\.com|instagram|linkedin|youtube|tiktok)\.com#',$h)) $footerSocial++;
            }
        }
        $S['ck-25'] = $clamp( min(100, ($orgSameAs?100:0) + (!$orgSameAs && $hasOrganization ? 60:0) + min(40,$footerSocial*10)) );

        // Auto-check threshold
        $autoIds = [];
        foreach ($S as $id => $score) if ($score >= 70) $autoIds[] = $id;

        $overall = array_sum($S) / max(1,count($S));

        return response()->json([
            'ok'=>true,
            'url'=>$url,
            'status'=>$status,
            'title'=>$titleText,
            'meta_description_len'=>mb_strlen($metaDesc),
            'canonical'=>$canonicalHref,
            'robots'=>$robots,
            'viewport'=>$viewport,
            'counts'=>[
                'h1'=>count($h1s),'h2'=>count($h2s),'h3'=>count($h3s),
                'images'=>count($imgs),'images_with_alt'=>count($imgsWithAlt),
                'internal_links'=>$internalLinks,'external_links'=>$externalLinks
            ],
            'schema'=>[
                'found_types'=>array_values(array_unique($schemaTypes)),
                'hasFAQ'=>$hasFAQ,'hasArticle'=>$hasArticle,'hasProduct'=>$hasProduct,
                'hasBreadcrumb'=>$hasBreadcrumb,'orgSameAs'=>$orgSameAs
            ],
            'scores'=>$S,
            'auto_check_ids'=>$autoIds,
            'overall_score'=>round($overall,1),
        ]);
    } catch (\Throwable $e) {
        return response()->json(['ok'=>false,'error'=>'Analyze error: '.$e->getMessage()], 500);
    }
})->name('analyze.json');
