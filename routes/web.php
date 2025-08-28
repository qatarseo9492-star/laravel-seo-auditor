<?php
/**
 * =============================
 * FILE: routes/web.php
 * =============================
 */
?>
<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnalyzerController;

Route::get('/', [AnalyzerController::class, 'home'])->name('home');
Route::get('/semantic-analyzer', [AnalyzerController::class, 'semantic'])->name('semantic');
Route::get('/ai-content-checker', [AnalyzerController::class, 'aiChecker'])->name('aiChecker');
Route::get('/topic-cluster', [AnalyzerController::class, 'topicCluster'])->name('topicCluster');

// Auth (Laravel Breeze/Fortify/Jetstream will supply these; fall back to placeholders if not installed)
// These routes are typical; remove if your app already registers them.
Route::get('/login', fn() => view('auth.login'));
Route::get('/register', fn() => view('auth.register'));
?>

<?php
/**
 * =============================
 * FILE: routes/api.php
 * =============================
 */
?>
<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnalyzerController;

Route::post('/semantic-analyze', [AnalyzerController::class, 'semanticAnalyze']);
Route::post('/ai-check', [AnalyzerController::class, 'aiCheck']);
Route::post('/topic-cluster', [AnalyzerController::class, 'topicClusterAnalyze']);
?>

<?php
/**
 * ============================================
 * FILE: app/Http/Controllers/AnalyzerController.php
 * ============================================
 */
