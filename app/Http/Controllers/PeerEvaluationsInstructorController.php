<?php

namespace App\Http\Controllers;

use App\Group;
use App\Incident;
use App\PeerEvaluation;
use App\User;
use Illuminate\Http\Request;

class PeerEvaluationsInstructorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = User::get();

        if(!$user->isAdmin())
        {
            Incident::report($user, 'Admin Access Only - Creating a new peer evaluation');
            return redirect()->route('unauthorized');
        }

        if(!User::isSetup() || !Group::isSetup())
        {
            return redirect()->route('unauthorized');
        }

        return view('instructor.peer_evaluations.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = User::get();

        if(!$user->isAdmin())
        {
            Incident::report($user, 'Admin Access Only - Creating a new peer evaluation');
            return redirect()->route('unauthorized');
        }



        $this->validate($request, [
            'name' => 'required|string'
        ]);

        PeerEvaluation::query()->update(['active' => 0]);

        PeerEvaluation::query()->create($request->all());

        return view('instructor.peer_evaluations.create')->with('success', 1);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $user = User::get();

        if(!$user->isAdmin())
        {
            Incident::report($user, 'CRITICAL - Admin Access Only - Trying to access the peer evaluation viewer');
            return redirect()->route('unauthorized');
        }

        $peerEvaluation = PeerEvaluation::query()->findOrFail($id);

        $group = null;

        if($request->has('group'))
        {
            $group = Group::query()->find($request->input('group'));
        }

        return view('instructor.peer_evaluations.show')
            ->with('peerEvaluation', $peerEvaluation)
            ->with('group', $group);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
