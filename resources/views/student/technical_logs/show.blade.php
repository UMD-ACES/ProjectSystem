@extends('layouts.app')
<?php /** @var \App\TechnicalLog $technicalLog */?>

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

    <h1 style="text-align: center;">Technical Log</h1>
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
            <p>Category: {{ $technicalLog->category->name }}</p>
        </div>
        <div>
            <p><strong>Completed At:</strong> {{ (new Carbon\Carbon($technicalLog->completed_at))->toDayDateTimeString() }}</p>
        </div>
        <div class="form-group">
            <label for="description">Description:</label><br/>
            <textarea class="form-control" name="description" id="description" >{{ $technicalLog->description }}</textarea>
        </div>
    </div>
@endsection

@section('scripts')
    <script>

        window.onload = function()
        {
            createReadOnlyEditor('description');
        }


    </script>

@endsection