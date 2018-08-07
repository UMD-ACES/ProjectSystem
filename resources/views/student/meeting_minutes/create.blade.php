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
        <div class="form-group">
            <label for="group">Select your group:</label>
            <select name="group" id="group" class="form-control">
                <option></option>
                @foreach(\App\Group::all() as $group)
                    <option value="{{ $group->id }}" @if(old('group') == $group->id) selected @endif>{{ $group->name }} </option>
                @endforeach
            </select>
        </div>
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
                <input type="datetime-local" class="form-control" name="start" id="start" value="{{ old('start') }}" step="1"/>
            @else
                <input type="datetime-local" class="form-control" name="start" id="start" value="{{ (\Carbon\Carbon::now())->format('Y-m-d\TH:i:s') }}" step="1" />
            @endif
        </div>
        <div class="form-group">
            <label for="end">End Time:</label>
            @if(old('end'))
                <input type="datetime-local" class="form-control" name="end" id="end" value="{{ old('end') }}" step="1"/>
            @else
                <input type="datetime-local" class="form-control" name="end" id="end" value="{{ (\Carbon\Carbon::now())->addHour()->format('Y-m-d\TH:i:s') }}" step="1" />
            @endif
        </div>
        <p class="sectionTitle">Meeting Items</p>

        <p>Action Items from your last meeting</p>
        <!-- TODO: -->


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
                <input type="datetime-local" class="form-control" name="nextMeeting" id="nextMeeting" value="{{ old('nextMeeting') }}" step="1"/>
            @else
                <input type="datetime-local" class="form-control" name="nextMeeting" id="nextMeeting" value="" step="1" />
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

            createClassicEditor('notes');
            createClassicEditor('actionItems');
        }


    </script>

@endsection