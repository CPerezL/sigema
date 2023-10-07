<?php

namespace App\Http\Middleware;

use Closure;
use DB;
use Illuminate\Http\Request;
//use \App\Models\Menu_items_model;
// use \App\Models\User;
use Spatie\Permission\Exceptions\UnauthorizedException;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     * se neceita verificar si tiene permisos  de escirtura
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, $permission = null, $guard = null)
    {
        $authGuard = app('auth')->guard($guard);
        if ($authGuard->guest()) {
            throw UnauthorizedException::notLoggedIn();
        }
        if (!is_null($permission)) {
            $permissions = is_array($permission)
            ? $permission
            : explode('|', $permission);
        }

        if (is_null($permission)) {
            $permission = $request->route()->getName();

            $permissions = array($permission);
        }
        foreach ($permissions as $permission) {
            //$permiso=User::getPermisos($permission,$authGuard->user()->idRol);
            $permiso = self::getPermisos($permission, $authGuard->user()->idRol);
            if ($permiso) {
                return $next($request);
            }
        }
        throw UnauthorizedException::forPermissions($permissions);
    }
    private function getPermisos($enlace, $user)
    { //funcion par el mieldeware de seguridad
        $mod = DB::table('sgm2_modulos')->select('id')->where('link', 'LIKE', "{$enlace}")->first();
        if (!$mod) {
            $es = false;
        } else {
            $es = DB::table('sgm2_modulos_roles')->where('idModulo', $mod->id)->exists();
        }
        return $es;
    }
}
