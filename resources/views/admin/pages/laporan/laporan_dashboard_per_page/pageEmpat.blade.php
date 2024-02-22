<style>
    .title-color-wo {
        text-align: center;
        background-color: rgb(220, 235, 248);
    }
    .title-color-non-wo {
        text-align: center;
        background-color: rgb(237, 209, 174);
    }
</style>
<b>Susut Bahan Baku - Produksi</b>
<div class="row mb-3">
    <div class="col-sm-4 col-lg mb-2 pr-sm-1">
        <div class="card">
            <div class="card-header">Boneless</div>
            <div class="card-body p-2">
                <div class="small title-color-wo">Work Order</div>
                <div class="row mb-1">
                    <div class="col pr-1">
                        <div class="border text-center">
                            <div class="small">Bahan baku</div>
                            <div class="font-weight-bold"> {{$produksi['dataBonelessWOBB'] }} kg </div>
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="border text-center">
                            <div class="small">Produksi</div>
                            <div class="font-weight-bold">  {{ $produksi['dataBonelessWOFG'] }} kg</div>
                        </div>
                    </div>
                </div>
                <div class="small title-color-non-wo mt-2">Non Work Order</div>
                <div class="row mb-1">
                    <div class="col pr-1">
                        <div class="border text-center">
                            <div class="small">Bahan baku</div>
                            <div class="font-weight-bold"> {{$produksi['dataBonelessNONWOBB'] }} kg </div>
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="border text-center">
                            <div class="small">Produksi</div>
                            <div class="font-weight-bold">  {{ $produksi['dataBonelessNONWOFG'] }} kg</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col pr-1">
                        <div class="border text-center">
                            <div class="small">Total</div>
                            <div class="font-weight-bold"> {{ number_format(($produksi['dataBonelessWOBB'] + $produksi['dataBonelessNONWOBB']) , 2) }} kg</div>
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="border text-center">
                            <div class="small">Total</div>
                            <div class="font-weight-bold"> {{ number_format((($produksi['dataBonelessWOFG'] + $produksi['dataBonelessNONWOFG'])) , 2) }} kg</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col pr-1">
                        <div class="border text-center">
                            <div class="small">Selisih</div>
                            @php
                                $selisih_boneless = ($produksi['dataBonelessWOFG'] - $produksi['dataBonelessWOBB']);
                            @endphp
                            <div class="font-weight-bold"> {{ number_format($selisih_boneless, 2) }} kg</div>
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="border text-center">
                            <div class="small">Persentase</div>
                            @php
                                $persentase = 0;
                                // $produksi_boneless = $produksi['bb_tt_boneless'] + $produksi['non_wo_bb_boneless'];
                                if($produksi['dataBonelessWOFG'] > 0) {
                                    $persentase = number_format(($selisih_boneless / $produksi['dataBonelessWOBB'] * 100), 2);
                                }
                            @endphp
                            <div class="font-weight-bold">
                                @if($persentase>0)
                                <span class="green">{{$persentase}}% </span> <span class="fa fa-caret-up green"></span>
                                @elseif($persentase<0)
                                <span class="red">{{$persentase}}% </span> <span class="fa fa-caret-down red"></span>
                                @else
                                <span class="blue">{{$persentase}}% </span>
                                @endif
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4 col-lg mb-2 px-sm-1">
        <div class="card">
            <div class="card-header">Parting</div>
            <div class="card-body p-2">
                <div class="small title-color-wo">Work Order</div>
                <div class="row mb-1">
                    <div class="col pr-1">
                        <div class="border text-center">
                            <div class="small">Bahan baku</div>
                            <div class="font-weight-bold"> {{ $produksi['dataPartingWOBB']}} kg</div>
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="border text-center">
                            <div class="small">Produksi</div>
                            <div class="font-weight-bold"> {{ $produksi['dataPartingWOFG']}} kg</div>
                        </div>
                    </div>
                </div>
                <div class="small title-color-non-wo mt-2">Non Work Order</div>
                <div class="row mb-1">
                    <div class="col pr-1">
                        <div class="border text-center">
                            <div class="small">Bahan baku</div>
                            <div class="font-weight-bold"> {{ $produksi['dataPartingNONWOBB'] }} kg</div>
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="border text-center">
                            <div class="small">Produksi</div>
                            <div class="font-weight-bold"> {{ $produksi['dataPartingNONWOFG'] }} kg</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col pr-1">
                        <div class="border text-center">
                            <div class="small">Total</div>
                            <div class="font-weight-bold"> {{ number_format(($produksi['dataPartingWOBB'] + $produksi['dataPartingNONWOBB']) , 2) }} kg</div>
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="border text-center">
                            <div class="small">Total</div>
                            <div class="font-weight-bold"> {{ number_format((($produksi['dataPartingWOFG'] + $produksi['dataPartingNONWOFG'])) , 2) }} kg</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col pr-1">
                        <div class="border text-center">
                            <div class="small">Selisih</div>
                            @php
                                $selisih_parting = ($produksi['dataPartingWOFG'] - $produksi['dataPartingWOBB']) * -1;
                            @endphp
                            <div class="font-weight-bold"> {{ number_format($selisih_parting * -1, 2) }} kg</div>
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="border text-center">
                            <div class="small">Persentase</div>
                            @php
                                $persentase = 0;
                                // $produksi_parting = $produksi['dataWholeWOFG'] - $produksi['dataPartingWOBB'];
                                if($produksi['dataPartingWOBB']>0){
                                    $persentase = number_format(($selisih_parting / $produksi['dataPartingWOBB'] *100) * -1, 2);
                                }
                            @endphp
                            <div class="font-weight-bold">
                                @if($persentase>0)
                                <span class="green">{{$persentase}}% </span> <span class="fa fa-caret-up green"></span>
                                @elseif($persentase<0)
                                <span class="red">{{$persentase}}% </span> <span class="fa fa-caret-down red"></span>
                                @else
                                <span class="blue">{{$persentase}}% </span>
                                @endif
                                </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="col-sm-4 col-lg pl-sm-1 pl-lg-0 mb-2 px-lg-1">
        <div class="card">
            <div class="card-header">Parting M</div>
            <div class="card-body p-2">
                <div class="small title-color-wo">Work Order</div>
                <div class="row mb-1">
                    <div class="col pr-1">
                        <div class="border text-center">
                            <div class="small">Bahan baku</div>
                            <div class="font-weight-bold"> {{ $produksi['dataMarinasiWOBB']}} kg</div>
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="border text-center">
                            <div class="small">Produksi</div>
                            <div class="font-weight-bold"> {{ $produksi['dataMarinasiWOFG']}} kg</div>
                        </div>
                    </div>
                </div>
                <div class="small title-color-non-wo mt-2">Non Work Order</div>
                <div class="row mb-1">
                    <div class="col pr-1">
                        <div class="border text-center">
                            <div class="small">Bahan baku</div>
                            <div class="font-weight-bold"> {{ $produksi['dataMarinasiNONWOBB'] }} kg</div>
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="border text-center">
                            <div class="small">Produksi</div>
                            <div class="font-weight-bold"> {{ $produksi['dataMarinasiNONWOFG'] }} kg</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col pr-1">
                        <div class="border text-center">
                            <div class="small">Total</div>
                            <div class="font-weight-bold"> {{ number_format(($produksi['dataMarinasiWOBB'] + $produksi['dataMarinasiNONWOBB']), 2) }} kg</div>
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="border text-center">
                            <div class="small">Total</div>
                            <div class="font-weight-bold"> {{ number_format((($produksi['dataMarinasiWOFG'] + $produksi['dataMarinasiNONWOFG'])), 2) }} kg</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col pr-1">
                        <div class="border text-center">
                            <div class="small">Selisih</div>
                            @php
                                $selisih_marinasi = ($produksi['dataMarinasiWOFG'] - $produksi['dataMarinasiWOBB']);
                            @endphp
                            <div class="font-weight-bold"> {{ number_format($selisih_marinasi, 2) }} kg</div>
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="border text-center">
                            <div class="small">Persentase</div>
                            @php
                                $persentase = 0;
                                // $produksi_marinasi = $produksi['bb_tt_marinasi'] + $produksi['non_wo_bb_marinasi'];
                                if($produksi['dataMarinasiWOBB']>0){
                                    $persentase = number_format(($selisih_marinasi/$produksi['dataMarinasiWOBB'] *100), 2);
                                    // $persentase = number_format((($produksi['bb_tt_marinasi'] - $produksi['fg_tt_marinasi'])/$produksi['bb_tt_marinasi']*100) * -1, 2);
                                }
                            @endphp
                            <div class="font-weight-bold">
                                @if($persentase>0)
                                <span class="green">{{$persentase}}% </span> <span class="fa fa-caret-up green"></span>
                                @elseif($persentase<0)
                                <span class="red">{{$persentase}}% </span> <span class="fa fa-caret-down red"></span>
                                @else
                                <span class="blue">{{$persentase}}% </span>
                                @endif
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4 col-lg pr-sm-1 pr-lg-0 mb-2 px-lg-1">
        <div class="card">
            <div class="card-header">Whole Chicken</div>
            <div class="card-body p-2">
                <div class="small title-color-wo">Work Order</div>
                <div class="row mb-1">
                    <div class="col pr-1">
                        <div class="border text-center">
                            <div class="small">Bahan baku</div>
                            <div class="font-weight-bold"> {{ $produksi['dataWholeWOBB'] }} kg</div>
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="border text-center">
                            <div class="small">Produksi</div>
                            <div class="font-weight-bold"> {{ $produksi['dataWholeWOFG'] }} kg</div>
                        </div>
                    </div>
                </div>
                <div class="small title-color-non-wo mt-2">Non Work Order</div>
                <div class="row mb-1">
                    <div class="col pr-1">
                        <div class="border text-center">
                            <div class="small">Bahan baku</div>
                            <div class="font-weight-bold"> {{ $produksi['dataWholeNONWOBB'] }} kg</div>
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="border text-center">
                            <div class="small">Produksi</div>
                            <div class="font-weight-bold"> {{ $produksi['dataWholeNONWOFG'] }} kg</div>
                        </div>
                    </div>
                </div>
                <div class="row mt-1">
                    <div class="col pr-1">
                        <div class="border text-center">
                            <div class="small">Total</div>
                            <div class="font-weight-bold"> {{ number_format(($produksi['dataWholeWOBB'] + $produksi['dataWholeNONWOBB']) , 2) }} kg</div>
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="border text-center">
                            <div class="small">Total</div>
                            <div class="font-weight-bold"> {{ number_format((($produksi['dataWholeWOFG'] + $produksi['dataWholeNONWOFG'])) , 2) }} kg</div>
                        </div>
                    </div>
                </div>
                <div class="row mt-1">
                    <div class="col pr-1">
                        <div class="border text-center">
                            <div class="small">Selisih</div>
                            @php
                                $selisih_whole = ($produksi['dataWholeWOFG'] - $produksi['dataWholeWOBB']);
                            @endphp
                            <div class="font-weight-bold"> {{ number_format($selisih_whole, 2) }} kg</div>
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="border text-center">
                            <div class="small">Persentase</div>
                            @php
                                $persentase = 0;
                                // $produksi_whole = $produksi['bb_tt_whole'] + $produksi['non_wo_bb_whole'];
                                if($produksi['dataWholeWOBB']>0){
                                    $persentase = number_format(($selisih_whole/$produksi['dataWholeWOBB'] *100) , 2);
                                }
                            @endphp
                            <div class="font-weight-bold">
                                @if($persentase>0)
                                <span class="green">{{$persentase}}% </span> <span class="fa fa-caret-up green"></span>
                                @elseif($persentase<0)
                                <span class="red">{{$persentase}}% </span> <span class="fa fa-caret-down red"></span>
                                @else
                                <span class="blue">{{$persentase}}% </span>
                                @endif
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4 col-lg pl-sm-1 pr-sm-1 pr-lg-3">
        <div class="card">
            <div class="card-header">Frozen</div>
            <div class="card-body p-2">
                <div class="small title-color-wo">Work Order</div>
                <div class="row mb-1">
                    <div class="col pr-1">
                        <div class="border text-center">
                            <div class="small">Bahan baku</div>
                            <div class="font-weight-bold"> {{ $produksi['dataFrozenWOBB'] }} kg</div>
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="border text-center">
                            <div class="small">Produksi</div>
                            <div class="font-weight-bold"> {{ $produksi['dataFrozenWOFG'] }} kg</div>
                        </div>
                    </div>
                </div>
                <div class="small title-color-non-wo mt-2">Non Work Order</div>
                <div class="row mb-1">
                    <div class="col pr-1">
                        <div class="border text-center">
                            <div class="small">Bahan baku</div>
                            <div class="font-weight-bold"> {{ $produksi['dataFrozenNONWOBB'] }} kg</div>
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="border text-center">
                            <div class="small">Produksi</div>
                            <div class="font-weight-bold"> {{ $produksi['dataFrozenNONWOFG'] }} kg</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col pr-1">
                        <div class="border text-center">
                            <div class="small">Total</div>
                            <div class="font-weight-bold"> {{ number_format(($produksi['dataFrozenWOBB'] + $produksi['dataFrozenNONWOBB']) , 2) }} kg</div>
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="border text-center">
                            <div class="small">Total</div>
                            <div class="font-weight-bold"> {{ number_format((($produksi['dataFrozenWOFG'] + $produksi['dataFrozenNONWOFG'])) , 2) }} kg</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col pr-1">
                        <div class="border text-center">
                            <div class="small">Selisih</div>
                            @php
                                $selisih_frozen = ($produksi['dataFrozenWOFG'] - $produksi['dataFrozenWOBB']);
                            @endphp
                            <div class="font-weight-bold"> {{ number_format($selisih_frozen, 2) }} kg</div>
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="border text-center">
                            <div class="small">Persentase</div>
                            @php
                                $persentase = 0;
                                // $produksi_frozen = $produksi['bb_tt_whole'] + $produksi['non_wo_bb_whole'];
                                if($produksi['dataFrozenWOBB']>0){
                                    $persentase = number_format(($selisih_frozen/$produksi['dataFrozenWOBB'] *100), 2);
                                }
                            @endphp
                            <div class="font-weight-bold">
                            @if($persentase>0)
                            <span class="green">{{$persentase}}% </span> <span class="fa fa-caret-up green"></span>
                            @elseif($persentase<0)
                            <span class="red">{{$persentase}}% </span> <span class="fa fa-caret-down red"></span>
                            @else
                            <span class="blue">{{$persentase}}% </span>
                            @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">Summary Produksi Total</div>
    @php

        $total_all_bb = 0;
        $total_all_fg = 0;

        // $total_all_bb = $produksi['bb_tt_frozen'] + $produksi['bb_tt_whole'] + $produksi['bb_tt_marinasi'] + $produksi['bb_tt_parting'] + $produksi['bb_tt_boneless'];
        $total_all_bb = $produksi['dataFrozenWOBB'] + $produksi['dataWholeWOBB'] + $produksi['dataMarinasiWOBB'] + $produksi['dataPartingWOBB'] + $produksi['dataBonelessWOBB'];
        $total_all_fg = $produksi['dataFrozenWOFG'] + $produksi['dataWholeWOFG'] + $produksi['dataMarinasiWOFG'] + $produksi['dataPartingWOFG'] + $produksi['dataBonelessWOFG'];
        // $total_all_fg = $produksi['fg_tt_frozen'] + $produksi['dataWholeWOFG'] + $produksi['fg_tt_marinasi'] + $produksi['fg_tt_parting'] + $produksi['fg_tt_boneless'];
    @endphp
    <div class="card-body p-2">
        <div class="row mb-1">
            <div class="col pr-1">
                <div class="border text-center">
                    <div class="small">Bahan baku</div>
                    <div class="font-weight-bold"> {{ number_format($total_all_bb, 2)}} kg</div>
                </div>
            </div>
            <div class="col px-1">
                <div class="border text-center">
                    <div class="small">Produksi</div>
                    <div class="font-weight-bold"> {{ number_format($total_all_fg, 2) }} kg</div>
                </div>
            </div>
            <div class="col px-1">
                <div class="border text-center">
                    <div class="small">Selisih</div>
                    <div class="font-weight-bold"> {{ number_format(($total_all_bb - $total_all_fg) * -1, 2) }} kg</div>
                </div>
            </div>
            <div class="col pl-1">
                <div class="border text-center">
                    <div class="small">Persentase</div>
                    @if($total_all_bb >0)
                    <div class="font-weight-bold"> {{ number_format((($total_all_bb - $total_all_fg)/$total_all_bb * 100)  * -1, 2) }} %</div>
                    @else
                    <div class="font-weight-bold"> 0 %</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


