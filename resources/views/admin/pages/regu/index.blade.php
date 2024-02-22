@extends('admin.layout.template')

@section('title', 'Produksi Kepala Regu')

@section('content')

<div class="row mb-4">
    <div class="col"></div>
    <div class="col text-center py-2">
        <b>PRODUKSI KEPALA REGU</b>
    </div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">

        <div class="section">
            <div class="row mt-3">
                @if (User::setIjin(8))
                    <div class="col mb-3">
                        <a href="{{ route('regu.index', ['kategori'=> 'boneless']) }}" class="btn btn-primary btn-block">Kepala Regu Boneless</a>
                    </div>
                @endif
                @if (User::setIjin(9))
                    <div class="col mb-3">
                        <a href="{{ route('regu.index', ['kategori'=> 'parting']) }}" class="btn btn-primary btn-block">Kepala Regu Parting</a>
                    </div>
                @endif
                @if (User::setIjin(10))
                    <div class="col mb-3">
                        <a href="{{ route('regu.index', ['kategori'=> 'marinasi']) }}" class="btn btn-primary btn-block">Kepala Regu Parting M</a>
                    </div>
                @endif
                @if (User::setIjin(11))
                    <div class="col mb-3">
                        <a href="{{ route('regu.index', ['kategori'=> 'whole']) }}" class="btn btn-primary btn-block">Kepala Regu Whole Chicken</a>
                    </div>
                @endif
                @if (User::setIjin(12))
                    <div class="col mb-3">
                        <a href="{{ route('regu.index', ['kategori'=> 'frozen']) }}" class="btn btn-primary btn-block">Kepala Regu Frozen</a>
                    </div>
                @endif
                @if (User::setIjin(47) && env("NET_SUBSIDIARY", "CGL") == "CGL")
                    <div class="col mb-3">
                        <a href="{{ route('regu.index', ['kategori'=> 'meyer']) }}" class="btn btn-primary btn-block">Kepala Regu Meyer</a>
                    </div>
                @endif
                @if (User::setIjin(48))
                    <div class="col mb-3">
                        <a href="{{ route('regu.index', ['kategori'=> 'admin-produksi']) }}" class="btn btn-info btn-block">Kepala Regu Admin Produksi</a>
                    </div>
                @endif
            </div>
            <div class="row mt-4">
                <div class="col mb-3">
                    <a href="{{ route('index.thawing') }}" class="btn btn-green btn-block">Request Thawing</a>
                </div>
                <div class="col mb-3">
                    <a href="{{ route('chiller.index') }}" class="btn btn-warning btn-block">Chiller Bahan Baku</a>
                </div>
                <div class="col mb-3">
                    <a href="{{ route('hasilproduksi.index') }}" class="btn btn-warning btn-block">Chiller Finished Good</a>
                </div>
                <div class="col mb-3">
                    <a href="{{ route('chiller.soh') }}" class="btn btn-warning btn-block">Chiller SOH</a>
                </div>
                <div class="col mb-3">
                    <a href="{{ route('produksi.siap_kirim_export') }}" class="btn btn-green btn-block">Rekap Order</a>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
