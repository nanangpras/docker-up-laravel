@extends('admin.layout.template')

@section('title', 'Penyiapan')

@section('content')
<div class="row mb-4">
    <div class="col py-1">
        <a href="{{ route('sync.index') }}"><i class="fa fa-arrow-left"></i> Back</a>
    </div>
    <div class="col-8 py-1 text-center">
        <b class="text-uppercase">TRACING WO 1</b>
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
        <form method="get" action="{{url('admin/wo/wo-1-list')}}">
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif name="tanggal" value="{{ Request::get('tanggal') ?? date('Y-m-d') }}" class="form-control mb-2">
            <button type="submit" class="btn btn-blue">Filter</button>
            <a href="{{ route('wo.wo_1_list', ['key'=>'unduh_wo1'] ) }}&tanggal={{$tanggal}}"
                class="btn btn-outline-warning"><i class="fa fa-download"></i>Unduh</a>
        </form>
    </div>
    <div class="row">
        <div class="table-responsive card-body">
            <div class="table-responsive mt-4">
                <table width="100%" class="table default-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>No. LPAH / DO</th>
                            <th>Supir</th>
                            <th>PO</th>
                            <th>Jam Masuk</th>
                            <th>Operator</th>
                            <th>DO Ekor/Berat</th>
                            <th>Ekor/Berat</th>
                            <th>NoUrut</th>
                            <th>Status</th>
                            <th>#</th>
                            <th>#</th>
                            <th>#</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $row)
                        <tr>
                            <td>{{ $row->prod_tanggal_potong }}</td>
                            <td>{{ $row->prodpur->purcsupp->nama ?? '####' }}<br>{{ $row->no_lpah }}<br>NoDO :
                                {{ $row->no_do }}<br>
                                {{ $row->prodpur->no_po ?? '####' }}</td>
                            <td>{{ $row->sc_pengemudi }}<br>{{ $row->sc_no_polisi }}</td>
                            <td>@if ($row->prodpur->ukuran_ayam == '&lt; 1.1') {{ '<1.1' }} @else {{ $row->prodpur->ukuran_ayam }} @endif<br><span class="text-capitalize">{{$row->po_jenis_ekspedisi }}</span> <br>
                                    {{ $row->prodpur->type_po }}</td>
                            <td>{{ date('d/m/y', strtotime($row->sc_tanggal_masuk ?? '')) }}
                                <br>{{ date('H:i', strtotime($row->sc_jam_masuk ?? '00:00')) }} WIB
                            </td>
                            <td>{{ $row->lpah_user_nama }}</td>
                            <td>{{ number_format($row->sc_ekor_do) }}
                                ekor<br>{{ number_format($row->sc_berat_do, 2) }} Kg <br> Rata :
                                {{ $row->sc_rerata_do }} Kg</td>
                            <td>{{ number_format($row->ekoran_seckle) }} ekor <br>
                                {{ number_format($row->lpah_berat_terima, 2) }} Kg <br> Rata :
                                @if ($row->ekoran_seckle > 0) {{ number_format($row->lpah_berat_terima /
                                ($row->ekoran_seckle ?? '1'), 2) }} Kg @endif</td>
                            <td>{{ $row->no_urut }}</td>
                            <td>
                                @if ($row->sc_status == '0')
                                <span class="status status-danger">Dibatalkan</span>
                                @else
                                @if ($row->lpah_status == 1)
                                <span class="status status-success">Selesai</span>
                                @if ($row->lpah_netsuite_status == 1)
                                <br><span class="status status-danger">NSTerkirim</span>
                                @endif
                                @elseif($row->lpah_status==2)
                                <span class="status status-other">Proses</span>
                                @elseif($row->lpah_status==3)
                                <span class="status status-warning">Checker</span>
                                @else
                                <span class="status status-info">Pending</span>
                                @endif

                                @if($row->prod_pending=="1")
                                <br><span class="status status-danger">POTunda</span>
                                @endif

                                @endif
                            </td>
                            <td>
                                @if ($row->sc_status != '0')
                                @if ($row->lpah_status == null)
                                <form action="{{ route('lpah.store') }}" method="POST">
                                    @csrf <input type="hidden" name="x_code" value="{{ $row->id }}">
                                    <button class="btn btn-sm btn-primary btn-rounded mb-1">Proses</button>
                                </form>
                                @endif
                                @if ($row->lpah_status == 1 || $row->lpah_status == 3)
                                <a href="{{ route('lpah.show', $row->id) }}"
                                    class="btn btn-sm btn-warning btn-rounded mb-1">Detail</a>
                                @endif
                                @if ($row->lpah_status == 2)

                                <div style="display:inline-flex">
                                    <a href="{{ route('lpah.show', $row->id) }}"
                                        class="btn btn-sm btn-success btn-rounded mb-1">Edit</a>
                                    &nbsp;

                                    <form action="{{ route('lpah.store', ['key' => 'simpan']) }}" method="POST">
                                        @csrf <input type="hidden" name="x_code" value="{{ $row->id }}">
                                        <button class="btn btn-sm btn-danger btn-rounded mb-1">Simpan</button>
                                    </form>
                                </div>

                                @endif

                                @if ($row->evis_status != null)
                                @if (User::setIjin(33))
                                <a href="{{ route('checker.produksi', $row->id) }}"
                                    class="btn btn-sm btn-info btn-rounded mb-1">NS Checker</a>
                                @endif
                                @endif
                                @endif
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                        @php
                        $ns = \App\Models\Netsuite::where('tabel', 'productions')->where('tabel_id', $row->id)->get();
                        @endphp
                        @foreach ($ns as $i => $n)
                        @include('admin.pages.log.netsuite_one', ($netsuite = $n))
                        @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

@stop

@section('footer')

@endsection