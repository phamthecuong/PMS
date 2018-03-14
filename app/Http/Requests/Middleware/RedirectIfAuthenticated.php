<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check())
        {
            // if (Auth::guard($guard)->user()->hasRole('superadmin') || Auth::guard($guard)->user()->hasRole('adminlv1') || Auth::guard($guard)->user()->hasRole('adminlv2') || Auth::guard($guard)->user()->hasRole('adminlv3'))
            // {
            return redirect('user/home');
            // }
            // else if (Auth::guard($guard)->user()->hasRole('userlv1') || Auth::guard($guard)->user()->hasRole('userlv2') || Auth::guard($guard)->user()->hasRole('userlv3'))
            // {
            //     // should be changed later
            //     return redirect()->route('user.home');
            // }
            // else
            // {
            //     Auth::logout();
            // }
        }
        // if (Auth::guard($guard)->check()) {
        //     return redirect('/');
        // }

        return $next($request);
    }
}
