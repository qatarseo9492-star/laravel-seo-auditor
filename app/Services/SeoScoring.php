<?php

namespace App\Services;

class SeoScoring
{
    /** Main entry — returns a RankMath/Seobility-style payload */
    public function analyze(string $html, string $url, ?string $target = null): array
    {
        $target = trim((string)$target);
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $xp = new \DOMXPath($dom);

        // ---------- Raw facts we’ll reuse ----------
        $facts = $this->collectFacts($xp, $html, $url, $target);

        // ---------- Checks (each returns id,label,score,value,advice) ----------
        $checks = [];
        $add = function ($arr) use (&$checks) { $checks[] = $arr; };

        // Title
        $add($this->checkTitle($facts['title'], $target));

        // Meta description
        $add($this->checkMetaDescription($facts['meta_desc'], $target));

        // Canonical
        $add($this->checkCanonical($facts['canonical'], $url));

        // Robots meta
        $add($this->checkRobotsMeta($facts['robots']));

        // OpenGraph essentials
        $add($this->checkOpenGraph($facts['og']));

        // Viewport for mobile
        $add($this->checkViewport($facts['viewport']));

        // H1 / headings structure
        $add($this->checkH1($facts['h1'], $target));
        $add($this->checkHeadingHierarchy($facts['heading_levels']));

        // Keyword presence early
        $add($this->checkKeywordFirstParagraph($facts['first100'], $target));

        // Images alt coverage
        $add($this->checkImageAltRatio($facts['image_count'], $facts['image_alt_missing']));

        // Link signals
        $add($this->checkLinks($facts['links']));

        // JSON-LD / schema presence
        $add($this->checkSchema($facts['has_jsonld']));

        // Text/HTML ratio
        $add($this->checkTextHtmlRatio($facts['text_to_html_ratio']));

        // Readability (Flesch)
        $add($this->checkReadability($facts['flesch']));

        // Page object counts (perf proxy)
        $add($this->checkPageObjects($facts['assets']));

        // ---------- Category scores (weighted) ----------
        // Each check declares a category; compute weighted totals
        $weights = [
            'content'   => 0.30,  // Titles, meta, keyword usage, readability
            'structure' => 0.20,  // Headings, anchors, hierarchy
            'technical' => 0.30,  // Canonical, robots, OG, schema, ratio
            'ux'        => 0.20,  // Mobile viewport, page objects proxy, links balance
        ];

        $byCat = ['content'=>[],'structure'=>[],'technical'=>[],'ux'=>[]];
        foreach ($checks as $c) { $byCat[$c['category']][] = $c['score']; }

        $catScores = array_map(function($arr){
            return count($arr) ? (int) round(array_sum($arr) / count($arr)) : 0;
        }, $byCat);

        $overall = (int) round(
            $catScores['content']   * $weights['content'] +
            $catScores['structure'] * $weights['structure'] +
            $catScores['technical'] * $weights['technical'] +
            $catScores['ux']        * $weights['ux']
        );

        // Badge text (like you wanted)
        $badge = $overall >= 80
            ? ['type'=>'success','text'=>'Great Work — Well Optimized']
            : ($overall >= 60
                ? ['type'=>'warn','text'=>'Needs Optimization']
                : ['type'=>'danger','text'=>'Needs Significant Optimization']);

        return [
            'ok'            => true,
            'overall_score' => $overall,
            'badge'         => $badge,
            'categories'    => $catScores,
            'facts'         => $facts,    // handy for UI “Quick Stats”
            'checks'        => $checks,   // drive your color-coded checklist
        ];
    }

