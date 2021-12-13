<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifiedEmail
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(empty(auth()->user()->email_verified_at)){
            return response()->json(['errors'=>['message'=>[trans('auth.emailNotVerified')]]], 422);
        };
        return $next($request);
    }
}
