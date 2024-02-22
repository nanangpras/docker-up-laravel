@extends('admin.layout.template')

@section('title', 'Antemortem & Postmortem')

@section('content')
<div class="my-4 text-uppercase text-center"><b>Quality Control</b></div>
<section class="panel caritanggalqcretur">
    <div class="card-body">
        <form action="{{ route('qc.index') }}" method="GET">
            <div class="row">
                <input type="hidden" name="navigate" id="navigate" value="{{ $hash }}">
                <div class="col-md-4 col-sm-4 col-6">
                    <div class="form-group">
                        Tanggal Awal
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif id="tanggalawal" name="tanggalawal" class="form-control change-date"
                            value="{{ $tanggalawal }}" id="pencarian" placeholder="Cari...." autocomplete="off">
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 col-6">
                    <div class="form-group">
                        Tanggal Akhir
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif id="tanggalakhir" name="tanggalakhir"
                            class="form-control change-date" value="{{ $tanggalakhir }}" id="pencarian"
                            placeholder="Cari...." autocomplete="off">
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
<ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
    <li class="nav-item">
        <a class="nav-link tab-link" id="custom-tabs-three-qc-tab" data-toggle="pill" href="#custom-tabs-three-qc"
            role="tab" aria-controls="custom-tabs-three-qc" aria-selected="true">
            QC
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link tab-link" id="custom-tabs-three-returpo-tab" data-toggle="pill"
            href="#custom-tabs-three-returpo" role="tab" aria-controls="custom-tabs-three-returpo"
            aria-selected="false">
            RETUR PURCHASE
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link tab-link" id="custom-tabs-three-order-tab" data-toggle="pill" href="#custom-tabs-three-order"
            role="tab" aria-controls="custom-tabs-three-order" aria-selected="false">
            REKAP ORDER
        </a>
    </li>
