<?php

namespace App\Http\Middleware;

use App\Models\Client;
use Closure;

class ApiAuth
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
        $token = $request->bearerToken();

        if ($token=="") {
            $response = array(
                'code'    => "0",
                'client'    => NULL,
                'status'   => "Error",
                'message'   => "TOKEN NOT FOUND"
            );
    
            return response($response, 403);
        }
        $user = Client::where('token', $token)->first();
        if ($user) {
            return $next($request);
        }

        $response = array(
            'code'    => "0",
            'client'    => NULL,
            'status'   => "Error",
            'message'   => "INVALID LOGIN ATTEMP"
        );

        return response($response, 403);
    }
}
