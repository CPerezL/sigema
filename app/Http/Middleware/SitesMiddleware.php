<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;

class SitesMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, $permission = null, $guard = null)
    {
        $search = array('http:', 'https:', '/');
        $replace = array('', '', '');
        $url1 = explode('|', env('APP_SITE'));
        $url2 = str_replace($search, $replace, url('/'));
        foreach ($url1 as $tu) {
            if (\Hash::check($url2, $tu)) {
                return $next($request);
            }
        }
        throw UnauthorizedException::forPermissions($url1);
    }
}
