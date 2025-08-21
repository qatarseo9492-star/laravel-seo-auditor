   <!DOCTYPE html>
   <html lang="en">
   <head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>SEO Auditor</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
   </head>
   <body class="bg-light">
   <div class="container my-5">
       <h1 class="mb-4">Semantic SEO Auditor</h1>
       <form method="GET" action="{{ route('seo.audit') }}" class="mb-4 d-flex">
         <input type="url" name="url" class="form-control me-2" placeholder="https://example.com" required>
         <button class="btn btn-primary">Audit</button>
       </form>
       @if($result)
           <div class="card p-4 shadow-sm">
               @if(isset($result['error']))
                   <div class="alert alert-danger">{{ $result['error'] }}</div>
               @else
                   <h3>Score: <span class="badge bg-success">{{ $result['score'] ?? 0 }}/100</span></h3>
                   <p><b>Title:</b> {{ $result['title'] ?? 'N/A' }}</p>
                   <p><b>Description:</b> {{ $result['description'] ?? 'N/A' }}</p>
                   <p><b>Word Count:</b> {{ $result['word_count'] ?? 0 }}</p>
                   <h5>Recommendations:</h5>
                   <ul>
                     @forelse($result['recommendations'] as $rec)
                         <li>{{ $rec }}</li>
                     @empty
                         <li>No recommendations — great job!</li>
                     @endforelse
                   </ul>
                   <h5>Headings:</h5>
                   <p><b>H1:</b> {{ implode(', ', $result['h1']) }}</p>
                   <p><b>H2:</b> {{ implode(', ', $result['h2']) }}</p>
               @endif
           </div>
       @endif
   </div>
   </body>
   </html>
   
