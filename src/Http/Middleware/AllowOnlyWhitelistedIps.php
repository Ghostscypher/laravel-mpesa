<?php

namespace Ghostscypher\Mpesa\Http\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AllowOnlyWhitelistedIps
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  mixed  $roles
     */
    public function handle(Request $request, \Closure $next, ?bool $fuzzy_match = null): Response
    {
        // If localhost or environment is local
        if (app()->environment(config('mpesa.allowed_environments'))) {
            return $next($request);
        }

        $whitelisted_ips = config('mpesa.whitelisted_ips');
        $client_ip = $request->ip();

        if (is_null($fuzzy_match)) {
            $fuzzy_match = config('mpesa.allow_fuzzy_matching');
        }

        // If not a fuzzy match and the client IP is not in the whitelist, abort
        if (! $fuzzy_match && ! in_array($client_ip, $whitelisted_ips)) {
            abort(404, 'Not Found');
        } elseif ($fuzzy_match) {
            // If it is a fuzzy match, check if the client IP is in the whitelist
            $is_whitelisted = false;
            $fuzzy_match_ips = config('mpesa.fuzzy_match_ips'); // Get the IPs to fuzzy match against
            foreach ($fuzzy_match_ips as $ip) {
                if (strpos($client_ip, $ip) === 0) {
                    $is_whitelisted = true;

                    break;
                }
            }

            if (! $is_whitelisted) {
                abort(404, 'Not Found');
            }
        }

        return $next($request);
    }
}
