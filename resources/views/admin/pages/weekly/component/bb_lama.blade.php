@if($download == true)
    @php
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=export-weekly-bb-lama.xls");
    @endphp
@endif

<div class="table-responsive">
    <div  id="table-bb-lama">
        <table class="default-table table">
            <style>
                .text {
                    mso-number-format:"\@";
                    border:thin solid black;
                }
            </style>
            <thead>
                <tr>
                    <th class="text" rowspan="3">Tanggal</th>
                    <th class="text" colspan="10">Hasil Produksi Bahan Baku Lama</th>
                    <th class="text" colspan="2" rowspan="2">Total</th>
                </tr>
                <tr>
                    <th colspan="2" class="text">WHOLE CHICKEN</th>
                    <th colspan="2" class="text">PARTING</th>
                    <th colspan="2" class="text">PARTING M</th>
                    <th colspan="2" class="text">BONELESS</th>
                    <th colspan="2" class="text">Stock Frozen</th>
                </tr>
                <tr>
                    <th class="text">Ekor/Pcs/Pack</th>
                    <th class="text">Kg</th>
                    <th class="text">Ekor/Pcs/Pack</th>
                    <th class="text">Kg</th>
                    <th class="text">Ekor/Pcs/Pack</th>
                    <th class="text">Kg</th>
                    <th class="text">Ekor/Pcs/Pack</th>
                    <th class="text">Kg</th>
                    <th class="text">Ekor/Pcs/Pack</th>
                    <th class="text">Kg</th>
                    <th class="text">Ekor/Pcs/Pack</th>
                    <th class="text">Kg</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $yield  =   0 ;
                    $qty_wc =   0 ;
                    $bb_wc  =   0 ;
                    $qty_pt =   0 ;
                    $bb_pt  =   0 ;
                    $qty_mr =   0 ;
                    $bb_mr  =   0 ;
                    $qty_bn =   0 ;
                    $bb_bn  =   0 ;
                    $qty_fz =   0 ;
                    $bb_fz  =   0 ;
                    $bb_tt  =   0 ;
                    $qty_tt =   0 ;
                @endphp
                @foreach ($collection as $row)
                    @php
                        $qty_wc     +=  $row['qtywc'];
                        $bb_wc      +=  $row['bbwc'];
                        $qty_pt     +=  $row['qtypt'];
                        $bb_pt      +=  $row['bbpt'];
                        $qty_mr     +=  $row['qtymr'];
                        $bb_mr      +=  $row['bbmr'];
                        $qty_bn     +=  $row['qtybn'];
                        $bb_bn      +=  $row['bbbn'];
                        $qty_fz     +=  $row['qtyfz'];
                        $bb_fz      +=  $row['bbfz'];
                        $qty_tt     +=  $row['qtytt'];
                        $bb_tt      +=  $row['bbtt'];

                    @endphp
                    <tr>
                        <td class="text">{{ date('d/m/Y', strtotime($row['lpah_tanggal_potong'])) }}</td>
                        <td class="text">
                            @if ($download == false)
                            <a href="{{route('weekly.index',['key' => 'hasilKey'])}}" class="hitungWhole" data-target="#modalHasil" data-jenis="lama"
                                data-toggle="modal" data-asaltujuan="gradinggabungan"
                                data-regu="whole" data-tanggal="{{$row['lpah_tanggal_potong']}}"
                                data-backdrop="static" data-keyboard="false" tabindex="-1"
                                aria-hidden="true">
                                    {{ number_format($row['qtywc']) }}
                            </a>
                            @else
                                {{ number_format($row['qtywc']) }}
                            @endif
                        </td>
                        <td class="text">
                            @if ($download == false)
                            <a href="{{route('weekly.index',['key' => 'hasilKey'])}}" class="hitungWhole" data-target="#modalHasil" data-jenis="lama"
                                data-toggle="modal" data-asaltujuan="gradinggabungan"
                                data-regu="whole" data-tanggal="{{$row['lpah_tanggal_potong']}}"
                                data-backdrop="static" data-keyboard="false" tabindex="-1"
                                aria-hidden="true">
                                {{ number_format($row['bbwc'], 1) }}
                            </a>
                            @else
                                {{ number_format($row['bbwc'], 1) }}
                            @endif
                        </td>
                        <td class="text">
                            @if ($download == false)

                            <a href="{{route('weekly.index',['key' => 'hasilKey'])}}" class="hitungParting" data-target="#modalHasil" data-jenis="lama"
                                data-toggle="modal" data-asaltujuan="gradinggabungan"
                                data-regu="parting" data-tanggal="{{$row['lpah_tanggal_potong']}}"
                                data-backdrop="static" data-keyboard="false" tabindex="-1"
                                aria-hidden="true">
                                {{ number_format($row['qtypt']) }}
                            </a>
                            @else
                                {{ number_format($row['qtypt']) }}

                            @endif
                        </td>
                        <td class="text">
                            @if ($download == false)

                            <a href="{{route('weekly.index',['key' => 'hasilKey'])}}" class="hitungParting" data-target="#modalHasil" data-jenis="lama"
                                data-toggle="modal" data-asaltujuan="gradinggabungan"
                                data-regu="parting" data-tanggal="{{$row['lpah_tanggal_potong']}}"
                                data-backdrop="static" data-keyboard="false" tabindex="-1"
                                aria-hidden="true">
                                {{ number_format($row['bbpt'], 1) }}
                            </a>
                            @else
                                {{ number_format($row['bbpt'], 1) }}

                            @endif
                        </td>
                        <td class="text">
                            @if ($download == false)

                            <a href="{{route('weekly.index',['key' => 'hasilKey'])}}" class="hitungMarinasi" data-target="#modalHasil" data-jenis="lama"
                                data-toggle="modal" data-asaltujuan="gradinggabungan"
                                data-regu="marinasi" data-tanggal="{{$row['lpah_tanggal_potong']}}"
                                data-backdrop="static" data-keyboard="false" tabindex="-1"
                                aria-hidden="true">
                                {{ number_format($row['qtymr']) }}
                            </a>
                            @else
                                {{ number_format($row['qtymr']) }}

                            @endif
                        </td>
                        <td class="text">
                            @if ($download == false)

                            <a href="{{route('weekly.index',['key' => 'hasilKey'])}}" class="hitungMarinasi" data-target="#modalHasil" data-jenis="lama"
                                data-toggle="modal" data-asaltujuan="gradinggabungan"
                                data-regu="marinasi" data-tanggal="{{$row['lpah_tanggal_potong']}}"
                                data-backdrop="static" data-keyboard="false" tabindex="-1"
                                aria-hidden="true">
                                {{ number_format($row['bbmr'], 1) }}
                            </a>
                            @else
                                {{ number_format($row['bbmr'], 1) }}

                            @endif
                        </td>
                        <td class="text">
                            @if ($download == false)

                            <a href="{{route('weekly.index',['key' => 'hasilKey'])}}" class="hitungBoneless" data-target="#modalHasil" data-jenis="lama"
                                data-toggle="modal" data-asaltujuan="gradinggabungan"
                                data-regu="boneless" data-tanggal="{{$row['lpah_tanggal_potong']}}"
                                data-backdrop="static" data-keyboard="false" tabindex="-1"
                                aria-hidden="true">
                                {{ number_format($row['qtybn']) }}
                            </a>
                            @else
                                {{ number_format($row['qtybn']) }}

                            @endif
                        </td>
                        <td class="text">
                            @if ($download == false)

                            <a href="{{route('weekly.index',['key' => 'hasilKey'])}}" class="hitungBoneless" data-target="#modalHasil" data-jenis="lama"
                                data-toggle="modal" data-asaltujuan="gradinggabungan"
                                data-regu="boneless" data-tanggal="{{$row['lpah_tanggal_potong']}}"
                                data-backdrop="static" data-keyboard="false" tabindex="-1"
                                aria-hidden="true">
                                {{ number_format($row['bbbn'], 1) }}
                            </a>
                            @else
                                {{ number_format($row['bbbn'], 1) }}

                            @endif
                        </td>
                        <td class="text">
                            @if ($download == false)

                            <a href="{{route('weekly.index',['key' => 'hasilKey'])}}" class="hitungFrozen" data-target="#modalHasil" data-jenis="lama"
                                data-toggle="modal" data-asaltujuan="gradinggabungan"
                                data-regu="frozen" data-tanggal="{{$row['lpah_tanggal_potong']}}"
                                data-backdrop="static" data-keyboard="false" tabindex="-1"
                                aria-hidden="true">
                                {{ number_format($row['qtyfz']) }}
                            </a>
                            @else
                                {{ number_format($row['qtyfz']) }}

                            @endif
                        </td>
                        <td class="text">
                            @if ($download == false)

                            <a href="{{route('weekly.index',['key' => 'hasilKey'])}}" class="hitungFrozen" data-target="#modalHasil" data-jenis="lama"
                                data-toggle="modal" data-asaltujuan="gradinggabungan"
                                data-regu="frozen" data-tanggal="{{$row['lpah_tanggal_potong']}}"
                                data-backdrop="static" data-keyboard="false" tabindex="-1"
                                aria-hidden="true">
                                {{ number_format($row['bbfz'], 1) }}
                            </a>
                            @else
                                {{ number_format($row['bbfz'], 1) }}

                            @endif
                        </td>
                        <td class="text">{{ number_format($row['qtytt']) }}</td>
                        <td class="text">{{ number_format($row['bbtt'], 1) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th class="text">TOTAL</th>
                    <th class="text">{{ number_format($qty_wc) }}</th>
                    <th class="text">{{ number_format($bb_wc, 1) }}</th>
                    <th class="text">{{ number_format($qty_pt) }}</th>
                    <th class="text">{{ number_format($bb_pt, 1) }}</th>
                    <th class="text">{{ number_format($qty_mr) }}</th>
                    <th class="text">{{ number_format($bb_mr, 1) }}</th>
                    <th class="text">{{ number_format($qty_bn) }}</th>
                    <th class="text">{{ number_format($bb_bn, 1) }}</th>
                    <th class="text">{{ number_format($qty_fz) }}</th>
                    <th class="text">{{ number_format($bb_fz, 1) }}</th>
                    <th class="text">{{ number_format($qty_tt) }}</th>
                    <th class="text">{{ number_format($bb_tt, 1) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
    @if ($download == false)
        <div class="form-group">
            <button type="button" class="btn btn-primary mb-2 downloadBBLama"><i class="fa fa-spinner fa-spin spinerloading" style="display:none;"></i> <span id="text">Export</span></button>
        </div>
    @endif

    {{-- modal --}}
    <div class="modal fade" id="modalHasil" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalHasilLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" width="900px">
            <div class="modal-body" id="modalHasilProduksi">
            </div>
        </div>
    </div>
    {{-- end modal --}}

    @if ($download == false)
        <script>
            $(document).ready(function(){
                $('.hitungWhole, .hitungMarinasi , .hitungParting , .hitungBoneless , .hitungFrozen').on("click",function(e) {
                    e.preventDefault();
                        var jenis           = $(this).data('jenis');
                        var asalTujuan      = $(this).data('asaltujuan');
                        var regu            = $(this).data('regu');
                        var tanggal         = $(this).data('tanggal');
                        var href            = $(this).attr('href');

                        $.ajax({
                            url : href,
                            type: "GET",
                            data: {
                                asalTujuan      : asalTujuan,
                                jenis           : jenis,
                                regu            : regu,
                                tanggal         : tanggal,
                            },
                            success: function(data){
                                $('#modalHasilProduksi').html(data);
                            }
                        });

                })
            })
            $(".downloadBBLama").on('click', function () {
                var mulai = "{{ $mulai }}";
                var akhir = "{{ $akhir }}";
                $.ajax({
                    method: "GET",
                    url: "{{ route('weekly.index', ['key' => 'bb_lama']) }}&download=true&mulai="+mulai+"&akhir="+akhir,
                    beforeSend: function() {
                        $(".spinerloading").show();
                        $("#text").text('Downloading...');
                    },
                    success: function (response) {
                        window.location =  "{{ route('weekly.index', ['key' => 'bb_lama']) }}&download=true&mulai="+mulai+"&akhir="+akhir,

                        $("#text").text('Download');
                    $(".spinerloading").hide();
                    }
                });
            });
        </script>
    @endif
</div>
