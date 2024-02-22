@extends('admin.layout.template')

@section('title', 'Produksi Kepala Regu')

@section('content')

    <div class="row mb-4">
        <div class="col"></div>
        <div class="col text-center py-2">
            <b>HISTORY DELETE PRODUKSI HARIAN</b>
        </div>
        <div class="col"></div>
    </div>

    <section class="panel">
        <div class="card-body">

            @if (count($datalist) > 0)
                <div class="section">
                    @foreach ($datalist as $key => $row)
                        <div class="card mb-4">
                            @php
                                $json_data[] = json_decode($row->data, true);
                                $item = 0;
                                $berat = 0;
                            @endphp
                            <div class="mt-4 ml-2">
                                <a href="#">Produksi
                                    {{ date('d/m/Y', strtotime($json_data[$key]['freestock']['tanggal'])) }}</a><br>
                                User Input : {{ App\Models\User::find($json_data[$key]['freestock']['user_id'])->name }}
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6 pr-sm-1">
                                        <table class="table default-table table-small">
                                            <thead>
                                                <tr>
                                                    <th>Bahan Baku</th>
                                                    <th>Tanggal Produksi</th>
                                                    <th>Asal</th>
                                                    <th>Ekor/Pcs/Pack</th>
                                                    <th>Berat</th>
                                                    <th>Batalakan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @for ($bb = 0; $bb < count($json_data[$key]['freestock']['listfreestock']); $bb++)
                                                    @php
                                                        $list_item = App\Models\Item::select('nama')
                                                            ->where('id', $json_data[$key]['freestock']['listfreestock'][$bb]['item_id'])
                                                            ->first();
                                                        $user = App\Models\User::where('id', $row->user_id)->first()->name;
                                                        $item += $json_data[$key]['freestock']['listfreestock'][$bb]['qty'];
                                                        $berat += $json_data[$key]['freestock']['listfreestock'][$bb]['berat'];
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $list_item->nama }}</td>
                                                        <td>{{ $json_data[$key]['chiller']['tanggal_produksi'] }}</td>
                                                        <td>{{ $json_data[$key]['chiller']['tujuan'] }}</td>
                                                        <td>{{ number_format($json_data[$key]['freestock']['listfreestock'][$bb]['qty']) }}
                                                        </td>
                                                        <td>{{ number_format($json_data[$key]['freestock']['listfreestock'][$bb]['berat']) }}
                                                        </td>
                                                        <td>{{ $user }} // {{ $row->created_at }}</td>
                                                    </tr>
                                                @endfor
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="3" class="text-center">Total</th>
                                                    <th> {{ number_format($item) }}</th>
                                                    <th class="text-right">{{ number_format($berat, 2) }} Kg</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="col-lg-6 pr-sm-1">
                                        <table class="table default-table table-small">
                                            <thead>
                                                <th>Hasil Produksi</th>
                                                <th>Ekor/Pcs/Pack</th>
                                                <th>Berat</th>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $qty = 0;
                                                    $berat = 0;
                                                @endphp
                                                @for ($i = 0; $i < count($json_data[$key]['freestock']['freetemp']); $i++)
                                                    @php
                                                        $qty += $json_data[$key]['freestock']['freetemp'][$i]['qty'];
                                                        $berat += $json_data[$key]['freestock']['freetemp'][$i]['berat'];
                                                        // $exp = json_decode($item->label);
                                                    @endphp
                                                    <tr class="filter-name-harian">
                                                        <td>
                                                            @if ($json_data[$key]['freestock']['freetemp'][$i]['prod_nama'] == 1)
                                                                <span class="status status-danger">[ABF]</span>
                                                            @elseif($json_data[$key]['freestock']['freetemp'][$i]['prod_nama'] == 2)
                                                                <span class="status status-warning">[EKSPEDISI]</span>
                                                            @elseif($json_data[$key]['freestock']['freetemp'][$i]['prod_nama'] == 3)
                                                                <span class="status status-warning">[TITIP CS]</span>
                                                            @else
                                                                <span class="status status-info">[CHILLER]</span>
                                                            @endif
                                                            {{ $json_data[$key]['freestock']['freetemp'][$i]['prod_nama'] }}
                                                        </td>
                                                        <td>{{ number_format($json_data[$key]['freestock']['freetemp'][$i]['qty']) }}
                                                        </td>
                                                        <td class="text-right">
                                                            {{ number_format($json_data[$key]['freestock']['freetemp'][$i]['berat'], 2) }}Kg
                                                        </td>
                                                    </tr>
                                                    <tr class="filter-name-harian">
                                                        <td colspan="5">
                                                            <div class="row">
                                                                <div class="col pr-1">
                                                                    @if ($json_data[$key]['freestock']['freetemp'][$i]['kode_produksi'])
                                                                        Kode Produksi :
                                                                        {{ $json_data[$key]['freestock']['freetemp'][$i]['kode_produksi'] }}
                                                                    @endif
                                                                </div>
                                                                <div class="col pl-1 text-right">
                                                                    @if ($json_data[$key]['freestock']['freetemp'][$i]['unit'])
                                                                        Unit :
                                                                        {{ $json_data[$key]['freestock']['freetemp'][$i]['unit'] }}
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            @if ($json_data[$key]['freestock']['freetemp'][$i]['keranjang'])
                                                                <div>
                                                                    {{ $json_data[$key]['freestock']['freetemp'][$i]['keranjang'] }}Keranjang
                                                                </div>
                                                            @endif
                                                            <div class="status status-success">
                                                                <div class="row">
                                                                    <div class="col pr-1">
                                                                        {{ $json_data[$key]['freestock']['freetemp'][$i]['plastik_nama'] }}
                                                                    </div>
                                                                    <div class="col-auto pl-1">
                                                                        <span class="float-right">//
                                                                            {{ $json_data[$key]['freestock']['freetemp'][$i]['plastik_qty'] }}Pcs</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @php
                                                                $customer = App\Models\Customer::where('id', $json_data[$key]['freestock']['freetemp'][$i]['customer_id'])->first();
                                                            @endphp
                                                            <div class="row mt-1 text-info">
                                                                <div class="col pr-1">
                                                                    @if ($json_data[$key]['freestock']['freetemp'][$i]['customer_id'])
                                                                        <div>Customer : {{ $customer->nama ?? '-' }}</div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endfor
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Total</th>
                                                    <th> {{ $qty }} Ekor</th>
                                                    <th class="text-right">{{ $berat }} Kg</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                @foreach ($frestok as $no => $row)
                    <div class="card mb-2">
                        <div class="p-2">
                            <a href="#">Produksi
                                {{ date('d/m/Y', strtotime($row->tanggal)) }}</a><br>
                            User Input : {{ App\Models\User::find($row->user_id)->name }}
                        </div>
                        <div class="card-body p-2">
                            <div class="row">
                                @php
                                    $total_bb = 0;
                                    $total_fg = 0;
                                @endphp
                                <div class="col-sm-6 pr-sm-1">
                                    <table class="table default-table table-small">
                                        <thead>
                                            <th>Bahan Baku</th>
                                            <th>Tanggal</th>
                                            <th>Asal</th>
                                            <th>Ekor/Pcs/Pack</th>
                                            <th>Berat</th>
                                        </thead>
                                        <tbody>
                                            @php
                                                $item = 0;
                                                $berat = 0;
                                            @endphp
                                            @foreach ($row->listfreestock as $no => $rfs)
                                                @php
                                                    $item += $rfs->qty;
                                                    $berat += $rfs->berat;
                                                    if ($rfs->chiller->label ?? false) {
                                                        $exp = json_decode($rfs->chiller->label);
                                                    } else {
                                                        $exp = [];
                                                    }
                                                @endphp
                                                <tr class="filter-name-harian">
                                                    <td>{{ ++$no }}. {{$row->id}}
                                                        {{ $rfs->chiller->item_name ?? '<span class="status status-danger">ITEM TELAH DIHAPUS</span>' }}
                                                        @if ($rfs->chiller->label ?? false)
                                                            @if ($rfs->chiller->label != '' && $rfs->chiller->type == 'bahan-baku')
                                                                <br><span
                                                                    class="status status-info">{{ $rfs->chiller->label ?? '' }}</span>
                                                            @endif
                                                        @endif
                                                        @if ($rfs->catatan != '')
                                                            <br>Catatan : {{ $rfs->catatan }}
                                                        @endif
    
                                                    </td>
                                                    <td>{{ $rfs->chiller->tanggal_produksi ?? '' }}
                                                        <br>{{ $rfs->bb_kondisi }}
    
                                                    </td>
                                                    <td>{{ $rfs->chiller->tujuan ?? '' }}</td>
                                                    <td>{{ number_format($rfs->qty) }}</td>
                                                    <td class="text-right">{{ number_format($rfs->berat, 2) }} Kg</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="5">
                                                        <div class="row">
                                                            <div class="col pr-1">
                                                                @if ($rfs->kode_produksi)
                                                                    Kode Produksi : {{ $rfs->kode_produksi }}
                                                                @endif
                                                            </div>
                                                            <div class="col pl-1 text-right">
                                                                @if ($rfs->unit)
                                                                    Unit : {{ $rfs->unit }}
                                                                @endif
                                                            </div>
                                                        </div>
                                                        @if ($rfs->keranjang)
                                                            <div>{{ $rfs->keranjang }} Keranjang</div>
                                                        @endif
                                                        @if ($exp->plastik->jenis ?? false)
                                                            <div class="status status-success">
                                                                <div class="row">
                                                                    <div class="col pr-1">
                                                                        {{ $exp->plastik->jenis }}
                                                                    </div>
                                                                    <div class="col-auto pl-1">
                                                                        @if ($exp->plastik->qty > 0)
                                                                            <span class="float-right">//
                                                                                {{ $exp->plastik->qty }} Pcs</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
    
                                                        @if ($exp->additional ?? false)
                                                            {{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }}
                                                            {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }}
                                                            {{ $exp->additional->maras ? 'Tanpa Maras' : '' }}
                                                        @endif
                                                        <div class="row mt-1 text-info">
                                                            <div class="col pr-1">
                                                                @if ($rfs->customer_id)
                                                                    <div>Customer : {{ $rfs->konsumen->nama ?? '-' }}</div>
                                                                @endif
                                                                @if ($exp->sub_item ?? false)
                                                                    <div>Keterangan : {{ $exp->sub_item }}</div>
                                                                @endif
                                                            </div>
                                                            <div class="col-auto pl-1 text-right">
                                                                @if ($rfs->selonjor ?? false)
                                                                    <div class="text-danger font-weight-bold">SELONJOR</div>
                                                                @endif
                                                                @if ($exp->parting->qty ?? false)
                                                                    Parting : {{ $exp->parting->qty }}
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3" class="text-center">Total</th>
                                                <th> {{ number_format($item) }}</th>
                                                <th class="text-right">{{ number_format($berat, 2) }} Kg</th>
                                        </tfoot>
                                    </table>
    
                                    @php
                                        $total_bb = $berat;
                                    @endphp
    
    
                                </div>
                                <div class="col-sm-6 pl-sm-1">
                                    <table class="table default-table table-small">
                                        <thead>
                                            <th>Hasil Produksi</th>
                                            <th>Ekor/Pcs/Pack</th>
                                            <th>Berat</th>
                                        </thead>
                                        <tbody>
                                            @php
                                                $qty = 0;
                                                $berat = 0;
                                            @endphp
                                            @foreach ($row->freetemp as $no => $item)
                                                @php
                                                    $qty += $item->qty;
                                                    $berat += $item->berat;
                                                    $exp = json_decode($item->label);
                                                @endphp
                                                <tr class="filter-name-harian">
                                                    <td>{{ ++$no }}.
                                                        @if ($item->kategori == '1')
                                                            <span class="status status-danger">[ABF]</span>
                                                        @elseif($item->kategori == '2')
                                                            <span class="status status-warning">[EKSPEDISI]</span>
                                                        @elseif($item->kategori == '3')
                                                            <span class="status status-warning">[TITIP CS]</span>
                                                        @else
                                                            <span class="status status-info">[CHILLER]</span>
                                                        @endif
                                                        {{ $item->item->nama ?? '<span class="status status-danger">ITEM TELAH DIHAPUS</span>' }}
                                                    </td>
                                                    <td>{{ number_format($item->qty) }}</td>
                                                    <td class="text-right">{{ number_format($item->berat, 2) }} Kg</td>
                                                </tr>
                                                <tr class="filter-name-harian">
                                                    <td colspan="5">
                                                        <div class="row">
                                                            <div class="col pr-1">
                                                                @if ($item->kode_produksi)
                                                                    Kode Produksi : {{ $item->kode_produksi }}
                                                                @endif
                                                            </div>
                                                            <div class="col pl-1 text-right">
                                                                @if ($item->unit)
                                                                    Unit : {{ $item->unit }}
                                                                @endif
                                                            </div>
                                                        </div>
                                                        @if ($item->keranjang)
                                                            <div>{{ $item->keranjang }} Keranjang</div>
                                                        @endif
                                                        @if ($exp->plastik->jenis)
                                                            <div class="status status-success">
                                                                <div class="row">
                                                                    <div class="col pr-1">
                                                                        {{ $exp->plastik->jenis }}
                                                                    </div>
                                                                    <div class="col-auto pl-1">
                                                                        @if ($exp->plastik->qty > 0)
                                                                            <span class="float-right">//
                                                                                {{ $exp->plastik->qty }} Pcs</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
    
                                                        @if ($exp->additional)
                                                            {{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }}
                                                            {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }}
                                                            {{ $exp->additional->maras ? 'Tanpa Maras' : '' }}
                                                        @endif
                                                        <div class="row mt-1 text-info">
                                                            <div class="col pr-1">
                                                                @if ($item->customer_id)
                                                                    <div>Customer : {{ $item->konsumen->nama ?? '-' }}
                                                                    </div>
                                                                @endif
                                                                @if ($exp->sub_item)
                                                                    <div>Keterangan : {{ $exp->sub_item }}</div>
                                                                @endif
                                                            </div>
                                                            <div class="col-auto pl-1 text-right">
                                                                @if ($item->selonjor)
                                                                    <div class="text-danger font-weight-bold">SELONJOR
                                                                    </div>
                                                                @endif
                                                                @if ($exp->parting->qty)
                                                                    Parting : {{ $exp->parting->qty }}
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Total</th>
                                                <th> {{ $qty }} Ekor</th>
                                                <th class="text-right">{{ $berat }} Kg</th>
                                        </tfoot>
                                    </table>
                                </div>
    
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif


        </div>
    </section>
@endsection
