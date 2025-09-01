<?php

namespace App\Http\Controllers\Seo;

use App\Http\Controllers\Controller;
use App\Models\TopicAnalysis;
use App\Services\TopicClusterService;
use Illuminate\Http\Request;

class TopicAnalysisController extends Controller
{
    /**
     * Show the Topic Cluster form.
     */
    public function create()
    {
        // View: resources/views/seo/topic-analysis.blade.php
        return view('seo.topic-analysis');
    }

    /**
     * Handle form submission:
     *  - Validate
     *  - Check cached analysis (by signature)
     *  - Generate (via TopicClusterService) if not cached
     *  - Persist and redirect to results
     */
    public function store(Request $request, TopicClusterService $service)
    {
        $data = $request->validate([
            'urls' => ['required', 'string', 'min:5'],
            'num_clusters' => ['nullable', 'integer', 'min:2', 'max:12'],
        ]);

        // Split by lines, trim, dedupe
        $urls = preg_split('/\r\n|\r|\n/', $data['urls'] ?? '');
        $urls = array_values(
            array_unique(
                array_filter(
                    array_map('trim', $urls)
                )
            )
        );

        if (empty($urls)) {
            return back()
                ->withErrors(['urls' => 'Please provide at least one valid URL.'])
                ->withInput();
        }

        $numClusters = (int) ($data['num_clusters'] ?? 5);

        // Signature for this exact set of URLs
        $signature = TopicClusterService::signatureForUrls($urls);

        // Return cached result if exists
        $existing = TopicAnalysis::where('urls_signature', $signature)->latest('id')->first();
        if ($existing) {
            return redirect()->route('seo.topic-clusters.results', ['analysis' => $existing->id]);
        }

        // Generate via service
        $out = $service->generateClusters($urls, $numClusters);

        // Persist
        $analysis = TopicAnalysis::create([
            'urls_list'       => $urls,
            'urls_signature'  => $signature,
            'analysis_result' => $out['result'] ?? ['clusters' => []],
            'openai_metadata' => $out['openai_meta'] ?? [],
        ]);

        // Redirect to results
        return redirect()->route('seo.topic-clusters.results', ['analysis' => $analysis->id]);
    }
}