<div class="card mb-3">
    <div class="card-header">Produksi x Plastik</div>
    <div class="card-body p-2">
        <div class="row">
            <div class="col-sm-4 col-lg mb-2 pr-sm-1">
                <div class="card">
                    <div class="card-header">Boneless</div>
                    <div class="card-body p-2">
                        <div class="row mb-1">
                            <div class="col pr-1">
                                <div class="border text-center">
                                    <div class="small">Qty</div>
                                    <div class="font-weight-bold"> {{$produksi['fg_qty_boneless'] }}</div>
                                </div>
                            </div>
                            <div class="col pl-1">
                                <div class="border text-center">
                                    <div class="small">Plastik</div>
                                    <div class="font-weight-bold">  {{ $produksi['fg_pe_boneless'] }}</div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('dashboard.produksiplastik', ['regu' => 'boneless', 'tanggal_awal' => $tanggal_awal, 'tanggal_akhir' => $tanggal_akhir]) }}" class="rounded-0 btn btn-sm p-0 btn-outline-info btn-block small">Detail</a>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-lg mb-2 px-sm-1">
                <div class="card">
                    <div class="card-header">Parting</div>
                    <div class="card-body p-2">
                        <div class="row mb-1">
                            <div class="col pr-1">
                                <div class="border text-center">
                                    <div class="small">Qty</div>
                                    <div class="font-weight-bold"> {{ $produksi['fg_qty_parting'] }}</div>
                                </div>
                            </div>
                            <div class="col pl-1">
                                <div class="border text-center">
                                    <div class="small">Plastik</div>
                                    <div class="font-weight-bold"> {{ $produksi['fg_pe_parting'] }}</div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('dashboard.produksiplastik', ['regu' => 'parting', 'tanggal_awal' => $tanggal_awal, 'tanggal_akhir' => $tanggal_akhir]) }}" class="rounded-0 btn btn-sm p-0 btn-outline-info btn-block small">Detail</a>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-lg pl-sm-1 pl-lg-0 mb-2 px-lg-1">
                <div class="card">
                    <div class="card-header">Parting M</div>
                    <div class="card-body p-2">
                        <div class="row mb-1">
                            <div class="col pr-1">
                                <div class="border text-center">
                                    <div class="small">Qty</div>
                                    <div class="font-weight-bold"> {{ $produksi['fg_qty_marinasi'] }}</div>
                                </div>
                            </div>
                            <div class="col pl-1">
                                <div class="border text-center">
                                    <div class="small">Plastik</div>
                                    <div class="font-weight-bold"> {{ $produksi['fg_pe_marinasi'] }}</div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('dashboard.produksiplastik', ['regu' => 'marinasi', 'tanggal_awal' => $tanggal_awal, 'tanggal_akhir' => $tanggal_akhir]) }}" class="rounded-0 btn btn-sm p-0 btn-outline-info btn-block small">Detail</a>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-lg pr-sm-1 pr-lg-0 mb-2 px-lg-1">
                <div class="card">
                    <div class="card-header">Whole Chicken</div>
                    <div class="card-body p-2">
                        <div class="row mb-1">
                            <div class="col pr-1">
                                <div class="border text-center">
                                    <div class="small">Qty</div>
                                    <div class="font-weight-bold"> {{ $produksi['fg_qty_whole'] }}</div>
                                </div>
                            </div>
                            <div class="col pl-1">
                                <div class="border text-center">
                                    <div class="small">Plastik</div>
                                    <div class="font-weight-bold"> {{ $produksi['fg_pe_whole'] }}</div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('dashboard.produksiplastik', ['regu' => 'whole', 'tanggal_awal' => $tanggal_awal, 'tanggal_akhir' => $tanggal_akhir]) }}" class="rounded-0 btn btn-sm p-0 btn-outline-info btn-block small">Detail</a>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-lg pl-sm-1 pr-sm-1 pr-lg-3">
                <div class="card">
                    <div class="card-header">Frozen</div>
                    <div class="card-body p-2">
                        <div class="row mb-1">
                            <div class="col pr-1">
                                <div class="border text-center">
                                    <div class="small">Qty</div>
                                    <div class="font-weight-bold"> {{ $produksi['fg_qty_frozen'] }}</div>
                                </div>
                            </div>
                            <div class="col pl-1">
                                <div class="border text-center">
                                    <div class="small">Plastik</div>
                                    <div class="font-weight-bold"> {{ $produksi['fg_pe_frozen'] }}</div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('dashboard.produksiplastik', ['regu' => 'frozen', 'tanggal_awal' => $tanggal_awal, 'tanggal_akhir' => $tanggal_akhir]) }}" class="rounded-0 btn btn-sm p-0 btn-outline-info btn-block small">Detail</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="dashboard-loading-pageLima" style="height: 30px">
    <div class="text-center mb-2 mt-2" style="position: absolute; left: 0; right: 0;">
        <img src="{{asset('loading.gif')}}" style="width: 20px"> Loading ...
    </div>
</div>