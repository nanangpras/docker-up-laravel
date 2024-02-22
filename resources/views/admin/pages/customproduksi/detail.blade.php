@extends('admin.layout.template')

@section('title', 'Custom Produksi')

@section('header')
    <style>


    </style>
@endsection

@section('content')

    <div class="row mb-4">
        <div class="col">
            <a href="{{ route('kepalaregu.index') }}" class="btn btn-outline btn-sm btn-back"> <i
                    class="fa fa-arrow-left"></i>
                Back</a>
        </div>
        <div class="col text-center">
            <b>CUSTOM PRODUKSI</b>
        </div>
        <div class="col"></div>
    </div>

    <section class="panel">
        <div class="card-body">
            Hasil Produksi {{$client}} <br><br>
            <div id="summary"></div>

        </div>
    </section>

    <style>
        .button-menu a{
            margin-bottom: 15px;
        }
    </style>
@stop


@section('footer')
    <script>
        var url_route_sumary = "{{ route('customproduksi.summary', ['client'=> $client]) }}";

        $("#summary").load(url_route_sumary);
    </script>
@endsection