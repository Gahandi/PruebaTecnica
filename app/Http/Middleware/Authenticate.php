<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     * Always redirects to the main domain login page, even from subdomains.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // Get the main app URL from config
        $appUrl = config('app.url');

        // Build the full login URL on the main domain
        $loginUrl = $appUrl . '/login';

        // Store the intended URL so user can be redirected back after login
        session()->put('url.intended', $request->fullUrl());

        return $loginUrl;
    }
}
