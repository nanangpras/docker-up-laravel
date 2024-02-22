@extends('admin.layout.template')

@section('title', 'Warehouse')

@section('footer')

@endsection

@section('content')
    <div class="row mb-3">
        <div class="col">
            <a
                href="{{ route('warehouse.index') }}?tanggal_mulai={{ $tanggal_mulai }}&tanggal_akhir={{ $tanggal_akhir }}"><i
                    class="fa fa-arrow-left"></i> Back</a>
        </div>
        <div class="col font-weight-bold text-uppercase text-center">
            Detail Konsumen
        </div>
        <div class="col"></div>
    </div>

    <section class="panel">
        <div class="card-body">
            <table class="table default-table">
                <tbody>
                    <tr>
                        <th style="width:150px">Nama Konsumen</th>
                        <td>{{ $data[0]->konsumen->nama ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Mulai</th>
                        <td>{{ $tanggal_mulai }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Akhir</th>
                        <td>{{ $tanggal_akhir }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="row">
                @foreach ($gudang as $key => $value)
                    {{-- <div class="col">
                        <div class="form-group">
                            <div class="bg-info p-2 text-center text-light font-weight-bold text-uppercase">
                                {{ $value->productgudang->code }}
                            </div>
                            <div class="border p-2 text-center">
                                <h5 class="mb-0">{{ number_format($value->jumlah_qty) }}</h5>
                            </div>
                        </div>
                    </div> --}}

                    <div class="col-sm-4 col-lg mb-2 pr-sm-1">
                        <div class="card">
                            <div class="card-header">{{ $value->productgudang->code }}</div>
                            <div class="card-body p-2">
                                <div class="row mb-1">
                                    <div class="col pr-1">
                                        <div class="border text-center">
                                            <div class="small">Qty</div>
                                            <div class="font-weight-bold">{{ number_format($value->jumlah_qty) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col pl-1">
                                        <div class="border text-center">
                                            <div class="small">Berat</div>
                                            <div class="font-weight-bold"> {{ number_format($value->jumlah_berat,2) }} kg
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- <div class="col">
                    <div class="form-group">
                        <div class="bg-info p-2 text-center text-light font-weight-bold text-uppercase">Total Berat</div>
                        <div class="border p-2 text-center">
                            <h5 class="mb-0">{{ number_format($result['kg'], 2) }}</h5>
                        </div>
                    </div>
                </div> --}}
            </div>

            <div class="table-responsive">
                <table class="table default-table">
                    <thead>
                        <tr class="text-center">
                            <th>Nama</th>
                            <th>Packaging</th>
                            <th>Qty/Pcs/Ekor</th>
                            <th>Berat (Kg)</th>
                            <th>Status</th>
                            <th>Tujuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $row)
                            <tr class="text-center">
                                <td>{{ $row->nama ?? '' }}</td>
                                <td>{{ $row->packaging ?? '' }}</td>
                                <td class="text-right">{{ $row->qty ?? '' }}</td>
                                <td class="text-right">{{ number_format($row->berat,2) }}</td>
                                @if ($row->status == 2)
                                    <td>
                                        <div class="status status-info">Masuk</div>
                                    </td>
                                @else
                                    <td>
                                        <div class="status status-warning">Keluar</div>
                                    </td>
                                @endif
                                <td>{{ $row->productgudang->code ?? 0 }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div id="daftar_paginate">
                {{ $data->appends($_GET)->links() }}
            </div>

            <script>
                $('#daftar_paginate .pagination a').on('click', function(e) {
                    e.preventDefault();
                    showNotif('Menunggu');

                    url = $(this).attr('href');
                    $.ajax({
                        url: url,
                        method: "GET",
                        success: function(response) {
                            $('#data_view').html(response);
                        }

                    });
                });
            </script>

        </div>
    </section>

@stop
