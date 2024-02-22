@extends('admin.layout.template')

@section('title', 'Marketing')

@section('content')
<div class="my-4 text-center"><b>MARKETING</b></div>

<section class="panel">
    <div class="row">
        <div class="col-12 col-sm-12">
            <div class="card card-primary card-outline card-tabs">
                <ul class="nav nav-tabs" id="custom-tabs-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link tab-link" id="custom-tabs-fullfilment-tab" data-toggle="pill"
                            href="#custom-tabs-fullfilment" role="tab" aria-controls="custom-tabs-fullfilment"
                            aria-selected="true">Orders Fullfilment</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link tab-link" id="custom-tabs-ekspedisi-tab" data-toggle="pill"
                            href="#custom-tabs-ekspedisi" role="tab" aria-controls="custom-tabs-ekspedisi"
                            aria-selected="false">Rekap Ekspedisi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link tab-link" id="custom-tabs-retur-tab" data-toggle="pill"
                            href="#custom-tabs-retur" role="tab" aria-controls="custom-tabs-retur"
                            aria-selected="false">Rekap Retur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link tab-link" id="custom-tabs-stock-tab" data-toggle="pill"
                            href="#custom-tabs-stock" role="tab" aria-controls="custom-tabs-stock"
                            aria-selected="false">Stock</a>
                    </li>
                </ul>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-tabContent">
                        <div class="tab-pane fade active show" id="custom-tabs-fullfilment" role="tabpanel"
                            aria-labelledby="custom-tabs-fullfilment-tab">
                            <div class="">
                                <div class="col-12">
                                    <form action="{{ route('marketing.index') }}" method="get">
                                        <div class="form-group row">
                                            <label class="col-sm-7 col-form-label" style="text-align: right">Tanggal
                                                Kirim</label>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' )
                                                            min="2023-01-01" @endif name="tanggal" class="form-control"
                                                            value="{{ $tanggal ? $tanggal : date('Y-m-d') }}"
                                                            id="tglpotong">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <button type="submit"
                                                        class="btn btn-primary btn-block">FILTER</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm default-table  table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>Type</th>
                                                <th>Item</th>
                                                <th>Berat</th>
                                                <th>Status</th>
                                                <th>Proses</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($fulfillment as $i => $full)
                                            @php
                                            $berat = 0;
                                            $item = 0;
                                            @endphp
                                            @foreach ($full->daftar_order as $tot)
                                            @php
                                            $berat = $berat + $tot->berat;
                                            $item = $item + $tot->qty;
                                            @endphp
                                            @endforeach
                                            <tr>
                                                <td>{{ ++$i }}</td>
                                                <td>{{ $full->nama }}</td>
                                                <td></td>
                                                <td>{{ $item }} ekor</td>
                                                <td>{{ $berat }} Kg</td>

                                                <td>{!!$full->status_order!!}</td>

                                                <td>
                                                    <div class="progress">
                                                        @php
                                                        $cuk = '';
                                                        $persen = ($full->status *10);
                                                        if (($persen) < 50) { $cuk='bg-danger' ; } @endphp <div
                                                            class="progress-bar progress-bar-striped progress-bar-animated"
                                                            role="progressbar" aria-valuemin="0" aria-valuemax="100"
                                                            style="width: {{ $persen }}%">
                                                    </div>
                                </div>
                                {{ number_format($persen, 2) }} %
                                </td>
                                <td>
                                    <button type="submit" class="btn btn-primary btn-block btn-sm" data-toggle="modal"
                                        data-target="#modal{{ $full->id }}"">Detail</button>
                                                                </td>
                                                            </tr>

                                                            <div class=" modal fade" id="modal{{ $full->id }}"
                                        tabindex="-1" aria-labelledby="modal{{ $full->id }}Label" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modal{{ $full->id }}Label">
                                                        Customer :
                                                        {{ $full->ordercustomer->nama }}
                                                    </h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        @foreach ($full->daftar_order as $detail)
                                                        <div class="col-3">
                                                            &nbsp
                                                            <div class="radio-toolbar">
                                                                <input type="radio" id="do-{{ $detail->id }}" onclick=''
                                                                    data-jenis='' value="{{ $detail->id }}"
                                                                    name="purchase" required>
                                                                <label for="do-{{ $detail->id }}">
                                                                    WO#10000
                                                                </label>
                                                            </div>

                                                        </div>
                                                        <div class="col-6">
                                                            <label style="text-align: right">Order</label>
                                                            <div class="radio-toolbar">
                                                                <input type="radio" id="f-{{ $detail->id }}" onclick=''
                                                                    data-jenis='' value="{{ $detail->id }}"
                                                                    name="purchase" required>
                                                                <label for="f-{{ $detail->id }}">
                                                                    {{ $detail->item->nama }}
                                                                    <span class=" pull-right">
                                                                        Qty : <span class="label label-rounded-grey">{{ $detail->qty ?? '0' }}
                                                                        </span> &nbsp
                                                                        Berat : <span
                                                                            class="label label-rounded-grey">{{ $detail->berat ?? '0' }}
                                                                        </span> &nbsp
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-3">
                                                            <label>Fullfilment</label>
                                                            <div class="radio-toolbar">
                                                                <input type="radio" id="e-{{ $detail->id }}" onclick=''
                                                                    data-jenis='' value="{{ $detail->id }}"
                                                                    name="purchase" required>
                                                                <label for="e-{{ $detail->id }}">
                                                                    <span class=" pull-left">
                                                                        Qty : <span class="label label-rounded-grey">{{ $detail->fulfillment_qty ?? '0' }}
                                                                        </span> &nbsp
                                                                    </span>
                                                                    <span class=" pull-right">
                                                                        Berat : <span
                                                                            class="label label-rounded-grey">{{ $detail->fulfillment_berat ?? '0' }}
                                                                        </span> &nbsp
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-primary"
                                                        data-dismiss="modal">OK</button>
                                                </div>
                                            </div>
                                        </div>
                            </div>
                            @endforeach
                            </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade " id="custom-tabs-ekspedisi" role="tabpanel"
                    aria-labelledby="custom-tabs-ekspedisi-tab">
                    <div class="">
                        <div class="table-responsive">
                            <table class="table table-sm default-table  table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>No Polisi</th>
                                        <th>Nama Sopir</th>
                                        <th>Sales</th>
                                        <th>Customer</th>
                                        <th>Area</th>
                                        <th>Ekor/Pcs</th>
                                        <th>Berat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ekspedisi as $i => $eks)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ $eks->no_polisi }}</td>
                                        <td>{{ $eks->nama }}</td>
                                        <td>
                                            @foreach ($eks->eksrute as $detail)
                                            {{ $detail->ruteorder->ordercustomer->nama_marketing ?? ""}} <br>
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach ($eks->eksrute as $detail)
                                            {{ $detail->nama }} <br>
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach ($eks->eksrute as $detail)
                                            {{ $eks->wilayah->nama ?? '' }} <br>
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach ($eks->eksrute as $detail)
                                            @php
                                            $item = 0;
                                            @endphp
                                            @if($detail->ruteorder->daftar_order ?? false)
                                            @foreach ($detail->ruteorder->daftar_order as $cok)
                                            @php
                                            $item = $item + $cok->fulfillment_qty;
                                            @endphp
                                            @endforeach
                                            @endif
                                            {{ $item }}<br>
                                            @endforeach
                                        </td>
                                        </td>
                                        <td>
                                            @foreach ($eks->eksrute as $detail)
                                            @php
                                            $berat = 0;
                                            @endphp
                                            @if($detail->ruteorder->daftar_order ?? false)
                                            @foreach ($detail->ruteorder->daftar_order as $cok)
                                            @php
                                            $berat = $berat + $cok->fulfillment_berat;
                                            @endphp
                                            @endforeach
                                            @endif
                                            {{ $berat }}<br>
                                            @endforeach
                                        </td>
                                        <td>
                                            <a href="{{ url('marketing/detail', $eks->driver_id) }}"
                                                class="btn btn-primary btn-block btn-sm">Detail</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade " id="custom-tabs-retur" role="tabpanel"
                    aria-labelledby="custom-tabs-retur-tab">
                    <div class="">
                        <div class="table-responsive">
                            <table class="table table-sm default-table  table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Item</th>
                                        <th>QTY Retur</th>
                                        <th>Berat Retur</th>
                                        <th>Tujuan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($retur as $i => $ret)

                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ $ret->itemorder->nama }}</td>
                                        <th>{{ $ret->nama_detail }}</th>
                                        <td>{{ $ret->retur_qty }}</td>
                                        <td>{{ $ret->retur_berat }}</td>
                                        <td>{{ $ret->status_tujuan }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade " id="custom-tabs-stock" role="tabpanel"
                    aria-labelledby="custom-tabs-stock-tab">
                    <div id="chiller-stock"></div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
</section>

<script>
    $('#chiller-stock').load("{{route('chiller.stock')}}")
</script>

@stop