<div  id="export-table">
@foreach ($data as $row)
<section class="panel">
    <div class="card-header font-weight-bold">
        @if ($row->no_urut)
        <div class="float-right status status-success">No Urut {{ $row->no_urut }}</div>
        @endif
        Ekspedisi {{ $row->tanggal }} @if ($row->status == 4) <span class="status status-success">Selesai</span> @endif
    </div>
    <div class="card-body p-2">
        <table class="table default-table">
            <tbody>
                <tr>
                    <th style="width: 140px">Nama Driver</th>
                    <td>{{ $row->nama ?? 'BELUM DIPILIH' }}</td>
                </tr>
                @if ($row->kernek)
                <tr>
                    <th>Nama Kernek</th>
                    <td>{{ $row->kernek ?? '' }}</td>
                </tr>
                @endif
                <tr>
                    <th>Nomor Polisi</th>
                    <td>{{ $row->no_polisi ?? 'BELUM DIPILIH' }}</td>
                </tr>
                <tr>
                    <th>Wilayah</th>
                    <td>{{ $row->wilayah->nama ?? 'BELUM DIPILIH' }}</td>
                </tr>
                <tr>
                    <td colspan="2"><a href="{{ route('ekspedisi.show', $row->id) }}" class="btn btn-primary">Detail</a></td>
                </tr>
            </tbody>
        </table>

        <table class="table default-table">
            <thead>
                <tr>
                    <th>Nomor SO</th>
                    <th>Customer</th>
                    <th>Tanggal Kirim</th>
                    <th>Qty</th>
                    <th>Berat</th>
                    <th>Keranjang</th>
                    <th>Status SO</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_qty      = 0;
                    $total_berat    = 0;
                    $total_keranjang    = 0;
                @endphp
                @foreach ($row->eksrute as $item)
                @php
                    $total_qty          = $total_qty+$item->qty;
                    $total_berat        = $total_berat+$item->berat;
                    $total_keranjang    = $total_keranjang+$item->keranjang;
                    $getOrder           = App\Models\Order::where('no_so', $item->no_so)->first();

                    if ($getOrder) {
                        $getKeranjang   = App\Models\Bahanbaku::where('order_id', $getOrder->id)->sum('keranjang');
                    }

                    $getStatusSO        = $item->marketing_so->status ?? '';
                @endphp
                <tr>
                    <td>{{ $item->no_so }}</td>
                    <td>{{ $item->marketing_so->socustomer->nama ?? $item->order_so->nama }}</td>
                    <td>{{ $item->marketing_so->tanggal_kirim ?? '' }}</td>
                    <td class="text-right">{{ $item->qty }}</td>
                    <td class="text-right">{{ number_format($item->berat, 2) }} </td>
                    @if (isset($getOrder))
                    <td class="text-right">{{ $getKeranjang }}</td>
                    @else
                    <td class="text-right">-</td>
                    @endif
                    <td>{{ $getStatusSO == 3 ? 'Verified' : 'Unverified' }}</td>
                    <td>
                    <button class="float-right btn btn-outline-primary rounded-0" data-toggle="modal" data-target="#pindah{{ $item->id }}">Edit</button>
                    <button class="float-right btn btn-outline-success rounded-0 btnDetail" data-toggle="modal" id="btnDetail" data-target="#detailSummaryRute" data-noso="{{$item->no_so}}">Detail</button>
                    {{-- @if ($item->status == 2) Proses Loading @endif
                    @if ($item->status == 3) Dalam Perjalanan @endif
                    @if ($item->status == 4) Terkirim @endif --}}
                    Terkirim
                    </td>
                </tr>

                <div class="modal fade" id="pindah{{ $item->id }}" tabindex="-1" aria-labelledby="pindah{{ $item->id }}Label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="pindah{{ $item->id }}Label">Pindah Supir</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <label><b>Edit Qty dan Berat</b></label>
                                <div class="row">
                                    <div class="col pr-1">
                                        Qty
                                        <input class="form-control" type="number" value="{{ $item->qty }}" id="jumlahsummaryqty{{ $item->id }}" required>
                                    </div>
                                    <div class="col px-1">
                                        Berat
                                        <input class="form-control" type="number" value="{{ $item->berat }}" id="jumlahsummaryberat{{ $item->id }}" required>
                                    </div>
                                    <div class="col-auto pl-1">
                                        &nbsp;
                                        <button type="submit" class="btn btn-success btn-block updatesummaryrute" data-id="{{ $item->id }}">Edit</button>
                                    </div>
                                </div>
                                <hr>
                                <label><b>Pindah Supir</b></label>
                                <div class="row">
                                    <div class="col pr-1">
                                        <div class="form-group">
                                            <select id="pindahkan{{ $item->id }}" class="form-control select2" data-placeholder="Pilih Ekspedisi" data-width="100%">
                                                <option value=""></option>
                                                @foreach ($data as $list)
                                                @if ($list->id != $row->id)
                                                <option value="{{ $list->id }}">@if ($list->no_urut) No Urut {{ $list->no_urut }} | @endif {{ $list->nama }} - {{ $list->nopol->nama ?? '' }}</option>
                                                @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-auto pl-1">
                                        <div class="form-group">
                                            <button type="button" class="btn btn-primary pindah_supir" data-id="{{ $item->id }}">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td></td>
                    <td>Total</td>
                    <td></td>
                    <td class="text-right">{{$total_qty}}</td>
                    <td class="text-right">{{number_format($total_berat,2)}}</td>
                    <td class="text-right">{{$total_keranjang}}</td>
                </tr>
            </tfoot>
        </table>

        <div class="border-top pt-2">
            <div class="row">
                <div class="col">
                    @if (env('NET_SUBSIDIARY', 'EBA') == 'EBA')
                    <a href="{{ route('ekspedisi.index', ['key' => 'unduh', 'id' => $row->id]) }}" class="btn btn-outline-success"><i class="fa fa-file-excel-o"></i> Excel</a>
                    @endif
                    <a href="{{ route('ekspedisi.index', ['key' => 'pdf', 'id' => $row->id]) }}" class="btn btn-outline-danger"><i class="fa fa-file-pdf-o"></i> PDF</a>
                </div>
                <div class="col text-right">
                    @if ($row->status != 4)
                        {{-- <button class="btn btn-outline-danger kirim mr-2" data-type="hapus_ekspedisi" data-id="{{ $row->id }}">Hapus</button> --}}
                        {{-- <button class="btn btn-{{ $row->status == 2 ? 'primary' : 'warning' }} kirim" data-type="{{ $row->status == 2 ? 'kirim' : 'batal_kirim' }}" data-id="{{ $row->id }}">@if ($row->status == 3) Batal @endif Kirim</button> --}}

                        {{-- @if ($row->status == 3)
                        <button class="btn btn-success ml-2 kirim" data-type="selesai" data-id="{{ $row->id }}">Selesai</button>
                        @endif --}}
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="detailSummaryRute" tabindex="-1" aria-labelledby="detailSummaryRute" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pindahabel">Detail Summary Ekspedisi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <h5 id="spinnerLoading" style="display: none" class="text-center"><i class="fa fa-refresh fa-spin"></i> Loading....</h5>
                <div class="content_summary_rute"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</section>