    // =========================================================
    // Facts extraction
    // =========================================================
    private function collectFacts(\DOMXPath $xp, string $html, string $url, string $target): array
    {
        $titleNode = $xp->query('//title')->item(0);
        $title = $titleNode ? trim($titleNode->textContent) : '';

        $metaNode = $xp->query("//meta[translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='description']/@content")->item(0);
        $meta = $metaNode ? trim($metaNode->nodeValue) : '';

        $robots = $this->attr($xp, "//meta[translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='robots']", 'content');
        $canonical = $this->attr($xp, "//link[translate(@rel,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='canonical']", 'href');

        $viewport = $this->attr($xp, "//meta[translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='viewport']", 'content');

        $og = [
            'title'       => $this->attr($xp, "//meta[translate(@property,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='og:title']", 'content'),
            'description' => $this->attr($xp, "//meta[translate(@property,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='og:description']", 'content'),
            'image'       => $this->attr($xp, "//meta[translate(@property,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='og:image']", 'content'),
        ];

        // Headings
        $headings = [
            'h1' => $this->texts($xp, '//h1'),
            'h2' => $this->texts($xp, '//h2'),
            'h3' => $this->texts($xp, '//h3'),
            'h4' => $this->texts($xp, '//h4'),
            'h5' => $this->texts($xp, '//h5'),
            'h6' => $this->texts($xp, '//h6'),
        ];
        $levels = [];
        foreach (['h1','h2','h3','h4','h5','h6'] as $k) if (!empty($headings[$k])) $levels[] = (int)substr($k,1);
        sort($levels);

        // Links & assets
        $anchors = $xp->query('//a[@href]');
        $internal = 0; $external = 0; $anchorList = [];
        $host = parse_url($url, PHP_URL_HOST) ?: '';
        foreach ($anchors as $a) {
            $href = trim($a->getAttribute('href'));
            $text = trim(preg_replace('/\s+/',' ', $a->textContent));
            if ($href === '' || str_starts_with($href, 'mailto:') || str_starts_with($href, 'tel:')) continue;
            $isAbs = preg_match('#^https?://#i', $href);
            $isExt = $isAbs && (parse_url($href, PHP_URL_HOST) !== $host);
            $isExt ? $external++ : $internal++;
            $anchorList[] = ['text'=>$text, 'href'=>$href, 'type'=>$isExt?'external':'internal'];
        }

        $imgNodes = $xp->query('//img');
        $imgTotal = $imgNodes->length;
        $imgNoAlt = 0;
        foreach ($imgNodes as $img) {
            $alt = trim((string)$img->getAttribute('alt'));
            if ($alt === '') $imgNoAlt++;
        }

        // Assets (quick perf proxy)
        $assets = [
            'scripts' => $xp->query('//script')->length,
            'styles'  => $xp->query("//link[translate(@rel,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='stylesheet']")->length,
            'images'  => $imgTotal,
        ];

        // Readability & text/html ratio
        $text = $this->visibleText($xp);
        [$wc,$sc,$syll] = $this->basicCounts($text);
        $flesch = $this->flesch($wc, $sc, $syll);
        $ratio = $this->textHtmlRatio($text, $html);

        // First 100 words
        $first100 = trim(implode(' ', array_slice(preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY), 0, 100)));

        // JSON-LD presence
        $hasJsonLd = $xp->query("//script[@type='application/ld+json']")->length > 0;

