<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use DOMDocument, DOMXPath;

class ToolsController extends Controller
{
    public function crawlSitemap(Request $req)
    {
        $site = $req->validate(['site'=>'required|url'])['site'];
        $base = rtrim($site,'/');
        $sitemapUrls = [
            "$base/sitemap.xml",
            "$base/sitemap_index.xml"
        ];

        // Try robots.txt hint
        try {
            $robots = Http::timeout(6)->get("$base/robots.txt")->body();
            if ($robots && preg_match_all('/sitemap:\s*(.+)/i', $robots, $m)) {
                foreach ($m[1] as $u) $sitemapUrls[] = trim($u);
            }
        } catch (\Throwable $e) {}

        $found = [];
        foreach (array_unique($sitemapUrls) as $sm) {
            try {
                $xml = Http::timeout(10)->get($sm)->body();
                if (!$xml) continue;
                if (preg_match_all('/<loc>(.*?)<\/loc>/i', $xml, $m)) {
                    foreach ($m[1] as $loc) $found[] = trim($loc);
                }
            } catch (\Throwable $e) {}
        }

        $found = array_values(array_unique($found));
        return response()->json(['ok'=>true,'count'=>count($found),'urls'=>array_slice($found,0,200)]);
    }

    public function pageSpeed(Request $req)
    {
        $url = $req->validate(['url'=>'required|url'])['url'];
        $key = env('PAGESPEED_API_KEY');
        if (!$key) {
            return response()->json(['ok'=>true,'note'=>'PAGESPEED_API_KEY is not set. Returning placeholders.','lighthouse'=>['LCP'=>null,'INP'=>null,'CLS'=>null]]);
        }
        try{
            $api = "https://www.googleapis.com/pagespeedonline/v5/runPagespeed?strategy=mobile&url=".urlencode($url)."&key=$key";
            $json = Http::timeout(30)->get($api)->json();
            $audits = $json['lighthouseResult']['audits'] ?? [];
            $metrics = [
                'LCP' => $audits['largest-contentful-paint']['numericValue'] ?? null,
                'INP' => $audits['experimental-interaction-to-next-paint']['numericValue'] ?? null,
                'CLS' => $audits['cumulative-layout-shift']['numericValue'] ?? null,
                'Performance' => $json['lighthouseResult']['categories']['performance']['score']*100 ?? null
            ];
            return response()->json(['ok'=>true,'lighthouse'=>$metrics]);
        } catch(\Throwable $e){
            return response()->json(['ok'=>false,'error'=>$e->getMessage()], 500);
        }
    }

    public function schemaGenerate(Request $req)
    {
        $data = $req->validate([
            'url' => 'required|url',
            'type'=> 'nullable|string'
        ]);
        $type = $data['type'] ?: 'Article';
        $tpl = [
            "@context"=>"https://schema.org",
            "@type"=>$type,
            "headline"=>"Your Article Title",
            "description"=>"One‑sentence summary.",
            "datePublished"=>date(DATE_ATOM),
            "author"=>["@type"=>"Person","name"=>"Author Name"],
            "mainEntityOfPage"=>$data['url']
        ];
        if ($type==='FAQPage') {
            $tpl = [
                "@context"=>"https://schema.org",
                "@type"=>"FAQPage",
                "mainEntity"=>[
                    ["@type"=>"Question","name"=>"Question 1","acceptedAnswer"=>["@type"=>"Answer","text"=>"Answer 1"]],
                    ["@type"=>"Question","name"=>"Question 2","acceptedAnswer"=>["@type"=>"Answer","text"=>"Answer 2"]],
                ]
            ];
        }
        return response()->json(['ok'=>true,'jsonld'=>$tpl]);
    }

    public function socialPreview(Request $req)
    {
        $url = $req->validate(['url'=>'required|url'])['url'];
        try{
            $html = Http::timeout(12)->get($url)->body() ?? '';
            libxml_use_internal_errors(true);
            $dom = new DOMDocument(); @$dom->loadHTML($html);
            $xp  = new DOMXPath($dom);
            $q   = fn($x) => iterator_to_array($xp->query($x) ?? []);
            $attr= fn($n,$a) => $n?->attributes?->getNamedItem($a)?->nodeValue ?? '';

            $og = [];
            foreach ($q('//meta[@property]') as $m) {
                $p = strtolower($attr($m,'property'));
                if (str_starts_with($p,'og:') || str_starts_with($p,'twitter:')) {
                    $og[$p] = $attr($m,'content');
                }
            }
            return response()->json(['ok'=>true,'meta'=>$og]);
        } catch (\Throwable $e) {
            return response()->json(['ok'=>false,'error'=>$e->getMessage()], 500);
        }
    }

