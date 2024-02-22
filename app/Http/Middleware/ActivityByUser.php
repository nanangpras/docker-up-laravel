<?php

namespace App\Http\Middleware;

use App\User;
use Cache;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Auth;

class ActivityByUser
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
        if (Auth::check()) {
            $expiresAt = Carbon::now()->addSeconds(120); // keep online for 1 min
            Cache::put('user-is-online-' . Auth::user()->id, true, $expiresAt);
            if($request->getRequestUri()==('/admin/notification') 
            || $request->getRequestUri()==('/admin/cloud-status') 
            || $request->getRequestUri()==('/admin/server-status') 
            || $request->getRequestUri()==('/admin/dashboard/chat') 
            || $request->getRequestUri()==('/admin/dashboard/new_chat') 
            || $request->getRequestUri()==('/admin/sync-status')){
            }else{
                Cache::put('user-is-located-' . Auth::user()->id, $request->getRequestUri(), $expiresAt);
            }
            // last seen
            User::where('id', Auth::user()->id)->update(['last_login' => (new \DateTime())->format("Y-m-d H:i:s")]);
        }
        return $next($request);
    }
}
