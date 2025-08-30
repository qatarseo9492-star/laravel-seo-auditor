<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.openai.key');
    }

    /**
     * Analyzes content to generate SEO optimization data.
     *
     * @param string $content The HTML content of the user's page.
     * @return array|null The structured optimization data or null on failure.
     */
    public function getOptimizationData(string $content): ?array
    {
        if (!$this->apiKey) {
            Log::error('OpenAI API key is not configured.');
            return null;
        }

        $prompt = $this->buildPrompt($content);

        $response = Http::withToken($this->apiKey)
            ->timeout(120) // Allow up to 2 minutes for the API call
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o', // Use a powerful model
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an expert SEO Content Analyst. Your response must be a valid JSON object.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0.2,
                'response_format' => ['type' => 'json_object'],
            ]);

        if ($response->failed()) {
            Log::error('OpenAI API request failed.', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return null;
        }
        
        // The response is already a JSON object because of 'response_format'
        return json_decode($response->json()['choices'][0]['message']['content'], true);
    }

    /**
     * Builds the detailed prompt for the OpenAI API.
     *
     * @param string $textContent The text content to analyze.
     * @return string The formatted prompt.
     */
    private function buildPrompt(string $textContent): string
    {
        // A simple way to clean up the content for analysis
        $cleanText = strip_tags($textContent);
        $cleanText = preg_replace('/\s+/', ' ', $cleanText);
        $truncatedText = mb_substr($cleanText, 0, 15000); // Truncate to avoid being too long

        return "
        Analyze the following text content from a webpage and provide a detailed content optimization report.

        Content to Analyze:
        \"\"\"
        {$truncatedText}
        \"\"\"

        Based on the content, generate a JSON object with the following exact structure:
        {
            \"nlp_score\": <A score from 0-100 evaluating overall content quality, depth, and relevance.>,
            \"topic_coverage\": {
                \"covered\": <An estimated number of key topics covered, e.g., 18>,
                \"total\": <An estimated total number of relevant topics, e.g., 25>,
                \"percentage\": <The percentage calculated from covered/total>
            },
            \"content_gaps\": {
                \"missing_count\": <A count of missing topics, e.g., 7>,
                \"missing_topics\": [
                    {\"term\": \"<first missing topic>\", \"severity\": \"<bad or warn>\"},
                    {\"term\": \"<second missing topic>\", \"severity\": \"<bad or warn>\"}
                ]
            },
            \"schema_suggestions\": [\"<e.g., Article>\", \"<e.g., FAQPage>\"],
            \"readability_intent\": {
                \"intent\": \"<Informational, Commercial, or Transactional>\",
                \"grade_level\": <An estimated reading grade level, e.g., 8>
            }
        }
        ";
    }
}
