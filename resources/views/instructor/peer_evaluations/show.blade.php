@extends('layouts.app')
<?php /** @var \App\PeerEvaluations $peerEvaluation */ ?>
<?php /** @var \App\User $teamMember */ ?>

@section('stylesheets')
    <style>
        .sectionTitle
        {
            text-align:center;
            font-size: 1.5em;
        }
    </style>
@endsection

@section('content')
    <h1 style="text-align:center;">Instructor View: {{ $peerEvaluation->name }}</h1>

    <form method="GET" id="groupSelect" action="{{ route('Admin.peer_evaluations.show', $peerEvaluation->id ) }}">

        <div class="form-group">
            <label for="group">Group:</label>
            <select name="group" id="group" class="form-control" onchange="$('#groupSelect').submit();">
                <option></option>
                @foreach(\App\Group::all() as $groupOption)
                    <option value="{{ $groupOption->id }}" @if(isset($group) && $group->id == $groupOption->id) selected @endif>{{ $groupOption->name }} </option>
                @endforeach
            </select>
        </div>
    </form>
    <br/>
    @if(isset($group) && $group != null && $peerEvaluation->getAllTeamMembers($group)->count() > 0)

        <table id="contribution_grades">
            <thead>
                <tr>
                    <th>From - To</th>
                    @foreach($peerEvaluation->getAllTeamMembers($group) as $teamMember)
                        <th>{{ $teamMember->name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($peerEvaluation->getAllTeamMembers($group) as $teamMember)
                    <tr>
                        <td><strong>{{ $teamMember->name }}</strong></td>
                        @foreach($peerEvaluation->getAllTeamMembers($group) as $teamMemberTo)
                            @if($teamMember->getSubmittedPeerEvaluationTeamMember($peerEvaluation, $teamMemberTo) != null)
                                <td><a href="#" data-toggle="modal" data-target="#teamMemberEvaluation_{{ $teamMember->id }}_{{ $teamMemberTo->id }}">{{ $teamMember->getSubmittedPeerEvaluationTeamMember($peerEvaluation, $teamMemberTo)->grade }}%</a></td>
                            @else
                                <td>Not Submitted</td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br/>
        <h2 style="text-align:center;">Team Evaluation</h2>
        @foreach($peerEvaluation->getTeamMembers($group) as $teamMember)
            <p class="sectionTitle">{{ $teamMember->name }}</p>
            <div class="form-group">
                <small>How well did your team work together? Explain in detail and provide examples if necessary. </small>
                <textarea class="form-control textarea-editor" style="background-color:white" readonly>{{ $teamMember->getSubmittedPeerEvaluationTeam($peerEvaluation)->team_evaluation }}</textarea>
            </div>
        @endforeach

        <input type="button" onclick="individualEvaluations(this);" class="btn btn-primary" value="Show individual evaluations"/>

        <div id="teamMemberEvaluation" style="display: none;">
            @foreach($peerEvaluation->getAllTeamMembers($group) as $teamMember)
                <div>
                @foreach($peerEvaluation->getAllTeamMembers($group) as $teamMemberTo)

                    @if($teamMember->getSubmittedPeerEvaluationTeamMember($peerEvaluation, $teamMemberTo))

                    <p class="sectionTitle">From: {{ $teamMember->name }} To {{ $teamMemberTo->name }} <br/><small>Contribution: <span>{{ $teamMember->getSubmittedPeerEvaluationTeamMember($peerEvaluation, $teamMemberTo)->grade }}%</span></small></p>
                    <div class="form-group">
                        <label>Evaluate your team member's contribution to the project</label>
                        <textarea class="form-control textarea-editor" style="background-color:white" readonly>{{ $teamMember->getSubmittedPeerEvaluationTeamMember($peerEvaluation, $teamMemberTo)->grade_evaluation  }}</textarea>
                    </div>

                        <!-- Participation Table -->
                        <table>
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
                            @foreach($teamMember->getSubmittedPeerEvaluationCriteria($peerEvaluation, $teamMemberTo) as $peerEvaluationCriterion)
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
                        @else
                            <p class="sectionTitle">From: {{ $teamMember->name }} To {{ $teamMemberTo->name }} <br/><small>No Submission</small></p>
                        @endif
                @endforeach
                </div>
            @endforeach
        </div>


        @foreach($peerEvaluation->getAllTeamMembers($group) as $teamMember)
            <div>
                @foreach($peerEvaluation->getAllTeamMembers($group) as $teamMemberTo)
                    <div class="modal fade" id="teamMemberEvaluation_{{ $teamMember->id }}_{{ $teamMemberTo->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-body">
                                    @if($teamMember->getSubmittedPeerEvaluationTeamMember($peerEvaluation, $teamMemberTo))
                                    <p class="sectionTitle">From {{ $teamMember->name }} To {{ $teamMemberTo->name }} <br/><small>Contribution: <span>{{ $teamMember->getSubmittedPeerEvaluationTeamMember($peerEvaluation, $teamMemberTo)->grade }}%</span></small></p>
                                    <div class="form-group">
                                        <label><small>Evaluate your team member's contribution to the project</small></label>
                                        <textarea class="form-control textarea-editor" style="background-color:white" readonly>{{ $teamMember->getSubmittedPeerEvaluationTeamMember($peerEvaluation, $teamMemberTo)->grade_evaluation  }}</textarea>
                                    </div>

                                    <!-- Participation Table -->
                                    <table>
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
                                        @foreach($teamMember->getSubmittedPeerEvaluationCriteria($peerEvaluation, $teamMemberTo) as $peerEvaluationCriterion)
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
                                    @else
                                        <p class="sectionTitle">From: {{ $teamMember->name }} To {{ $teamMemberTo->name }} <br/><small>No Submission</small></p>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach


    @elseif(isset($group) && $group != null && $peerEvaluation->getAllTeamMembers($group)->count() == 0)
        <p style="color:red;text-align: center;">Nothing submitted</p>
    @endif

@endsection

@section('scripts')

    <script>
        $('#group').select2({
            placeholder: "Group:"
        });

        let dataTableOptions = {
            paging: false
        };

        window.onload = function() {
            var allEditors = document.querySelectorAll('.textarea-editor');
            for (var i = 0; i < allEditors.length; ++i) {
                createClassicEditor(allEditors[i], false);
            }
        }


        //let scrollHeightThreshold = 0;
        //let scrollHeightIndividualSet = 0;
        //let scrollHeightModals = [];

        $('table').DataTable(dataTableOptions);

        /*$("textarea").each(function(textarea) {
            if($(this)[0].scrollHeight > scrollHeightThreshold)
            {
                $(this).height( $(this)[0].scrollHeight );
            }
        });*/

        $('.modal').on('shown.bs.modal', function (e) {

            /*if(!scrollHeightModals.includes($(e.target).attr('id')))
            {
                let height = $(e.target).find('textarea')[0].scrollHeight;
                console.log(height);

                $(e.target).find('textarea').height(height);

                scrollHeightModals.push($(e.target).attr('id'));
            }*/
        })
    </script>

    <script>
        function individualEvaluations(el)
        {
            if($(el).val() === 'Show individual evaluations')
            {
                $('#teamMemberEvaluation').show();
                $(el).val('Hide individual evaluations');

                /*if(scrollHeightIndividualSet === 0)
                {
                    $("#teamMemberEvaluation textarea").each(function(textarea) {
                        $(this).height( $(this)[0].scrollHeight );
                    });
                }

                scrollHeightIndividualSet = 1;*/
            }
            else
            {
                $('#teamMemberEvaluation').hide();
                $(el).val('Show individual evaluations');
            }
        }


    </script>


@endsection