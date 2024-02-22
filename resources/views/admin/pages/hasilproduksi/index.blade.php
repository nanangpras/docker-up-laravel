@extends('admin.layout.template')

@section('title', 'Edit Hasil Produksi')

@section('content')

    <div class="row mb-4">
        <div class="col">
            <a href="{{ route('index') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
                Back</a>
        </div>
        <div class="col text-center">
            <b>EDIT PRODUKSI</b>
        </div>
        <div class="col"></div>
    </div>

    <section class="panel">
        <div class="card-body">

            <div id="show"></div>
        </div>
    </section>

    <script>
        $('#show').load("{{ route('hasilproduksi.show') }}");

    </script>
@stop
