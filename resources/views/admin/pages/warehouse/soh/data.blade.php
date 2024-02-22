@php
                    // total saldo awal
                    $total_saep = 0;
                    $total_sakg = 0;
                    $total_sakrg = 0;

                    // total production
                    $total_prodep = 0;
                    $total_prodkg = 0;
                    $total_prodkrg = 0;

                    // total retur
                    $total_returep = 0;
                    $total_returkg = 0;
                    $total_returkrg = 0;

                    // total other
                    $total_othep            = 0;
                    $total_othkg            = 0;
                    $total_othkrg           = 0;

                    $TotalQtyInbound = 0;
                    $TotalBeratInbound = 0;
                    $TotalKarungInbound = 0;

                    // total seluruh inbound
                    $total_inboundep        = 0;
                    $total_inboundkg        = 0;
                    $total_inboundkrg       = 0;

                    // expedisi
                    $total_ekspedisiQty     = 0;
                    $total_ekspedisiBerat   = 0;
                    $total_ekspedisiKrg     = 0;

                    // sample
                    $total_sampleQty        = 0;
                    $total_sampleBerat      = 0;
                    $total_sampleKrg        = 0;

                    // thawing
                    $total_thawingQty       = 0;
                    $total_thawingBerat     = 0;
                    $total_thawingKrg       = 0;

                    // FG
                    $total_freegoodQty      = 0;
                    $total_freegoodBerat    = 0;
                    $total_freegoodKrg      = 0;

                    // other outbound
                    $total_otherOutboundQty      = 0;
                    $total_otherOutboundBerat    = 0;
                    $total_otherOutboundKrg      = 0;

                    $TotalQtyOutbound = 0;
                    $TotalBeratOutbound = 0;
                    $TotalKarungOutbound = 0;

                    // total outbound
                    $total_outboundQty      = 0;
                    $total_outboundBerat    = 0;
                    $total_outboundKrg      = 0;

                    // total akhir
                    $totalAkhirQty          = 0;
                    $totalAkhirBerat        = 0;
                    $totalAkhirKrg          = 0;

                    $total_prodQty          = 0;
                    $total_prodBerat        = 0;
                    $total_prodKrg          = 0;

                    $total_reprodQty          = 0;
                    $total_reprodBerat        = 0;
                    $total_reprodKrg          = 0;

                @endphp


@foreach ($data as $no => $row)

@php
    // total saldo awal
    $total_saep             += $row->qty_saldo_awal;
    $total_sakg             += $row->berat_saldo_awal;
    $total_sakrg            += $row->karung_saldo_awal;

    // total production
    $total_prodep           += $row->hp_qty;
    $total_prodkg           += $row->hp_bb;
    $total_prodkrg           += $row->hp_krg;

    // total retur
    $total_returep           += $row->retur_qty;
    $total_returkg           += $row->retur_bb;
    $total_returkrg           += $row->retur_krg;

    // total other
    $total_othep            += $row->other_qty;
    $total_othkg            += $row->other_bb;
    $total_othkrg           += $row->other_krg;

    // inbound
    $TotalQtyInbound        = $row->hp_qty + $row->retur_qty + $row->other_qty;
    $TotalBeratInbound      = $row->hp_bb + $row->retur_bb + $row->other_bb;
    $TotalKarungInbound     = $row->hp_krg + $row->retur_krg + $row->other_krg;

    // total inbound
    $total_inboundep        += $TotalQtyInbound;
    $total_inboundkg        += $TotalBeratInbound;
    $total_inboundkrg       += $TotalKarungInbound;

    // expedisi
    $total_ekspedisiQty     += $row->siapkirim_qty;
    $total_ekspedisiBerat   += $row->siapkirim_bb;
    $total_ekspedisiKrg     += $row->siapkirim_krg;

    // sample
    $total_sampleQty        += $row->sample_qty;
    $total_sampleBerat      +=  $row->sample_bb;
    $total_sampleKrg        += $row->sample_krg;

    // thawing
    $total_thawingQty       += $row->thawing_qty;
    $total_thawingBerat     += $row->thawing_bb;
    $total_thawingKrg       += $row->thawing_krg;

    // FG
    $total_freegoodQty      += $row->freegood_qty;
    $total_freegoodBerat    += $row->freegood_bb;
    $total_freegoodKrg      += $row->freegood_krg;

    // other outbound
    $total_otherOutboundQty      += $row->otheroutbound_qty;
    $total_otherOutboundBerat    += $row->otheroutbound_bb;
    $total_otherOutboundKrg      += $row->otheroutbound_krg;

    $TotalQtyOutbound       = $row->siapkirim_qty + $row->sample_qty + $row->thawing_qty + $row->freegood_qty + $row->otheroutbound_qty;
    $TotalBeratOutbound     = $row->siapkirim_bb + $row->sample_bb + $row->thawing_bb + $row->freegood_bb + $row->otheroutbound_bb;
    $TotalKarungOutbound    = $row->siapkirim_krg + $row->sample_krg + $row->thawing_krg + $row->freegood_krg + $row->otheroutbound_krg;

    // total outbound
    $total_outboundQty      += $TotalQtyOutbound;
    $total_outboundBerat    += $TotalBeratOutbound;
    $total_outboundKrg      += $TotalKarungOutbound;

