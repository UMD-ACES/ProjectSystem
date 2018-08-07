@extends('layouts.skeleton')

@section('head')
    <title>Project System - @yield('title')</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css"/>
    <style>
        body {
            padding-top: 5rem;
        }
        .template {
            padding: 3rem 1.5rem;
        }
        .ck-editor__editable {
            min-height: 200px;
        }
    </style>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <link href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet"/>
    @yield('stylesheets')
@endsection

@section('body')
    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
        <a class="navbar-brand" href="{{ route('home') }}">Group Project System</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarsExampleDefault">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="{{ url('/') }}">Home <span class="sr-only">(current)</span></a>
                </li>
                <li>
                    <a class="nav-link" href="{{ route('logout') }}">Logout</a>
                </li>
            </ul>
        </div>

    </nav>

    <main role="main" class="container">

        <div class="template">
            @yield('content')
        </div>

    </main><!-- /.container -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="/js/popper.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <!--<script src="/js/tinymce/tinymce.min.js"></script>-->
    <script src="https://cdn.ckeditor.com/ckeditor5/11.0.1/classic/ckeditor.js"></script>
    <!--<script src="https://cdn.ckeditor.com/ckeditor5/11.0.1/inline/ckeditor.js"></script>-->

    <script>
        let toolBarSetting = [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', "undo", "redo" ];

        function createClassicEditor(selector, id = true)
        {
            let element;

            if(id === true)
            {
                element = document.querySelector('#' + selector);
            }
            else
            {
                element = selector;
            }


            ClassicEditor
                .create( element, {
                    toolbar: toolBarSetting,
                })
                .then( editor => {
                    console.log( Array.from( editor.ui.componentFactory.names ) );

                    window.editor = editor;
                } )
                .catch( error => {
                    console.error( error );
                } );
        }

        function createReadOnlyEditor(selector, id = true)
        {
            let element;

            if(id === true)
            {
                element = document.querySelector('#' + selector);
            }
            else
            {
                element = selector;
            }


            ClassicEditor
                .create( element, {
                    toolbar: toolBarSetting,
                    //isReadOnly: true,

                })
                .then( editor => {
                    editor.isReadOnly = true;
                    console.log(editor);
                } )
                .catch( error => {
                    console.error( error );
                } );
        }
    </script>

    @yield('scripts')
@endsection