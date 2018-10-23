<?php

namespace App\Http\Middleware;

use Closure;

class DisplayChinese
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next ($request);
        $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
        return $response;
    }
}