@endphp


@php $item_qty_akhir = 0 @endphp
@php $item_berat_akhir = 0 @endphp
@php $item_krg_akhir = 0 @endphp

@php $qty_prod = $row->inb_prod_qty ?? "0" @endphp
@php $berat_prod = $row->inb_prod_bb ?? "0" @endphp
@php $krg_prod = $row->inb_prod_krg ?? "0" @endphp

@php
    $total_prodQty      += $qty_prod;
    $total_prodBerat    += $berat_prod;
    $total_prodKrg      += $krg_prod;
@endphp


@php $qty_reprod = $row->out_prod_qty ?? "0" @endphp
@php $berat_reprod = $row->out_prod_bb ?? "0" @endphp
@php $krg_reprod = $row->out_prod_krg ?? "0" @endphp

@php
    $total_reprodQty      += $qty_reprod;
    $total_reprodBerat    += $berat_reprod;
    $total_reprodKrg      += $krg_reprod;
@endphp

@php $item_qty_akhir = $row->qty_saldo_akhir @endphp
@php $item_berat_akhir = $row->berat_saldo_akhir @endphp
@php $item_krg_akhir = $row->karung_saldo_akhir @endphp


@php
    // saldo akhir
    $totalAkhirQty          += $item_qty_akhir;
    $totalAkhirBerat        += $item_berat_akhir;
    $totalAkhirKrg          += $item_krg_akhir;
@endphp

@php
$category = App\Models\Item::where('id', $row->product_id)->withTrashed()->first()->category_id ?? App\Models\Item::where('nama', $row->nama)->withTrashed()->first()->category_id;
@endphp
@endforeach

