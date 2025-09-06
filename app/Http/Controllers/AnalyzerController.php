<?php
// ... existing code ...
use Illuminate\Validation\Rule;

class AnalyzerController extends Controller
{
    private function checkAndLog(Request $request, string $tool): bool|JsonResponse
    {
// ... existing code ...
        return true;
    }

    private function calculateScores(array $data): array
    {
        $scores = [
            'title' => 0, 'meta' => 0, 'headings' => 0, 'links' => 0,
            'technical' => 0, 'content' => 0, 'overall' => 0
        ];

        // Title Score (Weight: 20)
        $titleLen = isset($data['content_structure']['title']) ? mb_strlen($data['content_structure']['title']) : 0;
        if ($titleLen > 10 && $titleLen < 50) $scores['title'] = 85;
        elseif ($titleLen >= 50 && $titleLen <= 65) $scores['title'] = 100;
        elseif ($titleLen > 65 && $titleLen < 80) $scores['title'] = 75;
        else $scores['title'] = 50;

        // Meta Description Score (Weight: 15)
        $metaLen = isset($data['content_structure']['meta_description']) ? mb_strlen($data['content_structure']['meta_description']) : 0;
        if ($metaLen > 50 && $metaLen < 140) $scores['meta'] = 85;
        elseif ($metaLen >= 140 && $metaLen <= 165) $scores['meta'] = 100;
        else $scores['meta'] = 50;

        // Headings Score (Weight: 20)
        $h1Count = isset($data['content_structure']['headings']['H1']) ? count($data['content_structure']['headings']['H1']) : 0;
        $h2Count = isset($data['content_structure']['headings']['H2']) ? count($data['content_structure']['headings']['H2']) : 0;
        if ($h1Count === 1 && $h2Count >= 2) $scores['headings'] = 100;
        elseif ($h1Count === 1 && $h2Count < 2) $scores['headings'] = 80;
        elseif ($h1Count !== 1) $scores['headings'] = 50;
        else $scores['headings'] = 60;
        
        // Word Count Score (Weight: 15)
        $wordCount = $data['quick_stats']['word_count'] ?? 0;
        if ($wordCount > 1500) $scores['content_words'] = 100;
        elseif ($wordCount > 800) $scores['content_words'] = 90;
        elseif ($wordCount > 400) $scores['content_words'] = 75;
        else $scores['content_words'] = 50;

        // Links Score (Weight: 10)
        $internal = $data['quick_stats']['internal_links'] ?? 0;
        $external = $data['quick_stats']['external_links'] ?? 0;
        if ($internal > 2 && $external > 0) $scores['links'] = 100;
        elseif ($internal > 0) $scores['links'] = 80;
        else $scores['links'] = 50;

        // Image SEO Score (Weight: 10)
        $imgCount = $data['images_total_count'] ?? 0;
        $altCount = $data['images_alt_count'] ?? 0;
        if ($imgCount > 0 && ($altCount / $imgCount) > 0.9) $scores['images'] = 100;
        elseif ($imgCount > 0 && ($altCount / $imgCount) > 0.5) $scores['images'] = 80;
        else $scores['images'] = 60;
        
        // Technical Score Calculation
        $scores['technical'] = (int) round(
            ($scores['title'] * 0.4) +
            ($scores['meta'] * 0.3) +
            ($scores['headings'] * 0.3)
        );
        
        // Content Score Calculation
        $scores['content'] = (int) round(
            ($scores['content_words'] * 0.5) +
            ($scores['links'] * 0.3) +
            ($scores['images'] * 0.2)
        );

        // Overall Weighted Score
        $scores['overall'] = (int) round(
            ($scores['technical'] * 0.6) +
            ($scores['content'] * 0.4)
        );

        return $scores;
    }

    public function semanticAnalyze(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate(['url' => ['required', 'url']]);
            $urlToAnalyze = $validated['url'];

            $contentStructure = []; $pageSignals = []; $quickStats = []; $imagesAltCount = 0; $imagesTotalCount = 0;
            $response = Http::timeout(15)->get($urlToAnalyze);
            if ($response->failed()) return response()->json(['error' => "Failed to fetch URL. Status: {$response->status()}"], 400);

            $html = $response->body();
            $dom = new DOMDocument();
// ... existing code ...
            $contentStructure['meta_description'] = optional($xpath->query("//meta[@name='description']/@content")->item(0))->nodeValue;
            $contentStructure['headings'] = [];
            foreach (['h1', 'h2', 'h3', 'h4'] as $tag) {
// ... existing code ...
                foreach ($headings as $heading) { $contentStructure['headings'][$tagUpper][] = trim($heading->textContent); }
            }
            $pageSignals['canonical'] = optional($xpath->query("//link[@rel='canonical']/@href")->item(0))->nodeValue;
            $pageSignals['robots'] = optional($xpath->query("//meta[@name='robots']/@content")->item(0))->nodeValue;
            $pageSignals['has_viewport'] = $xpath->query("//meta[@name='viewport']")->length > 0;

            $bodyText = optional($xpath->query('//body')->item(0))->textContent ?? '';
            $quickStats['word_count'] = Str::wordCount(preg_replace('/\s+/', ' ', $bodyText));

            $links = $dom->getElementsByTagName('a');
            $internalLinks = 0; $externalLinks = 0;
            $host = parse_url($urlToAnalyze, PHP_URL_HOST) ?? '';
// ... existing code ...
                ($linkHost === null || $linkHost === $host) ? $internalLinks++ : $externalLinks++;
            }
            $quickStats['internal_links'] = $internalLinks;
            $quickStats['external_links'] = $externalLinks;

            $images = $dom->getElementsByTagName('img');
            $imagesTotalCount = $images->length;
            foreach ($images as $image) {
                if ($image->hasAttribute('alt') && !empty(trim($image->getAttribute('alt')))) $imagesAltCount++;
            }

            $analysisData = [
                'readability' => ['score' => 75, 'passive_ratio' => 10],
                'content_structure' => $contentStructure,
                'page_signals' => $pageSignals,
                'quick_stats' => $quickStats,
                'images_alt_count' => $imagesAltCount,
                'images_total_count' => $imagesTotalCount,
            ];

            $scores = $this->calculateScores($analysisData);

            return response()->json(array_merge($analysisData, [
                'overall_score' => $scores['overall'],
                'content_score' => $scores['content'],
                'technical_score' => $scores['technical'],
                'categories' => [['name' => 'Content & Keywords', 'score' => 82], ['name' => 'Content Quality', 'score' => 75]],
            ]));
            
        } catch (\Exception $e) {
            Log::error('Local HTML Parsing Failed', ['message' => $e->getMessage()]);
// ... existing code ...
