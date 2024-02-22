<div class="form-group">
    <label for="tanggalsummary">Filter</label>
    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01" @endif
        name="tanggalsummary" class="form-control" id="tanggalsummary" placeholder="Tuliskan" value="{{ $tanggal }}"
        autocomplete="off">
</div>

{{-- <div class="border p-2 mb-3">
    <div>Unduh Data Per Tanggal {{ $tanggal }} :</div>
    <a href="{{ route('produksi.summary', ['key' => 'unduh_bb', 'tanggal' => $tanggal]) }}"><i
            class="fa fa-file-excel-o"></i> Pengambilan Bahan Baku</a><br>
    <a href="{{ route('produksi.summary', ['key' => 'unduh_fg', 'tanggal' => $tanggal]) }}"><i
            class="fa fa-file-excel-o"></i> Pengambilan Hasil Produksi</a>
</div> --}}
{{-- @foreach ($item_sama_bb as $item)
@endforeach --}}
<h6 class="text-center">Produksi Kirim WO</h6><br>
<div class="row">
    @php
    $total_bb = 0;
    $total_fg = 0;
    @endphp
    <div class="col-md-6">
        <div class="table-responsive">
            <table class="table default-table" width="100%">
                <thead>
                    <tr>
                        <th class="text-info" colspan="4">Bahan Baku</th>
                    </tr>
                    <tr>
                        <th>No</th>
                        <th>Nama Item</th>
                        <th>Ekor/Pcs/Pack</th>
                        <th>Berat</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $ekor = 0;
                    $berat = 0;
                    @endphp
                    @foreach ($collectionQueryBBWO as $i => $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            {{ App\Models\Item::find($row->item_id)->nama }}
                            @if ($row->type == 'hasil-produksi')
                            <span class="status status-info">FG</span>
                            @elseif($row->type == 'bahan-baku')
                            <span class="status status-danger">BB</span>
                            @endif
                        </td>
                        <td>{{ number_format($row->qty) }}</td>
                        <td>{{ number_format($row->berat, 2) }} Kg</td>
                    </tr>
                    @php
                    $ekor += $row->qty;
                    $berat += $row->berat;
                    @endphp
                    @endforeach
                    <tr>
                        <td></td>
                        <td>Total</td>
                        <td>{{ $ekor }}</td>
                        <td>{{ number_format($berat, 2) }} Kg</td>
                    </tr>
                </tbody>
            </table>
            @php
            $total_bb = $berat;
            $total_bb_new = $berat + $tambahan_kg;
            @endphp
        </div>
    </div>
    <div class="col-md-6">
        <div class="table-responsive">
            <table class="table default-table" width="100%">
                <thead>
                    <tr>
                        <th class="text-info" colspan="4">Hasil Produksi</th>
                    </tr>
                    <tr>
                        <th>No</th>
                        <th>Nama Item</th>
                        <th>Ekor/Pcs/Pack</th>
                        <th>Berat</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $ekor = 0;
                    $berat = 0;
                    @endphp
                    @foreach ($collectionQueryFGWO as $i => $row)
                        @php
                            
                            $item_cat = \App\Models\Item::find($row->item_id);
                            
                            $bom_item = \App\Models\BomItem::where('sku', $item_cat->sku)
                                ->where('bom_id', $bom->id)
                                ->first();

                            $type = ($item_cat->category_id == 4 or $item_cat->category_id == 6 or $item_cat->category_id == 10 or $item_cat->category_id == 16) ? 'By Product' : 'Finished Goods';
                            if ($bom_item) {
                                $type = $bom_item->kategori;
                            }
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ App\Models\Item::find($row->item_id)->nama }}

                                @if ($type == 'Finished Goods')
                                     <span class="status status-info">FG</span>
                                @else
                                    <span class="status status-warning">{{ $type }}</span>
                                @endif
                            </td>
                            <td>{{ number_format($row->qty) }}</td>
                            <td>{{ number_format($row->berat, 2) }} Kg</td>
                            {{-- @php
                                $trueBB = false;
                            @endphp
                            @foreach ($item_sama_bb as $prod)
                                @if ($row->item_id === $prod->item_id)
                                @php
                                    $trueBB = true;
                                @endphp
                                    @if ($trueBB == true)
                                        <td>{{ number_format($row->qty - $prod->jumlah) }}</td>
                                    @endif
                                @endif
                            @endforeach
                            @if ($trueBB == false) --}}




                            {{-- @endif --}}
                        </tr>
                        @php
                            
                            $ekor = $ekor + $row->qty;
                            $berat = $berat + $row->berat;
                        @endphp
                    @endforeach
                    <tr>
                        <td></td>
                        <td>Total</td>
                        <td>{{ $ekor - $tambahan_jumlah }}</td>
                        <td>{{ number_format($berat - $tambahan_kg, 2) }} Kg</td>
                    </tr>
                </tbody>
            </table>
            @php
                $total_fg = $berat;
                $total_fg_new = $berat - $tambahan_kg;
            @endphp
            {{-- {{$tambahan_kg}} --}}
        </div>
    </div>

    @if ($total_bb > 0)
        <div class="col-md-12">
            @php
                $selisih = $total_fg_new - $total_bb ;
                $presentase = ($selisih / $total_bb_new) * 100;
            @endphp

        <hr>
        <div class="row">
            <div class="col-2">
                <div class="px-2">
                    <label>Selisih</label><br>
                    @if ($presentase > 5 || $presentase < -5) <b class="red">{{ number_format($selisih, 2) }} Kg</b>
                        @else
                        <b class="blue">{{ number_format($selisih , 2) }} Kg</b>
                        @endif
                </div>
            </div>
            <div class="col-2">
                <div class="px-2">
                    <label>Presentase</label><br>
                    @if ($presentase > 5 || $presentase < -5) <b class="red">{{ number_format($presentase, 2) }} %</b>
                        @else
                        <b class="blue">{{ number_format($presentase, 2) }} %</b>
                        @endif
                </div>
            </div>
            <div class="col-8">
                <label>Keterangan</label><br>
                @if ($presentase > 5 || $presentase < -5) <div class="status status-warning">Presentasi susut masih
                    diatas atau dibawah benchmark 5%
            </div>
            @else
            <div class="status status-success">Presentasi susut sesuai dengan benchmark 5%</div>
            @endif
        </div>
    </div>
    <hr>
