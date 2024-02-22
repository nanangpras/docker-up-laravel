@extends('admin.layout.template')

@section('title', 'Produksi Kepala Regu')

@section('content')

    <div class="row mb-4">
        <div class="col"></div>
        <div class="col text-center py-2">
            <b>HISTORY EDIT PENANGANAN RETUR ITEM</b>
        </div>
        <div class="col"></div>
    </div>

    <section class="panel">
        <div class="card-body">
            <b>DATA ASLI</b>
            <table class="table default-table">
                <thead>
                    <tr>
                        <th>Nama Item</th>
                        <th>Lokasi NS</th>
                        <th>Penanganan</th>
                        <th>Retur Qty</th>
                        <th>Retur Berat</th>
                        <th>Alasan</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $json_dataAsli = json_decode($dataAsli->data,true);
                    @endphp
                    @if ($content == NULL || $content == 'Original Item')
                    <tr>
                        <td>{{ \App\Models\Item::where('id', $json_dataAsli['data']['item_id'])->withTrashed()->first()->nama }}</td>
                        <td>{{ $json_dataAsli['data']['unit'] }}</td>
                        <td>{{ $json_dataAsli['data']['penanganan'] }}</td>
                        <td>{{ $json_dataAsli['data']['qty'] }}</td>
                        <td>{{ $json_dataAsli['data']['berat'] }}</td>
                        <td>{{ $json_dataAsli['data']['catatan'] }}</td>
                        <td>{{ $json_dataAsli['data']['kategori'] }}</td>
                        <td>{{ $json_dataAsli['data']['satuan'] }}</td>
                    </tr>
                    @else
                    @php
                        $json_data = json_decode($logretur[0]->data,true);
                    @endphp
                    <tr>
                        <td>{{ \App\Models\Item::where('id', $json_data['data']['item_id'])->withTrashed()->first()->nama }}</td>
                        <td>{{ $json_data['data']['unit'] }}</td>
                        <td>{{ $json_data['data']['penanganan'] }}</td>
                        <td>{{ $json_data['data']['qty'] }}</td>
                        <td>{{ $json_data['data']['berat'] }}</td>
                        <td>{{ $json_data['data']['catatan'] }}</td>
                        <td>{{ $json_data['data']['kategori'] }}</td>
                        <td>{{ $json_data['data']['satuan'] }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
            <div class="section">
                <div class="row">
                    <div class="col-lg-12 pr-lg-1">
 
                        <div class="panel panel-item">
                            <div class="panel-heading">
                            </div>
                            <div class="panel-body">
                                <table class="table default-table">
                                    <thead>
                                        <tr>
                                            <th>Nama Item</th>
                                            <th>Lokasi NS</th>
                                            <th>Penanganan</th>
                                            <th>Retur Qty</th>
                                            <th>Retur Berat</th>
                                            <th>Alasan</th>
                                            <th>Kategori</th>
                                            <th>Satuan</th>
                                            <th>Tanggal Edit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($logretur as $key => $row)   
                                        @php
                                            $json_data = json_decode($row->data,true);
                                        @endphp
                                        <tr>
                                            <td>{{ \App\Models\Item::where('id', $json_data['data']['item_id'])->withTrashed()->first()->nama }}</td>
                                            <td>{{ $json_data['data']['unit'] }}</td>
                                            <td>{{ $json_data['data']['penanganan'] }}</td>
                                            <td>{{ $json_data['data']['qty'] }}</td>
                                            <td>{{ $json_data['data']['berat'] }}</td>
                                            <td>{{ $json_data['data']['catatan'] }}</td>
                                            <td>{{ $json_data['data']['kategori'] }}</td>
                                            <td>{{ $json_data['data']['satuan'] }}</td>
                                            <td>{{ $row->created_at }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection
