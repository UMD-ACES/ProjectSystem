@extends('layouts.app')

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

    <h1 style="text-align: center;">Peer Evaluation: {{ \App\PeerEvaluations::active()->name }}</h1>
    <br/>
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
            Success!
        </div>
    @endif
    <form method="POST" action="{{ route('peer_evaluations.store') }}">
        <div class="form-group">
            <label for="group">Select your group:</label>
            <select name="group" id="group" class="form-control">
                <option></option>
                @foreach(\App\Group::all() as $group)
                    <option value="{{ $group->id }}" @if(old('group') == $group->id) selected @endif>{{ $group->name }} </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="teamMembers">Select your team members (including yourself):</label>
            <select name="teamMembers[]" id="teamMembers" class="form-control" multiple>
                @foreach(\App\User::getAllStudents() as $student)
                    @if($user->id == $student->id)
                        <option value="{{ $student->id }}" selected>{{ $student->name }} ({{ $student->dirID }})</option>
                    @elseif(old('teamMembers') != null && in_array($student->id, old('teamMembers')))
                        <option value="{{ $student->id }}" selected>{{ $student->name }} ({{ $student->dirID }})</option>
                    @else
                        <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->dirID }})</option>
                    @endif
                @endforeach
            </select>
        </div>
        <br/><hr/><br/>
        <p class="sectionTitle">Team Dynamic</p>
        <p>In the contribution column, please <strong>apportion 100 points</strong> among each of the different team members (including yourself) according to <span style="font-style: italic;">your</span> subjective assessment of each person’s <span style="text-decoration: underline;">contribution</span>.</p>
        <div class="form-group">
            <table id="teamContribution">
                <thead>
                    <tr>
                        <th>Team Member</th>
                        <th>Contribution (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $user->name }} ({{ $user->dirID }})</td>
                        <td><input type="number" class="grade" name="grade_member_{{ $user->id }}" id="grade_member_{{ $user->id }}" value="{{ old('grade_member_'.$user->id) }}" onblur="setContributionGrade('{{ $user->id }}');"/></td>
                    </tr>
                    @if(old('teamMembers') != null)
                        @foreach(old('teamMembers') as $teamMember)
                            @unless($teamMember == $user->id)
                                <tr>
                                    <td>{{ \App\User::find($teamMember)->name }} ({{ \App\User::find($teamMember)->dirID }})</td>
                                    <td><input type="number" class="grade" name="grade_member_{{ \App\User::find($teamMember)->id }}" id="grade_member_{{ \App\User::find($teamMember)->id }}" value="{{ old('grade_member_'.\App\User::find($teamMember)->id) }}" onblur="setContributionGrade('{{ \App\User::find($teamMember)->id }}');"/></td>
                                </tr>
                            @endunless
                        @endforeach
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <th>Total should be:</th>
                        <th>100%</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <br/>
        <div class="form-group">
            <label for="team_evaluation">Evaluate your team:</label><br/>
            <small>How well did your team work together? Explain in detail and provide examples if necessary. </small>
            <textarea class="form-control" name="team_evaluation" id="team_evaluation" style="height: 300px;">{{ old('team_evaluation') }}</textarea>
        </div>

        <br/><hr/><br/>

        <!-- Team Member Evaluations -->
        <div id="teamMemberEvaluations">
            <div id="teamMember_{{ $user->id }}">
                <p class="sectionTitle">Evaluate yourself</p>
                <!-- Grade Evaluation -->
                <div class="form-group">
                    <label for="grade_evaluation_{{ $user->id }}">Evaluate your own contribution to the team</label>
                    <textarea class="form-control" name="grade_evaluation_{{ $user->id }}" id="grade_evaluation_{{ $user->id }}" rows="5">{{ old('grade_evaluation_'.$user->id) }}</textarea>
                </div>

                <!-- Participation Table -->
                <table id="participation_table_{{ $user->id }}">
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
                    @foreach(\App\Criterion::all() as $criterion)
                        <tr>
                            <td>{{ $criterion->humanName }}</td>
                            <td><input type="radio" name="{{ $criterion->name }}_{{ $user->id }}" value="Strong" @if(old($criterion->name.'_' . $user->id) == 'Strong') checked @endif/></td>
                            <td><input type="radio" name="{{ $criterion->name }}_{{ $user->id }}" value="Ok" @if(old($criterion->name.'_' . $user->id) == 'Ok') checked @endif/></td>
                            <td><input type="radio" name="{{ $criterion->name }}_{{ $user->id }}" value="Weak" @if(old($criterion->name.'_' . $user->id) == 'Weak') checked @endif/></td>
                            <td><input type="radio" name="{{ $criterion->name }}_{{ $user->id }}" value="N/A" @if(old($criterion->name.'_' . $user->id) == 'N/A') checked @endif/></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @if(old('teamMembers') != null) @foreach(old('teamMembers') as $teamMemberID) @unless($teamMemberID == $user->id)
                <div id="teamMember_{{ $teamMemberID }}">
                    <br/><hr/><br/>
                    <p class="sectionTitle">Team Member: {{ \App\User::find($teamMemberID)->name }}<br/><small>Contribution: <span id="teamMemberInfoGrade_{{ $teamMemberID }}">@if(old('grade_member_'.$teamMemberID)) {{ old('grade_member_'.$teamMemberID) }}% @else TBD @endif</span></small></p>

                    <div class="form-group">
                        <label for="grade_evaluation_{{ $teamMemberID }}">Evaluate your team member's contribution to the project</label>
                        <textarea class="form-control" name="grade_evaluation_{{ $teamMemberID }}" id="grade_evaluation_{{ $teamMemberID }}" rows="5">{{ old('grade_evaluation_'.$teamMemberID) }}</textarea>
                    </div>

                    <!-- Participation Table -->
                    <table id="participation_table_{{ $teamMemberID }}">
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
                        @foreach(\App\Criterion::all() as $criterion)
                            <tr>
                                <td>{{ $criterion->humanName }}</td>
                                <td><input type="radio" name="{{ $criterion->name }}_{{ $teamMemberID }}" value="Strong" @if(old($criterion->name.'_' . $teamMemberID) == 'Strong') checked @endif/></td>
                                <td><input type="radio" name="{{ $criterion->name }}_{{ $teamMemberID }}" value="Ok" @if(old($criterion->name.'_' . $teamMemberID) == 'Ok') checked @endif/></td>
                                <td><input type="radio" name="{{ $criterion->name }}_{{ $teamMemberID }}" value="Weak" @if(old($criterion->name.'_' . $teamMemberID) == 'Weak') checked @endif/></td>
                                <td><input type="radio" name="{{ $criterion->name }}_{{ $teamMemberID }}" value="N/A" @if(old($criterion->name.'_' . $teamMemberID) == 'N/A') checked @endif/></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                    @endunless
                @endforeach
            @endif
        </div>

        {{ csrf_field() }}
        <br/>
        <button type="submit" class="btn btn-primary" onclick="return submitForm();">Submit</button>
    </form>
