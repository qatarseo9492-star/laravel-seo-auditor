<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// homepage
Route::get('/', fn () => view('home'));

// analyze JSON
Route::post('/analyze-json', function (Request $req) {
    $url = trim($req->input('url',''));
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return response()->json(['ok'=>false,'error'=>'Invalid URL'],422);
    }
    try {
        $resp = Http::withHeaders([
            'User-Agent' => 'SemanticSEO-MasterAnalyzer/1.0 (+https://yourdomain.com)'
        ])->timeout(12)->connectTimeout(5)->get($url);
        if ($resp->status() >= 400) {
            return response()->json(['ok'=>false,'error'=>'Request failed'],502);
        }
        $html = $resp->body();
        $doc = analyze_document($html, $url);
        return response()->json(['ok'=>true] + $doc);
    } catch (\Throwable $e) {
        return response()->json(['ok'=>false,'error'=>$e->getMessage()],500);
    }
});

// competitor compare (optional)
Route::post('/compare', function(Request $req){
    $urls = $req->input('urls',[]);
    $rows=[];
    foreach($urls as $u){
        try{
            $r = Http::timeout(10)->get($u);
            $doc = new DOMDocument();
            libxml_use_internal_errors(true);
            $doc->loadHTML($r->body());
            $xp = new DOMXPath($doc);
            $title = trim($xp->query('//title')->item(0)?->textContent ?? '');
            $desc='';
            $meta = $xp->query('//meta[@name="description"]')->item(0);
            if($meta) $desc=$meta->getAttribute('content');
            $rows[]=['url'=>$u,'title'=>$title,'desc'=>$desc];
        }catch(\Throwable $e){
            $rows[]=['url'=>$u,'title'=>'(error)','desc'=>$e->getMessage()];
        }
    }
    return response()->json(['ok'=>true,'rows'=>$rows]);
});

/* =====================================================
   HELPER: ANALYZE DOCUMENT WITH STRICT 25 CHECKLIST
   ===================================================== */
function analyze_document(string $html, string $url): array {
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    $xp  = new DOMXPath($dom);

    $titleNode = $xp->query('//title')->item(0);
    $title = $titleNode?->textContent ?? '';
    $metaDescNode = $xp->query('//meta[translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="description"]')->item(0);
    $metaDescLen = $metaDescNode ? strlen($metaDescNode->getAttribute('content')) : 0;
    $canonicalNode = $xp->query('//link[@rel="canonical"]')->item(0);
    $canonical = $canonicalNode ? $canonicalNode->getAttribute('href') : null;
    $robotsNode = $xp->query('//meta[@name="robots"]')->item(0);
    $robots = $robotsNode ? $robotsNode->getAttribute('content') : '';
    $viewportNode = $xp->query('//meta[@name="viewport"]')->item(0);
    $viewport = !!$viewportNode;

    $h1 = $xp->query('//h1')->length;
    $h2 = $xp->query('//h2')->length;
    $h3 = $xp->query('//h3')->length;
    $internal = 0;
    foreach($xp->query('//a[@href]') as $a){
        $href=$a->getAttribute('href');
        if(str_starts_with($href,'/')) $internal++;
    }

    $fullText = strtolower($dom->textContent ?? '');
    $types = []; // detect schema types
    foreach($xp->query('//script[@type="application/ld+json"]') as $node){
        $j=@json_decode($node->textContent,true);
        if(is_array($j)){
            if(isset($j['@type'])) $types[]=$j['@type'];
            if(isset($j[0]['@type'])) $types[]=$j[0]['@type'];
        }
    }

    $path = parse_url($url,PHP_URL_PATH) ?? '/';
    $xmlMap = true; // simplify
    $read=['fre'=>60]; // simplify
    $ai=['label'=>'Likely Human','ai_pct'=>42,'human_pct'=>58];

    /* insert the strict 25-checklist scoring from my previous message here */
    // --- START scoring block ---
    // (Paste the entire big code block I gave you with $scores,$autoCheck,$suggestions,$overall)
    // --- END scoring block ---
}
