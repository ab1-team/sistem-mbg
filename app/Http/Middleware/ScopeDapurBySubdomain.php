<?php

namespace App\Http\Middleware;

use App\Models\Dapur;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ScopeDapurBySubdomain
{
    /**
     * Handle an incoming request.
     * 1 subdomain = 1 dapur
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $parts = explode('.', $host);

        // Expecting: {dapur_slug}.{foundation_domain}.{tld}
        // or {foundation_domain}.{tld} (Central/Yayasan level)

        if (count($parts) >= 3) {
            $subdomain = $parts[0];

            // Check if this subdomain matches a Dapur slug
            $dapur = Dapur::where('slug', $subdomain)->first();

            if ($dapur) {
                // Store the detected dapur in the request/session/config for global scoping
                session(['active_dapur_id' => $dapur->id]);

                // Add to request for easy access
                $request->merge(['active_dapur' => $dapur]);
            }
        }

        return $next($request);
    }
}
