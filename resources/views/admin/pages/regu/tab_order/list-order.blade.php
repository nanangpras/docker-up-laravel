<div>

    @if(count($pending)=="0")
        <div class="alert alert-danger">Item Order Belum Tersedia</div>
    @endif
    <section class="panel">
        <div class="">

            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped table-bordered table-small">
                    <thead>
                        <tr>
                            <th class="text-center" rowspan="2">No</th>
                            <th class="text-center" rowspan="2">NomorSO</th>
                            <th class="text-center" rowspan="2">Customer</th>
                            <th class="text-center" rowspan="2">Item</th>
                            <th class="text-center" rowspan="2">Jenis</th>
                            <th class="text-center" rowspan="2">Part</th>
                            <th class="text-center" rowspan="2">Memo</th>
                            <th class="text-center" rowspan="2">Bumbu</th>
                            <th class="text-center" colspan="2">Order</th>
                            <th class="text-center" colspan="2">Fulfillment</th>
                            <th class="text-center" rowspan="2">Status</th>
                        </tr>
                        <tr>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Berat</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Berat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pending as $row)
                        <tr
                        @if($row->order_status_so=="Closed") style="background-color: red; color:white" @endif
                        @if($row->order_status_so=="Pending Fulfillment")
                            @if($row->edit_item==1) style="background-color: #FFFF8F" @endif
                            @if($row->edit_item==2) style="background-color: #FFEA00" @endif
                            @if($row->edit_item==3) style="background-color: #FDDA0D" @endif
                        @endif
                        @if($row->delete_at_item!=NULL) style="background-color: red; color:white" @endif
                        >
                            <td>{{ $loop->iteration}}</td>
                            <td>{{ $row->no_so }}<br>
                                <span class="small">{{date('d/m/y H:i:s', strtotime($row->created_at_order))}}</span>
                            </td>
                            <td>{{ $row->cust_nama }}</td>
                            <td>{{ $row->nama_detail }}
                            </td>
                            <td>
                                @php 
                                    $jenis = "<span class='status status-info pull-right'>FRESH</span>";
                                    if (str_contains($row->nama_detail, 'FROZEN')) {
                                        $jenis = "<span class='status status-danger pull-right'>FROZEN</span>";
                                    }
                                @endphp
                                {!!$jenis!!}
                            </td>
                            <td>{{ $row->part }}</td>
                            <td>{{ $row->memo }}</td>
                            <td>{{ $row->bumbu }}</td>
                            <td class="text-right">{{ number_format($row->qty) }}</td>
                            <td class="text-right">{{ number_format($row->berat, 2) }}</td>
                            <td class="text-right">@if($row->fulfillment_qty){{ number_format($row->fulfillment_qty) }} @endif</td>
                            <td class="text-right">@if($row->fulfillment_berat){{ number_format($row->fulfillment_berat, 2) }}@endif</td>
                            <td>
                                @if ($row->free_stock)
                                    @if (((Auth::user()->account_role == 'superadmin') || App\Models\User::setIjin(33)) ? TRUE : (($row->user_id == Auth::user()->id) ? TRUE : FALSE))
                                        <a href="{{ route('regu.request_view', [$row->id, 'regu' => $regu]) }}" class="btn btn-outline-primary btn-block btn-sm py-0 rounded-0" target="_blank">Lihat</a>
                                    @endif
                                @endif
                                @if($row->edit_item>0)<span class="text-small status status-warning">EditKe{{$row->edit_item}} </span>@endif
                                @if($row->delete_at_item!=NULL) <span class="text-small status status-danger">Batal </span>@endif
                            </td>
                        </tr>
                        
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <script>
            $(".select2").select2({
                theme: "bootstrap4"
            });
            </script>
            
            <script>
            $(".proses_produksi").on('click', function() {
            
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
            
                var id              =   $(this).data('id') ;
                var item            =   $(this).data('item') ;
                var page            =   $(this).data('page') ;
                var menunggu        =   $("#menunggu:checked").val();
                var tanggal         =   $("#tanggal_request").val();
                var cari            =   encodeURIComponent($("#cari_request").val());
            
                var plastik         =   $('#plastik' + id).val();
                var jumlah_plastik  =   $('#jumlah_plastik' + id).val();
                var parting         =   $('#part' + id).val();
                var berat           =   $('#berat' + id).val();
                var jumlah          =   $('#jumlah' + id).val();
                var sub_item        =   $('#sub_item' + id).val();
            
                if (plastik == 'Curah') {
                    var next = 'TRUE';
                } else {
                    if (jumlah_plastik > 0) {
                        var next = 'TRUE';
                    }
                }
            
                if (next != 'TRUE') {
                    showAlert('Lengkapi data plastik');
                } else {
            
                    $.ajax({
                        url: "{{ route('regu.store') }}",
                        method: "POST",
                        data: {
                            jenis           :   "{{ $regu }}",
                            item            :   item,
                            berat           :   berat,
                            jumlah          :   jumlah,
                            parting         :   parting,
                            plastik         :   plastik,
                            jumlah_plastik  :   jumlah_plastik,
                            tujuan_produksi :   'chillerfg',
                            sub_item        :   sub_item,
                            orderitem       :   id
                        },
                        success: function(data) {
                            $.ajax({
                                url: "{{ route('regu.store') }}",
                                method: "POST",
                                data: {
                                    key         :   'selesaikan',
                                    jenis       :   "{{ $regu }}",
                                    orderitem   :   id
                                },
                                success: function(data) {
                                    if (data.status == 400) {
                                        showAlert(data.msg);
                                    } else {
                                        $.ajax({
                                            url: "{{ route('regu.store') }}",
                                            method: "POST",
                                            data: {
                                                key     :   'selesaikan',
                                                jenis   :   "{{ $regu }}",
                                                cast    :   'approve',
                                                id      :   data.freestock_id,
                                            },
                                            success: function(data) {
                                                $('.modal-backdrop').remove();
                                                $('body').removeClass('modal-open');
            
                                                $("#data_request").attr("style", "display: none") ;
                                                $("#loading_request").attr("style", "display: block") ;
                                                $("#data_request").load("{{ route('regu.request_order', ['key' => 'view']) }}&regu={{ $regu }}&tanggal=" + tanggal + "&cari=" + cari + "&menunggu=" + menunggu + "&page=" + page, function() {
                                                    $("#data_request").attr("style", "display: block") ;
                                                    $("#loading_request").attr("style", "display: none") ;
                                                }) ;
                                                showNotif('Produksi berhasil diselesaikan');
                                            }
                                        });
                                    }
                                }
                            });
            
                        }
                    });
            
                }
            })
            </script>
            

        </div>
    </section>

    <br>
    {{-- {{ $pending->appends(\Illuminate\Support\Facades\Request::except('page'))->links() }} --}}
</div>

<script>

    $('.pagination a').on('click', function(e) {
        showNotif("Loading ...")
        e.preventDefault();
        var url = $(this).attr('href');
        $.ajax({
            url: url,
            method: "GET",
            success: function(response) {
                $('#list_order').html(response);
            }

        });
    });

</script>
