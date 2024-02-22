<table id="example" class="table default-table dataTable" style="width:100%">
    <thead>
        <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Label</th>
            <th>Record Type</th>
            <th>Document No</th>
            {{-- <th>Sync Status</th> --}}
            {{-- <th>Status</th> --}}
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $row)
            <tr>
                <td>{{$row->id}}</td>
                <td>{{$row->created_at}}</td>
                <td>{{$row->activity}}</td>
                <td>{{$row->label}}</td>
                <td>

                    
                    @php 
                        $table_data = $row->table_data;
                        $datas      = json_decode($table_data);
                    @endphp
                
                    {{-- ROW LOCATION --}}
                    @if($row->activity=='location')
                        @foreach($row->data_location as $no => $p)
                            <li>{{$no+1}}. {{$p->nama_location}}</li>
                        @endforeach
                        @if(count($row->data_location)==0)
                            <div class="status status-danger">Data telah diupdate <br>di kiriman selanjutnya</div>
                        @endif
                    @endif
                    {{-- BATAS ROW LOCATION --}}



                    {{-- ROW BOM --}}
                    @if($row->activity=='bom')
                        @foreach($row->data_bom as $no => $p)
                            <li>{{$no+1}}. {{$p->bom_name}}</li>
                        @endforeach
                        @if(count($row->data_bom)==0)
                            <div class="status status-danger">Data telah diupdate <br>di kiriman selanjutnya</div>
                        @endif
                    @endif
                    {{-- BATAS ROW BOM --}}

                    {{-- ROW SALES ORDER --}}
                    @if($row->activity=='sales-order')

                        @if(count($row->data_salesorder)==0)
                            <div class="status status-danger">Data telah diupdate <br>di kiriman selanjutnya</div>

                        @endif
                        
                        @if($datas->data ?? FALSE)
                            @foreach($datas->data as $no => $d)
                                {{$no+1}}. {{$d->data_sales_order->nomor_so}} || {{$d->data_sales_order->nama_customer}}<br>
                                @if($d->data_sales_order->memo)
                                MEMO : {{$d->data_sales_order->memo}}<br>
                                @endif
                            @endforeach
                        @endif

                    @endif
                    {{-- BATAS SALES ORDER --}}



                    {{-- ROW PURCHASE ORDER --}}
                    @if($row->activity=='purchase-order')
                        @foreach($row->data_purchasing as $no =>  $p)
                            <li>{{$no+1}}. {{$p->document_number}} ||  {{$p->type_po}} ||  {{$p->vendor_name}}</li>
                        @endforeach

                        @if(count($row->data_purchasing)==0)
                            <div class="status status-danger">Data telah diupdate <br>di kiriman selanjutnya</div>

                            @if($datas->data ?? FALSE)
                                @foreach($datas->data as $no => $d)
                                    {{$no+1}}. {{$d->data_purchasing->document_number}} || {{$d->data_purchasing->vendor_name}}<br>
                                @endforeach
                            @endif
                        @endif

                    @endif
                    {{-- BATAS PURCHASE ORDER --}}



                    {{-- ROW PO ITEM RECEIPT --}}
                    @if($row->activity=='po-item-receipt')
                        @foreach($row->data_po_item_receipt as $no =>  $p)
                            <li>{{$no+1}}. {{$p->document_number}} ||  {{$p->type_po}} ||  {{$p->vendor_name}}</li>
                        @endforeach
                        @if(count($row->data_po_item_receipt)==0)
                            <div class="status status-danger">Data telah diupdate <br>di kiriman selanjutnya</div>
                        @endif
                    @endif
                    {{-- BATAS ROW ITEM RECEIPT --}}



                    {{-- ROW VENDOR --}}
                    @if($row->activity=='vendor')
                        @foreach($row->data_vendor as $no => $p)
                            <li>{{$p->nama_vendor}}</li>
                        @endforeach
                        @if(count($row->data_vendor)==0)
                            <div class="status status-danger">Data telah diupdate <br>di kiriman selanjutnya</div>
                        @endif
                    @endif
                    {{-- BATAS ROW VENDOR --}}



                    {{-- ROW CUSTOMER --}}
                    @if($row->activity=='customer')
                        @foreach($row->data_customer as $no => $p)
                            <li>{{$no+1}}. {{$p->internal_id_customer}} . {{$p->nama_customer}} ||  {{$p->subsidiary}}</li>
                        @endforeach 
                        @if(count($row->data_customer)==0)
                            <div class="status status-danger">Data telah diupdate <br>di kiriman selanjutnya</div>
                        @endif
                    @endif
                    {{-- BATAS ROW CUSTOMER --}}



                    {{-- ROW ITEM --}}
                    @if($row->activity=='item')
                        @foreach($row->data_item as $no => $p)
                            <li>{{$no+1}}. {{$p->nama_item}}</li>
                        @endforeach
                        @if(count($row->data_item)==0)
                            <div class="status status-danger">Data telah diupdate <br>di kiriman selanjutnya</div>
                        @endif
                    @endif
                    {{-- BATAS ROW ITEM --}}


                </td>
                {{-- <td>{{$row->sync_status}}</td> --}}
                {{-- <td>{{$row->status}}</td> --}}
                <td>
                    @if($row->activity=='location')
                        <a href="{{route('report.netsuite.location')}}?id={{$row->id}}" class="btn btn-sm btn-blue">Detail</a>
                    @endif
                    @if($row->activity=='bom')
                        <a href="{{route('report.netsuite.bom')}}?id={{$row->id}}" class="btn btn-sm btn-blue">Detail</a>
                    @endif
                    @if($row->activity=='sales-order')
                        <a href="{{route('report.netsuite.so')}}?id={{$row->id}}" class="btn btn-sm btn-blue">Detail</a>
                    @endif
                    @if($row->activity=='purchase-order')
                        <a href="{{route('report.netsuite.po')}}?id={{$row->id}}" class="btn btn-sm btn-blue">Detail</a>
                    @endif
                    @if($row->activity=='po-item-receipt')

                    @endif

                    <a href="{{route('report.netsuite.raw')}}?id={{$row->id}}" class="btn btn-sm btn-green" target="_blank">Raw Data</a>
                    
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div id="daftar_paginate">
        {{ $data->appends($_GET)->links() }}
</div>

<script>
    $('#daftar_paginate .pagination a').on('click', function(e) {
        e.preventDefault();

        url = $(this).attr('href') ;

        reload_data(url);
    });

    function reload_data(url){
        $.ajax({
            url: url,
            method: "GET",
            success: function(response) {
                $('#list-table').html(response);
                    window.history.pushState('Netsuite', 'Netsuite', (url.replace("/list", "")));
                    console.log(url);
                    $('#load-refresh').on('click', function(){
                        reload_data(url);
                    })
            }

        });
    }
    </script>
    