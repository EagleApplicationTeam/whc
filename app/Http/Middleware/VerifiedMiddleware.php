<?php

namespace App\Http\Middleware;

use Closure;

class VerifiedMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Check if the user has been verified
        if (!auth()->user()->verified) {
            // Log the user out
            auth()->logout();

            // Redirect with message
            return redirect('/login')->with('unauth', "You're account has not been verfied yet by the site administrator.");
        }

        return $next($request);
    }
}
