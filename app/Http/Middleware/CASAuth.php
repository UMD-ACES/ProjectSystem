<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use Subfission\Cas\Facades\Cas;

class CASAuth
{
    protected $auth;
    protected $cas;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
        $this->cas = app('cas');
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if(! $this->cas->checkAuthentication())
        {
            if ($request->ajax() || $request->wantsJson())
            {
                return response('Unauthorized.', 401);
            }
            $this->cas->authenticate();
        }

        $user = User::where('dirID', CAS::user())
            //->where('group', User::$admin) // Allows only Admins to the application
            ->first();

        if(!$user)
        {
            return response('Unauthorized.', 401);
        }

        return $next($request);
    }
}
