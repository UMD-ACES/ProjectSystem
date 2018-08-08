<?php

namespace App\Http\Controllers;

use App\Incident;
use App\TechnicalCategory;
use App\TechnicalLog;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class TechnicalLogsStudentController extends Controller
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

        if(!$user->isStudent())
        {
            return redirect()->route('unauthorized');
        }

        return view('student.technical_logs.create');
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

        if(!$user->isStudent())
        {
            return redirect()->route('unauthorized');
        }

        if($request->input('description') == '<p>&nbsp;</p>')
        {
            $request->merge(['description' => '']);
        }

        $this->validate($request, [
            'category' => 'required|exists:technical_categories,id',
            'completed_at' => 'required|date',
            'description' => 'required|string'
        ]);

        $technicalLog = new TechnicalLog([
            'completed_at' => $request->input('completed_at'),
            'description' => $request->input('description')
        ]);

        $technicalLog->category()->associate(TechnicalCategory::query()->find($request->input('category')));
        $technicalLog->user()->associate($user);
        $technicalLog->group()->associate($user->group);

        $technicalLog->save();

        return redirect()->route('home');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::get();

        if(!$user->isStudent() && !$user->isAdmin())
        {
            return redirect()->route('unauthorized');
        }

        // Make sure the user has access to this technical log
        /** @var Collection $technicalLogs */
        $technicalLogs = $user->group->technicalLogs;

        if(!in_array($id, $technicalLogs->pluck('id')->toArray()))
        {
            Incident::report($user, 'Accessing unauthorized technical log');
            return redirect()->route('unauthorized');
        }

        // Double check - Better be safe
        foreach ($technicalLogs as $technicalLog)
        {
            if($technicalLog->id == $id)
            {
                return view('student.technical_logs.show')->with('technicalLog', $technicalLog);
            }
        }


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