@endforeach
</div>


<script>
    $('.select2').select2({
        theme: 'bootstrap4'
    })
</script>

<script>
$(".kirim").on('click', function() {
    var id              =   $(this).data('id') ;
    var tipe            =   $(this).data('type') ;
    var awal            =   $("#tanggal_awal").val() ;
    var akhir           =   $("#tanggal_akhir").val() ;
    var tanggal_kirim   =   $("#tanggal_kirim").val() ;
    var cari            =   $("#cari").val() ;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(".kirim").hide() ;

    $.ajax({
        url: "{{ route('ekspedisi.store') }}",
        method: "POST",
        data: {
            id  :   id,
            key :   tipe
        },
        success: function(data) {
            if (data.status == 400) {
                showAlert(data.msg);
            } else {
                showNotif(data.msg);
                $("#data_riwayat").load("{{ route('ekspedisi.riwayat', ['key' => 'data_riwayat']) }}&tanggal_awal=" + awal + "&tanggal_akhir=" + akhir) ;
                $("#data_so").load("{{ route('ekspedisi.index', ['key' => 'sales_order']) }}&tanggal_kirim=" + tanggal_kirim + "&cari=" + cari) ;
            }
            $('.kirim').show() ;
        }
    });
})
</script>


<script>
    $(document).ready(function(){
        var html  = $('#export-table').html();
        $('#html-bb-fresh').val(html);

        $(".btnDetail").click(function () { 
            $("#spinnerLoading").show();
            var nomer_so = $(this).data("noso");
            $.ajax({
                type: "GET",
                url: "{{route('ekspedisi.riwayat')}}",
                data: {
                    'key' : "data_riwayat",
                    'get' : "detail_item",
                    nomer_so : nomer_so
                },
                success: function (response) {
                    $(".content_summary_rute").html(response);
                    $("#spinnerLoading").hide();
                }
            });
        });
    })
