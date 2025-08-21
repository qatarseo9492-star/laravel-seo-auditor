<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class SeoOptimizerController extends Controller
{
    public function showForm()
    {
        return view('seo.optimizer');
    }

    public function analyze(Request $request)
    {
        $url = $request->input('url');
        $keyword = trim($request->input('keyword'));

        if (!$url || !$keyword) {
            return back()->withErrors(['msg' => 'Please provide both URL and keyword.']);
        }

        $client = new Client();
        $response = $client->get($url);
        $html = (string) $response->getBody();

        $crawler = new Crawler($html);

        // Extract key elements
        $title = $crawler->filter('title')->count() ? $crawler->filter('title')->text() : '';
        $h1 = $crawler->filter('h1')->count() ? $crawler->filter('h1')->text() : '';
        $descriptionTag = $crawler->filter('meta[name="description"]')->count()
            ? $crawler->filter('meta[name="description"]')->attr('content')
            : '';

        $bodyText = $crawler->filter('body')->count() ? $crawler->filter('body')->text() : '';

        // Keyword relevance analysis
        $keywordLower = strtolower($keyword);
        $bodyLower = strtolower($bodyText);

        $count = substr_count($bodyLower, $keywordLower);
        $score = min(100, $count * 10); // simple scoring system

        // Optimization checks
        $recommendations = [];

        if (!str_contains(strtolower($title), $keywordLower)) {
            $recommendations[] = "Add the keyword to the <title> tag.";
        }
        if (!str_contains(strtolower($h1), $keywordLower)) {
            $recommendations[] = "Include the keyword in the H1 heading.";
        }
        if (!str_contains(strtolower($descriptionTag), $keywordLower)) {
            $recommendations[] = "Include the keyword in the meta description.";
        }
        if ($count < 3) {
            $recommendations[] = "Use the keyword more often in the body text (currently $count times).";
        }
        if ($count > 10) {
            $recommendations[] = "Avoid keyword stuffing (currently $count times).";
        }
        if (empty($recommendations)) {
            $recommendations[] = "Great job! The keyword seems well optimized.";
        }

        return view('seo.optimizer', [
            'analysis' => [
                'url' => $url,
                'keyword' => $keyword,
                'title' => $title,
                'h1' => $h1,
                'description' => $descriptionTag,
                'body_count' => $count,
                'score' => $score,
                'recommendations' => $recommendations
            ]
        ]);
    }
}
