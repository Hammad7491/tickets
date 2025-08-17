<?php

// app/Http/Middleware/EnsureUserIsNotBlocked.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsNotBlocked
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && (bool) Auth::user()->is_blocked) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Your account has been blocked by an administrator.']);
        }

        return $next($request);
    }
}
