<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SeoAuditService;

class SeoAuditController extends Controller
{
    protected $audit;

    public function __construct(SeoAuditService $audit)
    {
        $this->audit = $audit;
    }

    public function index(Request $request)
    {
        $result = null;

        if ($request->has('url')) {
            $url = $request->get('url');
            $result = $this->audit->analyze($url);
        }

        return view('seo.auditor', compact('result'));
    }
}
