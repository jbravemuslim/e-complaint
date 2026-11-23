<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class RoleMiddleware
{
    /**
     * @param  string  ...$roles  Daftar role yang diizinkan, mis. role:admin,superadmin
     */
    public function handle(Request $request, Closure $next, string ...$roles): SymfonyResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        // Dukung penulisan role:admin,superadmin maupun role:admin superadmin
        $allowed = [];
        foreach ($roles as $role) {
            foreach (explode(',', $role) as $part) {
                $part = trim($part);
                if ($part !== '') {
                    $allowed[] = $part;
                }
            }
        }

        if ($allowed !== [] && ! in_array(Auth::user()->role, $allowed, true)) {
            return redirect()->route('home')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        return $next($request);
    }
}