?>
<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AnalyzerController extends Controller
{
    public function home()
    {
        return view('home');
    }

    public function semantic()
    {
        return view('analyzers.semantic');
    }

    public function aiChecker()
    {
        return view('analyzers.ai');
    }

    public function topicCluster()
    {
        return view('analyzers.topic');
    }

    /**
     * =========================
     * API: Semantic Analyzer
     * =========================
     */
    public function semanticAnalyze(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'target_keyword' => 'nullable|string|max:120'
        ]);

        $url = $request->input('url');
        $target = trim($request->input('target_keyword',''));

        $fetch = $this->fetchUrl($url, $request->input('ua', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120 Safari/537.36'));

        if (!$fetch['ok']) {
            return response()->json([
                'ok' => false,
                'error' => $fetch['error'] ?? 'Fetch failed'
            ], 422);
        }

        $html = $fetch['body'];
        $baseHost = $this->getHost($url);

        // Parse DOM
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);

        // Title
        $titleNode = $xpath->query('//title')->item(0);
        $title = $titleNode ? trim($titleNode->textContent) : '';

        // Meta description
        $metaDescNode = $xpath->query("//meta[translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='description']/@content")->item(0);
        $metaDescription = $metaDescNode ? trim($metaDescNode->nodeValue) : '';

        // Headings
        $headings = [];
        for ($i=1;$i<=6;$i++) {
            $nodes = $xpath->query("//h{$i}");
            $headings["h{$i}"] = [];
            foreach ($nodes as $n) {
                $headings["h{$i}"][] = trim($n->textContent);
            }
        }

        // Images & alt
        $imgNodes = $xpath->query('//img');
        $imgTotal = $imgNodes->length;
        $imgMissingAlt = 0;
        foreach ($imgNodes as $img) {
            $alt = $img->getAttribute('alt');
            if ($alt === null || trim($alt) === '') $imgMissingAlt++;
        }

        // Links
        $aNodes = $xpath->query('//a[@href]');
        $internal = 0; $external = 0; $anchors = [];
        foreach ($aNodes as $a) {
            $href = trim($a->getAttribute('href'));
            if ($href === '' || str_starts_with($href, '#') || str_starts_with($href, 'mailto:')) continue;
            $host = $this->getHost($this->absUrl($href, $url));
            if ($host === $baseHost) $internal++; else $external++;
            $anchors[] = [
                'text' => trim($a->textContent),
                'href' => $href,
                'type' => $host === $baseHost ? 'internal' : 'external'
            ];
        }

        // Structured data presence
        $hasJsonLd = $xpath->query("//script[@type='application/ld+json']")->length > 0;
        $hasMicrodata = $xpath->query('//*[@itemscope or @itemtype]')->length > 0;
        $hasRdfa = $xpath->query('//*[@typeof or @property]')->length > 0;

        // Raw text (very light boilerplate removal by removing scripts/styles)
        foreach (['//script','//style','//noscript'] as $rm) {
            foreach ($xpath->query($rm) as $node) { $node->parentNode?->removeChild($node); }
        }
        $text = trim(preg_replace('/\s+/u',' ', $dom->textContent ?? ''));
        $textLen = mb_strlen($text);
        $htmlLen = mb_strlen($html);
        $textToHtmlRatio = $htmlLen > 0 ? round(($textLen/$htmlLen)*100,2) : 0.0;

        // Readability (very approximate Flesch Reading Ease)
        $sentences = preg_split('/(?<=[\.!?])\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $words = preg_split('/\s+/u', preg_replace('/[^\p{L}\p{N}\s\-\']+/u',' ', $text), -1, PREG_SPLIT_NO_EMPTY);
        $wordCount = max(count($words), 1);
        $sentenceCount = max(count($sentences), 1);
        $syllables = 0;
        foreach ($words as $w) { $syllables += $this->syllableGuess($w); }
        $flesch = max(0, min(100, round(206.835 - (1.015*($wordCount/$sentenceCount)) - (84.6*($syllables/$wordCount)), 2)));

        // Heading structure flags
        $missingH1 = count($headings['h1']) === 0;
        $skippedLevels = $this->checkHeadingSkips($headings);

        // Keyword coverage (very light)
        $kwCoverage = null;
        if ($target !== '') {
            $kwCount = substr_count(mb_strtolower($text), mb_strtolower($target));
            $kwCoverage = [
                'target' => $target,
                'occurrences' => $kwCount,
                'in_title' => Str::contains(Str::lower($title), Str::lower($target)),
                'in_meta' => Str::contains(Str::lower($metaDescription), Str::lower($target)),
            ];
        }

        // Overall score (simple weighted model)
        $score = 0; $max = 100;
        // Title present & length
        if ($title !== '') $score += 12; // base
        $len = mb_strlen($title);
        if ($len >= 30 && $len <= 60) $score += 8; // ideal length bonus
        // Meta description
        if ($metaDescription !== '') $score += 10;
        // Images alt completeness
        $altRatio = ($imgTotal > 0) ? (1 - ($imgMissingAlt/$imgTotal)) : 1.0;
        $score += (int)round(20*$altRatio);
        // Links mix
        if ($internal > 0) $score += 5;
        if ($external > 0) $score += 5;
        // Structured data presence
        if ($hasJsonLd || $hasMicrodata || $hasRdfa) $score += 10;
        // Readability
        $score += (int)round($flesch/10); // up to 10 points
        // Text/HTML ratio (prefer >=15%)
        if ($textToHtmlRatio >= 15) $score += 10; elseif ($textToHtmlRatio >= 8) $score += 5;
        // Heading structure
        if (!$missingH1) $score += 5; if (!$skippedLevels) $score += 5;
        $score = max(0, min($max, $score));

        // Sentiment (very naive)
        $sentiment = 'Neutral';

        // Recommendations
        $recs = [];
        if ($missingH1) $recs[] = ['severity'=>'Critical','text'=>'Add a clear, single H1 heading reflecting the main topic.'];
        if ($len < 30 || $len > 60) $recs[] = ['severity'=>'Warning','text'=>'Adjust title tag length to ~30–60 characters.'];
        if ($metaDescription === '') $recs[] = ['severity'=>'Warning','text'=>'Add a compelling meta description (120–160 characters).'];
        if ($imgMissingAlt > 0) $recs[] = ['severity'=>'Warning','text'=>"Add alt text to {$imgMissingAlt} image(s) to improve accessibility and SEO."];
        if ($internal === 0) $recs[] = ['severity'=>'Warning','text'=>'Add relevant internal links to connect related content.'];
        if ($external === 0) $recs[] = ['severity'=>'Info','text'=>'Consider citing authoritative external sources.'];
        if (!$hasJsonLd && !$hasMicrodata && !$hasRdfa) $recs[] = ['severity'=>'Info','text'=>'Add structured data (JSON‑LD) appropriate to the page type.'];
        if ($textToHtmlRatio < 15) $recs[] = ['severity'=>'Info','text'=>'Increase main content depth (raise text‑to‑HTML ratio).'];
        if ($skippedLevels) $recs[] = ['severity'=>'Info','text'=>'Fix heading hierarchy (avoid jumping levels).'];
        if ($kwCoverage && !$kwCoverage['in_title']) $recs[] = ['severity'=>'Info','text'=>'Work the target keyword (or close variant) into the title naturally.'];

        return response()->json([
            'ok' => true,
            'overall_score' => $score,
            'semantic_core' => [
                'primary_topics' => [], // placeholder; a full TF‑IDF requires a corpus
                'topic_cloud' => [],
                'sentiment' => $sentiment,
            ],
            'content_structure' => [
                'title' => $title,
                'meta_description' => $metaDescription,
                'headings' => $headings,
                'missing_h1' => $missingH1,
                'skipped_levels' => $skippedLevels,
                'readability_flesch' => $flesch,
                'text_to_html_ratio' => $textToHtmlRatio,
            ],
            'intent_coverage' => [
                'search_intent' => 'Informational', // heuristic placeholder
                'semantic_coverage_score' => null,
                'gaps' => [],
            ],
            'technical_seo' => [
                'image_alt_missing' => $imgMissingAlt,
                'image_count' => $imgTotal,
                'links' => [
                    'internal' => $internal,
                    'external' => $external,
                    'anchors' => $anchors,
                ],
                'structured_data' => [
                    'json_ld' => $hasJsonLd,
                    'microdata' => $hasMicrodata,
                    'rdfa' => $hasRdfa,
                ],
                'target_keyword' => $kwCoverage,
            ],
            'recommendations' => $recs,
        ]);
    }

    /**
     * =========================
     * API: AI Content Checker (heuristic, offline)
     * =========================
     */
    public function aiCheck(Request $request)
    {
        $request->validate([
            'text' => 'nullable|string',
            'url'  => 'nullable|url'
        ]);

        $text = trim($request->input('text',''));
        if ($text === '' && $request->filled('url')) {
            $fetch = $this->fetchUrl($request->input('url'));
            if ($fetch['ok']) {
                $text = $this->extractVisibleText($fetch['body']);
            }
        }
        if ($text === '') {
            return response()->json(['ok'=>false,'error'=>'No text found to analyze.'], 422);
        }

        $clean = Str::of($text)->squish()->toString();
        $sentences = preg_split('/(?<=[\.!?])\s+/u', $clean, -1, PREG_SPLIT_NO_EMPTY);
        $words = preg_split('/\s+/u', preg_replace('/[^\p{L}\p{N}\s\-\']+/u',' ', $clean), -1, PREG_SPLIT_NO_EMPTY);
        $wc = max(count($words), 1);

        // Features
        $types = count(array_unique(array_map('mb_strtolower', $words)));
        $ttr = $types / $wc; // type-token ratio
        $avgSentLen = $wc / max(count($sentences),1);
        $punctVariety = preg_match_all('/[,:;\-\—\(\)\"\']/u', $clean);
        $stopwordRate = $this->stopwordRate($words);

        // Heuristic scoring (0..1 AI-likeliness)
        $ai = 0.0;
        if ($ttr < 0.35) $ai += 0.25;        // repetitive vocab
        if ($avgSentLen between 16 and 28) { /* neutral */ } // stylistic middle
        if ($avgSentLen < 12 || $avgSentLen > 32) $ai += 0.2; // too short/too long
        if ($punctVariety < max(5, $wc*0.01)) $ai += 0.2;     // low punctuation variety
        if ($stopwordRate < 0.38 || $stopwordRate > 0.62) $ai += 0.15; // odd function-word mix

        $ai = min(1.0, max(0.0, $ai));
        $aiPct = round($ai*100);

        return response()->json([
            'ok' => true,
            'ai_probability_percent' => $aiPct,
            'human_probability_percent' => 100 - $aiPct,
            'metrics' => compact('ttr','avgSentLen','punctVariety','stopwordRate','wc')
        ]);
    }

    /**
     * =========================
     * API: Topic Cluster Identification (very light TF style)
     * =========================
     */
    public function topicClusterAnalyze(Request $request)
    {
        $request->validate([
            'text' => 'nullable|string',
            'url'  => 'nullable|url',
            'top_k' => 'nullable|integer|min:5|max:50'
        ]);

        $text = trim($request->input('text',''));
        if ($text === '' && $request->filled('url')) {
            $fetch = $this->fetchUrl($request->input('url'));
            if ($fetch['ok']) $text = $this->extractVisibleText($fetch['body']);
        }
        if ($text === '') return response()->json(['ok'=>false,'error'=>'No text found to analyze.'], 422);

        $topK = (int)($request->input('top_k', 20));
        $tokens = $this->tokenize($text);
        $freq = [];
        foreach ($tokens as $t) {
            if ($this->isStop($t)) continue;
            $stem = $this->stem($t);
            $freq[$stem] = ($freq[$stem] ?? 0) + 1;
        }
        arsort($freq);
        $top = array_slice($freq, 0, $topK, true);

        // Naive clusters by first 4 chars of stem
        $clusters = [];
        foreach ($top as $stem => $count) {
            $key = mb_substr($stem, 0, 4);
            $clusters[$key]['label'] = $key;
            $clusters[$key]['stems'][] = ['term'=>$stem,'count'=>$count];
            $clusters[$key]['weight'] = ($clusters[$key]['weight'] ?? 0) + $count;
        }
        usort($clusters, fn($a,$b)=> $b['weight'] <=> $a['weight']);

        return response()->json([
            'ok' => true,
            'top_terms' => $top,
            'clusters' => array_values($clusters),
        ]);
    }

    // =====================
    // Helpers
    // =====================
    private function fetchUrl(string $url, string $ua = 'Mozilla/5.0', int $timeout = 15): array
    {
        $ch = curl_init();
        curl_setopt_array($ch,[
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_CONNECTTIMEOUT => $timeout,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_USERAGENT => $ua,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
            ],
        ]);
        $body = curl_exec($ch);
        $err = curl_error($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($body === false || $status >= 400) {
            return ['ok'=>false,'error'=>$err ?: ("HTTP status ".$status)];
        }
        return ['ok'=>true,'body'=>$body,'status'=>$status];
    }

    private function getHost(string $url): ?string
    {
        $p = parse_url($url);
        return $p['host'] ?? null;
    }

    private function absUrl(string $href, string $base): string
    {
        if (str_starts_with($href,'http://') || str_starts_with($href,'https://')) return $href;
        $p = parse_url($base);
        $scheme = $p['scheme'] ?? 'https';
        $host = $p['host'] ?? '';
        if (str_starts_with($href,'/')) return $scheme.'://'.$host.$href;
        $path = isset($p['path']) ? preg_replace('#/[^/]*$#','/',$p['path']) : '/';
        return $scheme.'://'.$host.$path.$href;
    }

    private function extractVisibleText(string $html): string
    {
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);
        foreach (['//script','//style','//noscript'] as $rm) {
            foreach ($xpath->query($rm) as $node) { $node->parentNode?->removeChild($node); }
        }
        $text = trim(preg_replace('/\s+/u',' ', $dom->textContent ?? ''));
        return $text;
    }

    private function syllableGuess(string $word): int
    {
        $w = mb_strtolower($word);
        $w = preg_replace('/[^a-z]/','', $w);
        if ($w === '') return 1;
        $vowelGroups = preg_match_all('/[aeiouy]+/', $w);
        $vowelGroups = $vowelGroups ?: 1;
        // silent e
        if (str_ends_with($w,'e') && $vowelGroups>1) $vowelGroups--;
        return max(1, $vowelGroups);
    }

    private function checkHeadingSkips(array $headings): bool
    {
        $last = 0; $skipped = false;
        for ($i=1;$i<=6;$i++) {
            $count = count($headings["h{$i}"] ?? []);
            if ($count>0) {
                if ($last>0 && ($i - $last)>1) { $skipped = true; break; }
                $last = $i;
            }
        }
        return $skipped;
    }

    private function stopwordRate(array $words): float
    {
        $stops = $this->stopwords();
        $c=0;$w=0;
        foreach ($words as $wd) { $w++; if (isset($stops[mb_strtolower($wd)])) $c++; }
        return $w>0? $c/$w : 0.5;
    }

    private function tokenize(string $text): array
    {
        $text = mb_strtolower($text);
        $text = preg_replace('/[^\p{L}\p{N}\s\-\']+/u',' ', $text);
        $parts = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        return array_values(array_filter($parts, fn($t)=> mb_strlen($t)>=3));
    }

    private function isStop(string $w): bool
    {
        return isset($this->stopwords()[mb_strtolower($w)]);
    }

    private function stem(string $w): string
    {
        // Tiny, language-agnostic-ish stemmer (very rough)
        $w = mb_strtolower($w);
        $w = preg_replace('/(ing|edly|edly|ed|ly|s)$/u','', $w);
        return $w;
    }

    private function stopwords(): array
    {
        static $s = null;
        if ($s !== null) return $s;
        $list = [
            'a','an','and','the','of','in','on','for','to','from','with','by','is','are','was','were','be','been','it','that','this','as','at','or','if','but','not','your','you','we','our','their','they','i','he','she','them','these','those','can','will','would','should','could','about','into','over','than','then','there','here','out','up','down','across','between','after','before','during','also','when','where','how','what','which','why','who','whom'
        ];
        $s = array_fill_keys($list, true);
        return $s;
    }
}
?>

