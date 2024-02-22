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
                    <td class="list-record">internal_id_bom</td> 
                    <td class="list-record">bom_name</td> 
                    <td class="list-record">internal_subsidiary_id</td> 
                    <td class="list-record">subsidiary</td> 
                    <td class="list-record">memo</td> 
                    <td class="list-record">last_update</td> 
                    <td class="list-record">data</td> 
                    <td class="list-record">server_update</td>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $r)
                <tr class="isi-list">
                    <td class="list-record">{{$r->id}}</td> 
                    <td class="list-record">{{$r->internal_id_bom}}</td> 
                    <td class="list-record">{{$r->bom_name}}</td> 
                    <td class="list-record">{{$r->internal_subsidiary_id}}</td> 
                    <td class="list-record">{{$r->subsidiary}}</td> 
                    <td class="list-record">{{$r->memo}}</td> 
                    <td class="list-record">{{$r->last_update}}</td> 
                    <td class="list-record">
                        <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal{{$r->id}}">Lihat Data</button>

                        <!-- Modal -->
                        <div id="myModal{{$r->id}}" class="modal fade" role="dialog">
                        <div class="modal-dialog">

                            <!-- Modal content-->
                            <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Data JSON</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                @php 
                                    $bom        = \App\Models\Bom::where('netsuite_internal_id', $r->internal_id_bom)->first();
                                    $bom_item   = \App\Models\BomItem::where('bom_id', ($bom->id ?? "0"))->get();
                                @endphp
                                <table class="table default-table">
                                    <tbody>
                                        @foreach($bom_item as $bi)
                                            <tr>
                                                <td>{{$bi->item->sku}}</td> 
                                                <td>{{$bi->item->nama}}</td> 
                                                <td>{{$bi->qty_per_assembly}}</td> 
                                                <td>{{$bi->unit}}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                </table>
                                {{-- <pre>{{json_encode($bom_item, JSON_PRETTY_PRINT)}}</pre> --}}
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                            </div>

                        </div>
                        </div> 

                    </td> 
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
    <link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css"/>
@stop

@section('footer')
    <script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script>
@stop