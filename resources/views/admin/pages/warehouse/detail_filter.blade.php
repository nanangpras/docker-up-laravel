@extends('admin.layout.template')

@section('title', 'Detail Filter')

@section('content')
@php
    function tgl_indo($tgl)
{
    $tanggal = substr($tgl, 8, 2);
    $bulan = getBulan(substr($tgl, 5, 2));
    $tahun = substr($tgl, 0, 4);
    return $tanggal . ' ' . $bulan . ' ' . $tahun;
}

function getBulan($bln)
{
    switch ($bln) {
            case 1:
                return 'Januari';
                break;
            case 2:
                return 'Februari';
                break;
            case 3:
                return 'Maret';
                break;
            case 4:
                return 'April';
                break;
            case 5:
                return 'Mei';
                break;
            case 6:
                return 'Juni';
                break;
            case 7:
                return 'Juli';
                break;
            case 8:
                return 'Agustus';
                break;
            case 9:
                return 'September';
                break;
            case 10:
                return 'Oktober';
                break;
            case 11:
                return 'November';
                break;
            case 12:
                return 'Desember';
                break;
    }
} 
$period = new DatePeriod(new DateTime($data[0]->production_date ?? ''), new DateInterval('P1D'), new DateTime($tanggal .'+1 day'));
@endphp
    <div class="row mb-4">
        <div class="col">
            <a href="{{ route('warehouse.filter') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
                Back</a>
        </div>
        <div class="col text-center">
            <b>DETAIL WAREHOUSE FILTER</b>
        </div>
        <div class="col"></div>
    </div>

    <section class="panel">
        <div class="card-body">
            <b>Detail</b>
            <div class="table-responsive mb-4">
                <table width="100%" id="" class="table default-table">
                    <thead>
                            <tr>
                                @foreach ($period as $date)
                                <th colspan="3"" class="text-center">{{ tgl_indo($date->format('Y-m-d')) }}</th>
                                @endforeach
                            </tr>
                    </thead>
                    <tbody>
                        <tr class="text-center">
                            @foreach($period as $date)
                            <th>Masuk</th>
                            <th>Keluar</th>
                            <th>Total</th>
                            @endforeach
                        </tr>
                        <tr class="text-center">
                            @php
                            $p = 0;
                            @endphp
                                @foreach ($period as $date)
                                <th>{{ $datamasuk =  Product_gudang::detailfilter($date->format('Y-m-d'), 'masuk', $nama,$konsumen,$lokasi,$kemasan,$subitem,$customerid) }}</th>
                                <th>{{ $datakeluar = Product_gudang::detailfilter($date->format('Y-m-d'), 'keluar', $nama,$konsumen,$lokasi,$kemasan,$subitem,$customerid) }}</th>
                                <th>{{ $p += ($datamasuk - $datakeluar) }}</th>
                                @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@stop

