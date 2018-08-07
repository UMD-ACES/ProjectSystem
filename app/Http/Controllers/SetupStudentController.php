<?php

namespace App\Http\Controllers;

use App\Group;
use App\User;
use Illuminate\Http\Request;

class SetupStudentController extends Controller
{

    public function setupForm()
    {
        $user = User::get();

        if(!$user->isStudent())
        {
            return redirect()->route('unauthorized');
        }

        if($user->group != null)
        {
            return redirect()->route('home');
        }

        return view('student.setup.form');
    }


    public function setup(Request $request)
    {
        $user = User::get();

        if(!$user->isStudent())
        {
            return redirect()->route('unauthorized');
        }

        if($user->group != null)
        {
            return redirect()->route('home');
        }

        $this->validate($request, [
            'group' => 'required|exists:groups,id',
        ]);

        $group = Group::query()->find($request->input('group'));

        $user->group()->associate($group);

        $user->save();

        return redirect()->route('home')->with('success', 1);
    }
}
