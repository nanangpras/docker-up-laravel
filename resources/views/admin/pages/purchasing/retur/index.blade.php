@extends('admin.layout.template')

@section('title', 'Retur Purchase Order')

@section('content')
<div class="row my-4">
    <div class="col"><a href="{{ route('purchasing.index') }}"><i class="fa fa-arrow-left"></i> Kembali</a></div>
    <div class="col-8 text-center font-weight-bold text-uppercase">Retur Purchase Order</div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">
        <table class="table default-table">
            <tbody>
                <tr>
                    <th style="width: 130px">Nomor PO</th>
                    <td>{{ $data->no_po }}</td>
                </tr>
                <tr>
                    <th>Jenis PO</th>
                    <td>{{ $data->jenis_po }}</td>
                </tr>
                <tr>
                    <th>Netsuite ID</th>
                    <td>{{ $data->internal_id_po }}</td>
                </tr>
                <tr>
                    <th>Supplier</th>
                    <td>{{ $data->purcsupp->nama }}</td>
                </tr>
                <tr>
                    <th>Wilayah</th>
                    <td>{{ $data->wilayah_daerah }}</td>
                </tr>
                <tr>
                    <th>Ekspedisi</th>
                    <td class="text-capitalize">{{ $data->type_ekspedisi }}</td>
                </tr>
            </tbody>
        </table>

        <form action="{{ route('purchasing.returstore', $data->id) }}" method="post">
            @csrf

            <table class="table default-table table-hover">
                <thead>
                    <tr>
                        <th rowspan="2">Item</th>
                        <th class="text-center" colspan="2">Purchase (1)</th>
                        <th class="text-center" colspan="2">Retur (2)</th>
                        <th class="text-center" colspan="2">Sisa (1-2)</th>
                    </tr>
                    <tr>
                        <th class="text-center">Qty</th>
                        <th class="text-center">Berat</th>
                        <th class="text-center">Qty</th>
                        <th class="text-center">Berat</th>
                        <th class="text-center">Qty</th>
                        <th class="text-center">Berat</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data->purchasing_item as $row)
                    <input type="hidden" name="id[]" id="{{ $row->id }}" value="{{ $row->id }}">
                    <tr>
                        <td>{{ $row->description }}</td>
                        <td class="text-right">{{ number_format($row->jumlah_ayam) }}</td>
                        <td class="text-right">{{ number_format($row->berat_ayam, 2) }}</td>
                        <td class="p-0">
                            <input type="number" autocomplete="off" min="0" max="{{ $row->jumlah_ayam }}" name="qty[]"
                                id="qty{{ $row->id }}" placeholder="Tulis Qty" style="font-size: 12px"
                                class="form-control form-control-sm py-2 border-0 rounded-0">
                        </td>
                        <td class="p-0">
                            <input type="number" autocomplete="off" min="0" max="{{ $row->berat_ayam }}" step="0.01"
                                name="berat[]" id="berat{{ $row->id }}" placeholder="Tulis Berat"
                                style="font-size: 12px" class="form-control form-control-sm py-2 border-0 rounded-0">
                        </td>
                        <td class="text-right">{{ number_format(($row->jumlah_ayam -
                            App\Models\Returpurchase::where("purchaseitem_id", $row->id)->sum('qty'))) }}</td>
                        <td class="text-right">{{ number_format(($row->berat_ayam -
                            App\Models\Returpurchase::where("purchaseitem_id", $row->id)->sum('berat')), 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="form-group">
                        Tanggal Retur
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif name="tanggal" value="{{ date("Y-m-d") }}" class="form-control">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        Alasan Retur
                        <select name="alasan" data-placeholder="Pilih Alasan" data-width="100%"
                            class="form-control select2" required>
                            <option value=""></option>
                            @foreach ($alasan as $row)
                            <option value="{{ $row->id }}">{{ $row->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        Penginput
                        <input type="text" name="penginput" class="form-control" placeholder="Tulis Penginput Retur"
                            autocomplete="off" required>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-block btn-outline-primary">Submit Retur</button>
        </form>

    </div>
</section>

@if (COUNT($data->retur_purchase))
<h6>Daftar Retur Purchase</h6>
<section class="panel">
    <div class="card-body p-2">
        <table class="table default-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Berat</th>
                    <th>Alasan</th>
                    <th>Penginput</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data->retur_purchase as $row)
                <tr>
                    <td>{{ $row->tanggal }}</td>
                    <td>{{ $row->purchase_item->description ?? '' }}</td>
                    <td class="text-right">{{ number_format($row->qty) }}</td>
                    <td class="text-right">{{ number_format($row->berat, 2) }}</td>
                    <td>{{ $row->get_alasan->nama }}</td>
                    <td>{{ $row->penginput }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
@endif
@endsection

@section('footer')
<script>
    $('.select2').select2({
    theme: 'bootstrap4',
})
</script>

<script>
    $(".btn-hidden").on('click', function() {
    var id      =   $(this).data('id') ;
    var qty     =   $("#qty" + id).val() ;
    var berat   =   $("#berat" + id).val() ;
    var alasan  =   $("#alasan" + id).val() ;

    if (qty && berat && alasan) {
        $(".btn-hidden").hide() ;
    }
})
</script>
@endsection