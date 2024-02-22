<tr>
    <td>
        <input type="checkbox" name="selected_id[]" value="{{$netsuite->id}}" class="ns-checklist">
    </td>
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
        Created by {{ $netsuite->dataUsers->name ?? ""}}
    </td>
    <td>
        {{$netsuite->label}}
        @if ($netsuite->record_type == 'wo_build' || $netsuite->record_type == 'work_order')
        @if ($netsuite->produksi)
        <div><a href="{{ route('syncprod.index', ['paket' => $netsuite->paket_id]) }}"
                target="_blank">ListProduction</a></div>
        @endif


        @endif

        @if(substr($netsuite->document_code,0,4) == 'ABF-')
        <a href="{{ route('abf.timbang', str_replace('ABF-','',$netsuite->document_code) )}}">
            <br>{{$netsuite->document_code ?? ""}}
        </a>
        @endif

        @if($netsuite->tabel=="orders")
        @if($netsuite->dataOrders)
        <br><a href="{{url('admin/laporan/sales-order/'.$netsuite->dataOrders->id)}}"
            target="_blank">{{$netsuite->dataOrders->no_so}}</a>
        <br>{{$netsuite->dataOrders->nama}}
        @endif
        @endif
        @if($netsuite->tabel=="productions")
        @if($netsuite->dataProductions)
        @if($netsuite->label=="item_receipt_lpah")
        <br><a href="{{url('admin/produksi/'.$netsuite->dataProductions->id)}}"
            target="_blank">{{$netsuite->dataProductions->prodpur->no_po}}</a>
        @elseif($netsuite->label=="item_receipt_chiller")
        <br><a href="{{url('admin/penerimaan-non-karkas/'.$netsuite->dataProductions->id)}}"
            target="_blank">{{$netsuite->dataProductions->prodpur->no_po}}</a>
        @elseif($netsuite->label=="item_receipt_fresh")
        <br><a href="{{url('admin/grading/'.$netsuite->dataProductions->id)}}"
            target="_blank">{{$netsuite->dataProductions->prodpur->no_po}}</a>
        @endif
        <br>{{$netsuite->dataProductions->no_lpah}}
        @endif
        @endif
        @if($netsuite->tabel=="retur")
        @if($netsuite->dataRetur->data_order)
        <br><a href="{{url('admin/retur/detail/'.$netsuite->dataRetur->id)}}"
            target="_blank">{{$netsuite->dataRetur->to_customer->nama ?? ""}}</a>
        @if($netsuite->dataRetur)
        <br><a href="{{url('admin/laporan/sales-order/'.$netsuite->dataRetur->data_order->id)}}"
            target="_blank">{{$netsuite->dataRetur->data_order->no_so ?? ""}}</a>
        @endif
        @endif
        @endif
        @if($netsuite->record_type=="transfer_inventory")
        {{-- @php
        $orderBahanBaku = App\Models\Bahanbaku::where('netsuite_id', $netsuite->id)->first();
        @endphp --}}
        @if($netsuite->tabel=="chiller" && ($netsuite->label=="ti_bb_ekspedisi" ||
        $netsuite->label=="ti_finishedgood_ekspedisi"))
        @if($netsuite->dataChillerTI)
        <br>
        {{$netsuite->dataChillerTI->item_name}} ({{$netsuite->dataChillerTI->qty_item}} pcs ||
        {{$netsuite->dataChillerTI->berat_item}} kg) <br>
        {{$netsuite->document_code ?? ""}}
        @endif
        @elseif($netsuite->tabel=="product_gudang")

        @if($netsuite->dataProductGudang)
        <br>
        {{$netsuite->dataProductGudang->nama}}<br>
        {{$netsuite->document_code ?? ""}} <br>
        @if ($netsuite->dataProductGudang)
        ({{ $netsuite->dataProductGudang->qty_awal ?? 0 }} pcs ||
        {{ $netsuite->dataProductGudang->berat_awal }} kg) <br>
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
        <a href="https://6484226.app.netsuite.com/app/accounting/transactions/workord.nl?id={{$netsuite->response_id}}&whence="
            target="_blank">{{$netsuite->response_id}}</a>
        @elseif($netsuite->record_type=="wo_build")
        <a href="https://6484226.app.netsuite.com/app/accounting/transactions/build.nl?whence=&id={{$netsuite->response_id}}"
            target="_blank">{{$netsuite->response_id}}</a>
        @elseif($netsuite->record_type=="transfer_inventory")
        <a href="https://6484226.app.netsuite.com/app/accounting/transactions/invtrnfr.nl?id={{$netsuite->response_id}}&whence="
            target="_blank">{{$netsuite->response_id}}</a>
        @elseif($netsuite->record_type=="item_fulfill")
        <a href="https://6484226.app.netsuite.com/app/accounting/transactions/itemship.nl?whence=&id={{$netsuite->response_id}}"
            target="_blank">{{$netsuite->response_id}}</a>
        @elseif($netsuite->record_type=="itemreceipt" || $netsuite->record_type=="receipt_return")
        <a href="https://6484226.app.netsuite.com/app/accounting/transactions/itemrcpt.nl?id={{$netsuite->response_id}}&whence="
            target="_blank">{{$netsuite->response_id}}</a>
        @elseif($netsuite->record_type=="return_authorization")
        <a href="https://6484226.app.netsuite.com/app/accounting/transactions/rtnauth.nl?id={{$netsuite->response_id}}&whence="
            target="_blank">{{$netsuite->response_id}}</a>
        @else
        {{$netsuite->response_id}}
        @endif

    </td>
    <td>
        <a href="{{route('sync.detail', $netsuite->id)}}" target="_blank">
            @if($netsuite->paket_id=="0")
            <span class="status status-info">parent</span>
            @else
            {{$netsuite->paket_id}}
            @endif
        </a>
    </td>
    <td>


        <button type="button" class="mb-1 btn btn-blue" data-toggle="modal"
            data-target="#myModal{{$netsuite->id}}"><span class="fa fa-send"></span></button>
        <button type="button" class="mb-1 btn btn-success" data-toggle="modal"
            data-target="#myModalRsp{{$netsuite->id}}"><span class="fa fa-download"></span></button>

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
                        Created by : {{ $netsuite->dataUsers->name ?? ""}}
                        @if($netsuite->tabel=="orders")
                        @if($netsuite->dataOrders)
                        <br>{{$netsuite->dataOrders->no_so}}
                        <br>{{$netsuite->dataOrders->nama}}
                        @endif
                        @endif
                        @if($netsuite->tabel=="productions")
                        @if($netsuite->dataProductions)
                        <br>{{$netsuite->dataProductions->prodpur->no_po}}
                        <br>{{$netsuite->dataProductions->no_lpah}}
                        @endif
                        @endif
                        @if($netsuite->tabel=="retur")
                        @if($netsuite->dataRetur)
                        <br>{{$netsuite->dataRetur->to_customer->nama ?? ""}}
                        <br>{{$netsuite->dataRetur->no_so ?? "#non-so"}}
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
        <a href="{{ route('sync.showsync', $netsuite->id) }}" target="_blank" class="mb-1 btn btn-warning"><i
                class="fa fa-edit"></i></a>
        <a href="{{ route('sync.deleteNetsuite', $netsuite->id) }}" onclick="return confirm('Hapus integasi?')"
            class="mb-1 btn btn-danger"><i class="fa fa-trash"></i></a>

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
        <br><button type="button" class="color-red" data-toggle="modal"
            data-target="#myModalError{{$netsuite->id}}">Lihat data</button>

        @endif

    </td>

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