<button type="button" class="btn btn-blue float-right mb-2 downloadSOH"><i class="fa fa-spinner fa-spin spinerloading" style="display:none;"></i> <span id="text">Download SOH</span></button>
<div id="container">
    <div class="table-responsive table-height">
        <table class="table table-bordered table-striped table-hover table-sticky" border="1">
            <thead>
                <tr>
                    <th class="text-center" colspan="14" rowspan="2">SOH TANGGAL {{ date('d-m-Y', strtotime($tanggal)) }}</th>
                    <th class="text-center" colspan="3" rowspan="2">Saldo Awal</th>
                    <th class="text-center" colspan="12">Inbound</th>
                    <th class="text-center" colspan="18">Outbound</th>
                    <th class="text-center" colspan="3" rowspan="2">Stock Akhir</th>
                    <th class="text-center" colspan="3" rowspan="2">MTD IN</th>
                    <th class="text-center" colspan="3" rowspan="2">MTD OUT</th>
                </tr>
                <tr>
                    <th class="text-center" colspan="3">Production</th>
                    <th class="text-center" colspan="3">Tolak/Return</th>
                    <th class="text-center" colspan="3">Other</th>
                    <th class="text-center" colspan="3">Total Inbound</th>
                    <th class="text-center" colspan="3">Ekspedisi</th>
                    <th class="text-center" colspan="3">Sample</th>
                    <th class="text-center" colspan="3">Reproses (Thawing)</th>
                    <th class="text-center" colspan="3">Free Good</th>
                    <th class="text-center" colspan="3">Other</th>
                    <th class="text-center" colspan="3">Total Outbound</th>
                </tr>
                <tr>
                    <td rowspan="3" class="stuck">No</td>
                    <td rowspan="3" class="stuck">Item Number</td>
                    <td rowspan="3" class="stuck">Item NS</td>
                    <th rowspan="3">Category</th>
                    <th rowspan="3">Marinated</th>
                    <th rowspan="3">Item Name</th>
                    <th rowspan="3">Parting</th>
                    <th rowspan="3">Isi</th>
                    <th rowspan="3">Grade</th>
                    <th rowspan="3">Customer</th>
                    <th rowspan="3">Status</th>
                    <th rowspan="3">Sales</th>
                    <th rowspan="3">Plastik</th>
                    <th rowspan="3">Loc</th>
                </tr>
    
                

                <tr>
                    @for ($i = 0; $i < 14; $i++) <th>E/P</th>
                        <th>KG</th>
                        <th>KRG</th>
                    @endfor
                </tr>
                
                <tr>

                </tr>
                
                <tr style="font-weight: 600;">
                        <td class="stuck" rowspan="3"></td>
                        <td class="stuck" rowspan="3" style="font-weight: 700;">
                                Total
                        </td>
                        <td class="stuck" rowspan="3">
                        </td>
                        
                    
                </tr>

                <tr style="font-weight: 600;">
                    <th class="text-right" colspan="11"></th>
                    <th class="text-right">{{ number_format($total_saep, 0, ',', '.') }}</th>
                    <th class="text-right">{{ number_format($total_sakg, 2, ',', '.') }}</th>
                    <th class="text-right">{{ $total_sakrg}}</th>

                    {{-- production --}}
                    <th class="text-right">{{ number_format($total_prodep, 0, ',', '.') }}</th>
                    <th class="text-right">{{ number_format($total_prodkg, 2, ',', '.') }}</th>
                    <th class="text-right">{{ $total_prodkrg}}</th>
                    {{-- end production --}}

                    {{-- RETURN --}}
                    <th class="text-right">{{ number_format($total_returep, 0,',', '.') }}</th>
                    <th class="text-right">{{ number_format($total_returkg, 2,',', '.') }}</th>
                    <th class="text-right">{{ $total_returkrg }}</th>
                    {{-- END RETURN --}}

                    {{-- OTHERS --}}
                    <th class="text-right">{{ number_format($total_othep, 0,',', '.') }}</th>
                    <th class="text-right">{{ number_format($total_othkg, 2,',', '.') }}</th>
                    <th class="text-right">{{ $total_othkrg }}</th>
                    {{-- END OTHERS --}}

                    {{-- TOTAL INBOUND --}}
                    <th class="text-right">{{ number_format($total_inboundep, 0,',', '.') }}</th>
                    <th class="text-right">{{ number_format($total_inboundkg, 2,',', '.') }}</th>
                    <th class="text-right">{{ $total_inboundkrg }}</th>
                    {{-- END TOTAL INBOUND --}}

                    {{-- EXPEDISI / PACKING SLIP --}}
                    <th class="text-right">{{ number_format($total_ekspedisiQty, 0,',', '.') }}</th>
                    <th class="text-right">{{ number_format($total_ekspedisiBerat, 2,',', '.') }}</th>
                    <th class="text-right">{{ $total_ekspedisiKrg }}</th>
                    {{-- END EXPEDISI --}}

                    {{-- SAMPLE --}}
                    <th class="text-right">{{ number_format($total_sampleQty, 0,',', '.') }}</th>
                    <th class="text-right">{{ number_format($total_sampleBerat, 2,',', '.') }}</th>
                    <th class="text-right">{{ $total_sampleKrg  }}</th>
                    {{-- END SAMPLE --}}


                    {{-- THAWING --}}
                    <th class="text-right">{{ number_format($total_thawingQty, 0,',', '.') }}</th>
                    <th class="text-right">{{ number_format($total_thawingBerat, 2,',', '.') }}</th>
                    <th class="text-right">{{ $total_thawingKrg }}</th>
                    {{-- END THAWING --}}

                    {{-- FREE GOOD --}}
                    <th class="text-right">{{ number_format($total_freegoodQty, 0,',', '.') }}</th>
                    <th class="text-right">{{ number_format($total_freegoodBerat, 2,',', '.') }}</th>
                    <th class="text-right">{{ $total_freegoodKrg }}</th>
                    {{-- END FREE GOOD --}}

                    {{-- OTHER --}}
                    <th class="text-right">{{ number_format($total_otherOutboundQty, 0,',', '.') }}</th>
                    <th class="text-right">{{ number_format($total_otherOutboundBerat, 2,',', '.') }}</th>
                    <th class="text-right">{{ $total_otherOutboundKrg }}</th>
                    {{-- END OTHER --}}


                    {{-- TOTAL OUTBOUND --}}
                    <th class="text-right">{{ number_format($total_outboundQty, 2,',', '.') }}</th>
                    <th class="text-right">{{ number_format($total_outboundBerat, 2,',', '.') }}</th>
                    <th class="text-right">{{ $total_outboundKrg }}</th>
                    {{-- END TOTAL OUTBOUND --}}

                    {{-- SALDO AKHIR --}}
                    <th class="text-right">{{ number_format($totalAkhirQty, 0,',', '.') }}</th>
                    <th class="text-right">{{ number_format($totalAkhirBerat, 2,',', '.') }}</th>
                    <th>{{ $totalAkhirKrg }}</th>
                    {{-- KARUNG SALDO AKHIR --}}

                    {{-- MTD IN --}}
                    <th class="text-right">{{ number_format($total_prodQty, 2,',', '.') }}</th>
                    <th class="text-right">{{ number_format($total_prodBerat, 2,',', '.') }}</th>
                    <th class="text-right">{{ $total_prodKrg }}</th>
                    {{-- END MTD IN --}}

                    {{-- MTD OUT --}}
                    <th class="text-right">{{ number_format($total_reprodQty, 0,',', '.') }}</th>
                    <th class="text-right">{{ number_format($total_reprodBerat, 2,',', '.') }}</th>
                    <th>{{ $total_reprodKrg }}</th>
                    {{-- END MTD OUT --}}
                </tr>
            </thead>
            <tbody>
           
            @foreach ($data as $no => $row)

            @php
    // total saldo awal
    $total_saep             += $row->qty_saldo_awal;
    $total_sakg             += $row->berat_saldo_awal;
    $total_sakrg            += $row->karung_saldo_awal;

    // total production
    $total_prodep           += $row->hp_qty;
    $total_prodkg           += $row->hp_bb;
    $total_prodkrg           += $row->hp_krg;

    // total retur
    $total_returep           += $row->retur_qty;
    $total_returkg           += $row->retur_bb;
    $total_returkrg           += $row->retur_krg;

    // total other
    $total_othep            += $row->other_qty;
    $total_othkg            += $row->other_bb;
    $total_othkrg           += $row->other_krg;

    // inbound
    $TotalQtyInbound        = $row->hp_qty + $row->retur_qty + $row->other_qty;
    $TotalBeratInbound      = $row->hp_bb + $row->retur_bb + $row->other_bb;
    $TotalKarungInbound     = $row->hp_krg + $row->retur_krg + $row->other_krg;

    // total inbound
    $total_inboundep        += $TotalQtyInbound;
    $total_inboundkg        += $TotalBeratInbound;
    $total_inboundkrg       += $TotalKarungInbound;

    // expedisi
    $total_ekspedisiQty     += $row->siapkirim_qty;
    $total_ekspedisiBerat   += $row->siapkirim_bb;
    $total_ekspedisiKrg     += $row->siapkirim_krg;

    // sample
    $total_sampleQty        += $row->sample_qty;
    $total_sampleBerat      +=  $row->sample_bb;
    $total_sampleKrg        += $row->sample_krg;

    // thawing
    $total_thawingQty       += $row->thawing_qty;
    $total_thawingBerat     += $row->thawing_bb;
    $total_thawingKrg       += $row->thawing_krg;

    // FG
    $total_freegoodQty      += $row->freegood_qty;
    $total_freegoodBerat    += $row->freegood_bb;
    $total_freegoodKrg      += $row->freegood_krg;

    // other outbound
    $total_otherOutboundQty      += $row->otheroutbound_qty;
    $total_otherOutboundBerat    += $row->otheroutbound_bb;
    $total_otherOutboundKrg      += $row->otheroutbound_krg;

    $TotalQtyOutbound       = $row->siapkirim_qty + $row->sample_qty + $row->thawing_qty + $row->freegood_qty + $row->otheroutbound_qty;
    $TotalBeratOutbound     = $row->siapkirim_bb + $row->sample_bb + $row->thawing_bb + $row->freegood_bb + $row->otheroutbound_bb;
    $TotalKarungOutbound    = $row->siapkirim_krg + $row->sample_krg + $row->thawing_krg + $row->freegood_krg + $row->otheroutbound_krg;

    // total outbound
    $total_outboundQty      += $TotalQtyOutbound;
    $total_outboundBerat    += $TotalBeratOutbound;
    $total_outboundKrg      += $TotalKarungOutbound;

