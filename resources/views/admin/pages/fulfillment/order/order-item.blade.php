<table class="table default-table table-small">
    <thead>
        <tr>
            <th width="30%">Nama</th>
            <th width="60">Qty</th>
            <th width="60">Berat</th>
            <th width="50%">Pengalokasian
            </th>
        </tr>
    </thead>
    <tbody>
        @php
            $close_order = false;
        @endphp
        @foreach ($order->daftar_order_full as $item)
            @php
                if ($item->status == 2) {
                    $close_order = true;
                }
            @endphp

            @if(in_array((string)$item->item->category_id, (json_decode(json_encode($kategori)))))
            <tr>
                <td>
                    {{ $item->nama_detail }}
                    <br>
                    @if($jenis=="fresh")
                    <span class="pull-right status status-success">FRESH</span>
                    @endif
                    @if($jenis=="frozen")
                    <span class="pull-right status status-info">FROZEN</span>
                    @endif

                    @if ($item->memo != '')
                        <br>Memo: <span class="blue">{{ $item->memo }}</span>
                    @endif

                    @if (App\Models\Order::getInternalMemo($order->no_so, $item->id) != '')
                        <br> Internal Memo: <span class="status status-warning">{{ App\Models\Order::getInternalMemo($order->no_so, $item->id) }}</span> <br>
                    @endif

                    @if ($item->description_item != '')
                        <br> Description: <span class="status status-info">{{ $item->description_item }}</span>
                    @endif
                    @if ($item->part != '')
                        <br><span class="orange">Potong {{ $item->part }}</span>
                    @endif
                    @if ($item->bumbu != '')
                        <br><span class="green">{{ $item->bumbu }}</span>
                    @endif

                </td>
                <td>{{ number_format($item->qty ?? '0') }}</td>
                <td>{{ number_format($item->berat ?? '0', 2) }} kg</td>
                <td>

                    {{-- Pemenuhan list order item --}}
                    @php
                    $pcs    =   0 ;
                    $kg     =   0 ;
                    $pemenuhan =  $item->bahan_baku;
                    @endphp

                    @include('admin.pages.fulfillment.order.order-bahan-baku-list', ['pemenuhan' => $pemenuhan])

                    @if ($order->status == '0')
                        <span class="status status-danger pull-right">Dibatalkan</span>
                    @else

                        @if($item->fulfillment_berat==0)
                        <a href="#" class="btn btn-default btn-sm show-ket pull-right blue" data-toggle="modal" data-target="#keteranganModal" data-item_id="{{ $item->item_id }}" data-order_id="{{ $item->order_id }}" data-keterangan="{{ $item->tidak_terkirim_catatan }}">Ket Tdk Terkirim</a>
                        @endif

                        @if ($item->status == null)
                            @if ($order->status != '10')

                                @php
                                    $default_location   = "chiller-fg";

                                    if(strpos($item->nama_detail, 'MEMAR') && ($order->sales_channel=="By Product - Paket" || $order->sales_channel=="By Product - Retail")){
                                        $default_location   = "chiller-bb";
                                    }
                                    if(strpos($item->nama_detail, 'FROZEN')){
                                        $default_location   = "frozen";
                                    }
                                    if(in_array($item->sku, [1211810005, 1211830001, 1211840002, 1211820005])){
                                        $default_location   = "chiller-bb";
                                    }

                                    $nama_replace = str_replace("'","",$order->nama);
                                @endphp
                                <button type="button" class="btn btn-default" data-toggle="modal" data-target="#exampleModal" id="form-item-name{{ $item->id }}" onclick="return selected_id('{{ $item->order_id }}','{{ $item->id }}','{{ $item->item_id }}', '{{$default_location}}', '{{$nama_replace}}')">
                                    Pilih Item Dari Chiller <span class="fa fa-chevron-down"></span>
                                </button>
                                
                            @endif
                            <div class="status status-warning status{{ $item->id }}" style="display: none">
                                Selesai dialokasikan
                            </div>
                        @else
                            <div class="status status-success">Terpenuhi</div>
                        @endif
                    @endif

                    @if($item->tidak_terkirim == "1")
                        <br><span class="status status-danger">Tidak terkirim : {{$item->tidak_terkirim_catatan}}</span>
                    @endif
                </td>
            </tr>
            @endif
        @endforeach
        <tr>
            <td colspan="4">
                <span class="green">Alamat : {{$order->alamat_kirim}}</span><br>
                <span style="color: #bbbbbb">SO Masuk :
                {{ date('d/m/y H:i:s', strtotime($order->created_at)) }}</span>

                @if ($order->status == '0')
                    <div class="status status-danger pull-right">Dibatalkan</div>
                @else

                    @if ($close_order == true or $order->status > 6)

                        <div class="pull-right">

                            @if($order->no_do=="")
                                @if ($order->getNetsuite)
                                    @if($order->getNetsuite->status=="2") <div class="status status-info"> Proses Integrasi <span class="fa fa-refresh fa-spin"></span></div>@endif
                                    @if (!empty($order->getNetsuite->failed))
                                        <div class="status status-danger">
                                            @php
                                                //code...
                                                $resp = json_decode($order->getNetsuite->failed);
                                            @endphp

                                            Gagal : {{ $resp[0]->message->message ?? $resp[0]->message }}
                                        </div>
                                    @endif
                                @endif

                            @else
                                <span class='status status-danger'>{{$order->no_do}}</span>
                            @endif

                            @if($order->getNetsuite)
                            @if($order->getNetsuite->status==6)
                                <span class="status status-purple">INTEGRASI HOLD</span>
                                <a href="{{ route('buatso.netsuite_retry', $order->getNetsuite->id) }}"
                                    type="button" class="btn btn-blue btn-sm">
                                    Proses Ulang
                                </a>
                            @endif
                            @if($order->getNetsuite->status==4)
                                <span class="status status-other">INTEGRASI ANTRIAN</span>
                            @endif
                            
                                <a href="https://6484226.app.netsuite.com/app/accounting/transactions/itemship.nl?id={{$order->getNetsuite->response_id}}&whence=" target="_blank"><span class='fa fa-share'></span></a>
                            @endif

                        </div>

                        <div class="status status-success pull-right">Telah Selesai</div>
                        @if (User::setIjin('superadmin'))
                            {{-- || <a href="javascript:void(0)" class="orange batalkanorder" data-batal="{{ $order->id }}"><span class="fa fa-times"></span> Batalkan Fulfill</a> --}}
                        @endif
                    @else
                        @if(env('NET_SUBSIDIARY', 'CGL') && $divisi=="frozen")

                        @else
                        <a href="javascript:void(0)" id="selesaikan-fulfillment-{{$order->id}}" class="btn btn-red btn-sm pull-right finish-{{$order->id}}">Selesaikan</a>
                        @endif
                    @endif

                @endif
            </td>
        </tr>
    </tbody>
</table>

<script>
    $('#selesaikan-fulfillment-{{$order->id}}').on('click', function(){
        $('#text-notif').html('Sedang Diproses ...');
        $('#topbar-notification').fadeIn();
        var url_selesaikan          = "{{ route('fulfillment.selesaikan') }}?order_id={{ $order->id }}";
        var idnya                   = "{{ $order->id }}";
        if(confirm('Selesaikan Order {{$order->no_so}} ?')){
            $.ajax({
                url: url_selesaikan,
                type: 'get',
                beforeSend: function() {
                    $('.finish-'+idnya).addClass('disabled');
                },
                success: function(data) {

                    if (data.status == "400") {
                        showAlert(data.msg)
                        $(".fulfill-item").show();
                    } else {
                        setTimeout(() => {
                            showNotif('Alokasi Berhasil Di Selesaikan');
                        },1500)
                        loadOrderItem("{{$order->id}}");
                    }
                }
            });
        }


    })
</script>