</ul>
<section class="panel">
    <div class="card-body card-tabs">
        <div class="tab-content" id="custom-tabs-three-tabContent">
            <div class="tab-pane fade show active" id="custom-tabs-three-qc" role="tabpanel"
                aria-labelledby="custom-tabs-three-qc-tab">
                <div>
                    <section class="panel">
                        <div class="card-body">


                            <a href="{{ route('qc.siap_kirim_export') }}" class="btn btn-primary mb-1"> Laporan Siap
                                Kirim</a>
                            <a href="{{ route('laporan.qc') }}" class="btn btn-primary mb-1"> Laporan QC</a>
                            <a href="{{ route('laporan.qc-retur') }}" class="btn btn-primary mb-1"> Laporan Retur</a>
                            <a href="{{ route('laporan.qc-kualitas-karkas') }}" class="btn btn-primary mb-1"> Laporan
                                Kualitas Karkas</a>

                            <table class="table default-table" width="100%" id="qcTable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Vendor</th>
                                        <th>Kandang</th>
                                        <th>NoUrut</th>
                                        <th>Jam</th>
                                        <th>DO</th>
                                        <th>Sakit/Mati</th>
                                        <th>Uniformity</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $i => $row)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ date('d/m/y', strtotime($row->tanggal_potong)) }}<br>{{ $row->sc_pengemudi }}</td>
                                        <td>{{ $row->prodpur->purcsupp->nama }}
                                            @if($row->prodpur->tanggal_potong!=$row->prod_tanggal_potong)
                                            <br><span class="status status-info">MOBIL LAMA</span>
                                            @endif
                                        </td>
                                        <td>{{ $row->sc_nama_kandang }}<br>@if ($row->prodpur->ukuran_ayam == '&lt;
                                            1.1') {{ '<1.1' }} @else {{ $row->prodpur->ukuran_ayam }} @endif</td>
                                        <td>{{ $row->no_urut }}</td>
                                        <td>{{ date('H:i', strtotime($row->sc_jam_masuk)) }} WIB <br>{{ date('H:i',
                                            strtotime($row->lpah_jam_bongkar)) ?? '-' }} WIB</td>
                                        <td>{{ number_format($row->sc_ekor_do) }}Ekr<br>
                                            {{ number_format($row->sc_berat_do, 2) }}Kg</td>
                                        <td>Sakit : {{ number_format($row->antem->ayam_sakit ?? 0, 0) }}Ekr<br>
                                            Mati : {{ number_format($row->antem->ayam_mati ?? 0, 0) }}Ekr</td>
                                        <td class="text-right">
                                            @php
                                            $total_sample = $row->qc_over+$row->qc_uniform+$row->qc_under;
                                            @endphp
                                            @if($total_sample>0)
                                            <div class="row">
                                                <div class="col border-right">
                                                    {{$row->qc_over ?? "0"}}<br>
                                                    {{$row->qc_uniform ?? "0"}}<br>
                                                    {{$row->qc_under ?? "0"}}
                                                </div>
                                                <div class="col">
                                                    <span class="red">{{number_format($row->qc_over/$total_sample*100,
                                                        2)}}% </span><br>
                                                    <span
                                                        class="green">{{number_format($row->qc_uniform/$total_sample*100,
                                                        2)}}%</span><br>
                                                    <span
                                                        class="orange">{{number_format($row->qc_under/$total_sample*100,
                                                        2)}}%</span>
                                                </div>
                                            </div>
                                            <div class="border-bottom"></div>
                                            <b>Sample : {{($total_sample)}}</b>
                                            @endif
                                        </td>
                                        <td class="text-center">@php echo $row->aksi_qc ; @endphp</td>
                                        <td class="text-center">
                                            @if (Antemortem::where('production_id', $row->id)->count() > 0)
                                            <div style="width:120px">
                                                <a href="{{ route('qc.show', $row->id) }}"
                                                    class="btn btn-success btn-sm mb-1">Detail</a>
                                                <a href="{{ route('qc.nekropsi_show', $row->id) }}"
                                                    class="btn btn-sm mb-1 btn-{{ $row->nekrop ? 'info' : 'warning' }}">{{ $row->nekrop ? 'Update Nekprosi' : 'Nekropsi' }}</a>
                                            </div>
                                            @else
                                            <form action="{{ route('qc.update') }}" method="post">
                                                @csrf <input type="hidden" name="x_code" value="{{ $row->id }}">
                                                <button type="submit"
                                                    class="btn btn-primary mb-1 btn-sm">Proses</button>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>
                <div id="lpah"></div>
            </div>

            <div class="tab-pane fade" id="custom-tabs-three-returpo" role="tabpanel"
                aria-labelledby="custom-tabs-three-returpo-tab">
                <div>@include('admin.pages.laporan.accounting.purchasing')</div>
            </div>
            <div class="tab-pane fade" id="custom-tabs-three-order" role="tabpanel"
                aria-labelledby="custom-tabs-three-order-tab">
                <div>@include('admin.pages.menu_order.rekap_order')</div>
            </div>
        </div>
    </div>
</section>
{{-- <section class="panel">
    <div class="card-body">
        <form action="{{ route('qc.index') }}" method="GET">
            <div class="row">
                <div class="col-md-4 col-sm-4 col-6">
                    <div class="form-group">
                        Tanggal Awal
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif id="tanggalawal" name="tanggalawal" class="form-control change-date"
                            value="{{ $tanggalawal }}" id="pencarian" placeholder="Cari...." autocomplete="off">
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 col-6">
                    <div class="form-group">
                        Tanggal Akhir
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif id="tanggalakhir" name="tanggalakhir"
                            class="form-control change-date" value="{{ $tanggalakhir }}" id="pencarian"
                            placeholder="Cari...." autocomplete="off">
                    </div>
                </div>
            </div>
        </form>

        <a href="{{ route('qc.siap_kirim_export') }}" class="btn btn-primary mb-1"> Laporan Siap Kirim</a>
        <a href="{{ route('laporan.qc') }}" class="btn btn-primary mb-1"> Laporan QC</a>
        <a href="{{ route('laporan.qc-retur') }}" class="btn btn-primary mb-1"> Laporan Retur</a>

        <table class="table default-table" width="100%" id="qcTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Vendor</th>
                    <th>Kandang</th>
                    <th>NoUrut</th>
                    <th>Jam</th>
                    <th>DO</th>
                    <th>Sakit/Mati</th>
                    <th>Uniformity</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $i => $row)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ date('d/m/y', strtotime($row->tanggal_potong)) }}<br>{{ $row->sc_pengemudi }}</td>
                    <td>{{ $row->prodpur->purcsupp->nama }}</td>
                    <td>{{ $row->sc_nama_kandang }}<br>{{ $row->prodpur->ukuran_ayam }}</td>
                    <td>{{ $row->no_urut }}</td>
                    <td>{{ date('H:i', strtotime($row->sc_jam_masuk)) }} WIB <br>{{ date('H:i',
                        strtotime($row->lpah_jam_bongkar)) ?? '-' }} WIB</td>
                    <td>{{ number_format($row->sc_ekor_do) }}Ekr<br>
                        {{ number_format($row->sc_berat_do, 2) }}Kg</td>
                    <td>Sakit : {{ number_format($row->antem->ayam_sakit ?? 0, 0) }}Ekr<br>
                        Mati : {{ number_format($row->antem->ayam_mati ?? 0, 0) }}Ekr</td>
                    <td class="text-right">
                        @php
                        $total_sample = $row->qc_over+$row->qc_uniform+$row->qc_under;
                        @endphp
                        @if($total_sample>0)
                        <div class="row">
                            <div class="col border-right">
                                {{$row->qc_over ?? "0"}}<br>
                                {{$row->qc_uniform ?? "0"}}<br>
                                {{$row->qc_under ?? "0"}}
                            </div>
                            <div class="col">
                                <span class="red">{{number_format($row->qc_over/$total_sample*100, 2)}}% </span><br>
                                <span class="green">{{number_format($row->qc_uniform/$total_sample*100, 2)}}%</span><br>
                                <span class="orange">{{number_format($row->qc_under/$total_sample*100, 2)}}%</span>
                            </div>
                        </div>
                        <div class="border-bottom"></div>
                        <b>Sample : {{($total_sample)}}</b>
                        @endif
                    </td>
                    <td class="text-center">@php echo $row->aksi_qc ; @endphp</td>
                    <td class="text-center">
                        @if (Antemortem::where('production_id', $row->id)->count() > 0)
                        <div style="width:120px">
                            <a href="{{ route('qc.show', $row->id) }}" class="btn btn-success btn-sm mb-1">Detail</a>
                            <a href="{{ route('qc.nekropsi_show', $row->id) }}"
                                class="btn btn-sm mb-1 btn-{{ $row->nekrop ? 'info' : 'warning' }}">{{ $row->nekrop ?
                                'Update Nekprosi' : 'Nekropsi' }}</a>
                        </div>
                        @else
                        <form action="{{ route('qc.update') }}" method="post">
                            @csrf <input type="hidden" name="x_code" value="{{ $row->id }}">
                            <button type="submit" class="btn btn-primary mb-1 btn-sm">Proses</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section> --}}



