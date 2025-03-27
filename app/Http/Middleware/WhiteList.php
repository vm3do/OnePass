<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Ip;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WhiteList
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
            if(!Ip::where('ip', $request->ip())->where('user_id', auth()->id())->where('status', 'white')->exists()){
                return response()->json([
                    'error' => 'Access denied. Your Ip is not whitelisted'
                ], 403);
            }
        
        return $next($request);
        }
}