<?php
/**
 * ============================================
 * FILE: resources/views/layouts/app.blade.php
 * ============================================
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>@yield('title','Semantic SEO Master Analyzer 2.0')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui'] },
          colors: {
            brand: {
              50:'#eff6ff',100:'#dbeafe',200:'#bfdbfe',300:'#93c5fd',400:'#60a5fa',
              500:'#3b82f6',600:'#2563eb',700:'#1d4ed8',800:'#1e40af',900:'#1e3a8a'
            },
          },
        }
      }
    }
  </script>
  <style>
    html { scroll-behavior: smooth; }
  </style>
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased">
  <!-- Top Menu Bar (above header) -->
  <nav class="w-full border-b border-slate-200 bg-white/90 backdrop-blur supports-[backdrop-filter]:bg-white/60 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="h-14 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-2 font-semibold">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-brand-600"><path d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 5v10h-2V7h2zm-1 12a1.25 1.25 0 110-2.5 1.25 1.25 0 010 2.5z"/></svg>
          <span>Semantic SEO Master Analyzer 2.0</span>
        </a>
        <div class="hidden md:flex items-center gap-6">
          <a href="{{ route('semantic') }}" class="hover:text-brand-700">Semantic Analyzer</a>
          <a href="{{ route('aiChecker') }}" class="hover:text-brand-700">AI Content Checker</a>
          <a href="{{ route('topicCluster') }}" class="hover:text-brand-700">Topic Analysis</a>
        </div>
        <div class="flex items-center gap-3">
          <a href="/login" class="text-sm px-3 py-1.5 rounded-lg border border-slate-300 hover:bg-slate-100">Login</a>
          <a href="/register" class="text-sm px-3 py-1.5 rounded-lg bg-brand-600 text-white hover:bg-brand-700">Signup</a>
        </div>
      </div>
    </div>
  </nav>

  @yield('content')

  <footer class="border-t mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 text-sm text-slate-600">
      <p>© {{ date('Y') }} Semantic SEO Master Analyzer 2.0 · Built with ❤️ for clarity, speed and actionable insights.</p>
    </div>
  </footer>
</body>
</html>

<?php
/**
 * ============================================
 * FILE: resources/views/home.blade.php
 * ============================================
 */
