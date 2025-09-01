<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AnalyzerController extends Controller
{
    public function semanticAnalyze(Request $request)
    {
        if ($resp = $this->ensureRuntime()) { return $resp; }

        $request->validate([
            'url' => 'required|url',
            'target_keyword' => 'nullable|string|max:120',
            'ua' => 'nullable|string|max:255',
        ]);

        try {
            $url = (string)$request->input('url');
            $target = trim((string)$request->input('target_keyword', ''));
            $ua = (string)$request->input('ua', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124 Safari/537.36');

            $fetch = $this->fetchUrl($url, $ua);
            if (!$fetch['ok']) {
                return response()->json(['ok'=>false,'error'=>$fetch['error'] ?? 'Fetch failed'], 422);
            }

            $html = $fetch['body'];
            libxml_use_internal_errors(true);
            $dom = new \DOMDocument();
            $dom->loadHTML($html);
            $xp = new \DOMXPath($dom);

            $titleNode = $xp->query('//title')->item(0);
            $title = $titleNode ? trim($titleNode->textContent) : '';
            $metaNode = $xp->query("//meta[translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='description']/@content")->item(0);
            $meta = $metaNode ? trim($metaNode->nodeValue) : '';

            $headings = [];
            for ($i=1; $i<=6; $i++) {
                $arr = [];
                foreach ($xp->query("//h{$i}") as $n) { $arr[] = trim($n->textContent); }
                $headings["h{$i}"] = $arr;
            }

            $imgNodes = $xp->query('//img');
            $imgTotal = $imgNodes->length;
            $imgMissingAlt = 0;
            foreach ($imgNodes as $img) { if (trim((string)$img->getAttribute('alt')) === '') { $imgMissingAlt++; } }

            $baseHost = $this->getHost($url);
            $internal=0; $external=0; $anchors=[];
            foreach ($xp->query('//a[@href]') as $a) {
                $href = trim((string)$a->getAttribute('href'));
                if ($href === '' || Str::startsWith($href, ['#','mailto:'])) { continue; }
                $abs = $this->absUrl($href, $url);
                $host = $this->getHost($abs);
                $type = ($host === $baseHost) ? 'internal' : 'external';
                if ($type==='internal') { $internal++; } else { $external++; }
                $anchors[] = ['text'=>trim($a->textContent),'href'=>$href,'type'=>$type];
            }

            $hasJsonLd = $xp->query("//script[@type='application/ld+json']")->length > 0;
            $hasMicro = $xp->query('//*[@itemscope or @itemtype]')->length > 0;
            $hasRdfa  = $xp->query('//*[@typeof or @property]')->length > 0;

            foreach (['//script','//style','//noscript'] as $rm) {
                foreach ($xp->query($rm) as $node) { $node->parentNode?->removeChild($node); }
            }
            $text = trim(preg_replace('/\s+/u', ' ', $dom->textContent ?? ''));
            $textLen = mb_strlen($text);
            $htmlLen = mb_strlen($html);
            $ratio = $htmlLen > 0 ? round(($textLen/$htmlLen)*100, 2) : 0.0;

            $sentences = preg_split('/(?<=[\.\!\?])\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
            $words = preg_split('/\s+/u', preg_replace('/[^\p{L}\p{N}\s\-\'’]+/u',' ', $text), -1, PREG_SPLIT_NO_EMPTY);
            $wc = max(count($words),1);
            $sc = max(count($sentences),1);
            $syll=0; foreach ($words as $w) { $syll += $this->syllableGuess($w); }
            $flesch = (int) round(max(0, min(100, 206.835 - (1.015*($wc/$sc)) - (84.6*($syll/$wc)) )));

            $missingH1 = count($headings['h1']) === 0;
            $skipped = $this->checkHeadingSkips($headings);

            $kw = null;
            if ($target !== '') {
                $kw = [
                    'target'=>$target,
                    'occurrences'=>substr_count(mb_strtolower($text), mb_strtolower($target)),
                    'in_title'=>Str::contains(Str::lower($title), Str::lower($target)),
                    'in_meta'=>Str::contains(Str::lower($meta), Str::lower($target)),
                ];
            }

            $score = 0;
            if ($title !== '') $score += 12;
            $tlen = mb_strlen($title);
            if ($tlen>=30 && $tlen<=60) $score += 8;
            if ($meta !== '') $score += 10;
            $altRatio = ($imgTotal>0) ? (1-($imgMissingAlt/$imgTotal)) : 1.0;
            $score += (int) round(20*$altRatio);
            if ($internal>0) $score += 5;
            if ($external>0) $score += 5;
            if ($hasJsonLd || $hasMicro || $hasRdfa) $score += 10;
            $score += (int) round($flesch/10);
            if ($ratio>=15) { $score += 10; } elseif ($ratio>=8) { $score += 5; }
            if (!$missingH1) $score += 5;
            if (!$skipped) $score += 5;
            $score = max(0, min(100, $score));

            $recs=[];
            if ($missingH1) $recs[] = ['severity'=>'Critical','text'=>'Add a single descriptive H1.'];
            if ($tlen<30 || $tlen>60) $recs[] = ['severity'=>'Warning','text'=>'Adjust title length to ~30–60 chars.'];
            if ($meta==='') $recs[] = ['severity'=>'Warning','text'=>'Add a compelling meta description (120–160 chars).'];
            if ($imgMissingAlt>0) $recs[] = ['severity'=>'Warning','text'=>"Add alt text to {$imgMissingAlt} image(s)."];
            if ($internal===0) $recs[] = ['severity'=>'Warning','text'=>'Add relevant internal links.'];
            if ($external===0) $recs[] = ['severity'=>'Info','text'=>'Consider citing authoritative external sources.'];
            if (!$hasJsonLd && !$hasMicro && !$hasRdfa) $recs[] = ['severity'=>'Info','text'=>'Add JSON-LD structured data.'];
            if ($ratio<15) $recs[] = ['severity'=>'Info','text'=>'Increase main content depth.'];
            if ($skipped) $recs[] = ['severity'=>'Info','text'=>'Fix heading hierarchy (avoid level jumps).'];
            if ($kw && !$kw['in_title']) $recs[] = ['severity'=>'Info','text'=>'Consider using the target keyword in the title naturally.'];

            return response()->json([
                'ok'=>true,
                'overall_score'=>$score,
                'semantic_core'=>['primary_topics'=>[],'topic_cloud'=>[],'sentiment'=>'Neutral'],
                'content_structure'=>[
                    'title'=>$title,
                    'meta_description'=>$meta,
                    'headings'=>$headings,
                    'missing_h1'=>$missingH1,
                    'skipped_levels'=>$skipped,
                    'readability_flesch'=>$flesch,
                    'text_to_html_ratio'=>$ratio,
                ],
                'intent_coverage'=>[
                    'search_intent'=>'Informational',
                    'semantic_coverage_score'=>null,
                    'gaps'=>[],
                ],
                'technical_seo'=>[
                    'image_alt_missing'=>$imgMissingAlt,
                    'image_count'=>$imgTotal,
                    'links'=>['internal'=>$internal,'external'=>$external,'anchors'=>$anchors],
                    'structured_data'=>['json_ld'=>$hasJsonLd,'microdata'=>$hasMicro,'rdfa'=>$hasRdfa],
                    'target_keyword'=>$kw,
                ],
                'recommendations'=>$recs,
            ]);
        } catch (\Throwable $e) {
            \Log::error('semanticAnalyze failed', ['ex'=>$e->getMessage()]);
            return response()->json(['ok'=>false,'error'=>'Server error: '.$e->getMessage()], 500);
        }
    }

    public function aiCheck(Request $request)
    {
        if ($resp = $this->ensureRuntime()) { return $resp; }

        $request->validate([
            'text'=>'nullable|string',
            'url'=>'nullable|url',
        ]);

        try {
            $text = trim((string)$request->input('text',''));
            if ($text==='' && $request->filled('url')) {
                $fetch = $this->fetchUrl((string)$request->input('url'));
                if ($fetch['ok']) { $text = $this->extractVisibleText($fetch['body']); }
            }
            if ($text==='') { return response()->json(['ok'=>false,'error'=>'No text found to analyze.'], 422); }

            $clean = Str::of($text)->squish()->toString();
            $sentences = preg_split('/(?<=[\.\!\?])\s+/u', $clean, -1, PREG_SPLIT_NO_EMPTY);
            $words = preg_split('/\s+/u', preg_replace('/[^\p{L}\p{N}\s\-\'’]+/u',' ', $clean), -1, PREG_SPLIT_NO_EMPTY);
            $wc = max(count($words),1);

            $uniq=[]; foreach ($words as $w) { $uniq[mb_strtolower($w)] = true; }
            $ttr = count($uniq)/$wc;
            $avgSent = $wc / max(count($sentences),1);
            $puncVar = preg_match_all('/[,:;\-\—\(\)\"\'’]/u', $clean);
            $stopRate = $this->stopwordRate($words);

            $ai=0.0;
            if ($ttr<0.35) $ai+=0.25;
            if ($avgSent<12 || $avgSent>32) $ai+=0.20;
            $minP = max(5, $wc*0.01);
            if ($puncVar < $minP) $ai+=0.20;
            if ($stopRate<0.38 || $stopRate>0.62) $ai+=0.15;

            $aiPct = (int) round(min(1.0, max(0.0, $ai))*100);

            return response()->json([
                'ok'=>true,
                'ai_probability_percent'=>$aiPct,
                'human_probability_percent'=>100-$aiPct,
                'metrics'=>[
                    'ttr'=>round($ttr,4),
                    'avgSentLen'=>round($avgSent,3),
                    'punctVariety'=>(int)$puncVar,
                    'stopwordRate'=>round($stopRate,4),
                    'wc'=>$wc,
                ],
            ]);
        } catch (\Throwable $e) {
            \Log::error('aiCheck failed', ['ex'=>$e->getMessage()]);
            return response()->json(['ok'=>false,'error'=>'Server error: '.$e->getMessage()], 500);
        }
    }

    public function topicClusterAnalyze(Request $request)
    {
        if ($resp = $this->ensureRuntime()) { return $resp; }

        $request->validate([
            'text'=>'nullable|string',
            'url'=>'nullable|url',
            'top_k'=>'nullable|integer|min:5|max:50',
        ]);

        try {
            $text = trim((string)$request->input('text',''));
            if ($text==='' && $request->filled('url')) {
                $fetch = $this->fetchUrl((string)$request->input('url'));
                if ($fetch['ok']) { $text = $this->extractVisibleText($fetch['body']); }
            }
            if ($text==='') { return response()->json(['ok'=>false,'error'=>'No text found to analyze.'], 422); }

            $topK = (int)$request->input('top_k',20);
            $tokens = $this->tokenize($text);
            $freq=[];
            foreach ($tokens as $t) {
                if ($this->isStop($t)) continue;
                $stem = $this->stem($t);
                $freq[$stem] = ($freq[$stem] ?? 0) + 1;
            }
            arsort($freq);
            $top = array_slice($freq, 0, $topK, true);

            $clusters = [];
            foreach ($top as $stem=>$count) {
                $key = mb_substr($stem,0,4);
                if (!isset($clusters[$key])) { $clusters[$key] = ['label'=>$key,'stems'=>[],'weight'=>0]; }
                $clusters[$key]['stems'][] = ['term'=>$stem,'count'=>$count];
                $clusters[$key]['weight'] += $count;
            }
            usort($clusters, fn($a,$b)=> $b['weight'] <=> $a['weight']);

            return response()->json(['ok'=>true,'top_terms'=>$top,'clusters'=>array_values($clusters)]);
        } catch (\Throwable $e) {
            \Log::error('topicClusterAnalyze failed', ['ex'=>$e->getMessage()]);
            return response()->json(['ok'=>false,'error'=>'Server error: '.$e->getMessage()], 500);
        }
    }

    private function ensureRuntime(): ?\Illuminate\Http\JsonResponse
    {
        if (!function_exists('curl_init')) { return response()->json(['ok'=>false,'error'=>'PHP cURL extension is disabled.'], 500); }
        if (!class_exists(\DOMDocument::class)) { return response()->json(['ok'=>false,'error'=>'PHP DOM/XML extension is disabled.'], 500); }
        if (!function_exists('mb_strlen')) { return response()->json(['ok'=>false,'error'=>'PHP mbstring extension is disabled.'], 500); }
        return null;
    }

    private function fetchUrl(string $url, string $ua='Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124 Safari/537.36', int $timeout=15): array
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL=>$url,
            CURLOPT_RETURNTRANSFER=>true,
            CURLOPT_FOLLOWLOCATION=>true,
            CURLOPT_MAXREDIRS=>5,
            CURLOPT_CONNECTTIMEOUT=>$timeout,
            CURLOPT_TIMEOUT=>$timeout,
            CURLOPT_USERAGENT=>$ua,
            CURLOPT_SSL_VERIFYPEER=>false,
            CURLOPT_SSL_VERIFYHOST=>false,
            CURLOPT_ENCODING=>'',
            CURLOPT_HTTPHEADER=>[
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language: en-US,en;q=0.9',
                'Cache-Control: no-cache',
            ],
        ]);
        $body = curl_exec($ch);
        $errNo = curl_errno($ch);
        $errStr = curl_error($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($body === false) return ['ok'=>false,'error'=>"cURL error #$errNo: $errStr"];
        if ($status >= 400) return ['ok'=>false,'error'=>"HTTP status $status"];
        return ['ok'=>true,'body'=>$body,'status'=>$status];
    }

    private function getHost(string $url): ?string
    {
        $p = parse_url($url);
        return $p['host'] ?? null;
    }

    private function absUrl(string $href, string $base): string
    {
        if (Str::startsWith($href, ['http://','https://'])) return $href;
        $p = parse_url($base);
        $scheme = $p['scheme'] ?? 'https';
        $host = $p['host'] ?? '';
        if (Str::startsWith($href, ['/'])) return $scheme.'://'.$host.$href;
        $path = isset($p['path']) ? preg_replace('#/[^/]*$#','/', $p['path']) : '/';
        return $scheme.'://'.$host.$path.$href;
    }

    private function extractVisibleText(string $html): string
    {
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML($html);
        $xp = new \DOMXPath($dom);
        foreach (['//script','//style','//noscript'] as $rm) {
            foreach ($xp->query($rm) as $node) { $node->parentNode?->removeChild($node); }
        }
        return trim(preg_replace('/\s+/u',' ', $dom->textContent ?? ''));
    }

    private function syllableGuess(string $word): int
    {
        $w = mb_strtolower($word);
        $w = preg_replace('/[^a-z]/','', $w);
        if ($w === '') return 1;
        $v = preg_match_all('/[aeiouy]+/', $w);
        $v = $v ?: 1;
        if (Str::endsWith($w,'e') && $v>1) $v--;
        return max(1,$v);
    }

    private function checkHeadingSkips(array $headings): bool
    {
        $last=0; $skipped=false;
        for ($i=1;$i<=6;$i++) {
            $c = count($headings["h{$i}"] ?? []);
            if ($c>0) {
                if ($last>0 && ($i-$last)>1) { $skipped = true; break; }
                $last = $i;
            }
        }
        return $skipped;
    }

    private function stopwordRate(array $words): float
    {
        $stops = $this->stopwords();
        $c=0; $w=0;
        foreach ($words as $wd) { $w++; if (isset($stops[mb_strtolower($wd)])) $c++; }
        return $w>0 ? $c/$w : 0.5;
    }

    private function tokenize(string $text): array
    {
        $t = mb_strtolower($text);
        $t = preg_replace('/[^\p{L}\p{N}\s\-\'’]+/u',' ', $t);
        $parts = preg_split('/\s+/u', $t, -1, PREG_SPLIT_NO_EMPTY);
        return array_values(array_filter($parts, fn($x)=> mb_strlen($x) >= 3));
    }

    private function isStop(string $w): bool
    {
        return isset($this->stopwords()[mb_strtolower($w)]);
    }

    private function stem(string $w): string
    {
        $w = mb_strtolower($w);
        $w = preg_replace('/(ingly|edly|ing|edly|ed|ly|s)$/u','', $w);
        return $w;
    }

    private function stopwords(): array
    {
        static $s=null; if ($s!==null) return $s;
        $list = ['a','an','and','the','of','in','on','for','to','from','with','by','is','are','was','were','be','been','it','that','this','as','at','or','if','but','not',
        'your','you','we','our','their','they','i','he','she','them','these','those','can','will','would','should','could','about','into','over','than','then',
        'there','here','out','up','down','across','between','after','before','during','also','when','where','how','what','which','why','who','whom'];
        $s = array_fill_keys($list, true);
        return $s;
    }
}
