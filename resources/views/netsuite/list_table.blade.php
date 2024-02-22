<table id="example" class="table table-bordered table-sm" style="width:100%">
    <thead>
        <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Label</th>
            <th>Sync Status</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $row)
            <tr>
                <td>{{$row->id}}</td>
                <td>{{$row->created_at}}</td>
                <td>{{$row->activity}}</td>
                <td>{{$row->sync_status}}</td>
                <td>{{$row->status}}</td>
            </tr>

            @if($row->label=='sales_order')
                @if(count($row->data_salesorder)>0)
                    <tr>
                        <td colspan="5">
                            <table style="width: 100%">
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
                            @foreach($row->data_salesorder as $so)
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

                                <td class="list-record">{{$so->data_item}}</td>

                                <td class="list-record">{{$so->server_update}}</td>
                                <td class="list-record">{{$so->last_update}}</td>
                                <td class="list-record">{{$so->netsuite_log_id}}</td>
                            </tr>
                            @endforeach
                            </table>
                        </td>
                    </tr>
                @endif
            @endif

            @if($row->label=='bom')
                @if(count($row->data_bom)>0)
                    <tr>
                        <td colspan="5">
                            <table style="width: 100%">
                                <tr class="isi-list">
                                    <td class="list-record">id</td> 
                                    <td class="list-record">internal_id_bom</td> 
                                    <td class="list-record">bom_name</td> 
                                    <td class="list-record">internal_subsidiary_id</td> 
                                    <td class="list-record">subsidiary</td> 
                                    <td class="list-record">memo</td> 
                                    <td class="list-record">last_update</td> 
                                    <td class="list-record">server_update</td>
                                </tr>
                            @foreach($row->data_bom as $r)
                            <tr class="isi-list">
                                <td class="list-record">{{$r->id}}</td> 
                                <td class="list-record">{{$r->internal_id_bom}}</td> 
                                <td class="list-record">{{$r->bom_name}}</td> 
                                <td class="list-record">{{$r->internal_subsidiary_id}}</td> 
                                <td class="list-record">{{$r->subsidiary}}</td> 
                                <td class="list-record">{{$r->memo}}</td> 
                                <td class="list-record">{{$r->last_update}}</td> 
                                <td class="list-record">{{$r->server_update}}</td>
                            </tr>
                            @endforeach
                            </table>
                        </td>
                    </tr>
                @endif
            @endif

            @if($row->label=='location')
                @if(count($row->data_location)>0)
                    <tr>
                        <td colspan="5">
                            <table style="width: 100%">
                                <tr class="isi-list">
                                    <td class="list-record">id</td> 
                                    <td class="list-record">internal_id</td> 
                                    <td class="list-record">nama_location</td> 
                                    <td class="list-record">last_update</td> 
                                    <td class="list-record">server_update</td>
                                </tr>
                            @foreach($row->data_location as $r)
                            <tr class="isi-list">
                                <td class="list-record">{{$r->id}}</td> 
                                <td class="list-record">{{$r->internal_id_location}}</td> 
                                <td class="list-record">{{$r->nama_location}}</td> 
                                <td class="list-record">{{$r->last_update}}</td> 
                                <td class="list-record">{{$r->server_update}}</td>
                            </tr>
                            @endforeach
                            </table>
                        </td>
                    </tr>
                @endif
            @endif

            @if($row->label=='purchasing')
                @if(count($row->data_purchasing)>0)
                    <tr>
                        <td colspan="5">
                            <table style="width: 100%">
                                <tr class="isi-list">
                                    <td class="list-record">document_number</td> 
                                    <td class="list-record">type_po</td> 
                                    <td class="list-record">internal_id</td> 
                                    <td class="list-record">item</td> 
                                    <td class="list-record">rate</td> 
                                    <td class="list-record">vendor</td> 
                                    <td class="list-record">vendor_name</td> 
                                    <td class="list-record">ukuran_ayam</td> 
                                    <td class="list-record">qty</td> 
                                    <td class="list-record">tipe_ekspedisi</td> 
                                    <td class="list-record">jenis_ayam</td> 
                                    <td class="list-record">jumlah_do</td> 
                                    <td class="list-record">tanggal_kirim</td> 
                                    <td class="list-record">internal_id_po</td> 
                                    <td class="list-record">po_subsidiary</td> 

                                    <td class="list-record">internal_id_vendor</td> 
                                    <td class="list-record">nama_vendor</td> 
                                    <td class="list-record">alamat</td> 
                                    <td class="list-record">no_telp</td> 
                                    <td class="list-record">jenis_ekspedisi</td> 
                                    <td class="list-record">wilayah_vendor</td> 
                                    <td class="list-record">vendor_subsidiary</td> 

                                    <td class="list-record">internal_id_item</td> 
                                    <td class="list-record">sku</td> 
                                    <td class="list-record">name</td> 
                                    <td class="list-record">category_item</td> 
                                    <td class="list-record">item_subsidiary</td> 

                                    <td class="list-record">server_update</td> 
                                    <td class="list-record">last_update</td> 
                                    <td class="list-record">netsuite_log_id</td> 
                                </tr>
                            @foreach($row->data_location as $po)
                            <tr class="isi-list">
                                <td class="list-record">{{$po->document_number}}</td> 
                                <td class="list-record">{{$po->type_po}}</td> 
                                <td class="list-record">{{$po->internal_id}}</td> 
                                <td class="list-record">{{$po->item}}</td> 
                                <td class="list-record">{{$po->rate}}</td> 
                                <td class="list-record">{{$po->vendor}}</td> 
                                <td class="list-record">{{$po->vendor_name}}</td> 
                                <td class="list-record">{{$po->ukuran_ayam}}</td> 
                                <td class="list-record">{{$po->qty}}</td> 
                                <td class="list-record">{{$po->tipe_ekspedisi}}</td> 
                                <td class="list-record">{{$po->jenis_ayam}}</td> 
                                <td class="list-record">{{$po->jumlah_do}}</td> 
                                <td class="list-record">{{$po->tanggal_kirim}}</td> 
                                <td class="list-record">{{$po->internal_id_po}}</td> 
                                <td class="list-record">{{$po->po_subsidiary}}</td> 

                                <td class="list-record">{{$po->internal_id_vendor}}</td> 
                                <td class="list-record">{{$po->nama_vendor}}</td> 
                                <td class="list-record">{{$po->alamat}}</td> 
                                <td class="list-record">{{$po->no_telp}}</td> 
                                <td class="list-record">{{$po->jenis_ekspedisi}}</td> 
                                <td class="list-record">{{$po->wilayah_vendor}}</td> 
                                <td class="list-record">{{$po->vendor_subsidiary}}</td> 

                                <td class="list-record">{{$po->internal_id_item}}</td> 
                                <td class="list-record">{{$po->sku}}</td> 
                                <td class="list-record">{{$po->name}}</td> 
                                <td class="list-record">{{$po->category_item}}</td> 
                                <td class="list-record">{{$po->item_subsidiary}}</td> 

                                <td class="list-record">{{$po->server_update}}</td> 
                                <td class="list-record">{{$po->last_update}}</td> 
                                <td class="list-record">{{$po->netsuite_log_id}}</td> 
                            </tr>
                            @endforeach
                            </table>
                        </td>
                    </tr>
                @endif
            @endif

        @endforeach
    </tbody>
</table>

<style>
    table{
        font-size: 9pt;
    }
</style>
@section('header')
    <!-- <link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css"/> -->
@stop

@section('footer')
    <!-- <script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script> -->
    <script>
        $('.dataTable').DataTable({
            "bPaginate": true,
            "bLengthChange": true,
            "bFilter": true,
            "bInfo": true,
            "bAutoWidth": true 
        });
    </script>
@stop
