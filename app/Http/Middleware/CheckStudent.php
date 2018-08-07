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
            return response('Unauthorized access. You must be a student.', 401);
        }

        return $next($request);
    }
}
