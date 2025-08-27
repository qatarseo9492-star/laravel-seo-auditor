<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Services\ContentDetectionService;
use App\Models\ContentDetection; // if you store history

class ContentDetectionController extends Controller
{
    public function __construct(private ContentDetectionService $svc) {}

    // QUICK PING so the UI status pill can show Connected without content
    private function maybePing(Request $request)
    {
        if ($request->boolean('ping')) {
            return response()->json(['ok' => true, 'data' => ['ping' => true]]);
        }
        return null;
    }

    public function detect(Request $request)
    {
        if ($ping = $this->maybePing($request)) return $ping;

        $v = Validator::make($request->all(), [
            'content' => ['required','string','min:20'],
        ]);
        if ($v->fails()) {
            return response()->json(['ok' => false, 'errors' => $v->errors()], 422);
        }

        $content = (string) $request->input('content');
        $result  = $this->svc->detect($content);

        // (Optional) store history
        if (class_exists(ContentDetection::class)) {
            $det = new ContentDetection();
            $det->content    = encrypt($content);
            $det->ai_score   = $result['final_score'] ?? null;
            $det->confidence = $result['confidence'] ?? null;
            $det->model_used = implode('+', $result['used'] ?? []);
            $det->features   = $result['stats']['features'] ?? [];
            $det->verdict    = $result['verdict'] ?? null;
            $det->save();
        }

        return response()->json(['ok' => true, 'data' => $result], 200);
    }

    public function detectUrl(Request $request)
    {
        if ($ping = $this->maybePing($request)) return $ping;

        $v = Validator::make($request->all(), [
            'url' => ['required','url','max:2048'],
        ]);
        if ($v->fails()) {
            return response()->json(['ok' => false, 'errors' => $v->errors()], 422);
        }

        $url = (string) $request->input('url');

        try {
            $res = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (MultiModelDetector/1.0)',
                    'Accept'     => 'text/html,application/xhtml+xml',
                ])->timeout(10)->get($url);

            if (!$res->successful()) {
                return response()->json(['ok' => false, 'error' => 'Fetch failed: HTTP '.$res->status()], 502);
            }

            $html = $res->body();
            $text = $this->extractMainText($html);
            $max  = (int) config('content-detection.limits.max_chars', 20000);
            $text = mb_substr(trim(preg_replace('/\s+/u',' ', $text)), 0, $max);

            if (mb_strlen($text) < 20) {
                return response()->json(['ok' => false, 'error' => 'Insufficient textual content extracted.'], 422);
            }

            $result = $this->svc->detect($text);

            if (class_exists(ContentDetection::class)) {
                $det = new ContentDetection();
                $det->content    = encrypt($text);
                $det->ai_score   = $result['final_score'] ?? null;
                $det->confidence = $result['confidence'] ?? null;
                $det->model_used = implode('+', $result['used'] ?? []);
                $det->features   = $result['stats']['features'] ?? [];
                $det->verdict    = $result['verdict'] ?? null;
                $det->save();
            }

            return response()->json([
                'ok'        => true,
                'data'      => $result,
                'extracted' => mb_substr($text, 0, 20000),
            ], 200);

        } catch (\Throwable $e) {
            Log::error('detectUrl failed', ['e' => $e->getMessage()]);
            return response()->json(['ok' => false, 'error' => 'URL analysis failed: '.$e->getMessage()], 500);
        }
    }

    // very light extractor; you can replace with a proper parser later
    protected function extractMainText(string $html): string
    {
        $clean = preg_replace('#<(script|style|noscript)[^>]*>.*?</\\1>#si', ' ', $html);
        $title = '';
        if (preg_match('#<title[^>]*>(.*?)</title>#si', $clean, $m)) {
            $title = trim(html_entity_decode(strip_tags($m[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }
        $og = '';
        if (preg_match('#<meta[^>]+property=["\']og:description["\'][^>]*content=["\']([^"\']+)["\'][^>]*>#si', $clean, $m)) {
            $og = trim(html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }
        $candidates = [];
        foreach (['article','main'] as $tag) {
            if (preg_match("#<{$tag}[^>]*>(.*?)</{$tag}>#si", $clean, $m)) {
                $candidates[] = $m[1];
            }
        }
        if (empty($candidates)) {
            if (preg_match('#<body[^>]*>(.*?)</body>#si', $clean, $m)) $candidates[] = $m[1]; else $candidates[] = $clean;
        }
        $pick = ''; $best = -1;
        foreach ($candidates as $chunk) {
            $chunk = preg_replace('#<(header|footer|nav|aside)[^>]*>.*?</\\1>#si', ' ', $chunk);
            $chunk = preg_replace('#<figure[^>]*>.*?</figure>#si', ' ', $chunk);
            $chunk = preg_replace('#<[^>]+>#', ' ', $chunk);
            $chunk = html_entity_decode($chunk, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $chunk = preg_replace('/\s+/u', ' ', $chunk);
            $paras = preg_split('/(?<=\.)\s+/', $chunk);
            $score = 0; $col = [];
            foreach ($paras as $p) { $p = trim($p); if (mb_strlen($p) >= 60) { $col[]=$p; $score+=mb_strlen($p);} }
            if ($score > $best) { $best = $score; $pick = implode(' ', $col); }
        }
        $base = trim($pick);
        $prefix = trim($title . (strlen($og) ? ' — ' . $og : ''));
        $text = trim($prefix . (strlen($base) ? "\n\n" . $base : ''));
        return $text !== '' ? $text : trim(strip_tags($clean));
    }
}
