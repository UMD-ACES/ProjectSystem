<?php

namespace App\Http\Controllers;

use App\Group;
use App\PeerEvaluations;
use App\User;
use Illuminate\Http\Request;

class SetupController extends Controller
{
    public function setupForm()
    {
        $user = User::get();

        if(!$user->isAdmin())
        {
            return response('Unauthorized.', 401);
        }

        if(Group::isSetup() || User::isSetup())
        {
            // Already setup
            return view('welcome');
        }

        return view('setup.create');
    }

    public function setup(Request $request)
    {
        $user = User::get();

        if(!$user->isAdmin())
        {
            return response('Unauthorized.', 401);
        }

        if(Group::isSetup() || User::isSetup())
        {
            // Already setup
            return view('welcome');
        }

        // Remove the "while(1);" loop
        $request->merge(['students' =>
            str_replace('while(1);', '', $request->input('students'))]);
        $request->merge(['groups' =>
            str_replace('while(1);', '', $request->input('groups'))]);

        $this->validate($request, [
            'students' => 'required|json',
            'groups' => 'required|json'
        ]);

        $students = json_decode($request->input('students'), true);
        $groups   = json_decode($request->input('groups'), true);

        foreach ($students as $student)
        {
            /** @var User $user */
            $user = User::create(array(
                'dirID' => $student['login_id'],
                'name'  => $student['name'],
            ));

            $user->type = User::$student;

            $user->save();
        }

        foreach ($groups as $group)
        {
            Group::create(array(
                'name' => $group['name']
            ));
        }

        return view('welcome')->with('success', 1);
    }

    public function reset()
    {
        $user = User::get();

        if(!$user->isAdmin())
        {
            return response('Unauthorized.', 401);
        }

        if(Group::isSetup() || User::isSetup())
        {
            // Setup so let's truncate
            User::query()->where('type', User::$student)->truncate();
            Group::query()->truncate();
        }

        return redirect()->route('home');
    }
}
