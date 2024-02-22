@extends('admin.layout.template')

@section('title', 'Laporan Penerimaan Ayam Hidup')

@section('content')
<div class="text-center py-1">
    <b>Laporan Penerimaan Ayam Hidup</b>
</div>

<section class="panel mt-2">
    <div class="card-body">
        <form action="{{ route('laporan.lpah') }}" method="get">
            Pencarian
            <div class="row">
                <div class="col-6">
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif class="form-control change-date" name="tanggal_mulai"
                        value="{{ $tanggal_mulai }}" placeholder="Cari...">
                </div>
                <div class="col-6">
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif class="form-control change-date" name="tanggal_selesai"
                        value="{{ $tanggal_selesai }}" placeholder="Cari...">
                </div>
            </div>
        </form>
    </div>
</section>

<section class="panel">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm default-table">
                <thead>
                    <tr class="text-center">
                        <th rowspan="2">#</th>
                        <th rowspan="2">Tanggal</th>
                        <th rowspan="2">No. PO</th>
                        <th rowspan="2">No. LPAH</th>
                        <th rowspan="2">UKURAN AYAM</th>
                        <th rowspan="2">SUPPLIER</th>
                        <th rowspan="2">MOBIL</th>
                        <th rowspan="2">No. DO</th>
                        <th rowspan="2">Jam Datang</th>
                        <th rowspan="2">Jam Bongkar</th>
                        <th rowspan="2">Jam Selesai</th>
                        <th rowspan="2">DRIVER</th>
                        <th rowspan="2">Jenis Ekspedisi</th>
                        <th rowspan="2">No. MOBIL</th>
                        <th rowspan="2">Tanggal Potong</th>
                        <th colspan="3">TIMBANG KANDANG</th>
                        <th colspan="3">KENYATAAN TERIMA</th>
                        <th colspan="2">SUSUT TIMBANG</th>
                        <th colspan="2">MATI</th>
                        <th colspan="2">PROSENTASE MATI (%)</th>
                    </tr>
                    <tr class="text-center">
                        <th>Ekor/Pcs/Pack</th>
                        <th>Kg</th>
                        <th>Rata2 Kg</th>
                        <th>Ekor</th>
                        <th>Kg</th>
                        <th>Rata2 Kg</th>
                        <th>Kg</th>
                        <th>%</th>
                        <th>Ekor</th>
                        <th>Kg</th>
                        <th>Ekor</th>
                        <th>Kg</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($produksi as $i => $val)

                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $val->prod_tanggal_potong }}</td>
                        <td>{{ $val->prodpur->no_po }}</td>
                        <td>{{ $val->no_lpah }}</td>
                        <td>@if ($val->prodpur->ukuran_ayam == '&lt; 1.1') {{ '<1.1' }} @else {{ $val->prodpur->ukuran_ayam }} @endif</td>
                        <td>{{ $val->prodpur->purcsupp->nama }}</td>
                        <td>{{ $val->no_urut }}</td>
                        <td>{{ $val->no_do }}</td>
                        <td>{{ $val->sc_jam_masuk }}</td>
                        <td>{{ $val->lpah_jam_bongkar }}</td>
                        <td>{{ $val->lpah_jam_selesai }}</td>
                        <td>{{ $val->sc_pengemudi }}</td>
                        <td>{{ $val->po_jenis_ekspedisi }}</td>
                        <td>{{ $val->sc_no_polisi }}</td>
                        <td>{{ $val->prod_tanggal_potong }}</td>
                        <td>{{ number_format($val->sc_ekor_do, 0) }}</td>
                        <td>{{ number_format($val->sc_berat_do, 2) }}</td>
                        <td>{{ $val->sc_rerata_do }}</td>
                        <td>{{ number_format($val->ekoran_seckle, 0) }}</td>
                        <td>{{ number_format($val->lpah_berat_terima, 2) }}</td>
                        <td>{{ $val->lpah_rerata_terima }}</td>
                        <td>{{ number_format($val->lpah_berat_susut, 2) }}</td>
                        <td>{{ $val->lpah_persen_susut }}</td>
                        <td>{{ number_format($val->qc_ekor_ayam_mati, 0) }}</td>
                        <td>{{ $val->qc_berat_ayam_mati }}</td>
                        <td>{{ number_format($val->sc_ekor_do != 0 ? ($val->qc_ekor_ayam_mati / $val->sc_ekor_do) * 100 : 0, 2) }}
                        </td>
                        <td>{{ number_format($val->sc_berat_do != 0 ? ($val->qc_berat_ayam_mati / $val->sc_berat_do) * 100 : 0, 2) }}
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
<style>
    .default-table td {
        min-width: 100px;
    }
</style>

<a href="{{ route('bukubesar.exportlpah') }}?tanggal_mulai={{ $tanggal_mulai }}&tanggal_selesai={{ $tanggal_selesai }}"
    class="btn btn-blue">Export Excel</a>
@stop

@section('footer')
<script>
    $('.change-date').change(function() {
        $(this).closest("form").submit();
    });
</script>
@stop