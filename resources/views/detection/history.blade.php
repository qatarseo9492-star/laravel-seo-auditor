@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
  <h1 class="text-2xl font-bold mb-6">Detection History</h1>

  <table class="min-w-full text-sm">
    <thead>
      <tr class="border-b border-white/10">
        <th class="text-left py-2">ID</th>
        <th class="text-left py-2">Score</th>
        <th class="text-left py-2">Confidence</th>
        <th class="text-left py-2">Verdict</th>
        <th class="text-left py-2">Excerpt</th>
        <th class="text-left py-2">Created</th>
      </tr>
    </thead>
    <tbody>
      @foreach($items as $row)
      <tr class="border-b border-white/5">
        <td class="py-2">{{ $row->id }}</td>
        <td class="py-2">{{ number_format($row->ai_score * 100, 1) }}%</td>
        <td class="py-2">{{ number_format($row->confidence * 100, 0) }}%</td>
        <td class="py-2">{{ $row->verdict }}</td>
        <td class="py-2">{{ \Illuminate\Support\Str::limit($row->content_plain, 80) }}</td>
        <td class="py-2">{{ $row->created_at->diffForHumans() }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <div class="mt-6">
    {{ $items->links() }}
  </div>
</div>
@endsection