@endsection

@section('scripts')
    <script>

        var teamContributionTable;
        // DataTable setup
        var dataTableOptions = {
            paging: false
        };

        window.onload = function()
        {
            // Select2 setup
            $('#group').select2({
                placeholder: "Group:"
            });
            $('#teamMembers').select2();



            teamContributionTable = $("#teamContribution").DataTable(dataTableOptions);
            createClassicEditor('team_evaluation');
            $('#participation_table_' + '{{ $user->id }}').DataTable(dataTableOptions);
            createClassicEditor('grade_evaluation_' + '{{ $user->id }}');

            @if(old('teamMembers') != null)
                @foreach(old('teamMembers') as $teamMemberID)
                    @unless($teamMemberID == $user->id)
                        $('#participation_table_{{ $teamMemberID }}').DataTable(dataTableOptions);
                    @endunless
                @endforeach
            @endif
        }




        // Flags
        var submittingForm = false;
    </script>

    <script>
        $('#teamMembers').on('select2:select', function(e) {
            let data = e.params.data;
            addTeamMember(data.id, data.text)
        });


        $('#teamMembers').on('select2:unselect', function(e) {
            let data = e.params.data;
            deleteTeamMember(data.id);
        })
    </script>

    <script>
        function addTeamMember(id, name)
        {
            teamContributionTable.row.add([
                name,
                '<input type="number" name="grade_member_' + id + '" id="grade_member_' + id + '" onblur="setContributionGrade(' + id + ');"/>'
            ]).draw( false );

            let teamMemberInfo = '<p class="sectionTitle">Team Member: ' + name + '<br/><small>Contribution: <span id="teamMemberInfoGrade_' + id + '">TBD</span></small></p>';

            let grade_evaluation =
                '<div class="form-group">\n' +
                '   <label for="grade_evaluation_' + id + '">Evaluate your team member\'s contribution to the project</label>' +
                '   <textarea class="form-control" name="grade_evaluation_' + id + '" id="grade_evaluation_' + id + '" rows="5"></textarea>' +
                '</div>';

            let participation_table =
                '<table id="participation_table_' + id + '">' +
                '   <thead>' +
                '       <tr>' +
                '           <th>Criterion</th>' +
                '           <th>Strong</th>' +
                '           <th>Ok</th>' +
                '           <th>Weak</th>' +
                '           <th>N/A</th>' +
                '       </tr>' +
                '       </thead>' +
                '       <tbody>';

                @foreach(\App\Criterion::all() as $criterion)
                participation_table += '<tr>' +
                '                   <td>{{ $criterion->humanName }}</td>\n' +
                '                   <td><input type="radio" name="{{ $criterion->name }}_' + id + '" value="Strong" /></td>' +
                '                   <td><input type="radio" name="{{ $criterion->name }}_' + id + '" value="Ok" /></td>' +
                '                   <td><input type="radio" name="{{ $criterion->name }}_' + id + '" value="Weak" /></td>' +
                '                   <td><input type="radio" name="{{ $criterion->name }}_' + id + '" value="N/A" /></td>' +
                '               </tr>';

                @endforeach

            participation_table += '</tbody>' +
                '   </table>';


            var teamMemberDiv = '<div id="teamMember_' + id + '"><br/><hr/><br/>' + teamMemberInfo + grade_evaluation + participation_table + '</div>';

            $('#teamMemberEvaluations').append(teamMemberDiv);
            $('#participation_table_' + id).DataTable(dataTableOptions);
            createClassicEditor('grade_evaluation_' + id);


        }


        function setContributionGrade(id)
        {
            let grade = $('#grade_member_' + id).val();

            if(grade === '')
            {
                $('#teamMemberInfoGrade_' + id).html('TBD');
            }
            else
            {
                $('#teamMemberInfoGrade_' + id).html(grade + '%');
            }
        }

        function deleteTeamMember(id)
        {
            // Contribution Table remove (DataTables)
            teamContributionTable.row($('#grade_member_' + id).parents('tr')).remove().draw(false);

            $('#teamMember_' + id).remove();

        }

    </script>

    <script>
        function submitForm()
        {
            submittingForm = true;
            return true;
        }
        $(window).bind('beforeunload', function(){
            if(submittingForm)
            {
                return undefined;
            }

            return 'Are you sure you want to leave?';
        });
    </script>

    <script>
       /* tinymce.init({ selector:'textarea',
            theme: 'modern',
            height: 300,
            plugins: [
                'advlist autolink link lists charmap print preview hr anchor pagebreak',
                'searchreplace wordcount visualblocks visualchars code insertdatetime nonbreaking',
                'save table contextmenu directionality emoticons paste textcolor'
            ],
            content_css: 'css/content.css',

        });*/

       /* Editor Configuration */

    </script>

@endsection