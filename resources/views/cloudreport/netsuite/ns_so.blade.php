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
                    <td class="list-record">internal_id_customer</td>
                    <td class="list-record">nama_customer</td>
                    <td class="list-record">category_customer</td>
                    <td class="list-record">id_sales</td>
                    <td class="list-record">sales</td>
                    <td class="list-record">so_subsidiary</td>
                    <td class="list-record">internal_id_parent</td>

                    <td class="list-record">internal_id_so</td>
                    <td class="list-record">nomor_so</td>
                    <td class="list-record">nomor_po</td>
                    <td class="list-record">nama_customer</td>
                    <td class="list-record">tanggal_kirim</td>
                    <td class="list-record">tanggal_so</td>
                    <td class="list-record">customer_partner</td>
                    <td class="list-record">alamat_customer_partner</td>
                    <td class="list-record">wilayah</td>
                    <td class="list-record">id_sales</td>
                    <td class="list-record">sales</td>
                    <td class="list-record">memo</td>
                    <td class="list-record">sales_channel</td>
                    <td class="list-record">alamat_ship_to</td>
                    <td class="list-record">so_subsidiary</td>

                    <td class="list-record">data_item</td>

                    <td class="list-record">server_update</td>
                    <td class="list-record">last_update</td>
                    <td class="list-record">netsuite_log_id</td>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $so)
                <tr class="isi-list">
                    <td class="list-record">{{$so->internal_id_customer}}</td>
                    <td class="list-record">{{$so->nama_customer}}</td>
                    <td class="list-record">{{$so->category_customer}}</td>
                    <td class="list-record">{{$so->id_sales}}</td>
                    <td class="list-record">{{$so->sales}}</td>
                    <td class="list-record">{{$so->so_subsidiary}}</td>
                    <td class="list-record">{{$so->internal_id_parent}}</td>

                    <td class="list-record">{{$so->internal_id_so}}</td>
                    <td class="list-record">{{$so->nomor_so}}</td>
                    <td class="list-record">{{$so->nomor_po}}</td>
                    <td class="list-record">{{$so->nama_customer}}</td>
                    <td class="list-record">{{$so->tanggal_kirim}}</td>
                    <td class="list-record">{{$so->tanggal_so}}</td>
                    <td class="list-record">{{$so->customer_partner}}</td>
                    <td class="list-record">{{$so->alamat_customer_partner}}</td>
                    <td class="list-record">{{$so->wilayah}}</td>
                    <td class="list-record">{{$so->id_sales}}</td>
                    <td class="list-record">{{$so->sales}}</td>
                    <td class="list-record">{{$so->memo}}</td>
                    <td class="list-record">{{$so->sales_channel}}</td>
                    <td class="list-record">{{$so->alamat_ship_to}}</td>
                    <td class="list-record">{{$so->so_subsidiary}}</td>

                    <td class="list-record">
                        @php $data_item = json_decode($so->data_item); @endphp
                        @foreach($data_item as $it)
                            <div style="width: 600px"><li>{{$it->internal_id_item}} - {{$it->sku}} - {{$it->name}} - {{$it->category_item}} <br> {{$it->qty}} kg - {{$it->qty_pcs}} pcs - Rp {{$it->rate}}
                                <br> Bumbu : {{$it->bumbu}} - Plastik {{$it->plastik}} - Memo : {{$it->memo}}
                            </li></div>
                        @endforeach

                    </td>

                    <td class="list-record">{{$so->server_update}}</td>
                    <td class="list-record">{{$so->last_update}}</td>
                    <td class="list-record">{{$so->netsuite_log_id}}</td>
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