        return [
            'title'               => $title,
            'meta_desc'           => $meta,
            'robots'              => $robots,
            'canonical'           => $canonical,
            'viewport'            => $viewport,
            'og'                  => $og,
            'headings'            => $headings,
            'heading_levels'      => $levels,
            'h1'                  => $headings['h1'],
            'links'               => ['internal'=>$internal,'external'=>$external,'anchors'=>$anchorList],
            'image_count'         => $imgTotal,
            'image_alt_missing'   => $imgNoAlt,
            'assets'              => $assets,
            'text_to_html_ratio'  => $ratio,
            'flesch'              => $flesch,
            'first100'            => $first100,
            'has_jsonld'          => $hasJsonLd,
        ];
    }

    private function attr(\DOMXPath $xp, string $xpath, string $name): ?string
    {
        $n = $xp->query($xpath)->item(0);
        return $n ? trim((string)$n->getAttribute($name)) : null;
    }
    private function texts(\DOMXPath $xp, string $xpath): array
    {
        $list = [];
        foreach ($xp->query($xpath) as $n) {
            $list[] = trim(preg_replace('/\s+/', ' ', $n->textContent));
        }
        return $list;
    }
    private function visibleText(\DOMXPath $xp): string
    {
        // strip script/style/noscript
        $nodes = $xp->query('//body//*[not(self::script or self::style or self::noscript)]');
        $parts = [];
        foreach ($nodes as $n) {
            $t = trim($n->textContent);
            if ($t !== '') $parts[] = $t;
        }
        $text = preg_replace('/\s+/',' ', implode(' ', $parts) ?: '');
        return trim($text);
    }
    private function basicCounts(string $text): array
    {
        if ($text === '') return [0,0,0];
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        $wc = count($words);
        $sc = max(1, preg_match_all('/[.!?]+/', $text));
        $syll = 0;
        foreach ($words as $w) $syll += $this->syllables($w);
        return [$wc,$sc,$syll];
    }
    private function syllables(string $word): int
    {
        $w = strtolower(preg_replace('/[^a-z]/', '', $word));
        if ($w === '') return 0;
        $w = preg_replace('/e$/', '', $w);
        $cnt = preg_match_all('/[aeiouy]+/', $w);
        return max(1, (int)$cnt);
    }
    private function flesch(int $wc, int $sc, int $syll): int
    {
        if ($wc === 0 || $sc === 0) return 0;
        $score = 206.835 - 1.015 * ($wc / $sc) - 84.6 * ($syll / $wc);
        return (int)round(max(0, min(100, $score)));
    }
    private function textHtmlRatio(string $text, string $html): float
    {
        $t = strlen($text);
        $h = max(1, strlen($html));
        return round(($t / $h) * 100, 2);
    }

    // =========================================================
    // Individual checks (each returns: id,label,category,score,value,advice)
    // =========================================================
    private function pack(string $id, string $label, string $cat, int $score, $value = null, string $advice = ''): array
    {
        return compact('id','label','category','score','value','advice');
    }

    private function checkTitle(?string $title, string $target): array
    {
        $len = strlen((string)$title);
        $score = 0;
        if ($len === 0) {
            $advice = 'Add a descriptive title (50–60 chars). Include the primary keyword near the front.';
        } else {
            $lenScore = ($len >= 50 && $len <= 60) ? 100 : (($len >= 30 && $len <= 70) ? 80 : 50);
            $kwBonus = ($target && stripos($title, $target) !== false) ? 20 : 0;
            $score = min(100, $lenScore + $kwBonus);
            $advice = 'Aim for ~55 chars and keep the key phrase early. Make it compelling.';
        }
        return $this->pack('title', 'Title Tag', 'content', $score, ['length'=>$len, 'text'=>$title], $advice);
    }

    private function checkMetaDescription(?string $meta, string $target): array
    {
        $len = strlen((string)$meta);
        if ($len === 0) {
            return $this->pack('meta', 'Meta Description', 'content', 30, null,
                'Add a 140–160 character meta description with a value proposition and CTA.'
            );
        }
        $lenScore = ($len >= 140 && $len <= 160) ? 100 : (($len >= 120 && $len <= 180) ? 80 : 60);
        $kwBonus = ($target && stripos($meta, $target) !== false) ? 10 : 0;
        $score = min(100, $lenScore + $kwBonus);
        return $this->pack('meta', 'Meta Description', 'content', $score, ['length'=>$len], 'Tweak length and include secondary phrases naturally.');
    }

    private function checkCanonical(?string $canonical, string $url): array
    {
        if (!$canonical) {
            return $this->pack('canonical','Canonical Tag','technical',60,null,'Add a self-referencing canonical to avoid duplicates.');
        }
        $self = rtrim($canonical,'/') === rtrim($url,'/');
        return $this->pack('canonical','Canonical Tag','technical', $self?100:70, ['href'=>$canonical],
            $self ? 'Looks good.' : 'Point canonical to the current URL unless this page is a known alternate.'
        );
    }

    private function checkRobotsMeta(?string $robots): array
    {
        $robots = strtolower((string)$robots);
        if ($robots === '') return $this->pack('robots','Robots Meta','technical', 90, null, 'No robots meta is fine (defaults to index,follow).');
        if (str_contains($robots, 'noindex')) return $this->pack('robots','Robots Meta','technical', 10, $robots, 'Remove noindex if the page should rank.');
        return $this->pack('robots','Robots Meta','technical', 90, $robots, 'Indexable.');
    }

    private function checkOpenGraph(array $og): array
    {
        $need = ['title','description','image'];
        $have = 0;
        foreach ($need as $k) if (!empty($og[$k])) $have++;
        $score = [0=>30,1=>55,2=>80,3=>100][$have];
        return $this->pack('og','OpenGraph Essentials','technical', $score, $og,
            $have===3 ? 'OG tags complete.' : 'Add missing OG tags (title, description, image) for better previews.'
        );
    }

    private function checkViewport(?string $vp): array
    {
        if (!$vp) return $this->pack('viewport','Mobile Viewport','ux',50,null,'Add `<meta name="viewport" content="width=device-width, initial-scale=1">`.');
        $ok = (bool)preg_match('/width\s*=\s*device-width/i', $vp);
        return $this->pack('viewport','Mobile Viewport','ux', $ok?100:80, $vp, $ok?'Responsive ready.':'Tweak viewport to `width=device-width, initial-scale=1`.');
    }

    private function checkH1(array $h1, string $target): array
    {
        $count = count($h1);
        if ($count === 0) return $this->pack('h1','H1 Tag','structure',40,null,'Add one H1 that matches search intent.');
        if ($count > 1)  return $this->pack('h1','H1 Tag','structure',70,$h1,'Keep a single H1.');
        $score = 90 + (($target && stripos($h1[0], $target) !== false) ? 10 : 0);
        return $this->pack('h1','H1 Tag','structure',$score,$h1[0], 'Great. Make it natural and close to the title.');
    }

    private function checkHeadingHierarchy(array $levels): array
    {
        if (!$levels) return $this->pack('hier','Heading Hierarchy','structure',60,null,'Use H2 for sections and H3 for sub-sections.');
        // detect skips (e.g., H1 -> H3)
        $ok = true;
        for ($i=1;$i<count($levels);$i++) { if ($levels[$i]-$levels[$i-1] > 1) { $ok=false; break; } }
        return $this->pack('hier','Heading Hierarchy','structure', $ok?95:75, $levels,
            $ok?'Looks consistent.':'Avoid skipping levels (H1→H2→H3).'
        );
    }

    private function checkKeywordFirstParagraph(string $first100, string $target): array
    {
        if ($target === '') return $this->pack('kwfirst','Keyword in Intro','content',80,null,'Set a primary keyword to evaluate.');
        $hit = stripos($first100, $target) !== false;
        return $this->pack('kwfirst','Keyword in Intro','content', $hit?100:70, null,
            $hit?'Good coverage in the opening.':'Mention the primary phrase naturally in the first paragraph.'
        );
    }

    private function checkImageAltRatio(int $total, int $missing): array
    {
        if ($total === 0) return $this->pack('imgalt','Image ALT Attributes','technical',90,['total'=>0,'missing'=>0],'No images found — OK.');
        $ok = (int) round((($total-$missing)/max(1,$total))*100);
        $advice = $ok===100 ? 'All images have ALT text.' : 'Add descriptive ALT text (use keywords only when relevant).';
        return $this->pack('imgalt','Image ALT Attributes','technical',$ok,['total'=>$total,'missing'=>$missing],$advice);
    }

    private function checkLinks(array $links): array
    {
        $int = (int)$links['internal']; $ext = (int)$links['external'];
        // Simple balance rule of thumb
        $score = 70;
        if ($int >= 5) $score += 15;
        if ($ext >= 1 && $ext <= 25) $score += 15;
        $score = min(100, $score);
        return $this->pack('links','Links (internal/external)','ux',$score, ['internal'=>$int,'external'=>$ext],
            'Use descriptive anchors; link to hub/related pages and at least one authoritative external source.'
        );
    }

    private function checkSchema(bool $has): array
    {
        return $this->pack('schema','Structured Data (JSON-LD)','technical', $has?100:70, ['json_ld'=>$has],
            $has?'Schema detected.':'Add appropriate schema (Article, FAQ, Product, etc.) validated with Rich Results Test.'
        );
    }

    private function checkTextHtmlRatio(float $ratio): array
    {
        // Good content pages usually ~10–30% text/HTML (very rough proxy)
        $score = ($ratio >= 10 && $ratio <= 35) ? 95 : (($ratio >= 6 && $ratio <= 45) ? 80 : 60);
        return $this->pack('ratio','Text/HTML Ratio','technical',$score,$ratio,
            'Keep markup lean and ensure sufficient body copy.'
        );
    }

    private function checkReadability(int $flesch): array
    {
        // Map Flesch→score directly (0–100)
        $advice = $flesch >= 60
            ? 'Easy to read. Keep sentences concise.'
            : 'Shorten sentences, prefer simpler words, add headings & lists.';
        return $this->pack('readability','Readability (Flesch)','content',$flesch,$flesch,$advice);
    }

    private function checkPageObjects(array $assets): array
    {
        $total = (int)$assets['scripts'] + (int)$assets['styles'] + (int)$assets['images'];
        // Soft encouragement to keep requests modest
        $score = $total <= 25 ? 95 : ($total <= 45 ? 80 : 60);
        return $this->pack('objects','Page Objects (scripts/styles/images)','ux',$score,$assets,
            'Reduce unused JS/CSS and lazy-load media where possible.'
        );
    }
}
