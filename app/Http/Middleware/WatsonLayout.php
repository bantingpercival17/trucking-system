<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WatsonLayout
{
    /**
     * Handle an incoming request.
     *
     * Mirrors FlashLayout. Note: this fixes a real gap in the original Watson
     * stub — routes/watson-routes.php applied the 'flash.layout' middleware to
     * /watson/* routes, but FlashLayout only matches 'flash/*' URLs, so the
     * session('layout') value was never being set to 'watson'. That broke the
     * shared TruckController/DriverController view selection for Watson.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('watson/*')) {
            session(['layout' => 'watson']);
        }

        return $next($request);
    }
}
