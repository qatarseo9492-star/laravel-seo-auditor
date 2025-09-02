<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AnalyzerController extends Controller
{
    /**
     * POST /api/semantic-analyze — API endpoint (no CSRF)
     * Add ?debug=1 to include diagnostics in the response.
     */
    public function analyze(Request $request)
    {
        $validated = $request->validate([
            'url'     => ['nullable','url'],
            'content' => ['nullable','string'],
            'language' => ['nullable','string','max:10'],
            'debug'   => ['nullable'],
        ]);

        // 1) Input collection
        $text = trim((string)($validated['content'] ?? ''));
        if (!$text && !empty($validated['url'])) {
            try {
                $resp = Http::timeout(15)->get($validated['url']);
                if ($resp->ok()) {
                    $html = $resp->body();
                    $text = trim(strip_tags($html));
                    $text = Str::limit($text, 12000, ' [truncated]');
                }
            } catch (\Throwable $e) {
                Log::warning('Fetch URL failed', ['err' => $e->getMessage()]);
            }
        }
        if (!$text) {
            return response()->json(['ok' => false, 'error' => 'No content to analyze. Provide url or content.'], 422);
        }

        // 2) OpenAI config
        $apiKey  = config('services.openai.key')
                ?: env('OPENAI_API_KEY')
                ?: ($_ENV['OPENAI_API_KEY'] ?? null)
                ?: ($_SERVER['OPENAI_API_KEY'] ?? null);
        $org     = config('services.openai.org', env('OPENAI_ORG'));
        $baseUrl = rtrim(config('services.openai.base_url', env('OPENAI_BASE_URL', 'https://api.openai.com/v1')), '/');
        $model   = config('services.openai.model', env('OPENAI_MODEL', 'gpt-4o-mini'));
        $timeout = (int) config('services.openai.timeout', (int) env('OPENAI_TIMEOUT', 60));
        $debug   = (bool) ($validated['debug'] ?? false);

        if (empty($apiKey)) {
            Log::warning('OPENAI key missing');
            return response()->json(['ok'=>false,'error'=>'OPENAI_API_KEY missing in config/env'], 500);
        }

        $language = $validated['language'] ?? 'en';

        // 3) Messages
        $systemText = "You are an SEO content optimization model. Return only the requested JSON with exact keys.";
        $userInstruction = "Analyze this content and produce fields: nlp_score (0-100), topic_coverage {percentage,total,covered}, content_gaps {missing_topics:[{term,severity}]}, schema {suggested:[{type,why}]}, intent {label,why}, grade {letter}. Language: ".$language.". Content:\n\n".$text;

        $system = ["role" => "system", "content" => $systemText];
        $user   = ["role" => "user",   "content" => $userInstruction];

        // 4) Helpers
        $headersFn = function () use ($org) {
            $h = ['Accept' => 'application/json'];
            if ($org) $h['OpenAI-Organization'] = $org;
            return $h;
        };
        $parseJson = function (?string $s) {
            if (!$s || !is_string($s)) return null;
            $j = json_decode($s, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($j)) return $j;
            $first = strpos($s, '{'); $last = strrpos($s, '}');
            if ($first !== false && $last !== false && $last > $first) {
                $slice = substr($s, $first, $last - $first + 1);
                $j2 = json_decode($slice, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($j2)) return $j2;
            }
            return null;
        };
        $fallbackScore = function(string $t) {
            // Local heuristic so UI never shows 0 when AI is unavailable
            $wc = str_word_count($t);
            if ($wc >= 900) return 86;
            if ($wc >= 600) return 78;
            if ($wc >= 400) return 70;
            if ($wc >= 250) return 62;
            if ($wc >= 120) return 54;
            return 45;
        };

        // 5) Desired JSON schema (for Chat structured output)
        $schema = [
            "type" => "object",
            "additionalProperties" => false,
            "properties" => [
                "content_optimization" => [
                    "type" => "object",
                    "additionalProperties" => true,
                    "properties" => [
                        "nlp_score" => ["type"=>"integer","minimum"=>0,"maximum"=>100],
                        "topic_coverage" => [
                            "type"=>"object",
                            "properties"=>[
                                "percentage"=>["type"=>"integer","minimum"=>0,"maximum"=>100],
                                "total"=>["type"=>"integer","minimum"=>0],
                                "covered"=>["type"=>"integer","minimum"=>0]
                            ],
                            "required"=>["percentage","total","covered"]
                        ],
                        "content_gaps" => [
                            "type"=>"object",
                            "properties"=>[
                                "missing_topics"=>[
                                    "type"=>"array",
                                    "items"=>[
                                        "type"=>"object",
                                        "properties"=>[
                                            "term"=>["type"=>"string"],
                                            "severity"=>["type"=>"string","enum"=>["bad","warn"]]
                                        ],
                                        "required"=>["term","severity"]
                                    ]
                                ]
                            ],
                            "required"=>["missing_topics"]
                        ],
                        "schema" => [
                            "type"=>"object",
                            "properties"=>[
                                "suggested"=>[
                                    "type"=>"array",
                                    "items"=>[
                                        "type"=>"object",
                                        "properties"=>[
                                            "type"=>["type"=>"string"],
                                            "why"=>["type"=>"string"]
                                        ],
                                        "required"=>["type","why"]
                                    ]
                                ]
                            ],
                            "required"=>["suggested"]
                        ],
                        "intent" => [
                            "type"=>"object",
                            "properties"=>[
                                "label"=>["type"=>"string"],
                                "why"=>["type"=>"string"]
                            ],
                            "required"=>["label","why"]
                        ],
                        "grade" => [
                            "type"=>"object",
                            "properties"=>[
                                "letter"=>["type"=>"string","enum"=>["A+","A","B","C","D","E","F"]]
                            ],
                            "required"=>["letter"]
                        ]
                    ],
                    "required" => ["nlp_score","topic_coverage","content_gaps","schema","intent","grade"]
                ]
            ],
            "required" => ["content_optimization"]
        ];

        $diag = ["flow" => []];
        $co = null; $jsonText = null;
        $scoreSource = 'fallback';

        // ---- STRATEGY 1: Chat Completions with JSON Schema
        try {
            $diag["flow"][] = "chat_schema";
            $chatPayload = [
                "model" => $model,
                "response_format" => [
                    "type" => "json_schema",
                    "json_schema" => [
                        "name"   => "ContentOptimization",
                        "schema" => $schema
                    ]
                ],
                "messages" => [
                    ["role"=>"system","content"=>"Return only JSON for the given schema. No prose."],
                    ["role"=>"user","content"=>$userInstruction]
                ]
            ];
            $r1 = Http::withToken($apiKey)
                ->withHeaders($headersFn())
                ->timeout($timeout)
                ->retry(2, 250)
                ->post($baseUrl.'/chat/completions', $chatPayload);
            $diag["chat_schema"] = [
                "status" => $r1->status(),
                "ok"     => $r1->ok(),
                "body"   => substr($r1->body(), 0, 400),
            ];
            if ($r1->ok()) {
                $d1 = $r1->json();
                if (isset($d1['choices'][0]['message']['content'])) {
                    $jsonText = $d1['choices'][0]['message']['content'];
                    $coParsed = $parseJson($jsonText);
                    if (is_array($coParsed)) {
                        $co = isset($coParsed['content_optimization']) ? $coParsed['content_optimization'] : $coParsed;
                        $scoreSource = 'ai';
                    }
                }
            }
        } catch (\Throwable $e) {
            $diag["chat_schema_exception"] = $e->getMessage();
        }

        // ---- STRATEGY 2: Chat Completions with json_object
        if (!$co) {
            try {
                $diag["flow"][] = "chat_json_object";
                $chatPayload2 = [
                    "model" => $model,
                    "response_format" => ["type"=>"json_object"],
                    "messages" => [
                        ["role"=>"system","content"=>"Return only JSON with keys exactly: content_optimization{nlp_score,topic_coverage{percentage,total,covered},content_gaps{missing_topics[]},schema{suggested[]},intent{label,why},grade{letter}}. No prose."],
                        ["role"=>"user","content"=>$userInstruction]
                    ]
                ];
                $r2 = Http::withToken($apiKey)
                    ->withHeaders($headersFn())
                    ->timeout($timeout)
                    ->retry(2, 250)
                    ->post($baseUrl.'/chat/completions', $chatPayload2);
                $diag["chat_json_object"] = [
                    "status" => $r2->status(),
                    "ok"     => $r2->ok(),
                    "body"   => substr($r2->body(), 0, 400),
                ];
                if ($r2->ok()) {
                    $d2 = $r2->json();
                    if (isset($d2['choices'][0]['message']['content'])) {
                        $jsonText = $d2['choices'][0]['message']['content'];
                        $coParsed = $parseJson($jsonText);
                        if (is_array($coParsed)) {
                            $co = isset($coParsed['content_optimization']) ? $coParsed['content_optimization'] : $coParsed;
                            $scoreSource = 'ai';
                        }
                    }
                }
            } catch (\Throwable $e) {
                $diag["chat_json_object_exception"] = $e->getMessage();
            }
        }

        // ---- STRATEGY 3: Responses API (NO response_format; use input_text blocks)
        if (!$co) {
            try {
                $diag["flow"][] = "responses";
                $responsesPayload = [
                    "model" => $model,
                    "input" => [
                        [
                            "role" => "system",
                            "content" => [
                                ["type" => "input_text", "text" => "Return ONLY raw JSON for this schema (no prose): " . json_encode($schema)]
                            ]
                        ],
                        [
                            "role" => "user",
                            "content" => [
                                ["type" => "input_text", "text" => $userInstruction]
                            ]
                        ]
                    ],
                    "temperature" => 0.2,
                ];
                $r3 = Http::withToken($apiKey)
                    ->withHeaders($headersFn())
                    ->timeout($timeout)
                    ->retry(2, 250)
                    ->post($baseUrl.'/responses', $responsesPayload);
                $diag["responses"] = [
                    "status" => $r3->status(),
                    "ok"     => $r3->ok(),
                    "body"   => substr($r3->body(), 0, 400),
                ];
                if ($r3->ok()) {
                    $d3 = $r3->json();
                    // Try output_text first
                    if (isset($d3['output_text'])) {
                        $jsonText = $d3['output_text'];
                    } elseif (isset($d3['output']) && is_array($d3['output'])) {
                        foreach ($d3['output'] as $o) {
                            if (!empty($o['content']) && is_array($o['content'])) {
                                foreach ($o['content'] as $c) {
                                    if (isset($c['type']) && $c['type'] === 'output_text' && isset($c['text'])) {
                                        $jsonText = $c['text']; break 2;
                                    }
                                    if (isset($c['text']) && is_string($c['text'])) { // lenient
                                        $jsonText = $c['text']; break 2;
                                    }
                                }
                            }
                        }
                    }
                    if ($jsonText) {
                        $coParsed = $parseJson($jsonText);
                        if (is_array($coParsed)) {
                            $co = isset($coParsed['content_optimization']) ? $coParsed['content_optimization'] : $coParsed;
                            $scoreSource = 'ai';
                        }
                    }
                }
            } catch (\Throwable $e) {
                $diag["responses_exception"] = $e->getMessage();
            }
        }

        // 6) Normalize for UI
        $co = is_array($co) ? $co : [];
        $from_ai = isset($co['nlp_score']);

        if (!isset($co['schema_suggestions'])) {
            if (isset($co['schema']['suggested']) && is_array($co['schema']['suggested'])) {
                $co['schema_suggestions'] = array_values(array_filter(array_map(function($it){
                    $t = is_array($it) ? ($it['type'] ?? '') : (string)$it;
                    $w = is_array($it) ? ($it['why'] ?? '')  : '';
                    $t = trim($t);
                    return $t ? trim($t . ($w ? ' — ' . $w : '')) : '';
                }, $co['schema']['suggested'] ?? [])));
            } else {
                $co['schema_suggestions'] = [];
            }
        }

        if (!isset($co['readability_intent'])) {
            $intent = $co['intent']['label'] ?? ($co['intent'] ?? 'unknown');
            $grade  = $co['grade']['letter'] ?? ($co['grade'] ?? 'C');
            $co['readability_intent'] = [
                'intent'      => is_array($intent)?($intent['label']??'unknown'):(is_string($intent)?$intent:'unknown'),
                'grade_level' => is_array($grade)?($grade['letter']??'C'):(is_string($grade)?$grade:'C'),
            ];
        }

        if (isset($co['topic_coverage']) && is_array($co['topic_coverage'])) {
            $tc = $co['topic_coverage'];
            $co['topic_coverage'] = [
                'percentage' => (int) max(0, min(100, (int)($tc['percentage'] ?? 0))),
                'total'      => (int) max(0, (int)($tc['total'] ?? 0)),
                'covered'    => (int) max(0, (int)($tc['covered'] ?? 0)),
            ];
        } else {
            $co['topic_coverage'] = ['percentage'=>0,'total'=>0,'covered'=>0];
        }

        if (!isset($co['content_gaps']['missing_topics']) || !is_array($co['content_gaps']['missing_topics'])) {
            $co['content_gaps'] = ['missing_topics'=>[]];
        }

        // Final score: AI if present, else fallback based on content length
        $nlp = isset($co['nlp_score']) ? (int) max(1, min(100, (int)$co['nlp_score'])) : $fallbackScore($text);
        $co['nlp_score'] = $nlp;

        $resp = [
            "ok" => true,
            "overall_score" => $nlp,
            "content_optimization" => $co,
            "score_source" => $from_ai ? 'ai' : $scoreSource, // 'ai' or 'fallback'
        ];
        if ($debug) {
            $resp["diagnostics"] = array_merge($diag, [
                "had_ai_score"    => $from_ai,
                "org_header_used" => (bool) $org,
                "word_count"      => str_word_count($text),
            ]);
        }
        return response()->json($resp);
    }

    /**
     * ✅ NEW METHOD: Handles the Technical SEO analysis using OpenAI.
     *
     * This is the missing method that you need to add to your controller.
     * It receives the URL, sends it to OpenAI for analysis, and returns
     * the structured data that the frontend expects.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function analyzeTechnicalSeo(Request $request)
    {
        $request->validate([
            'url' => 'required|url'
        ]);

        $urlToAnalyze = $request->input('url');
        $apiKey = env('OPENAI_API_KEY');

        if (!$apiKey) {
            return response()->json(['message' => 'OpenAI API key is not configured.'], 500);
        }

        try {
            // =================================================================
            // 💡 IMPORTANT: This is where you call the OpenAI API.
            // You will need to construct the appropriate prompt to get the
            // analysis you need in a structured JSON format.
            // The example below is a conceptual guide.
            // =================================================================
            
            // Example Prompt (you will need to refine this)
            $prompt = "Analyze the technical SEO of the page at {$urlToAnalyze}. Provide a detailed analysis in JSON format. The JSON object must include: a 'score' (0-100), 'internal_linking' suggestions as an array of objects each with 'text' and 'anchor' keys, 'url_structure' analysis as an object with 'clarity_score' and 'suggestion', 'meta_optimization' as an object with 'title' and 'description', 'alt_text_suggestions' as an array of objects with 'image_src' and 'suggestion', a 'site_structure_map' as a simple HTML ul list string, and a final list of 'suggestions' as an array of objects each with 'text' and 'type' keys.";

            // Example OpenAI API Call
            $response = Http::withToken($apiKey)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4-turbo', // Or your preferred model
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a world-class Technical SEO expert.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'response_format' => ['type' => 'json_object']
            ]);

            if ($response->failed()) {
                 return response()->json(['message' => 'Failed to get a response from OpenAI.', 'details' => $response->body()], 502);
            }

            $analysisResult = $response->json('choices.0.message.content');

            // The result from OpenAI should be a JSON string, so we decode it.
            $decodedResult = json_decode($analysisResult, true);
            
            // Check if the JSON is valid before returning it
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['message' => 'OpenAI returned invalid JSON.', 'raw_response' => $analysisResult], 500);
            }

            // ✅ FIX: Sanitize the response to ensure arrays are arrays.
            // This prevents the '.map is not a function' error on the frontend.
            if (!isset($decodedResult['internal_linking']) || !is_array($decodedResult['internal_linking'])) {
                $decodedResult['internal_linking'] = [];
            }
            if (!isset($decodedResult['alt_text_suggestions']) || !is_array($decodedResult['alt_text_suggestions'])) {
                $decodedResult['alt_text_suggestions'] = [];
            }
            if (!isset($decodedResult['suggestions']) || !is_array($decodedResult['suggestions'])) {
                $decodedResult['suggestions'] = [];
            }


            return response()->json($decodedResult);

        } catch (\Exception $e) {
            Log::error('Technical SEO Analysis Failed: ' . $e->getMessage());
            return response()->json(['message' => 'An unexpected error occurred during the analysis.'], 500);
        }
    }

    /** CSRF-protected proxy for the Blade */
    public function analyzeWeb(Request $request) { return $this->analyze($request); }

    /** Legacy alias for older routes */
    public function semanticAnalyze(Request $request) { return $this->analyze($request); }

    /** PSI placeholder */
    public function psi(Request $request) { return response()->json(['ok'=>true,'note'=>'PSI proxy not implemented here.']); }

    public function aiCheck(Request $request) { return response()->json(['ok'=>true,'note'=>'aiCheck stub']); }
    public function topicClusterAnalyze(Request $request) { return response()->json(['ok'=>true,'note'=>'topicClusterAnalyze stub']); }
}

