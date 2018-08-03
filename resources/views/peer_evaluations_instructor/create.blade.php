@extends('layouts.app')

@section('content')

    <h1 style="text-align: center;">Create a new Peer Evaluation</h1>
<br/>
    <p>Note: Students will no longer be able to submit the previous peer evaluation (if there was one)</p>
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
    <form method="POST" action="{{ route('peer_evaluations_instructor.store') }}">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" class="form-control" id="name" placeholder="Enter peer evaluation name">
        </div>

        {{ csrf_field() }}
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>



@endsection