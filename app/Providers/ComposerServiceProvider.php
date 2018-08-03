<?php

namespace App\Providers;


use App\Group;
use App\PeerEvaluations;
use App\User;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{

    public function boot()
    {
        View::composer('*', function($view) {
            $user = User::get();

            $view->with('user', $user);
        });

        View::composer('*', function($view) {
            $peerEvaluations = PeerEvaluations::all();


            $view->with('peerEvaluations', $peerEvaluations);
        });

        View::composer('team_evaluations_team.*', function($view) {
            $groups = Group::all();

            $view->with('groups', $groups);
        });
    }


}