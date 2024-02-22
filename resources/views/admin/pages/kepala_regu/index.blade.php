@extends('admin.layout.template')

@section('title', 'Kepala Regu')

@section('header')
    <style>


    </style>
@endsection

@section('content')

    <div class="row mb-4">
        <div class="col">
            <a href="{{ route('index') }}" class="btn btn-outline btn-sm btn-back"> <i
                    class="fa fa-arrow-left"></i>
                Back</a>
        </div>
        <div class="col text-center py-2">
            <b>KEPALA REGU</b>
        </div>
        <div class="col"></div>
    </div>

    <section class="panel">
        <div class="card-body">

            <div class="section">
                <div class="form-group row button-menu">
                    @if (User::setIjin(8))
                        <div class="col">
                            <a href="{{ route('produksi.index', ['regu'=> 'boneless']) }}" class="btn btn-primary btn-block">
                                Kepala Regu Boneles</a>
                        </div>
                    @endif
                    @if (User::setIjin(9))
                        <div class="col">
                            <a href="{{ route('produksi.index', ['regu'=> 'parting']) }}" class="btn btn-primary btn-block">
                                Kepala Regu Parting</a>
                        </div>
                    @endif
                    @if (User::setIjin(10))
                        <div class="col">
                            <a href="{{ route('produksi.index', ['regu'=> 'marinasi']) }}" class="btn btn-primary btn-block">
                                Kepala Regu Parting M</a>
                        </div>
                    @endif
                    @if (User::setIjin(11))
                        <div class="col">
                            <a href="{{ route('produksi.index', ['regu'=> 'whole']) }}" class="btn btn-primary btn-block">
                                Kepala Regu Whole Chicken</a>
                        </div>
                    @endif
                    @if (User::setIjin(12))
                        <div class="col">
                            <a href="{{ route('produksi.index', ['regu'=> 'frozen']) }}" class="btn btn-primary btn-block">
                                Kepala Regu Frozen</a>
                        </div>
                    @endif
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
