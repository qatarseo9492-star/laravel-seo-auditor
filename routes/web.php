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
        $resp = Http::timeout(12)->withHeaders([
            'User-Agent' => 'SemanticSEO-MasterAnalyzer/1.0'
        ])->get($url);

        $status = $resp->status();
        if ($status >= 400) return response()->json(['ok'=>false,'error'=>"Request failed ($status)"], 502);

        $html = $resp->body() ?? '';
        $host = parse_url($url, PHP_URL_HOST) ?: '';

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);
        $xp = new DOMXPath($dom);
        $q = fn($x) => iterator_to_array($xp->query($x) ?? []);
        $attr = fn($n,$a) => $n?->attributes?->getNamedItem($a)?->nodeValue ?? '';

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

        $h1s = $q('//h1'); $h2s = $q('//h2'); $h3s = $q('//h3');

        $imgs = $q('//img');
        $imgsWithAlt = array_filter($imgs, fn($n) => strlen(trim($attr($n, 'alt'))) > 0);

        $anchors = $q('//a[@href]');
        $internalLinks = 0;
        foreach ($anchors as $a) {
            $href = trim($attr($a, 'href'));
            if (!$href || str_starts_with($href,'mailto:') || str_starts_with($href,'tel:') || str_starts_with($href,'javascript:')) continue;
            $abs = $href;
            if (!str_starts_with($href, 'http')) {
                $scheme = (preg_match('#^https://#',$url)?'https://':'http://');
                $abs = str_starts_with($href,'/') ? $scheme.$host.$href : rtrim($scheme.$host, '/').'/'.$href;
            }
            $hrefHost = parse_url($abs, PHP_URL_HOST) ?: '';
            if ($hrefHost === $host) $internalLinks++;
        }

        $ctaWords = ['buy','signup','sign up','contact','get started','start now','download','subscribe','add to cart','learn more','try'];
        $ctaFound = false;
        foreach ($anchors as $a) {
            $t = strtolower(trim($a->textContent ?? '')); if (!$t) continue;
            foreach ($ctaWords as $w) { if (str_contains($t, $w)) { $ctaFound = true; break 2; } }
        }
        if (!$ctaFound) foreach ($q('//button') as $b) {
            $t = strtolower(trim($b->textContent ?? ''));
            foreach ($ctaWords as $w) { if (str_contains($t, $w)) { $ctaFound = true; break 2; } }
        }

        $jsonLdScripts = $q('//script[@type="application/ld+json"]');
        $schemaTypes = [];
        $hasFAQ=false; $hasArticle=false; $hasProduct=false; $hasBreadcrumb=false; $orgSameAs=false;

        foreach ($jsonLdScripts as $sc) {
            $txt = trim($sc->textContent ?? ''); if (!$txt) continue;
            try {
                $data = json_decode($txt, true, 512, JSON_INVALID_UTF8_IGNORE);
                $nodes = is_array($data) && array_is_list($data) ? $data : [$data];
                foreach ($nodes as $node) {
                    $type = $node['@type'] ?? ($node['type'] ?? null);
                    if (is_array($type)) { foreach ($type as $t) $schemaTypes[] = $t; }
                    elseif (is_string($type)) { $schemaTypes[] = $type; }
                    $ct = strtolower(json_encode($node));
                    $hasFAQ        = $hasFAQ        || str_contains($ct, '"@type":"faqpage"');
                    $hasArticle    = $hasArticle    || str_contains($ct, '"@type":"article"');
                    $hasProduct    = $hasProduct    || str_contains($ct, '"@type":"product"');
                    $hasBreadcrumb = $hasBreadcrumb || str_contains($ct, '"@type":"breadcrumblist"');
                    if (($node['@type'] ?? '') === 'Organization' && !empty($node['sameAs']) && is_array($node['sameAs'])) $orgSameAs = true;
                }
            } catch (\Throwable $e) { /* ignore */ }
        }

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

        $authorMeta = $q('//meta[contains(translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz"),"author") or contains(translate(@property,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz"),"author")]');
        $timeTags = $q('//time[@datetime] | //meta[@itemprop="datePublished" or @itemprop="dateModified"]');

        $autoIds = [];
        if (count($h1s) >= 1) $autoIds[] = 'ck-3';
        if (strlen($titleText) >= 10 && strlen($titleText) <= 65) $autoIds[] = 'ck-6';
        if (strlen($metaDesc) >= 70 && strlen($metaDesc) <= 170) $autoIds[] = 'ck-7';
        if ($canonicalHref) $autoIds[] = 'ck-8';
        if (!str_contains($robots, 'noindex')) $autoIds[] = 'ck-9';
        if (count($h2s) >= 2 || (count($h2s) >= 1 && count($h3s) >= 1)) $autoIds[] = 'ck-14';
        if ($internalLinks >= 3) $autoIds[] = 'ck-15';
        if ($slugOk) $autoIds[] = 'ck-16';
        if ($hasBreadcrumb) $autoIds[] = 'ck-17';
        if ($viewport && str_contains($viewport, 'width=device-width')) $autoIds[] = 'ck-18';
        if ($ctaFound) $autoIds[] = 'ck-21';
        if ($hasArticle || $hasFAQ || $hasProduct) $autoIds[] = 'ck-24';
        if ($orgSameAs) $autoIds[] = 'ck-25';
        if (count($imgsWithAlt) >= 1) $autoIds[] = 'ck-13';
        if (count($authorMeta) >= 1 || count($timeTags) >= 1) $autoIds[] = 'ck-10';
        if ($hasFAQ) $autoIds[] = 'ck-4';

        return response()->json([
            'ok'=>true,'url'=>$url,'status'=>$status,'title'=>$titleText,
            'meta_description_len'=>strlen($metaDesc),'canonical'=>$canonicalHref,
            'robots'=>$robots,'viewport'=>$viewport,
            'counts'=>['h1'=>count($h1s),'h2'=>count($h2s),'h3'=>count($h3s),'images'=>count($imgs),'images_with_alt'=>count($imgsWithAlt),'internal_links'=>$internalLinks],
            'schema'=>['found_types'=>array_values(array_unique($schemaTypes)),'hasFAQ'=>$hasFAQ,'hasArticle'=>$hasArticle,'hasProduct'=>$hasProduct,'hasBreadcrumb'=>$hasBreadcrumb,'orgSameAs'=>$orgSameAs],
            'auto_check_ids'=>array_values(array_unique($autoIds)),
        ]);
    } catch (\Throwable $e) {
        return response()->json(['ok'=>false,'error'=>'Analyze error: '.$e->getMessage()], 500);
    }
})->name('analyze.json');
