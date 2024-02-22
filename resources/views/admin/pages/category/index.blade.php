@extends('admin.layout.template')

@section('title', 'Category')

@section('header')
    <style>


    </style>
@endsection

@section('content')

    <div class="row mb-4">
        <div class="col">
            <a href="" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
                Back</a>
        </div>
        <div class="col text-center">
            <b>CATEGORY</b>
        </div>
        <div class="col"></div>
    </div>

    <div id="data_show"></div>

    <script>
        $("#data_show").load("{{ route('category.show') }}");

    </script>

@stop
