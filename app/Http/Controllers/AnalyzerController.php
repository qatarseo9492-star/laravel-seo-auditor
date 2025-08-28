<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AnalyzerController extends Controller
{
    /* ============================================================
     |  SEMANTIC ANALYZE  (drives the new stylish UI)
     |  - Returns: overall score + wheel label/color,
     |    quick stats, structure, links, schema,
     |    and a rich "categories" array with per-check scores.
     * ============================================================*/
    public function semanticAnalyze(Request $request)
    {
        if ($resp = $this->ensureRuntime()) { return $resp; }

        $request->validate([
            'url'            => 'required|url',
            'target_keyword' => 'nullable|string|max:120',
            'ua'             => 'nullable|string|max:255',
        ]);

        try {
            $url    = (string) $request->input('url');
            $target = trim((string) $request->input('target_keyword', ''));
            $ua     = (string) $request->input(
                'ua',
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124 Safari/537.36'
            );

            // Fetch
            $fetch = $this->fetchUrl($url, $ua);
            if (!$fetch['ok']) {
                return response()->json(['ok'=>false,'error'=>$fetch['error'] ?? 'Fetch failed'], 422);
            }
            $html = $fetch['body'];

            // Parse
            libxml_use_internal_errors(true);
            $dom = new \DOMDocument();
            $dom->loadHTML($html);
            $xp  = new \DOMXPath($dom);

            // Title & Meta
            $titleNode = $xp->query('//title')->item(0);
            $title = $titleNode ? trim($titleNode->textContent) : '';
            $metaNode = $xp->query("//meta[translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='description']/@content")->item(0);
            $meta = $metaNode ? trim($metaNode->nodeValue) : '';

            // Canonical
            $canonNode = $xp->query("//link[translate(@rel,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='canonical']/@href")->item(0);
            $canonical = $canonNode ? trim($canonNode->nodeValue) : null;

            // OpenGraph (basic presence)
            $og = $xp->query("//meta[starts-with(translate(@property,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz'),'og:')]");
            $ogPresent = $og && $og->length > 0;

            // Headings
            $headings = [];
            for ($i=1; $i<=6; $i++) {
                $arr = [];
                foreach ($xp->query("//h{$i}") as $n) { $arr[] = trim($n->textContent); }
                $headings["h{$i}"] = $arr;
            }
            $missingH1 = count($headings['h1'] ?? []) === 0;
            $skipped   = $this->checkHeadingSkips($headings);

            // Images
            $imgNodes = $xp->query('//img');
            $imgTotal = $imgNodes->length;
            $imgMissingAlt = 0;
            foreach ($imgNodes as $img) {
                if (trim((string)$img->getAttribute('alt')) === '') { $imgMissingAlt++; }
            }

            // Links
            $baseHost = $this->getHost($url);
            $internal=0; $external=0; $anchors=[];
            foreach ($xp->query('//a[@href]') as $a) {
                $href = trim((string)$a->getAttribute('href'));
                if ($href === '' || Str::startsWith($href, ['#','mailto:','javascript:'])) { continue; }
                $abs  = $this->absUrl($href, $url);
                $host = $this->getHost($abs);
                $type = ($host === $baseHost) ? 'internal' : 'external';
                if ($type==='internal') { $internal++; } else { $external++; }
                $text = trim(preg_replace('/\s+/u',' ', $a->textContent ?? ''));
                $anchors[] = ['text'=>$text,'href'=>$href,'type'=>$type];
            }

            // Structured data
            $hasJsonLd = $xp->query("//script[@type='application/ld+json']")->length > 0;
            $hasMicro  = $xp->query('//*[@itemscope or @itemtype]')->length > 0;
            $hasRdfa   = $xp->query('//*[@typeof or @property]')->length > 0;

            // Visible text & metrics
            foreach (['//script','//style','//noscript'] as $rm) {
                foreach ($xp->query($rm) as $node) { $node->parentNode?->removeChild($node); }
            }
            $text = trim(preg_replace('/\s+/u', ' ', $dom->textContent ?? ''));
            $textLen = mb_strlen($text);
            $htmlLen = mb_strlen($html);
            $ratio   = $htmlLen > 0 ? round(($textLen/$htmlLen)*100, 2) : 0.0;

            $sentences = preg_split('/(?<=[\.\!\?])\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
            $words     = preg_split('/\s+/u', preg_replace('/[^\p{L}\p{N}\s\-\'’]+/u',' ', $text), -1, PREG_SPLIT_NO_EMPTY);
            $wc        = max(count($words),1);
            $sc        = max(count($sentences),1);
            $syll=0;
            foreach ($words as $w) { $syll += $this->syllableGuess($w); }
            $flesch = (int) round(max(0, min(100, 206.835 - (1.015*($wc/$sc)) - (84.6*($syll/$wc)) )));

            // Target keyword signals
            $kw = null;
            if ($target !== '') {
                $first100 = mb_strtolower(Str::of($text)->words(100)->toString());
                $kw = [
                    'target'      => $target,
                    'occurrences' => substr_count(mb_strtolower($text), mb_strtolower($target)),
                    'in_title'    => Str::contains(Str::lower($title), Str::lower($target)),
                    'in_meta'     => Str::contains(Str::lower($meta), Str::lower($target)),
                    'in_intro'    => Str::contains($first100, mb_strtolower($target)),
                ];
            }

            /* --------------------------- BASIC SCORE --------------------------- */
            $score = 0;
            if ($title !== '') $score += 10;
            $tlen = mb_strlen($title);
            if ($tlen>=30 && $tlen<=60) $score += 10;
            if ($meta !== '') $score += 8;
            $mlen = mb_strlen($meta);
            if ($mlen>=120 && $mlen<=180) $score += 8;
            $altRatio = ($imgTotal>0) ? (1-($imgMissingAlt/$imgTotal)) : 1.0;
            $score += (int) round(16*$altRatio);
            if ($internal>0) $score += 5;
            if ($external>0) $score += 5;
            if ($hasJsonLd || $hasMicro || $hasRdfa) $score += 10;
            $score += (int) round($flesch/8);
            if ($ratio>=15) { $score += 10; } elseif ($ratio>=8) { $score += 6; }
            if (!$missingH1) $score += 6;
            if (!$skipped)   $score += 6;
            if ($ogPresent)  $score += 4;
            if ($canonical)  $score += 6;
            $score = max(0, min(100, $score));

            $wheelLabel = $score >= 80 ? 'Great Work — Well Optimized'
                         : ($score >= 60 ? 'Needs Optimization'
                         : 'Needs Significant Optimization');

            $wheelColor = $this->colorForScore($score);

            /* ---------------------- CATEGORIES + CHECKS ----------------------- */
            $domain = $this->getHost($url) ?? '';
            $checks = [];

            // Helper to push checks
            $push = function(string $catKey, string $catName, string $id, string $label, ?int $chkScore, ?bool $pass, string $advice, string $query) use (&$checks, $domain) {
                $color = is_null($chkScore) ? 'neutral' : $this->colorForScore($chkScore);
                $checks[$catKey]['key']  = $catKey;
                $checks[$catKey]['name'] = $catName;
                $checks[$catKey]['items'][] = [
                    'id'     => $id,
                    'label'  => $label,
                    'score'  => $chkScore,
                    'pass'   => $pass,
                    'color'  => $color,
                    'advice' => $advice,
                    'improve_search_url' => $this->googleUrl($query . ($domain ? " site:$domain" : "")),
                ];
            };

            // Content & Keywords
            $cat = 'content_keywords'; $catName = 'Content & Keywords';
            $kwTitleScore = ($target !== '') ? ($kw['in_title'] ? 100 : 45) : null;
            $push($cat,$catName,'kw-title','H1/Title includes primary topic',
                 $kwTitleScore, $kwTitleScore ? $kwTitleScore>=80 : null,
                 'Use your primary topic naturally near the start of the title/H1.',
                 "how to write title tag with primary keyword");

            $introScore = ($target !== '') ? ($kw['in_intro'] ? 100 : 55) : null;
            $push($cat,$catName,'kw-intro','Primary keyword in first paragraph',
                 $introScore, $introScore ? $introScore>=80 : null,
                 'Mention the main topic early (first 100–150 words) to establish relevance.',
                 "primary keyword in first paragraph SEO");

            $readableScore = $flesch; // 0–100 already
            $push($cat,$catName,'readability','Readable, NLP-friendly language',
                 $readableScore, $readableScore>=60,
                 'Short sentences, active voice, and concrete nouns help machines and humans.',
                 "improve readability score SEO");

            $faqScore = $this->detectFaqHint($dom) ? 100 : 50;
            $push($cat,$catName,'faqs','Includes FAQs / questions with answers',
                 $faqScore, $faqScore>=80,
                 'Add an FAQ section answering common People-Also-Ask queries.',
                 "add FAQ section SEO benefits");

            // Technical Elements
            $cat = 'technical'; $catName = 'Technical Elements';
            $titleLenScore = ($tlen>=50 && $tlen<=65) ? 100 : (($tlen>0) ? 70 : 0);
            $push($cat,$catName,'title-len','Title tag length ≈50–60 chars',
                 $titleLenScore, $titleLenScore>=80,
                 'Keep titles concise (≈55–60 chars) and compelling.',
                 "ideal title tag length");

            $metaLenScore = ($mlen>=140 && $mlen<=180) ? 100 : (($mlen>0) ? 70 : 0);
            $push($cat,$catName,'meta-len','Meta description ≈140–160 chars',
                 $metaLenScore, $metaLenScore>=80,
                 'Write value-focused copy with the primary/secondary topics.',
                 "ideal meta description length");

            $indexable = !$this->hasNoindex($xp);
            $push($cat,$catName,'indexable','Page indexable (no noindex)',
                 $indexable ? 100 : 0, $indexable,
                 'Remove noindex for pages that should rank.',
                 "why is my page not indexed noindex");

            $canonScore = $canonical ? (Str::startsWith($canonical, $this->originOf($url)) ? 100 : 70) : 40;
            $push($cat,$catName,'canonical','Canonical tag set correctly',
                 $canonScore, $canonScore>=80,
                 'Self-reference canonical for canonical pages; set to primary if duplicate.',
                 "canonical tag best practices");

            $ogScore = $ogPresent ? 100 : 40;
            $push($cat,$catName,'opengraph','OpenGraph meta present',
                 $ogScore, $ogScore>=80,
                 'Add basic OG tags: og:title, og:description, og:image.',
                 "required opengraph meta tags list");

            // Content Quality
            $cat = 'quality'; $catName = 'Content Quality';
            $extLinksScore = $external>0 ? 100 : 50;
            $push($cat,$catName,'citations','Cites authoritative sources (external links)',
                 $extLinksScore, $extLinksScore>=80,
                 'Cite 1–3 high-quality sources with descriptive anchor text.',
                 "how to cite authoritative sources SEO");

            $authorHints = $this->detectAuthorOrDate($dom) ? 100 : 60;
            $push($cat,$catName,'eeat','Clear author/date (E-E-A-T hint)',
                 $authorHints, $authorHints>=80,
                 'Show author name/role and updated date; add author bio page.',
                 "how to show author and date for E-E-A-T");

            // Structure & Architecture
            $cat = 'structure'; $catName = 'Structure & Architecture';
            $hScore = $skipped ? 50 : 100;
            $push($cat,$catName,'heading-flow','Logical H2/H3 hierarchy (no skips)',
                 $hScore, $hScore>=80,
                 'Use H2 for main sections, H3 for sub-sections without skipping levels.',
                 "heading hierarchy h2 h3 best practices");

            $intLinksScore = min(100, $internal*15); // 1→15, 2→30 ... cap 100
            $push($cat,$catName,'internal-links','Internal links to hub/related pages',
                 $intLinksScore, $intLinksScore>=80,
                 'Add contextual internal links using descriptive anchors.',
                 "internal linking strategy SEO");

            $urlScore = $this->slugScore($url);
            $push($cat,$catName,'url-slug','Clean, descriptive URL slug',
                 $urlScore, $urlScore>=80,
                 'Use short, hyphenated slugs including the primary topic.',
                 "how to write seo friendly url slug");

            $bcScore = $this->hasBreadcrumbJsonLd($dom) ? 100 : 50;
            $push($cat,$catName,'breadcrumbs','Breadcrumbs (+ schema) present',
                 $bcScore, $bcScore>=80,
                 'Add visual breadcrumbs and BreadcrumbList schema.',
                 "BreadcrumbList schema example");

            // User Signals & Experience
            $cat = 'ux'; $catName = 'User Signals & Experience';
            $viewport = $xp->query("//meta[translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='viewport']")->length>0;
            $push($cat,$catName,'mobile','Mobile-friendly viewport meta',
                 $viewport ? 100 : 40, $viewport,
                 'Add `<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">`.',
                 "viewport meta tag responsive");

            $ratioScore = $ratio>=20 ? 100 : ($ratio>=12 ? 75 : 45);
            $push($cat,$catName,'speed-hint','Content weight vs HTML (ratio)',
                 $ratioScore, $ratioScore>=80,
                 'Increase meaningful text and optimize heavy assets to improve TTI.',
                 "improve page speed content to html ratio");

            $ctaScore = $this->detectCTA($dom) ? 100 : 55;
            $push($cat,$catName,'cta','Clear CTAs / next steps',
                 $ctaScore, $ctaScore>=80,
                 'Add clear next steps: sign up, contact, learn more, related articles.',
                 "how to write effective call to action website");

            // Entities & Context
            $cat = 'entities'; $catName = 'Entities & Context';
            $schemaTypes = $this->jsonLdTypes($dom);
            $hasTypedSchema = !empty($schemaTypes);
            $push($cat,$catName,'schema','Valid schema (Article/FAQ/Product/etc.)',
                 $hasTypedSchema ? 100 : 50, $hasTypedSchema,
                 'Add appropriate schema (e.g., Article, FAQPage, Product).',
                 "which schema markup to use article product faq");

            $sameAs = $this->orgSameAs($dom);
            $sameAsScore = count($sameAs) ? 100 : 55;
            $push($cat,$catName,'sameas','Organization sameAs / profiles',
                 $sameAsScore, $sameAsScore>=80,
                 'In Organization schema, include sameAs links to official profiles.',
                 "organization schema sameAs examples");

            $primaryEntityScore = ($target && (($kw['occurrences'] ?? 0) >= 2)) ? 100 : (($target==='') ? null : 60);
            $push($cat,$catName,'primary-entity','Primary entity clearly present',
                 $primaryEntityScore, is_null($primaryEntityScore) ? null : $primaryEntityScore>=80,
                 'Reference your primary entity multiple times with context.',
                 "how to define primary entity in content");

            // Build categories array with average scores + color
            $categories = [];
            foreach ($checks as $ck) {
                $sc = collect($ck['items'])->pluck('score')->filter(fn($s)=>$s!==null)->all();
                $avg = count($sc) ? (int) round(array_sum($sc)/count($sc)) : null;
                $categories[] = [
                    'key'   => $ck['key'],
                    'name'  => $ck['name'],
                    'score' => $avg,
                    'color' => is_null($avg) ? 'neutral' : $this->colorForScore($avg),
                    'checks'=> $ck['items'],
                ];
            }

            // Recommendations (concise)
            $recs=[];
            if ($missingH1) $recs[] = ['severity'=>'Critical','text'=>'Add a single descriptive H1'];
            if ($tlen<30 || $tlen>65) $recs[] = ['severity'=>'Warning','text'=>'Adjust title to ~50–60 chars'];
            if ($meta==='') $recs[] = ['severity'=>'Warning','text'=>'Add a compelling meta description (140–160 chars)'];
            if ($imgMissingAlt>0) $recs[] = ['severity'=>'Warning','text'=>"Add alt text to {$imgMissingAlt} image(s)"];
            if ($internal===0) $recs[] = ['severity'=>'Warning','text'=>'Add relevant internal links'];
            if ($external===0) $recs[] = ['severity'=>'Info','text'=>'Cite 1–2 external authoritative sources'];
            if (!$hasJsonLd && !$hasMicro && !$hasRdfa) $recs[] = ['severity'=>'Info','text'=>'Add JSON-LD structured data'];
            if ($ratio<15) $recs[] = ['severity'=>'Info','text'=>'Increase main content depth'];
            if ($skipped) $recs[] = ['severity'=>'Info','text'=>'Fix heading hierarchy (avoid level jumps)'];
            if ($kw && !$kw['in_title']) $recs[] = ['severity'=>'Info','text'=>'Use the primary topic in the title naturally'];

            return response()->json([
                'ok' => true,
                'overall_score' => $score,
                'wheel' => [
                    'label' => $wheelLabel,
                    'color' => $wheelColor, // 'green'|'orange'|'red'
                ],
                'quick_stats' => [
                    'readability_flesch' => $flesch,
                    'word_count'         => $wc,
                    'image_count'        => $imgTotal,
                    'internal_links'     => $internal,
                    'external_links'     => $external,
                    'text_to_html_ratio' => $ratio,
                ],
                'content_structure' => [
                    'title'              => $title,
                    'meta_description'   => $meta,
                    'headings'           => $headings,
                    'missing_h1'         => $missingH1,
                    'skipped_levels'     => $skipped,
                ],
                'technical' => [
                    'structured_data'    => ['json_ld'=>$hasJsonLd,'microdata'=>$hasMicro,'rdfa'=>$hasRdfa],
                    'canonical'          => $canonical,
                    'opengraph_present'  => $ogPresent,
                ],
                'links' => [
                    'internal' => $internal,
                    'external' => $external,
                    'anchors'  => $anchors,
                ],
                'target_keyword' => $kw,
                'categories'     => $categories,
                'recommendations'=> $recs,
            ]);
        } catch (\Throwable $e) {
            \Log::error('semanticAnalyze failed', ['ex'=>$e->getMessage()]);
            return response()->json(['ok'=>false,'error'=>'Server error: '.$e->getMessage()], 500);
        }
    }

    /* ============================================================
     |  AI CHECK  (kept compatible)
     * ============================================================*/
    public function aiCheck(Request $request)
    {
        if ($resp = $this->ensureRuntime()) { return $resp; }

        $request->validate([
            'text'=>'nullable|string',
            'url' =>'nullable|url',
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

    /* ============================================================
     |  TOPIC CLUSTER  (kept compatible)
     * ============================================================*/
    public function topicClusterAnalyze(Request $request)
    {
        if ($resp = $this->ensureRuntime()) { return $resp; }

        $request->validate([
            'text'  =>'nullable|string',
            'url'   =>'nullable|url',
            'top_k' =>'nullable|integer|min:5|max:50',
        ]);

        try {
            $text = trim((string)$request->input('text',''));
            if ($text==='' && $request->filled('url')) {
                $fetch = $this->fetchUrl((string)$request->input('url'));
                if ($fetch['ok']) { $text = $this->extractVisibleText($fetch['body']); }
            }
            if ($text==='') { return response()->json(['ok'=>false,'error'=>'No text found to analyze.'], 422); }

            $topK   = (int) $request->input('top_k', 20);
            $tokens = $this->tokenize($text);
            $freq   = [];
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

    /* ============================= Helpers ============================= */

    private function ensureRuntime(): ?\Illuminate\Http\JsonResponse
    {
        if (!function_exists('curl_init'))        { return response()->json(['ok'=>false,'error'=>'PHP cURL extension is disabled.'], 500); }
        if (!class_exists(\DOMDocument::class))   { return response()->json(['ok'=>false,'error'=>'PHP DOM/XML extension is disabled.'], 500); }
        if (!function_exists('mb_strlen'))        { return response()->json(['ok'=>false,'error'=>'PHP mbstring extension is disabled.'], 500); }
        return null;
    }

    private function fetchUrl(string $url, string $ua='Mozilla/5.0', int $timeout=15): array
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
        $body   = curl_exec($ch);
        $errNo  = curl_errno($ch);
        $errStr = curl_error($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($body === false) return ['ok'=>false,'error'=>"cURL error #$errNo: $errStr"];
        if ($status >= 400)   return ['ok'=>false,'error'=>"HTTP status $status"];
        return ['ok'=>true,'body'=>$body,'status'=>$status];
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

    private function getHost(string $url): ?string
    {
        $p = parse_url($url);
        return $p['host'] ?? null;
    }

    private function originOf(string $url): string
    {
        $p = parse_url($url);
        $scheme = $p['scheme'] ?? 'https';
        $host   = $p['host'] ?? '';
        $port   = isset($p['port']) ? (':'.$p['port']) : '';
        return $scheme.'://'.$host.$port;
    }

    private function absUrl(string $href, string $base): string
    {
        if (Str::startsWith($href, ['http://','https://'])) return $href;
        $p = parse_url($base);
        $scheme = $p['scheme'] ?? 'https';
        $host   = $p['host'] ?? '';
        if (Str::startsWith($href, ['/'])) return $scheme.'://'.$host.$href;
        $path = isset($p['path']) ? preg_replace('#/[^/]*$#','/', $p['path']) : '/';
        return $scheme.'://'.$host.$path.$href;
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

    private function hasNoindex(\DOMXPath $xp): bool
    {
        $m = $xp->query("//meta[translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='robots']/@content")->item(0);
        if (!$m) return false;
        $v = strtolower($m->nodeValue ?? '');
        return str_contains($v,'noindex');
    }

    private function detectFaqHint(\DOMDocument $dom): bool
    {
        $html = strtolower($dom->saveHTML() ?? '');
        return str_contains($html,'faq') || str_contains($html,'schema.org/faqpage');
    }

    private function detectAuthorOrDate(\DOMDocument $dom): bool
    {
        $html = strtolower($dom->saveHTML() ?? '');
        return str_contains($html,'itemprop="author"')
            || str_contains($html,'"author":')
            || str_contains($html,'rel="author"')
            || str_contains($html,'datetime=');
    }

    private function detectCTA(\DOMDocument $dom): bool
    {
        $html = strtolower($dom->saveHTML() ?? '');
        foreach (['sign up','subscribe','buy','contact','learn more','read more','get started','join','download'] as $kw) {
            if (str_contains($html, $kw)) return true;
        }
        return false;
    }

    private function jsonLdTypes(\DOMDocument $dom): array
    {
        $out = [];
        foreach ($dom->getElementsByTagName('script') as $s) {
            if (strtolower($s->getAttribute('type')) === 'application/ld+json') {
                $json = trim($s->nodeValue ?? '');
                $data = json_decode($json, true);
                if (json_last_error() !== JSON_ERROR_NONE) continue;
                $types = $this->collectTypes($data);
                $out = array_values(array_unique(array_merge($out, $types)));
            }
        }
        return $out;
    }

    private function collectTypes($node): array
    {
        $types = [];
        if (is_array($node)) {
            if (isset($node['@type'])) {
                $t = is_array($node['@type']) ? $node['@type'] : [$node['@type']];
                foreach ($t as $x) { $types[] = (string)$x; }
            }
            foreach ($node as $v) {
                if (is_array($v)) $types = array_merge($types, $this->collectTypes($v));
            }
        }
        return $types;
    }

    private function orgSameAs(\DOMDocument $dom): array
    {
        $out = [];
        foreach ($dom->getElementsByTagName('script') as $s) {
            if (strtolower($s->getAttribute('type')) === 'application/ld+json') {
                $json = trim($s->nodeValue ?? '');
                $data = json_decode($json, true);
                if (json_last_error() !== JSON_ERROR_NONE) continue;

                $stack = [$data];
                while ($stack) {
                    $cur = array_pop($stack);
                    if (!is_array($cur)) continue;
                    if (($cur['@type'] ?? null) === 'Organization' && isset($cur['sameAs'])) {
                        $sa = $cur['sameAs'];
                        $arr = is_array($sa) ? $sa : [$sa];
                        $out = array_merge($out, array_map('strval', $arr));
                    }
                    foreach ($cur as $v) if (is_array($v)) $stack[] = $v;
                }
            }
        }
        return array_values(array_unique($out));
    }

    private function slugScore(string $url): int
    {
        $p = parse_url($url);
        $path = trim($p['path'] ?? '/', '/');
        if ($path === '') return 80; // homepage
        $slug = basename($path);
        $len  = mb_strlen($slug);
        $hasHyphen = str_contains($slug, '-');
        if ($len>=3 && $len<=60 && $hasHyphen) return 100;
        if ($len>0 && $len<=75) return 80;
        return 55;
    }

    private function colorForScore(int $s): string
    {
        return $s>=80 ? 'green' : ($s>=60 ? 'orange' : 'red');
    }

    private function googleUrl(string $q): string
    {
        return 'https://www.google.com/search?q=' . rawurlencode($q);
    }

    /* -------------------- Topic/AI helpers -------------------- */

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