</div>
@endif

</div>

{{-- bukan netsuite --}}
{{-- @if ($regu == 'byproduct') --}}
<hr>

<h6 class="text-center">Produksi Tidak Kirim WO</h6><br>
<div class="row">
    @php
    $total_bb = 0;
    $total_fg = 0;
    @endphp
    <div class="col-md-6">
        <div class="table-responsive">
            <table class="table default-table" width="100%">
                <thead>
                    <tr>
                        <th class="text-info" colspan="4">Bahan Baku</th>
                    </tr>
                    <tr>
                        <th>No</th>
                        <th>Nama Item</th>
                        <th>Ekor/Pcs/Pack</th>
                        <th>Berat</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $ekor = 0;
                    $berat = 0;
                    @endphp
                    {{-- ada item yang sama masuk non wo --}}
                    {{-- @if (count($item_sama_bb) > 0)
                        @foreach ($item_sama_bb as $i => $row)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>
                                    {{ App\Models\Item::find($row->item_id)->nama }}
                                    @if ($row->type == 'hasil-produksi')
                                        <span class="status status-info">FG</span>
                                    @elseif($row->type == 'bahan-baku')
                                        <span class="status status-danger">BB</span>
                                    @endif
                                </td>
                                <td>{{ number_format($row->qty) }}</td>
                                <td>{{ number_format($row->berat, 2) }} Kg</td>
                            </tr>
                            @php
                                $ekor += $row->qty ;
                                $berat += $row['kg'];
                            @endphp
                        @endforeach
                    @endif --}}

                    @foreach($collectionNetsuiteNullBB as $key => $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            {{ App\Models\Item::find($row->item_id)->nama }}
                            @if ($row->type == 'hasil-produksi')
                                <span class="status status-info">FG</span>
                            @elseif($row->type == 'bahan-baku')
                                <span class="status status-danger">BB</span>
                                @else
                                <span class="status status-danger">{{ $row->type }}</span>
                            @endif
                        </td>
                        <td>{{ number_format($row->qty) }}</td>
                        <td>{{ number_format($row->berat, 2) }} Kg</td>
                    </tr>
                    @php
                        $ekor += $row->qty + $tambahan_jumlah;
                        $berat += $row->berat + $tambahan_kg;
                    @endphp
                    @endforeach
                    {{-- end if item yang sama --}}
                    {{-- @foreach ($netsuite_null_bb as $i => $row)
                        <tr>
                            <td>{{ ++$i }}</td>
                            <td>
                                {{ App\Models\Item::find($row->item_id)->nama }}
                                @if ($row->type == 'hasil-produksi')
                                    <span class="status status-info">FG</span>
                                @elseif($row->type == 'bahan-baku')
                                    <span class="status status-danger">BB</span>
                                @endif
                            </td>
                            @foreach ($item_sama_bb as $prod)
                                @if ($row->item_id === $prod->item_id)
                                    <td>{{ number_format($row->qty + $prod->jumlah) }}</td>
                                @endif
                            @endforeach
                                <td>{{ number_format($row->qty) }}</td>
                                <td>{{ number_format($row->berat, 2) }} Kg</td>
                        </tr>
                        @php
                            $ekor += $row->qty + $tambahan_jumlah;
                            $berat += $row['kg'] + $tambahan_kg;
                        @endphp
                    @endforeach --}}
                    <tr>
                        <td></td>
                        <td>Total</td>
                        <td>{{ $ekor }}</td>
                        <td>{{ number_format($berat, 2) }} Kg</td>
                    </tr>
                </tbody>
            </table>
            @php
            $total_bb = $berat;
            @endphp
        </div>
    </div>
    <div class="col-md-6">
        <div class="table-responsive">
            <table class="table default-table" width="100%">
                <thead>
                    <tr>
                        <th class="text-info" colspan="4">Hasil Produksi</th>
                    </tr>
                    <tr>
                        <th>No</th>
                        <th>Nama Item</th>
                        <th>Ekor/Pcs/Pack</th>
                        <th>Berat</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $ekor = 0;
                    $berat = 0;
                    @endphp
                    {{-- ada item yang sama masuk non wo --}}
                    {{-- @if (count($item_sama_bb) > 0)
                        @foreach ($item_sama_bb as $i => $row)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>
                                    {{ App\Models\Item::find($row->item_id)->nama }}
                                    @if ($row->type == 'hasil-produksi')
                                        <span class="status status-info">FG</span>
                                    @elseif($row->type == 'bahan-baku')
                                        <span class="status status-danger">BB</span>
                                    @endif
                                </td>
                                <td>{{ number_format($row->qty) }}</td>
                                <td>{{ number_format($row->berat, 2) }} Kg</td>
                            </tr>
                            @php
                                $ekor += $row->qty ;
                                $berat += $row['kg'];
                            @endphp
                        @endforeach
                    @endif --}}


                    @foreach($collectionNetsuiteNullFG as $key => $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            {{ App\Models\Item::find($row->item_id)->nama }}
                            @if ($row->type == 'hasil-produksi')
                                <span class="status status-info">FG</span>
                            @elseif($row->type == 'bahan-baku')
                                <span class="status status-danger">BB</span>
                            @endif
                        </td>
                        <td>{{ number_format($row->qty) }}</td>
                        <td>{{ number_format($row->berat, 2) }} Kg</td>
                    </tr>
                    @php
                    $ekor = $ekor + $row->qty + $qty_prod;
                    $berat = $berat + $row->berat + $berat_prod;
                    @endphp
                    @endforeach


                    {{-- end if item yang sama --}}
                    {{-- @foreach ($netsuite_null_pb as $i => $row)
                    @php
                    $bom_item = \App\Models\BomItem::where('sku', $row->sku)
                    ->where('bom_id', $bom->id)
                    ->first();

                    $item_cat = \App\Models\Item::find($row->item_id);

                    $type = ($item_cat->category_id == 4 or $item_cat->category_id == 6 or $item_cat->category_id == 10
                    or $item_cat->category_id == 16) ? 'By Product' : 'Finished Goods';
                    if ($bom_item) {
                    $type = $bom_item->kategori;
                    }
                    @endphp
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ App\Models\Item::find($row->item_id)->nama }}

                            @if ($type == 'Finished Goods')
                             <span class="status status-info">FG</span>
                            @else
                            <span class="status status-warning">{{ $type }}</span>
                            @endif
                        </td>
                        @if ($row->item_id === $id_item_prod)
                        <td>{{ number_format($row->qty + $qty_prod) }}</td>
                        <td>{{ number_format($row['kg'] + $berat_prod, 2) }} Kg</td>
                        @else
                        <td>{{ number_format($row->qty) }}</td>
                        <td>{{ number_format($row->berat, 2) }} Kg</td>
                        @endif
                    </tr>

                    @endforeach --}}
                    <tr>
                        <td></td>
                        <td>Total</td>
                        <td>{{ $ekor }}</td>
                        <td>{{ number_format($berat, 2) }} Kg</td>
                    </tr>
                </tbody>
            </table>
            @php
            $total_fg = $berat;
            @endphp

        </div>
    </div>