@stop
@section('header')
<link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css" />
@stop

@section('footer')
<script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script>
<script>
    var hash = $('#navigate').val();
    var href = window.location.href;
    let linkload        =   $('.tab-link.active').attr('aria-controls');

    deafultPage();

    function deafultPage() {
        if (hash == undefined || hash == "") {
            hash =  "custom-tabs-three-qc";
            console.log(hash)
        }

        $('#' + hash + '-tab').addClass('active').siblings().removeClass('active');
        $('#' + hash).addClass('active show').siblings().removeClass('active show');
    }

    $('.tab-link').click(function(e) {
        e.preventDefault();
        status = $(this).attr('aria-controls');
        window.location.hash = status;
        href = window.location.href;
        // console.log(status);
        $('#navigate').val(status);
        if(status == "custom-tabs-three-order"){
            // $('#qcTable').DataTable().ajax.reload();
            $('.caritanggalqcretur').hide();
        } else {
            $('.caritanggalqcretur').show();
        }
    }); 
    
        var tanggalawal          =   $('#tanggalawal').val();
        var tanggalakhir         =   $('#tanggalakhir').val();
        $('#navigate').val($('.tab-link').attr('aria-controls'));
        $('.change-date').change(function() {
            tanggalawal          =   $('#tanggalawal').val();
            tanggalakhir         =   $('#tanggalakhir').val();
            // console.log($('.tab-link.active').attr('aria-controls'))
            linkload        =   $('.tab-link.active').attr('aria-controls');
            $('#navigate').val(linkload);
            // console.log($('#navigate').val())
            $(this).closest("form").submit();
            
            $('#lpah').load("{{ route('qc.lpah') }}?tanggal_potong_awal=" + tanggalawal + "&tanggal_potong_akhir=" + tanggalakhir);

        });

        $('#lpah').load("{{ route('qc.lpah') }}?tanggal_potong_awal=" + tanggalawal + "&tanggal_potong_akhir=" + tanggalakhir) ;

        console.log("{{ route('qc.lpah') }}?tanggal_potong_awal=" + tanggalawal + "&tanggal_potong_akhir=" + tanggalakhir)

        $(document).ready(function() {

            $('#qcTable').DataTable({
                "bInfo": false,
                responsive: true,
                scrollY:        500,
                scrollX:        true,
                scrollCollapse: true,
                paging:         false,
            });

            $('a[data-toggle="pill"]').on('shown.bs.tab', function(e){
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
        } );
</script>
@stop