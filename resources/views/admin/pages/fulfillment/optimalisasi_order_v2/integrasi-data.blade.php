    <span class="green">Alamat : {{$order->alamat_kirim}}</span><br>
    <span style="color: #bbbbbb">SO Masuk :
    {{ date('d/m/y H:i:s', strtotime($order->created_at)) }}</span>
    {{-- @if ($order->status == '0')
        <br><a href="javascript:void(0)" class="green close_order" data-status="{{ $order->status }}" data-batal="{{ $order->id }}"><span class="fa fa-history"></span>Buka Kembali Order</a>
    @else
        <br><a href="javascript:void(0)" class="close_order red" data-status="{{ $order->status }}" data-batal="{{ $order->id }}"><span class="fa fa-times"></span>Tutup Order</a>
    @endif --}}

    @if ($order->status == '0')
        <div class="status status-danger pull-right">Dibatalkan</div>
    @else
        {{-- @if ($close_order == true or $order->status > 6) --}}
        @if ($order->status > 6)
            @php 
                $ns = $order->fulfillNetsuite;
            @endphp 
            <div class="pull-right">
                @if($order->no_do=="")
                    @if ($ns)
                        @if($ns->status=="2") <div class="status status-info"> Proses Integrasi <span class="fa fa-refresh fa-spin"></span></div>@endif
                        @if (!empty($ns->failed))
                            <div class="status status-danger">
                                @php
                                    //code...
                                    $resp = json_decode($ns->failed);
                                @endphp

                                Gagal : {{ $resp[0]->message->message ?? $resp[0]->message }}
                            </div>
                        @endif
                    @endif

                @else
                    <span class='status status-danger'>{{$order->no_do}}</span>
                @endif

                @if($ns)
                    @if($ns->status==6)
                        <span class="status status-purple">INTEGRASI HOLD</span>
                        <a href="{{ route('buatso.netsuite_retry', $ns->id) }}" type="button" class="btn btn-blue btn-sm">
                            Proses Ulang
                        </a>
                    @endif
                    @if($ns->status==4)
                        <span class="status status-other">INTEGRASI ANTRIAN</span>
                    @endif
                
                    <a href="https://6484226.app.netsuite.com/app/accounting/transactions/itemship.nl?id={{$ns->response_id}}&whence=" target="_blank"><span class='fa fa-share'></span></a>
                @endif
            </div>

            <div class="status status-success pull-right">Telah Selesai</div>
            @if (User::setIjin('superadmin'))
                {{-- || <a href="javascript:void(0)" class="orange batalkanorder" data-batal="{{ $order->id }}"><span class="fa fa-times"></span> Batalkan Fulfill</a> --}}
            @endif
        @else
            @if(env('NET_SUBSIDIARY', 'CGL') && $divisi=="frozen")

            @else
            <a href="javascript:void(0)" id="selesaikan-fulfillment-{{$order->id}}" data-id="{{ $order->id }}" class="btn btn-red btn-sm pull-right finish-{{$order->id}}">Selesaikan</a>
            @endif
        @endif

    @endif

    <script>
         $('#selesaikan-fulfillment-{{$order->id}}').on('click', function(){
            $('#text-notif').html('Sedang Diproses ...');
            $('#topbar-notification').fadeIn();
            var url_selesaikan          = "{{ route('fulfillment.selesaikan') }}?order_id={{ $order->id }}";
            var idnya                   = "{{ $order->id }}";
            if(confirm('Selesaikan Order {{$order->no_so}} ?')){
                $.ajax({
                    url: url_selesaikan,
                    type: 'GET',
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
                            loadIntegrasiNetsuite("{{$order->id}}");
                        }
                    },
                    error: function(xhr) { 
                        if(xhr.status > 500){
                            showAlert(xhr.statusText);
                            // setTimeout(() => {
                            //     refreshHalaman();
                            // }, 1000);
                        }
                    },
                });
            }
        })
    </script>