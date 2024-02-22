<div class="modal-body">
    <input type="text" hidden id="idEditItem" value="">
    <div class="row mb-3">
        <div class="col">
            <label for="nama">Nama</label>
            <input type="text" class="form-control" id="nama_item" value="{{$data->nama}}">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
            <label for="nama">Kode</label>
            <input type="text" class="form-control" id="code_item" value="{{$data->code_item}}">
        </div>
        <div class="col">
            <label for="sku">SKU</label>
            <input type="text" class="form-control" id="sku_item" value="{{$data->sku}}">
        </div>
        <div class="col">
            <label for="netsuiteId">Netsuite Internal ID</label>
            <input type="text" class="form-control" id="netsuiteId" value="{{$data->netsuite_internal_id}}">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="kategori">Kategori</label>
            <select name="category_item" id="selectKategoriItem" class="form-control select2">
                <option value="" disabled selected hidden>Pilih Kategori</option>
                @foreach ($category as $item)
                    <option value="{{ $item->id}}" {{$item->id == $data->category_id ? 'selected' : ''}}> {{ $item->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="col">
            <label for="status">Status</label>
            <select id="selectStatusItem" class="form-control select2" data-width="100%">
                <option value="1" {{ $data->status == 1 && $data->deleted_at == NULL ? 'selected' : ''}}>Aktif</option>
                <option value="0" {{ $data->status == 0 || $data->deleted_at != NULL ? 'selected' : ''}}>Tidak Aktif</option>
            </select>
        </div>
        <div class="col">
            <label for="subsidiary">Subsidiary</label>
            <input type="text" class="form-control" id="subsidiary_item" value="{{$data->subsidiary}}">
        </div>
        @php
            $cekBomItem = App\Models\BomItem::where('sku', $data->sku)->first();
        @endphp

        @if ($cekBomItem)
            <div class="col mt-3">
                <label for="qty">Qty Assembly</label>
                <input type="text" class="form-control" id="qty_assembly" value="{{ $cekBomItem->qty_per_assembly }}">
            </div>
        @endif
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary" id="updateItem" data-id="{{$data->id}}" data-dismiss="modal">Update</button>
</div>
<script>
    $('.select2').select2({
        theme: 'bootstrap4',
    });

    $(document).ready(function () {
        $(document).on('click','#updateItem', function () {
            var id           = $(this).data('id');
            var status       = $("#selectStatusItem").val();
            var kategori     = $("#selectKategoriItem").val();
            var sku          = $("#sku_item").val();
            var nama         = $("#nama_item").val();
            var netsuiteId   = $("#netsuiteId").val();
            var subsidiary   = $("#subsidiary_item").val();
            var code_item    = $("#code_item").val();
            var qty_assembly = $("#qty_assembly").val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{route('update.item',$data->id)}}",
                method: "PATCH",
                data: {
                    category_id : kategori,
                    status      : status,
                    sku         : sku,
                    nama        : nama,
                    subsidiary  : subsidiary,
                    code_item   : code_item,
                    qty_assembly   : qty_assembly,
                    netsuiteId
                },
                success: function (res) {
                    if (res.status == 400) {
                        showAlert(res.msg);
                    } else {
                        showNotif(res.msg);
                        location.reload();
                    }
                }
            });
            // alert(id);
            
        });
    });
</script>
