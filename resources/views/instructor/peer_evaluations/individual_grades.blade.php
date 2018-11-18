@extends('layouts.app')

@section('content')

    <h1 style="text-align: center;">Compute Suggested Individual Grades</h1>
    <br/>
    <p style="color:red;">Important Note: CSV File should not be opened with Excel/Numbers. Only use a text editor like Sublime Text that does not modify files on open</p>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (isset($success))
        <div class="alert alert-success">
            Successfully created!
        </div>
    @endif
    <form method="POST" action="{{ route('Admin.peer_evaluations.individual_grades.compute') }}" enctype="multipart/form-data">
        <span style="font-weight: bold;">Select Peer Evaluations:</span><br/>
        <br/>
        @foreach($peerEvaluations as $peerEvaluation)
            <div class="form-group">
                <input type="checkbox" class="form-check-input" name="peerEvaluations[]"
                       id="peer_evaluation_{{ $peerEvaluation->id }}" value="{{ $peerEvaluation->id }}"/>
                <label class="form-check-label" id="peer_evaluation_{{ $peerEvaluation->id }}">{{ $peerEvaluation->name }}</label>
            </div>
        @endforeach
        <br/>
        <span style="font-weight: bold;">Select ELMS Gradebook Exported CSV File:</span><br/>
        <br/>
        <div class="form-group">
            <div class="gradebook">
                <input type="file" name="gradebook" class="form-control-file" id="gradebook">
            </div>
        </div>

        <span>For the next two questions, open the file in Sublime Text or any other text editor and find the ID associated with the assignment name. The ID should be located after assignment name in between parentheses</span>
        <br/><br/>
        <div class="form-group">
            <label for="group_column" style="font-weight: bold;">Source Column ID (i.e. group grade):</label>
            <input type="number" name="group_column" class="form-control" id="group_column" placeholder="1111111111">
        </div>
        <div class="form-group">
            <label for="individual_column" style="font-weight: bold;">Destination Column ID (i.e. individual grade):</label>
            <input type="number" name="individual_column" class="form-control" id="individual_column" placeholder="22222222">
        </div>

        <br/>
        {{ csrf_field() }}
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>



@endsection