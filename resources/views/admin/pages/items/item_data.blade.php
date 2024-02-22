<div class="table-responsive">
    <table class="table table-sm default-table dataTable">
        <thead>
            <tr>
                <th>No</th>
                <th>App ID</th>
                <th>Kode</th>
                <th>Nama</th>
                <th>Sub</th>
                <th>Netsuite ID</th>
                <th>Jenis</th>
                <th>SKU</th>
                <th>Kategori</th>
                <th>Tax</th>
                <th>Berat Kali</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $i => $row)
                <tr>
                    <td>{{ ++$i + ($data->currentpage() - 1) * $data->perPage() }}</td>
                    <td>{{ $row->id }}</td>
                    <td>{{ $row->code_item ?? '' }}</td>
                    <td>{{ $row->nama }}</td>
                    <td>{{ $row->subsidiary }}</td>
                    <td>{{ $row->netsuite_internal_id }}</td>
                    <td>{{ $row->jenis }}</td>
                    <td>{{ $row->sku }}</td>
                    <td>{{ $row->itemkat->nama ?? '' }}</td>
                    <td>{{ $row->tax_code ?? '' }}</td>
                    <td>{{ $row->berat_kali ?? '' }}</td>
                    <td class="text-center {{ $row->status == 1 && $row->deleted_at == NULL ? 'status status-success' : 'status status-danger' }}">
                        {{ $row->status == 1 && $row->deleted_at == NULL ? 'Aktif' : 'Tidak Aktif' }}</td>
                    <td>
                        <button class="btn btn-outline-warning rounded-0 btn-block" id="btnItem" data-toggle="modal"
                        data-target="#editDataItem" onclick="loadItem({{$row->id}})">Edit</button>

                        @if (in_array($row->category_id, $dataOption) && $row->status == 1 && $row->nama != "AKUMULASI SUSUT AYAM")    
                            <button class="btn btn-outline-primary rounded-0 btn-block" id="btnAksesItem" data-toggle="modal"
                            data-target="#aksesDataItem" onclick="loadAkses({{ $row->id }})">
                                Akses Regu
                            </button>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="modal fade" id="editDataItem" aria-hidden="true">
    <div class="modal-dialog" style="width: 800px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Item</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div id="content_edit_item"></div>
        </div>
    </div>
</div>

{{-- modal akses --}}
<div class="modal fade" id="aksesDataItem" aria-hidden="true">
    <div class="modal-dialog" style="width: 800px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Akses Regu Item</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div id="content_hak_item"></div>
        </div>
    </div>
</div>



<div class="paginate_item">
    {{ $data->appends($_GET)->onEachSide(1)->links() }}
</div>

<script>
    $('.paginate_item .pagination a').on('click', function(e) {
        e.preventDefault();
        showNotif('Menunggu');

        url = $(this).attr('href');
        $.ajax({
            url: url,
            method: "GET",
            success: function(response) {
                $('#data_item').html(response);
            }

        });
    });


    function loadItem(id) {
        $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        }); 
        $.ajax({
            url: "{{ route('item.index') }}",
            method: "GET",
            data: {
                key: 'editItem',
                id: id,
            },
            success: function(data) {
                // console.log(data.data);
                $("#content_edit_item").html(data);
            }
        });
    }

    function loadAkses(id) {
        $.ajax({
        url     : "{{ route('item.index') }}",
        method  : "GET",
        data    : {
            key : 'akses',
            id  : id,
        },
        success : function (data) {
            $("#content_hak_item").html(data);
        }
    })
    }
    
</script>
