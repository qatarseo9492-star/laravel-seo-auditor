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
        $titleNode = $xp->query('//title')->item
