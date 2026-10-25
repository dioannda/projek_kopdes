<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Hanya mengizinkan peran yang disebut (mis. UC-02: gudang, manajer).
     * Peran lain mendapat 403.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $pengguna = $request->user();

        if ($pengguna === null || ! in_array($pengguna->peran, $roles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
