<?php

namespace App\Http\Controllers;

use App\Group;
use App\Incident;
use App\MeetingMinute;
use App\MeetingMinutesAttendance;
use App\User;
use Illuminate\Http\Request;

class MeetingMinutesStudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

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

        return view('student.meeting_minutes.create');
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

        if($request->input('actionItems') == '<p>&nbsp;</p>')
        {
            $request->merge(['actionItems' => '']);
        }

        if($request->input('notes') == '<p>&nbsp;</p>')
        {
            $request->merge(['notes' => '']);
        }

        // Validate request
        $this->validate($request, [
            'presentMembers' => 'required|array|min:2',
            'presentMembers.*' => 'required|numeric|distinct|exists:users,id',
            'absentMembers' => 'array',
            'absentMembers.*' => 'required|numeric|distinct|exists:users,id',
            'start' => 'required|date',
            'end' => 'required|date|after:start',
            'notes' => 'required|string',
            'actionItems' => 'required|string',
            'nextMeeting' => 'required|date|after:now'
        ]);

        $meetingMinute = new MeetingMinute([
            'notes' => $request->input('notes'),
            'action_items' => $request->input('actionItems'),
            'next_meeting' => $request->input('nextMeeting'),
            'start' => $request->input('start'),
            'end' => $request->input('end')
        ]);

        $meetingMinute->user()->associate($user);
        $meetingMinute->group()->associate($user->group);

        $meetingMinute->save();

        foreach ($request->input('presentMembers') as $presentMember)
        {
            $meetingMinuteAttendance = new MeetingMinutesAttendance();
            $meetingMinuteAttendance->present = 1;
            $meetingMinuteAttendance->meetingMinute()->associate($meetingMinute);
            $meetingMinuteAttendance->user()->associate(User::query()->find($presentMember));

            $meetingMinuteAttendance->save();
        }

        if($request->input('absentMembers'))
        {
            foreach ($request->input('absentMembers') as $absentMembers)
            {
                $meetingMinuteAttendance = new MeetingMinutesAttendance();
                $meetingMinuteAttendance->present = 0;
                $meetingMinuteAttendance->meetingMinute()->associate($meetingMinute);
                $meetingMinuteAttendance->user()->associate(User::query()->find($absentMembers));

                $meetingMinuteAttendance->save();
            }
        }

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

        if(!$user->isStudent())
        {
            return redirect()->route('unauthorized');
        }

        $meetingMinutesForUser = MeetingMinute::findForUser($user);

        if(!in_array($id, $meetingMinutesForUser->pluck('id')->toArray()))
        {
            Incident::report($user, 'Meeting Minutes Unauthorized Page');
            return redirect()->route('unauthorized');
        }

        // Double check - Better be safe
        foreach ($meetingMinutesForUser as $meetingMinute)
        {
            if($meetingMinute->id == $id)
            {
                return view('student.meeting_minutes.show')->with('meetingMinute', $meetingMinute);
            }
        }

        //Incident::report($user, 'Meeting Minutes Unauthorized Page');
        return redirect()->route('unauthorized');
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