    public function entitiesGap(Request $req)
    {
        $data = $req->validate([
            'url'=>'required|url',
            'competitors'=>'array'
        ]);
        $fetch = function($u){
            try { return Http::timeout(12)->get($u)->body() ?? ''; } catch(\Throwable $e){ return ''; }
        };
        $clean = fn($h)=> mb_strtolower(preg_replace('/\s+/u',' ', strip_tags($h)));
        $tokenize = function($t){
            $w = preg_split('/\W+/u', $t, -1, PREG_SPLIT_NO_EMPTY) ?: [];
            $stop = ['the','and','for','you','with','that','your','from','this','are','was','were','have','has','had','but','not','they','their','them','his','her','its','our','ours','about','into','out','over','under','a','an','to','of','in','on','by','or','as','is','it','be','at','we','can','will','if','then','so'];
            return array_values(array_diff($w, $stop));
        };

        $ourHtml = $fetch($data['url']);
        $ourTokens = $tokenize($clean($ourHtml));

        $freq = array_count_values($ourTokens);
        arsort($freq);
        $topOurs = array_slice($freq, 0, 50, true);

        $compFreq = [];
        foreach (($data['competitors'] ?? []) as $c) {
            $tokens = $tokenize($clean($fetch($c)));
            foreach ($tokens as $t) $compFreq[$t] = ($compFreq[$t] ?? 0) + 1;
        }
        arsort($compFreq);
        $topComp = array_slice($compFreq, 0, 50, true);

        // gap: tokens in competitors not frequent in ours
        $gap = array_diff_key($topComp, $topOurs);
        $gap = array_slice($gap, 0, 50, true);

        return response()->json(['ok'=>true,'top_ours'=>$topOurs,'top_comp'=>$topComp,'gap'=>$gap]);
    }

    public function readability(Request $req)
    {
        $url = $req->validate(['url'=>'required|url'])['url'];
        try{
            $html = Http::timeout(12)->get($url)->body() ?? '';
            $text = trim(preg_replace('/\s+/u',' ', strip_tags($html)));
            $words = preg_split('/\W+/u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [];
            $sentences = preg_split('/(?<=[.!?])\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [];
            $syll = 0;
            foreach ($words as $w) $syll += max(1, preg_match_all('/[aeiouy]+/i',$w));
            $W = max(1, count($words)); $S = max(1, count($sentences));
            $FRE = 206.835 - 1.015*($W/$S) - 84.6*($syll/$W);
            $grade = 0.39*($W/$S) + 11.8*($syll/$W) - 15.59;
            return response()->json(['ok'=>true,'flesch'=>round($FRE,1),'grade'=>round($grade,1),'words'=>$W,'sentences'=>$S]);
        } catch (\Throwable $e) {
            return response()->json(['ok'=>false,'error'=>$e->getMessage()], 500);
        }
    }

    public function hreflang(Request $req)
    {
        $url = $req->validate(['url'=>'required|url'])['url'];
        try{
            $html = Http::timeout(12)->get($url)->body() ?? '';
            libxml_use_internal_errors(true);
            $dom = new DOMDocument(); @$dom->loadHTML($html);
            $xp  = new DOMXPath($dom);
            $list = [];
            foreach ($xp->query('//link[@rel="alternate"][@hreflang]') as $lnk) {
                $list[] = [
                    'hreflang' => $lnk->getAttribute('hreflang'),
                    'href' => $lnk->getAttribute('href'),
                ];
            }
            $valid = !empty($list);
            return response()->json(['ok'=>true,'valid'=>$valid,'tags'=>$list]);
        } catch (\Throwable $e) {
            return response()->json(['ok'=>false,'error'=>$e->getMessage()], 500);
        }
    }
}
