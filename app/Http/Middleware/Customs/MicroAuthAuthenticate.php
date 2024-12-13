<?php

namespace App\Http\Middleware\Customs;

use App\Models\AuthenticatedUser;
use App\Services\MicroAuth\MicroAuthService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class MicroAuthAuthenticate
{
    public function __construct(
        private MicroAuthService $microAuthService
    ) { }
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $bearerToken = $request->header('Authorization');

        // Tentar validar o token no cache
        $user = Cache::get($bearerToken);

        if (!$user) {
            // Validar o token no micro auth
            $response = $this->microAuthService->validateToken($bearerToken);

            if ($response->failed()) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            $user = $response->json()['user'];

            // Armazenar no cache por 24 horas
            Cache::put($bearerToken, $user, 86400);
        }

        Auth::setUser(new AuthenticatedUser($user));

        // $request->merge(['user' => collect($user)->except(['created_at', 'updated_at'])]);

        return $next($request);
    }
}
