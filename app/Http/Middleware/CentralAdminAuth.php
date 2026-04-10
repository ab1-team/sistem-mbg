<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CentralAdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = env('CENTRAL_ADMIN_USER', 'admin');
        $pass = env('CENTRAL_ADMIN_PASSWORD', 'password');

        if ($request->getUser() !== $user || $request->getPassword() !== $pass) {
            return response('Unauthorized.', 401, [
                'WWW-Authenticate' => 'Basic realm="Central Admin Portal"',
            ]);
        }

        return $next($request);
    }
}
