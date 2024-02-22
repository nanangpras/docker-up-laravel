@extends('admin.layout.template')

@section('title', 'Stock Chiller')

@section('content')

    <div class="row mb-4">
        <div class="col">
            <a href="{{ route('index') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
                Back</a>
        </div>
        <div class="col text-center">
            <b>STOCK CHILLER</b>
        </div>
        <div class="col"></div>
    </div>

    <section class="panel">
        <div class="card-body">
            <div id="chiller-stock"></div>
        </div>
    </section>

    <script>
        $('#chiller-stock').load("{{ route('chiller.showstock') }}")
    </script>
@stop
