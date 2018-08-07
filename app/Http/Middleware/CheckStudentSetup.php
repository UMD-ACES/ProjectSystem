<?php

namespace App\Http\Middleware;

use App\Group;
use App\User;
use Closure;

class CheckStudentSetup
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

        if($user->group == null || Group::query()->find($user->group)->count() == 0)
        {
            return redirect()->route('Student.Setup.Form');
        }


        return $next($request);
    }
}
