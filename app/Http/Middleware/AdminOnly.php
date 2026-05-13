<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Akses ditolak. Admin only.'], 403)
                : abort(403, 'Akses ditolak. Admin only.');
        }

        return $next($request);
    }
}