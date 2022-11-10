<?php

namespace App\Http\Middleware;

use App;
use Closure;

class ApiLogMiddleware
{
    public function handle($request, Closure $next)
    {
        $locale = $request->header('locale');
        if (empty($locale)) {
            $locale = "en";
        }
        App::SetLocale($locale);
        return $next($request);
    }

    // terminate function will call after request execution done
    public function terminate($request, $response)
    {
        \Log::info(['header' => $request->header(), 'api' => $request->url(), 'Request' => $request->all(), 'response' => $response->getContent()]);
    }
}
