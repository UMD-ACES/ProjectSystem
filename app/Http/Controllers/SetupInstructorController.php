<?php

namespace App\Http\Controllers;

use App\Criterion;
use App\Group;
use App\Incident;
use App\MeetingMinute;
use App\MeetingMinutesAttendance;
use App\PeerEvaluation;
use App\PeerEvaluationsTeam;
use App\PeerEvaluationsTeamMember;
use App\TechnicalCategory;
use App\TechnicalLog;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SetupInstructorController extends Controller
{
    public function setupForm()
    {
        $user = User::get();

        if(!$user->isAdmin())
        {
            return redirect()->route('unauthorized');
        }

        if(Group::isSetup() || User::isSetup())
        {
            // Already setup
            return view('welcome');
        }

        return view('instructor.setup.create');
    }

    public function setup(Request $request)
    {
        $user = User::get();

        if(!$user->isAdmin())
        {
            return redirect()->route('unauthorized');
        }

        if(Group::isSetup() || User::isSetup())
        {
            // Already setup
            return redirect()->route('home');
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
            if($student['name'] == 'Test Student')
            {
                continue;
            }

            /** @var User $user */
            $user = User::query()->create(array(
                'dirID' => $student['login_id'],
                'name'  => $student['name'],
            ));

            $user->type = User::$student;

            $user->save();
        }

        foreach ($groups as $group)
        {
            Group::query()->create(array(
                'name' => $group['name']
            ));
        }

        return redirect()->route('home')->with('success', 1);
    }

    public function reset()
    {
        $user = User::get();

        if(!$user->isAdmin())
        {
            return redirect()->route('unauthorized');
        }

        if(Group::isSetup() || User::isSetup())
        {
            // Setup so let's truncate
            User::query()->where('type', User::$student)->delete();
            Group::query()->truncate();
        }

        PeerEvaluation::query()->truncate();
        Criterion::query()->truncate();
        Incident::query()->truncate();
        MeetingMinute::query()->truncate();
        MeetingMinutesAttendance::query()->truncate();
        PeerEvaluation::query()->truncate();
        PeerEvaluationsTeam::query()->truncate();
        PeerEvaluationsTeamMember::query()->truncate();
        TechnicalCategory::query()->truncate();
        TechnicalLog::query()->truncate();
        DB::table('user_peer_evaluation')->truncate();
        DB::table('user_criterion')->truncate();

        return redirect()->route('home');
    }

    public function refresh()
    {
        $user = User::get();

        if(!$user->isAdmin())
        {
            return redirect()->route('unauthorized');
        }

        PeerEvaluation::query()->truncate();
        PeerEvaluationsTeamMember::query()->truncate();
        PeerEvaluationsTeam::query()->truncate();
        DB::table('user_peer_evaluation')->truncate();
        DB::table('user_criterion')->truncate();

        return redirect()->route('home');
    }
}
