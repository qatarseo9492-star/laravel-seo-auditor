<?php

namespace App\Services;

use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ContentDetectionService
{
    protected array $config;
    protected string $hfToken;
    protected int $timeout;
    protected int $retryAttempts;
    protected int $retryBaseDelayMs;

    public function __construct()
    {
        $this->config = config('content-detection');
        $this->hfToken = (string) config('content-detection.huggingface.token', env('HUGGINGFACE_TOKEN', ''));
        $this->timeout = (int) config('content-detection.http.timeout', 15);
        $this->retryAttempts = (int) config('content-detection.http.retries', 2);
        $this->retryBaseDelayMs = (int) config('content-detection.http.retry_base_delay_ms', 400);
    }

    /**
     * Top-level method to detect AI content for one text input.
     */
    public function detect(string $content): array
    {
        $normalized = $this->normalizeInput($content);
        $hash = $this->cacheKey($normalized);

        // 24h cache for identical content
        $ttl = (int) ($this->config['cache']['ttl_seconds'] ?? 86400);

        return Cache::remember($hash, $ttl, function () use ($normalized) {
            $hf = $this->analyzeWithHuggingFace($normalized);
            $stats = $this->analyzeStatistically($normalized);

            $aggregate = $this->aggregateScores($hf, $stats);
            $verdict = $this->verdictFromScore($aggregate['final_score']);

            return [
                'ok' => true,
                'final_score' => $aggregate['final_score'],        // 0..1 (closer to 1 => likely AI)
                'confidence'  => $aggregate['confidence'],         // 0..1
                'verdict'     => $verdict,                         // "ai-like" | "human-like" | "uncertain"
                'by_model'    => $hf['by_model'] ?? [],            // raw probabilities per HF model
                'stats'       => $stats,                            // statistical features
                'used'        => $aggregate['used'],               // which signals used (hf/stats/fallback)
                'timings_ms'  => [
                    'hf'    => $hf['timing_ms'] ?? null,
                    'stats' => $stats['timing_ms'] ?? null,
                ],
            ];
        });
    }

    /**
     * Batch detection for an array of texts.
     */
    public function detectBatch(array $items): array
    {
        $results = [];
        foreach ($items as $i => $content) {
            try {
                $results[$i] = $this->detect((string) $content);
            } catch (\Throwable $e) {
                Log::error('Batch detect error', ['i' => $i, 'e' => $e->getMessage()]);
                $results[$i] = [
                    'ok' => false,
                    'error' => 'Batch item failed: ' . $e->getMessage(),
                ];
            }
        }
        return $results;
    }

    /**
     * Parallel Hugging Face calls with weights and graceful degradation.
     */
    public function analyzeWithHuggingFace(string $content): array
    {
        $start = microtime(true);

        if (empty($this->hfToken)) {
            Log::warning('HuggingFace token not configured.');
            return [
                'by_model' => [],
                'weighted_score' => null,
                'ok' => false,
                'error' => 'Missing Hugging Face token',
                'timing_ms' => (int) ((microtime(true) - $start) * 1000),
            ];
        }

        $models = $this->config['huggingface']['models'] ?? [];
        if (empty($models)) {
            return [
                'by_model' => [],
                'weighted_score' => null,
                'ok' => false,
                'error' => 'No models configured',
                'timing_ms' => (int) ((microtime(true) - $start) * 1000),
            ];
        }

        // Build pooled requests
        $responses = Http::pool(function (Pool $pool) use ($models, $content) {
            $reqs = [];
            foreach ($models as $name => $m) {
                $endpoint = 'https://api-inference.huggingface.co/models/' . $m['id'];
                $reqs[$name] = $pool->withHeaders([
                        'Authorization' => 'Bearer ' . $this->hfToken,
                        'Accept'        => 'application/json',
                        'Content-Type'  => 'application/json',
                    ])
                    ->timeout($this->timeout)
                    ->retry($this->retryAttempts, $this->retryBaseDelayMs, throw: false)
                    ->post($endpoint, [
                        'inputs' => $content,
                        'options' => ['wait_for_model' => false],
                    ]);
            }
            return $reqs;
        });

        $byModel = [];
        $weighted = 0.0;
        $weightsTotal = 0.0;

        foreach ($responses as $name => $resp) {
            $modelCfg = $models[$name];
            $weight = (float) ($modelCfg['weight'] ?? 0.0);
            $prob = null;
            $raw = null;
            $ok = false;
            $err = null;

            try {
                if ($resp->successful()) {
                    $raw = $resp->json();
                    $prob = $this->normalizeHuggingFaceResponse($raw);
                    if ($prob !== null) {
                        $ok = true;
                        $weighted += $prob * $weight;
                        $weightsTotal += $weight;
                    } else {
                        $err = 'No probability found in response';
                    }
                } else {
                    $err = 'HTTP error: ' . $resp->status();
                }
            } catch (\Throwable $e) {
                $err = 'Exception parsing model response: ' . $e->getMessage();
            }

            $byModel[$name] = [
                'id' => $modelCfg['id'],
                'weight' => $weight,
                'prob_ai' => $prob, // 0..1
                'ok' => $ok,
                'error' => $err,
                'raw' => $raw,
            ];
        }

        $finalWeighted = null;
        if ($weightsTotal > 0) {
            $finalWeighted = $weighted / $weightsTotal;
        }

        return [
            'ok' => $finalWeighted !== null,
            'weighted_score' => $finalWeighted,
            'by_model' => $byModel,
            'timing_ms' => (int) ((microtime(true) - $start) * 1000),
        ];
    }

    /**
     * Statistical fallback/reinforcement, fully local.
     */
    public function analyzeStatistically(string $content): array
    {
        $start = microtime(true);

        // Basic tokenization
        $sentences = $this->splitSentences($content);
        $words = $this->tokenizeWords($content);

        $wordCount = max(1, count($words));
        $charCount = mb_strlen($content);
        $sentenceCount = max(1, count($sentences));

        // Word frequencies & lexical diversity
        $freq = [];
        foreach ($words as $w) {
            $w = mb_strtolower($w);
            $freq[$w] = ($freq[$w] ?? 0) + 1;
        }
        $types = count($freq);
        $ttr = $types / $wordCount; // Type-Token Ratio
        $honore = $this->honoreStatistic($freq, $wordCount);

        // Sentence length stats (burstiness proxy)
        $sentenceLengths = array_map(function($s) { return max(1, count($this->tokenizeWords($s))); }, $sentences);
        $meanLen = array_sum($sentenceLengths) / max(1, count($sentenceLengths));
        $variance = 0.0;
        foreach ($sentenceLengths as $len) {
            $variance += pow($len - $meanLen, 2);
        }
        $variance /= max(1, count($sentenceLengths));
        $stdDev = sqrt($variance); // burstiness

        // Punctuation ratios
        $punctCount = preg_match_all('/[.,;:!?]/u', $content) ?: 0;
        $commaCount = preg_match_all('/[,،]/u', $content) ?: 0;
        $punctRatio = $wordCount ? $punctCount / $wordCount : 0.0;
        $commaRatio = $wordCount ? $commaCount / $wordCount : 0.0;

        // Readability: Flesch-Kincaid & Gunning Fog (approx syllable count)
        $syllables = $this->approxSyllableCount($words);
        $fkGrade = 0.39 * ($wordCount / $sentenceCount) + 11.8 * ($syllables / $wordCount) - 15.59;
        $complexWords = $this->countComplexWords($words);
        $gunningFog = 0.4 * (($wordCount / $sentenceCount) + 100 * ($complexWords / $wordCount));

        // Simple pseudo-perplexity via character-level entropy as proxy
        $entropy = $this->charEntropy($content);
        $perplexity = pow(2, $entropy);

        // Heuristic AI-likeness from stats (0..1). Tuned roughly.
        $aiLike = $this->statsToAiProbability([
            'ttr' => $ttr,
            'honore' => $honore,
            'std_dev' => $stdDev,
            'punct_ratio' => $punctRatio,
            'comma_ratio' => $commaRatio,
            'fk_grade' => $fkGrade,
            'gunning_fog' => $gunningFog,
            'perplexity' => $perplexity,
        ]);

        return [
            'ok' => true,
            'timing_ms' => (int) ((microtime(true) - $start) * 1000),
            'features' => [
                'word_count' => $wordCount,
                'sentence_count' => $sentenceCount,
                'type_token_ratio' => $ttr,
                'honore_stat' => $honore,
                'burstiness_std' => $stdDev,
                'punct_ratio' => $punctRatio,
                'comma_ratio' => $commaRatio,
                'flesch_kincaid_grade' => $fkGrade,
                'gunning_fog' => $gunningFog,
                'char_entropy' => $entropy,
                'perplexity_proxy' => $perplexity,
            ],
            'prob_ai' => $aiLike,
        ];
    }

    protected function aggregateScores(array $hf, array $stats): array
    {
        $used = [];
        $scores = [];

        if (($hf['ok'] ?? false) && isset($hf['weighted_score'])) {
            $scores[] = $hf['weighted_score'];
            $used[] = 'huggingface';
        }

        if ($stats['ok'] ?? false) {
            $scores[] = $stats['prob_ai'];
            $used[] = 'stats';
        }

        // If no signals, fallback score neutral
        if (empty($scores)) {
            return [
                'final_score' => 0.5,
                'confidence' => 0.2,
                'used' => ['fallback'],
            ];
        }

        // Weighted average: HF gets 0.7, Stats 0.3 if both present
        if (count($scores) === 2) {
            $final = 0.7 * $scores[0] + 0.3 * $scores[1];
        } else {
            $final = $scores[0];
        }

        // Confidence based on agreement (lower std dev => higher confidence)
        $std = $this->stddev($scores);
        $conf = max(0.0, min(1.0, 1.0 - $std)); // simple mapping

        return [
            'final_score' => round($final, 4),
            'confidence'  => round($conf, 4),
            'used' => $used,
        ];
    }

    protected function verdictFromScore(float $s): string
    {
        $th = $this->config['thresholds'] ?? ['ai' => 0.7, 'human' => 0.3];
        if ($s >= $th['ai']) return 'ai-like';
        if ($s <= $th['human']) return 'human-like';
        return 'uncertain';
    }

    protected function normalizeHuggingFaceResponse($raw): ?float
    {
        // Try common shapes:
        // 1) [{"label":"LABEL_1","score":0.78}, ...] -> choose AI-like prob if present
        if (is_array($raw)) {
            // Case: array of arrays
            if (isset($raw[0]) && is_array($raw[0])) {
                // Try to find label "AI" or highest score
                $max = null;
                foreach ($raw[0] as $row) {
                    if (!is_array($row)) continue;
                    $label = strtoupper((string) ($row['label'] ?? ''));
                    $score = isset($row['score']) ? (float) $row['score'] : null;
                    if ($score === null) continue;

                    // Heuristic: treat LABEL_1 or "AI" as AI probability if present
                    if ($label === 'AI' || $label === 'LABEL_1') {
                        return max(0.0, min(1.0, $score));
                    }
                    $max = max($max ?? 0.0, $score);
                }
                // Fallback: use max class probability as AI-proxy
                if ($max !== null) return max(0.0, min(1.0, $max));
            }

            // Case: {"score": 0.83}
            if (isset($raw['score'])) {
                return max(0.0, min(1.0, (float) $raw['score']));
            }
        }
        return null;
    }

    protected function normalizeInput(string $content): string
    {
        // Trim and collapse weird whitespace
        $content = trim(preg_replace('/\s+/u', ' ', $content));
        return Str::limit($content, (int) $this->config['limits']['max_chars'], '');
    }

    protected function cacheKey(string $content): string
    {
        return 'content_detection:' . hash('sha256', $content);
    }

    protected function splitSentences(string $text): array
    {
        $parts = preg_split('/(?<=[\.\!\?])\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        return array_values(array_filter($parts, fn($s) => mb_strlen(trim($s)) > 0));
    }

    protected function tokenizeWords(string $text): array
    {
        preg_match_all('/\b[\p{L}\p{N}\']+\b/u', $text, $m);
        return $m[0] ?? [];
    }

    protected function approxSyllableCount(array $words): int
    {
        $count = 0;
        foreach ($words as $w) {
            $w = mb_strtolower($w);
            // Very rough approximation: count vowel-groups as syllables
            $count += max(1, preg_match_all('/[aeiouy]+/u', $w));
        }
        return $count;
    }

    protected function countComplexWords(array $words): int
    {
        $c = 0;
        foreach ($words as $w) {
            if (mb_strlen($w) >= 7) $c++;
        }
        return $c;
    }

    protected function charEntropy(string $text): float
    {
        if ($text === '') return 0.0;
        $freq = [];
        $len = mb_strlen($text);
        for ($i = 0; $i < $len; $i++) {
            $ch = mb_substr($text, $i, 1);
            $freq[$ch] = ($freq[$ch] ?? 0) + 1;
        }
        $entropy = 0.0;
        foreach ($freq as $n) {
            $p = $n / $len;
            $entropy += -$p * log($p, 2);
        }
        return $entropy;
    }

    protected function statsToAiProbability(array $f): float
    {
        // Heuristic mapping based on observed tendencies
        $score = 0.5;

        // Lower burstiness may indicate AI
        $score += (0.5 - min(0.5, min(1.0, $f['burstiness_std'] / 20))) * 0.15;

        // Very high TTR tends toward human
        $score += (0.3 - min(0.3, $f['type_token_ratio'])) * 0.1;

        // Punctuation too regular -> AI-ish
        $score += min(0.2, abs($f['punct_ratio'] - 0.05)) * 0.1;

        // FK & Fog clustering in 7–10 often AI-ish for generic prose
        $fk = $f['flesch_kincaid_grade'];
        $score += (abs($fk - 9) / 20) * 0.15;

        // Perplexity proxy: very low entropy can be AI
        $pp = $f['perplexity'];
        $score += ($pp < 8 ? 0.1 : 0.0);

        return max(0.0, min(1.0, $score));
    }

    protected function stddev(array $arr): float
    {
        $n = count($arr);
        if ($n <= 1) return 0.0;
        $mean = array_sum($arr) / $n;
        $acc = 0.0;
        foreach ($arr as $v) $acc += pow($v - $mean, 2);
        return sqrt($acc / $n);
    }

    // --- Extra: Honoré's Statistic (helper) ---
    protected function honoreStatistic(array $freq, int $N): float
    {
        $V = count($freq);
        $V1 = 0;
        foreach ($freq as $k) if ($k === 1) $V1++;
        if ($V === 0 || $N === 0) return 0.0;
        return 100 * log($N) / (1 - ($V1 / max(1, $V)));
    }
}
