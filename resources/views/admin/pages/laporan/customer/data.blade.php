<table class="table default-table">
    <thead>
        <tr>
            <th class="text-center" rowspan="2">Kode</th>
            <th class="text-center" rowspan="2">Nama</th>
            <th class="text-center" rowspan="2">Marketing</th>
            <th class="text-center" colspan="4">Order</th>
            <th class="text-center" rowspan="2">Status</th>
            <th class="text-center" rowspan="2">Aksi</th>
        </tr>
        <tr>
            <th class="text-center">Terakhir</th>
            <th class="text-center">Total</th>
            <th class="text-center">Alokasi</th>
            <th class="text-center">Pending</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $row)
        <tr>
            <td>{{ $row->kode }}</td>
            <td>{{ $row->nama }}</td>
            <td>{{ $row->nama_marketing ?? '' }}</td>
            <td>{{ $row->tanggal_so ?? '' }}</td>
            <td class="text-center">{{ $row->total_order ?? '' }}</td>
            <td class="text-center">{{ $row->alokasi ?? '' }}</td>
            <td class="text-center {{ $row->pending ? 'table-warning' : '' }}">{{ $row->pending ?? '' }}</td>
            <td class="text-center {{ $row->deleted_at == NULL ? 'status status-success' : 'status status-danger' }}">{{ $row->deleted_at == NULL ? 'Aktif': 'Tidak Aktif' }}</td>
            <td>
                <button class="btn btn-outline-warning rounded-0 btn-block" data-toggle="modal" data-target="#editStatusCustomer" onclick="loadCustomer({{ $row->id }})">Edit</button>
                <a href="{{ route('customer.show', $row->id) }}" class="btn btn-outline-primary rounded-0 btn-block">Detail</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="modal fade" id="editStatusCustomer" aria-hidden="true">
    <div class="modal-dialog" style="width: 800px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Data Customer</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="text" hidden id="idEditCustomer" value="">
                <div class="form-group">
                    <input type="text" class="form-control" id="namaCustomer" value="" >
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" id="kodeCustomer" value="" >
                </div>
                <div class="form-group">
                    <select id="selectEditCustomer" class="form-control select2" data-width="100%">
                        <option value="aktif">Aktif</option>
                        <option value="tidakaktif">Tidak Aktif</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="updateStatusCustomer" data-dismiss="modal">Update</button>
            </div>
        </div>
    </div>
</div>


<div class="paginate_pending">
    {{ $data->appends($_GET)->onEachSide(1)->links() }}
</div>

<script>
$('.paginate_pending .pagination a').on('click', function(e) {
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

function loadCustomer(id) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: "{{ route('customer.index') }}",
        method: "GET",
        data: {
            key: 'loadDataCustomer',
            id: id,
        },
        success: function(data) {
            if (data.status == 200) {
                $("#idEditCustomer").val(data.data.id);
                $("#namaCustomer").val(data.data.nama);
                $("#kodeCustomer").val(data.data.kode);
                data.data.deleted_at == null ?  $("#selectEditCustomer").val('aktif').trigger('change') : $("#selectEditCustomer").val('tidakaktif').trigger('change');
            } else {
                showAlert('Data Tidak Ditemukan')
            }
        }
    });
}

$("#updateStatusCustomer").on('click', function() {
    const statusCustomer    = $("#selectEditCustomer").val();
    const id                = $("#idEditCustomer").val();
    const namaCustomer      = $("#namaCustomer").val();
    const kodeCustomer      = $("#kodeCustomer").val();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: "{{ route('customer.index') }}",
        method: "GET",
        data: {
            key: 'updateDataCustomer',
            id: id,
            nama : namaCustomer,
            kode : kodeCustomer,
            statusCustomer
        },
        success: function(data) {
            if (data.status == 200) {
                $('#editStatusCustomer').modal('hide');
                pilih_parent()
                showNotif('Status berhasil diubah')
            } else {
                showAlert('Data Tidak Ditemukan')
            }
        }
    });
})

</script>