@endphp

            @php $qty_prod = $row->inb_prod_qty ?? "0" @endphp
            @php $berat_prod = $row->inb_prod_bb ?? "0" @endphp
            @php $krg_prod = $row->inb_prod_krg ?? "0" @endphp
            
            @php $qty_reprod = $row->out_prod_qty ?? "0" @endphp
            @php $berat_reprod = $row->out_prod_bb ?? "0" @endphp
            @php $krg_reprod = $row->out_prod_krg ?? "0" @endphp
            

            @php $item_qty_akhir = $row->qty_saldo_akhir @endphp
            @php $item_berat_akhir = $row->berat_saldo_akhir @endphp
            @php $item_krg_akhir = $row->karung_saldo_akhir @endphp

                <th class="stuck">{{ $no + 1 }} </th>
                <th class="stuck">
                    <div style="width:100px">{{ App\Models\Item::where('id', $row->product_id)->withTrashed()->first()->sku ?? App\Models\Item::where('nama', $row->nama)->withTrashed()->first()->sku }}</div>
                    <a href="javascript:void(0)" class="edit-ia" data-toggle="modal" data-target="#editSOH" data-name="{{ $row->nama }}" data-sub_item="{{ $row->sub_item }}" data-parting="{{ $row->parting }}" data-plastik_group="{{ $row->plastik_group }}" data-customer="{{ $row->nama_konsumen }}" data-customer_id="{{ $row->customer_id }}" data-qty="{{ $item_qty_akhir }}" data-berat="{{ $item_berat_akhir }}" data-grade="{{ $row->grade_item }}" data-gudang_id="{{ $row->gudang_id }}"><span class="fa fa-edit"></span></a>
                    <a href="{{ route('warehouse.soh_detail') }}?item={{ $row->nama }}&sub_item={{ $row->sub_item }}&parting={{ $row->parting }}&plastik_group={{ $row->plastik_group }}&customer={{ $row->customer_id }}&sub_pack={{ $row->subpack }}&tanggal={{ $tanggal }}&gradeItem={{ $row->grade_item }}&gudang={{ $row->gudang_id }}" target="_blank"><span class="fa fa-share"></span></a>
                </th>
                <th class="stuck">
                    <div style="width:280px">{{ $row->nama }}</div>
                </th>
                <td>
                    <div style="width:100px">
                        @if ($category != '#')
                        {{ App\Models\Category::find($category)->nama ?? '#' }}
                        @endif
                    </div>
                </td>
                <td>
                    <div style="width:100px">
                        @if(str_contains($row->nama, '(M)'))
                        MARINATED
                        @else
                        NON
                        @endif
                    </div>
                </td>
                <td>
                    <div style="width:120px">{{ $row->sub_item }}</div>
                </td>

                <td>
                    <div style="width:50px">{{ $row->parting }}</div>
                </td>

                <td>
                    <div style="width:100px">{{ App\Models\Product_gudang::modusKarungIsi($row->product_id, $row->plastik_group, $row->sub_item, $tanggal, $row->customer_id, $row->grade_item, $row->gudang_id) }}</div>
                </td> {{-- ISI --}}

                <td>
                    <div style="width:50px">{{ $row->grade_item ? 'Grade B' : 'Grade A' }}</div>
                </td> {{-- GRADE --}}

                <td>
                    <div style="width:100px">{{ App\Models\Customer::find($row->customer_id)->nama ?? '#' }}</div>
                </td>

                <td>
                    <div>{{ strtoupper($row->stock_type) }}</div>
                </td> {{-- STATUS --}}
                <td>
                    <div>{{ App\Models\Marketing::where('id', $row->marketing_id)->first()->nama ?? '-' }} </div>
                </td> {{-- SALES --}}

                <td>
                    <div>{{ $row->plastik_group }} </div>
                </td> {{-- PACK / SUB PACK --}}

                <td>
                    <div style="width:100px">{{ substr(App\Models\Gudang::namaGudangWithID($row->gudang_id), 6) }} </div>
                </td> {{-- LOKASI GUDANG --}}

                @if(env('NET_SUBSIDIARY', 'CGL')=='CGL')
                    @if ($tanggal == '2023-05-27')
                        @php
                            $data = App\Models\Product_gudang::cekTypeData($row->product_id, $row->plastik_group, $row->sub_item, $tanggal, $row->customer_id, $row->grade_item, $row->gudang_id);
                        @endphp

                        @if ($data == 0)
                            <td class="text-right">{{ ($data) }}</td> {{-- QTY SALDO AWAL --}}
                            <td class="text-right">{{ ($data) }}</td>{{-- BERAT SALDO AWAL --}}
                            <td class="text-right">{{ $data }}</td>{{-- KARUNG SALDO AWAL --}}
                        @else
                            <td class="text-right">{{ number_format($row->qty_saldo_awal, 0,',', '.') }}</td> {{-- QTY SALDO AWAL --}}
                            <td class="text-right">{{ number_format($row->berat_saldo_awal, 2,',', '.') }}</td>{{-- BERAT SALDO AWAL --}}
                            <td class="text-right">{{ $row->karung_saldo_awal }}</td>{{-- KARUNG SALDO AWAL --}}

                        @endif
                    @else
                        <td class="text-right">{{ number_format($row->qty_saldo_awal, 0,',', '.') }}</td> {{-- QTY SALDO AWAL --}}
                        <td class="text-right">{{ number_format($row->berat_saldo_awal, 2,',', '.') }}</td>{{-- BERAT SALDO AWAL --}}
                        <td class="text-right">{{ $row->karung_saldo_awal }}</td>{{-- KARUNG SALDO AWAL --}}
                    @endif
                @else
                    @if ($tanggal == '2023-05-05')
                        @php
                            $data = App\Models\Product_gudang::cekTypeData($row->product_id, $row->plastik_group, $row->sub_item, $tanggal, $row->customer_id, $row->grade_item, $row->gudang_id);
                        @endphp

                        @if ($data == 0)
                            <td class="text-right">{{ ($data) }}</td> {{-- QTY SALDO AWAL --}}
                            <td class="text-right">{{ ($data) }}</td>{{-- BERAT SALDO AWAL --}}
                            <td class="text-right">{{ $data }}</td>{{-- KARUNG SALDO AWAL --}}
                        @else
                            <td class="text-right">{{ number_format($row->qty_saldo_awal, 0,',', '.') }}</td> {{-- QTY SALDO AWAL --}}
                            <td class="text-right">{{ number_format($row->berat_saldo_awal, 2,',', '.') }}</td>{{-- BERAT SALDO AWAL --}}
                            <td class="text-right">{{ $row->karung_saldo_awal }}</td>{{-- KARUNG SALDO AWAL --}}

                        @endif
                    @else
                        <td class="text-right">{{ number_format($row->qty_saldo_awal, 0,',', '.') }}</td> {{-- QTY SALDO AWAL --}}
                        <td class="text-right">{{ number_format($row->berat_saldo_awal, 2,',', '.') }}</td>{{-- BERAT SALDO AWAL --}}
                        <td class="text-right">{{ $row->karung_saldo_awal }}</td>{{--KARUNG SALDO AWAL --}}
                    @endif

                @endif

                {{-- PRODUCTION --}}
                <td class="text-right">{{ number_format($row->hp_qty, 0,',', '.') }}</td>
                <td class="text-right">{{ number_format($row->hp_bb, 2,',', '.') }}</td>
                <td class="text-right">{{ $row->hp_krg }}</td>
                {{-- END PRODUCTION --}}

                {{-- RETURN --}}
                <td class="text-right">{{ number_format($row->retur_qty, 0,',', '.') }}</td>
                <td class="text-right">{{ number_format($row->retur_bb, 2,',', '.') }}</td>
                <td class="text-right">{{ $row->retur_krg }}</td>
                {{-- END RETURN --}}


                {{-- OTHERS --}}
                <td class="text-right">{{ number_format($row->other_qty, 0,',', '.') }}</td>
                <td class="text-right">{{ number_format($row->other_bb, 2,',', '.') }}</td>
                <td class="text-right">{{ $row->other_krg }}</td>
                {{-- END OTHERS --}}

                {{-- TOTAL INBOUND --}}
                <td class="text-right">{{ number_format($TotalQtyInbound, 0,',', '.') }}</td>
                <td class="text-right">{{ number_format($TotalBeratInbound, 2,',', '.') }}</td>
                <td class="text-right">{{ $TotalKarungInbound }}</td>
                {{-- END TOTAL INBOUND --}}



                {{-- EXPEDISI / PACKING SLIP --}}
                <td class="text-right">{{ number_format($row->siapkirim_qty, 0,',', '.') }}</td>
                <td class="text-right">{{ number_format($row->siapkirim_bb, 2,',', '.') }}</td>
                <td class="text-right">{{ $row->siapkirim_krg }}</td>
                {{-- END EXPEDISI --}}


                {{-- SAMPLE --}}
                <td class="text-right">{{ number_format($row->sample_qty, 0,',', '.') }}</td>
                <td class="text-right">{{ number_format($row->sample_bb, 2,',', '.') }}</td>
                <td class="text-right">{{ $row->sample_krg }}</td>
                {{-- END SAMPLE --}}


                {{-- THAWING --}}
                <td class="text-right">{{ number_format($row->thawing_qty, 0,',', '.') }}</td>
                <td class="text-right">{{ number_format($row->thawing_bb, 2,',', '.') }}</td>
                <td class="text-right">{{ $row->thawing_krg }}</td>
                {{-- END THAWING --}}

                {{-- FREE GOOD --}}
                <td class="text-right">{{ number_format($row->freegood_qty, 0,',', '.') }}</td>
                <td class="text-right">{{ number_format($row->freegood_bb, 2,',', '.') }}</td>
                <td class="text-right">{{ $row->freegood_krg }}</td>
                {{-- END FREE GOOD --}}

                {{-- OTHER --}}
                <td class="text-right">{{ number_format($row->otheroutbound_qty, 0,',', '.') }}</td>
                <td class="text-right">{{ number_format($row->otheroutbound_bb, 2,',', '.') }}</td>
                <td class="text-right">{{ $row->otheroutbound_krg }}</td>
                {{-- END OTHER --}}


                {{-- TOTAL OUTBOUND --}}
                <td class="text-right">{{ number_format($TotalQtyOutbound, 2,',', '.') }}</td>
                <td class="text-right">{{ number_format($TotalBeratOutbound, 2,',', '.') }}</td>
                <td class="text-right">{{ $TotalKarungOutbound }}</td>
                {{-- END TOTAL OUTBOUND --}}

                <td class="text-right">{{ number_format($item_qty_akhir, 0,',', '.') }}</td> {{-- QTY SALDO AKHIR --}}
                <td class="text-right">{{ number_format($item_berat_akhir, 2,',', '.') }}</td> {{-- BERAT SALDO AKHIR --}}
                <td>{{ $item_krg_akhir }}</td> {{-- KARUNG SALDO AKHIR --}}



                <td class="text-right" @if ($qty_prod> 0) style="background-color: pink" @endif>
                    {{ number_format($qty_prod,0,',', '.') }}</td>
                <td class="text-right" @if ($berat_prod> 0) style="background-color: pink" @endif>
                    {{ number_format($berat_prod,2,',', '.') }}</td>
                <td class="text-right" @if ($krg_prod> 0) style="background-color: pink" @endif>
                    {{ number_format($krg_prod,2,',', '.') }}</td>

                <td class="text-right" @if ($qty_reprod> 0) style="background-color: pink" @endif>
                    {{ number_format($qty_reprod,0,',', '.') }}</td>
                <td class="text-right" @if ($berat_reprod> 0) style="background-color: pink" @endif>
                    {{ number_format($berat_reprod,2,',', '.') }}</td>
                <td class="text-right" @if ($krg_reprod> 0) style="background-color: pink" @endif>
                    {{ number_format($krg_reprod,2,',', '.') }}</td>


                </tr>
                @endforeach

            

            </tbody>

        </table>
    </div>
