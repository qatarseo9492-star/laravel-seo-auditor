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

        $title = $crawler->filter('title')->count() ? $crawler->filter('title')->text() : null;
        $description = $crawler->filterXPath('//meta[@name="description"]')->count()
            ? $crawler->filterXPath('//meta[@name="description"]')->attr('content')
            : null;

        $h1 = $crawler->filter('h1')->each(fn ($node) => $node->text());
        $h2 = $crawler->filter('h2')->each(fn ($node) => $node->text());
        $wordCount = str_word_count(strip_tags($html));

        return [
            'url' => $url,
            'title' => $title,
            'description' => $description,
            'h1' => $h1,
            'h2' => $h2,
            'word_count' => $wordCount,
        ];
    }
}
