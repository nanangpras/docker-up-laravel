@extends('admin.layout.template')

@section('title', 'Laporan Purhasing')

@section('content')
<div class="row mb-4">
    <div class="col">
        <a href="{{ route('dashboard') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
            Back</a>
    </div>
    <div class="col text-center">
        <b>Laporan Purhasing</b>
    </div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">
        <b>Pencarian Bedasarkan Tanggal</b>
        <form method="get" action="{{url('admin/laporan/purchasing')}}">
            <div class="row mt-2">
                <div class="col">
                    <div class="form-group">
                        Tanggal Mulai
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif name="mulai" value="{{ $mulai }}"
                            class="form-control form-control-sm" required>
                    </div>
                </div>

                <div class="col">
                    <div class="form-group">
                        Tanggal Selesai
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif name="selesai" value="{{ $selesai }}"
                            class="form-control form-control-sm" required>
                    </div>
                </div>

                <div class="col">
                    &nbsp;
                    <button type="submit" class="btn btn-primary btn-block">Cari</button>
                </div>
            </div>
        </form>
    </div>
</section>

<div class="row">
    <div class="col">
        <div class="card mb-2">
            <div class="card-body">
                <div class="small">Jumlah PO</div>
                {{ $hasil['jumlah_po'] }}
            </div>
        </div>
        <div class="card mb-2">
            <div class="card-body">
                <div class="small">Kirim</div>
                {{ $hasil['kirim'] }}
            </div>
        </div>
        <div class="card mb-2">
            <div class="card-body">
                <div class="small">Tangkap</div>
                {{ $hasil['tangkap'] }}
            </div>
        </div>
        <div class="card mb-2">
            <div class="card-body">
                <div class="small">Berat Ayam</div>
                {{ $hasil['berat_ayam'] }}
            </div>
        </div>
        <div class="card mb-2">
            <div class="card-body">
                <div class="small">Jumlah Ayam</div>
                {{ $hasil['jumlah_ayam'] }}
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card mb-4">
            <div class="card-body">
                <div class="mb-2"><b>Top 5 Supplier</b></div>
                <table class="table table-sm">
                    <tbody>
                        @foreach ($top5 as $row)
                        <tr>
                            <td>{{ $row->purcsupp->nama }}</td>
                            <td>{{ $row->supplier }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="mb-2"><b>Ukuran Ayam</b></div>
                <table class="table table-sm">
                    <tbody>
                        {{-- @if ($hasil['uk8_10'] > 0)
                        <tr>
                            <td>0.8 - 1.0</td>
                            <td>{{ $hasil['uk8_10'] }}</td>
                        </tr>
                        @endif

                        @if ($hasil['uk10_12'] > 0)
                        <tr>
                            <td>1.0 - 1.2</td>
                            <td>{{ $hasil['uk10_12'] }}</td>
                        </tr>
                        @endif


                        @if ($hasil['uk12_14'] > 0)
                        <tr>
                            <td>1.2 - 1.4</td>
                            <td>{{ $hasil['uk12_14'] }}</td>
                        </tr>
                        @endif

                        @if ($hasil['uk12_15'] > 0)
                        <tr>
                            <td>1.2 - 1.5</td>
                            <td>{{ $hasil['uk12_15'] }}</td>
                        </tr>
                        @endif

                        @if ($hasil['uk13_15'] > 0)
                        <tr>
                            <td>1.3 - 1.5</td>
                            <td>{{ $hasil['uk13_15'] }}</td>
                        </tr>
                        @endif

                        @if ($hasil['uk13_16'] > 0)
                        <tr>
                            <td>1.3 - 1.6</td>
                            <td>{{ $hasil['uk13_16'] }}</td>
                        </tr>

                        @endif

                        @if ($hasil['uk14_16'] > 0)
                        <tr>
                            <td>1.4 - 1.6</td>
                            <td>{{ $hasil['uk14_16'] }}</td>
                        </tr>
                        @endif

                        @if ($hasil['uk14_17'] > 0)
                        <tr>
                            <td>1.4 - 1.7</td>
                            <td>{{ $hasil['uk14_17'] }}</td>
                        </tr>
                        @endif

                        @if ($hasil['uk15_17'] > 0)
                        <tr>
                            <td>1.5 - 1.7</td>
                            <td>{{ $hasil['uk15_17'] }}</td>
                        </tr>
                        @endif

                        @if ($hasil['uk15_18'] > 0)
                        <tr>
                            <td>1.5 - 1.8</td>
                            <td>{{ $hasil['uk15_18'] }}</td>
                        </tr>
                        @endif

                        @if ($hasil['uk16_18'] > 0)
                        <tr>
                            <td>1.6 - 1.8</td>
                            <td>{{ $hasil['uk16_18'] }}</td>
                        </tr>
                        @endif

                        @if ($hasil['uk18_20'] > 0)
                        <tr>
                            <td>1.8 - 2.0</td>
                            <td>{{ $hasil['uk18_20'] }}</td>
                        </tr>
                        @endif

                        @if ($hasil['uk20_22'] > 0)
                        <tr>
                            <td>2.0 - 2.2</td>
                            <td>{{ $hasil['uk20_22'] }}</td>
                        </tr>
                        @endif --}}

                        @foreach ($hitungEkoranAyam as $key => $row)
                        <tr>
                            <td>{{ $row }}</td>
                            <td>{{ number_format($key) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card mb-4">
            <div class="card-body">
                <div class="mb-2"><b>Jenis Ayam</b></div>
                <table class="table table-sm">
                    <tbody>
                        <tr>
                            <td>Maklon</td>
                            <td>{{ $hasil['maklon'] }}</td>
                        </tr>
                        <tr>
                            <td>Broiler</td>
                            <td>{{ $hasil['broiler'] }}</td>
                        </tr>
                        <tr>
                            <td>Pejantan</td>
                            <td>{{ $hasil['pejantan'] }}</td>
                        </tr>
                        <tr>
                            <td>Kampung</td>
                            <td>{{ $hasil['kampung'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="mb-2"><b>Detail Susut</b></div>
                <table class="table default-table">
                    <thead>
                        <tr>
                            <th>Jenis Ekspedisi</th>
                            <th>Nama Sopir</th>
                            <th>Nama Kandang</th>
                            <th>Ekor Ayam Mati</th>
                            <th>%Ekor Ayam Mati</th>
                            <th>%Susut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($susut as $row)
                        <tr>
                            <td class="text-capitalize">{{ $row->po_jenis_ekspedisi }}</td>
                            <td>{{ $row->sc_pengemudi }}</td>
                            <td>{{ $row->sc_nama_kandang }}</td>
                            <td>{{ $row->qc_ekor_ayam_mati ?? 0 }}</td>
                            <td>{{ ($row->qc_persen_ayam_mati) ?? 0 }}</td>
                            <td>{{ ($row->lpah_persen_susut) ?? 0 }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


@stop