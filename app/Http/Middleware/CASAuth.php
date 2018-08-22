<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
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
        if(Auth::guard()->check())
        {
            return $next($request);
        }

        if(! $this->cas->checkAuthentication())
        {
            if ($request->ajax() || $request->wantsJson())
            {
                return redirect()->route('unauthorized');
            }

            $this->cas->authenticate();
        }

        /** @var User $user */
        $user = User::query()->where('dirID', CAS::user())
            //->where('group', User::$admin) // Allows only Admins to the application
            ->first();
        
        if(!$user)
        {
            // User is not in the database
            return redirect()->route('unauthorized');
        }

        // Manually authenticate user
        Auth::login($user);

        return $next($request);
    }
}
