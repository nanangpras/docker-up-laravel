@extends('admin.layout.template')

@section('title', 'Detail Supplier')

@section('footer')
<script>
$("#loading_lb").attr("style", "display: block") ;
$("#ayam_hidup").load("{{ route('supplier.show', [$data->id, 'key' => 'ayam_hidup']) }}", function() {
    $("#loading_lb").attr("style", "display: none") ;
}) ;
</script>

<script>
$("#cari_lb").on('keyup', function() {
    var cari    =   encodeURIComponent($("#cari_lb").val()) ;
    $("#loading_lb").attr("style", "display: block") ;
    $("#ayam_hidup").load("{{ route('supplier.show', [$data->id, 'key' => 'ayam_hidup']) }}&cari=" + cari, function() {
        $("#loading_lb").attr("style", "display: none") ;
    }) ;
})
</script>
@endsection

@section('content')
<div class="row my-4">
    <div class="col"><a href="{{ route('supplier.index') }}"><i class="fa fa-arrow-left"></i> Kembali</a></div>
    <div class="col-8 font-weight-bold text-center">DETAIL SUPPLIER</div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body p-2">
        <table class="table default-table mb-0">
            <tbody>
                <tr>
                    <th style="width: 150px">Kode</th>
                    <td>{{ $data->kode }}</td>
                </tr>
                <tr>
                    <th>Nama Supplier</th>
                    <td>{{ $data->nama }}</td>
                </tr>
                @if ($data->telp)
                <tr>
                    <th>Telepon</th>
                    <td>{{ $data->telp }}</td>
                </tr>
                @endif
                @if ($data->alamat)
                <tr>
                    <th>Alamat</th>
                    <td>{{ $data->alamat }}</td>
                </tr>
                @endif
                @if ($data->wilayah)
                <tr>
                    <th>Wilayah</th>
                    <td>{{ $data->wilayah }}</td>
                </tr>
                @endif
                @if ($data->kategori)
                <tr>
                    <th>Kategori</th>
                    <td>{{ $data->kategori }}</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</section>

@if (COUNT($data->suppurc))
<section class="panel">
    <div class="card-header font-weight-bold">Pembelian Ayam</div>
    <div class="card-body p-2">
        <div class="mb-3">
            <input type="text" autocomplete="off" placeholder="Cari Nomor PO..." id="cari_lb" class="form-control">
        </div>
        <h5 id="loading_lb" style="display: none"><i class="fa fa-refresh fa-spin"></i> Loading.....</h5>
        <div id="ayam_hidup"></div>
    </div>
</section>
@endif
@endsection
