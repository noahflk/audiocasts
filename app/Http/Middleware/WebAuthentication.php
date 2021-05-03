<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebAuthentication
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
        // if there is the user of type web AND is enabled -> AUTH
        // if there is no user OR is not enabled -> NO AUTH

        $result = User::where([["type", "web"], ["enabled", "1"]])->get();

        if(!$result->isEmpty() && !Auth::check()) {
            // User exists and is active, so authentication is needed
            return redirect("/login");
        }

        return $next($request);
    }
}
