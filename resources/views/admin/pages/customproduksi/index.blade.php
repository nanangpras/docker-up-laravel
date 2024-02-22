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

            <div class="section">
                <div class="form-group row button-menu">
                    <div class="col">
                        <a href="{{ route('customproduksi.detail', ['client'=> 'meyerfood']) }}" class="btn btn-primary btn-block">
                            Produksi Meyer</a>
                    </div>

                </div>
            </div>

        </div>
    </section>

    <style>
        .button-menu a{
            margin-bottom: 15px;
        }
    </style>
@stop
