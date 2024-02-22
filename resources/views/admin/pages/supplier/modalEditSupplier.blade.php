<div class="modal-body">
    <input type="text" hidden id="idEditSupplier" value="">
    <h6 class="mb-4" id="namaSupplier"> {{$data->nama}}</h6>
    <div class="row">
        <div class="col">
            <label for="nama">Kode</label>
            <input type="text" class="form-control" id="kodeSupplier" value="{{$data->kode}}">
        </div>
        <div class="col">
            <label for="status">Status</label>
            <select id="selectStatusSupplier" class="form-control" data-width="100%">
                <option value="1" {{ $data->deleted_at == NULL ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ $data->deleted_at != NULL ? 'selected' : '' }}>Tidak Aktif</option>
            </select>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary" id="updateSupplier" data-id="{{$data->id}}" data-dismiss="modal">Update</button>
</div>
<script>

    $(document).ready(function () {
        $(document).on('click','#updateSupplier', function () {
            var id          = $(this).data('id');
            var status      = $("#selectStatusSupplier").val();
            var kode        = $("#kodeSupplier").val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('supplier.index') }}",
                data: {
                    status      : status,
                    kode        : kode,
                    key         : 'updateSupplier',
                    id          : id
                },
                success: function (res) {
                    // console.log(res)
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
