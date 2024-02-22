<div class="card card-body px-2 mb-1">
    <table class="table default-table">
        <thead>
            <tr>
                {{-- <th>
                    <input type="checkbox" id="ns-checkall">
                </th> --}}
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
                <th>Waktu Delete</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($netsuites as $no => $netsuite)
            <tr>
                {{-- <td>
                    <input type="checkbox" name="selected_id[]" value="{{$netsuite->id}}" class="ns-checklist">
                </td> --}}
                <td>{{ $netsuite->id }}</td>
                <td>
                    {{date('d/m/Y H:i:s', strtotime($netsuite->created_at))}}
                    <hr>
                    {{date('d/m/Y H:i:s', strtotime($netsuite->updated_at))}}
                </td>
                <td>
                    {{date('d/m/Y', strtotime($netsuite->trans_date))}}
                </td>
                <td>
                    {{$netsuite->record_type}}<br>
                    Created by {{\App\Models\User::find($netsuite->user_id)->name ?? ""}}
                </td>
                <td>
                    {{$netsuite->label}}
                    @if ($netsuite->record_type == 'wo_build' || $netsuite->record_type == 'work_order')
                        @if ($netsuite->produksi)
                        <div><a href="{{ route('syncprod.index', ['paket' => $netsuite->paket_id]) }}" target="_blank">ListProduction</a></div>
                        @endif
            
                        
                    @endif
            
                    @if(substr($netsuite->document_code,0,4) == 'ABF-')
                        <a href="{{ route('abf.timbang', str_replace('ABF-','',$netsuite->document_code) )}}">
                            <br>{{$netsuite->document_code ?? ""}}
                        </a>
                    @endif
            
                    @if($netsuite->tabel=="orders")
                        @php
                            $so = \App\Models\Order::where('id', $netsuite->tabel_id)->first();
                        @endphp
                        @if($so)
                            <br><a href="{{url('admin/laporan/sales-order/'.$so->id)}}" target="_blank">{{$so->no_so}}</a>
                            <br>{{$so->nama}}
                        @endif
                    @endif
                    @if($netsuite->tabel=="productions")
                        @php
                            $prod = \App\Models\Production::where('id', $netsuite->tabel_id)->first();
                        @endphp
                        @if($prod)
                            @if($netsuite->label=="item_receipt_lpah")
                            <br><a href="{{url('admin/produksi/'.$prod->id)}}" target="_blank">{{$prod->prodpur->no_po}}</a>
                            @elseif($netsuite->label=="item_receipt_chiller")
                            <br><a href="{{url('admin/penerimaan-non-karkas/'.$prod->id)}}" target="_blank">{{$prod->prodpur->no_po}}</a>
                            @elseif($netsuite->label=="item_receipt_fresh")
                            <br><a href="{{url('admin/grading/'.$prod->id)}}" target="_blank">{{$prod->prodpur->no_po}}</a>
                            @endif
                            <br>{{$prod->no_lpah}}
                            @endif
                            @endif
                            @if($netsuite->tabel=="retur")
                            @php
                            $retur = \App\Models\Retur::where('id', $netsuite->tabel_id)->first();
                            if($retur){
                                $order = \App\Models\Order::where('id_so', $retur->id_so)->first();
                            }
                            @endphp
                            @if($retur)
                                <br><a href="{{url('admin/retur/detail/'.$retur->id)}}" target="_blank">{{$retur->to_customer->nama ?? ""}}</a>
                                @if($order)
                                    <br><a href="{{url('admin/laporan/sales-order/'.$order->id)}}" target="_blank">{{$order->no_so ?? ""}}</a>
                                @endif
                            @endif
                    @endif
                    @if($netsuite->record_type=="transfer_inventory")
                        @php
                            $chill          = \App\Models\Chiller::find($netsuite->tabel_id);
                            $orderBahanBaku = App\Models\Bahanbaku::where('netsuite_id', $netsuite->id)->first();
                        @endphp
                        @if($netsuite->tabel=="chiller" && ($netsuite->label=="ti_bb_ekspedisi" || $netsuite->label=="ti_finishedgood_ekspedisi"))
                            @if($chill)
                                <br>
                                {{$chill->item_name}} ({{$chill->qty_item}} pcs || {{$chill->berat_item}} kg) <br>
                                {{$netsuite->document_code ?? ""}}
                                @endif
                        @elseif($netsuite->tabel=="product_gudang")
                            @php
                                $gudang = \App\Models\Product_gudang::find($netsuite->tabel_id);
                            @endphp
                            @if($gudang)
                                <br>
                                {{$gudang->nama}}<br>
                                {{$netsuite->document_code ?? ""}} <br>
                                @if ($orderBahanBaku)
                                    ({{ $orderBahanBaku->bb_item ?? 0 }} pcs ||
                                    {{ $orderBahanBaku->bb_berat }} kg) <br>
                                @endif
                            @endif
                        @else
                                @if($netsuite->tabel!="productions")
                                    <br>{{$netsuite->document_code ?? ""}}
                                @endif
                        @endif
                    @endif
                </td>
                <td>
                    {{$netsuite->id_location}}<br>
                    {{$netsuite->location}}
                </td>
                <td>
                    @if($netsuite->record_type=="work_order")
                        <a href="https://6484226.app.netsuite.com/app/accounting/transactions/workord.nl?id={{$netsuite->response_id}}&whence=" target="_blank">{{$netsuite->response_id}}</a>
                    @elseif($netsuite->record_type=="wo_build")
                        <a href="https://6484226.app.netsuite.com/app/accounting/transactions/build.nl?whence=&id={{$netsuite->response_id}}" target="_blank">{{$netsuite->response_id}}</a>
                    @elseif($netsuite->record_type=="transfer_inventory")
                        <a href="https://6484226.app.netsuite.com/app/accounting/transactions/invtrnfr.nl?id={{$netsuite->response_id}}&whence=" target="_blank">{{$netsuite->response_id}}</a>
                    @elseif($netsuite->record_type=="item_fulfill")
                        <a href="https://6484226.app.netsuite.com/app/accounting/transactions/itemship.nl?whence=&id={{$netsuite->response_id}}" target="_blank">{{$netsuite->response_id}}</a>
                    @elseif($netsuite->record_type=="itemreceipt" || $netsuite->record_type=="receipt_return")
                        <a href="https://6484226.app.netsuite.com/app/accounting/transactions/itemrcpt.nl?id={{$netsuite->response_id}}&whence=" target="_blank">{{$netsuite->response_id}}</a>
                    @elseif($netsuite->record_type=="return_authorization")
                        <a href="https://6484226.app.netsuite.com/app/accounting/transactions/rtnauth.nl?id={{$netsuite->response_id}}&whence=" target="_blank">{{$netsuite->response_id}}</a>
                    @else
                        {{$netsuite->response_id}}
                    @endif
            
                </td>
                <td>
                    {{-- <a href="{{route('sync.detail', $netsuite->id)}}" target="_blank"> --}}
                        @if($netsuite->paket_id=="0")
                        <span class="status status-info">parent</span>
                        @else
                        {{$netsuite->paket_id}}
                        @endif
                    {{-- </a> --}}
                </td>
                <td>
            
            
                    <button type="button" class="mb-1 btn btn-blue" data-toggle="modal" data-target="#myModal{{$netsuite->id}}"><span class="fa fa-send"></span></button>
                    <button type="button" class="mb-1 btn btn-success" data-toggle="modal" data-target="#myModalRsp{{$netsuite->id}}"><span class="fa fa-download"></span></button>
            
                        <!-- Modal -->
                        <div id="myModal{{$netsuite->id}}" class="modal fade" role="dialog">
                        <div class="modal-dialog">
            
                            <!-- Modal content-->
                            <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Data JSON</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
            
                                    <br>
                                    Created : {{date('d/m/Y H:i:s', strtotime($netsuite->created_at))}}
                                    <br>
                                    Resp : {{date('d/m/Y H:i:s', strtotime($netsuite->updated_at))}}
                                    <br>
                                    Label : {{$netsuite->record_type}}<br>
                                    Created by : {{\App\Models\User::find($netsuite->user_id)->name ?? ""}}
                                @if($netsuite->tabel=="orders")
                                @php
                                    $so = \App\Models\Order::where('id', $netsuite->tabel_id)->first();
                                @endphp
                                @if($so)
                                    <br>{{$so->no_so}}
                                    <br>{{$so->nama}}
                                @endif
                                @endif
                                @if($netsuite->tabel=="productions")
                                    @php
                                        $prod = \App\Models\Production::where('id', $netsuite->tabel_id)->first();
                                    @endphp
                                    @if($prod)
                                        <br>{{$prod->prodpur->no_po}}
                                        <br>{{$prod->no_lpah}}
                                    @endif
                                @endif
                                @if($netsuite->tabel=="retur")
                                    @php
                                        $retur = \App\Models\Retur::where('id', $netsuite->tabel_id)->first();
                                    @endphp
                                    @if($retur)
                                        <br>{{$retur->to_customer->nama ?? ""}}
                                        <br>{{$retur->no_so ?? "#non-so"}}
                                    @endif
                                @endif
                                <hr>
                                @php
                                    $json = json_decode($netsuite->data_content);
                                @endphp
                                <div style="max-height: 600px; overflow:auto">
                                    <pre>{{json_encode($json, JSON_PRETTY_PRINT)}}</pre>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                            </div>
            
                        </div>
                        </div>
                        <div id="myModalRsp{{$netsuite->id}}" class="modal fade" role="dialog">
                        <div class="modal-dialog">
            
                            <!-- Modal content-->
                            <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Response JSON</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                @if($netsuite->response)
                                    @php
                                        $json_rsp = json_decode($netsuite->response);
                                    @endphp
                                    <pre>{{json_encode($json_rsp, JSON_PRETTY_PRINT)}}</pre>
                                @endif
            
                                @if($netsuite->failed)
                                    <hr>
                                    @php
                                        $json = json_decode($netsuite->failed);
                                    @endphp
                                    <pre>{{json_encode($json, JSON_PRETTY_PRINT)}}</pre>
                                @endif
            
                                @if($netsuite->resp_update)
                                    <hr>
                                    @php
                                        $json = json_decode($netsuite->resp_update);
                                    @endphp
                                    <pre>{{json_encode($json, JSON_PRETTY_PRINT)}}</pre>
                                @endif
            
            
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                            </div>
            
                        </div>
                        </div>
                </td>
                <td>
                    <a href="{{ route('sync.showsync', $netsuite->id) }}" target="_blank" class="mb-1 btn btn-warning"><i class="fa fa-edit"></i></a>
                    <button class="mb-1 btn btn-info btnRestore" data-id="{{ $netsuite->id }}"><i class="fa fa-undo"></i></button>
                </td>
                <td style="max-width: 150px">
            
                    @if($netsuite->resp_update)
                        <span class="status status-info">INTEGRASI UPDATE</span><br>
                    @endif
            
                    @if ( !empty( $netsuite->response) )
            
                        @php
                            //code...
                            $resp = json_decode($netsuite->response);
                        @endphp
            
                        @if(is_array($resp))
                            @if($resp[0]->status=='success')
                            <div class="status status-success">
                                <div style="max-width: 300px">
                                    Document No : {{$resp[0]->documentno ?? ""}}<br>
                                </div>
                            </div>
                            @elseif($resp[0]->status=='failed')
            
                            @endif
            
                        @else
                        {{$netsuite->response}}
                        @endif
            
                    @else
                        {{-- <div class="status status-info">Belum diproses atau<br> Proses sebelumnya gagal</div> --}}
                    @endif
            
                    @if ( !empty( $netsuite->failed) )
                    <div class="status status-danger">
            
                            @php
                                //code...
                                $resp = json_decode($netsuite->failed);
                            @endphp
            
                            @php
                                try {
                                    //code...
                                    echo ($resp[0]->message->message);
                                } catch (\Throwable $th) {
                                    //throw $th;
                                }
                                try {
                                    //code...
                                    echo ( $resp[0]->message);
                                } catch (\Throwable $th) {
                                    //throw $th;
                                }
                                try {
                                    //code...
                                    echo ( $resp->error->message);
                                } catch (\Throwable $th) {
                                    //throw $th;
                                }
                            @endphp
            
            
                            <!-- Modal -->
                            <div id="myModalError{{$netsuite->id}}" class="modal fade" role="dialog">
                            <div class="modal-dialog">
            
                                <!-- Modal content-->
                                <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Data JSON</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    @php
                                        $json = json_decode($netsuite->response);
                                    @endphp
                                    <pre>{{json_encode($json, JSON_PRETTY_PRINT)}}</pre>
            
                                    <hr>
                                    @php
                                        $json = json_decode($netsuite->failed);
                                    @endphp
                                    <pre>{{json_encode($json, JSON_PRETTY_PRINT)}}</pre>
            
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                                </div>
            
                            </div>
                            </div>
            
                        </div>
                        <br><button type="button" class="color-red" data-toggle="modal" data-target="#myModalError{{$netsuite->id}}">Lihat data</button>
            
                        @endif
            
                </td>
            
                <td>{{date('d/m/Y H:i:s', strtotime($netsuite->deleted_at))}}</td>

                <td>
                    @if($netsuite->status=='1')
                    <div class="status status-success">Completed</div>
                    @elseif($netsuite->status=='0')
                    <div class="status status-danger">Gagal</div>
                    @elseif($netsuite->status=='3')
                    <div class="status status-warning">Dibatalkan</div>
                    @elseif($netsuite->status=='4')
                    <div class="status status-other">Scheduled</div>
                    @elseif($netsuite->status=='5')
                    <div class="status status-other">Approval</div>
                    @elseif($netsuite->status=='6')
                    <div class="status status-purple">Hold</div>
                    @else
                    <div class="status status-info">Pending</div>
                    @endif
                </td>

            </tr>
            @endforeach
        </tbody>
    </table>
</div>


<script>
        $('.btnRestore').on('click', function() {

        let result = confirm("Apakah yakin ingin mengembalikan JSON?");

        if (result === true) {
            
            let id                   =   $(this).data('id');
            let tanggal_awal         =   $("#tanggal_awal").val() ;
            let tanggal_akhir        =   $("#tanggal_akhir").val() ;
            let pencarian            =   encodeURIComponent($("#cari").val() ?? '') ;

            // $("#dataJSON").load("{{ route('netsuite.index', ['key' => 'restoreData']) }}&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir + "&pencarian=" + pencarian + "&id=" + id, function() {
            //     $("#loading").attr("style", 'display: none');
            // });

            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('netsuite.index') }}",
                method: "POST",
                data: {
                    id  :   id ,
                    key :   'restoreData' ,
                    tanggal_awal,
                    tanggal_akhir,
                    pencarian
                },
                success: function(data) {
                    if (data.status == 400) {
                        showAlert(data.msg);
                    } else {
                        showNotif(data.msg);
                        loadSummary();
                    }
                    $(".batal_header").show() ;
                }
            });

        } else {
            return false;
        }

    })
</script>