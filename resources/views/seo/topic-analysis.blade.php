@extends('layouts.app')

@section('title', 'Topic Cluster Identification & Mapping')

@section('content')
<div class="max-w-5xl mx-auto p-6">
  <h1 class="text-2xl font-bold mb-4">Topic Cluster Identification & Mapping</h1>

  <div class="bg-white/70 dark:bg-gray-900/70 border border-gray-200 dark:border-gray-700 rounded-xl shadow-md p-6">
    <form method="POST" action="{{ route('seo.topic-clusters.store') }}">
      @csrf

      <label for="urls" class="block font-medium mb-2">Website URLs (one per line)</label>
      <textarea id="urls" name="urls" rows="8" required
        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 p-3"
        placeholder="https://example.com/page-1
https://example.com/blog/post-2">{{ old('urls') }}</textarea>
      @error('urls')
        <p class="text-red-600 mt-1 text-sm">{{ $message }}</p>
      @enderror

      <div class="mt-4">
        <label for="num_clusters" class="block font-medium mb-2">Number of clusters (optional)</label>
        <input type="number" min="2" max="12" name="num_clusters" id="num_clusters" value="{{ old('num_clusters', 5) }}"
          class="w-32 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 p-2" />
      </div>

      <button type="submit"
        class="mt-6 inline-flex items-center px-5 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
        Analyze
      </button>
    </form>
  </div>

  <p class="text-sm text-gray-500 mt-4">
    Tip: Include a mix of core pages and key articles to get sharper clusters.
  </p>
</div>
@endsection
