<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class UrlAnalysisService
{
    public function analyzeUrl(string $url, ?int $userId = null): array
    {
        $url = $this->normalizeUrl($url);

        $resp = Http::timeout(15)
            ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; SemanticSEO/1.0)'])
            ->get($url);

        if (!$resp->ok()) {
            throw new \RuntimeException('Fetch failed with status '.$resp->status());
        }

        $html = $resp->body();
        [$text, $meta] = $this->extractTextAndMeta($html, $url);

        // Minimal heuristic scoring to feed UI
        $scores = $this->scoreItems($text, $meta);

        $overall = (int) round(array_sum($scores) / max(1, count($scores)));

        return [
            'url' => $url,
            'title' => $meta['title'] ?? null,
            'description' => $meta['description'] ?? null,
            'wordCount' => $meta['wordCount'] ?? null,
            'sample' => Str::limit($text, 4000, ''),
            'overall' => $overall,
            'contentScore' => $overall,
            'itemScores' => $this->mapToUiIndices($scores),
            // Optional placeholders for other panels
            'humanPct' => null,
            'aiPct' => null,
            'confidence' => 60,
        ];
    }

    protected function normalizeUrl(string $url): string
    {
        $url = trim($url);
        if (!Str::startsWith($url, ['http://','https://'])) {
            $url = 'https://'.$url;
        }
        return $url;
    }

    protected function extractTextAndMeta(string $html, string $baseUrl): array
    {
        $internalHost = parse_url($baseUrl, PHP_URL_HOST);
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);

        $xpath = new \DOMXPath($dom);

        // Title
        $titleNodes = $dom->getElementsByTagName('title');
        $title = $titleNodes->length ? trim($titleNodes->item(0)->textContent) : null;

        // Meta description
        $md = null;
        foreach ($dom->getElementsByTagName('meta') as $m) {
            $name = strtolower($m->getAttribute('name'));
            if ($name === 'description') {
                $md = trim((string) $m->getAttribute('content'));
                break;
            }
        }

        // Canonical
        $canonical = null;
        foreach ($dom->getElementsByTagName('link') as $lnk) {
            if (strtolower($lnk->getAttribute('rel')) === 'canonical') {
                $canonical = trim((string) $lnk->getAttribute('href'));
                break;
            }
        }

        // H1/H2 count
        $h1 = $dom->getElementsByTagName('h1')->length ?? 0;
        $h2 = $dom->getElementsByTagName('h2')->length ?? 0;

        // Links & images
        $aTags = $dom->getElementsByTagName('a');
        $internal = 0; $external = 0;
        foreach ($aTags as $a) {
            $href = $a->getAttribute('href');
            if (!$href) continue;
            if (strpos($href, 'http://') === 0 || strpos($href,'https://') === 0) {
                $host = parse_url($href, PHP_URL_HOST);
                if ($host && $internalHost && $host === $internalHost) $internal++;
                else $external++;
            }
        }
        $imgs = $dom->getElementsByTagName('img');
        $imgCount = $imgs->length ?? 0;
        $imgAlt = 0;
        foreach ($imgs as $img) {
            if (trim((string) $img->getAttribute('alt')) !== '') $imgAlt++;
        }
        $imgAltRatio = $imgCount ? ($imgAlt / $imgCount) : 0;

        // Extract visible text (rough)
        $body = $dom->getElementsByTagName('body')->item(0);
        $text = $body ? $this->getNodeText($body) : strip_tags($html);
        $text = preg_replace('/\s+/u', ' ', $text ?? '');
        $wordCount = str_word_count($text);

        // Schema detection
        $hasLdJson = false;
        foreach ($dom->getElementsByTagName('script') as $s) {
            if (strtolower($s->getAttribute('type')) === 'application/ld+json') { $hasLdJson = true; break; }
        }

        return [$text, [
            'title' => $title,
            'description' => $md,
            'canonical' => $canonical,
            'h1' => $h1, 'h2' => $h2,
            'internalLinks' => $internal, 'externalLinks' => $external,
            'images' => $imgCount, 'imagesWithAlt' => $imgAlt, 'imgAltRatio' => $imgAltRatio,
            'wordCount' => $wordCount,
            'hasLdJson' => $hasLdJson,
        ]];
    }

    protected function getNodeText(\DOMNode $node): string
    {
        // Skip script/style/noscript
        if (in_array($node->nodeName, ['script','style','noscript'], true)) return '';
        $text = '';
        if ($node->nodeType === XML_TEXT_NODE) {
            $text .= $node->nodeValue;
        }
        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $child) {
                $text .= ' '.$this->getNodeText($child);
            }
        }
        return $text;
    }

    protected function scoreItems(string $text, array $m): array
    {
        $scores = [];

        // Title present + reasonable length
        $scores[] = $m['title'] ? (strlen($m['title']) >= 30 && strlen($m['title']) <= 65 ? 90 : 70) : 30;

        // Meta description present + length
        $scores[] = $m['description'] ? (strlen($m['description']) >= 80 && strlen($m['description']) <= 160 ? 85 : 65) : 25;

        // H1 count (ideally 1)
        $scores[] = $m['h1'] === 1 ? 90 : ($m['h1'] > 1 ? 60 : 40);

        // H2 usage
        $scores[] = $m['h2'] >= 2 ? 80 : 55;

        // Word count
        $scores[] = $m['wordCount'] >= 500 ? 85 : ($m['wordCount'] >= 200 ? 70 : 40);

        // Images & alt
        $scores[] = $m['images'] > 0 ? (int) round(60 + 40 * min(1, $m['imgAltRatio'])) : 50;

        // Internal/external links
        $scores[] = $m['internalLinks'] >= 3 ? 80 : 55;
        $scores[] = $m['externalLinks'] >= 1 ? 75 : 50;

        // Canonical
        $scores[] = !empty($m['canonical']) ? 80 : 50;

        // Structured data presence
        $scores[] = $m['hasLdJson'] ? 80 : 50;

        // Pad to 25 items with average to satisfy UI
        $avg = (int) round(array_sum($scores) / max(1, count($scores)));
        while (count($scores) < 25) { $scores[] = $avg; }

        return $scores;
    }

    protected function mapToUiIndices(array $scores): array
    {
        $map = [];
        for ($i=1; $i<=25; $i++) {
            $map[$i] = isset($scores[$i-1]) ? (int) $scores[$i-1] : null;
        }
        return $map;
    }
}
