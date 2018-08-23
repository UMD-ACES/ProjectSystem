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

    <h1 style="text-align: center;">Technical Logs</h1>
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
    <form method="POST" action="{{ route('Student.technical_logs.store') }}">
        <div class="form-group">
            <label for="category">Category:</label>
            <select name="category" id="category" class="form-control">
                <option></option>
                @foreach(\App\TechnicalCategory::all() as $category)
                    @if(old('category') != null && old('category') == $category->id)
                        <option value="{{ $category->id }}" selected>{{ $category->name }}</option>
                    @else
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endif
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="completed_at">Completed At:</label>
            @if(old('completed_at'))
                <input type="text" class="form-control" name="completed_at" id="completed_at" value="{{ old('completed_at') }}"/>
            @else
                <input type="text" class="form-control" name="completed_at" id="completed_at" value="{{ (\Carbon\Carbon::now())->format('Y-m-d H:i:s') }}" />
            @endif
        </div>
        <div class="form-group">
            <label for="description">Description:</label><br/>
            @if(old('description'))
                <textarea class="form-control" name="description" id="description" >{{ old('description') }}</textarea>
            @else
                <textarea class="form-control" name="description" id="description"></textarea>
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
            $('#category').select2({
                placeholder: "Category"
            });

            $('#completed_at').datetimepicker({
                format: 'yyyy-mm-dd hh:ii:ss'
            });

            createClassicEditor('description');
        }


    </script>

@endsection