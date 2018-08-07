@extends('layouts.app')

@section('content')

    <h1 style="text-align: center;">Setup your account</h1>
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
            Successfully created!
        </div>
    @endif
    <form method="POST" action="{{ route('Student.Setup.Store') }}">
        <div class="form-group">
            <label for="group">Select your group:</label>
            <select name="group" id="group" class="form-control">
                <option></option>
                @foreach(\App\Group::all() as $group)
                    <option value="{{ $group->id }}" @if(old('group') == $group->id) selected @endif>{{ $group->name }} </option>
                @endforeach
            </select>
        </div>

        {{ csrf_field() }}
        <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure that this is your group?');">Submit</button>
    </form>
@endsection

@section('scripts')
    <script>
        $('#group').select2({
            placeholder: 'Group'
        });
    </script>
@endsection

