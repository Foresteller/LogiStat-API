<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        $allowedRoles = array_map(fn (string $role) => UserRole::tryFrom($role), $roles);
        if (! $user || ! in_array($user->role, $allowedRoles)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Forbidden. Not enough permissions',
            ], 403);
        }

        return $next($request);
    }
}
