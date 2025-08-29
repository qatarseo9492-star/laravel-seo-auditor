<?php

namespace App\Services;

class ReadabilityService
{
    /** Unicode-aware sentence splitter (simple + robust). */
    protected function splitSentences(string $text): array
    {
        $text = preg_replace('/\s+/u', ' ', trim($text));
        if ($text === '') return [];
        // break on ., !, ? and their CJK equivalents
        $text = preg_replace('/([.!?。！？])\s+/u', "$1\n", $text);
        $parts = preg_split("/\n+/u", $text) ?: [];
        return array_values(array_filter(array_map('trim', $parts), fn($s) => $s !== ''));
    }

    /** Unicode word tokenizer: sequences of letters (any script) + apostrophes. */
    protected function tokenizeWords(string $text): array
    {
        preg_match_all('/[\p{L}\']+/u', mb_strtolower($text, 'UTF-8'), $m);
        return $m[0] ?? [];
    }

    /** Crude script detection by Unicode blocks (fast & good enough for scoring path). */
    protected function detectScript(string $text): string
    {
        if (preg_match('/\p{Han}|\p{Katakana}|\p{Hiragana}/u', $text)) return 'CJK';
        if (preg_match('/\p{Arabic}/u', $text))  return 'ARABIC';
        if (preg_match('/\p{Cyrillic}/u', $text))return 'CYRILLIC';
        if (preg_match('/\p{Greek}/u', $text))   return 'GREEK';
        return 'LATIN';
    }

    /** English syllable estimator (LATIN only). */
    protected function countSyllables(string $word): int
    {
        $w = strtolower(preg_replace('/[^a-z]/', '', $word));
        if ($w === '') return 0;
        preg_match_all('/[aeiouy]+/', $w, $m);
        $count = count($m[0]);
        if (strlen($w) > 2 && substr($w, -1) === 'e' && !preg_match('/[aeiouy][^aeiouy]e$/', $w)) $count--;
        if (preg_match('/[^aeiouy]le$/', $w)) $count++;
        return max(1, $count);
    }

    /** Lexical Type-Token Ratio and tri-gram repetition % */
    protected function ttrAndTrigram(array $words): array
    {
        $w = max(1, count($words));
        $ttr = count(array_unique($words)) / $w;

        $tri = []; $repeated = 0;
        for ($i=0; $i < $w-2; $i++) {
            $g = $words[$i].' '.$words[$i+1].' '.$words[$i+2];
            $tri[$g] = ($tri[$g] ?? 0) + 1;
        }
        foreach ($tri as $c) if ($c > 1) $repeated += $c - 1;
        $triRatio = $w > 0 ? min(1, $repeated / max(1, $w/3)) : 0;

        return [$ttr, $triRatio];
    }

