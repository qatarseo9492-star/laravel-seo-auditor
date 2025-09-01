<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KeywordAnalysisController extends Controller
{
    /**
     * Show the keyword analysis input form.
     */
    public function index()
    {
        return view('seo.keyword-analysis');
    }

    /**
     * Process the keyword analysis.
     */
    public function analyze(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'keyword' => 'required|string'
        ]);

        $content = $request->input('content');
        $keyword = strtolower($request->input('keyword'));

        $count = substr_count(strtolower($content), $keyword);

        $results = [
            'keyword' => $keyword,
            'count'   => $count,
            'density' => round(($count / str_word_count($content)) * 100, 2)
        ];

        return view('seo.keyword-results', compact('results', 'content'));
    }
}
