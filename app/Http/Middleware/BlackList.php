<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Ip;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlackList
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        if(Ip::where('ip', $ip)->where('status', 'black')->exists()){
            return response()->json([
                'error' => 'Access denied. Your Ip is blacklisted'
            ], 403);
        }
        return $next($request);
    }
}
