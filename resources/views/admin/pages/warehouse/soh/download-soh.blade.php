@php
    header('Content-Transfer-Encoding: none');
    header("Content-type: application/vnd-ms-excel");
    header("Content-type: application/x-msexcel");
    header("Content-Disposition: attachment; filename=Export-SOH.xls");
@endphp

<style>
    .text-right {
        text-align:right;
    }
</style>
<div id="container">
    <div class="table-responsive table-height">
        <table class="table table-bordered table-striped table-hover table-sticky" border="1">
            <thead>
                <tr>
                    <th class="text-center" colspan="14">SOH TANGGAL {{ date('d-m-Y', strtotime($tanggal)) }}</th>
                    <th class="text-center" colspan="3" rowspan="2" >SALDO AWAL</th>
                    <th class="text-center" colspan="12">INBOUND</th>
                    <th class="text-center" colspan="18">OUTBOUND</th>
                    <th class="text-center" colspan="3" rowspan="2">STOCK AKHIR</th>
                    <th class="text-center" colspan="3" rowspan="2">MTD IN</th>
                    <th class="text-center" colspan="3" rowspan="2">MTD OUT</th>
                </tr>
                <tr>
                    <th rowspan="2" class="stuck" >No</th>
                    <th rowspan="2" class="stuck" >Item Number</th>
                    <th rowspan="2" class="stuck" >Item NS</th>
                    <th rowspan="2" >Category</th>
                    <th rowspan="2" >Marinated</th>
                    <th rowspan="2" >Item Name</th>
                    <th rowspan="2" >Parting</th>
                    <th rowspan="2" >Isi</th>
                    <th rowspan="2" >Brand</th>
                    <th rowspan="2" >Customer</th>
                    <th rowspan="2" >Status</th>
                    <th rowspan="2" >Sales</th>
                    <th rowspan="2" >Pack</th>
                    <th rowspan="2" >Loc</th>
                    <th class="text-center" colspan="3" >Production</th>
                    <th class="text-center" colspan="3" >Tolak/Return</th>
                    <th class="text-center" colspan="3" >Other</th>
                    <th class="text-center" colspan="3" >Total Inbound</th>
                    <th class="text-center" colspan="3" >Ekspedisi</th>
                    <th class="text-center" colspan="3" >Sample</th>
                    <th class="text-center" colspan="3" >Reproses (Thawing)</th>
                    <th class="text-center" colspan="3" >Free Good</th>
                    <th class="text-center" colspan="3" >Other</th>
                    <th class="text-center" colspan="3" >Total Outbound</th>
                </tr>
                <tr>
                    @for ($i = 0; $i < 14; $i++)
                        <th>E/P</th>
                        <th>KG</th>
                        <th>KRG</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
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
                    @php $item_qty_akhir    = 0 @endphp
                    @php $item_berat_akhir  = 0 @endphp
                    @php $item_krg_akhir    = 0 @endphp

                {{-- @if ($row->berat_saldo_awal != 0 || $row->berat_saldo_awal != $row->berat_saldo_akhir) --}}

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
                @php $krg_prod = $row->inb_prod_krj ?? "0" @endphp

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
                    $totalAkhirKrg          += $item_berat_akhir;   
                @endphp

                @php
                $category = App\Models\Item::find($row->product_id)->category_id ?? '#';
                @endphp
                <tr>
                    <td class="stuck">{{ $no + 1 }} </td>
                    <td class="stuck">
                        <div style="width:100px">{{ App\Models\Item::find($row->product_id)->sku ?? '#' }}</div>
                    </td>
                    <td class="stuck">
                        <div style="width:280px">{{ $row->nama }}</div>
                    </td>
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
                            @endif</div>
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
                            <td class="text-right">{{ $row->karung_saldo_awal }}</td>{{-- KARUNG SALDO AWAL --}}
                        @endif

                    @endif



                    {{-- PRODUCTION --}}
                    <td class="text-right">{{ number_format($row->hp_qty, 0) }}</td>
                    <td class="text-right">{{ number_format($row->hp_bb, 2) }}</td>
                    <td class="text-right">{{ $row->hp_krg }}</td>
                    {{-- END PRODUCTION --}}

                    {{-- RETURN --}}
                    <td class="text-right">{{ number_format($row->retur_qty, 0) }}</td>
                    <td class="text-right">{{ number_format($row->retur_bb, 2) }}</td>
                    <td class="text-right">{{ $row->retur_krg }}</td>
                    {{-- END RETURN --}}


                    {{-- OTHERS --}}
                    <td class="text-right">{{ number_format($row->other_qty, 0) }}</td>
                    <td class="text-right">{{ number_format($row->other_bb, 2) }}</td>
                    <td class="text-right">{{ $row->other_krg }}</td>
                    {{-- END OTHERS --}}

                    {{-- TOTAL INBOUND --}}
                    <td class="text-right">{{ number_format($TotalQtyInbound, 0) }}</td>
                    <td class="text-right">{{ number_format($TotalBeratInbound, 2) }}</td>
                    <td class="text-right">{{ $TotalKarungInbound }}</td>
                    {{-- END TOTAL INBOUND --}}



                    {{-- EXPEDISI / PACKING SLIP --}}
                    <td class="text-right">{{ number_format($row->siapkirim_qty, 0) }}</td>
                    <td class="text-right">{{ number_format($row->siapkirim_bb, 2) }}</td>
                    <td class="text-right">{{ $row->siapkirim_krg }}</td>
                    {{-- END EXPEDISI --}}


                    {{-- SAMPLE --}}
                    <td class="text-right">{{ number_format($row->sample_qty, 0) }}</td>
                    <td class="text-right">{{ number_format($row->sample_bb, 2) }}</td>
                    <td class="text-right">{{ $row->sample_krg }}</td>
                    {{-- END SAMPLE --}}


                    {{-- THAWING --}}
                    <td class="text-right">{{ number_format($row->thawing_qty, 0) }}</td>
                    <td class="text-right">{{ number_format($row->thawing_bb, 2) }}</td>
                    <td class="text-right">{{ $row->thawing_krg }}</td>
                    {{-- END THAWING --}}

                    {{-- FREE GOOD --}}
                    <td class="text-right">{{ number_format($row->freegood_qty, 0) }}</td>
                    <td class="text-right">{{ number_format($row->freegood_bb, 2) }}</td>
                    <td class="text-right">{{ $row->freegood_krg }}</td>
                    {{-- END FREE GOOD --}}

                    {{-- OTHER --}}
                    <td class="text-right">{{ number_format($row->otheroutbound_qty, 0) }}</td>
                    <td class="text-right">{{ number_format($row->otheroutbound_bb, 2) }}</td>
                    <td class="text-right">{{ $row->otheroutbound_krg }}</td>
                    {{-- END OTHER --}}


                    {{-- TOTAL OUTBOUND --}}
                    <td class="text-right">{{ number_format($TotalQtyOutbound, 0) }}</td>
                    <td class="text-right">{{ number_format($TotalBeratOutbound, 2) }}</td>
                    <td class="text-right">{{ $TotalKarungOutbound }}</td>
                    {{-- END TOTAL OUTBOUND --}}

                    <td class="text-right">{{ number_format($item_qty_akhir, 0) }}</td> {{-- QTY SALDO AKHIR --}}
                    <td class="text-right">{{ number_format($item_berat_akhir, 2) }}</td> {{-- BERAT SALDO AKHIR --}}
                    <td class="text-right">{{ $item_krg_akhir }}</td> {{-- KARUNG SALDO AKHIR --}}

                    <td @if ($qty_prod> 0) style="background-color: pink" @endif class="text-right">
                        {{ number_format($qty_prod, 0) }}
                    </td>
                    <td @if ($berat_prod> 0) style="background-color: pink" @endif class="text-right">
                        {{ number_format($berat_prod,2) }}
                    </td>
                    <td @if ($krg_prod> 0) style="background-color: pink" @endif class="text-right">
                        {{ $krg_prod }}
                    </td>

                    <td @if ($qty_reprod> 0) style="background-color: pink" @endif class="text-right">
                        {{ number_format($qty_reprod,0) }}
                    </td>
                    <td @if ($berat_reprod> 0) style="background-color: pink" @endif class="text-right">
                        {{ number_format($berat_reprod,2) }}
                    </td>
                    <td @if ($krg_reprod> 0) style="background-color: pink" @endif class="text-right">
                        {{ $krg_reprod }}
                    </td>
                </tr>
                @endforeach

                <tr style="font-weight: 600;" >
                    <th class="text-left" colspan="14" 
                    style="font-weight: 600;background-color:#98CE00;text-align:left;">
                        Total
                    </th>
                    {{-- <td class="text-right" colspan="11"></td> --}}
                    <td class="text-right" style="font-weight: 600;background-color:#98CE00;">{{ number_format($total_saep, 0, ',', '.') }}</td>
                    <td class="text-right" style="font-weight: 600;background-color:#98CE00;">{{ number_format($total_sakg, 2, ',', '.') }}</td>
                    <td class="text-right" style="font-weight: 600;background-color:#98CE00;">
                        {{ $total_sakrg}}
                    </td>
    
                    {{-- production --}}
                    <td class="text-right" style="font-weight: 600;background-color:#98CE00;">{{ number_format($total_prodep, 0, ',', '.') }}</td>
                    <td class="text-right" style="font-weight: 600;background-color:#98CE00;">{{ number_format($total_prodkg, 2, ',', '.') }}</td>
                    <td class="text-right" 
                    style="font-weight: 600;background-color:#98CE00;">
                    {{ $total_prodkrg}}
                    </td>
                    {{-- end production --}}
    
                    {{-- RETURN --}}
                    <td class="text-right" 
                    style="font-weight: 600;
                    background-color:#98CE00;">{{ number_format($total_returep, 0,',', '.') }}
                    </td>
                    <td class="text-right" 
                    style="font-weight: 600;
                    background-color:#98CE00;">{{ number_format($total_returkg, 2,',', '.') }}
                    </td>
                    <td class="text-right" 
                    style="font-weight: 600;
                    background-color:#98CE00;">{{ $total_returkrg }}
                    </td>
                    {{-- END RETURN --}}
    
                    {{-- OTHERS --}}
                    <td class="text-right"
                    style="font-weight: 600;
                    background-color:#98CE00;">{{ number_format($total_othep, 0,',', '.') }}
                    </td>
                    <td class="text-right" 
                    style="font-weight: 600;
                    background-color:#98CE00;">{{ number_format($total_othkg, 2,',', '.') }}
                    </td>
                    <td class="text-right" 
                    style="font-weight: 600;
                    background-color:#98CE00;">{{ $total_othkrg }}
                    </td>
                    {{-- END OTHERS --}}
    
                    {{-- TOTAL INBOUND --}}
                    <td class="text-right" 
                    style="font-weight: 600;
                    background-color:#98CE00;">{{ number_format($total_inboundep, 0,',', '.') }}</td>
                    <td class="text-right" 
                    style="font-weight: 600;
                    background-color:#98CE00;">{{ number_format($total_inboundkg, 2,',', '.') }}</td>
                    <td class="text-right" 
                    style="font-weight: 600;
                    background-color:#98CE00;">{{ $total_inboundkrg }}
                    </td>
                    {{-- END TOTAL INBOUND --}}
    
                    {{-- EXPEDISI / PACKING SLIP --}}
                    <td class="text-right" 
                    style="font-weight: 600;
                    background-color:#98CE00;">{{ number_format($total_ekspedisiQty, 0,',', '.') }}
                    </td>
                    <td class="text-right"
                    style="font-weight: 600;
                    background-color:#98CE00;">{{ number_format($total_ekspedisiBerat, 2,',', '.') }}
                    </td>
                    <td class="text-right" 
                    style="font-weight: 600;
                    background-color:#98CE00;">{{ $total_ekspedisiKrg }}
                    </td>
                    {{-- END EXPEDISI --}}
    
                    {{-- SAMPLE --}}
                    <td class="text-right" 
                    style="font-weight: 600;
                    background-color:#98CE00;">{{ number_format($total_sampleQty, 0,',', '.') }}</td>
                    <td class="text-right"
                    style="font-weight: 600;
                    background-color:#98CE00;">{{ number_format($total_sampleBerat, 2,',', '.') }}
                    </td>
                    <td class="text-right"
                    style="font-weight: 600;
                    background-color:#98CE00;">{{ $total_sampleKrg  }}
                    </td>
                    {{-- END SAMPLE --}}
    
    
                    {{-- THAWING --}}
                    <td class="text-right"
                    style="font-weight: 600;
                    background-color:#98CE00;">
                    {{ number_format($total_thawingQty, 0,',', '.') }}
                    </td>
                    <td class="text-right" 
                    style="font-weight: 600;
                    background-color:#98CE00;">
                    {{ number_format($total_thawingBerat, 2,',', '.') }}
                    </td>
                    <td class="text-right"
                    style="font-weight: 600;background-color:#98CE00;">
                    {{ $total_thawingKrg }}
                    </td>
                    {{-- END THAWING --}}
    
                    {{-- FREE GOOD --}}
                    <td class="text-right" 
                    style="font-weight: 600;
                    background-color:#98CE00;">
                    {{ number_format($total_freegoodQty, 0,',', '.') }}
                    </td>
                    <td class="text-right" style="font-weight: 600;background-color:#98CE00;">
                        {{ number_format($total_freegoodBerat, 2,',', '.') }}
                    </td>
                    <td class="text-right" style="font-weight: 600;background-color:#98CE00;">
                        {{ $total_freegoodKrg }}
                    </td>
                    {{-- END FREE GOOD --}}
    
                    {{-- OTHER --}}
                    <td class="text-right" 
                    style="font-weight: 600;
                    background-color:#98CE00;">
                    {{ number_format($total_otherOutboundQty, 0,',', '.') }}
                    </td>
                    <td class="text-right" 
                    style="font-weight: 600;background-color:#98CE00;">
                        {{ number_format($total_otherOutboundBerat, 2,',', '.') }}
                    </td>
                    <td class="text-right"
                    style="font-weight: 600;background-color:#98CE00;">
                        {{ $total_otherOutboundKrg }}
                    </td>
                    {{-- END OTHER --}}
    
    
                    {{-- TOTAL OUTBOUND --}}
                    <td class="text-right" 
                    style="font-weight: 600;background-color:#98CE00;">
                        {{ number_format($total_outboundQty, 2,',', '.') }}
                    </td>
                    <td class="text-right"
                    style="font-weight: 600;background-color:#98CE00;">
                        {{ number_format($total_outboundBerat, 2,',', '.') }}
                    </td>
                    <td class="text-right"
                    style="font-weight: 600;background-color:#98CE00;">
                        {{ $total_outboundKrg }}
                    </td>
                    {{-- END TOTAL OUTBOUND --}}
    
                    {{-- SALDO AKHIR --}}
                    <td class="text-right"
                    style="font-weight: 600;background-color:#98CE00;">
                        {{ number_format($totalAkhirQty, 0,',', '.') }}
                    </td> 
                    <td class="text-right" 
                    style="font-weight: 600;background-color:#98CE00;">
                        {{ number_format($totalAkhirBerat, 2,',', '.') }}
                    </td> 
                    <td style="font-weight: 600;background-color:#98CE00;">
                        {{ $totalAkhirKrg }}
                    </td> 
                    {{-- KARUNG SALDO AKHIR --}}
                    
                     {{-- MTD IN --}}
                    <td class="text-right"
                    style="font-weight: 600;background-color:#98CE00;">
                        {{ number_format($total_prodQty, 2,',', '.') }}
                    </td>
                    <td class="text-right"
                    style="font-weight: 600;background-color:#98CE00;">
                        {{ number_format($total_prodBerat, 2,',', '.') }}
                    </td>
                    <td class="text-right"
                    style="font-weight: 600;background-color:#98CE00;">
                        {{ $total_prodKrg }}
                    </td>
                    {{-- END MTD IN --}}

                    {{-- MTD OUT --}}
                    <td class="text-right" 
                    style="font-weight: 600;background-color:#98CE00;">
                        {{ number_format($total_reprodQty, 0,',', '.') }}
                    </td> 
                    <td class="text-right"
                    style="font-weight: 600;background-color:#98CE00;">
                    {{ number_format($total_reprodBerat, 2,',', '.') }}
                    </td> 
                    <td style="font-weight: 600;background-color:#98CE00;">
                        {{ $total_reprodKrg }}
                    </td> 
                    {{-- END MTD OUT --}}
                </tr>
            </tbody>
        </table>
    </div>
</div>
