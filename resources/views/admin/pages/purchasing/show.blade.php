@extends('admin.layout.template')

@section('title', 'Detail Purchasing')

@section('footer')
<script>
$('.mobil').change(function() {
    id      = $(this).val();

    console.log("{{ route('purchasing.show', $data->id) }}?key=mobil&id=" + id);
    $("#show").load("{{ route('purchasing.show', $data->id) }}?key=mobil&id=" + id);
})
</script>
<link href="{{asset('plugin')}}/jquery-ui.css" rel="stylesheet">
<script src="{{asset('plugin')}}/jquery-ui.js"></script>
{{-- <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script> --}}
@endsection

@section('content')
<div class="row mb-3">
    <div class="col">
        <a href="{{ route('purchasing.index') }}" class="btn btn-outline btn-sm btn-back"><i class="fa fa-arrow-left"></i> Back</a>
    </div>
    <div class="col text-center"><b>DETAIL PURCHASING ORDER</b></div>
    <div class="col"></div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="row">
            <div class="col-lg-2 col-md-3 col-6">
                <div class="form-group">
                    <b>Nomor PO</b>
                    <div>{{ $data->no_po ?? '' }}</div>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <div class="form-group">
                    <b>Supplier</b>
                    <div>{{ $data->purcsupp->nama ?? '' }}</div>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <div class="form-group">
                    <b>Daerah</b>
                    <div>{{ $data->wilayah_daerah ?? '' }}</div>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <div class="form-group">
                    <b>Tipe PO</b>
                    <div>{{ $data->type_po ?? '' }}</div>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <div class="form-group">
                    <b>Ekspedisi</b>
                    <div>{{ $data->type_ekspedisi ?? '' }}</div>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <div class="form-group">
                    <b>Tanggal Potong</b>
                    <div>{{ date('d/m/y', strtotime($data->tanggal_potong)) }}</div>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <b>Item</b>
                    @if ($data->type_po == 'PO LB')
                    <ol style="margin:0; padding-left: 15px">
                        <li>AYAM UKURAN {{ $data->ukuran_ayam }}
                            @foreach ($data->purchasing_item as $item)
                                <br><span
                                    class="status status-success">{{ number_format($item->jumlah_ayam) }}
                                    Ekor</span> || <span
                                    class="status status-info">{{ number_format($item->berat_ayam, 2) }}
                                    Kg</span>
                            @endforeach
                        </li>
                    </ol>
                @else
                    <ol style="margin:0; padding-left: 15px">
                        @foreach ($data->purchasing_item as $item)
                            <li>
                                {{ \App\Models\Item::item_sku($item->item_po)->nama ?? "#" }}<br><span
                                    class="status status-success">{{ number_format($item->jumlah_ayam) }}
                                    {{ ($item->type_po == "PO LB" || $item->type_po == "PO Maklon") ? "Ekor" : "Pcs" }}</span> || <span
                                    class="status status-info">{{ number_format($item->berat_ayam, 2) }}
                                    Kg</span>
                            </li>
                        @endforeach
                    </ol>
                @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-3 col-md-4 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="radio-toolbar">
                    <div class="row">
                        @foreach ($produksi as $i => $row)
                            <div class="col-4 col-md-12">
                                <div class="mb-2">
                                    <input type="radio" name="mobil" class="mobil" value="{{ $row->id }}" id="{{ $row->id }}">
                                    <label for="{{ $row->id }}">Mobil {{ $i + 1 }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-9 col-md-8 mb-3">
        <div id="show"></div>
    </div>
</div>
{{-- <div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-body">
                <div class="small">Supplier</div>
                {{ $data->purcsupp->nama }}
                <div class="row mt-2 mb-2">
                    <div class="col">
                        <div class="small">Tipe PO</div>
                        <div class="text-uppercase">{{ $data->type_po }}</div>
                    </div>
                    <div class="col">
                        <div class="small">Ukuran</div>
                        {{ $data->ukuran_ayam }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col">
                        <div class="small">Berat Ayam</div>
                        {{ number_format(0) }}
                    </div>
                    <div class="col">
                        <div class="small">Jumlah Ayam</div>
                        {{ number_format(0) }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col">
                        <div class="small">Daerah</div>
                        <span class="text-capitalize">{{ $data->wilayah_daerah }}</span>
                    </div>
                    <div class="col">
                        <div class="small">Ekspedisi</div>
                        <span class="text-capitalize">{{ $data->type_ekspedisi }}</span>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col">
                        <div class="small">Harga Awal</div>
                        {{ number_format($data->harga_penawaran) }}
                    </div>
                    <div class="col">
                        <div class="small">Harga Deal</div>
                        {{ number_format($data->harga_deal) }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col">
                        <div class="small">Jumlah PO</div>
                        {{ number_format($data->jumlah_po) }}
                    </div>
                    <div class="col">
                        <div class="small">Tanggal Potong</div>
                        {{ date('d/m/y', strtotime($data->tanggal_potong)) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table default-table">
                        <thead>
                            <tr>
                                <th>Mobil</th>
                                <th>Supir</th>
                                <th>Toleransi</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($produksi as $i => $item)
                            <tr>
                                <td>Mobil {{ ($i+1) }}</td>
                                <td>{{ $item->driver->nama ?? '' }}</td>
                                <td>{{ $item->sc_pengemudi_target . ' %' ?? '' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('purchasing.detail', [$data->id, $item->id]) }}" class="btn btn-sm btn-primary">Lihat</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div> --}}


@stop
