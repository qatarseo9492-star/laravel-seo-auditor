<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SeoOptimizerController extends Controller
{
    /**
     * Show the SEO Content Optimizer form.
     */
    public function index()
    {
        return view('seo.optimizer'); // Blade file: resources/views/seo/optimizer.blade.php
    }

    /**
     * Analyze given URL + keyword and provide optimization suggestions.
     */
    public function analyze(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'keyword' => 'required|string'
        ]);

        $url = $request->input('url');
        $keyword = strtolower($request->input('keyword'));

        // ⚡ Starter placeholder suggestions
        $suggestions = [
            "Include '{$keyword}' in the <title> tag of $url if missing.",
            "Use '{$keyword}' at least once within an <h2> heading.",
            "Add the keyword '{$keyword}' 2-3 more times in the body while keeping it natural.",
            "Add an image with alt text containing '{$keyword}'.",
            "Ensure meta description contains '{$keyword}' naturally."
        ];

        return view('seo.optimizer-results', compact('url', 'keyword', 'suggestions'));
    }
}
