<?php

namespace App\Http\Controllers;

use App\Criterion;
use App\Group;
use App\PeerEvaluations;
use App\PeerEvaluationsTeam;
use App\PeerEvaluationsTeamMember;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

class PeerEvaluationsStudentController extends Controller
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
            return response('Unauthorized.', 401);
        }

        if($user->hasSubmittedActivePeerEvaluation())
        {
            return response('Unauthorized.', 401);
        }

        return view('student.peer_evaluations.create');
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
            return response('Unauthorized.', 401);
        }

        // Make sure there is an active peer evaluation
        if(!PeerEvaluations::isOneActive())
        {
            return response('Unauthorized.', 401);
        }

        if($user->hasSubmittedActivePeerEvaluation())
        {
            return response('Unauthorized.', 401);
        }


        // Validate Basics: Group, Team Members, Team Evaluation
        $this->validate($request, [
            'group' => 'required|exists:groups,id',
            'teamMembers' => 'required|array|min:2',
            'teamMembers.*' => 'required|numeric|distinct|exists:users,id',
            'team_evaluation' => 'required|string',
        ]);

        // Grade Validation
        $gradeValidation = array();

        foreach ($request->input('teamMembers') as $teamMemberID)
        {
            $gradeValidation['grade_member_'.$teamMemberID] = 'required|numeric|min:0|max:100';
        }

        $this->validate($request, $gradeValidation);

        // Make sure the contribution numbers add up to 100%
        $gradeSum = 0;
        foreach ($request->input('teamMembers') as $teamMemberID)
        {
            $gradeSum += $request->input('grade_member_'.$teamMemberID);
        }

        if($gradeSum != 100)
        {
            return Redirect::back()
                ->withErrors(['Grade Contribution has to add up to 100%'])
                ->withInput(Input::all());
        }

        // Grade Evaluation Validation
        $gradeEvaluationValidation = array();

        foreach ($request->input('teamMembers') as $teamMemberID)
        {
            $gradeEvaluationValidation['grade_evaluation_'.$teamMemberID] = 'required|string';
        }

        $this->validate($request, $gradeEvaluationValidation);

        // Criteria Validation
        $criteriaValidation = array();

        foreach (Criterion::all() as $criterion)
        {
            foreach ($request->input('teamMembers') as $teamMemberID)
            {
                $criteriaValidation[$criterion->name.'_'.$teamMemberID] = 'required';
            }
        }

        $this->validate($request, $criteriaValidation);

        // Insert that the student has completed the active peer evaluation
        $user->peerEvaluations()->attach(PeerEvaluations::active()->id,
            ['display_to_user' => 1]);

        // Associate user to group for this peer evaluation
        $user->group()->attach($request->input('group'),
            ['peer_evaluation_id' => PeerEvaluations::active()->id]);

        // Peer Evaluation Team
        PeerEvaluationsTeam::create(array(
            'peer_evaluation_id' => PeerEvaluations::active()->id,
            'user_id' => $user->id,
            'team_evaluation' => $request->input('team_evaluation')
        ));

        // Peer Evaluation for each team member (including oneself)
        foreach ($request->input('teamMembers') as $teamMemberID)
        {
            PeerEvaluationsTeamMember::create(array(
                'peer_evaluation_id' => PeerEvaluations::active()->id,
                'user_id' => $user->id,
                'user_to_id' => $teamMemberID,
                'grade'      => $request->input('grade_member_'.$teamMemberID),
                'grade_evaluation' => $request->input('grade_evaluation_'.$teamMemberID)
            ));
        }

        // For each criterion
        foreach (Criterion::all() as $criterion)
        {
            // for each team member
            foreach ($request->input('teamMembers') as $teamMemberID)
            {
                /** @var User $teamMember */
                $teamMember = User::find($teamMemberID);

                $user->criteria()->attach($criterion->id,
                    ['value' => $request->input($criterion->name.'_'.$teamMemberID),
                     'peer_evaluation_id' => PeerEvaluations::active()->id,
                     'user_to_id' => $teamMember->id]);

            }
        }


        return redirect()->route('home')->with('success', 1);
    }

    /**
     * Display only the latest peer evaluation. ID is not used
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        /** @var User $user */
        $user = User::get();

        if(!$user->isStudent())
        {
            return response('Unauthorized.', 401);
        }

        // Make sure there is an active peer evaluation
        if(!PeerEvaluations::isOneActive())
        {
            return response('Unauthorized.', 401);
        }

        if(!$user->hasSubmittedActivePeerEvaluation())
        {
            return response('Unauthorized.', 401);
        }

        return view('student.peer_evaluations.show');
    }

    /**
     * Remove the resource from student view
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        /** @var User $user */
        $user = User::get();

        if(!$user->isStudent())
        {
            return response('Unauthorized.', 401);
        }

        // Make sure there is an active peer evaluation
        if(!PeerEvaluations::isOneActive())
        {
            return response('Unauthorized.', 401);
        }

        if(!$user->hasSubmittedActivePeerEvaluation())
        {
            return response('Unauthorized.', 401);
        }

        $peerEvaluation = $user->getSubmittedActivePeerEvaluation();

        $peerEvaluation->pivot->display_to_user = 0;
        $peerEvaluation->pivot->save();

        return redirect()->route('home');
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
