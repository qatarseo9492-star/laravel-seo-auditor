<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AnalyzeController extends Controller
{
    public function analyzeJson(Request $request)
    {
        // Alias to the same logic (UI tries this first as GET)
        return $this->analyze($request);
    }

    public function analyze(Request $request)
    {
        $raw = $request->query('url') ?? $request->input('url');
        $url = $this->normalizeUrl($raw);

        if (!$url) {
            return response()->json(['error' => 'Invalid or missing URL.'], 422);
        }

        // ---- Fetch HTML (follow redirects, reasonable timeout). ----
        try {
            $res = Http::withHeaders([
                    'User-Agent' => 'SemanticSEO-Analyzer/1.0 (+https://example.com)',
                    'Accept'     => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                ])
                ->timeout(12)
                ->withOptions(['allow_redirects' => true])
                // If your server has CA bundle, keep verification ON (recommended).
                // ->withoutVerifying() // uncomment only if you have SSL issues in dev
                ->get($url);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Request failed: '.$e->getMessage(),
                'overall' => 0, 'contentScore' => 0, 'itemScores' => (object)[],
            ], 500);
        }

        $status = $res->status();
        $html   = $res->body() ?? '';
        $headersRobots = $res->header('X-Robots-Tag');

        // ---- Parse DOM safely. ----
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();
        $xp = new \DOMXPath($dom);

        $host = parse_url($url, PHP_URL_HOST) ?: '';

        // Helpers
        $getText = function (?string $q) use ($xp) {
            if (!$q) return '';
            $n = $xp->query($q);
            return ($n && $n->length) ? trim($n->item(0)->textContent ?? '') : '';
        };
        $getAttr = function (?string $q, string $attr) use ($xp) {
            if (!$q) return '';
            $n = $xp->query($q);
            if ($n && $n->length) {
                $node = $n->item(0);
                if ($node && $node->attributes && $node->attributes->getNamedItem($attr)) {
                    return trim($node->attributes->getNamedItem($attr)->nodeValue ?? '');
                }
            }
            return '';
        };
        $count = function (string $q) use ($xp) {
            $n = $xp->query($q);
            return $n ? $n->length : 0;
        };

        // Basic fields
        $title         = $getText('//title');
        $metaDesc      = $getAttr("//meta[translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='description']", 'content');
        $canonical     = $getAttr("//link[translate(@rel,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='canonical']", 'href');
        $robotsMeta    = $getAttr("//meta[translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='robots']", 'content');
        $viewport      = $getAttr("//meta[translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='viewport']", 'content');
        $h1c           = $count('//h1');
        $h2c           = $count('//h2');
        $h3c           = $count('//h3');
        $ldJsonCount   = $count("//script[@type='application/ld+json']");
        $imgCount      = $count('//img');
        $videoCount    = $count('//video');
        $buttonCount   = $count('//button');

        // Internal & external links
        $allLinks = $xp->query('//a[@href]');
        $internal = 0; $external = 0;
        if ($allLinks) {
            foreach ($allLinks as $a) {
                /** @var \DOMElement $a */
                $href = $a->getAttribute('href');
                if (!$href) continue;
                if (str_starts_with($href, '#')) continue;
                if (str_starts_with($href, 'mailto:') || str_starts_with($href, 'tel:')) continue;

                // relative links are internal
                if (!preg_match('~^https?://~i', $href)) {
                    $internal++; continue;
                }
                $h = parse_url($href, PHP_URL_HOST) ?: '';
                if ($h && $host && preg_replace('/^www\./i','', $h) === preg_replace('/^www\./i','', $host)) {
                    $internal++;
                } else {
                    $external++;
                }
            }
        }

        // Some text for crude readability
        $textOnly = trim(
            preg_replace('/\s+/', ' ',
                strip_tags($html)
            )
        );
        $wordCount   = max(1, str_word_count($textOnly));
        $sentences   = max(1, preg_match_all('/[\.!\?]+(\s|$)/u', $textOnly, $m));
        $avgWordsPerSentence = $wordCount / $sentences;

        // Heuristics → item scores
        $item = [];

        // 1–5 Content & Keywords
        $item[1] = 65; // intent (unknown) baseline
        $item[2] = min(90, 50 + (int)min(40, $h2c*4 + $h3c*2)); // rough based on headings
        $item[3] = $h1c >= 1 ? 85 : 40;                          // H1 present
        $hasFAQ  = ($ldJsonCount && stripos($html, '"@type"') !== false && stripos($html, 'FAQPage') !== false)
                   || preg_match('~\bFAQ\b~i', $textOnly);
        $item[4] = $hasFAQ ? 85 : 55;
        // Readability ~ good if avg words per sentence 12–24
        $readability = $avgWordsPerSentence;
        $item[5] = ($readability >= 12 && $readability <= 24) ? 85 : (($readability <= 30) ? 65 : 45);

        // 6–9 Technical
        $titleLen = mb_strlen($title);
        $item[6] = ($titleLen >= 50 && $titleLen <= 60) ? 90 : (($titleLen >= 35 && $titleLen <= 70) ? 70 : 45);

        $metaLen = mb_strlen($metaDesc);
        $item[7] = ($metaLen >= 140 && $metaLen <= 160) ? 90 : (($metaLen >= 90 && $metaLen <= 180) ? 70 : 45);

        $item[8] = $canonical ? 95 : 45;

        $noindex  = (stripos($robotsMeta, 'noindex') !== false) || (stripos((string)$headersRobots, 'noindex') !== false);
        $item[9]  = $noindex ? 10 : 80;

        // 10–13 Content Quality
        $hasAuthor = stripos($html, 'author') !== false || preg_match('~rel=["\']author["\']~i', $html);
        $hasDate   = preg_match('~<time[^>]*datetime=~i', $html) || preg_match('~\b\d{4}-\d{2}-\d{2}\b~', $html);
        $item[10]  = ($hasAuthor ? 20 : 0) + ($hasDate ? 20 : 0) + ($ldJsonCount ? 40 : 20); // 40–80
        $item[10]  = max(40, min(90, $item[10]));

        $item[11]  = 60; // uniqueness (unknown)
        $item[12]  = $external >= 3 ? 80 : ($external >= 1 ? 65 : 45);
        $item[13]  = ($imgCount + $videoCount) >= 2 ? 85 : (($imgCount + $videoCount) >= 1 ? 70 : 50);

        // 14–17 Structure & Architecture
        $item[14]  = ($h2c + $h3c) >= 3 ? 85 : (($h2c + $h3c) >= 1 ? 70 : 50);
        $item[15]  = $internal >= 8 ? 85 : ($internal >= 4 ? 70 : 45);

        $path      = parse_url($url, PHP_URL_PATH) ?: '/';
        $hasQuery  = (parse_url($url, PHP_URL_QUERY) !== null);
        $slugOk    = !$hasQuery && mb_strlen($path) <= 80 && !preg_match('~[A-Z _]~', $path);
        $item[16]  = $slugOk ? 85 : 55;

        $hasBreadcrumb = stripos($html, 'BreadcrumbList') !== false || preg_match('~class=["\'][^"\']*breadcrumb~i', $html);
        $item[17]  = $hasBreadcrumb ? 90 : 55;

        // 18–21 UX
        $item[18]  = $viewport ? 90 : 40;
        $lazyCount = preg_match_all('~loading=["\']lazy["\']~i', $html, $m2);
        $item[19]  = $lazyCount ? 75 : 60; // soft heuristic
        $item[20]  = 60; // cannot measure CWV here; neutral-mid
        $ctaCount  = preg_match_all('~\b(get started|sign up|contact|buy now|learn more|try|subscribe)\b~i', $textOnly, $m3);
        $item[21]  = $ctaCount ? 80 : 60;

        // 22–25 Entities & Schema
        $hasArticle = stripos($html, '"@type"') !== false && preg_match('~"@type"\s*:\s*"(Article|NewsArticle|Product|Organization|WebPage)"~i', $html);
        $item[22]   = $hasArticle ? 80 : 55;
        $item[23]   = ($h2c + $h3c) >= 5 ? 75 : 60;
        $item[24]   = $ldJsonCount ? 85 : 40;
        $item[25]   = (stripos($html, '"sameAs"') !== false || stripos($html, '"@type":"Organization"') !== false) ? 85 : 55;

        // Clamp 0–100
        foreach ($item as $k => $v) {
            $item[$k] = max(0, min(100, (int)round($v)));
        }

        // Category avgs
        $avg = function(array $ids) use ($item) {
            $vals = array_map(fn($i)=>$item[$i] ?? 0, $ids);
            return (int) round(array_sum($vals) / max(1, count($vals)));
        };
        $contentCat   = $avg(range(1,5));
        $techCat      = $avg(range(6,9));
        $qualityCat   = $avg(range(10,13));
        $structureCat = $avg(range(14,17));
        $uxCat        = $avg(range(18,21));
        $entityCat    = $avg(range(22,25));

        // contentScore you show in the chip (weight: content+quality+structure)
        $contentScore = (int) round(($contentCat*0.45) + ($qualityCat*0.30) + ($structureCat*0.25));

        // overall = weighted mix of all categories
        $overall = (int) round(
            $contentCat*0.25 + $techCat*0.18 + $qualityCat*0.18 + $structureCat*0.17 + $uxCat*0.12 + $entityCat*0.10
        );

        // crude “human vs AI” indicators (purely heuristic)
        $humanPct = max(20, min(95, 70 - abs(18 - $avgWordsPerSentence) + ($overall - 60) * 0.3));
        $aiPct    = max(5, 100 - $humanPct);

        // Small, friendly report fields your Blade displays
        $out = [
            'httpStatus'   => $status,
            'titleLen'     => $titleLen,
            'metaLen'      => $metaLen,
            'canonical'    => $canonical ?: '—',
            'robots'       => $robotsMeta ?: ($headersRobots ?: '—'),
            'viewport'     => $viewport ? 'Yes' : '—',
            'headings'     => "H1:$h1c • H2:$h2c • H3:$h3c",
            'internalLinks'=> $internal,
            'schema'       => $ldJsonCount ? "Yes ($ldJsonCount)" : '—',

            'itemScores'   => $item,         // keys 1..25
            'contentScore' => $contentScore, // for the “Content:” chip
            'overall'      => $overall,      // drives the water gauge
            'humanPct'     => (int) round($humanPct),
            'aiPct'        => (int) round($aiPct),
        ];

        return response()->json($out);
    }

    // ---------------- helpers ----------------

    private function normalizeUrl(?string $u): ?string
    {
        if (!$u) return null;
        $u = trim($u);
        if ($u === '') return null;

        // Add scheme if missing
        if (!preg_match('~^https?://~i', $u)) {
            $u = 'https://' . ltrim($u, '/');
        }

        // Validate
        if (!filter_var($u, FILTER_VALIDATE_URL)) return null;

        $scheme = parse_url($u, PHP_URL_SCHEME);
        if (!in_array(strtolower($scheme), ['http', 'https'], true)) return null;

        return $u;
    }
}
