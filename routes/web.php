<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

Route::get('/', fn () => view('home'));

Route::post('/analyze-json', function (\Illuminate\Http\Request $req) {
    $url = trim($req->input('url', ''));
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return response()->json(['ok' => false, 'error' => 'Invalid URL'], 422);
    }

    try {
        $resp = Http::withHeaders([
            'User-Agent' => 'SemanticSEO-MasterAnalyzer/2.1 (+https://yourdomain.com)'
        ])->timeout(12)->connectTimeout(5)->get($url);

        $status = $resp->status();
        if ($status >= 400) return response()->json(['ok'=>false,'error'=>"Request failed ($status)"], 502);

        $html  = $resp->body() ?? '';
        $host  = parse_url($url, PHP_URL_HOST) ?: '';
        $scheme= preg_match('#^https://#',$url)?'https://':'http://';

        // PHP 8.0 polyfill for array_is_list (Laravel on 8.0 sometimes)
        if (!function_exists('array_is_list')) {
            function array_is_list(array $array): bool {
                $i = 0;
                foreach ($array as $k => $_) { if ($k !== $i++) return false; }
                return true;
            }
        }

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);
        $xp  = new DOMXPath($dom);
        $q   = fn($x) => iterator_to_array($xp->query($x) ?? []);
        $attr= fn($n,$a) => $n?->attributes?->getNamedItem($a)?->nodeValue ?? '';

        // pulls
        $titleNode = $q('//title')[0] ?? null;
        $titleText = trim($titleNode?->textContent ?? '');
        $metaDescNode = $q('//meta[translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="description"]')[0] ?? null;
        $metaDesc = trim($attr($metaDescNode, 'content'));
        $canonicalNode = $q('//link[translate(@rel,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="canonical"]')[0] ?? null;
        $canonicalHref = trim($attr($canonicalNode, 'href'));
        $robotsNode    = $q('//meta[translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="robots"]')[0] ?? null;
        $robots        = strtolower(trim($attr($robotsNode, 'content')));
        $viewportNode  = $q('//meta[translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="viewport"]')[0] ?? null;
        $viewport      = strtolower(trim($attr($viewportNode, 'content')));

        $bodyNode = $q('//body')[0] ?? null;
        $bodyText = trim(preg_replace('/\s+/u', ' ', $bodyNode?->textContent ?? ''));

        $h1s=$q('//h1'); $h2s=$q('//h2'); $h3s=$q('//h3');
        $h1Text = trim($h1s[0]?->textContent ?? '');
        $firstP = trim(($q('//p[normalize-space()][1]')[0]?->textContent) ?? '');

        $anchors = $q('//a[@href]');
        $internalLinks=0; $externalLinks=0; $externalTrusted=0; $externalYears=0; $keywordyAnchors=0;
        $trustedTlds=['.gov','.edu']; $trustedHosts=['wikipedia.org','who.int','nih.gov','un.org','oecd.org','worldbank.org','data.gov'];
        foreach ($anchors as $a) {
            $href = trim($attr($a,'href'));
            if (!$href || str_starts_with($href,'mailto:') || str_starts_with($href,'tel:') || str_starts_with($href,'javascript:')) continue;
            $abs = $href;
            if (!str_starts_with($href,'http')) {
                $abs = str_starts_with($href,'/') ? $scheme.$host.$href : rtrim($scheme.$host,'/').'/'.$href;
            }
            $hHost = parse_url($abs, PHP_URL_HOST) ?: '';
            $txt = strtolower(trim($a->textContent ?? ''));
            if ($hHost === $host) $internalLinks++; else {
                $externalLinks++;
                foreach ($trustedTlds as $tld) if (str_ends_with($hHost,$tld)) { $externalTrusted++; break; }
                foreach ($trustedHosts as $th) if (str_ends_with($hHost,$th)) { $externalTrusted++; break; }
            }
            if ($txt && !in_array($txt, ['click here','here','learn more'])) $keywordyAnchors++;
            if (preg_match('/\b(2023|2024|2025)\b/', $txt)) $externalYears++;
        }

        $imgs = $q('//img');
        $imgsWithAlt = array_filter($imgs, fn($n) => strlen(trim($attr($n,'alt'))) > 0);
        $imgsLazy = array_filter($imgs, fn($n) => strtolower($attr($n,'loading'))==='lazy' || strtolower($attr($n,'decoding'))==='async');

        $ctaWords=['buy','signup','sign up','contact','get started','start now','download','subscribe','add to cart','learn more','try','join','register'];
        $ctaFound=false;
        foreach ($anchors as $a) {
            $t = strtolower(trim($a->textContent ?? ''));
            if (!$t) continue; foreach ($ctaWords as $w) if (str_contains($t,$w)) { $ctaFound=true; break 2; }
        }
        if (!$ctaFound) foreach ($q('//button') as $b) {
            $t = strtolower(trim($b->textContent ?? '')); foreach ($ctaWords as $w) if (str_contains($t,$w)) { $ctaFound=true; break; }
        }

        $jsonLdScripts = $q('//script[@type="application/ld+json"]');
        $schemaTypes=[]; $hasFAQ=false; $hasArticle=false; $hasProduct=false; $hasBreadcrumb=false; $orgSameAs=false; $hasOrganization=false;
        foreach ($jsonLdScripts as $sc) {
            $txt = trim($sc->textContent ?? ''); if (!$txt) continue;
            try{
                $data = json_decode($txt, true, 512, JSON_INVALID_UTF8_IGNORE);
                $nodes = (is_array($data) && array_is_list($data)) ? $data : [$data];
                foreach ($nodes as $node){
                    $type = $node['@type'] ?? ($node['type'] ?? null);
                    if (is_array($type)) foreach($type as $t) $schemaTypes[]=$t; elseif (is_string($type)) $schemaTypes[]=$type;
                    $ct = strtolower(json_encode($node));
                    $hasFAQ        = $hasFAQ        || str_contains($ct,'"@type":"faqpage"');
                    $hasArticle    = $hasArticle    || str_contains($ct,'"@type":"article"');
                    $hasProduct    = $hasProduct    || str_contains($ct,'"@type":"product"');
                    $hasBreadcrumb = $hasBreadcrumb || str_contains($ct,'"@type":"breadcrumblist"');
                    if (($node['@type'] ?? '') === 'Organization') {
                        $hasOrganization = true;
                        if (!empty($node['sameAs']) && is_array($node['sameAs'])) $orgSameAs = true;
                    }
                }
            }catch(\Throwable $e){}
        }
        $hasBreadcrumbUI = count($q('//*[@aria-label="breadcrumb"] | //nav[contains(@class,"breadcrumb") or contains(@aria-label,"breadcrumb")]'))>0;

        $path = parse_url($url, PHP_URL_PATH) ?: '/';
        $slugOk=false; $slug='';
        if ($path !== '/') {
            $segments = array_values(array_filter(explode('/', $path)));
            $slug = end($segments) ?: '';
            $slugOk = (strlen($slug)>1) && (strtolower($slug)===$slug) && !str_contains($slug,' ') && !preg_match('/[_%]/',$slug) && (substr_count($slug,'-')<=8);
        }

        $authorMeta = $q('//meta[contains(translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz"),"author") or contains(translate(@property,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz"),"author")]');
        $timeTags   = $q('//time[@datetime] | //meta[@itemprop="datePublished" or @itemprop="dateModified"] | //span[contains(@class,"date") or contains(@class,"updated")]');

        $scripts = $q('//script');
        $deferred=0; $asyncd=0; $blockingScripts=0;
        foreach($scripts as $s){ $d=strtolower($attr($s,'defer'))==='defer'; if($d)$deferred++; $a=strtolower($attr($s,'async'))==='async'; if($a)$asyncd++; if(!$d && !$a && $attr($s,'src')) $blockingScripts++; }
        $hasPreload = count($q('//link[@rel="preload" or @rel="preconnect" or @rel="dns-prefetch"]'))>0;
        $responsiveImgs = count($q('//img[@srcset or @sizes]'))>0;

        preg_match_all('/\b[\p{L}\p{N}’\'\-]+\b/u', $bodyText, $m);
        $wc = max(1, count($m[0] ?? []));
        $sentences = max(1, count(preg_split('/[.!?]+[\s]+/u', $bodyText, -1, PREG_SPLIT_NO_EMPTY)));
        $avgSentLen = $wc / $sentences;
        $uniqueWords = count(array_unique(array_map('mb_strtolower', $m[0] ?? [])));
        $ttr = $uniqueWords / max(1,$wc);

        $clamp = fn($v)=>max(0,min(100,(int)round($v)));
        $inRangeScore=function($n,$min,$max,$softMin,$softMax)use($clamp){ if($n<=0) return 0; if($n>=$min && $n<=$max) return 100; if($n>=$softMin && $n<=$softMax) return 70; $d=min(abs($n-$min),abs($n-$max)); return $clamp(max(0,70-$d)); };
        $jaccard=function($a,$b){ $wa=array_unique(preg_split('/\W+/u', mb_strtolower($a), -1, PREG_SPLIT_NO_EMPTY)); $wb=array_unique(preg_split('/\W+/u', mb_strtolower($b), -1, PREG_SPLIT_NO_EMPTY)); if(!$wa||!$wb) return 0; $i=count(array_intersect($wa,$wb)); $u=count(array_unique(array_merge($wa,$wb))); return $u? $i/$u : 0; };
        $sc = fn($v) => is_countable($v) ? count($v) : 0; // safe count

        // ==== Scoring ====
        $S=[];
        $patterns=['how to','what is','guide','best','vs','compare','price','buy','review','download'];
        $tLower = mb_strtolower($titleText.' '.$h1Text);
        $intentHints=0; foreach($patterns as $p) if(str_contains($tLower,$p)) $intentHints+=10;
        $sim = $jaccard($titleText, $h1Text);

        $qHeads=0; foreach(array_merge($h2s,$h3s) as $h){ if(str_contains(mb_strtolower($h->textContent),'?')) $qHeads++; }

        $S['ck-1'] = $clamp( min(100, 40*$sim + min(40,$intentHints) + (mb_strlen($firstP)>40?20:0)) );
        $S['ck-2'] = $clamp( min(100, ($qHeads*12) + ($ttr*100*0.25) + ($hasFAQ?30:0)) );
        $S['ck-3'] = $clamp( min(100, ($sim*100*0.6) + ($inRangeScore(mb_strlen($h1Text),20,80,10,100)*0.4)) );
        $S['ck-4'] = $clamp( min(100, ($hasFAQ?80:0) + min(20, preg_match_all('/\?/',$bodyText,$tmp)*2)) );
        $S['ck-5'] = $clamp( 100 - abs($avgSentLen - 17) * 6 );
        $S['ck-6'] = $clamp( $inRangeScore(mb_strlen($titleText),50,60,35,70) );
        $S['ck-7'] = $clamp( $inRangeScore(mb_strlen($metaDesc),140,160,110,180) );
        $S['ck-8'] = $canonicalHref ? 100 : 0;
        $S['ck-9'] = (!str_contains($robots,'noindex')) ? 100 : 0;
        $tables=$sc($q('//table')); $pres=$sc($q('//pre | //code')); $lists=$sc($q('//ol/li'))+$sc($q('//ul/li'));
        $uvWords=preg_match('/\b(example|template|case study|dataset|calculator|tool)\b/i',$bodyText)?1:0;
        $S['ck-11']= $clamp( min(100, min(40,(int)floor($wc/600)*10) + min(20,$tables*10) + min(15,$pres*7) + min(15,(int)floor($lists/8)*5) + ($uvWords?20:0)) );
        $S['ck-12']= $clamp( min(100, min(60,$externalTrusted*20) + min(40,$externalYears*10)) );
        $altRatio = ($sc($imgs)? ($sc($imgsWithAlt)/max(1,$sc($imgs))) : 0);
        $S['ck-13']= $clamp( min(100, min(60,$sc($imgs)*10) + (int)round($altRatio*40)) );
        $S['ck-14']= $clamp( min(100, min(60,$sc($h2s)*15) + min(40,$sc($h3s)*10)) );
        $S['ck-15']= $clamp( min(100, min(70,$internalLinks*10) + min(30,$keywordyAnchors*3)) );
        $S['ck-16']= $clamp( ($slugOk?100:(strlen($slug)?50:30)) );
        $S['ck-17']= $clamp( ($hasBreadcrumb || $hasBreadcrumbUI)?100:0 );
        $S['ck-18']= $clamp( min(100, ($viewport && str_contains($viewport,'width=device-width')?70:0) + ($responsiveImgs?30:0)) );
        $S['ck-19']= $clamp( min(100, min(40,$sc($imgsLazy)*8) + min(30,$deferred*5 + $asyncd*5) + ($hasPreload?20:0) - min(30,max(0,$blockingScripts-2)*10)) );
        $S['ck-20']= $clamp( min(100, 50 + ($responsiveImgs?15:0) + ($viewport?15:0) - min(30,$blockingScripts*7)) );
        $S['ck-21']= $clamp( $ctaFound?100:20 );
        $eSim = $jaccard($h1Text,$firstP);
        $S['ck-22']= $clamp( min(100, ($eSim*100*0.7) + ($hasArticle?30:0)) );
        $wikiLinks=0; foreach($anchors as $a){ $h=strtolower($attr($a,'href')??''); if(str_contains($h,'wikipedia.org')) $wikiLinks++; }
        $properH2s=0; foreach($h2s as $h){ if(preg_match('/\b[A-Z][a-z]{2,}/', trim($h->textContent??''))) $properH2s++; }
        $S['ck-23']= $clamp( min(100, min(60,$wikiLinks*20) + min(40,$properH2s*10)) );
        $validTypes = array_values(array_unique($schemaTypes));
        $S['ck-24']= $clamp( min(100, min(100,count($validTypes)*20)) );
        $footer = $q('//footer')[0] ?? null; $footerSocial=0;
        if($footer){ foreach($footer->getElementsByTagName('a') as $a){ $h=strtolower($a->getAttribute('href')); if(preg_match('#(facebook|twitter|x\.com|instagram|linkedin|youtube|tiktok)\.com#',$h)) $footerSocial++; } }
        $S['ck-25']= $clamp( min(100, ($orgSameAs?100:0) + (!$orgSameAs && $hasOrganization ? 60:0) + min(40,$footerSocial*10)) );

        // Suggestions — FIX: include $h2s and $h3s in use(), use sc() for safe counts
        $suggest = function($id) use (
            $S,$titleText,$metaDesc,$h1Text,$qHeads,$hasFAQ,$imgs,$imgsWithAlt,$altRatio,
            $internalLinks,$keywordyAnchors,$slugOk,$slug,$hasBreadcrumb,$hasBreadcrumbUI,
            $viewport,$responsiveImgs,$imgsLazy,$deferred,$asyncd,$blockingScripts,$hasPreload,
            $ctaFound,$authorMeta,$timeTags,$externalTrusted,$externalYears,$validTypes,$orgSameAs,
            $hasOrganization,$wikiLinks,$properH2s,$wc,$lists,$tables,$pres,$eSim,$sim,$h2s,$h3s // <— added here
        ){
            $tips=[]; $SC = fn($v)=> (is_countable($v)?count($v):0); // local safe count

            switch($id){
                case 'ck-1':
                    if ($sim<0.4) $tips[]='Align H1 with Title (same primary keyword).';
                    if (mb_strlen($h1Text)<20) $tips[]='Make H1 20–80 chars and descriptive.';
                    $tips[]='Open with a clear first paragraph stating the intent.';
                    break;
                case 'ck-2':
                    if ($qHeads<2) $tips[]='Add 2–4 H2/H3 in question form (PAA).';
                    $tips[]='Cover synonyms/related terms; add a short FAQ block.';
                    break;
                case 'ck-3':
                    if ($sim<0.6) $tips[]='Keep H1 wording closer to Title.';
                    $tips[]='Keep H1 length ~20–80 chars.';
                    break;
                case 'ck-4':
                    if (!$hasFAQ) $tips[]='Add an FAQ section with FAQPage schema.';
                    $tips[]='Answer 3–5 common sub‑questions.';
                    break;
                case 'ck-5':
                    $tips[]='Shorten sentences to average ~12–22 words.';
                    $tips[]='Use plain language and short paragraphs.';
                    break;
                case 'ck-6':
                    $tips[]='Keep Title ~50–60 chars; front‑load main keyword.';
                    break;
                case 'ck-7':
                    if (mb_strlen($metaDesc)<140) $tips[]='Write a 140–160 char meta description with a CTA.';
                    else $tips[]='Trim meta description to ~160 and add a CTA.';
                    break;
                case 'ck-8':
                    $tips[]='Add <link rel="canonical" href="preferred-URL"> in <head>.';
                    break;
                case 'ck-9':
                    $tips[]='Remove noindex and include the URL in your XML sitemap.';
                    break;
                case 'ck-10':
                    if (!count($authorMeta)) $tips[]='Add author name (visible + meta).';
                    if (!count($timeTags))  $tips[]='Show published/updated date.';
                    $tips[]='Link to About/Contact/Editorial policy.';
                    break;
                case 'ck-11':
                    if ($wc<1200) $tips[]='Add unique value (examples, templates, data, tool).';
                    if ($tables<1 && $pres<1 && $lists<8) $tips[]='Use tables/lists/code where helpful.';
                    break;
                case 'ck-12':
                    if ($externalTrusted<2) $tips[]='Cite 2–3 trustworthy sources (.gov/.edu/WHO/Wikipedia).';
                    if ($externalYears<1)  $tips[]='Update stats to 2024/2025 and cite them.';
                    break;
                case 'ck-13':
                    if ($SC($imgs)<2) $tips[]='Add 2–4 relevant images/diagrams with captions.';
                    if ($altRatio<0.8)    $tips[]='Provide descriptive alt text for images.';
                    break;
                case 'ck-14':
                    if ($SC($h2s)<3) $tips[]='Add ≥3 H2 sections for key subtopics.';
                    if ($SC($h3s)<2) $tips[]='Nest H3s for deeper subsections.';
                    break;
                case 'ck-15':
                    if ($internalLinks<3) $tips[]='Insert 3–6 internal links to hubs/related pages.';
                    if ($keywordyAnchors<3) $tips[]='Use descriptive anchor text (avoid “click here”).';
                    break;
                case 'ck-16':
                    if (!$slugOk) $tips[]='Use short, lowercase, hyphenated slug; avoid spaces/underscores.';
                    break;
                case 'ck-17':
                    if (!$hasBreadcrumb && !$hasBreadcrumbUI) $tips[]='Add visible breadcrumbs + BreadcrumbList schema.';
                    break;
                case 'ck-18':
                    if (!$viewport) $tips[]='Add responsive meta viewport in <head>.';
                    if (!$responsiveImgs) $tips[]='Serve responsive images (srcset/sizes).';
                    break;
                case 'ck-19':
                    if ($SC($imgsLazy)<2) $tips[]='Lazy‑load below‑the‑fold images (loading="lazy").';
                    if ($blockingScripts>2) $tips[]='Defer/async non‑critical JS; reduce blocking scripts.';
                    if (!$hasPreload) $tips[]='Preload critical fonts/assets; preconnect to CDNs.';
                    break;
                case 'ck-20':
                    if ($blockingScripts>0) $tips[]='Reduce render‑blocking JS/CSS for better LCP/INP.';
                    if (!$responsiveImgs) $tips[]='Serve properly sized images to reduce CLS/LCP.';
                    break;
                case 'ck-21':
                    if (!$ctaFound) $tips[]='Add clear CTAs (e.g., “Get started”, “Contact”, “Download”).';
                    break;
                case 'ck-22':
                    if ($eSim<0.4) $tips[]='Define the primary entity in the first paragraph.';
                    if (!in_array('Article',$validTypes ?? [])) $tips[]='Add Article schema with headline and date.';
                    break;
                case 'ck-23':
                    if ($wikiLinks<1) $tips[]='Mention related entities and link to their knowledge pages.';
                    if ($properH2s<2) $tips[]='Use H2s that name related entities/concepts.';
                    break;
                case 'ck-24':
                    if (!count($validTypes ?? [])) $tips[]='Add valid JSON‑LD (Article/FAQ/Product etc.).';
                    break;
                case 'ck-25':
                    if (!$orgSameAs) $tips[]='Add Organization schema with sameAs links to official profiles.';
                    break;
            }
            return $tips ?: ['Looks good—minor polishing only.'];
        };

        $tipsOut=[];
        foreach(array_keys($S) as $id){ $tipsOut[$id] = $suggest($id); }

        $autoIds=[]; foreach($S as $id=>$score) if($score>=70) $autoIds[]=$id;
        $overall = array_sum($S)/max(1,count($S));

        return response()->json([
            'ok'=>true,
            'url'=>$url,
            'status'=>$status,
            'title'=>$titleText,
            'meta_description_len'=>mb_strlen($metaDesc),
            'canonical'=>$canonicalHref,
            'robots'=>$robots,
            'viewport'=>$viewport,
            'counts'=>[
                'h1'=>count($h1s),'h2'=>count($h2s),'h3'=>count($h3s),
                'images'=>count($imgs),'images_with_alt'=>count($imgsWithAlt),
                'internal_links'=>$internalLinks,'external_links'=>$externalLinks
            ],
            'schema'=>[
                'found_types'=>array_values(array_unique($schemaTypes)),
                'hasFAQ'=>$hasFAQ,'hasArticle'=>$hasArticle,'hasProduct'=>$hasProduct,
                'hasBreadcrumb'=>$hasBreadcrumb,'orgSameAs'=>$orgSameAs
            ],
            'scores'=>$S,
            'suggestions'=>$tipsOut,
            'auto_check_ids'=>$autoIds,
            'overall_score'=>round($overall,1),
        ]);
    } catch (\Throwable $e) {
        return response()->json(['ok'=>false,'error'=>'Analyze error: '.$e->getMessage()], 500);
    }
})->name('analyze.json');
