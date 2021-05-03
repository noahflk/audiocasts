<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;

class EnsureSetupIsIncomplete
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
        $result = Setting::where("key", "SETUP_DONE")->first();

        if($result && $result->value != "" && $result->value != "0") {
            return redirect("/");
        }

        return $next($request);
    }
}
