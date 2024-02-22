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
                    <td class="list-record">document_number</td> 
                    <td class="list-record">internal_id_po</td> 
                    <td class="list-record">type_po</td> 

                    <td class="list-record">data_item</td>
                    <td class="list-record">tanggal_kirim</td> 
                    <td class="list-record">vendor</td> 
                    <td class="list-record">vendor_name</td> 
                    
                    <td class="list-record">tipe_ekspedisi</td> 
                    <td class="list-record">po_subsidiary</td> 

                    <td class="list-record">internal_id_vendor</td> 
                    <td class="list-record">nama_vendor</td> 
                    <td class="list-record">alamat</td> 
                    <td class="list-record">no_telp</td> 
                    <td class="list-record">jenis_ekspedisi</td> 
                    <td class="list-record">wilayah_vendor</td> 
                    <td class="list-record">vendor_subsidiary</td> 


                    <td class="list-record">server_update</td> 
                    <td class="list-record">last_update</td> 
                    <td class="list-record">netsuite_log_id</td> 
                </tr>
            </thead>
            <tbody>
                @foreach($data as $po)
                <tr class="isi-list">
                    <td class="list-record">{{$po->document_number}}</td> 
                    <td class="list-record">{{$po->internal_id_po}}</td> 
                    <td class="list-record">{{$po->type_po}}</td> 
                    <td class="list-record">
                        <table>
                            <tr>
                                <td class="list-record">No</td> 
                                <td class="list-record">item</td> 
                                <td class="list-record">rate</td> 
                                <td class="list-record">ukuran_ayam</td> 
                                <td class="list-record">qty</td> 
        
                                <td class="list-record">jenis_ayam</td> 
                                <td class="list-record">jumlah_do</td> 
        
                                <td class="list-record">internal_id_item</td> 
                                <td class="list-record">sku</td> 
                                <td class="list-record">name</td> 
                                <td class="list-record">category_item</td> 
                                <td class="list-record">item_subsidiary</td> 
                                
                            </tr>
                            @foreach(json_decode($po->data_item) as $no => $itm)
                            <tr>
                                <td class="list-record">{{$no+1}}</td> 
                                <td class="list-record">{{$itm->item}}</td> 
                                <td class="list-record">{{$itm->rate}}</td> 
                                <td class="list-record">{{$itm->ukuran_ayam}}</td> 
                                <td class="list-record">{{$itm->qty}}</td> 
            
                                <td class="list-record">{{$itm->jenis_ayam}}</td> 
                                <td class="list-record">{{$itm->jumlah_do}}</td> 
                                
                                <td class="list-record">{{$itm->internal_id_item}}</td> 
                                <td class="list-record">{{$itm->sku}}</td> 
                                <td class="list-record">{{$itm->name}}</td> 
                                <td class="list-record">{{$itm->category_item}}</td> 
                                <td class="list-record">{{$itm->subsidiary}}</td> 
                            </tr>
                            @endforeach
                        </table>
                    </td>

                    <td class="list-record">{{$po->tanggal_kirim}}</td> 
                    <td class="list-record">{{$po->vendor}}</td> 
                    <td class="list-record">{{$po->vendor_name}}</td> 
                    
                    <td class="list-record">{{$po->tipe_ekspedisi}}</td> 
                    <td class="list-record">{{$po->po_subsidiary}}</td> 

                    <td class="list-record">{{$po->internal_id_vendor}}</td> 
                    <td class="list-record">{{$po->nama_vendor}}</td> 
                    <td class="list-record">{{$po->alamat}}</td> 
                    <td class="list-record">{{$po->no_telp}}</td> 
                    <td class="list-record">{{$po->jenis_ekspedisi}}</td> 
                    <td class="list-record">{{$po->wilayah_vendor}}</td> 
                    <td class="list-record">{{$po->vendor_subsidiary}}</td> 

                    
                    <td class="list-record">{{$po->server_update}}</td> 
                    <td class="list-record">{{$po->last_update}}</td> 
                    <td class="list-record">{{$po->netsuite_log_id}}</td> 
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