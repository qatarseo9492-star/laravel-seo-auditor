<?php

namespace App\Http\Controllers;

use App\Models\ContentDetection;
use App\Services\ContentDetectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ContentDetectionController extends Controller
{
    protected ContentDetectionService $svc;

    public function __construct(ContentDetectionService $svc)
    {
        $this->svc = $svc;
    }

    public function detect(Request $request)
    {
        $v = Validator::make($request->all(), [
            'content' => ['required', 'string', 'min:20', 'max:' . config('content-detection.limits.max_chars')],
        ]);

        if ($v->fails()) {
            return response()->json(['ok' => false, 'errors' => $v->errors()], 422);
        }

        $content = (string) $request->input('content');

        try {
            $result = $this->svc->detect($content);

            // Save to DB (encrypt content to meet GDPR-ish requirement)
            $det = new ContentDetection();
            $det->content = encrypt($content);
            $det->ai_score = $result['final_score'];
            $det->confidence = $result['confidence'];
            $det->model_used = implode('+', $result['used']);
            $det->features = $result['stats']['features'] ?? [];
            $det->verdict = $result['verdict'];
            $det->save();

            return response()->json(['ok' => true, 'data' => $result, 'id' => $det->id]);
        } catch (\Throwable $e) {
            Log::error('Detect failed', ['e' => $e]);
            return response()->json(['ok' => false, 'error' => 'Detection failed: ' . $e->getMessage()], 500);
        }
    }

    public function detectBatch(Request $request)
    {
        $v = Validator::make($request->all(), [
            'contents' => ['required', 'array', 'min:1', 'max:100'],
            'contents.*' => ['required', 'string', 'min:20', 'max:' . config('content-detection.limits.max_chars')],
        ]);

        if ($v->fails()) {
            return response()->json(['ok' => false, 'errors' => $v->errors()], 422);
        }

        $contents = $request->input('contents');

        $results = $this->svc->detectBatch($contents);

        // Save each to DB
        foreach ($contents as $i => $content) {
            try {
                $r = $results[$i] ?? null;
                if (!$r || !($r['ok'] ?? false)) continue;

                $det = new ContentDetection();
                $det->content = encrypt($content);
                $det->ai_score = $r['final_score'];
                $det->confidence = $r['confidence'];
                $det->model_used = implode('+', $r['used']);
                $det->features = $r['stats']['features'] ?? [];
                $det->verdict = $r['verdict'];
                $det->save();
            } catch (\Throwable $e) {
                Log::warning('Batch save error', ['i' => $i, 'e' => $e->getMessage()]);
            }
        }

        return response()->json(['ok' => true, 'data' => $results]);
    }

    public function history(Request $request)
    {
        $items = ContentDetection::latest()->paginate(20);
        // Decrypt content for view
        foreach ($items as $item) {
            try {
                $item->content_plain = decrypt($item->content);
            } catch (\Throwable $e) {
                $item->content_plain = '—';
            }
        }
        return view('detection.history', compact('items'));
    }

    public function show($id)
    {
        $det = ContentDetection::findOrFail($id);
        try {
            $det->content_plain = decrypt($det->content);
        } catch (\Throwable $e) {
            $det->content_plain = '—';
        }
        return response()->json(['ok' => true, 'data' => $det]);
    }
}
