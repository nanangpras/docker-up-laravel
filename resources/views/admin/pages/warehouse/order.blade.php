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
                                    <div class="font-weight-bold">{{ number_format($totalsum['sumqty']) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-lg mb-2 px-sm-1">
                <div class="card">
                    <div class="card-header">Total Berat SO</div>
                    <div class="card-body p-2">
                        <div class="row mb-1">
                            <div class="col ">
                                <div class="border text-center">
                                    <div class="font-weight-bold">{{ number_format($totalsum['sumberatso'], 2) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-lg mb-2 px-sm-1">
                <div class="card">
                    <div class="card-header">Total Berat DO</div>
                    <div class="card-body p-2">
                        <div class="row mb-1">
                            <div class="col ">
                                <div class="border text-center">
                                    <div class="font-weight-bold">{{ number_format($totalsum['sumberatdo'], 2) }}</div>
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
                                <div class="font-weight-bold">{{ number_format($totalsum['sumitemfresh'] + $totalsum['sumitemfrozen']) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-2">
                        <div class="row mb-1">
                            <div class="col pr-1">
                                <div class="border text-center">
                                    <div class="small">Fresh</div>
                                    <div class="font-weight-bold">{{ number_format($totalsum['sumitemfresh']) }}</div>
                                </div>
                            </div>
                            <div class="col pl-1">
                                <div class="border text-center">
                                    <div class="small">Frozen</div>
                                    <div class="font-weight-bold">{{ number_format($totalsum['sumitemfrozen']) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<a href="{{ route('warehouse.order', array_merge(['key' => 'unduh_order'], $_GET)) }}" class="btn btn-success float-right mb-3">Unduh</a>
<div class="table-responsive">
    <table class="table table-sm table-hover table-striped table-bordered table-small">
        <thead class="sticky-top bg-white">
            <tr>
                <th class="text-center" rowspan="2">NO</th>
                <th class="text-center" rowspan="2">MKT</th>
                <th class="text-center" rowspan="2">CUSTOMER</th>
                <th class="text-center" rowspan="2">ITEM</th>
                <th class="text-center" rowspan="2">JENIS</th>
                <th class="text-center" rowspan="2">BUMBU</th>
                <th class="text-center" rowspan="2">MEMO</th>
                <th class="text-center" rowspan="2">KERANJANG</th>
                <th class="text-center" colspan="4">ORDER</th>
                <th class="text-center" colspan="4">AKTUAL</th>
                <th class="text-center" rowspan="2" id="thlog"></th>
            </tr>
            <tr>
                <th class="text-center">PART</th>
                <th class="text-center">Ekor/Pcs/Pack</th>
                <th class="text-center">Package</th>
                <th class="text-center">BERAT</th>
                <th class="text-center">PART</th>
                <th class="text-center">Ekor/Pcs/Pack</th>
                <th class="text-center">Package</th>
                <th class="text-center">BERAT</th>
            </tr>
        </thead>
        <tbody>
            <script>
                var ceklog = "{{ $totalsum['countedited'] }}";
                if(ceklog){
                    $("#logaction").hide();
                    $("#thlog").hide();
                    $("#tdlog").hide();
                }
            </script>
            @foreach ($data as $row)
            <tr class="small"
                    @if($row->order_status_so=="Closed") style="background-color: red; color:white" @endif
                    @if($row->order_status_so=="Pending Fulfillment")
                        @if($row->edit_item==1) style="background-color: #FFFF8F" @endif
                        @if($row->edit_item==2) style="background-color: #FFEA00" @endif
                        @if($row->edit_item==3) style="background-color: #FDDA0D" @endif
                    @endif
                    @if($row->delete_at_item!=NULL) style="background-color: red; color:white" @endif
                    >
                <td>{{ $loop->iteration }}</td>
                <td>
                    {{$row->marketing_nama ?? $row->sales_id}}
                </td>
                <td>{{ $row->nama }}<br>
                    <span class="small">{{$row->no_so}}</span>
                    <span class="small">{{$row->wilayah ?? ""}}</span>
                </td>
                <td>{{ $row->nama_detail }}
                    @if(date('Y-m-d', strtotime($row->created_at_order))==$row->tanggal_kirim)
                        <br><span class="small red">*Prioritas Same Day</span>
                    @endif
                </td>
                @php
                    $order_bahan_baku = App\Models\Bahanbaku::where('order_item_id', $row->id)->first();
                @endphp
                <td>
                    @php
                        $jenis = "<span class='small'>FRESH</span>";
                        if (str_contains($row->nama_detail, 'FROZEN')) {
                            $jenis = "<span class='small'>FROZEN</span>";
                        }
                    @endphp
                    {!!$jenis!!}
                </td>
                <td>{{ $row->bumbu }}</td>
                <td>@if($row->memo_header){{ $row->memo_header }} <hr> @endif {{ $row->memo }}</td>
                <td class="text-right">{{ $order_bahan_baku->keranjang ?? '0' }}</td>
                <td class="text-right">{{ $row->part }}</td>
                <td class="text-right">
                    {{-- @if(strpos(strtolower($row->memo), "ekor") || strtolower($row->memo) == "ekor") --}}
                    {{ number_format($row->qty) }}
                    {{-- @endif --}}
                </td>
                <td class="text-right">
                    {{-- @if(strpos(strtolower($row->memo), "pack") || strtolower($row->memo) == "pack") --}}
                    {{ number_format($row->qty) }}
                    {{-- @endif --}}
                </td>
                <td class="text-right">{{ number_format($row->berat, 2) }}</td>
                <td class="text-right"> {{ $row->part }}</td>
                <td class="text-right">
                    {{-- @if(strpos(strtolower($row->memo), "ekor") || strtolower($row->memo) == "ekor") --}}
                    {{ number_format($row->fulfillment_qty) }}
                    {{-- @endif --}}
                </td>
                <td class="text-right">
                    {{-- @if(strpos(strtolower($row->memo), "pack") || strtolower($row->memo) == "pack") --}}
                    {{ number_format($row->fulfillment_qty) }}
                    {{-- @endif --}}
                </td>
                <td class="text-right">{{ str_replace(".", ",",$row->fulfillment_berat) }}</td>
                <td id="logaction">
                    @if ($row->free_stock)

                    @endif
                    @if($row->edit_item>0)<br><span class="text-small status status-warning">EditKe{{$row->edit_item}} </span>@endif
                    @if($row->delete_at_item!=NULL) <br><span class="text-small status status-danger">Batal </span>@endif
                   
                </td>
            </tr>

            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="8">SUB TOTAL</td>
                <td class="text-right">{{ number_format($totalsum['sumparting']) }}</td>
                <td class="text-right">{{ number_format($totalsum['sumqty'], 2) }}</td>
                <td class="text-right">{{ number_format($totalsum['sumqty'], 2) }}</td>
                <td class="text-right">{{ number_format($totalsum['sumberatso'], 2) }}</td>
                <td class="text-right">{{ number_format($totalsum['sumparting']) }}</td>
                <td class="text-right">{{ number_format($totalsum['sumqtydo'], 2) }}</td>
                <td class="text-right">{{ number_format($totalsum['sumqtydo'], 2) }}</td>
                <td class="text-right">{{ number_format($totalsum['sumberatdo'], 2) }}</td>
                <td id="tdlog"></td>
            </tr>
        </tfoot>
    </table>
</div>

<div id="paginate_order">
    {{-- {{ $data->appends($_GET)->onEachSide(1)->links() }} --}}
</div>

<script>
$('#paginate_order .pagination a').on('click', function(e) {
    e.preventDefault();
    showNotif('Menunggu');

    url = $(this).attr('href');
    $.ajax({
        url: url,
        method: "GET",
        success: function(response) {
            $('#warehouse-order').html(response);
        }

    });
});
</script>

