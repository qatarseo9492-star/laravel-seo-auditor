<?php

namespace App\Http\Controllers\Seo;

use App\Http\Controllers\Controller;
use App\Models\TopicAnalysis;
use App\Services\TopicClusterService;
use Illuminate\Http\Request;

class TopicAnalysisController extends Controller
{
    public function create()
    {
        return view('seo.topic-analysis');
    }

    public function store(Request $request, TopicClusterService $service)
    {
        $data = $request->validate([
            'urls' => ['required', 'string', 'min:5'],
            'num_clusters' => ['nullable', 'integer', 'min:2', 'max:12'],
        ]);

        $urls = preg_split('/\r\n|\r|\n/', $data['urls']);
        $urls = array_values(array_unique(array_filter(array_map('trim', $urls)))));
        if (empty($urls)) {
            return back()->withErrors(['urls' => 'Please provide at least one valid URL.'])->withInput();
        }

        $numClusters = (int)($data['num_clusters'] ?? 5);

        // Dedupe exact list by signature
        $signature = TopicClusterService::signatureForUrls($urls);
        $existing = TopicAnalysis::where('urls_signature', $signature)->latest()->first();

        if ($existing) {
            // Redirect to results page (route closure) for cached result
            return redirect()->route('seo.topic-clusters.results', ['analysis' => $existing->id]);
        }

        // Run fresh analysis
        $out = $service->generateClusters($urls, $numClusters);

        // Persist
        $analysis = TopicAnalysis::create([
            'urls_list'       => $urls,
            'urls_signature'  => $signature,
            'analysis_result' => $out['result'],
            'openai_metadata' => $out['openai_meta'],
        ]);

        // Redirect to results
        return redirect()->route('seo.topic-clusters.results', ['analysis' => $analysis->id]);
    }
}
