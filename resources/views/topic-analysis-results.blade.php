@extends('layouts.app')

@section('title', 'Topic Cluster Results')

@section('content')
@php
  $clusters = data_get($analysis->analysis_result, 'clusters', []);
@endphp

<div class="max-w-6xl mx-auto p-6">
  <div class="flex items-baseline justify-between mb-4">
    <h1 class="text-2xl font-bold">Topic Cluster Results</h1>
    <a href="{{ route('seo.topic-clusters.create') }}" class="text-indigo-600 hover:underline">Run another</a>
  </div>

  <div class="grid gap-6 md:grid-cols-2">
    @forelse($clusters as $cluster)
      <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white/70 dark:bg-gray-900/70 shadow-lg p-5">
        <h2 class="text-xl font-semibold mb-2">
          {{ data_get($cluster, 'name', 'Untitled Cluster') }}
        </h2>
        <p class="text-gray-700 dark:text-gray-300 mb-3">
          {{ data_get($cluster, 'description') }}
        </p>

        <div class="mb-3">
          <h3 class="font-medium mb-1">Top Keywords</h3>
          <div class="flex flex-wrap gap-2">
            @foreach((array) data_get($cluster, 'top_keywords', []) as $kw)
              <span class="text-sm px-2 py-1 rounded-full border border-gray-300 dark:border-gray-600">
                {{ $kw }}
              </span>
            @endforeach
          </div>
        </div>

        <div>
          <h3 class="font-medium mb-1">Member URLs</h3>
          <ul class="list-disc list-inside space-y-1">
            @foreach((array) data_get($cluster, 'member_urls', []) as $u)
              <li><a class="text-indigo-600 hover:underline break-all" href="{{ $u }}" target="_blank" rel="noopener">{{ $u }}</a></li>
            @endforeach
          </ul>
        </div>
      </div>
    @empty
      <div class="col-span-full text-gray-600 dark:text-gray-300">
        No clusters were returned. Try running the analysis again with more URLs.
      </div>
    @endforelse
  </div>

  <div class="mt-8 text-sm text-gray-500">
    <p><strong>Analyzed URLs:</strong> {{ implode(', ', (array) $analysis->urls_list) }}</p>
    <details class="mt-2">
      <summary class="cursor-pointer">OpenAI metadata</summary>
      <pre class="mt-2 p-3 bg-gray-100 dark:bg-gray-800 rounded-md overflow-x-auto text-xs">{{ json_encode($analysis->openai_metadata, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre>
    </details>
  </div>
</div>
@endsection
