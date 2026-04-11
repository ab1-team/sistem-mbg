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
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = config('auth.central.user');
        $pass = config('auth.central.password');

        if ($request->getUser() !== $user || $request->getPassword() !== $pass) {
            return response('Unauthorized.', 401, [
                'WWW-Authenticate' => 'Basic realm="Central Admin Portal"',
            ]);
        }

        return $next($request);
    }
}
