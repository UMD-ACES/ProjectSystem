@extends('layouts.app')
<?php /** @var \App\MeetingMinute $meetingMinute */?>
<?php /** @var \App\User $user */?>

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
    <div>
        <div>
            <p style="text-align: center;font-size: 1.5em;">Your group: {{ $meetingMinute->group->name }}</p>
        </div>
        <p class="sectionTitle">Attendance</p>
        <div>
            <p style="font-size: 1.2em;">Present Members:</p>
            <ul>
                @foreach($meetingMinute->attendance()->get() as $meetingMinuteMember)
                    @if($meetingMinuteMember->present)
                        <li>{{ $meetingMinuteMember->user->name }}</li>
                    @endif
                @endforeach
            </ul>
            @if($meetingMinute->attendance()->where('present', 0)->count() > 0)
                <p style="font-size: 1.2em;">Absent Members:</p>
                <ul>
                    @foreach($meetingMinute->attendance()->get() as $meetingMinuteMember)
                        @if($meetingMinuteMember->present == 0)
                            <li>{{ $meetingMinuteMember->user->name }}</li>
                        @endif
                    @endforeach
                </ul>
            @endif
        </div>

        <br/><hr/><br/>
        <div>
            <p class="sectionTitle">Meeting Time</p>
            <p><strong>Start Date and Time:</strong> {{ (new Carbon\Carbon($meetingMinute->start))->toDayDateTimeString() }}</p>
            <p><strong>End Date and Time:</strong> {{ (new Carbon\Carbon($meetingMinute->end))->toDayDateTimeString() }}</p>
        </div>

        <p class="sectionTitle">Meeting Items</p>
        <div class="form-group">
            <label for="notes">Notes:</label><br/>
            <small>What is being discussed or worked on. (e.g. Decisions made, scripts made)  </small>
            <textarea class="form-control" name="notes" id="notes" >{{ $meetingMinute->notes }}</textarea>
        </div>
        <div class="form-group">
            <label for="actionItems">Action Items:</label><br/>
            <small>Items to be completed by next meeting (what and who) as well as any long term deadlines</small>
            <textarea class="form-control" name="actionItems" id="actionItems" >{{ $meetingMinute->action_items }}</textarea>
        </div>
        <div class="form-group">
            <strong>Next Meeting Date and Time:</strong> {{ (new Carbon\Carbon($meetingMinute->next_meeting))->toDayDateTimeString() }}
        </div>
    </div>
@endsection

@section('scripts')
    <script>

        window.onload = function()
        {
            createReadOnlyEditor('notes');
            createReadOnlyEditor('actionItems');
        }


    </script>

@endsection