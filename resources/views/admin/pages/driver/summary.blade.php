@extends('admin.layout.template')

@section('title', 'Ekspedisi Pengiriman')

@section('content')
<div class="row mb-4">
    <div class="col">
        <a href="{{ route('driver.index') }}"  class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
            Back</a>
    </div>
    <div class="col text-center">
        <b>EKSPEDISI PENGIRIMAN</b>
    </div>
    <div class="col"></div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <b>{{ $data->nama }}</b>
        <div class="row">
            <div class="col">
                <div>Status : Driver</div>
                <div>Wilayah : {{ $ekspedisi->wilayah->nama }}</div>
            </div>

            <div class="col">
                <div>No. Polisi : {{ $data->no_polisi }}</div>
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

<section class="panel">
    <div class="card-body">
        <table class="table default-table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Item</th>
                    <th>SO Qty</th>
                    <th>SO Berat</th>
                    <th>FU Qty</th>
                    <th>FU Berat</th>
                    {{-- <th>Retur</th> --}}
                    <th>Selesaikan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ekspedisi->ekspedisirute as $row)
                <tr>
                    <td>{{ $row->nama }}</td>
                    <td>{{ $row->ruteorderitem->nama_detail  ?? "###" }}</td>
                    <td>{{ $row->ruteorderitem->qty  ?? "###"}}</td>
                    <td>{{ $row->ruteorderitem->berat  ?? "###"}} Kg</td>
                    <td>{{ $row->ruteorderitem->fulfillment_qty  ?? "###"}}</td>
                    <td>{{ $row->ruteorderitem->fulfillment_berat  ?? "###"}} Kg</td>
                    {{-- <td>
                        <a href="{{ route('driver.retur', $row->order_id) }}" class="btn btn-danger btn-sm">Retur</a>
                        </td> --}}
                    <td>
                        <form action="{{ route('driver.ready', $ekspedisi->driver_id) }}" method="post">
                            @csrf @method('patch') <input type="hidden" name="x_code" value="{{ $row->order_id }}"">
                            <button class="btn btn-success btn-sm">Selesaikan</button>
                        </form>
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>
    </div>
</section>


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
        <div class="col-md-5">
            <div class="row">
                <div class="col">
                    &nbsp;
                    <a href="{{ route('delivery', $ekspedisi->id) }}" class="btn btn-block btn-outline-primary">Lihat DO</a>
                </div>
                <div class="col">
                    &nbsp;
                    <form action="{{ route('driver.complete', $ekspedisi->id) }}" method="post">
                        @csrf @method('put')
                        <button type="submit" class="btn btn-block btn-primary">Selesaikan Semua</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@stop