    /** Public API: pass raw page text; returns scoring + metrics for UI. */
    public function analyze(string $rawText): array
    {
        $text = trim($rawText);
        if ($text === '') {
            return [
                'language' => 'LATIN', 'score' => 0, 'band' => 'red', 'badge' => 'Needs Improvement in Content',
                'grade' => null, 'flesch' => null, 'fk_grade' => null, 'smog' => null, 'gunning_fog' => null,
                'coleman_liau' => null, 'ari' => null, 'avg_sentence_len' => null, 'syllables_per_word' => null,
                'words' => 0, 'lexical_ttr' => 0, 'trigram_repetition' => 0, 'digits_per_100' => 0,
                'simple_fixes' => ['We could not read any content on the page.'],
                'note' => 'No content captured.'
            ];
        }

        $script = $this->detectScript($text);
        $sentences = $this->splitSentences($text);
        $sCount = max(1, count($sentences));
        $words   = $this->tokenizeWords($text);
        $wCount  = max(1, count($words));

        $digits = preg_match_all('/\p{N}/u', $text);
        $digitsPer100 = (int) round(($digits / $wCount) * 100);

        [$ttr, $triRep] = $this->ttrAndTrigram($words);
        $asl = $wCount / $sCount;

        $isLatin = ($script === 'LATIN');
        $flesch = $fk = $smog = $fog = $cli = $ari = null;
        $asw = null; $poly = 0; $letters = 0; $chars = 0;

        if ($isLatin) {
            $syll = 0;
            foreach ($words as $w) {
                $letters += preg_match_all('/[a-z]/i', $w);
                $chars   += strlen($w);
                $sy = $this->countSyllables($w);
                $syll += $sy;
                if ($sy >= 3) $poly++;
            }
            $asw   = $syll / $wCount;
            $flesch= 206.835 - 1.015 * $asl - 84.6 * $asw;
            $fk    = 0.39 * $asl + 11.8 * $asw - 15.59;
            $smog  = 1.0430 * sqrt($poly * (30.0 / $sCount)) + 3.1291;
            $fog   = 0.4 * ($asl + 100.0 * ($poly / $wCount));
            $L     = ($letters / $wCount) * 100.0;
            $S     = ($sCount  / $wCount) * 100.0;
            $cli   = 0.0588 * $L - 0.296 * $S - 15.8;
            $ari   = 4.71 * ($chars / $wCount) + 0.5 * $asl - 21.43;

            $avgGrade = max(1, min(18, ($fk + $smog + $fog + $cli + $ari) / 5.0));
            $fre = max(0, min(100, $flesch));
            $gradeScore = max(0, min(100, 100 - (($avgGrade - 1) * (100.0 / 17.0))));
            $score = (int) round(($fre + $gradeScore) / 2);
            $grade = round($avgGrade, 1);
        } else {
            // LIX fallback (language-independent) → map to 0..100
            $long = 0; foreach ($words as $w) if (mb_strlen($w, 'UTF-8') > 6) $long++;
            $lix = ($wCount / $sCount) + (($long * 100.0) / $wCount);

            $pairs = [[20,95],[30,85],[40,70],[50,55],[60,40],[80,20],[100,5]];
            $score = 70;
            for ($i=0; $i<count($pairs)-1; $i++){
                [$x1,$y1] = $pairs[$i]; [$x2,$y2] = $pairs[$i+1];
                if ($lix <= $x2) { $t = ($lix - $x1)/max(0.0001,($x2-$x1)); $score = $y1 + $t*($y2-$y1); break; }
            }
            $score = (int) round(max(0,min(100,$score)));
            $grade = round(min(18, max(1, ($lix - 10) * (18/30))), 1);
        }

        preg_match_all('/\b(is|are|was|were|be|been|being)\s+[a-z]+ed\b/i', $text, $pv);
        $passiveHits  = count($pv[0] ?? []);
        $passiveRatio = min(100, round(($passiveHits / $sCount) * 100));

        $badge = $score >= 80 ? 'Very Easy To Read'
              : ($score >= 60 ? 'Good — Needs More Improvement'
                              : 'Needs Improvement in Content');
        $band  = $score >= 80 ? 'green' : ($score >= 60 ? 'orange' : 'red');

        $fixes = [];
        if ($asl > 16) $fixes[] = 'Break long sentences into 12–16 words.';
        if ($isLatin && $flesch !== null && $flesch < 60) $fixes[] = 'Prefer shorter words (use simpler synonyms).';
        if ($digitsPer100 > 10) $fixes[] = 'Reduce numeric density; round or group numbers where possible.';
        if ($triRep > 0.05) $fixes[] = 'Reduce phrase repetition; vary wording and structure.';
        if ($passiveRatio > 20) $fixes[] = 'Reduce passive voice where possible.';
        if (empty($fixes)) $fixes[] = 'Nice! Metrics look solid — keep sentences concise and vocabulary familiar.';

        return [
            'language'            => $script,
            'score'               => (int)$score,
            'band'                => $band,
            'badge'               => $badge,
            'grade'               => $grade,
            'flesch'              => $flesch !== null ? round($flesch,1) : null,
            'fk_grade'            => $fk    !== null ? round($fk,1) : null,
            'smog'                => $smog  !== null ? round($smog,1) : null,
            'gunning_fog'         => $fog   !== null ? round($fog,1) : null,
            'coleman_liau'        => $cli   !== null ? round($cli,1) : null,
            'ari'                 => $ari   !== null ? round($ari,1) : null,
            'avg_sentence_len'    => round($asl,2),
            'syllables_per_word'  => isset($asw) ? round($asw,2) : null,
            'words'               => (int)$wCount,
            'lexical_ttr'         => round($ttr*100, 0),
            'trigram_repetition'  => round($triRep*100, 0),
            'digits_per_100'      => $digitsPer100,
            'simple_fixes'        => $fixes,
            'note'                => $isLatin
                ? 'Score blends Flesch and grade-level; non-Latin texts use LIX mapping.'
                : 'Score uses LIX (language independent), scaled to 0–100.',
        ];
    }
}
