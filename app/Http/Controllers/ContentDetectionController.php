use Illuminate\Support\Facades\Http;
// use Illuminate\Support\Str; // uncomment if you use Str::limit

public function detectUrl(Request $request)
{
    $v = Validator::make($request->all(), [
        'url' => ['required', 'url', 'max:2048'],
    ]);

    if ($v->fails()) {
        return response()->json(['ok' => false, 'errors' => $v->errors()], 422);
    }

    $url = (string) $request->input('url');

    try {
        // Fetch the page (10s timeout, sane UA)
        $res = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (compatible; MultiModelDetector/1.0; +https://yourdomain.example)',
                'Accept' => 'text/html,application/xhtml+xml'
            ])
            ->timeout(10)
            ->get($url);

        if (!$res->successful()) {
            return response()->json(['ok' => false, 'error' => 'Fetch failed: HTTP '.$res->status()], 502);
        }

        $html = $res->body();
        $text = $this->extractMainText($html);

        // Enforce max length from config
        $max = (int) config('content-detection.limits.max_chars', 20000);
        $text = mb_substr(trim(preg_replace('/\s+/u',' ', $text)), 0, $max);

        if (mb_strlen($text) < 20) {
            return response()->json(['ok' => false, 'error' => 'Insufficient textual content extracted.'], 422);
        }

        $result = $this->svc->detect($text);

        // Save to DB like /detect
        $det = new \App\Models\ContentDetection();
        $det->content = encrypt($text);
        $det->ai_score = $result['final_score'];
        $det->confidence = $result['confidence'];
        $det->model_used = implode('+', $result['used']);
        $det->features = $result['stats']['features'] ?? [];
        $det->verdict = $result['verdict'];
        $det->save();

        return response()->json([
            'ok' => true,
            'data' => $result,
            // Send a trimmed copy for the UI textarea:
            'extracted' => mb_substr($text, 0, 20000),
            'id' => $det->id
        ]);
    } catch (\Throwable $e) {
        Log::error('detectUrl failed', ['e' => $e->getMessage()]);
        return response()->json(['ok' => false, 'error' => 'URL analysis failed: '.$e->getMessage()], 500);
    }
}

/**
 * Very lightweight main-text extractor: strips scripts/styles,
 * prefers <article>, <main>, and long <p> blocks, with fallbacks.
 */
protected function extractMainText(string $html): string
{
    // Remove scripts/styles/noscripts
    $clean = preg_replace('#<(script|style|noscript)[^>]*>.*?</\\1>#si', ' ', $html);
    // Grab title/og:description quickly
    $title = '';
    if (preg_match('#<title[^>]*>(.*?)</title>#si', $clean, $m)) {
        $title = trim(html_entity_decode(strip_tags($m[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }
    $og = '';
    if (preg_match('#<meta[^>]+property=["\']og:description["\'][^>]*content=["\']([^"\']+)["\'][^>]*>#si', $clean, $m)) {
        $og = trim(html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    // Prefer <article> or <main>
    $candidates = [];
    foreach (['article','main'] as $tag) {
        if (preg_match("#<{$tag}[^>]*>(.*?)</{$tag}>#si", $clean, $m)) {
            $candidates[] = $m[1];
        }
    }

    // If no article/main, fall back to body content
    if (empty($candidates)) {
        if (preg_match('#<body[^>]*>(.*?)</body>#si', $clean, $m)) {
            $candidates[] = $m[1];
        } else {
            $candidates[] = $clean;
        }
    }

    // Extract paragraphs that are likely content (longer than 60 chars)
    $pick = '';
    $bestScore = -1;
    foreach ($candidates as $chunk) {
        $chunk = preg_replace('#<(header|footer|nav|aside)[^>]*>.*?</\\1>#si', ' ', $chunk);
        $chunk = preg_replace('#<figure[^>]*>.*?</figure>#si', ' ', $chunk);
        $chunk = preg_replace('#<[^>]+>#', ' ', $chunk);
        $chunk = html_entity_decode($chunk, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $chunk = preg_replace('/\s+/u', ' ', $chunk);
        $paragraphs = preg_split('/(?<=\.)\s+/', $chunk);

        $score = 0;
        $collected = [];
        foreach ($paragraphs as $p) {
            $p = trim($p);
            if (mb_strlen($p) >= 60) {
                $collected[] = $p;
                $score += mb_strlen($p);
            }
        }
        if ($score > $bestScore) {
            $bestScore = $score;
            $pick = implode(' ', $collected);
        }
    }

    $base = trim($pick);
    // Prefix title/og if useful
    $prefix = trim($title . (strlen($og) ? ' — ' . $og : ''));
    $text = trim($prefix . (strlen($base) ? "\n\n" . $base : ''));

    return $text !== '' ? $text : trim(strip_tags($clean));
}
