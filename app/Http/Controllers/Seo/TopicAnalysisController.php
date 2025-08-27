<?php

namespace App\Http\Controllers\Seo;

use App\Http\Controllers\Controller;
use App\Models\TopicAnalysis;
use App\Services\TopicClusterService;
use Illuminate\Http\Request;

class TopicAnalysisController extends Controller
{
    /**
     * Show the input form.
     */
    public function create()
    {
        return view('seo.topic-analysis');
    }

    /**
     * Process the form: validate -> check cache -> analyze -> save -> redirect to results.
     */
    public function store(Request $request, TopicClusterService $service)
    {
        $data = $request->validate([
            'urls' => ['required', 'string', 'min:5'],
            'num_clusters' => ['nullable', 'integer', 'min:2', 'max:12'],
        ]);

        // Split lines into distinct URLs
        $urls = preg_split('/\r\n|\r|\n/', $data['urls']);
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

        $numClusters = (int)($data['num_clusters'] ?? 5);

        // Dedupe exact list by signature and check cache
        $signature = TopicClusterService::signatureForUrls($urls);
        $existing = TopicAnalysis::where('urls_signature', $signature)->latest()->first();

        if ($existing) {
            return redirect()->route('seo.topic-clusters.results', ['analysis' => $existing->id]);
        }

        // Fresh analysis via service
        $out = $service->generateClusters($urls, $numClusters);

        // Save to DB
        $analysis = TopicAnalysis::create([
            'urls_list'       => $urls,
            'urls_signature'  => $signature,
            'analysis_result' => $out['result'],
            'openai_metadata' => $out['openai_meta'] ?? [],
        ]);

        // Redirect to results page
        return redirect()->route('seo.topic-clusters.results', ['analysis' => $analysis->id]);
    }
}
