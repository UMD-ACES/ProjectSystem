@extends('layouts.app')
<?php /** @var \App\User $user */ ?>
@section('style')


@endsection

@section('content')
    <div>
        <h1 style="text-align: center;">Group Project System: {{ $user->name }}</h1>
        <br/>
        @if (isset($success))
            <div class="alert alert-success">
                Success!
            </div>
        @endif

        @if($user->isAdmin())
            <!-- Meeting Minutes -->
            <h2 style="text-align: center;">Meeting Minutes</h2>
            <table id="meetingMinutes">
                <thead>
                    <tr>
                        <th>Meeting</th>
                        <th>Group</th>
                        <th>Creator</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Duration</th>
                        <th>Submitted At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(\App\MeetingMinute::all() as $meetingMinute)
                        <tr>
                            <td><a href="{{ route('meeting_minutes_instructor.show', $meetingMinute->id) }}">Access</a></td>
                            <td>{{ $meetingMinute->group->name }}</td>
                            <td>{{ $meetingMinute->user->name }}</td>
                            <td>{{ (new Carbon\Carbon($meetingMinute->start))->toDayDateTimeString() }}</td>
                            <td>{{ (new Carbon\Carbon($meetingMinute->end))->toDayDateTimeString() }}</td>
                            <td>{{ ((new \Carbon\Carbon($meetingMinute->end))->diffInHours(new \Carbon\Carbon($meetingMinute->start))) }}:{{ ((new \Carbon\Carbon($meetingMinute->end))->diff(new \Carbon\Carbon($meetingMinute->start)))->format('%I:%S') }}</td>
                            <td>{{ (new Carbon\Carbon($meetingMinute->created_at))->toDayDateTimeString() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Peer Evaluation -->
            <h2 style="text-align: center;">Peer Evaluation</h2>
            <table id="peerEvaluations">
                <thead>
                <tr>
                    <th>Peer Evaluation</th>
                    <th>Active</th>
                    <th>Created at</th>
                </tr>
                </thead>
                <tbody>
                @foreach($peerEvaluations as $peerEvaluation)
                    <tr>
                        <td><a href="{{ route('peer_evaluations_instructor.show', $peerEvaluation->id) }}">{{ $peerEvaluation->name }}</a></td>
                        <td>{{ $peerEvaluation->active }}</td>
                        <td>{{ $peerEvaluation->created_at }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            @if(\App\Group::isSetup() || \App\User::isSetup())
                <a href="{{ route('peer_evaluations_instructor.create') }}" class="btn btn-primary">Create a new peer evaluation</a>
                <a href="{{ route('Admin.Setup.Reset') }}" class="btn btn-danger" onclick="return confirm('Are you want to reset the entire setup?\nPrevious Peer Evaluation data will NOT be deleted')">Reset Setup</a>
                <a href="{{ route('Admin.Setup.Refresh') }}" class="btn btn-danger" onclick="return confirm('Are you want to delete all peer evaluations?\nThis action is irreversible')">Delete Peer Evaluations</a>
            @else
                <a href="{{ route('Admin.Setup.Form') }}" class="btn btn-primary">Setup</a>
            @endif
            <!-- End of Peer Evaluation -->

        @elseif($user->isStudent())
            <!-- Meeting Minutes -->
            <h2 style="text-align: center;">Meeting Minutes</h2>
            <table id="meetingMinutes">
                <thead>
                    <tr>
                        <th>Meeting</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Duration</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(\App\MeetingMinute::findForUser($user) as $meetingMinute)
                        <tr>
                            <td><a href="{{ route('meeting_minutes.show', $meetingMinute->id) }}">Access</a></td>
                            <td>{{ (new Carbon\Carbon($meetingMinute->start))->toDayDateTimeString()  }}</td>
                            <td>{{ (new Carbon\Carbon($meetingMinute->end))->toDayDateTimeString()  }}</td>
                            <td>{{ ((new \Carbon\Carbon($meetingMinute->end))->diffInHours(new \Carbon\Carbon($meetingMinute->start))) }}:{{ ((new \Carbon\Carbon($meetingMinute->end))->diff(new \Carbon\Carbon($meetingMinute->start)))->format('%I:%S') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <p style="text-align:center">
                <a href="{{ route('meeting_minutes.create') }}" class="btn btn-primary">Add</a>
            </p>

            <br/><br/>

            <!-- Peer Evaluation -->
            <h2 style="text-align: center;">Peer Evaluation</h2>
            <br/>
            @if(\App\PeerEvaluations::isOneActive())
                <p style="text-align: center;">Current Peer Evaluation: {{ \App\PeerEvaluations::active()->name }}</p>
            @endif

            @if(\App\PeerEvaluations::isOneActive() && !$user->hasSubmittedActivePeerEvaluation())
                <p style="text-align:center;">
                    <a href="{{ route('peer_evaluations.create') }}" class="btn btn-primary">Fill out your peer evaluation</a>
                </p>
            @elseif(\App\PeerEvaluations::isOneActive() && $user->getSubmittedActivePeerEvaluation()->pivot->display_to_user)
                <p style="text-align: center;">
                    <a href="{{ route('peer_evaluations.show', \App\PeerEvaluations::active()->id) }}" class="btn btn-primary">Save your most recent peer evaluation</a><br/><br/>
                    <a href="{{ route('peer_evaluations.edit', \App\PeerEvaluations::active()->id) }}" class="btn btn-primary">Uploaded to ELMS</a>
                </p>
            @elseif(\App\PeerEvaluations::isOneActive() && $user->getSubmittedActivePeerEvaluation()->pivot->display_to_user == 0)
                <p style="text-align: center;color:green;"><strong>Submitted</strong></p>
            @else
                <p style="text-align: center;color: red;">Not active</p>
            @endif
            <!-- End of Peer Evaluation -->

        @endif
    </div>
@endsection

@section('scripts')

    @if($user->isAdmin())
        <script>
            $('#peerEvaluations').DataTable();
            $('#meetingMinutes').DataTable();
        </script>
    @endif

    @if($user->isStudent())
        <script>
            $('#meetingMinutes').DataTable();
        </script>
    @endif

@endsection