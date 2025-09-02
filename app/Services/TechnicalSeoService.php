<?php

namespace App\Services;

use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TechnicalSeoService
{
    // Tweak limits to control runtime cost/time
    const MAX_CRAWL_PAGES = 25;        // for internal linking
    const MAX_ALT_SUGGEST = 30;        // limit images we suggest on
    const OPENAI_MODEL = 'gpt-4o-mini';// fast + cheap; change if you like

    public function analyze(string $pageUrl): array
    {
        $hostBase = $this->baseOrigin($pageUrl);

        // 1) Fetch target page
        $html = $this->fetch($pageUrl);
        if (!$html) {
            return ['ok' => false, 'error' => 'Unable to fetch URL'];
        }

        // 2) Parse once
        $doc = $this->dom($html);
        $xp  = new DOMXPath($doc);

        // 3) Extract basic on-page data
        $title = $this->text($xp, '//title');
        $metaDesc = $this->attr($xp, '//meta[translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="description"]/@content');
        $h1 = $this->text($xp, '//h1');
        $headings = $this->headingsTree($xp);
        $images = $this->images($xp, $pageUrl);

        // 4) URL structure scoring
        $urlAudit = $this->auditUrl($pageUrl, $title);

        // 5) Meta optimization (heuristics + AI option)
        $metaAudit = $this->metaAudit($title, $metaDesc, $doc, $pageUrl);

        // 6) Alt text suggestions (surrounding context + AI)
        $altSuggestions = $this->altTextSuggest($images, $xp, $pageUrl);

        // 7) Internal linking (sitemap first, then shallow crawl)
        [$sitePages, $internalLinks] = $this->collectSitePages($hostBase);
        $linking = $this->internalLinking($pageUrl, $title, $h1, $sitePages);

        // 8) Site structure map
        $structure = [
            'h1' => $h1,
            'tree' => $headings
        ];

        // 9) Scores (0–100)
        $scores = [
            'url_structure' => $urlAudit['score'],
            'meta'          => $metaAudit['score'],
            'images'        => $altSuggestions['score'],
            'internal_links'=> $linking['score'],
            'structure'     => $this->structureScore($headings),
            'overall'       => $this->overall([
                $urlAudit['score'], $metaAudit['score'],
                $altSuggestions['score'], $linking['score'],
                $this->structureScore($headings)
            ]),
        ];

        return [
            'ok' => true,
            'url' => $pageUrl,
            'scores' => $scores,
            'url_structure' => $urlAudit,
            'meta' => $metaAudit,
            'images' => [
                'count' => count($images),
                'suggestions' => $altSuggestions['items']
            ],
            'internal_linking' => $linking,
            'structure' => $structure,
        ];
    }

    // ----------------------- Fetch & Parse -----------------------

    private function fetch(string $url): ?string
    {
        try {
            $res = Http::timeout(20)->withHeaders([
                'User-Agent' => 'SEO-Master-Analyzer/1.0'
            ])->get($url);

            if ($res->ok()) return $res->body();
        } catch (\Throwable $e) {}
        return null;
    }

    private function dom(string $html): DOMDocument
    {
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML($html);
        libxml_clear_errors();
        return $doc;
    }

    private function baseOrigin(string $url): string
    {
        $p = parse_url($url);
        $scheme = $p['scheme'] ?? 'https';
        $host   = $p['host'] ?? '';
        $port   = isset($p['port']) ? ':' . $p['port'] : '';
        return $scheme . '://' . $host . $port;
    }

    // ----------------------- Extractors --------------------------

    private function text(DOMXPath $xp, string $xpath): string
    {
        $n = $xp->query($xpath)?->item(0);
        return $n ? trim($n->textContent) : '';
    }

    private function attr(DOMXPath $xp, string $xpath): string
    {
        $n = $xp->query($xpath)?->item(0);
        return $n ? trim($n->nodeValue) : '';
    }

    private function images(DOMXPath $xp, string $pageUrl): array
    {
        $list = [];
        $nodes = $xp->query('//img');
        foreach ($nodes as $img) {
            $src = $img->getAttribute('src') ?: $img->getAttribute('data-src');
            if (!$src) continue;
            $alt = $img->getAttribute('alt');
            $list[] = [
                'src' => $this->absUrl($pageUrl, $src),
                'alt' => $alt ?: null,
            ];
        }
        return $list;
    }

    private function headingsTree(DOMXPath $xp): array
    {
        // Build a simple {level, text} list, then nest
        $nodes = $xp->query('//h1|//h2|//h3|//h4|//h5|//h6');
        $flat = [];
        foreach ($nodes as $h) {
            $flat[] = [
                'level' => intval(substr(strtolower($h->nodeName), 1)),
                'text'  => trim($h->textContent)
            ];
        }
        // Nesting
        $stack = [['level' => 0, 'text' => 'ROOT', 'children' => []]];
        foreach ($flat as $node) {
            while (!empty($stack) && end($stack)['level'] >= $node['level']) array_pop($stack);
            $child = ['level' => $node['level'], 'text' => $node['text'], 'children' => []];
            $stack[array_key_last($stack)]['children'][] = $child;
            $stack[] = &$stack[array_key_last($stack)]['children'][array_key_last($stack[array_key_last($stack)]['children'])];
        }
        return $stack[0]['children'];
    }

    private function absUrl(string $base, string $path): string
    {
        if (Str::startsWith($path, ['http://','https://'])) return $path;
        if (Str::startsWith($path, '//')) {
            $p = parse_url($base);
            return ($p['scheme'] ?? 'https') . ':' . $path;
        }
        // resolve simple relatives
        return rtrim($this->baseOrigin($base), '/') . '/' . ltrim($path, '/');
    }

    // ----------------------- Audits ------------------------------

    private function auditUrl(string $url, string $title): array
    {
        $p = parse_url($url);
        $path = $p['path'] ?? '/';
        $segments = array_values(array_filter(explode('/', $path)));
        $filename = end($segments) ?: '';
        $isLower  = strtolower($path) === $path;
        $hasUnderscore = str_contains($path, '_');
        $length = strlen($url);

        $readable = !preg_match('#\d{4,}#', $path) && !preg_match('#[?&]id=#i', $url);
        $hyphens  = !$hasUnderscore;
        $short    = $length <= 115;
        $keywordy = $title && $filename && Str::contains(Str::slug($title), Str::slug($filename));

        $score = $this->clamp(
            ($readable ? 25 : 0) +
            ($hyphens ? 20 : 0) +
            ($isLower ? 15 : 0) +
            ($short ? 20 : 5) +
            ($keywordy ? 20 : 0),
            0, 100
        );

        $tips = [];
        if (!$short) $tips[] = 'Shorten the URL to under ~115 characters.';
        if (!$hyphens) $tips[] = 'Use hyphens (-) instead of underscores in URLs.';
        if (!$isLower) $tips[] = 'Lowercase all URL paths.';
        if (!$readable) $tips[] = 'Avoid opaque IDs/params; make paths descriptive.';
        if (!$keywordy) $tips[] = 'Align slug with the page’s primary keyword/title.';

        return [
            'path' => $path ?: '/',
            'score' => $score,
            'issues' => $tips
        ];
    }

    private function metaAudit(string $title, string $desc, \DOMDocument $doc, string $url): array
    {
        $lenTitle = mb_strlen($title);
        $lenDesc  = mb_strlen($desc);
        $hasTitle = $lenTitle > 0;
        $hasDesc  = $lenDesc > 0;

        $titleOk = $lenTitle >= 15 && $lenTitle <= 60;
        $descOk  = $lenDesc >= 50 && $lenDesc <= 160;

        $score = $this->clamp(
            ($hasTitle ? 25 : 0) + ($titleOk ? 25 : 0) +
            ($hasDesc ? 25 : 0) + ($descOk ? 25 : 0),
            0, 100
        );

        $suggestions = [];
        if (!$hasTitle) $suggestions[] = 'Add a concise, descriptive <title> (≤60 chars).';
        elseif (!$titleOk) $suggestions[] = 'Adjust title length to ~15–60 characters.';

        if (!$hasDesc) $suggestions[] = 'Add a compelling meta description (≤160 chars).';
        elseif (!$descOk) $suggestions[] = 'Adjust meta description to ~50–160 characters.';

        // Optional AI enhancements when missing/weak
        $ai = null;
        if (!$hasTitle || !$hasDesc || !$titleOk || !$descOk) {
            $textForAi = $this->extractReadableText($doc);
            $ai = $this->openaiMeta($textForAi, $url);
        }

        return [
            'title' => $title,
            'description' =
