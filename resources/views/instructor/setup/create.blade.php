@extends('layouts.app')

@section('content')

    <h1 style="text-align: center;">Setup the System</h1>
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
    <form method="POST" action="{{ route('Admin.Setup.Store') }}">
        <ol>
            <li>Sign into Canvas/ELMS by going to myelms.umd.edu</li>
            <li>Go to the HACSXXX Class Page</li>
            <li>At the end of the URL or in the URL, there is a large number (aka course number), copy this number</li>
            <li>Go to https://myelms.umd.edu/api/v1/courses/{{ '<course number>' }}/students</li>
            <li>Copy paste the contents in that URL to the box below</li>
        </ol>
        <div class="form-group">
            <label for="students">Students:</label>
            <textarea name="students" class="form-control" id="students">{{ old('students') }}</textarea>
        </div>

        <ol>
            <li>Go to https://myelms.umd.edu/api/v1/courses/{{ '<course number>' }}/groups?per_page=100&page=1</li>
            <li>Copy paste the contents in that URL to the box below</li>
        </ol>
        <div class="form-group">
            <label for="groups">Groups:</label>
            <textarea name="groups" class="form-control" id="groups">{{ old('groups') }}</textarea>
        </div>

        {{ csrf_field() }}
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>



@endsection

