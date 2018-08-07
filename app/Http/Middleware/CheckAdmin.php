<?php

namespace App\Http\Middleware;

use App\Incident;
use App\User;
use Closure;

class CheckAdmin
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
        $user = User::get();

        if(!$user->isAdmin())
        {
            Incident::report($user, 'Not an administrator - Restricted');
            return redirect()->route('unauthorized');
        }


        return $next($request);
    }
}
