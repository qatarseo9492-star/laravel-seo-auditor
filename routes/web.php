<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

Route::get('/', fn () => view('home'));

Route::post('/analyze-json', function (\Illuminate\Http\Request $req) {
    $url = trim($req->input('url', ''));
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return response()->json(['ok' => false, 'error' => 'Invalid URL'], 422);
    }

    try {
        $resp = Http::withHeaders([
            'User-Agent' => 'SemanticSEO-MasterAnalyzer/2.6 (+https://yourdomain.com)'
        ])->timeout(12)->connectTimeout(5)->get($url);

        $status = $resp->status();
        if ($status >= 400) return response()->json(['ok'=>false,'error'=>"Request failed ($status)"], 502);

        $html  = $resp->body() ?? '';
        $host  = parse_url($url, PHP_URL_HOST) ?: '';
        $scheme= preg_match('#^https://#',$url)?'https://':'http://';

        if (!function_exists('array_is_list')) {
            function array_is_list(array $array): bool { $i = 0; foreach ($array as $k => $_) { if ($k !== $i++) return false; } return true; }
        }

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);
        $xp  = new DOMXPath($dom);
        $q   = fn($x) => iterator_to_array($xp->query($x) ?? []);
        $attr= fn($n,$a) => $n?->attributes?->getNamedItem($a)?->nodeValue ?? '';

        $titleNode = $q('//title')[0] ?? null;
        $titleText = trim($titleNode?->textContent ?? '');
        $metaDescNode = $q('//meta[translate(@name
