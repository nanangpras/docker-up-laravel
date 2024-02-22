@php
$qty = 0;
$verifikasi = 0;
@endphp
<div class="card mb-3">
    <div class="card-body p-2">
        <div class="row">
            <div class="col-sm-4 col-lg mb-2 pr-sm-1">
                <div class="card">
                    <div class="card-header">Total Customer</div>
                    <div class="card-body p-2">
                        <div class="row mb-1">
                            <div class="col ">
                                <div class="border text-center">
                                    <div class="font-weight-bold">{{ number_format($totalsum['sumcustomer']) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-lg mb-2 px-sm-1">
                <div class="card">
                    <div class="card-header">Total Qty</div>
                    <div class="card-body p-2">
                        <div class="row mb-1">
                            <div class="col ">
                                <div class="border text-center">
                                    <div class="font-weight-bold" id="totalqty"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-lg mb-2 px-sm-1">
                <div class="card">
                    <div class="card-header">Total Berat</div>
                    <div class="card-body p-2">
                        <div class="row mb-1">
                            <div class="col ">
                                <div class="border text-center">
                                    <div class="font-weight-bold">{{ number_format($totalsum['sumberat']) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-lg mb-2 pl-sm-1">
                <div class="card">
                    <div class="card-header">Total Order</div>
                    <div class="row">
                        <div class="col">
                            <div class="border text-center">
                                <div class="font-weight-bold" id="totalorder"></div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="card-body p-2">
                        <div class="row mb-1">
                            <div class="col pr-1">
                                <div class="border text-center">
                                    <div class="small">Fresh</div>
                                    <div class="font-weight-bold">1</div>
                                </div>
                            </div>
                            <div class="col pl-1">
                                <div class="border text-center">
                                    <div class="small">Frozen</div>
                                    <div class="font-weight-bold">2</div>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                    <div class="card-body p-2">
                        <div class="row mb-1">
                            <div class="col pr-1">
                                <div class="border text-center">
                                    <div class="small">Menunggu Verifikasi</div>
                                    <div class="font-weight-bold" id="totalverifikasi"></div>
                                </div>
                            </div>
                            <div class="col pl-1">
                                <div class="border text-center">
                                    <div class="small">Order Terverifikasi</div>
                                    <div class="font-weight-bold" id="totalorderterverifikasi">{{ number_format($totalsum['sumso']) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<a href="{{ route('regu.index', array_merge($_GET, ['key' => 'parking_orders', 'get' => 'unduh'])) }}" class="btn btn-success btn-sm float-right mb-2">Unduh</a>

<table class="table table-sm table-striped table-bordered">
    <thead class="sticky-top bg-white">
        <tr>
            <th class="text-center" rowspan="2">No</th>
            <th class="text-center" rowspan="2">Kirim</th>
            <th class="text-center" rowspan="2">Nomor SO</th>
            <th class="text-center" rowspan="2">Customer</th>
            <th class="text-center" rowspan="2">Item</th>
            <th class="text-center" rowspan="2">Kategori</th>
            <th class="text-center" rowspan="2">Bumbu</th>
            <th class="text-center" rowspan="2">Memo</th>
            <th class="text-center" rowspan="2">Plastik</th>
            <th class="text-center" colspan="4">Order</th>
        </tr>
        <tr>
            <th class="text-center">Part</th>
            <th class="text-center">Ekor/Pcs</th>
            <th class="text-center">Pack</th>
            <th class="text-center">Berat</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datas as $i => $row)
        <tr class="small"
        @if ($row->deleted_at != NULL) style="background-color:pink" @endif
        @if ($row->updated_at != $row->created_at) style="background-color: #FFFF8F" @endif>
            <td>{{ $loop->iteration }}</td>
            <td>{{ date('d/m/Y', strtotime($row->tanggal_kirim)) }}</td>
            <td>{{ $row->no_so }} @if ($row->no_so) @php $verifikasi += 1 @endphp @endif</td>
            <td>{{ $row->nama_customer }}</td>
            <td>{{ $row->item_nama }}
                @if(date('Y-m-d', strtotime($row->created_at_order))==$row->tanggal_kirim)
                <br><span class="small red">*Prioritas Same Day</span>
                @endif
            </td>
            <td>
                @if ( App\Models\Item::where('nama', $row->item_nama)->orderBy('id', 'DESC')->withTrashed()->first())
                    {{ App\Models\Category::where('id', App\Models\Item::where('nama', $row->item_nama)->orderBy('id', 'DESC')->withTrashed()->first()->category_id)->first()->nama }}

                @else 

                    Kategori item {{ $row->item_nama }} tidak ditemukan
                @endif
            </td>
            <td>{{ $row->bumbu }}</td>
            <td>{{ $row->memo }}</td>
            <td>
                @if ($row->plastik == '1') Meyer @endif
                @if ($row->plastik == '2') Avida @endif
                @if ($row->plastik == '3') Polos @endif
                @if ($row->plastik == '4') Curah @endif
                @if ($row->plastik == '5') Mojo @endif
                @if ($row->plastik == '6') Other @endif
            </td>
            <td class="text-right">{{ $row->parting }}</td>
            <td class="text-right">@if(strpos(strtolower($row->memo), "ekor") || strtolower($row->memo) == "ekor"){{ number_format($row->qty) }} @php $qty += $row->qty @endphp @endif</td>
            <td class="text-right">@if(strpos(strtolower($row->memo), "pack") || strtolower($row->memo) == "pack"){{ number_format($row->qty) }} @php $qty += $row->qty @endphp @endif</td>
            <td class="text-right"> {{number_format($row->berat)}}</td>
        </tr>
        @endforeach
        <input type="hidden" id="sumberatqty" value="{{ $qty }}">
        <input type="hidden" id="verifikasi" value="{{ $verifikasi }}">
    </tbody>
</table>
<script>
 $(function() {
    $('#totalqty').html($('#sumberatqty').val())
    $('#totalverifikasi').html($('#verifikasi').val())
    let totalorder = parseInt($('#totalorderterverifikasi').text().replace(/,+/g,',')) + parseInt($('#totalverifikasi').text().replace(/,+/g,','))
    $('#totalorder').html(totalorder)
});

</script>
{{-- <div id="paginate_parking_orders">
    {{ $datas->appends($_GET)->onEachSide(1)->links() }}
</div>
<script>
    $('#paginate_parking_orders .pagination a').on('click', function(e) {
        e.preventDefault();
        showNotif('Menunggu');

        url = $(this).attr('href');
        $.ajax({
            url: url,
            method: "GET",
            success: function(response) {
                $('#parking_orders').html(response);
            }
        });
    });
</script> --}}
