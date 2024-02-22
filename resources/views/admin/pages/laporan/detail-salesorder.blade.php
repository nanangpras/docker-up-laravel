@extends('admin.layout.template')

@section('title', 'Detail Sales Order')

@section('content')
<div class="row mb-4">
    <div class="col">
        <a href="javascript:void(0)" class="btn btn-outline btn-sm btn-back" onclick="return history.back()"> <i class="fa fa-arrow-left"></i>
            Back</a>
    </div>
    <div class="col text-center">
        <b>Detail Sales Order</b>
    </div>
    <div class="col"></div>
</div>


<div class="card mb-2">
    <div class="card-body row">
        <div class="form-group col">
            <div class="small">Tanggal SO</div>
            {{ $data->tanggal_so }}
        </div>
        <div class="form-group col">
            <div class="small">Tanggal Kirim</div>
            {{ $data->tanggal_kirim }}
        </div>
        <div class="form-group col">
            <div class="small">Tanggal Masuk</div>
            {{ $data->created_at }}
        </div>
        <div class="form-group col">
            <div class="small">Nama Customer</div>
            {{ $data->nama }}
        </div>
        <div class="form-group col">
            <div class="small">SO</div>
            {{ $data->no_so }}
        </div>
        <div class="form-group col">
            <div class="small">No DO</div>
            {{ $data->no_do }}
        </div>
    </div>
    <div class="card-body row">
        <div class="form-group col">
            <div class="small">Alamat Kirim</div>
            {{ $data->alamat_kirim }}
        </div>
        <div class="form-group col">
            <div class="small">Keterangan</div>
            {{ $data->keterangan }}
        </div>
    </div>
</div>


<div class="card">
    <div class="card-body">
        <table class="table default-table dataTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Line</th>
                    <th>Nama Item</th>
                    <th>Qty</th>
                    <th>Berat</th>
                    <th>Part</th>
                    <th>Bumbu</th>
                    <th>Memo</th>
                    <th>Rate</th>
                    <th>Keterangan</th>
                    <th>Fulfill Desc</th>
                    <th>Fulfill Item</th>
                    <th>Fulfill Berat</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total  =   0;
                    $berat  =   0;
                    $chiller_out = array();
                @endphp
                @foreach ($list as $i => $row)
                @php
                    $total  +=  $row->qty;
                    $berat  +=  $row->berat;
                @endphp
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $row->line_id }}</td>
                    <td>{{ $row->nama_detail }}</td>
                    <td>{{ $row->qty }}</td>
                    <td>{{ $row->berat }}</td>
                    <td>{{ $row->part }}</td>
                    <td>{{ $row->bumbu }}</td>
                    <td>{{ $row->memo }}</td>
                    <td>Rp {{ $row->rate }}</td>
                    <td>{{ $row->keterangan }}
                    </td>
                    <td width="200px">
                        @php 
                            if($row->bahan_baku){
                                $bb = $row->bahan_baku;
                                if(count($bb)>0){
                                    echo "Fulfill Chiller Out : <br>";
                                        foreach($bb as $bb_out):
                                        echo "ID : ".$bb_out->chiller_out." || ".$bb_out->bb_item."pcs - ".$bb_out->bb_berat."kg<br>" ?? "";
                                        $chiller_out[] = $bb_out->chiller_out;
                                        endforeach;
                                }
                            }
                        @endphp
                    </td>
                    <td>{{ $row->fulfillment_qty }}</td>
                    <td>{{ $row->fulfillment_berat }}</td>
                    <td>
                        @if($row->status==2)
                            <span class="status status-success">Selesai</span>
                        @endif
                        @if($row->status==3)
                            <span class="status status-danger">Pending</span>
                        @endif
                    </td>
                </tr>
                    @foreach($row->order_item_log as $l)
                    <tr>
                        <td colspan="4">#{{$l->id}} {{$l->activity}} <span class="pull-right">{{$l->created_at}}</span></td>
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>

        <h6>Total Qty : {{ number_format($total) }} Ekr/Pcs/Pack</h6>
        <h6>Total Berat : {{ number_format($berat, 2) }} Kg</h6>
        <h6>Total Item : {{ number_format(count($list)) }} Items</h6>
        <br>
        <a href="{{route('salesorder.export', $data->id)}}" class="btn btn-blue">Export CSV</a>

    </div>
</div>

@php
$netsuite_fulfill = \App\Models\Netsuite::where('tabel', 'orders')->where('tabel_id', $data->id)->get();

$netsuite_ti = \App\Models\Netsuite::where('tabel', 'chiller')
                                    ->whereIn('tabel_id', \App\Models\Chiller::select('id')
                                        ->whereIn('table_id', \App\Models\Bahanbaku::select('id')
                                            ->where('order_id', $data->id)
                                        )
                                    )->orWhere('document_code',$data->no_so)->get();

$merged = $netsuite_fulfill->merge($netsuite_ti);
$netsuite = $merged->all();

@endphp

@if(count($netsuite)>0)


<div class="card mt-2">
    <div class="card-body">

<h6>Netsuite Terbentuk</h6>

 <form method="post" action="{{route('sync.cancel')}}">
        @csrf
        <br>
        <button type="submit" class="btn btn-blue mb-1" name="status" value="approve">Approve Integrasi</button> &nbsp
        <button type="submit" class="btn btn-red mb-1" name="status" value="cancel">Batalkan Integrasi</button> &nbsp
        <button type="submit" class="btn btn-info mb-1" name="status" value="retry">Kirim Ulang</button> &nbsp
        <button type="submit" class="btn btn-success mb-1" name="status" value="completed">Selesaikan</button> &nbsp
        <button type="submit" class="btn btn-warning mb-1" name="status" value="hold">Hold</button> &nbsp
        <hr>
<table class="table default-table">
    <thead>
        <tr>
            <th>
                <input type="checkbox" id="ns-checkall">
            </th>
            <th>ID</th>
            <th>C&U Date</th>
            <th>TransDate</th>
            <th>Label</th>
            <th>Activity</th>
            <th>Location</th>
            <th>IntID</th>
            <th>Paket</th>
            <th width="100px">Data</th>
            <th width="100px">Action</th>
            <th>Response</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>

            @foreach ($netsuite as $no => $field_value)
                @include('admin.pages.log.netsuite_one', ($netsuite = $field_value))
            @endforeach

    </tbody>
</table>
 </form>

    </div>
</div>

@endif

@stop

@section('header')
    <!-- <link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css"/> -->
@stop

@section('footer')
    <!-- <script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script> -->
@stop
