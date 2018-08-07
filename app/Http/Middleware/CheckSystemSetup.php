<?php

namespace App\Http\Middleware;

use App\Group;
use App\User;
use Closure;

class CheckSystemSetup
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

        if(!Group::isSetup() || !User::isSetup())
        {
            if($user->isAdmin())
            {
                return redirect()->route('Admin.Setup.Form');
            }

            return response('System not yet setup');
        }


        return $next($request);
    }
}