</div>


<div class="modal fade" id="editSOH" tabindex="-1" aria-labelledby="editLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editLabel">Inventory Adjustment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{-- <form method="post" action="{{ route('warehouse.soh_ia') }}">
                @csrf --}}
                <div class="modal-body">

                    <div class="form-group">
                        Nama Item
                        <input type="text" name="item_name" id="form-nama" class="form-control" value="" readonly>
                        <input type="hidden" name="tanggal" id="form-tanggal" class="form-control" value="{{ $tanggal }}" readonly>
                    </div>

                    <div class="form-group">
                        Parting
                        <input type="text" name="parting" id="form-parting" class="form-control" value="" readonly>
                    </div>

                    <div class="form-group">
                        Sub Item
                        <input type="text" name="sub_item" id="form-sub_item" class="form-control" value="" readonly>
                    </div>

                    <div class="form-group">
                        Kemasan
                        <input type="text" name="packaging" id="form-packaging" class="form-control" value="" readonly>
                    </div>

                    {{-- <div class="form-group">
                        Sub Packaging
                        <input type="text" name="sub_pack" id="form-sub_pack" class="form-control" value="" readonly>
                    </div> --}}

                    <div class="form-group">
                        Grade Item
                        <input type="text" name="grade" id="grade" class="form-control" value="" readonly>
                    </div>
                    {{-- <div class="form-group">
                        Gudang
                        <input type="text" name="gudang" id="gudang" class="form-control" value="" readonly>
                    </div> --}}

                    <input type="text" name="gudang_id" id="gudang_id" class="form-control" value="" hidden>
                    <div class="form-group">
                        Customer
                        <input type="text" name="customer" id="form-customer" class="form-control" value="" readonly>
                        <input type="text" name="customer_id" id="customer_id" class="form-control" value="" hidden>
                    </div>

                    <div class="row">
                        <div class="col pr-1">
                            <div class="form-group">
                                Qty
                                <input name="qty" type="number" value="" class="form-control" id="qty">
                            </div>
                        </div>
                        <div class="col pl-1">
                            <div class="form-group">
                                Berat
                                <input name="berat" type="number" value="" class="form-control" id="berat" step="0.01">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary update_ia" onclick="updateIA()">Save</button>
                </div>
            {{-- </form> --}}
        </div>
    </div>
