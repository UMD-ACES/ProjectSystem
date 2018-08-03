@extends('layouts.app')

@section('style')


@endsection

@section('content')
    <div>
        <h1 style="text-align: center;">Peer Evaluations System</h1>
        <ul>
            @if($user->isAdmin())
                @if(\App\Group::isSetup() || \App\User::isSetup())
                    <li><a href="{{ route('Admin.Setup.Reset') }}" class="btn btn-danger">Reset Setup</a>
                        <ul>
                            <li>Peer Evaluations and their data are not deleted</li>
                        </ul>
                    </li>
                @else
                    <li><a href="{{ route('Admin.Setup.Form') }}" class="btn btn-primary">Setup</a></li>
                @endif

                <li><a href="{{ route('peer_evaluations_instructor.create') }}" class="btn btn-primary">Create a new peer evaluation</a></li>
                @foreach($peerEvaluations as $peerEvaluation)
                    <li><a href="{{ route('peer_evaluations_instructor.show', $peerEvaluation->id) }}">{{ $peerEvaluation->name }}</a></li>
                @endforeach
            @elseif($user->isStudent() && \App\PeerEvaluations::isOneActive() && !$user->hasSubmittedCurrentPeerEvaluation())
                <li><a href="{{ route('peer_evaluations_team.create') }}" class="btn btn-primary">Submit your peer evaluation</a></li>
            @elseif($user->isStudent() && \App\PeerEvaluations::isOneActive())
                <li><a href="{{ route('peer_evaluations_team.show', \App\PeerEvaluations::active()->id) }}" class="btn btn-primary">View your peer evaluation</a></li>
            @else
                Not active
            @endif
        </ul>
    </div>
@endsection