<?php

namespace App\Http\Middleware;

use App\Incident;
use App\User;
use Closure;

class CheckStudent
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

        if(!$user->isStudent())
        {
            Incident::report($user, 'Not a student');
            return redirect()->route('unauthorized');
        }

        return $next($request);
    }
}
