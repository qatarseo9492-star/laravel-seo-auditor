<?php

namespace App\Http\Middleware;

use Closure;
use App\Support\AnalysisLogger;

class LogAnalysis
{
    /**
     * Parameterized middleware.
     * Usage: ->middleware('log.analysis:psi,url')
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param string|null              $tool    Tool code (psi, content_engine, etc.)
     * @param string                   $param   Request input name containing the URL (default: 'url')
     */
    public function handle($request, Closure $next, ?string $tool = null, string $param = 'url')
    {
        $response = $next($request);

        try {
            if ($tool && $request->has($param)) {
                // You can pass tokens/cost if you have them on request/response
                AnalysisLogger::log($tool, (string) $request->input($param));
            }
        } catch (\Throwable $e) {
            // swallow
        }

        return $response;
    }
}
