@if($download == true)
@php
header('Content-Transfer-Encoding: none');
header("Content-type: application/vnd-ms-excel");
header("Content-type: application/x-msexcel");
header("Content-Disposition: attachment; filename=Export-SOH.xls");
@endphp
@endif
@php
$TotalQtySaldoAwal = 0;
$TotalBeratSaldoAwal = 0;
$TotalKarungSaldoAwal = 0;

$TotalQtyHasilProduksi = 0;
$TotalBeratHasilProduksi = 0;
$TotalKarungHasilProduksi = 0;

$TotalQtySaldoRetur = 0;
$TotalBeratSaldoRetur = 0;
$TotalKarungSaldoRetur = 0;

$TotalQtySaldoAkhir = 0;
$TotalBeratSaldoAkhir = 0;
$TotalKarungSaldoAkhir = 0;

$subTotalQtyProd = 0;
$subTotalBeratProd = 0;
$subTotalKarungProd = 0;
$subTotalQtyReprod = 0;
$subTotalBeratReprod = 0;
$subTotalKarungReprod = 0;
@endphp

<div id="container">
    <div class="table-responsive table-height">
        <table class="table table-bordered table-striped table-hover table-sticky" border="1">
            <thead>
                <tr>
                    <th class="text-center" colspan="13" rowspan="2">SOH TANGGAL</th>
                    <th class="text-center" colspan="3" rowspan="3">Saldo Awal</th>
                    <th class="text-center" colspan="12">Inbound</th>
                    <th class="text-center" colspan="18">Outbound</th>
                    <th class="text-center" colspan="3" rowspan="3">Stock Akhir</th>
                    <th class="text-center" colspan="3" rowspan="3">MTD IN</th>
                    <th class="text-center" colspan="3" rowspan="3">MTD OUT</th>
                </tr>
                <tr>
                    <th class="text-center" colspan="3" rowspan="2">Production</th>
                    <th class="text-center" colspan="3" rowspan="2">Tolak/Return</th>
                    <th class="text-center" colspan="3" rowspan="2">Other</th>
                    <th class="text-center" colspan="3" rowspan="2">Total Inbound</th>
                    <th class="text-center" colspan="3" rowspan="2">Packing Slip</th>
                    <th class="text-center" colspan="3" rowspan="2">Sample</th>
                    <th class="text-center" colspan="3" rowspan="2">Reproses (Thawing)</th>
                    <th class="text-center" colspan="3" rowspan="2">Free Good</th>
                    <th class="text-center" colspan="3" rowspan="2">Other</th>
                    <th class="text-center" colspan="3" rowspan="2">Total Outbound</th>
                </tr>
                <tr>
                    <td rowspan="3" class="stuck">No</td>
                    <td rowspan="3" class="stuck">Item Number</td>
                    <td rowspan="3" class="stuck">Item NS</td>
                    <th rowspan="3">Category</th>
                    <th rowspan="3">Marinated</th>
                    <th rowspan="3">Item Name</th>
                    <th rowspan="3">Isi</th>
                    <th rowspan="3">Brand</th>
                    <th rowspan="3">Customer</th>
                    <th rowspan="3">Status</th>
                    <th rowspan="3">Sales</th>
                    <th rowspan="3">Pack</th>
                    <th rowspan="3">Loc</th>

                </tr>
                <tr>
                    @for ($i = 0; $i < 14; $i++) <th rowspan="2">E/P</th>
                        <th rowspan="2">KG</th>
                        <th rowspan="2">KRG</th>
                        @endfor
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $no => $row)
                @php $item_qty_akhir = 0 @endphp
                @php $item_berat_akhir = 0 @endphp
                @php $item_krg_akhir = 0 @endphp

                {{-- @if ($row->berat_saldo_awal != 0 || $row->berat_saldo_awal != $row->berat_saldo_akhir) --}}

                @php
                $TotalQtySaldoAwal += $row->qty_saldo_awal;
                $TotalBeratSaldoAwal += $row->berat_saldo_awal;
                $TotalKarungSaldoAwal += $row->karung_saldo_awal;

                $TotalQtyHasilProduksi += $row->hp_qty;
                $TotalBeratHasilProduksi += $row->hp_berat;
                $TotalKarungHasilProduksi += $row->hp_karung;

                $TotalQtySaldoAkhir += $row->qty_saldo_akhir;
                $TotalBeratSaldoAkhir += $row->berat_saldo_akhir;
                $TotalKarungSaldoAkhir += $row->karung_saldo_akhir;

                $subTotalQtyProd += $row->inb_prod_qty;
                $subTotalBeratProd += $row->inb_prod_bb;
                $subTotalKarungProd += $row->inb_prod_krj;
                $subTotalQtyReprod += $row->out_prod_qty;
                $subTotalBeratReprod += $row->out_prod_bb;
                $subTotalKarungReprod += $row->out_prod_krg;
                @endphp

                @php $qty_prod = $row->inb_prod_qty ?? "0" @endphp
                @php $berat_prod = $row->inb_prod_bb ?? "0" @endphp
                @php $krg_prod = $row->inb_prod_krj ?? "0" @endphp

                @php $qty_reprod = $row->out_prod_qty ?? "0" @endphp
                @php $berat_reprod = $row->out_prod_bb ?? "0" @endphp
                @php $krg_reprod = $row->out_prod_krg ?? "0" @endphp

                @php $item_qty_akhir = $row->qty_saldo_akhir @endphp
                @php $item_berat_akhir = $row->berat_saldo_akhir @endphp
                @php $item_krg_akhir = $row->karung_saldo_akhir @endphp

                @php
                $category = App\Models\Item::find($row->product_id)->category_id ?? '#';
                @endphp
                <td class="stuck">{{ $no + 1 }} </td>
                <td class="stuck">
                    <div style="width:100px">{{ App\Models\Item::find($row->product_id)->sku ?? '#' }}</div>
                    {{-- <a href="javascript:void(0)" class="edit-ia" data-toggle="modal" data-target="#edit" data-name="{{ $row->nama }}" data-sub_item="{{ $row->sub_item }}" data-parting="{{ $row->parting }}" data-plastik_group="{{ $row->plastik_group }}" data-customer="{{ $row->nama_konsumen }}" data-sub_pack="{{ $row->subpack }}" data-qty="{{ $item_qty_akhir }}" data-berat="{{ $item_berat_akhir }}"><span class="fa fa-edit"></span></a> --}}
                    <a href="{{ route('warehouse.soh_detail') }}?item={{ $row->nama }}&sub_item={{ $row->sub_item }}&parting={{ $row->parting }}&plastik_group={{ $row->plastik_group }}&customer={{ $row->customer_id }}&sub_pack={{ $row->subpack }}&tanggal={{ $tanggal }}" target="_blank"><span class="fa fa-share"></span></a>
                </td>
                <td class="stuck">
                    <div style="width:280px">{{ $row->nama }}</div>
                </td>
                <td>
                    <div style="width:100px">
                        {{ App\Models\Category::find(App\Models\Item::find($row->product_id)->category_id ?? '#')->nama ?? '#' }}
                    </div>
                </td>
                <td>
                    <div style="width:100px">@if (str_contains($row->nama, '(M)')) MARINATED @else NON @endif</div>
                </td>
                <td>
                    <div style="width:120px">{{ $row->sub_item }}</div>
                </td>
                <td>
                    <div style="width:100px">{{ $row->karung_isi }}</div>
                </td> {{-- ISI --}}
                <td>
                    <div style="width:50px">{{ $row->grade_item ? 'Grade B' : 'Grade A' }}</div>
                </td> {{-- GRADE --}}
                <td>
                    <div style="width:100px">{{ App\Models\Customer::find($row->customer_id)->nama ?? '#' }}</div>
                </td>
                <td>
                    <div>{{ $row->stock_type }}</div>
                </td> {{-- STATUS --}}
                <td>
                    <div>{{ App\Models\Marketing::where('id', $row->marketing_id)->first()->nama ?? '-' }} </div>
                </td> {{-- SALES --}}
                <td>
                    <div>{{ $row->plastik_group }} </div>
                </td> {{-- PACK / PLASTIK GROUP --}}
                <td>
                    <div style="width:100px">{{ substr(App\Models\Gudang::namaGudangWithID($row->gudang_id), 6) }} </div>
                </td> {{-- LOKASI GUDANG --}}


                <td class="text-right">{{ number_format($row->qty_saldo_awal, 2) }}</td> {{-- QTY SALDO AWAL --}}
                <td class="text-right">{{ number_format($row->berat_saldo_awal, 2) }}</td>{{-- BERAT SALDO AWAL --}}
                <td class="text-right">{{ $row->karung_saldo_awal }}</td>{{-- KARUNG SALDO AWAL --}}


                {{-- PRODUCTION --}}
                <td class="text-right">{{ number_format($row->hp_qty, 2) }}</td>
                <td class="text-right">{{ number_format($row->hp_bb, 2) }}</td>
                <td class="text-right">{{ $row->hp_krg }}</td>
                {{-- END PRODUCTION --}}

                {{-- RETURN --}}
                <td class="text-right">{{ number_format($row->retur_qty, 2) }}</td>
                <td class="text-right">{{ number_format($row->retur_bb, 2) }}</td>
                <td class="text-right">{{ $row->retur_krg }}</td>
                {{-- END RETURN --}}


                {{-- OTHERS --}}
                <td class="text-right">{{ number_format($row->other_qty, 2) }}</td>
                <td class="text-right">{{ number_format($row->other_bb, 2) }}</td>
                <td class="text-right">{{ $row->other_kg }}</td>
                {{-- END OTHERS --}}

                {{-- TOTAL INBOUND --}}
                <td class="text-right">{{ number_format($row->inbound_qty, 2) }}</td>
                <td class="text-right">{{ number_format($row->inbound_bb, 2) }}</td>
                <td class="text-right">{{ $row->inbound_krg }}</td>
                {{-- END TOTAL INBOUND --}}



                {{-- EXPEDISI / PACKING SLIP --}}
                <td class="text-right">{{ number_format($row->siapkirim_qty, 2) }}</td>
                <td class="text-right">{{ number_format($row->siapkirim_bb, 2) }}</td>
                <td class="text-right">{{ $row->siapkirim_krg }}</td>
                {{-- END EXPEDISI --}}


                {{-- SAMPLE --}}
                <td class="text-right">{{ number_format($row->sample_qty, 2) }}</td>
                <td class="text-right">{{ number_format($row->sample_bb, 2) }}</td>
                <td class="text-right">{{ $row->sample_krg }}</td>
                {{-- END SAMPLE --}}


                {{-- THAWING --}}
                <td class="text-right">{{ number_format($row->thawing_qty, 2) }}</td>
                <td class="text-right">{{ number_format($row->thawing_bb, 2) }}</td>
                <td class="text-right">{{ $row->thawing_krg }}</td>
                {{-- END THAWING --}}

                {{-- FREE GOOD --}}
                <td class="text-right">{{ number_format($row->freegood_qty, 2) }}</td>
                <td class="text-right">{{ number_format($row->freegood_bb, 2) }}</td>
                <td class="text-right">{{ $row->freegood_krg }}</td>
                {{-- END FREE GOOD --}}

                {{-- OTHER --}}
                <td class="text-right">{{ number_format($row->otheroutbound_qty, 2) }}</td>
                <td class="text-right">{{ number_format($row->otheroutbound_bb, 2) }}</td>
                <td class="text-right">{{ $row->otheroutbound_krg }}</td>
                {{-- END OTHER --}}


                {{-- TOTAL OUTBOUND --}}
                <td class="text-right">{{ number_format($row->outbound_qty, 2) }}</td>
                <td class="text-right">{{ number_format($row->outbound_bb, 2) }}</td>
                <td class="text-right">{{ $row->outbound_krg }}</td>
                {{-- END TOTAL OUTBOUND --}}

                <td class="text-right">{{ number_format($item_qty_akhir, 2) }}</td> {{-- QTY SALDO AKHIR --}}
                <td class="text-right">{{ number_format($item_berat_akhir, 2) }}</td> {{-- BERAT SALDO AKHIR --}}
                <td>{{ $item_krg_akhir }}</td> {{-- KARUNG SALDO AKHIR --}}



                <td @if ($qty_prod> 0) style="background-color: pink" @endif>
                    {{ number_format($qty_prod, 2) }}</td>
                <td @if ($berat_prod> 0) style="background-color: pink" @endif>
                    {{ number_format($berat_prod,2) }}</td>
                <td @if ($krg_prod> 0) style="background-color: pink" @endif>
                    {{ number_format($krg_prod,2) }}</td>

                <td @if ($qty_reprod> 0) style="background-color: pink" @endif>
                    {{ number_format($qty_reprod,2) }}</td>
                <td @if ($berat_reprod> 0) style="background-color: pink" @endif>
                    {{ number_format($berat_reprod,2) }}</td>
                <td @if ($krg_reprod> 0) style="background-color: pink" @endif>
                    {{ number_format($krg_reprod,2) }}</td>


                </tr>

                @endforeach
            </tbody>

        </table>
    </div>
</div>