</div>

<hr>

<h6 class="text-center">Produksi Input By Order tidak kirim WO</h6><br>
<div class="row">
    @php
    $total_bb = 0;
    $total_fg = 0;
    @endphp
    {{-- <div class="col-md-6">
        <div class="table-responsive">
            <table class="table default-table" width="100%">
                <thead>
                    <tr>
                        <th class="text-info" colspan="4">Bahan Baku</th>
                    </tr>
                    <tr>
                        <th>No</th>
                        <th>Nama Item</th>
                        <th>Ekor/Pcs/Pack</th>
                        <th>Berat</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $ekor = 0;
                    $berat = 0;
                    @endphp
                    @foreach ($inputbyorder_bb as $i => $row)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>
                            {{ $row->nama }}
                            @if ($row->type == 'hasil-produksi')
                            <span class="status status-info">FG</span>
                            @elseif($row->type == 'bahan-baku')
                            <span class="status status-danger">BB</span>
                            @endif
                        </td>
                        <td>{{ number_format($row->jumlah) }}</td>
                        <td>{{ number_format($row->kg, 2) }} Kg</td>
                    </tr>
                    @php
                    $ekor += $row->jumlah;
                    $berat += $row->kg;
                    @endphp
                    @endforeach
                    <tr>
                        <td></td>
                        <td>Total</td>
                        <td>{{ $ekor }}</td>
                        <td>{{ number_format($berat, 2) }} Kg</td>
                    </tr>
                </tbody>
            </table>
            @php
            $total_bb = $berat;
            @endphp
        </div>
    </div> --}}
    <div class="col">
        <div class="table-responsive">
            <table class="table default-table" width="100%">
                <thead>
                    <tr>
                        <th class="text-info" colspan="4">Hasil Produksi</th>
                    </tr>
                    <tr>
                        <th>No</th>
                        <th>Nama Item</th>
                        <th>Ekor/Pcs/Pack</th>
                        <th>Berat</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $ekor = 0;
                    $berat = 0;
                    @endphp
                    @foreach ($inputbyorder_pb as $i => $row)
                    @php
                    $bom_item = \App\Models\BomItem::where('sku', $row->sku)
                    ->where('bom_id', $bom->id)
                    ->first();

                    $item_cat = \App\Models\Item::find($row->item_id);

                    $type = ($item_cat->category_id == 4 or $item_cat->category_id == 6 or $item_cat->category_id == 10
                    or $item_cat->category_id == 16) ? 'By Product' : 'Finished Goods';
                    if ($bom_item) {
                    $type = $bom_item->kategori;
                    }
                    @endphp
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $row->nama }}

                            @if ($type == 'Finished Goods')
                            <span class="status status-info">FG</span>
                            @else
                            <span class="status status-warning">{{ $type }}</span>
                            @endif
                        </td>
                        <td>{{ number_format($row->jumlah) }}</td>
                        <td>{{ number_format($row->kg, 2) }} Kg</td>
                    </tr>
                    @php
                    $ekor = $ekor + $row->jumlah;
                    $berat = $berat + $row->kg;
                    @endphp
                    @endforeach
                    <tr>
                        <td></td>
                        <td>Total</td>
                        <td>{{ $ekor }}</td>
                        <td>{{ number_format($berat, 2) }} Kg</td>
                    </tr>
                </tbody>
            </table>
            @php
            $total_fg = $berat;
            @endphp

        </div>
    </div>

</div>
{{-- @endif --}}
<div class="float-right pb-2">
    <a href="{{ route('produksi.summary', ['key' => 'unduh_all', 'regu' => $regu, 'tanggal' => $tanggal]) }}">Unduh
        Data</a>
</div>


@php
$netsuite = \App\Models\Netsuite::where('label', 'like', '%' . $regu . '%')
->where('trans_date', $tanggal)
->get();
@endphp

@if (count($netsuite) == 0)
@if (User::setIjin(37))
<hr>
<form action="{{ route('wo.create') }}" method="GET">
    <div class="form-group">
        <input type="hidden" name="tanggal" class="form-control tanggal" id="tanggal-form" value="{{ $tanggal }}"
            autocomplete="off">
        <input type="hidden" name="regu" class="form-control" id="regu-form" value="{{ $regu }}" autocomplete="off">
        <button type="submit" class="btn btn-blue btn-block">Buat WO</button>
    </div>
</form>
@endif
@endif





<script>
    $('#tanggalsummary').on('change', function() {
        var tanggal = $(this).val();
        $("#list_summary").load("{{ route('produksi.summary', ['regu' => $regu]) }}&tanggal=" + tanggal);
    })
</script>