?>
@extends('layouts.app')
@section('title','Home — Semantic SEO Master Analyzer 2.0')
@section('content')
  <!-- Header / Hero under top menu -->
  <header class="bg-gradient-to-br from-white to-slate-100 border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <div class="grid md:grid-cols-2 gap-10 items-center">
        <div>
          <h1 class="text-3xl sm:text-4xl font-extrabold leading-tight">Audit smarter. Write better. Rank higher.</h1>
          <p class="mt-4 text-slate-700">A modern, stylish toolkit: <span class="font-semibold">Semantic Analyzer</span>, <span class="font-semibold">AI Content Checker</span> and <span class="font-semibold">Topic Cluster Identification</span> — purpose‑built for clean UX and fast results.</p>
          <div class="mt-6 flex gap-3">
            <a href="{{ route('semantic') }}" class="px-4 py-2 rounded-xl bg-brand-600 text-white hover:bg-brand-700">Open Semantic Analyzer</a>
            <a href="{{ route('aiChecker') }}" class="px-4 py-2 rounded-xl border border-slate-300 hover:bg-slate-100">AI Checker</a>
          </div>
        </div>
        <div class="md:pl-8">
          <div class="grid grid-cols-3 gap-4">
            <div class="p-5 rounded-2xl bg-white border shadow-sm">
              <p class="text-xs text-slate-500">Overall Score</p>
              <p class="text-3xl font-bold">0–100</p>
              <p class="text-xs text-slate-500 mt-2">Simple weighted model</p>
            </div>
            <div class="p-5 rounded-2xl bg-white border shadow-sm">
              <p class="text-xs text-slate-500">Readability</p>
              <p class="text-3xl font-bold">Flesch</p>
              <p class="text-xs text-slate-500 mt-2">Fast, approximate</p>
            </div>
            <div class="p-5 rounded-2xl bg-white border shadow-sm">
              <p class="text-xs text-slate-500">Structured Data</p>
              <p class="text-3xl font-bold">JSON‑LD</p>
              <p class="text-xs text-slate-500 mt-2">Detect presence</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- Quick Cards -->
  <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid md:grid-cols-3 gap-6">
      <a href="{{ route('semantic') }}" class="block p-6 rounded-2xl bg-white border hover:shadow-md transition">
        <h3 class="font-semibold text-lg">Semantic SEO Master Analyzer 2.0</h3>
        <p class="mt-2 text-sm text-slate-600">URL audit, headings, links, images, structured data and more — in a clean report with an overall score and prioritized fixes.</p>
      </a>
      <a href="{{ route('aiChecker') }}" class="block p-6 rounded-2xl bg-white border hover:shadow-md transition">
        <h3 class="font-semibold text-lg">AI Content Checker</h3>
        <p class="mt-2 text-sm text-slate-600">Heuristic, offline signals (TTR, sentence length, punctuation variety) to estimate AI vs. human‑like writing.</p>
      </a>
      <a href="{{ route('topicCluster') }}" class="block p-6 rounded-2xl bg-white border hover:shadow-md transition">
        <h3 class="font-semibold text-lg">Topic Cluster Identification</h3>
        <p class="mt-2 text-sm text-slate-600">Extract top terms and group them into lightweight clusters to guide outlines and internal linking.</p>
      </a>
    </div>
  </section>
