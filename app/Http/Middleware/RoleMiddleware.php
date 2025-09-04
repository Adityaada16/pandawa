<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  mixed ...$allowed role IDs
     */
    public function handle(Request $request, Closure $next, ...$allowed): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        if (!in_array((string)$user->id_role, array_map('strval', $allowed), true)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Role Anda tidak memiliki izin untuk mengakses endpoint ini.',
                'role' => $user->name
            ], 403);
        }

        return $next($request);
    }
}
