@extends('admin.layout.template')

@section('title', 'Produksi Kepala Regu')

@section('content')

    <div class="row mb-4">
        <div class="col"></div>
        <div class="col text-center py-2">
            <b>HISTORY DELETE HASIL BAHAN BAKU</b>
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
                                    <th>Bahan Baku</th>
                                    <th>Tanggal Delete</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Asal</th>
                                    <th>Keterangan</th>
                                    <th>Ekor/Pcs/Pack</th>
                                    <th>Berat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($history as $item)
                                    <tr>
                                        <td>{{ $item->item->nama }}</td>
                                        <td>{{ $item->deleted_at }}</td>
                                        <td>{{ $item->chiller->tanggal_produksi ?? '' }}</td>
                                        <td>{{ $item->chiller->tujuan }}</td>
                                        <td>{{ $item->catatan ?? '' }}</td>
                                        <td>{{ $item->qty ?? '' }}</td>
                                        <td>{{ number_format($item->berat, 2) ?? '' }}</td>
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