@endsection

<?php
/**
 * ============================================
 * FILE: resources/views/analyzers/semantic.blade.php
 * ============================================
 */
?>
@extends('layouts.app')
@section('title','Semantic SEO Master Analyzer 2.0')
@section('content')
  <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-2xl font-bold">Semantic SEO Master Analyzer 2.0</h1>
    <p class="text-slate-600 mt-2">Enter a URL (and optional target keyword). The analyzer fetches the page with a Chrome‑like user‑agent, follows redirects, and returns a structured report.</p>

    <form id="semanticForm" class="mt-6 grid md:grid-cols-[1fr,280px] gap-3">
      <input name="url" type="url" required placeholder="https://example.com/article" class="w-full px-3 py-2 rounded-xl border" />
      <div class="grid grid-cols-[1fr,auto] gap-3">
        <input name="target_keyword" type="text" placeholder="Target keyword (optional)" class="w-full px-3 py-2 rounded-xl border" />
        <button class="px-4 py-2 rounded-xl bg-brand-600 text-white hover:bg-brand-700" type="submit">Analyze</button>
      </div>
    </form>

    <div id="semanticResult" class="mt-8 hidden">
      <div class="grid md:grid-cols-3 gap-6">
        <div class="p-6 rounded-2xl bg-white border">
          <p class="text-xs text-slate-500">Overall Score</p>
          <p id="overallScore" class="text-4xl font-extrabold">—</p>
          <div class="mt-2 h-2 rounded-full bg-slate-200">
            <div id="scoreBar" class="h-2 rounded-full" style="width:0%"></div>
          </div>
        </div>
        <div class="p-6 rounded-2xl bg-white border">
          <p class="text-xs text-slate-500">Readability (Flesch)</p>
          <p id="readability" class="text-4xl font-extrabold">—</p>
          <p class="text-xs text-slate-500 mt-2">Higher is easier (0–100)</p>
        </div>
        <div class="p-6 rounded-2xl bg-white border">
          <p class="text-xs text-slate-500">Links</p>
          <p class="text-4xl font-extrabold"><span id="internal">0</span>/<span id="external">0</span></p>
          <p class="text-xs text-slate-500 mt-2">internal / external</p>
        </div>
      </div>

      <div class="mt-8 grid lg:grid-cols-2 gap-6">
        <div class="p-6 rounded-2xl bg-white border">
          <h3 class="font-semibold">Content Structure</h3>
          <div class="mt-3 text-sm">
            <p><span class="font-medium">Title:</span> <span id="titleVal">—</span></p>
            <p class="mt-1"><span class="font-medium">Meta description:</span> <span id="metaVal">—</span></p>
            <p class="mt-1"><span class="font-medium">Text/HTML ratio:</span> <span id="ratioVal">—</span>%</p>
            <p class="mt-1"><span class="font-medium">Images missing alt:</span> <span id="imgAlt">—</span></p>
          </div>
          <div class="mt-4">
            <details class="rounded-xl border p-4 bg-slate-50">
              <summary class="cursor-pointer font-medium">Heading Map</summary>
              <div id="headingMap" class="mt-2 text-sm space-y-2"></div>
            </details>
          </div>
        </div>
        <div class="p-6 rounded-2xl bg-white border">
          <h3 class="font-semibold">Recommendations</h3>
          <ul id="recs" class="mt-3 space-y-2 text-sm"></ul>
          <div class="mt-6">
            <details class="rounded-xl border p-4 bg-slate-50">
              <summary class="cursor-pointer font-medium">Anchors</summary>
              <div id="anchors" class="mt-2 text-sm space-y-2"></div>
            </details>
          </div>
        </div>
      </div>

      <div class="mt-8 p-6 rounded-2xl bg-white border">
        <h3 class="font-semibold">Planned Capabilities</h3>
        <ul class="mt-2 list-disc pl-5 text-sm text-slate-700">
          <li>Boilerplate removal via Readability</li>
          <li>TF‑IDF topic cloud & entity mapping</li>
          <li>Competitor SERP sampling and content gaps</li>
          <li>Structured data validation status</li>
        </ul>
      </div>
    </div>
  </section>

  <script>
    const elForm = document.getElementById('semanticForm');
    const elWrap = document.getElementById('semanticResult');
    const els = {
      score: document.getElementById('overallScore'),
      bar: document.getElementById('scoreBar'),
      readability: document.getElementById('readability'),
      internal: document.getElementById('internal'),
      external: document.getElementById('external'),
      title: document.getElementById('titleVal'),
      meta: document.getElementById('metaVal'),
      ratio: document.getElementById('ratioVal'),
      imgAlt: document.getElementById('imgAlt'),
      map: document.getElementById('headingMap'),
      recs: document.getElementById('recs'),
      anchors: document.getElementById('anchors')
    };

    elForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const fd = new FormData(elForm);
      const payload = { url: fd.get('url'), target_keyword: fd.get('target_keyword') };
      const res = await fetch('/api/semantic-analyze', {
        method: 'POST', headers: { 'Accept':'application/json','Content-Type':'application/json' },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if (!data.ok) { alert(data.error || 'Analysis failed'); return; }
      elWrap.classList.remove('hidden');
      els.score.textContent = data.overall_score;
      els.bar.style.width = data.overall_score + '%';
      els.bar.className = 'h-2 rounded-full ' + (data.overall_score>=80? 'bg-green-500' : data.overall_score>=60? 'bg-amber-500':'bg-red-500');
      els.readability.textContent = data.content_structure.readability_flesch;
      els.internal.textContent = data.technical_seo.links.internal;
      els.external.textContent = data.technical_seo.links.external;
      els.title.textContent = data.content_structure.title || '—';
      els.meta.textContent = data.content_structure.meta_description || '—';
      els.ratio.textContent = data.content_structure.text_to_html_ratio;
      els.imgAlt.textContent = data.technical_seo.image_alt_missing + ' / ' + data.technical_seo.image_count;

      // Headings
      els.map.innerHTML = '';
      Object.entries(data.content_structure.headings).forEach(([level,arr])=>{
        if (!arr || !arr.length) return; 
        const div = document.createElement('div');
        div.innerHTML = `<div class="text-xs uppercase text-slate-500">${level}</div>` +
                        arr.map(t=>`<div class="pl-3">• ${t}</div>`).join('');
        els.map.appendChild(div);
      });

      // Recs
      els.recs.innerHTML = '';
      data.recommendations.forEach(r=>{
        const li = document.createElement('li');
        const badge = r.severity === 'Critical' ? 'bg-red-100 text-red-700' : r.severity==='Warning'? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700';
        li.innerHTML = `<span class="px-2 py-0.5 rounded ${badge} mr-2">${r.severity}</span>${r.text}`;
        els.recs.appendChild(li);
      });

      // Anchors
      els.anchors.innerHTML = '';
      data.technical_seo.links.anchors.slice(0,100).forEach(a=>{
        const p = document.createElement('p');
        p.textContent = `[${a.type}] ${a.text || '(no text)'} → ${a.href}`;
        els.anchors.appendChild(p);
      });
    });
  </script>
