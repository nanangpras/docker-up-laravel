@extends('admin.layout.template')

@section('title', 'List Gudang')

@section('footer')
<script>
$("#loading").attr("style", 'display: block') ;
$("#data_gudang").load("{{ route('gudang.index', ['key' => 'view']) }}", function() {
    $("#loading").attr("style", 'display: none') ;
});
</script>

<script>
$("#cari").on('keyup', function() {
    var cari    =   encodeURIComponent($("#cari").val()) ;
    var status  =   $("#status").val() ;
    $("#loading").attr("style", 'display: block') ;
    $("#data_gudang").load("{{ route('gudang.index', ['key' => 'view']) }}&cari=" + cari + "&status=" +status, function() {
        $("#loading").attr("style", 'display: none') ;
    });
})
$("#status").on("change", function () {
    var cari    =   encodeURIComponent($("#cari").val()) ;
    var status  =   $("#status").val() ;
    $("#loading").attr("style", 'display: block') ;
    $("#data_gudang").load("{{ route('gudang.index', ['key' => 'view']) }}&cari=" + cari + "&status=" +status, function() {
        $("#loading").attr("style", 'display: none') ;
    });
});
</script>
<script>
    $('.select2').select2({
        theme: 'bootstrap4'
    });
</script>
@endsection

@section('content')
<div class="my-4 text-center font-weight-bold">LIST GUDANG</div>

<section class="panel">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <label for="status">Status</label>
                <select name="status" class="form-control" id="status">
                    <option value="1">Aktif</option>
                    <option value="0">Tidak Aktif</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="cari">Pencarian</label>
                <input type="text" placeholder="Cari..." autocomplete="off" id="cari" class="form-control">
            </div>
            <div class="col-md-2">
                <div class="mt-4"> </div>
                <button type="button" id="btn-tambah" class="btn btn-primary" data-toggle="modal" data-target="#tambahGudang">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i> Tambah Gudang
                </button>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="tambahGudang" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{route('gudang.store')}}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            Nama Gudang
                            <input type="text" name="nama_gudang" id="namagudang" class="form-control" value="">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            Netsuite ID
                            <input type="text" name="netsuite_id" id="netsuite_id" class="form-control" value="">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    Kategori
                    <select name="kategori" id="kategori" data-placeholder="Pilih Kategori" class="form-control select2">
                        @foreach ($kategori as $item)
                            <option value="{{$item->kategori}}">{{$item->kategori}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    Subsidiary
                    <select name="subsidiary" data-placeholder="Pilih Subsidiary"  id="subsidiary" class="form-control select2">
                        <option value="EBA">EBA</option>
                        <option value="CGL">CGL</option>
                        <option value="MPP">MPP</option>
                    </select>
                </div>
                <div class="form-group">
                    Status
                    <select name="status" id="status" data-placeholder="Status Gudang" class="form-control select2">
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
      </div>
    </div>
  </div>

<section class="panel">
    <div class="card-body p-2">
        <h5 id="loading" style="display: none" class="text-center"><i class="fa fa-refresh fa-spin"></i> Loading....</h5>
        <div id="data_gudang"></div>
    </div>
</section>
@endsection
