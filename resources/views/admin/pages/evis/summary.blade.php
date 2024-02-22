@extends('admin.layout.template')

@section('title', 'Data Summary Evis')

@section('content')

<div class="row mb-4">
    <div class="col">
        <a href="{{ route('evis.index') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
            Back</a>
    </div>
    <div class="col6 py-1 text-center">
        <b>DATA TIMBANG EVIS</b>
    </div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">
        <form action="{{ route('evis.summary') }}" method="GET">
            <div class="row">
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                        Pencarian
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif name="tanggal" class="form-control change-date"
                            value="{{ $tanggal }}" id="pencarian" placeholder="Cari...." autocomplete="off">
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<section class="panel">
    <div class="card card-primary card-outline card-tabs">
        <ul class="nav nav-tabs" id="custom-tabs-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link tab-link active" id="custom-tabs-orders-tab" data-toggle="pill"
                    href="#custom-tabs-orders" role="tab" aria-controls="custom-tabs-orders" aria-selected="true">
                    Evis
                </a>
            <li class="nav-item">
                <a class="nav-link tab-link" id="custom-tabs-produksi-tab" data-toggle="pill"
                    href="#custom-tabs-produksi" role="tab" aria-controls="custom-tabs-produksi" aria-selected="false">
                    Produksi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link tab-link" id="custom-tabs-sampingan-tab" data-toggle="pill"
                    href="#custom-tabs-sampingan" role="tab" aria-controls="custom-tabs-sampingan"
                    aria-selected="false">
                    Jual Sampingan
                </a>
            </li>
        </ul>
        <div class="card-body">
            <div class="tab-content" id="custom-tabs-tabContent">
                <div class="tab-pane fade active show" id="custom-tabs-orders" role="tabpanel"
                    aria-labelledby="custom-tabs-orders-tab">
                    <table class="table default-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Ekor/Pcs/Pack</th>
                                <th>Berat Bersih</th>
                                <th>Hitung</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($evis as $i => $row)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $row->eviitem->nama ?? '' }}</td>
                                <td>{{ number_format($row->total) }}</td>
                                <td>{{ number_format($row->berat, 2) }}</td>
                                <td>{{ $row->jenis_evis }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="tab-pane fade" id="custom-tabs-produksi" role="tabpanel"
                    aria-labelledby="custom-tabs-produksi-tab">

                    <div class="card-body p-2">
                        <div class="row">
                            <div class="col-sm-4 col-lg mb-2 px-sm-1">
                                <div class="card">
                                    <div class="card-header text-info">Total Bahan Baku</div>
                                    <div class="card-body p-2">
                                        <div class="row mb-1">
                                            <div class="col pr-1">
                                                <div class="border text-center">
                                                    <div class="small">Pcs</div>
                                                    <div class="font-weight-bold">{{number_format($tot_bb_pcs)}} Pcs
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col pl-1">
                                                <div class="border text-center">
                                                    <div class="small">Berat</div>
                                                    <div class="font-weight-bold">{{number_format($tot_bb_kg,2)}} Kg
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4 col-lg mb-2 px-sm-1">
                                <div class="card">
                                    <div class="card-header text-info">Total Hasil Produksi</div>
                                    <div class="card-body p-2">
                                        <div class="row mb-1">
                                            <div class="col pr-1">
                                                <div class="border text-center">
                                                    <div class="small">Pcs</div>
                                                    <div class="font-weight-bold">{{number_format($tot_hp_pcs)}} Pcs
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col pl-1">
                                                <div class="border text-center">
                                                    <div class="small">Berat</div>
                                                    <div class="font-weight-bold">{{number_format($tot_hp_kg,2)}} Kg
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @foreach ($evisselesai as $row)
                    <div class="border border-dark p-2 mb-3">
                        <div class="row">
                            <div class="col-sm-6 pr-sm-1">
                                <table class="table default-table table-small">
                                    <thead>
                                        <tr>
                                            <th colspan="3" class="text-info">Bahan Baku</th>
                                        </tr>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Ekor/Pcs/Pack</th>
                                            <th>Berat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $total = 0 ;
                                        $berat = 0 ;
                                        @endphp
                                        @foreach ($row->listfreestock as $raw)
                                        @php
                                        $total += $raw->qty ;
                                        $berat += $raw->berat ;
                                        @endphp
                                        <tr>
                                            <td>{{ $raw->chiller->item_name }}</td>
                                            <td>{{ number_format($raw->qty) }} PCS</td>
                                            <td>{{ number_format($raw->berat, 2) }} Kg</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Total</th>
                                            <th>{{ number_format($total) }} PCS</th>
                                            <th>{{ number_format($berat, 2) }} Kg</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="col-sm-6 pl-sm-1">
                                <table class="table default-table table-small">
                                    <thead>
                                        <tr>
                                            <th colspan="3" class="text-info">Hasil Produksi</th>
                                        </tr>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Ekor/Pcs/Pack</th>
                                            <th>Berat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $total = 0 ;
                                        $berat = 0 ;
                                        @endphp
                                        @foreach ($row->freetemp as $raw)
                                        @php
                                        $exp = json_decode($raw->label) ;
                                        $total += $raw->qty ;
                                        $berat += $raw->berat ;
                                        @endphp
                                        <tr>
                                            <td>{{ $raw->item->nama }}</td>
                                            <td>{{ number_format($raw->qty) }} PCS</td>
                                            <td>{{ number_format($raw->berat, 2) }} Kg</td>
                                        </tr>
                                        @if ($raw->plastik_sku)
                                        <tr>
                                            <td colspan="3">
                                                <div class="status status-success">
                                                    <div class="row">
                                                        <div class="col pr-1">{{ $raw->plastik_nama }}</div>
                                                        <div class="col-auto pl-1">// {{ $raw->plastik_qty }} PCS</div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endif
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Total</th>
                                            <th>{{ number_format($total) }} PCS</th>
                                            <th>{{ number_format($berat, 2) }} Kg</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endforeach

                </div>
                <div class="tab-pane fade show" id="custom-tabs-sampingan" role="tabpanel"
                    aria-labelledby="custom-tabs-sampingan-tab">
                </div>
            </div>

        </div>
    </div>
</section>

<script>
    $('.change-date').change(function() {
            $(this).closest("form").submit();
        });
</script>
@stop