</script>


<script>
$('.updatesummaryrute').on('click', function(){
    var id      =   $(this).data('id') ;
    var awal    =   $("#tanggal_awal").val() ;
    var akhir   =   $("#tanggal_akhir").val() ;
    var cari    =   encodeURIComponent($("#cari").val()) ;

    $('.updatesummaryrute').hide() ;

    $.ajax({
        method: 'POST',
        url: "{{ route('ekspedisi.update') }}",
        data: {
            '_token'        :   $('input[name=_token]').val(),
            key             :   'update',
            idsummaryrute   :   id,
            qty             :   $('#jumlahsummaryqty' + id).val(),
            berat           :   $('#jumlahsummaryberat' + id).val()

        },
        dataType: 'json',
        success: res =>{
            $("#summaryrute .close").click()
            showNotif(res.msg)
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            $("#loading_riwayat").attr('style', 'display: block') ;
            $("#data_riwayat").attr('style', 'display: none') ;
            $("#data_riwayat").load("{{ route('ekspedisi.riwayat', ['key' => 'data_riwayat']) }}&tanggal_awal=" + awal + "&tanggal_akhir=" + akhir, function() {
                $("#data_riwayat").attr('style', 'display: block') ;
                $("#loading_riwayat").attr('style', 'display: none') ;
            }) ;
            $("#data_so").load("{{ route('ekspedisi.index', ['key' => 'sales_order']) }}&tanggal_kirim=" + tanggal_kirim + "&cari=" + cari) ;
            $('.updatesummaryrute').show() ;
        }
    })
})
</script>

<script>
$(".pindah_supir").on('click', function() {
    var id              =   $(this).data('id') ;
    var pindah          =   $("#pindahkan" + id).val() ;
    var awal            =   $("#tanggal_awal").val() ;
    var akhir           =   $("#tanggal_akhir").val() ;
    var tanggal_kirim   =   $("#tanggal_kirim").val() ;
    var cari            =   encodeURIComponent($("#cari").val()) ;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(".pindah_supir").hide() ;

    $.ajax({
        url: "{{ route('ekspedisi.store') }}",
        method: "POST",
        data: {
            id      :   id,
            pindah  :   pindah,
            key     :   'pindah_supir'
        },
        success: function(data) {
            if (data.status == 400) {
                showAlert(data.msg);
            } else {
                showNotif(data.msg);
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                $("#loading_riwayat").attr('style', 'display: block') ;
                $("#data_riwayat").attr('style', 'display: none') ;
                $("#data_riwayat").load("{{ route('ekspedisi.riwayat', ['key' => 'data_riwayat']) }}&tanggal_awal=" + awal + "&tanggal_akhir=" + akhir, function() {
                    $("#data_riwayat").attr('style', 'display: block') ;
                    $("#loading_riwayat").attr('style', 'display: none') ;
                }) ;
                $("#data_so").load("{{ route('ekspedisi.index', ['key' => 'sales_order']) }}&tanggal_kirim=" + tanggal_kirim + "&cari=" + cari) ;
            }
            $('.pindah_supir').show() ;
        }
    });
})
</script>
