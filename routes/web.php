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
        $scheme = preg_match('#^https://#', $url) ? 'https://' : 'http://';

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);
        $xp = new DOMXPath($dom);
        $q = fn($x) => iterator_to_array($xp->query($x) ?? []);
        $attr = fn($n,$a) => $n?->attributes?->getNamedItem($a)?->nodeValue ?? '';

        // Head essentials
        $titleNode = $q('//title')[0] ?? null;
        $titleText = trim($titleNode?->textContent ?? '');

        $metaDescNode = $q('//meta[translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="description"]')[0] ?? null;
        $metaDesc = trim($attr($metaDescNode, 'content'));

        $canonicalNode = $q('//link[translate(@rel,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="canonical"]')[0] ?? null;
        $canonicalHref = trim($attr($canonicalNode, 'href'));
        if ($canonicalHref && !str_starts_with($canonicalHref,'http')) {
            $canonicalHref = str_starts_with($canonicalHref,'/') ? $scheme.$host.$canonicalHref : $scheme.$host.'/'.$canonicalHref;
        }

        $robotsNode = $q('//meta[translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="robots"]')[0] ?? null;
        $robots = strtolower(trim($attr($robotsNode, 'content')));

        $viewportNode = $q('//meta[translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="viewport"]')[0] ?? null;
        $viewport = strtolower(trim($attr($viewportNode, 'content')));

        // Content structure
        $h1s = $q('//h1'); $h2s = $q('//h2'); $h3s = $q('//h3');
        $h1Text = trim(($h1s[0] ?? null)?->textContent ?? '');

        // Body text for readability
        $texts = $q('//body//*[not(self::script or self::style)]/text()');
        $plain = trim(implode(' ', array_map(fn($n)=>trim($n->nodeValue), $texts)));
        $plain = preg_replace('/\s+/', ' ', $plain);

        // Media, links
        $imgs = $q('//img');
        $imgsWithAlt = array_filter($imgs, fn($n) => strlen(trim($attr($n, 'alt'))) > 0);
        $imgsDimOk = array_filter($imgs, fn($n) => $attr($n, 'width') && $attr($n, 'height'));
        $lazyImgs = array_filter($imgs, fn($n) => strtolower($attr($n,'loading')) === 'lazy' || strtolower($attr($n,'decoding')) === 'async');
        $modernImgs = array_filter($imgs, fn($n) => preg_match('/\.(webp|avif)(\?|$)/i', $attr($n,'src') ?: ''));

        $anchors = $q('//a[@href]');
        $internalLinks = 0; $externalLinks = 0; $wikipediaLinks = 0;
        foreach ($anchors as $a) {
            $href = trim($attr($a,'href'));
            if (!$href || preg_match('#^(mailto:|tel:|javascript:)#i',$href)) continue;
            $abs = $href;
            if (!str_starts_with($href,'http')) {
                $abs = str_starts_with($href,'/') ? $scheme.$host.$href : rtrim($scheme.$host,'/').'/'.$href;
            }
            $h = parse_url($abs, PHP_URL_HOST) ?: '';
            if ($h === $host) $internalLinks++; else $externalLinks++;
            if (stripos($abs, 'wikipedia.org') !== false) $wikipediaLinks++;
        }

        // Buttons / CTAs
        $ctaWords = ['buy','signup','sign up','contact','get started','start now','download','subscribe','add to cart','learn more','try','join','book'];
        $ctaFound = false;
        foreach ($anchors as $a) {
            $t = strtolower(trim($a->textContent ?? '')); if (!$t) continue;
            foreach ($ctaWords as $w) { if (str_contains($t,$w)) { $ctaFound = true; break 2; } }
        }
        if (!$ctaFound) foreach ($q('//button') as $b) {
            $t = strtolower(trim($b->textContent ?? ''));
            foreach ($ctaWords as $w) { if (str_contains($t,$w)) { $ctaFound = true; break 2; } }
        }

        // JSON-LD schema
        $jsonLdScripts = $q('//script[@type="application/ld+json"]');
        $schemaTypes = []; $hasFAQ=false; $hasArticle=false; $hasProduct=false; $hasBreadcrumb=false; $orgSameAs=false; $hasOrganization=false;
        foreach ($jsonLdScripts as $sc) {
            $txt = trim($sc->textContent ?? ''); if (!$txt) continue;
            try {
                $data = json_decode($txt, true, 512, JSON_INVALID_UTF8_IGNORE);
                $nodes = is_array($data) && array_is_list($data) ? $data : [$data];
                foreach ($nodes as $node) {
                    if (!is_array($node)) continue;
                    $type = $node['@type'] ?? ($node['type'] ?? null);
                    if (is_array($type)) { foreach ($type as $t) $schemaTypes[] = $t; }
                    elseif (is_string($type)) { $schemaTypes[] = $type; }
                    $ct = strtolower(json_encode($node));
                    $hasFAQ        = $hasFAQ        || str_contains($ct,'"@type":"faqpage"');
                    $hasArticle    = $hasArticle    || str_contains($ct,'"@type":"article"');
                    $hasProduct    = $hasProduct    || str_contains($ct,'"@type":"product"');
                    $hasBreadcrumb = $hasBreadcrumb || str_contains($ct,'"@type":"breadcrumblist"');
                    if (($node['@type'] ?? '') === 'Organization') $hasOrganization = true;
                    if (($node['@type'] ?? '') === 'Organization' && !empty($node['sameAs']) && is_array($node['sameAs'])) $orgSameAs = true;
                }
            } catch (\Throwable $e) { /* ignore */ }
        }

        // URL slug quality
        $path = parse_url($url, PHP_URL_PATH) ?: '/';
        $slugOk = false;
        if ($path !== '/') {
            $segments = array_values(array_filter(explode('/', $path)));
            $slug = end($segments) ?: '';
            $slugOk = (strlen($slug) > 1) &&
                      (strtolower($slug) === $slug) &&
                      !str_contains($slug, ' ') &&
                      !preg_match('/[_%]/', $slug);
        }

        // Author / dates
        $authorMeta = $q('//meta[contains(translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz"),"author") or contains(translate(@property,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz"),"author")]');
        $timeTags = $q('//time[@datetime] | //meta[@itemprop="datePublished" or @itemprop="dateModified"]');

        // FAQ blocks (heuristic without schema)
        $questionBlocks = 0;
        foreach ($q('//*[self::h2 or self::h3 or self::summary]')) {
            $t = strtolower(trim($n = $attr($node??null,'') ? '' : ($node->textContent ?? ''))); // safe default
        }
        // simpler: count headings with '?' and following paragraph
        foreach (array_merge($h2s,$h3s) as $h) {
            $txt = strtolower(trim($h->textContent ?? ''));
            if (str_contains($txt,'?')) $questionBlocks++;
        }

        // Helper functions
        $tokenize = function($s){
            $s = strtolower($s);
            $s = preg_replace('/[^a-z0-9\- ]+/',' ',$s);
            $parts = array_values(array_filter(explode(' ', $s)));
            return array_unique($parts);
        };
        $jaccard = function($a,$b){
            $a = array_unique($a); $b = array_unique($b);
            $i = count(array_intersect($a,$b)); $u = count(array_unique(array_merge($a,$b))) ?: 1;
            return $i/$u;
        };

        // Readability: avg sentence length & simple proxy
        $sentences = preg_split('/[\.!\?]+/',$plain);
        $words = preg_split('/\s+/', trim($plain));
        $avgSentLen = ($sentences && count($sentences)>0) ? (count($words)/max(1,count($sentences))) : 0;
        $readabilityScore = 100 - min(100, abs($avgSentLen - 18) * 4); // best around 18 words/sentence

        // External citations recency proxy (years & outlinks)
        preg_match_all('/20(1[5-9]|2[0-9])/', $plain, $yrs); // 2015–2029
        $freshYears = array_filter($yrs[0] ?? [], fn($y) => intval($y) >= (intval(date('Y')) - 3)); // last 3 years

        // Speed heuristics
        $scripts = $q('//script');
        $deferScripts = array_filter($scripts, fn($n) => $attr($n,'defer') || $attr($n,'async'));
        $preloads = $q('//link[@rel="preload" or @rel="preconnect"]');

        // Headings similarity (intent alignment)
        $titleTokens = $tokenize($titleText);
        $h1Tokens = $tokenize($h1Text);
        $titleH1Sim = $jaccard($titleTokens, $h1Tokens); // 0..1

        // Scoring per item (0–100)
        $items = [];

        $score = function($val,$max){ $v = max(0,min($val,$max)); return intval(round(($v/$max)*100)); };

        // 1 Define search intent & primary topic (title↔h1 similarity + presence)
        $items['ck-1'] = [
            'score' => intval(round(($titleH1Sim*70) + (min(1,count($h1s))*30))),
            'reason' => "Title/H1 similarity: ".number_format($titleH1Sim*100,0)."% • H1s: ".count($h1s)
        ];

        // 2 Map target & related keywords (variety of H2/H3 + FAQ headings)
        $relSignals = min(8, count($h2s)) + min(6, count($h3s)) + min(4, $questionBlocks);
        $items['ck-2'] = ['score'=>$score($relSignals, 12),'reason'=>"H2: ".count($h2s).", H3: ".count($h3s).", Q-headings: ".$questionBlocks];

        // 3 H1 includes primary topic naturally (presence + similarity)
        $items['ck-3'] = ['score'=>intval(round(($titleH1Sim*60) + (count($h1s)>0?40:0))), 'reason'=>"H1 present: ".(count($h1s)>0?'yes':'no').", similarity ".number_format($titleH1Sim*100,0)."%"];

        // 4 Integrate FAQs (FAQ schema or Q blocks)
        $faqPts = ($hasFAQ?100:0) ?: $score($questionBlocks, 4);
        $items['ck-4'] = ['score'=>$faqPts,'reason'=>"FAQ schema: ".($hasFAQ?'yes':'no').", question blocks: ".$questionBlocks];

        // 5 Readable, NLP‑friendly language (readability proxy)
        $items['ck-5'] = ['score'=>intval($readabilityScore),'reason'=>"Avg sentence length: ".number_format($avgSentLen,1)];

        // 6 Title tag length
        $len = strlen($titleText);
        $lenScore = ($len>=45 && $len<=65)?100:(($len>=30 && $len<=75)?75:($len>0?40:0));
        $items['ck-6'] = ['score'=>$lenScore,'reason'=>"Title length: $len"];

        // 7 Meta description length
        $md = strlen($metaDesc);
        $mdScore = ($md>=140 && $md<=170)?100:(($md>=90 && $md<=200)?70:($md>0?40:0));
        $items['ck-7'] = ['score'=>$mdScore,'reason'=>"Meta description length: $md"];

        // 8 Canonical
        $items['ck-8'] = ['score'=>$canonicalHref?100:0,'reason'=>"Canonical present: ".($canonicalHref?'yes':'no')];

        // 9 Indexable & sitemap (we can only check noindex here)
        $idxScore = !str_contains($robots,'noindex') ? 100 : 0;
        $items['ck-9'] = ['score'=>$idxScore,'reason'=>"Robots: ".($robots ?: '—')];

        // 10 E-E-A-T (author or date)
        $eeat = (count($authorMeta)>0?50:0) + (count($timeTags)>0?50:0);
        $items['ck-10'] = ['score'=>$eeat,'reason'=>"Author meta: ".count($authorMeta).", time tags: ".count($timeTags)];

        // 11 Unique value vs competitors (tables/lists/code/media as proxy)
        $lists = count($q('//ul|//ol')); $tables = count($q('//table')); $codes = count($q('//pre|//code')); $videos = count($q('//video|//iframe[contains(@src,"youtube") or contains(@src,"vimeo")]'));
        $uvSignals = min(4,$lists)+min(3,$tables)+min(3,$codes)+min(4,$videos);
        $items['ck-11'] = ['score'=>$score($uvSignals, 10),'reason'=>"Lists:$lists, Tables:$tables, Code:$codes, Video:$videos"];

        // 12 Facts & citations up to date (external links + recent years)
        $citSignals = min(6,$externalLinks) + min(4,count($freshYears));
        $items['ck-12'] = ['score'=>$score($citSignals, 10),'reason'=>"External links:$externalLinks, recent years: ".count($freshYears)];

        // 13 Helpful media with captions/alt (images with alt + has video)
        $m = count($imgsWithAlt) + (count($q('//figure/figcaption'))>0?1:0) + (count($q('//video|//iframe[contains(@src,"youtube") or contains(@src,"vimeo")]'))>0?1:0);
        $items['ck-13'] = ['score'=>$score($m, 6),'reason'=>"Images alt: ".count($imgsWithAlt).", video/caption signals present"];

        // 14 Logical H2/H3 structure
        $struct = (count($h2s)>=2?60:0) + (count($h3s)>=1?40:0);
        $items['ck-14'] = ['score'=>$struct,'reason'=>"H2: ".count($h2s).", H3: ".count($h3s)];

        // 15 Internal links
        $items['ck-15'] = ['score'=>$score($internalLinks, 8),'reason'=>"Internal links: $internalLinks"];

        // 16 Clean URL slug
        $items['ck-16'] = ['score'=>$slugOk?100:40,'reason'=>"Slug clean: ".($slugOk?'yes':'no')];

        // 17 Breadcrumbs enabled (+schema)
        $bcDom = count($q('//nav[contains(@aria-label,"breadcrumb")]|//ol[contains(@class,"breadcrumb")]'));
        $bcScore = $hasBreadcrumb?100:(($bcDom>0)?70:0);
        $items['ck-17'] = ['score'=>$bcScore,'reason'=>"Schema: ".($hasBreadcrumb?'yes':'no').", DOM: ".($bcDom>0?'yes':'no')];

        // 18 Mobile friendly (viewport)
        $items['ck-18'] = ['score'=>($viewport && str_contains($viewport,'width=device-width'))?100:0,'reason'=>"Viewport: ".($viewport?:'—')];

        // 19 Optimized speed (lazy images, modern formats, defer/async, preconnect)
        $spdSignals = min(4,count($lazyImgs)) + min(3,count($modernImgs)) + min(4,count($deferScripts)) + min(3,count($preloads));
        $items['ck-19'] = ['score'=>$score($spdSignals, 12),'reason'=>"Lazy:".count($lazyImgs).", modern img:".count($modernImgs).", defer/async:".count($deferScripts).", preconnect/preload:".count($preloads)];

        // 20 Core Web Vitals proxy (image dims + few blocking scripts)
        $blockingScripts = count(array_filter($scripts, fn($n)=>!$attr($n,'defer') && !$attr($n,'async') && !$attr($n,'type')));
        $cwvSignals = min(6,count($imgsDimOk)) + max(0, 6 - min(6,$blockingScripts));
        $items['ck-20'] = ['score'=>$score($cwvSignals, 12),'reason'=>"Images sized:".count($imgsDimOk).", blocking scripts:".$blockingScripts];

        // 21 Clear CTAs
        $items['ck-21'] = ['score'=>$ctaFound?100:40,'reason'=>"CTA found: ".($ctaFound?'yes':'no')];

        // 22 Primary entity defined (schema types or clear H1 definition)
        $entityPrimary = $hasArticle || $hasProduct || $hasOrganization || !empty($schemaTypes);
        $items['ck-22'] = ['score'=>$entityPrimary?100:($h1Text?60:30),'reason'=>"Schema types: ".implode(',', array_unique($schemaTypes)) ?: '—'];

        // 23 Related entities (wiki/outbound variety)
        $relEntities = min(4,$wikipediaLinks) + min(6, $externalLinks>=3 ? 3 : 0);
        $items['ck-23'] = ['score'=>$score($relEntities, 10),'reason'=>"Wikipedia links: $wikipediaLinks, external variety: $externalLinks"];

        // 24 Valid schema (Article/FAQ/Product)
        $hasRich = ($hasArticle||$hasFAQ||$hasProduct);
        $items['ck-24'] = ['score'=>$hasRich?100:0,'reason'=>"Article:".($hasArticle?'yes':'no').", FAQ:".($hasFAQ?'yes':'no').", Product:".($hasProduct?'yes':'no')];

        // 25 sameAs/Organization
        $items['ck-25'] = ['score'=>$orgSameAs?100:0,'reason'=>"Organization sameAs: ".($orgSameAs?'yes':'no')];

        // Overall
        $sum = 0; foreach ($items as $it) { $sum += max(0,min(100,intval($it['score']))); }
        $overall = intval(round($sum / 25));

        return response()->json([
            'ok'=>true,
            'url'=>$url,
            'status'=>$status,
            'title'=>$titleText,
            'meta_description_len'=>strlen($metaDesc),
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
                'hasBreadcrumb'=>$hasBreadcrumb,'orgSameAs'=>$orgSameAs,
            ],
            'items'=>$items,
            'overall_score'=>$overall
        ]);
    } catch (\Throwable $e) {
        return response()->json(['ok'=>false,'error'=>'Analyze error: '.$e->getMessage()], 500);
    }
})->name('analyze.json');
