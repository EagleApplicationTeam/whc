<?php

namespace App\Http\Middleware;

use Closure;

class VerifiedMiddleware
{
    /**
     * Handle an incoming authorization request and
     * check to see if the authorized user is verified
     * by the super user. If not, logout and display a
     * message.
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

        // If the user is verified, continue with the request.
        return $next($request);
    }
}
