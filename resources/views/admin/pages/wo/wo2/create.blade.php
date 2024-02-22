@extends('admin.layout.template')

@section('title', 'Penyiapan')

@section('content')
<div class="row mb-4">
    <div class="col py-1">
        <a href="{{ route('sync.index') }}"><i class="fa fa-arrow-left"></i> Back</a>
    </div>
    <div class="col-8 py-1 text-center">
        <b class="text-uppercase">TRACING WO 2</b>
    </div>
    <div class="col"></div>
</div>


<style>
    .hidden-form {
        display: none;
    }
</style>

<section class="panel">

    <div class="card-body">
        <form method="get" action="{{ url('admin/wo/wo-2-list') }}">
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif name="tanggal" value="{{ Request::get('tanggal') ?? date('Y-m-d') }}" class="form-control mb-2">
            <button type="submit" class="btn btn-blue">Filter</button>
            <a href="{{ route('wo.wo_2_list', ['key' => 'unduh_wo2']) }}&tanggal={{ $tanggal }}"
                class="btn btn-outline-warning"><i class="fa fa-download"></i>Unduh</a>
        </form>
        <hr>

        @foreach ($produksi as $p)
        REGU : {{ $p['regu'] }} -----------
        <div class="row">
            @php
            $total_bb = 0;
            $total_fg = 0;
            $bahan_baku = $p['bb'];
            $fg = $p['fg'];
            $regu = $p['regu'];
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
                            @foreach ($bahan_baku as $i => $row)
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
                            $ekor_fg = 0;
                            $berat_fg = 0;
                            $ekor_bp = 0;
                            $berat_bp = 0;
                            @endphp
                            @foreach ($fg as $i => $row)
                            @php

                            if ($regu == 'boneless') {
                            $bom = \App\Models\Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL') . ' - KARKAS - BONELESS BROILER')->first();
                            $id_assembly = $bom->netsuite_internal_id;
                            } elseif ($regu == 'parting') {
                            $bom = \App\Models\Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL') . ' - AYAM PARTING BROILER')->first();
                            $id_assembly = $bom->netsuite_internal_id;
                            } elseif ($regu == 'marinasi') {
                            $bom = \App\Models\Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL') . ' - AYAM PARTING MARINASI BROILER')->first();
                            $id_assembly = $bom->netsuite_internal_id;
                            } elseif ($regu == 'whole') {
                            $bom = \App\Models\Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL') . ' - AYAM KARKAS BROILER')->first();
                            $id_assembly = $bom->netsuite_internal_id;
                            } elseif ($regu == 'frozen') {
                            $bom = \App\Models\Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL') . ' - AYAM KARKAS FROZEN')->first();
                            $id_assembly = $bom->netsuite_internal_id;
                            } else {
                            $bom = \App\Models\Bom::where('bom_name', env('NET_SUBSIDIARY', 'CGL') . ' - KARKAS - BONELESS BROILER')->first();
                            $id_assembly = $bom->netsuite_internal_id;
                            }
                            $bom_item = \App\Models\BomItem::where('sku', $row->sku)
                            ->where('bom_id', $bom->id)
                            ->first();

                            $item_cat = \App\Models\Item::find($row->item_id);

                            $type = ($item_cat->category_id == 4 or $item_cat->category_id == 6 or
                            $item_cat->category_id == 10 or $item_cat->category_id == 16) ? 'By Product' : 'Finished Goods';
                            if ($bom_item) {
                            $type = $bom_item->kategori;
                            }
                            @endphp
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $row->nama }}

                                    @if ($type == 'Finished Goods')
                                    <span class="status status-success">{{ $type }}</span>
                                    @else
                                    <span class="status status-warning">{{ $type }}</span>
                                    @endif

                                    @php
                                    if ($type == 'Finished Goods') {
                                    $ekor_fg = $ekor_fg + $row->jumlah;
                                    $berat_fg = $berat_fg + $row->kg;
                                    } else {
                                    $ekor_bp = $ekor_bp + $row->jumlah;
                                    $berat_bp = $berat_bp + $row->kg;
                                    }
                                    @endphp
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
                                <td>Total Finished Good</td>
                                <td>{{ $ekor_fg }}</td>
                                <td>{{ number_format($berat_fg, 2) }} Kg</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>Total By Product</td>
                                <td>{{ $ekor_bp }}</td>
                                <td>{{ number_format($berat_bp, 2) }} Kg</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>Total Global</td>
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
        @if ($total_bb > 0)
        @php
        $selisih = ($total_bb - $total_fg) * -1;
        $presentase = (($total_bb - $total_fg) / $total_bb) * 100 * -1;
        @endphp

        <div class="row">
            <div class="col-2">
                <div class="px-2">
                    <label>Selisih</label><br>
                    @if ($presentase > 5 || $presentase < -5) <b class="red">{{ number_format($selisih, 2) }} Kg</b>
                        @else
                        <b class="blue">{{ number_format($selisih, 2) }} Kg</b>
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
                @if ($presentase > 5 || $presentase < -5) <div class="status status-warning">Presentasi susut masih diatas atau dibawah benchmark 5%
            </div>
            @else
            <div class="status status-success">Presentasi susut sesuai dengan benchmark 5%</div>
            @endif
        </div>
    </div>
    @endif

    @php
    $netsuite = \App\Models\Netsuite::where('label', 'like', '%' . $regu . '%')
    ->where('trans_date', $tanggal)
    ->get();
    @endphp

    @if (count($netsuite) > 0)
    <hr>
    <h6>Netsuite Terbentuk</h6>

    <table class="table default-table">
        <thead>
            <tr>
                <th>
                    <input type="checkbox" id="ns-checkall">
                </th>
                <th>ID</th>
                <th>C&U Date</th>
                <th>TransDate</th>
                <th>Label</th>
                <th>Activity</th>
                <th>Location</th>
                <th>IntID</th>
                <th>Paket</th>
                <th width="100px">Data</th>
                <th width="100px">Action</th>
                <th>Response</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>

            @foreach ($netsuite as $no => $field_value)
            @include('admin.pages.log.netsuite_one', $netsuite = $field_value)
            @endforeach

        </tbody>
    </table>
    @endif
    <hr>
    @endforeach
    </div>
</section>

@stop

@section('footer')

@endsection