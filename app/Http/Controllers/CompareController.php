<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use DOMDocument, DOMXPath;

class CompareController extends Controller
{
    public function compare(Request $req)
    {
        $data = $req->validate([
            'urls' => 'required|array|min:2|max:5',
            'urls.*' => 'url'
        ]);

        $rows=[];
        foreach($data['urls'] as $u){
            try{
                $r = Http::timeout(12)->connectTimeout(5)->get($u);
                $html = $r->body() ?? '';
                libxml_use_internal_errors(true);
                $dom = new DOMDocument(); @$dom->loadHTML($html);
                $xp  = new DOMXPath($dom);
                $title = trim(optional($xp->query('//title'))->item(0)?->textContent ?? '');
                $desc = '';
                foreach ($xp->query('//meta[translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="description"]') as $n){
                    $desc = $n->getAttribute('content'); break;
                }
                $h1 = $xp->query('//h1')->length;
                $h2 = $xp->query('//h2')->length;
                $words = str_word_count(strip_tags($html));
                $rows[] = ['url'=>$u,'title'=>$title,'description'=>$desc,'h1'=>$h1,'h2'=>$h2,'words'=>$words];
            } catch(\Throwable $e){
                $rows[] = ['url'=>$u,'title'=>'(error)','description'=>$e->getMessage(),'h1'=>0,'h2'=>0,'words'=>0];
            }
        }
        return response()->json(['ok'=>true,'rows'=>$rows]);
    }
}
