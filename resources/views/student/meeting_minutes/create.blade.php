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

    <h1 style="text-align: center;">Meeting Minutes</h1>
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
    <form method="POST" action="{{ route('Student.meeting_minutes.store') }}">
        <p class="sectionTitle">Attendance</p>
        <div class="form-group">
            <label for="presentMembers">Present:</label>
            <select name="presentMembers[]" id="presentMembers" class="form-control" multiple>
                @foreach(\App\User::getAllStudents() as $student)
                    @if($user->id == $student->id)
                        <option value="{{ $student->id }}" selected>{{ $student->name }} ({{ $student->dirID }})</option>
                    @elseif(old('presentMembers') != null && in_array($student->id, old('presentMembers')))
                        <option value="{{ $student->id }}" selected>{{ $student->name }} ({{ $student->dirID }})</option>
                    @else
                        <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->dirID }})</option>
                    @endif
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="absentMembers">Absent:</label>
            <select name="absentMembers[]" id="absentMembers" class="form-control" multiple>
                @foreach(\App\User::getAllStudents() as $student)
                    @if(old('absentMembers') != null && in_array($student->id, old('absentMembers')))
                        <option value="{{ $student->id }}" selected>{{ $student->name }} ({{ $student->dirID }})</option>
                    @else
                        <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->dirID }})</option>
                    @endif
                @endforeach
            </select>
        </div>
        <br/><hr/><br/>
        <p class="sectionTitle">Meeting Time</p>
        <div class="form-group">
            <label for="start">Start Time:</label>
            @if(old('start'))
                <input type="text" class="form-control form_datetime" name="start" id="start" value="{{ old('start') }}" step="1"/>
            @else
                <input type="text" class="form-control form_datetime" name="start" id="start" value="{{ (\Carbon\Carbon::now())->format('Y-m-d H:i:s') }}" />
            @endif
        </div>
        <div class="form-group">
            <label for="end">End Time:</label>
            @if(old('end'))
                <input type="text" size="16" class="form-control form_datetime" name="end" id="end" value="{{ old('end') }}" step="1"/>
            @else
                <input type="text" size="16" class="form-control form_datetime" name="end" id="end" value="{{ (\Carbon\Carbon::now())->addHour()->format('Y-m-d H:i:s') }}" />
            @endif
        </div>
        <p class="sectionTitle">Meeting Items</p>

        @if($user->group->meetingMinutes->count() > 0)
            <div class="form-group">
                <label for="previousActionItems" style="font-size: 1.2em;font-weight: bold;">Action Items from your last meeting:</label>
                <textarea class="form-control" id="previousActionItems">{{ $user->group->lastMeetingMinute->first()->action_items }}</textarea>
            </div>
        @endif

        <div class="form-group">
            <label for="notes">Notes:</label><br/>
            <small>What is being discussed or worked on. (e.g. Decisions made, scripts made)  </small>
            @if(old('notes'))
                <textarea class="form-control" name="notes" id="notes" >{{ old('notes') }}</textarea>
            @else
                <textarea class="form-control" name="notes" id="notes"></textarea>
            @endif
        </div>
        <div class="form-group">
            <label for="actionItems">Action Items:</label><br/>
            <small>Items to be completed by next meeting (what and who) as well as any long term deadlines</small>
            @if(old('actionItems'))
                <textarea class="form-control" name="actionItems" id="actionItems" >{{ old('actionItems') }}</textarea>
            @else
                <textarea class="form-control" name="actionItems" id="actionItems"></textarea>
            @endif
        </div>
        <div class="form-group">
            <label for="nextMeeting">Next Meeting Date and Time:</label>
            @if(old('nextMeeting'))
                <input type="text" class="form-control form_datetime" name="nextMeeting" id="nextMeeting" value="{{ old('nextMeeting') }}"/>
            @else
                <input type="text" class="form-control form_datetime" name="nextMeeting" id="nextMeeting"  />
            @endif
        </div>

        {{ csrf_field() }}
        <br/>
        <button type="submit" class="btn btn-primary" onclick="return submitForm();">Submit</button>
    </form>
@endsection

@section('scripts')
    <script>

        window.onload = function()
        {
            $('#group').select2({
                placeholder: "Group"
            });

            $('#presentMembers').select2({
                placeholder: "Present Members"
            });

            $('#absentMembers').select2({
                placeholder: "Absent Members"
            });

            $("#start").datetimepicker({
                format: 'yyyy-mm-dd hh:ii:ss'
            });

            $("#end").datetimepicker({
                format: 'yyyy-mm-dd hh:ii:ss'
            });


            $("#nextMeeting").datetimepicker({
                format: 'yyyy-mm-dd hh:ii:ss'
            });

            createClassicEditor('notes');
            createClassicEditor('actionItems');
            createReadOnlyEditor('previousActionItems');
        }



    </script>

@endsection