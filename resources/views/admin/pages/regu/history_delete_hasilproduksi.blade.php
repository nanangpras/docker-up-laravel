@extends('admin.layout.template')

@section('title', 'Produksi Kepala Regu')

@section('content')

    <div class="row mb-4">
        <div class="col"></div>
        <div class="col text-center py-2">
            <b>HISTORY DELETE HASIL PRODUKSI</b>
        </div>
        <div class="col"></div>
    </div>

    <section class="panel">
        <div class="card-body">

            <div class="section">
                <div class="row">
                    <div class="col-lg-12 pr-lg-1">
                        <table class="table default-table table-small">
                            <thead>
                                <tr>
                                    <th>Hasil Produksi</th>
                                    <th>Tanggal Delete</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Ekor/Pcs/Pack</th>
                                    <th>Berat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($history as $item)
                                    <tr>
                                        <td>
                                            {{ $item->item->nama }}
                                            @if ($item->kategori == '1')
                                                <span class="status status-danger">[ABF]</span>
                                            @elseif($item->kategori == '2')
                                                <span class="status status-warning">[EKSPEDISI]</span>
                                            @elseif($item->kategori == '3')
                                                <span class="status status-warning">[TITIP CS]</span>
                                            @else
                                                <span class="status status-info">[CHILLER]</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->deleted_at }}</td>
                                        <td>{{ $item->chiller->tanggal_produksi ?? '' }}</td>
                                        <td>{{ number_format($item->qty) ?? '' }}</td>
                                        <td>{{ number_format($item->berat) ?? '' }} kg</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection
