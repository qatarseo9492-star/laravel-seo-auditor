<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use OpenAI\Laravel\Facades\OpenAI;

class TopicClusterService
{
    /**
     * Analyze given URLs and return normalized JSON clusters.
     *
     * @param  array  $urls
     * @param  int    $numClusters
     * @return array [ 'clusters' => [ ... ] ]
     */
    public function generateClusters(array $urls, int $numClusters = 5): array
    {
        // 1) Fetch & sanitize text from each URL (basic & safe).
        $docs = [];
        $maxTotal = 10000;          // overall char budget
        $maxPerUrl = 2000;          // per-URL cap inside overall budget
        $budgetLeft = $maxTotal;

        foreach ($urls as $rawUrl) {
            $url = trim($rawUrl);
            if ($url === '' || $budgetLeft <= 0) {
                continue;
            }

            try {
                $resp = Http::timeout(20)->retry(2, 200)->get($url); // 20s + light retry
                if (!$resp->successful()) {
                    continue;
                }
                $html = $resp->body();
            } catch (\Throwable $e) {
                continue; // ignore failing URLs
            }

            // crude HTML -> text
            $text = strip_tags($html);
            $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $text = preg_replace('/\s+/u', ' ', $text) ?? '';
            $text = trim($text);

            // enforce per-URL & global budget
            $slice = mb_substr($text, 0, min($maxPerUrl, $budgetLeft), 'UTF-8');
            $budgetLeft -= mb_strlen($slice, 'UTF-8');

            if ($slice !== '') {
                $docs[] = [
                    'url' => $url,
                    'text' => $slice,
                ];
            }

            if ($budgetLeft <= 0) {
                break;
            }
        }

        // 2) Build a strict JSON-only prompt.
        $instruction = <<<PROMPT
You are a topic clustering expert for website content.

Given multiple pages' plain text, group them into {$numClusters} coherent "topic clusters".
Rules:
- Output ONLY a valid JSON object (no prose) with this exact shape:
{
  "clusters": [
    {
      "name": "short, human-friendly cluster name",
      "description": "2-3 lines explaining the cluster focus and intent",
      "top_keywords": ["keyword1","keyword2","..."],   // 5-12 items
      "member_urls": ["https://...","https://..."]      // URLs from input, each appears in exactly one cluster
    }
  ]
}
- Do not invent URLs.
- Keep clusters distinct and non-overlapping.
- If content is thin, still return reasonable clusters from what is available.

Input pages (URL then text snippet):
PROMPT;

        $pagesBlock = "";
        foreach ($docs as $d) {
            $pagesBlock .= "\n# URL: {$d['url']}\n# TEXT: {$d['text']}\n";
        }

        $finalUserContent = $instruction . $pagesBlock;

        // 3) Call OpenAI Chat Completions with JSON forcing.
        // (Using the Laravel OpenAI facade from openai-php/laravel)
        // Docs: package env & usage (GitHub) + OpenAI API reference. 
        // Model is configurable; default here follows your request.
        $response = OpenAI::chat()->create([
            'model' => env('OPENAI_MODEL', 'gpt-4-turbo-preview'),
            'response_format' => ['type' => 'json_object'], // force JSON
            'temperature' => 0.2,
            'messages' => [
                ['role' => 'system', 'content' => 'You return STRICT valid JSON only.'],
                ['role' => 'user', 'content' => $finalUserContent],
            ],
        ]);

        // 4) Decode the JSON safely.
        $content = $response->choices[0]->message->content ?? '{}';
        $decoded = json_decode($content, true);

        if (!is_array($decoded) || !array_key_exists('clusters', $decoded)) {
            // Fallback minimal structure if model misbehaves
            $decoded = ['clusters' => []];
        }

        return [
            'result' => $decoded,
            'openai_meta' => $response->toArray(), // includes id/model/usage, etc.
        ];
    }

    /**
     * Deterministic signature for exact-list dedupe.
     */
    public static function signatureForUrls(array $urls): string
    {
        $clean = array_values(array_unique(array_filter(array_map('trim', $urls)))));
        sort($clean, SORT_NATURAL | SORT_FLAG_CASE);
        return hash('sha1', json_encode($clean, JSON_UNESCAPED_SLASHES));
    }
}
