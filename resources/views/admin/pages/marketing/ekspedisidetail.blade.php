@extends('admin.layout.template')

@section('title', 'Ekspedisi Pengiriman')

@section('content')
<div class="row mb-4">
    <div class="col">
        <a href="{{ route('marketing.index') }}" class="btn btn-outline-dark btn-sm"> <i class="fa fa-arrow-left"></i>
            Back</a>
    </div>
    <div class="col text-center">
        <b>EKSPEDISI PENGIRIMAN</b>
    </div>
    <div class="col"></div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <b>{{ $ekspedisi->ekspedisidriver->nama }}</b>
        <div class="row">
            <div class="col">
                <div>Status : Driver</div>
                <div>Wilayah : {{ $ekspedisi->wilayah->nama }}</div>
            </div>

            <div class="col">
                <div>No. Polisi : {{ $ekspedisi->ekspedisidriver->no_polisi }}</div>
                <div>Ekor : {{ number_format($ekspedisi->qty) }}</div>
            </div>

            <div class="col">
                <div>Berat : {{ number_format($ekspedisi->berat, 2) }}</div>
            </div>

            <div class="col"></div>
        </div>
    </div>
</div>

<div class="col text-center mb-3">
    <b>DELIVERY ROUTE</b>
</div>

@php
    $item   =   0;
    $berat  =   0;
    $qty    =   0;
@endphp
@foreach ($ekspedisi->ekspedisirute as $row)
<div class="card mb-4">
    <div class="card-header">
        <a href="{{ route('invoice', $row->order_id) }}" target="_blank" class="btn btn-light float-right py-0">Lihat Invoice</a>
        <b>Customer : {{ $row->nama }}</b>
    </div>
    <div class="card-body">
        Alamat : {{ $row->alamat }}

        <div class="bg-light p-2 mt-2">
            <div class="row">
                <div class="col">
                    <table class="tabletable-sm default-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Order</th>
                                <th>Fullfilment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($row->ruteorder->daftar_order as $list)
                            @php
                                $item   +=  1;
                                $berat  +=  $list->berat;
                                $qty    +=  $list->qty;
                            @endphp
                            <tr>
                                <td>{{ $list->nama_detail }}</td>
                                <td>{{ $list->qty }} | {{ $list->berat }}</td>
                                <td>{{ $list->fulfillment_qty }} | {{ $list->fulfillment_berat }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach


<div class="bg-white p-3">
    <div class="row">
        <div class="col-md-5">
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        Total Item
                        <input type="text" readonly class="form-control bg-white" value="{{ number_format($item) }}">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        Berat
                        <input type="text" readonly class="form-control bg-white" value="{{ number_format($berat, 2) }}">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        Qty
                        <input type="text" readonly class="form-control bg-white" value="{{ number_format($qty) }}">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2"></div>

    </div>
</div>

@stop