@endsection

<?php
/**
 * ============================================
 * FILE: resources/views/analyzers/ai.blade.php
 * ============================================
 */
?>
@extends('layouts.app')
@section('title','AI Content Checker')
@section('content')
  <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-2xl font-bold">AI Content Checker</h1>
    <p class="text-slate-600 mt-2">Paste text or provide a URL to estimate AI‑likeness vs human‑like writing using offline stylistic signals.</p>

    <form id="aiForm" class="mt-6 space-y-3">
      <input name="url" type="url" placeholder="https://example.com (optional)" class="w-full px-3 py-2 rounded-xl border" />
      <textarea name="text" rows="8" placeholder="Paste your content here (optional if URL provided)" class="w-full px-3 py-2 rounded-xl border"></textarea>
      <button class="px-4 py-2 rounded-xl bg-brand-600 text-white hover:bg-brand-700" type="submit">Check</button>
    </form>

    <div id="aiResult" class="mt-8 hidden p-6 rounded-2xl bg-white border">
      <div class="grid md:grid-cols-2 gap-6">
        <div>
          <p class="text-xs text-slate-500">AI Probability</p>
          <p id="aiPct" class="text-4xl font-extrabold">—</p>
        </div>
        <div>
          <p class="text-xs text-slate-500">Human Probability</p>
          <p id="humanPct" class="text-4xl font-extrabold">—</p>
        </div>
      </div>
      <div class="mt-6">
        <h3 class="font-semibold">Signals</h3>
        <div id="aiSignals" class="mt-2 text-sm grid sm:grid-cols-2 gap-2"></div>
      </div>
    </div>
  </section>
  <script>
    const aiForm = document.getElementById('aiForm');
    const aiWrap = document.getElementById('aiResult');
    const aiPct = document.getElementById('aiPct');
    const humanPct = document.getElementById('humanPct');
    const aiSignals = document.getElementById('aiSignals');

    aiForm.addEventListener('submit', async (e)=>{
      e.preventDefault();
      const fd = new FormData(aiForm);
      const payload = { url: fd.get('url'), text: fd.get('text') };
      const res = await fetch('/api/ai-check', { method:'POST', headers:{'Accept':'application/json','Content-Type':'application/json'}, body: JSON.stringify(payload)});
      const data = await res.json();
      if (!data.ok) { alert(data.error || 'Check failed'); return; }
      aiWrap.classList.remove('hidden');
      aiPct.textContent = data.ai_probability_percent + '%';
      humanPct.textContent = data.human_probability_percent + '%';
      aiSignals.innerHTML = '';
      const map = {
        ttr:'Type‑Token Ratio',
        avgSentLen:'Avg sentence length',
        punctVariety:'Punctuation variety',
        stopwordRate:'Stop‑word rate',
        wc:'Word count',
      };
      Object.entries(data.metrics).forEach(([k,v])=>{
        const div = document.createElement('div');
        div.className = 'p-3 rounded-lg bg-slate-50 border';
        div.innerHTML = `<div class="text-xs text-slate-500">${map[k]||k}</div><div class="font-medium">${typeof v === 'number' ? v.toFixed ? v.toFixed(3) : v : v}</div>`;
        aiSignals.appendChild(div);
      })
    });
  </script>