</div>

<style>
    .table-sticky>thead>tr>th,
    .table-sticky>thead>tr>td {
        background: #009688;
        color: #fff;
        position: sticky;
    }

    .table-height {
        height: 500px;
        display: block;
        overflow: scroll;
        width: 100%;
    }

    table {
        border-collapse: collapse;
        border-spacing: 0;
    }

    .table-sticky thead {
        position: sticky;
        top: 0px;
        z-index: 1;
    }

    .table-sticky thead td {
        position: sticky;
        top: 0px;
        left: 0;
        z-index: 4;
        background-color: #f9fbfd;
        color: #95aac9;
    }

    .table-sticky tbody th {
        position: sticky;
        left: 0;
        background-color: white;
        z-index: 0;

    }

    /* .table-sticky tbody th {
    position: sticky;
    background-color: #95aac9;
    z-index: 0;
} */

    tbody th.stuck:nth-child(2) {
        left: 35px;
    }

    tbody th.stuck:nth-child(1) {
        left: 0px;
    }

    tbody th.stuck:nth-child(3) {
        left: 150px;
    }

    thead td.stuck:nth-child(2) {
        left: 35px;

    }

    thead td.stuck:nth-child(1) {
        left: 0px;

    }

    thead td.stuck:nth-child(3) {
        left: 150px;

    }

    /* thead tr:nth-child(1) th {
    position: sticky; top: 0;
}
thead tr:nth-child(2) th {
    position: sticky; top: 40px;
} */

    /* .table-bordered>thead>tr>th,
.table-bordered>tbody>tr>th,
.table-bordered>thead>tr>td,
.table-bordered>tbody>tr>td {
 border: 1px solid #ddd;
} */

