<?php

namespace App\Services;

use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Http;

class SeoAuditService
{
    /**
     * Analyze a webpage for SEO signals
     */
    public function analyze(string $url): array
    {
        try {
            // Fetch page content
            $response = Http::get($url);
            $html = $response->body();

            $crawler = new Crawler($html);

            // Extract SEO data
            $title = $crawler->filter('title')->count()
                ? $crawler->filter('title')->text()
                : null;

            $description = $crawler->filterXPath('//meta[@name="description"]')->count()
                ? $crawler->filterXPath('//meta[@name="description"]')->attr('content')
                : null;

            $h1 = $crawler->filter('h1')->each(fn($node) => $node->text());
            $h2 = $crawler->filter('h2')->each(fn($node) => $node->text());
            $wordCount = str_word_count(strip_tags($html));

            // Return structured audit data
            return [
                'url'             => $url,
                'title'           => $title,
                'description'     => $description,
                'h1'              => $h1,
                'h2'              => $h2,
                'word_count'      => $wordCount,
                'recommendations' => $this->generateRecommendations($title, $description, $h1, $wordCount),
                'score'           => $this->calculateScore($title, $description, $h1, $wordCount),
            ];

        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Generate SEO recommendations
     */
    private function generateRecommendations($title, $description, $h1, $wordCount): array
    {
        $recs = [];

        // Check <title>
        if (!$title) {
            $recs[] = "Add a <title> tag.";
        } elseif (strlen($title) < 20 || strlen($title) > 60) {
            $recs[] = "Title length should be between 20 and 60 characters.";
        }

        // Check meta description
        if (!$description) {
            $recs[] = "Add a meta description.";
        } elseif (strlen($description) < 50 || strlen($description) > 160) {
            $recs[] = "Meta description should be between 50 and 160 characters.";
        }

        // Check H1
        if (empty($h1)) {
            $recs[] = "Include at least one <h1> heading.";
        } elseif (count($h1) > 1) {
            $recs[] = "Avoid using multiple <h1> tags.";
        }

        // Check word count
        if ($wordCount < 300) {
            $recs[] = "Content is too short; aim for 300+ words.";
        }

        // Success case
        if (empty($recs)) {
            $recs[] = "Looks good! No major issues found.";
        }

        return $recs;
    }

    /**
     * Calculate a simple SEO score (0–100)
     */
    private function calculateScore($title, $description, $h1, $wordCount): int
    {
        $score = 100;

        if (!$title)           $score -= 20;
        if (!$description)     $score -= 20;
        if (empty($h1))        $score -= 20;
        if ($wordCount < 300)  $score -= 20;

        // Enforce boundaries 0–100
        return max(0, min(100, $score));
    }
}