@endsection

<?php
/**
 * ============================================
 * FILE: resources/views/analyzers/topic.blade.php
 * ============================================
 */
?>
@extends('layouts.app')
@section('title','Topic Cluster Identification')
@section('content')
  <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-2xl font-bold">Topic Cluster Identification</h1>
    <p class="text-slate-600 mt-2">Extract top terms and group them into lightweight clusters. Use as a quick guide for outlines and internal links.</p>

    <form id="topicForm" class="mt-6 space-y-3">
      <input name="url" type="url" placeholder="https://example.com (optional)" class="w-full px-3 py-2 rounded-xl border" />
      <textarea name="text" rows="8" placeholder="Paste content here (optional if URL provided)" class="w-full px-3 py-2 rounded-xl border"></textarea>
      <div class="flex items-center gap-3">
        <input name="top_k" type="number" min="5" max="50" value="20" class="w-24 px-3 py-2 rounded-xl border" />
        <button class="px-4 py-2 rounded-xl bg-brand-600 text-white hover:bg-brand-700" type="submit">Identify</button>
      </div>
    </form>

    <div id="topicResult" class="mt-8 hidden">
      <div class="grid lg:grid-cols-2 gap-6">
        <div class="p-6 rounded-2xl bg-white border">
          <h3 class="font-semibold">Top Terms</h3>
          <div id="topTerms" class="mt-3 text-sm grid grid-cols-2 gap-2"></div>
        </div>
        <div class="p-6 rounded-2xl bg-white border">
          <h3 class="font-semibold">Clusters</h3>
          <div id="clusters" class="mt-3 text-sm space-y-3"></div>
        </div>
      </div>
    </div>
  </section>
  <script>
    const topicForm = document.getElementById('topicForm');
    const topicWrap = document.getElementById('topicResult');
    const topTerms = document.getElementById('topTerms');
    const clusters = document.getElementById('clusters');

    topicForm.addEventListener('submit', async (e)=>{
      e.preventDefault();
      const fd = new FormData(topicForm);
      const payload = { url: fd.get('url'), text: fd.get('text'), top_k: Number(fd.get('top_k')||20) };
      const res = await fetch('/api/topic-cluster', { method:'POST', headers:{'Accept':'application/json','Content-Type':'application/json'}, body: JSON.stringify(payload)});
      const data = await res.json();
      if (!data.ok) { alert(data.error || 'Identification failed'); return; }
      topicWrap.classList.remove('hidden');
      // Top terms
      topTerms.innerHTML = '';
      Object.entries(data.top_terms).forEach(([term,count])=>{
        const div = document.createElement('div');
        div.className = 'p-2 rounded-lg bg-slate-50 border flex items-center justify-between';
        div.innerHTML = `<span>${term}</span><span class="text-slate-500">${count}</span>`;
        topTerms.appendChild(div);
      });
      // Clusters
      clusters.innerHTML = '';
      data.clusters.forEach(c=>{
        const card = document.createElement('div');
        card.className = 'p-3 rounded-lg bg-slate-50 border';
        card.innerHTML = `<div class="text-xs text-slate-500">Cluster</div><div class="font-medium">${c.label}</div>`+
                         `<div class="mt-2 grid grid-cols-2 gap-2">`+
                         c.stems.map(s=>`<div class='px-2 py-1 rounded bg-white border'>${s.term} <span class='text-slate-500'>(${s.count})</span></div>`).join('')+
                         `</div>`;
        clusters.appendChild(card);
      })
    });
  </script>
@endsection
