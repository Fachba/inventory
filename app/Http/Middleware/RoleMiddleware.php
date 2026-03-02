<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $from): Response
    {
        $role = $request->auth->role_id;

        if ($from == "json") {
            # code...
            $menu = $request->input('modul');
            $status = $request->input('next_status');
        } else {
            $menu = $request->query('modul');
            $status = $request->query('next_status');
        }
        
        $query = DB::table('role_actions as ra')
            ->join('action_menus as am', 'ra.action_menu_id', '=', 'am.action_menu_id')
            ->where('ra.role_id', $role)
            ->where('am.status_id', $status)
            ->where('am.menu', 'LIKE', "%$menu%");

        $hasPermission = $query->exists();

        if (!$hasPermission) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
