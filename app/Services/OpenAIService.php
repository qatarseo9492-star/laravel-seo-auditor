<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    protected $apiKey;

    public function __construct()
    {
        // This securely reads the key from config/services.php, which reads from your .env file.
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
            Log::error('OpenAI API key is not configured in .env or config/services.php.');
            return ['error' => 'OpenAI API Key is missing in the server configuration.'];
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
        $cleanText = strip_tags($textContent);
        $cleanText = preg_replace('/\s+/', ' ', $cleanText);
        $truncatedText = mb_substr($cleanText, 0, 15000);

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
```

### Step 2: Final Configuration Checklist

Now, just follow this final checklist to ensure everything is connected correctly.

1.  **File `app/Services/OpenAIService.php`:**
    * ✅ Ensure it contains the secure code from the block above.

2.  **File `.env`:**
    * ✅ Make sure you have added your API key to the bottom of this file.
    * `OPENAI_API_KEY="sk-proj-YourSecretApiKeyGoesHere"`

3.  **File `config/services.php`:**
    * ✅ Confirm this file has the `openai` array entry that connects to the `.env` variable.
    * `'openai' => ['key' => env('OPENAI_API_KEY')]`



### Step 3: Clear Your Cache (Mandatory)

This is the final, crucial step. To make Laravel read your new `.env` variable, you **must** run this command in your project's terminal:

```bash
php artisan config:cache

