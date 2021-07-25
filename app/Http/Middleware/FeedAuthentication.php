<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedAuthentication
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
        $result = User::where([['type', 'feed'], ['enabled', '1']])->get();

        if($result->isEmpty()) {
            // authentication is not required
            return $next($request);
        }

        header('Cache-Control: no-cache, must-revalidate, max-age=0');
        $has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));

        if(!$has_supplied_credentials || !Auth::attempt(['username' => $_SERVER['PHP_AUTH_USER'], 'password' => $_SERVER['PHP_AUTH_PW'], 'type' => 'feed'], true)) {
            // No credentials found, so send basic authentication prompt
            // Or credentials found, but authentication unsuccessful, so resend prompt
            header('HTTP/1.1 401 Authorization Required');
            header('WWW-Authenticate: Basic realm="Please authenticate"');
            exit;
        }

        return $next($request);
    }
}
