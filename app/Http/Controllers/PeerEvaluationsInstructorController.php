<?php

namespace App\Http\Controllers;

use App\Group;
use App\Incident;
use App\PeerEvaluation;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use League\Csv\Writer;

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
     *
     */
    public function individual_grades_form()
    {
        $user = User::get();

        if(!$user->isAdmin())
        {
            Incident::report($user, 'CRITICAL - Admin Access Only - Trying to access the peer evaluation individual grades form');
            return redirect()->route('unauthorized');
        }

        return view('instructor.peer_evaluations.individual_grades');
    }

    /**
     *
     */
    public function individual_grades(Request $request)
    {
        $user = User::get();

        if(!$user->isAdmin())
        {
            Incident::report($user, 'CRITICAL - Admin Access Only - Trying to access the peer evaluation individual grades');
            return redirect()->route('unauthorized');
        }

        $this->validate($request, [
            'peerEvaluations' => 'required|array',
            'peerEvaluations.*' => 'numeric',
            'gradebook' => 'required|file',
            'group_column' => 'required|numeric',
            'individual_column' => 'required|numeric'
        ]);

        // File Upload
        $path = $request->file('gradebook')->store('gradebook');
        $file_contents = Storage::get($path);

        // Peer Evaluations Setup
        $peerEvaluationsID = $request->input('peerEvaluations');
        $peerEvaluations = new Collection();

        foreach ($peerEvaluationsID as $peerEvaluationID)
        {
            $peerEvaluations->push(PeerEvaluation::query()->findOrFail($peerEvaluationID));
        }

        $csv = Reader::createFromString($file_contents);
        $newCSV = Writer::createFromFileObject(new \SplTempFileObject());

        $groupColumnID = $request->input('group_column');
        $individualColumnID = $request->input('individual_column');
        $groupColumn = null;
        $individualColumn = null;
        $directoryIDColumn = null;
        $groupPointsPossible = null;
        $individualPointsPossible = null;

        $row = 0;
        foreach ($csv as $record)
        {
            $column = 0;
            $directoryID = null;
            $groupPoints = null;

            foreach ($record as $value)
            {
                if ($row == 0) {
                    // Header
                    if (strpos($value, $groupColumnID) !== false)
                    {
                        $groupColumn = $column;
                    }
                    else if (strpos($value, $individualColumnID) !== false)
                    {
                        $individualColumn = $column;
                    }
                    else if(strpos($value, 'Login ID') !== false)
                    {
                        $directoryIDColumn = $column;
                    }
                }
                else if($row == 1)
                {
                    // Muted or not - doesn't matter - test that the program got the columns
                    if($groupColumn == null)
                    {
                        echo 'Could not find group column. Try again';
                        exit();                        
                    }
                    if($individualColumn == null)
                    {
                        echo 'Could not find individual column. Try again';
                        exit();
                    }
                    if($directoryIDColumn == null)
                    {
                        echo 'Naming for the Directory ID column must have changed. Looking for hardcoded substring "Login ID" '.
                        'so it must be changed in the file or in the application';
                        exit();
                    }
                }
                else if ($row == 2) {
                    // Points Possible
                    if($column == $groupColumn)
                    {
                        $groupPointsPossible = floatval($value);
                    }
                    if($column == $individualColumn)
                    {
                        $individualPointsPossible = floatval($value);
                    }
                }
                else {
                    // Data
                    if($column == $directoryIDColumn)
                    {
                        $directoryID = $value;
                    }

                    if($column == $groupColumn)
                    {
                        $groupPoints = floatval($value);
                    }
                }

                $column++;
            }

            if($directoryID != null && $groupPoints != null)
            {
                /** @var User $user */
                $user = User::query()->where('dirID', '=', $directoryID)->first();

                if($user != null)
                {
                    $individualScore = $user->individualScore($peerEvaluations); // Percentage 0-100

                    //echo 'Individual Score: '.$individualScore.'<br/>';
                    //echo 'Group Points: '.$groupPoints.'<br/>';
                    //echo 'Group Points Possible: '.$groupPointsPossible.'<br/>';
                    $groupScoreOutOf100 = $groupPoints * (100 / $groupPointsPossible); // Percentage 0-100
                    //echo 'Group Score Out of 100: '.$groupScoreOutOf100.'<br/>';

                    $adjustedIndividualScore = $groupScoreOutOf100 / 100 * $individualScore / 100; // Multiply both values 0 - 1
                    $adjustedIndividualScore = $adjustedIndividualScore * $individualPointsPossible; // 0 - Individual Points Possible

                    //echo 'Adjusted Score:'.$adjustedIndividualScore.'<br/>';

                    if($adjustedIndividualScore > $individualPointsPossible)
                    {
                        $adjustedIndividualScore = $individualPointsPossible;
                    }

                    $record[$individualColumn] = round($adjustedIndividualScore);
                }
            }

            $newCSV->insertOne($record);
            $row++;
        }

        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="gradebook.csv"',
        ];
        return response()->make($newCSV, 200, $headers);

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
