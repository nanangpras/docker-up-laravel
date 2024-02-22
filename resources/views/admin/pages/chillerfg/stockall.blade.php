{{-- {{var_dump($cekCutOff)}} --}}
<a href="{{ route('hasilproduksi.index', ['key' => 'unduh']) }}&tipe=all&tanggal={{ $tanggal }}&tanggalakhir={{ $tanggalakhir }}" class="btn btn-success">Unduh</a>

<div class="table-responsive" id="datahp">
    <table width="100%" id="kategori" class="table table-sm default-table dataTable-stockall">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Lokasi</th>
                <th>Tanggal</th>
                <th>Ekor/Pcs/Pack Awal</th>
                <th>Berat Awal</th>
                <th>Ekor/Pcs/Pack</th>
                <th>Berat</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stock as $i => $item)
            @php
                $sisaQty        = $item->sisaQty;
                $sisaBerat      = number_format((float)$item->sisaBerat, 2, '.', '');
            @endphp
                <tr>
                    <td>{{++$i}}</td>
                    <td>{{ $item->item_name }}
                        @if ($item->selonjor)
                        <br><span class="font-weight-bold text-danger">SELONJOR</span>
                        @endif
                        @php
                            $exp = json_decode($item->label);
                        @endphp

                        @if ($item->asal_tujuan == 'retur')<br><span class="text-info">Customer : {{ $item->customer_name ?? '-' }}</span> @endif

                        <div class="status status-success">
                            <div class="row">
                                <div class="col pr-1">
                                    {{ $item->plastik_nama }}
                                </div>
                                <div class="col-auto pl-1">
                                    <span class="float-right">// {{ $item->plastik_qty }} Pcs</span>
                                </div>
                            </div>
                        </div>
                        @php
                            $cekCutoffChiller = App\Models\Chiller::where('status_cutoff',1)->where('id', $item->id)->first();
                        @endphp
                        @if ($cekCutoffChiller)
                            <div class="status status-danger mt-3">
                                Transaksi data sudah ditutup
                            </div>
                        @endif

                        @if($exp)<br>
                            @if ($exp->additional ?? FALSE) {{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }} {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }} {{ $exp->additional->maras ? 'Tanpa Maras' : '' }} @endif
                            <div class="row text-info">
                                <div class="col pr-1">@if ($exp->sub_item ?? FALSE) Keterangan : {{ $exp->sub_item }} @endif</div>
                                <div class="col-auto pl-1 text-right">@if ($exp->parting->qty ?? "") Parting : {{ $exp->parting->qty }} @endif</div>
                            </div>
                        @endif

                        @if($item->asal_tujuan == 'Open Balance')
                        <br>
                        <div class="status status-info">
                            <div class="row">
                                <div class="col pr-1">
                                    Keterangan: {{ $item->label }}
                                </div>
                            </div>
                        </div>
                        @endif
                    </td>
                    <td>
                        @if($item->kategori=="1")
                        <span class="status status-danger">[ABF]</span>
                        @elseif($item->kategori=="2")
                        <span class="status status-warning">[EKSPEDISI]</span>
                        @elseif($item->kategori=="3")
                        <span class="status status-warning">[TITIP CS]</span>
                        @else
                        <span class="status status-info">[CHILLER]</span>
                        @endif

                    </td>
                    <td>{{ $item->tanggal_produksi }}</td>
                    <td>{{ number_format($item->qty_item) }}</td>
                    <td>{{ number_format($item->berat_item, 2) }} Kg</td>
                    <td>{{ $sisaQty }}</td>
                    <td>{{ $sisaBerat }} Kg</td>
                    <td>{{ \App\Models\Chiller::renameData($item->asal_tujuan) }}</td>
                    <td>
                        @if ($item->status != 1)
                            <a href="{{ route('chiller.show', $item->id) }}" class="btn btn-info">Detail</a>
                        @endif

                    </td>
                </tr>

            @endforeach
        </tbody>
    </table>
    {{-- <div class="paginate_stock">
        {{ $stock->appends($_GET)->onEachSide(1)->links() }}
    </div> --}}
</div>

{{-- <script>
    $('.paginate_stock .pagination a').on('click', function(e) {
            e.preventDefault();
            $(".loading-stockall").attr('style', 'display: block');

            url = $(this).attr('href');
            $.ajax({
                url: url,
                method: "GET",
                success: function(response) {
                    $('#stockall').html(response);
                    $(".loading-stockall").attr('style', 'display: none');
                }

            });
        });
</script> --}}

@section('header')
    {{-- <link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css"/> --}}
@stop

@section('footer')
    {{-- <script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script>
    <script>
        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('.dataTable-stockall')) {
                $('.dataTable-stockall').DataTable().destroy();
            }
            $('.dataTable-stockall').DataTable({
                "bPaginate": true,
                "bLengthChange": false,
                "bFilter": true,
                "bInfo": false,
                "bAutoWidth": false
            });

            $('a[data-toggle="pill"]').on('shown.bs.tab', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
        });
    </script> --}}
@stop