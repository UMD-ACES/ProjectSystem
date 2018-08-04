@extends('layouts.app')
<?php /** @var \App\User $user */ ?>
<?php /** @var \App\PeerEvaluationsTeamMember $peerEvaluationsActiveTeamMember */ ?>

@section('stylesheets')

    <style>
        .sectionTitle
        {
            text-align:center;
            font-size: 2em;
            font-weight: bold;
        }
    </style>


@endsection

@section('content')

    <h1 style="text-align: center;">Peer Evaluation: {{ $user->getSubmittedActivePeerEvaluation()->name }}</h1>

    <div id="peerEvaluation">
        <p id="group" style="text-align: center; font-size: 1.5em;">Your group: {{ $user->getSubmittedActiveGroup()->name }}</p>

        <table id="teamMembers">
            <thead>
            <tr>
                <th>Team Member</th>
                <th>Contribution (%)</th>
            </tr>
            </thead>
            <tbody>
                @foreach($user->getSubmittedActivePeerEvaluationTeamMembers as $peerEvaluationsActiveTeamMember)
                    <tr>
                        <td>{{ $peerEvaluationsActiveTeamMember->teamMember->name }}</td>
                        <td>{{ $peerEvaluationsActiveTeamMember->grade }}%</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
            <tr>
                <th>Total:</th>
                <th>100%</th>
            </tr>
            </tfoot>
        </table>

        <div>
            <div class="sectionTitle"> Team Evaluation</div>
            <br/>
            <div class="form-group">
                <small>How well did your team work together? Explain in detail and provide examples if necessary. </small>
                <textarea class="form-control" id="team_evaluation"  disabled>{{ $user->getSubmittedActivePeerEvaluationTeam()->team_evaluation }}</textarea>
            </div>
        </div>

        @foreach($user->getSubmittedActivePeerEvaluationTeamMembers as $peerEvaluationsActiveTeamMember)
            <div id="teamMember_{{ $peerEvaluationsActiveTeamMember->teamMember->id }}">
                <br/><hr/><br/>
                <p class="sectionTitle">Team Member: {{ $peerEvaluationsActiveTeamMember->teamMember->name }}<br/><small>Contribution: <span id="teamMemberInfoGrade_' + id + '">{{ $peerEvaluationsActiveTeamMember->grade }}%</span></small></p>

                <div class="form-group">
                    <label for="grade_evaluation_{{ $peerEvaluationsActiveTeamMember->teamMember->id }}">Evaluate your team member's contribution to the project</label>
                    <textarea class="form-control" id="grade_evaluation_{{ $peerEvaluationsActiveTeamMember->teamMember->id }}" disabled>{{ $peerEvaluationsActiveTeamMember->grade_evaluation  }}</textarea>
                </div>

                <!-- Participation Table -->
                <table id="participation_table_{{ $peerEvaluationsActiveTeamMember->teamMember->id }}">
                    <thead>
                    <tr>
                        <th>Criterion</th>
                        <th>Strong</th>
                        <th>Ok</th>
                        <th>Weak</th>
                        <th>N/A</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($user->getSubmittedActivePeerEvaluationCriteria()->where('user_to_id', $peerEvaluationsActiveTeamMember->teamMember->id)->get() as $peerEvaluationCriterion)
                        <tr>
                            <td>{{ $peerEvaluationCriterion->humanName }}</td>
                            <td><input type="radio" value="Strong" onclick="return false" @if($peerEvaluationCriterion->pivot->value == 'Strong') checked @endif /></td>
                            <td><input type="radio" value="Ok" onclick="return false" @if($peerEvaluationCriterion->pivot->value == 'Ok') checked @endif /></td>
                            <td><input type="radio" value="Weak" onclick="return false" @if($peerEvaluationCriterion->pivot->value == 'Weak') checked @endif /></td>
                            <td><input type="radio" value="N/A" onclick="return false" @if($peerEvaluationCriterion->pivot->value == 'N/A') checked @endif /></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>
@endsection

@section('scripts')
    <script>
        let dataTableOptions = {
            paging: false
        };

        $('#teamMembers').DataTable(dataTableOptions);

        @foreach($user->getSubmittedActivePeerEvaluationTeamMembers as $peerEvaluationsActiveTeamMember)
            $('#participation_table_{{ $peerEvaluationsActiveTeamMember->teamMember->id }}').DataTable(dataTableOptions);
        @endforeach
    </script>

    <script>
        window.onload = function() {
            $("textarea").each(function(textarea) {
                $(this).height( $(this)[0].scrollHeight );
            });

            window.print();
        }

    </script>

@endsection