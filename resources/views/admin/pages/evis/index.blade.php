@extends('admin.layout.template')

@section('title', 'Data Proses Evis')

@section('content')
<div class="py-4 text-center text-uppercase"><b>DATA PROSES EVIS</b></div>

<div class="row mb-3">
    <div class="col-md-3 col-6 pr-md-1">
        <div class="form-group">
            <!-- <label></label> -->
            <a href="{{ route('evis.summary') }}" class="btn-block btn btn-primary">SUMMARY</a>
        </div>
    </div>
    <div class="col-md-3 col-6 px-md-1">
        <div class="form-group">
            <!-- <label></label> -->
            <a href="{{ route('evis.peruntukan') }}" class="btn-block btn btn-primary">TIMBANG PRODUKSI</a>
        </div>
    </div>
    <div class="col-md-3 col-6 px-md-1">
        <div class="form-group">
            <!-- <label></label> -->
            <a href="{{ route('evis.laporan') }}" class="btn-block btn btn-primary">LAPORAN</a>
        </div>
    </div>
    {{-- @if (env('NET_SUBSIDIARY', 'EBA') == 'EBA') --}}
    <div class="col-md-3 col-6 pl-md-1">
        <div class="form-group">
            <!-- <label></label> -->
            <a href="{{ route('evis.inputorder') }}" class="btn-block btn btn-primary">INPUT BY ORDER</a>
        </div>
    </div>
    {{-- @endif --}}
    <div class="col mb-3">
        <!-- <label></label> -->
        <a href="{{ route('index.thawing') }}" class="btn btn-green btn-block">Request Thawing</a>
    </div>
</div>

<section class="panel">
    <div class="card-body">
        <form action="{{ route('evis.index') }}" method="GET">
            <div class="row">
                <div class="col-md-4 col-sm-4 col-6">
                    <div class="form-group">
                        Tanggal Awal
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif name="tanggalawal" class="form-control change-date"
                            value="{{ $tanggalawal }}" id="pencarian" placeholder="Cari...." autocomplete="off">
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 col-6">
                    <div class="form-group">
                        Tanggal Akhir
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif name="tanggalakhir" class="form-control change-date"
                            value="{{ $tanggalakhir }}" id="pencarian" placeholder="Cari...." autocomplete="off">
                    </div>
                </div>
            </div>
        </form>

    </div>
</section>

<section class="panel">
    <div class="card-body">
        <table width="100%" id="evisTable" class="table default-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Supplier</th>
                    <th>No LPAH</th>
                    <th>Kandang</th>
                    <th>Sopir</th>
                    <th>Operator</th>
                    <th>Qty</th>
                    <th>Berat</th>
                    <th>Keterangan Yield</th>
                    <th>No Urut</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($evis as $row)
                    @php 
                        $cekLpahStatus = \App\Models\Production::cekLpahStatus($row->id,$row->sc_tanggal_masuk);
                    @endphp
                    @if($cekLpahStatus == 'OK' || $cekLpahStatus != '2')
                        @php
                        $qty        = 0 ;
                        $berat      = 0 ;
                        @endphp
                        @foreach ($row->prodevis as $list)
                        @php
                        $qty        += $list->total_item ;
                        $berat      += $list->berat_item ;
                        @endphp
                        @endforeach
                        <tr>
                            <td>{{ $row->lpah_tanggal_potong }}</td>
                            <td>{{ $row->prodpur->purcsupp->nama }}
                                @if($row->prodpur->tanggal_potong!=$row->prod_tanggal_potong)
                                <br><span class="status status-info">MOBIL LAMA</span>
                                @endif
                            </td>
                            <td>{{ $row->no_lpah }}</td>
                            <td>{{ $row->sc_nama_kandang ?? '####' }}</td>
                            <td>{{ $row->sc_pengemudi }}</td>
                            <td>{{ $row->evis_user_name }}</td>
                            <td>{{ $qty }}</td>
                            <td>{{ $berat }}</td>
                            <td>{{ $row->keterangan_benchmark ?? '-' }}</td>
                            <td>{{ $row->no_urut }}</td>
                            <td>
                                @if($row->evis_status==1)
                                <span class="status status-success">Selesai</span>
                                @if($row->wo_netsuite_status==1)
                                <br><span class="status status-danger">NSTerkirim</span>
                                @endif
                                @elseif($row->evis_status==2)
                                <span class="status status-other">Proses</span>
                                @elseif($row->evis_status==3)
                                <span class="status status-warning">Checker</span>
                                @else
                                <span class="status status-info">Pending</span>
                                @endif

                                @if($row->prod_pending=="1")
                                <br><span class="status status-danger">POTunda</span>
                                @endif
                            </td>
                            <td style="width: 130px">

                                @if ($row->evis_status == null)
                                <form action="{{ route('evis.store') }}" method="POST">
                                    @csrf <input type="hidden" name="x_code" value="{{ $row->id }}">
                                    <button class="btn btn-sm btn-primary btn-rounded">Proses</button>
                                </form>
                                @endif

                                @if ($row->evis_status == 1)
                                <a href="{{ route('evis.show', $row->id) }}"
                                    class="btn btn-sm btn-warning btn-rounded">Detail</a>
                                @endif

                                @if ($row->evis_status == 2)
                                <div style="display:inline-flex">
                                    <a href="{{ route('evis.show', $row->id) }}"
                                        class="btn btn-sm btn-success btn-rounded">Update</a>
                                    &nbsp
                                    <form action="{{ route('evis.update', $row->id) }}" method="POST">
                                        @csrf @method('patch')
                                        <button type="submit" class="btn-sm btn btn-danger btn-rounded">Simpan</button>
                                    </form>
                                </div>
                                @endif

                                @if ($row->evis_status == 3)
                                <a href="{{ route('evis.show', $row->id) }}"
                                    class="btn btn-sm btn-warning btn-rounded float-left mr-2">Detail</a>
                                <form action="{{ route('evis.update', $row->id) }}" method="POST">
                                    @csrf @method('patch') <input type="hidden" name="key" value="send">
                                    <button type="submit" class="btn btn-sm btn-dark btn-rounded float-left">Selesaikan</button>
                                </form>
                                @endif

                                @if ($row->evis_status != null)
                                <a href="{{ route('checker.produksi', $row->id) }}" class="btn btn-sm btn-info btn-rounded">NS
                                    Checker</a>
                                @endif

                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
</section>

@stop

@section('header')
<link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css" />
@stop

@section('footer')
<script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script>
<script>
    $('.change-date').change(function() {
            var tanggalawal         =   $('#tanggalawal').val();
            var tanggalakhir        =   $('#tanggalakhir').val();
            $(this).closest("form").submit();
        });

        $(document).ready(function() {
            $('#evisTable').DataTable({
                "bInfo"         : false,
                responsive      : true,
                scrollY         : 500,
                scrollX         : true,
                scrollCollapse  : true,
                paging          : false,
                searching       : false
            });

            $('a[data-toggle="pill"]').on('shown.bs.tab', function(e){
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
        } );
</script>
@stop