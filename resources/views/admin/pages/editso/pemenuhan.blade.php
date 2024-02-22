@foreach ($pemenuhan as $no => $row)
    @php
        $nomorTI    = App\Models\Netsuite::join('order_bahan_baku', 'order_bahan_baku.netsuite_id', '=', 'netsuite.id')->where('record_type', 'transfer_inventory')->where('netsuite.id', $row->netsuite_id)->first();
    @endphp
    <div class="border-bottom px-2 py-2">
        
        @if ($row->proses_ambil == 'chillerfg' || $row->proses_ambil == 'sampingan')
            <a href="{{ route('chiller.show', $row->chiller_out) }}" target="_blank">{{ ++$no }}.</a>
        @else
            <a href="{{ route('warehouse.tracing', $row->chiller_out) }}" target="_blank">{{ ++$no }}.</a>
        @endif
        {{ $row->nama }}
        ({{ $row->bb_item ?? '#' }} pcs || {{ $row->bb_berat }} Kg) || 
        @if($row->proses_ambil == 'frozen')
            {{ date('d/m/y', strtotime($row->to_product_gudang->created_at ?? date('Y-m-d'))) }}
        @else
            {{ date('d/m/y', strtotime($row->to_chiller->tanggal_produksi ?? date('Y-m-d'))) }}
        @endif
        <br>
        <span class="blue">{{$row->no_do ?? "#DO"}}</span> 
        || 
        <a href="{{url('admin/sync-detail/'.$row->netsuite_id)}}" target="_blank"><span class="green">{{$nomorTI->document_no ?? "#TI"}}</span></a>
        || 
        <a href="{{url('admin/sync/'.$row->netsuite_id)}}" target="_blank" class="orange"><span class="fa fa-share"></span> 
            @if($row->netsuite_id=="")
            TI Belum Terbentuk
            @else
            TI Netsuite
            @endif
        </a>
        @if (isset($nomorTI))
            @if ($nomorTI->document_no != NULL)
                @if(Auth::user()->account_role == 'superadmin')
                ||
                <a href="javascript:void(0)" class="green modalEditPemenuhan{{ $row->id }}" data-id="{{ $row->id }}" data-netsuite_id="{{ $row->netsuite_id }}"  
                    data-orderitemid="{{ $row->order_item_id }}" data-nama="{{ $row->nama}}" data-nodo="{{ $row->no_do}}" data-bbitem="{{ $row->bb_item}}" 
                    data-bbberat="{{ $row->bb_berat }}" data-prosesambil="{{ $row->proses_ambil }}" data-chillerout="{{ $row->chiller_out }}" 
                    data-toggle="modal" data-target="#editPemenuhan{{$row->id}}">
                    <span class="fa fa-edit"></span> Edit
                </a>

                @endif
            @else
            ||
            <a href="javascript:void(0)" class="green modalEditPemenuhan{{ $row->id }}" data-id="{{ $row->id }}" data-netsuite_id="{{ $row->netsuite_id }}"  
                data-orderitemid="{{ $row->order_item_id }}" data-nama="{{ $row->nama}}" data-nodo="{{ $row->no_do}}" data-bbitem="{{ $row->bb_item}}" 
                data-bbberat="{{ $row->bb_berat }}" data-prosesambil="{{ $row->proses_ambil }}" data-chillerout="{{ $row->chiller_out }}"
                data-toggle="modal" data-target="#editPemenuhan{{$row->id}}">
                <span class="fa fa-edit"></span> Edit
            </a>

            @endif
        @else 
        ||
        <a href="javascript:void(0)" class="green modalEditPemenuhan{{ $row->id }}" data-id="{{ $row->id }}" data-netsuite_id="{{ $row->netsuite_id }}"  
            data-orderitemid="{{ $row->order_item_id }}" data-nama="{{ $row->nama}}" data-nodo="{{ $row->no_do}}" data-bbitem="{{ $row->bb_item}}" 
            data-bbberat="{{ $row->bb_berat }}" data-prosesambil="{{ $row->proses_ambil }}" data-chillerout="{{ $row->chiller_out }}"
            data-toggle="modal" data-target="#editPemenuhan{{$row->id}}">
            <span class="fa fa-edit"></span> Edit
        </a>

        @endif
        {{-- @if($row->no_do=="" && $row->netsuite_id=="") --}}
        ||
        <a href="javascript:void(0)" class="red" data-id="{{ $row->id }}" data-orderitemid="{{ $row->order_item_id }}" onclick="removePemenuhan({{ $row->id }},{{ $row->order_item_id }})">
            <span class="fa fa-trash"></span> Hapus
        </a>
        {{-- @endif --}}

        || <span>{{date('d/m/Y H:i:s',strtotime($row->created_at))}}</span>

        @if($row->no_do !="" || $row->netsuite_id != "")
        || <span class="status status-warning mb-1">NS Terbentuk</span>
        @endif

        {{-- BATAS BUTTON WO2 --}}
        
        @php
        $ceklog     = App\Models\Adminedit::where('table_id', $row->id)->where('table_name','order_bahan_baku')->where('type', 'edit')->get()->count();
        $cekReset   = App\Models\Adminedit::where('table_id', $row->order_item_id)->where('table_name','order_bahan_baku')->where('type', 'reset')->get()->count();
        @endphp
        @if($ceklog) 
        || 
            <button class="btn btn-info btn-xs mb-2 px-2" data-toggle="modal" data-target="#historylogreset-{{ $row->id}}">Riwayat Edit</button>
        @endif


        @if ($row->proses_ambil == 'chiller-fg')
            @if ($row->to_chiller->label)
                @php
                    $exp = json_decode($row->to_chiller->label);
                @endphp
                @if ($exp->parting->qty)
                    <span class="status status-info">Parting {{ $exp->parting->qty }}</span>
                @endif
                @if ($exp->additional->tunggir)
                    <span class="status status-warning">Tanpa Tunggir</span>
                @endif
                @if ($exp->additional->maras)
                    <span class="status status-warning">Tanpa Maras</span>
                @endif
                @if ($exp->additional->lemak)
                    <span class="status status-warning">Tanpa Lemak</span>
                @endif
            @endif
        @endif
    </div>


    {{-- LOG RESET --}}
    <div class="modal fade" id="historylogreset-{{ $row->id }}" aria-labelledby="riwayatLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width: 800px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Riwayat Edit/Reset</h4>
                    <button type="button" class="close"
                        data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    
                    @php
                        $jsondata = App\Models\Adminedit::where('table_id', $row->order_item_id)->where('table_name', 'order_bahan_baku')->where('type', 'reset')->get();
                        $json = [];
                        $dataReset = [];
                        $lists = [];
                    @endphp

                    {{-- EDIT --}}
                    @php
                        $cekJsonEdit = App\Models\Adminedit::where('table_id', $row->id)->where('table_name', 'order_bahan_baku')->where('type', 'edit')->get();
                        $jsonEdit    = [];
                        $dataedit    = [];
                        $listsEdit   = [];
                    @endphp
                    {{-- <h5>DATA EDIT</h5> --}}
                    @foreach ($cekJsonEdit as $keysEdit => $rowsEdit)
                        <table class="table table-bordered ">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    @if($keysEdit == 0)
                                        <th>Di Buat </th>
                                    @else
                                        <th>Di Edit </th>
                                    @endif
                                    <th>Nama</th>
                                    <th>No. DO</th>
                                    <th>Ekor</th>
                                    <th>Berat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $jsonEdit[] = json_decode($rowsEdit->data, true);
                                    $dataedit[] = $rowsEdit->content;
                                @endphp
                                
                                @if(isset($jsonEdit[$keysEdit]['header']))
                                <tr>
                                    <td>{{ $keysEdit+1 }}</td>
                                    @if($keysEdit == 0)
                                    <td>{{ date('d-m-Y H:i:s',strtotime($jsonEdit[$keysEdit]['header']['created_at'])) }}</td>
                                    @else
                                    <td>{{ $rowsEdit->created_at }}</td>
                                    @endif
                                    <td>{{ $jsonEdit[$keysEdit]['header']['nama'] ?? '#' }} </td>
                                    <td
                                        @if($jsonEdit[$keysEdit-1]['header']['no_do'] ?? FALSE && $jsonEdit[$keysEdit]['header']['no_do'] ?? FALSE)
                                            @if($jsonEdit[$keysEdit]['header']['no_do'] != $jsonEdit[$keysEdit-1]['header']['no_do'])
                                                style="background-color: #fde0dd"
                                            @endif
                                        @endif
                                    >{{ $jsonEdit[$keysEdit]['header']['no_do'] }}</td>
                                    <td
                                        @if($jsonEdit[$keysEdit-1]['header']['bb_item'] ?? FALSE && $jsonEdit[$keysEdit]['header']['bb_item'] ?? FALSE)
                                            @if($jsonEdit[$keysEdit]['header']['bb_item'] != $jsonEdit[$keysEdit-1]['header']['bb_item'])
                                                style="background-color: #fde0dd"
                                            @endif
                                        @endif
                                    >{{ $jsonEdit[$keysEdit]['header']['bb_item'] }}</td>
                                    <td
                                        @if($jsonEdit[$keysEdit-1]['header']['bb_berat'] ?? FALSE && $jsonEdit[$keysEdit]['header']['bb_berat'] ?? FALSE)
                                            @if($jsonEdit[$keysEdit]['header']['bb_berat'] != $jsonEdit[$keysEdit-1]['header']['bb_berat'])
                                                style="background-color: #fde0dd"
                                            @endif
                                        @endif
                                    >{{ $jsonEdit[$keysEdit]['header']['bb_berat'] }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    @endforeach
                    {{-- END EDIT --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                        data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- END LOG RESET --}}

    {{-- LOG EDIT --}}
   

    {{-- END LOG EDIT --}}

    <div class="modal fade" id="editPemenuhan{{$row->id}}" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalEditPemenuhan{{$row->id}}Label" aria-hidden="false">
        <div class="modal-dialog">
            <div id="content_modal_edit_pemenuhan{{$row->id}}"></div>
        </div>
    </div>
    <script>
        $(document).ready(function(){
            var btns            = document.getElementsByClassName("modalEditPemenuhan");
            for (var i = 0; i < btns.length; i++) {
                btns[i].addEventListener("click", function() {
                    var current = document.getElementsByClassName("active");
                    current[0].className = current[0].className.replace("active", "");
                    this.className += " active";
                });
            }

            $('.modalEditPemenuhan{{ $row->id }}').on('click', function(e) {
                e.preventDefault()
                var id              = $(this).attr('data-id');
                var netsuite_id     = $(this).attr('data-netsuite_id');
                var orderitemid     = $(this).attr('data-orderitemid');
                var nama            = $(this).attr('data-nama');
                var nodo            = $(this).attr('data-nodo');
                var bb_item         = $(this).attr('data-bbitem');
                var bb_berat        = $(this).attr('data-bbberat');
                var prosesambil     = $(this).attr('data-prosesambil');
                var chillerouts     = $(this).attr('data-chillerout');

                $.ajax({
                    url : "{{ route('editso.pemenuhan', ['key' => 'viewmodaleditpemenuhan']) }}",
                    type: "GET",
                    data: {
                        id          : id,
                        netsuite_id : netsuite_id,
                        orderitemid : orderitemid,
                        nama        : nama,
                        nodo        : nodo,
                        bb_item     : bb_item,
                        bb_berat    : bb_berat,
                        prosesambil : prosesambil,
                        chiller_out : chillerouts,
                    },
                    success: function(data){
                        console.log(data)
                        $('#content_modal_edit_pemenuhan'+id).html(data);
                    }
                });
            })
        })
    </script>
@endforeach

<script>
    function removePemenuhan(id, orderitemid) {
        console.log(orderitemid)
        $.ajax({
            url: "{{ route('editso.deletealokasi') }}?id=" + id,
            type: 'get',
            success: function(data) {
                if (data.status == 200) {
                    // var url_pemenuhan = "{{ route('editso.pemenuhan') }}" + "?order_item_id=" + orderitemid;
                    // $('#order_bahan_baku' + orderitemid).load(url_pemenuhan)
                    // $('#info_order').load("{{ route('editso.pemenuhan') }}?key=info&order_item_id=" + orderitemid);
                    // $('#riwayat_ambil').load("{{ route('editso.pemenuhan') }}?order_item_id=" + orderitemid);
                    // load_penyiapan();
                    showNotif(data.msg)
                    window.location.reload();
                } else {
                    showAlert(data.msg)
                }
                
            }
        });
    }

    function kirimWO2SiapKirim(idItem, bahanBakuId) {
        // editso.pemenuhan
        $.ajax({
            url: "{{ route('editso.pemenuhan') }}",
            data: {
                idItem,
                bahanBakuId,
                key: 'createWO2SiapKirim'
            },
            // type: 'get',
            success: function(data) {
                if (data.status == 200) {
                    showNotif(data.msg)
                    window.location.reload();
                }
                
            }
        });
    }
</script>
