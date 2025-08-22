<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use DOMDocument, DOMXPath;

class AnalyzerController extends Controller
{
    public function analyze(Request $req)
    {
        $url = trim($req->input('url', ''));
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return response()->json(['ok' => false, 'error' => 'Invalid URL'], 422);
        }

        try {
            $resp = Http::withHeaders([
                'User-Agent' => 'SemanticSEO-Master/3.0 (+https://example.com)'
            ])->timeout(15)->connectTimeout(6)->get($url);

            $status = $resp->status();
            if ($status >= 400) return response()->json(['ok' => false, 'error' => "Request failed ($status)"], 502);

            $html = $resp->body() ?? '';
            $report = $this->audit($url, $html, $status);
            return response()->json($report);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

    private function audit(string $url, string $html, int $status): array
    {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xp = new DOMXPath($dom);

        $q = fn($x) => iterator_to_array($xp->query($x) ?? []);
        $attr = fn($n,$a) => $n?->attributes?->getNamedItem($a)?->nodeValue ?? '';

        $title = trim(($q('//title')[0] ?? null)?->textContent ?? '');
        $metaDescNode = null;
        foreach ($q('//meta') as $m) {
            $n = strtolower($attr($m, 'name'));
            if ($n === 'description') { $metaDescNode = $m; break; }
        }
        $metaDesc = trim($attr($metaDescNode, 'content'));
        $canonical = trim(($attr(($q('//link[@rel="canonical"]')[0] ?? null), 'href')) ?? '');
        $robots = '';
        foreach ($q('//meta') as $m) {
            $n = strtolower($attr($m,'name'));
            if ($n==='robots') { $robots = strtolower($attr($m,'content')); break; }
        }
        $viewport = !!($q('//meta[@name="viewport"]')[0] ?? null);

        $h1 = count($q('//h1'));
        $h2 = count($q('//h2'));
        $h3 = count($q('//h3'));
        $internalLinks = 0;
        $host = parse_url($url, PHP_URL_HOST);
        foreach ($q('//a[@href]') as $a) {
            $href = $attr($a,'href');
            if (!$href) continue;
            $isInternal = (str_starts_with($href,'/') || (parse_url($href, PHP_URL_HOST) ?? '') === $host);
            if ($isInternal) $internalLinks++;
        }

        // JSON-LD schema types
        $foundTypes = [];
        foreach ($q('//script[@type="application/ld+json"]') as $s) {
            $json = trim($s->textContent ?? '');
            $data = json_decode($json, true);
            if (is_array($data)) {
                $types = $this->extractTypesFromJsonLd($data);
                $foundTypes = array_values(array_unique(array_merge($foundTypes, $types)));
            }
        }

        // Full text for AI detection & entities
        $bodyText = trim(preg_replace('/\s+/u',' ', strip_tags($html)));
        $ai = $this->aiHeuristic($bodyText);
        $scores = $this->score25($title, $metaDesc, $canonical, $robots, $viewport, $h1, $h2, $h3, $internalLinks, $foundTypes, $bodyText, $url);

        // auto-check (ids with score >= 70)
        $auto = [];
        foreach ($scores as $id=>$sc) if ($sc >= 70) $auto[] = $id;

        // overall score (mean of 25, clamp)
        $overall = round(max(0, min(100, array_sum($scores)/max(1,count($scores)))));

        return [
            'ok' => true,
            'status' => $status,
            'title' => $title,
            'meta_description_len' => mb_strlen($metaDesc),
            'canonical' => (bool)$canonical,
            'robots' => $robots ?: null,
            'viewport' => $viewport,
            'counts' => ['h1'=>$h1,'h2'=>$h2,'h3'=>$h3,'internal_links'=>$internalLinks],
            'schema' => ['found_types'=>$foundTypes],
            'scores' => $scores,
            'suggestions' => $this->tips($scores, $title, $metaDesc, $foundTypes),
            'auto_check_ids' => $auto,
            'overall_score' => $overall,
            'ai_detection' => $ai
        ];
    }

    private function extractTypesFromJsonLd($data): array
    {
        $types = [];
        $walk = function($node) use (&$types, &$walk) {
            if (is_array($node)) {
                if (isset($node['@type'])) {
                    $t = $node['@type'];
                    if (is_array($t)) $types = array_merge($types, $t);
                    else $types[] = $t;
                }
                foreach ($node as $v) $walk($v);
            }
        };
        $walk($data);
        return array_map('strval', $types);
    }

    private function aiHeuristic(string $text): array
    {
        // Very light, transparent heuristic: sentence burstiness + unique ratio
        $sentences = preg_split('/(?<=[.!?])\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $lengths = array_map(fn($s)=>max(1, mb_strlen($s)), $sentences);
        $avg = count($lengths) ? array_sum($lengths)/count($lengths) : 0;
        $var = 0;
        foreach ($lengths as $L) $var += pow($L - $avg, 2);
        $std = count($lengths) ? sqrt($var / count($lengths)) : 0;
        $burstiness = $avg ? ($std / $avg) : 0; // lower tends to AI‑like

        $words = preg_split('/\W+/u', mb_strtolower($text), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $unique = count(array_unique($words));
        $uniqueRatio = count($words) ? $unique / count($words) : 0;

        // crude thresholds
        $aiScore = ( (0.35 - min(0.35, $burstiness)) / 0.35 ) * 0.6 + ( (0.55 - min(0.55, $uniqueRatio)) / 0.55 ) * 0.4;
        $aiPct = round(max(0, min(100, $aiScore * 100)));
        $humanPct = 100 - $aiPct;
        $label = $aiPct >= 65 ? 'likely_ai' : ( $aiPct >= 40 ? 'mixed' : 'likely_human' );

        // Return some sample sentences for UI
        $aiLike = [];
        $humanLike = [];
        foreach ($sentences as $s) {
            $L = mb_strlen($s);
            if ($L >= $avg*0.9 && $L <= $avg*1.1) $aiLike[] = trim($s);
            if ($L < $avg*0.6 || $L > $avg*1.4) $humanLike[] = trim($s);
            if (count($aiLike)>20 && count($humanLike)>20) break;
        }

        return [
            'label' => $label,
            'likelihood' => (int) max(50, min(95, $aiPct)),
            'ai_pct' => $aiPct,
            'human_pct' => $humanPct,
            'ai_sentences' => $aiLike,
            'human_sentences' => $humanLike,
            'full_text' => mb_substr($text, 0, 20000),
            'reasons' => [
                "Burstiness=".round($burstiness,3),
                "UniqueRatio=".round($uniqueRatio,3)
            ]
        ];
    }

    private function score25($title,$meta,$canonical,$robots,$viewport,$h1,$h2,$h3,$internal,$types,$text,$url): array
    {
        $scores = [];
        $within = fn($n,$min,$max)=> ($n>=$min && $n<=$max);
        $titleLen = mb_strlen($title);
        $metaLen = mb_strlen($meta);

        $scores['ck-1']  = min(100, 40 + ($h1>0?30:0) + ($titleLen>20?30:0));
        $scores['ck-2']  = 60; // placeholder (needs keyword map UI)
        $scores['ck-3']  = $h1>0 ? 90 : 40;
        $scores['ck-4']  = preg_match('/\bfaq\b/i', $text) ? 85 : 55;
        $scores['ck-5']  = 70; // readability handled elsewhere
        $scores['ck-6']  = $within($titleLen, 50, 65) ? 95 : ($titleLen?70:30);
        $scores['ck-7']  = $within($metaLen, 140, 170) ? 95 : ($metaLen?70:40);
        $scores['ck-8']  = $canonical ? 100 : 50;
        $scores['ck-9']  = stripos($robots,'noindex')!==false ? 20 : 85;
        $scores['ck-10'] = preg_match('/(author|written|reviewed)/i',$text) ? 85 : 55;
        $scores['ck-11'] = preg_match('/(unique|compare|vs|case study|original)/i',$text) ? 85 : 60;
        $scores['ck-12'] = preg_match('/\d{4}/',$text) ? 80 : 55;
        $scores['ck-13'] = preg_match('/<img\b/i',$text) ? 80 : 55;
        $scores['ck-14'] = $h2>=2 ? 90 : ($h2>0?75:50);
        $scores['ck-15'] = $internal>=3 ? 100 : ($internal>0?70:40);
        $scores['ck-16'] = strlen(parse_url($url, PHP_URL_PATH) ?? '')<=60 ? 85 : 60;
        $scores['ck-17'] = in_array('BreadcrumbList',$types) ? 100 : 50;
        $scores['ck-18'] = $viewport ? 95 : 50;
        $scores['ck-19'] = 70; // speed placeholder; PSI endpoint covers details
        $scores['ck-20'] = 70; // CWV placeholder; PSI endpoint covers details
        $scores['ck-21'] = preg_match('/(sign up|buy|learn more|contact)/i',$text) ? 85 : 55;
        $scores['ck-22'] = preg_match('/(is|are|refers|means)/i',$text) ? 75 : 55;
        $scores['ck-23'] = preg_match('/(related|also|see|compare)/i',$text) ? 75 : 55;
        $scores['ck-24'] = count($types)? 90 : 55;
        $scores['ck-25'] = preg_match('/(linkedin|twitter|facebook|instagram|youtube)/i',$text) ? 75 : 55;

        return $scores;
    }

    private function tips(array $scores, string $title, string $meta, array $types): array
    {
        $out = [];
        foreach ($scores as $id=>$sc) {
            $out[$id] = [];
            if ($sc>=80) { $out[$id][] = 'Looks good.'; continue; }

            switch ($id) {
                case 'ck-6':
                    $len = mb_strlen($title);
                    $out[$id][] = "Keep title around 50–60 chars; current length: $len.";
                    $out[$id][] = "Put the primary topic near the start of the title.";
                    break;
                case 'ck-7':
                    $len = mb_strlen($meta);
                    $out[$id][] = "Meta description ~140–160 chars; current length: $len.";
                    $out[$id][] = "Add a clear benefit + call‑to‑action.";
                    break;
                case 'ck-8':
                    $out[$id][] = "Add a canonical URL tag to prevent duplicates.";
                    break;
                case 'ck-14':
                    $out[$id][] = "Use H2/H3 to structure topics and subtopics.";
                    $out[$id][] = "Group related paragraphs under each heading.";
                    break;
                case 'ck-24':
                    if (!in_array('Article',$types)) $out[$id][] = "Add Article/FAQ/HowTo JSON‑LD where relevant.";
                    $out[$id][] = "Validate with Rich Results Test.";
                    break;
                default:
                    $out[$id][] = "Improve relevance and clarity for this item.";
            }
        }
        return $out;
    }
}
