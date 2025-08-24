<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AnalyzeController extends Controller
{
    public function analyzeJson(Request $request)
    {
        // Alias used by the front-end (GET)
        return $this->analyze($request);
    }

    public function analyze(Request $request)
    {
        $raw = $request->query('url') ?? $request->input('url');
        $url = $this->normalizeUrl($raw);
        if (!$url) {
            return response()->json(['error' => 'Invalid or missing URL.'], 422);
        }

        try {
            $res = Http::withHeaders([
                    'User-Agent' => 'SemanticSEO-Analyzer/1.0',
                    'Accept'     => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                ])
                ->timeout(12)
                ->withOptions(['allow_redirects' => true])
                ->get($url);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Request failed: '.$e->getMessage(),
                'overall' => 0, 'contentScore' => 0, 'itemScores' => (object)[],
            ], 500);
        }

        $status = $res->status();
        $html   = $res->body() ?? '';
        $headersRobots = $res->header('X-Robots-Tag');

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();
        $xp = new \DOMXPath($dom);

        $getText = fn($q) => ($n = $xp->query($q)) && $n->length ? trim($n->item(0)->textContent ?? '') : '';
        $getAttr = function ($q, $attr) use ($xp) {
            $n = $xp->query($q); if ($n && $n->length) {
                $node = $n->item(0);
                if ($node?->attributes?->getNamedItem($attr)) {
                    return trim($node->attributes->getNamedItem($attr)->nodeValue ?? '');
                }
            } return '';
        };
        $count = fn($q) => ($n = $xp->query($q)) ? $n->length : 0;

        $title     = $getText('//title');
        $metaDesc  = $getAttr("//meta[translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='description']", 'content');
        $canonical = $getAttr("//link[translate(@rel,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='canonical']", 'href');
        $robots    = $getAttr("//meta[translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='robots']", 'content');
        $viewport  = $getAttr("//meta[translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='viewport']", 'content');
        $h1c = $count('//h1'); $h2c = $count('//h2'); $h3c = $count('//h3');
        $ldJsonCount = $count("//script[@type='application/ld+json']");
        $imgs = $count('//img'); $vids = $count('//video');

        // internal/external links (rough)
        $host = parse_url($url, PHP_URL_HOST) ?: '';
        $allLinks = $xp->query('//a[@href]'); $internal = 0; $external = 0;
        if ($allLinks) foreach ($allLinks as $a) {
            $href = $a->getAttribute('href'); if (!$href) continue;
            if ($href[0] === '#' || str_starts_with($href, 'mailto:') || str_starts_with($href, 'tel:')) continue;
            if (!preg_match('~^https?://~i', $href)) { $internal++; continue; }
            $h = parse_url($href, PHP_URL_HOST) ?: '';
            $same = preg_replace('/^www\./i', '', $h) === preg_replace('/^www\./i', '', $host);
            $same ? $internal++ : $external++;
        }

        $textOnly = trim(preg_replace('/\s+/', ' ', strip_tags($html)));
        $wordCount = max(1, str_word_count($textOnly));
        $sentences = max(1, preg_match_all('/[\.!\?]+(\s|$)/u', $textOnly, $m));
        $wps = $wordCount / $sentences;

        // item scores 1..25 (heuristics)
        $item = [];
        $item[1]=65;
        $item[2]=min(90, 50 + min(40, $h2c*4 + $h3c*2));
        $item[3]=$h1c>=1?85:40;
        $hasFAQ = ($ldJsonCount && stripos($html,'FAQPage')!==false) || preg_match('~\bFAQ\b~i',$textOnly);
        $item[4]=$hasFAQ?85:55;
        $item[5]=($wps>=12 && $wps<=24)?85:(($wps<=30)?65:45);

        $titleLen=mb_strlen($title);
        $item[6]=($titleLen>=50 && $titleLen<=60)?90:(($titleLen>=35 && $titleLen<=70)?70:45);
        $metaLen=mb_strlen($metaDesc);
        $item[7]=($metaLen>=140 && $metaLen<=160)?90:(($metaLen>=90 && $metaLen<=180)?70:45);
        $item[8]=$canonical?95:45;
        $noindex=(stripos($robots,'noindex')!==false)||(stripos((string)$headersRobots,'noindex')!==false);
        $item[9]=$noindex?10:80;

        $hasAuthor = stripos($html,'author')!==false || preg_match('~rel=["\']author["\']~i',$html);
        $hasDate   = preg_match('~<time[^>]*datetime=~i',$html) || preg_match('~\b\d{4}-\d{2}-\d{2}\b~',$html);
        $item[10]=max(40,min(90, ($hasAuthor?20:0)+($hasDate?20:0)+($ldJsonCount?40:20)));
        $item[11]=60;
        $item[12]=$external>=3?80:($external>=1?65:45);
        $item[13]=($imgs+$vids)>=2?85:(($imgs+$vids)>=1?70:50);

        $item[14]=($h2c+$h3c)>=3?85:(($h2c+$h3c)>=1?70:50);
        $item[15]=$internal>=8?85:($internal>=4?70:45);

        $path = parse_url($url, PHP_URL_PATH) ?: '/';
        $hasQuery = parse_url($url, PHP_URL_QUERY) !== null;
        $slugOk = !$hasQuery && mb_strlen($path)<=80 && !preg_match('~[A-Z _]~',$path);
        $item[16]=$slugOk?85:55;

        $hasBreadcrumb = stripos($html,'BreadcrumbList')!==false || preg_match('~class=["\'][^"\']*breadcrumb~i',$html);
        $item[17]=$hasBreadcrumb?90:55;

        $item[18]=$viewport?90:40;
        $lazyCount=preg_match_all('~loading=["\']lazy["\']~i',$html,$m2);
        $item[19]=$lazyCount?75:60;
        $item[20]=60;
        $ctaCount=preg_match_all('~\b(get started|sign up|contact|buy now|learn more|try|subscribe)\b~i',$textOnly,$m3);
        $item[21]=$ctaCount?80:60;

        $hasArticle = stripos($html,'"@type"')!==false && preg_match('~"@type"\s*:\s*"(Article|NewsArticle|Product|Organization|WebPage)"~i',$html);
        $item[22]=$hasArticle?80:55;
        $item[23]=($h2c+$h3c)>=5?75:60;
        $item[24]=$ldJsonCount?85:40;
        $item[25]=(stripos($html,'"sameAs"')!==false || stripos($html,'"@type":"Organization"')!==false)?85:55;

        foreach($item as $k=>$v){ $item[$k]=max(0,min(100,(int)round($v))); }

        $avg = fn($ids)=> (int) round(array_sum(array_map(fn($i)=>$item[$i]??0,$ids))/max(1,count($ids)));
        $contentCat=$avg(range(1,5));
        $techCat=$avg(range(6,9));
        $qualityCat=$avg(range(10,13));
        $structureCat=$avg(range(14,17));
        $uxCat=$avg(range(18,21));
        $entityCat=$avg(range(22,25));

        $contentScore=(int) round($contentCat*0.45 + $qualityCat*0.30 + $structureCat*0.25);
        $overall=(int) round($contentCat*0.25 + $techCat*0.18 + $qualityCat*0.18 + $structureCat*0.17 + $uxCat*0.12 + $entityCat*0.10);

        $humanPct = max(20, min(95, 70 - abs(18 - $wps) + ($overall - 60)*0.3));
        $aiPct    = max(5, 100 - $humanPct);

        return response()->json([
            'httpStatus'   => $status,
            'titleLen'     => mb_strlen($title),
            'metaLen'      => mb_strlen($metaDesc),
            'canonical'    => $canonical ?: '—',
            'robots'       => $robots ?: ($headersRobots ?: '—'),
            'viewport'     => $viewport ? 'Yes' : '—',
            'headings'     => "H1:$h1c • H2:$h2c • H3:$h3c",
            'internalLinks'=> $internal,
            'schema'       => $ldJsonCount ? "Yes ($ldJsonCount)" : '—',

            'itemScores'   => $item,
            'contentScore' => $contentScore,
            'overall'      => $overall,
            'humanPct'     => (int) round($humanPct),
            'aiPct'        => (int) round($aiPct),
        ]);
    }

    private function normalizeUrl(?string $u): ?string
    {
        if (!$u) return null;
        $u = trim($u);
        if ($u === '') return null;
        if (!preg_match('~^https?://~i', $u)) {
            $u = 'https://' . ltrim($u, '/');
        }
        if (!filter_var($u, FILTER_VALIDATE_URL)) return null;
        $scheme = parse_url($u, PHP_URL_SCHEME);
        if (!in_array(strtolower($scheme), ['http', 'https'], true)) return null;
        return $u;
    }
}
