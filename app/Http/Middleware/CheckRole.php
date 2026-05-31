<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        if ($user->role !== $role) {
            dd([
                'user_role'     => $user->role,
                'required_role' => $role,
                'user_email'    => $user->email,
                'url'           => $request->url(),
                'intended'      => session('url.intended'),
            ]);
        }

        return $next($request);
    }
}