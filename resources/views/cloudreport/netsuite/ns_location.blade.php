@extends('cloudreport.template')

@section('title', 'NETSUITE')

@section('content')

<div class="col">
    <a href="{{ route('report.netsuite.index') }}" class="btn btn-outline btn-sm btn-back"> <i
            class="fa fa-arrow-left"></i>
        Back</a>
</div>

<section class="panel">
    <div class="card-body">
        <div class="table-responsive">
        <table class="table default-table dataTable">
            <thead>
                <tr class="isi-list">
                    <td class="list-record">id</td> 
                    <td class="list-record">internal_id</td> 
                    <td class="list-record">nama_location</td> 
                    <td class="list-record">kategori_gudang</td> 
                    <td class="list-record">subsidiary_id</td> 
                    <td class="list-record">subsidiary</td> 
                    <td class="list-record">isinactive</td> 
                    <td class="list-record">server_update</td>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $r)
                <tr class="isi-list">
                    <td class="list-record">{{$r->id}}</td> 
                    <td class="list-record">{{$r->internal_id_location}}</td> 
                    <td class="list-record">{{$r->nama_location}}</td> 
                    <td class="list-record">{{$r->kategori}}</td> 
                    <td class="list-record">{{$r->subsidiary_id}}</td> 
                    <td class="list-record">{{$r->subsidiary}}</td> 
                    <td class="list-record">{{$r->status}}</td> 
                    <td class="list-record">{{$r->last_update}}</td> 
                    <td class="list-record">{{$r->server_update}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
</section>

@stop

@section('header')
    <!-- <link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css"/> -->
@stop

@section('footer')
    <!-- <script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script> -->
@stop