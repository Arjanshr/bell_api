<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Skip verification check for admins and super-admins
        if ($user && $user->hasRole(['admin','super-admin'])) {
            return $next($request); // Allow the request to continue without verification
        }

        // For regular users, enforce email verification
        if ($user && !$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice'); // Redirect to the verification page
        }

        return $next($request); // Proceed with the request
    }
}