</style>

<script>
    $(document).ready(function() {
        $('.edit-ia').on('click', function() {
            var name = $(this).data('name');
            var sub_item = $(this).data('sub_item');
            // var sub_pack = $(this).data('sub_pack');
            var plastik_group = $(this).data('plastik_group');
            var customer = $(this).data('customer');
            var parting = $(this).data('parting');
            var qty = $(this).data('qty');
            var berat = $(this).data('berat') ;
            var grade = $(this).data('grade') == '' ? 'A' : 'B';
            var customer_id = $(this).data('customer_id');
            var gudang_id = $(this).data('gudang_id');

            $('#form-nama').val(name);
            $('#form-sub_item').val(sub_item);
            // $('#form-sub_pack').val(sub_pack);
            $('#form-packaging').val(plastik_group);
            $('#form-customer').val(customer);
            $('#form-parting').val(parting);
            $('#qty').val(qty);
            $('#berat').val(berat);
            $('#customer_id').val(customer_id);
            $('#grade').val(grade);
            $('#gudang_id').val(gudang_id);

        })
    })


    function updateIA() {
        let namaItem    = $("#form-nama").val();
        let subItem     = $("#form-sub_item").val();
        let packaging   = $("#form-packaging").val();
        let customer    = $("#form-customer").val();
        let parting     = $("#form-parting").val();
        let qty         = $("#qty").val();
        let berat       = $("#berat").val();
        let customerId  = $("#customer_id").val();
        let grade       = $("#grade").val();
        let gudangId    = $("#gudang_id").val();
        let tanggal     = $("#form-tanggal").val();

        $.ajaxSetup({
        headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
        });
        $.ajax({
            url     : "{{ route('warehouse.soh_ia') }}",
            method  : "POST",
            cache   : false,
            data    :{
                namaItem,
                subItem,
                packaging,
                customer,
                parting,
                qty,
                berat,
                customerId,
                grade,
                gudangId,
                tanggal
            },
            success: function(data) {
                // console.log(data)
                if (data.status == 200) {
                    showNotif(data.msg)
                    $('#editSOH').modal('hide');
                    LoadDataSOH();

                } else {
                    showAlert(data.msg)
                }

            }
        });
    }


    var container  = $('#container').html();
    $('#downloadSOH').val(container)
    // $(document).ready(function(){
    //     var html  = $('#export-soh').html();
    //     $('#html-export-soh').val(html);
    //     const tanggalSOH = document.getElementById('tanggal_soh').value
    //     document.getElementById('filename').value = `export-soh-${tanggalSOH}.xls`
    // })

    $(".downloadSOH").click(function(){
        let tanggal     = "{{ $tanggal ?? date('Y-m-d') }}";
        let firstmonth  = "{{ $firstMonth ?? date('Y-m-d') }}";
        let item        = "{{ $item ?? NULL }}";
        let cari        = "{{ $cari ?? NULL }}";

        let category    = "{{ $category_soh ?? NULL }}";
        let marinated   = "{{ $marinated_soh ?? NULL }}";
        let subitem     = "{{ $itemname_soh ?? NULL }}";
        let grade       = "{{ $grade_soh ?? NULL }}";
        let customerSOH = "{{ $customername_soh ?? NULL }}";

        let ordering    = "{{ $ordering ?? NULL }}";
        let order_by    = "{{ $order_by ?? NULL }}";

        $.ajax({
            url     : "{{ route('warehouse_dash.index') }}",
            method  : "GET",
            cache   : false,
            data    :{
                'tanggal'       : tanggal,
                'firstMonth'    : firstmonth,
                'item'          : item,
                'cari'          : cari,
                'category'      : category,
                'marinated'     : marinated,
                'subitem'       : subitem,
                'grade'         : grade,
                'customerSOH'   : customerSOH,
                'ordering'      : ordering,
                'order_by'      : order_by,
                'key'           : 'unduhsoh'

            },
            beforeSend: function() {
                $('.downloadSOH').attr('disabled');
                $(".spinerloading").show();
                $("#text").text('Downloading...');
            },
            success: function(data) {
                $(".downloadSOH").attr('disabled');
                setTimeout(() => {
                    $("#text").text('Download SOH');
                    $(".spinerloading").hide();
                    window.location.href = "{{ route('warehouse_dash.index') }}?tanggal=" + tanggal + "&firstMonth=" + firstmonth + "&item=" + item +"&category=" + category +"&marinated=" + marinated +"&subitem=" + subitem
                                                +"&grade=" + grade + "&customer=" + customerSOH +"&ordering=" + ordering + "&order_by=" + order_by + "&cari="+ cari +"&key=unduhsoh";
                }, 1000);
            }
        });
    })


</script>

