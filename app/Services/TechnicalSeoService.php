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
            'description' => $desc,
            'score' => $score,
            'suggestions' => $suggestions,
            'ai' => $ai // ['title' => ..., 'description' => ...] or null
        ];
    }

    private function extractReadableText(\DOMDocument $doc, int $limit=4000): string
    {
        $text = preg_replace('/\s+/', ' ', $doc->textContent ?? '');
        return mb_substr(trim($text), 0, $limit);
    }

    private function altTextSuggest(array $images, DOMXPath $xp, string $pageUrl): array
    {
        $items = [];
        $missing = 0;
        foreach (array_slice($images, 0, self::MAX_ALT_SUGGEST) as $img) {
            $has = !!($img['alt'] ?? '');
            if (!$has) $missing++;
            $items[] = [
                'src' => $img['src'],
                'current_alt' => $img['alt'],
                'suggested_alt' => $has ? null : $this->openaiAltFromContext($img['src'], $xp, $pageUrl)
            ];
        }

        // simple scoring: fewer missing alts => higher score
        $score = $this->clamp(100 - intval(($missing / max(1, count($images))) * 100), 0, 100);
        return ['score' => $score, 'items' => $items];
    }

    private function collectSitePages(string $origin): array
    {
        // Prefer /sitemap.xml, fallback: shallow crawl of homepage links
        $pages = [];
        $internalLinks = [];

        // sitemap
        try {
            $sm = Http::timeout(12)->get($origin . '/sitemap.xml');
            if ($sm->ok()) {
                if (preg_match_all('#<loc>(.*?)</loc>#', $sm->body(), $m)) {
                    foreach ($m[1] as $loc) {
                        if (Str::startsWith($loc, $origin)) $pages[] = $loc;
                        if (count($pages) >= self::MAX_CRAWL_PAGES) break;
                    }
                }
            }
        } catch (\Throwable $e) {}

        if (empty($pages)) {
            // fallback to homepage crawl
            $home = $this->fetch($origin);
            if ($home) {
                $doc = $this->dom($home);
                $xp  = new \DOMXPath($doc);
                $a = $xp->query('//a[@href]');
                foreach ($a as $node) {
                    $href = $node->getAttribute('href');
                    $abs = $this->absUrl($origin, $href);
                    if (Str::startsWith($abs, $origin)) $pages[] = $abs;
                    if (count($pages) >= self::MAX_CRAWL_PAGES) break;
                }
            }
        }

        $pages = array_values(array_unique($pages));
        return [$pages, $internalLinks];
    }

    private function internalLinking(string $pageUrl, ?string $title, ?string $h1, array $sitePages): array
    {
        // Heuristic: topic slug words intersection
        $thisSlug = Str::slug($h1 ?: $title ?: $pageUrl);
        $thisTokens = array_filter(explode('-', $thisSlug));

        $candidates = [];
        foreach ($sitePages as $p) {
            if ($p === $pageUrl) continue;
            $slug = Str::slug(parse_url($p, PHP_URL_PATH) ?? $p);
            $tokens = array_filter(explode('-', $slug));
            $overlap = count(array_intersect($thisTokens, $tokens));
            if ($overlap > 0) {
                $candidates[] = [
                    'url' => $p,
                    'reason' => 'Slug/topic overlap',
                    'overlap' => $overlap
                ];
            }
        }

        // basic score: at least 3 good candidates => 100
        $score = $this->clamp(intval(min(100, count($candidates) * 20)), 0, 100);

        // Optional AI to refine top-10
        $ai = $this->openaiInternalLinks($pageUrl, $candidates);

        return [
            'score' => $score,
            'candidates' => array_slice($candidates, 0, 10),
            'ai' => $ai // optional enriched suggestions
        ];
    }

    private function structureScore(array $tree): int
    {
        // Simple: has H1, uses H2s, minimal jumps
        $hasH1 = !empty($tree) && $tree[0]['level'] === 1;
        $hasH2 = $this->findLevel($tree, 2);
        $deep  = $this->maxDepth($tree);
        $okDepth = $deep <= 4;

        return $this->clamp(
            ($hasH1 ? 40 : 0) + ($hasH2 ? 30 : 0) + ($okDepth ? 30 : 0),
            0, 100
        );
    }

    private function findLevel(array $nodes, int $level): bool
    {
        foreach ($nodes as $n) {
            if ($n['level'] === $level) return true;
            if (!empty($n['children']) && $this->findLevel($n['children'], $level)) return true;
        }
        return false;
    }

    private function maxDepth(array $nodes, int $depth=0): int
    {
        $max = $depth;
        foreach ($nodes as $n) {
            $max = max($max, $this->maxDepth($n['children'] ?? [], $depth + 1));
        }
        return $max;
    }

    private function overall(array $scores): int
    {
        $scores = array_values(array_filter($scores, fn($s) => is_numeric($s)));
        return $this->clamp(intval(array_sum($scores) / max(1, count($scores))), 0, 100);
    }

    private function clamp(int $n, int $min, int $max): int
    {
        return max($min, min($max, $n));
    }

    // ----------------------- OpenAI helpers ----------------------

    private function openaiMeta(?string $pageText, string $url): ?array
    {
        $key = config('services.openai.key');
        if (!$key || !$pageText) return null;

        $prompt = "You are an SEO assistant. Write:\n".
                  "1) A title ≤60 chars\n2) A meta description ≤160 chars\n".
                  "Keep it natural, include a primary keyword from the text.\nURL: {$url}\n---\nContent:\n{$pageText}";

        try {
            $res = $this->chat($key, $prompt);
            // Expect "Title: ...\nDescription: ..."
            if (preg_match('/Title\s*:\s*(.+)\n/i', $res, $m1) &&
                preg_match('/Description\s*:\s*(.+)$/i', $res, $m2)) {
                return ['title' => trim($m1[1]), 'description' => trim($m2[1])];
            }
            // fallback: split lines
            $lines = array_values(array_filter(array_map('trim', explode("\n", $res))));
            return [
                'title' => $lines[0] ?? null,
                'description' => $lines[1] ?? null
            ];
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function openaiAltFromContext(string $imgSrc, DOMXPath $xp, string $pageUrl): ?string
    {
        $key = config('services.openai.key');
        if (!$key) return null;

        // Collect nearby text: first figure/parent/preceding/following text nodes (lightweight heuristic)
        $context = $this->contextTextForImage($xp, $imgSrc);
        $prompt = "Write a concise, descriptive ALT text (≤12 words) for an image.\n".
                  "Make it helpful for users and accessible. No quotes.\n".
                  "Image URL: {$imgSrc}\nContext: {$context}";

        try {
            $res = $this->chat($key, $prompt);
            return trim($res);
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function contextTextForImage(DOMXPath $xp, string $src): string
    {
        // best-effort: return page heading + first paragraph text snippet
        $h1 = $this->text($xp, '//h1');
        $p  = $this->text($xp, '//p');
        return trim(mb_substr($h1 . ' ' . $p, 0, 240));
    }

    private function openaiInternalLinks(string $pageUrl, array $candidates): ?array
    {
        $key = config('services.openai.key');
        if (!$key || empty($candidates)) return null;

        $list = implode("\n", array_map(fn($c) => "- {$c['url']}", array_slice($candidates, 0, 10)));
        $prompt = "You are an SEO specialist. For the page {$pageUrl}, given possible internal link targets below,\n".
                  "pick the 5 best and for each provide: anchor text (2–4 words) and why it helps topical relevance.\n".
                  "Return JSON array with objects: {url, anchor, reason}.\nTargets:\n{$list}";

        try {
            $json = $this->chat($key, $prompt, expectJson: true);
            $data = json_decode($json, true);
            if (is_array($data)) return array_slice($data, 0, 5);
        } catch (\Throwable $e) {}
        return null;
    }

    private function chat(string $key, string $prompt, bool $expectJson=false): string
    {
        // orhanerday/open-ai client style; keep it simple via HTTP for portability
        $payload = [
            'model' => self::OPENAI_MODEL,
            'messages' => [
                ['role' => 'system', 'content' => $expectJson ? 'Return ONLY strict JSON.' : 'Be concise.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.2,
        ];

        $res = Http::withToken($key)
            ->timeout(30)
            ->post('https://api.openai.com/v1/chat/completions', $payload);

        if (!$res->ok()) {
            throw new \RuntimeException('OpenAI error: ' . $res->status());
        }
        return $res->json('choices.0.message.content') ?? '';
    }
}
