<div class="table-responsive">
    <table class="table table-sm default-table">
        <thead>
            <tr>
                <th>NetID</th>
                <th>Kode</th>
                <th>Nama</th>
                <th>Status</th>
                <th>Detail</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
            <tr>
                <td>{{ $row->netsuite_internal_id }}</td>
                <td>{{ $row->kode }}</td>
                <td>{{ $row->nama }}</td>
                <td class="text-center {{ $row->deleted_at != NULL ? 'status status-danger' : 'status status-success' }}">{{ $row->deleted_at != NULL ? 'Tidak Aktif' : 'Aktif' }}</td>
                <td><a href="{{ route("supplier.show", $row->id) }}" class="btn btn-sm btn-outline-primary p-0 px-3 rounded-0">Detail</a>
                    
                </td>
                <td><button class="btn btn-outline-warning rounded-0 btn-block" id="btnSupplier" data-toggle="modal"
                    data-target="#editDataSupplier" onclick="loadSupplier({{$row->id}})">Edit</button></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="modal fade" id="editDataSupplier" aria-hidden="true">
    <div class="modal-dialog" style="width: 800px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Supplier</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div id="contentEditSupplier"></div>
        </div>
    </div>
</div>

<div class="paginate_supplier">
    {{ $data->appends($_GET)->onEachSide(1)->links() }}
</div>

<script>
$('.paginate_supplier .pagination a').on('click', function(e) {
    e.preventDefault();
    showNotif('Menunggu');

    url = $(this).attr('href');
    $.ajax({
        url: url,
        method: "GET",
        success: function(response) {
            $('#supplier_view').html(response);
        }

    });
});

function loadSupplier(id) {
    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    }); 
    $.ajax({
        url: "{{ route('supplier.index') }}",
        method: "GET",
        data: {
            key: 'editSupplier',
            id: id,
        },
        success: function(data) {
            // console.log(data.data);
            $("#contentEditSupplier").html(data);
        }
    });
}
</script>


