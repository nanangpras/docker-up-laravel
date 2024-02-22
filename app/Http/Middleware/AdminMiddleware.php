<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)

    {

        
        if (Auth::user())
        {
            if ($request->getRequestUri()!=('/admin/profile')) {
                if ($request->user()->company_id == "") {
                    return redirect()->route('profile');
                } else {
                    Session::put('subsidiary_id', $request->user()->company_id);
                    if ($request->user()->company_id == "1") {
                        Session::put('subsidiary', 'CGL');
                    } else if ($request->user()->company_id == "2") {
                        Session::put('subsidiary', 'EBA');
                    } 
                }
            }
            if ($request->user() && $request->user()->account_type == '1' ) {
                return $next($request);
            }

            return redirect('/');
        }

        return redirect('/login');

    }
}
