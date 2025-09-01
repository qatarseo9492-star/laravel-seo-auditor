<?php

namespace App\Services;

use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Http;

class SeoAuditService
{
    public function analyze($url)
    {
        try {
            $response = Http::get($url);
        } catch (\Exception $e) {
            return ['error' => 'Could not fetch URL: ' . $e->getMessage()];
        }

        $html = $response->body();
        $crawler = new Crawler($html);

        // Extract basic info
        $title = $crawler->filter('title')->count() ? $crawler->filter('title')->text() : null;
        $description = $crawler->filterXPath('//meta[@name="description"]')->count()
            ? $crawler->filterXPath('//meta[@name="description"]')->attr('content')
            : null;

        $h1 = $crawler->filter('h1')->each(fn ($node) => $node->text());
        $h2 = $crawler->filter('h2')->each(fn ($node) => $node->text());
        $schemas = $crawler->filter('script[type="application/ld+json"]')->each(fn ($n) => $n->text());
        $wordCount = str_word_count(strip_tags($html));

        // === Scoring ===
        $score = 0;
        $recommendations = [];

        if ($title) { $score += 15; } else { $recommendations[] = "Add a <title> tag (+15)"; }
        if ($description) { $score += 10; } else { $recommendations[] = "Add a meta description (+10)"; }
        if ($h1) { $score += 10; } else { $recommendations[] = "Add an H1 heading (+10)"; }
        if (count($h2) >= 2) { $score += 10; } else { $recommendations[] = "Add more H2/H3 subheadings (+10)"; }
        if ($schemas) { $score += 15; } else { $recommendations[] = "Add structured data schema (+15)"; }

        if ($wordCount > 800) { $score += 15; }
        elseif ($wordCount > 400) { $score += 8; $recommendations[] = "Expand article to 1000+ words (+7)"; }
        else { $recommendations[] = "Thin content – add 800+ words (+15)"; }

        return [
            'url' => $url,
            'title' => $title,
            'description' => $description,
            'h1' => $h1,
            'h2' => $h2,
            'schema_count' => count($schemas),
            'word_count' => $wordCount,
            'score' => $score,
            'recommendations' => $recommendations
        ];
    }
}
