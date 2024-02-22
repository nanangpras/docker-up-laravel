<table class="table default-table">
    <thead>
        <tr>
            <th>NetID</th>
            <th>Nama</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        
        @foreach ($data as $row)
        <tr>
            <td>{{ $row->netsuite_internal_id ?? ''}}</td>
            <td>{{ $row->code ?? ''}}</td>
            <td>
                @if ($row->status == 1)
                    <div class="text-center status status-success">Aktif</div>
                @else
                    <div class="text-center status status-danger">Tidak Aktif</div>
                @endif
            </td>
            {{-- <td>{{ ($row->status == 1) ? 'Aktif' : 'Tidak Aktif' }}</td> --}}
            <td>
                {{-- <div style="width: 100px; display: inline-block;"> --}}
                    <button href="javascript:void(0)" class="btn btn-sm btn-outline-secondary btnDetailGudang" data-id="{{$row->id}}" data-title="Detail Gudang" data-toggle="tooltip" data-placement="bottom" title="Detail"><i class="fa fa-eye" aria-hidden="true"></i></button>
                    <button  href="javascript:void(0)" type="button" class="btn btn-sm btn-outline-info btnEditGudang" data-id="{{$row->id}}" data-idabf="{{$row->table_id}}" data-title="Edit Gudang" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil-square-o"></i></button>
                    <button href="javascript:void(0)" type="button" class="btn btn-sm btn-outline-danger btnHapusGudang" data-id="{{$row->id}}" data-idabf="{{$row->table_id}}" data-title="Delete Gudang" data-toggle="tooltip" data-placement="bottom" title="Hapus" ><i class="fa fa-trash" aria-hidden="true"></i></button>
                {{-- </div> --}}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="modal fade modal-gudang" id="editInOut" aria-labelledby="editLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titleGudang"></h5>
                <button type="button" class="close btn-closein" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <h5 id="spinergudang" style="display: none" class="text-center"><i class="fa fa-refresh fa-spin"></i> Loading....</h5>
            <div class="content-gudang"></div>
        </div>
    </div>
</div>

<div class="paginate_gudang">
    {{ $data->appends($_GET)->onEachSide(1)->links() }}
</div>

<script>
$('.paginate_gudang .pagination a').on('click', function(e) {
    e.preventDefault();
    showNotif('Menunggu');

    url = $(this).attr('href');
    $.ajax({
        url: url,
        method: "GET",
        success: function(response) {
            $('#data_gudang').html(response);
        }

    });
});

// Detail
    $(".btnDetailGudang").click(function (e) { 
        e.preventDefault();
        $('.modal-gudang').modal('show');
        $("#spinergudang").show();
        var id = $(this).data('id');
        var title = $(this).data('title');
        $.ajax({
            type: "GET",
            url: "{{ route('gudang.show')}}",
            data: {
                'key' : 'detail',
                id: id,
            },
            success: function (response) {
                $(".content-gudang").html(response);
                $("#titleGudang").text(title);
                $("#spinergudang").hide();
            }
        });
            
    });
// Edit
    $(".btnEditGudang").click(function (e) { 
        e.preventDefault();
        $('.modal-gudang').modal('show');
        $("#spinergudang").show();
        var id = $(this).data('id');
        var title = $(this).data('title');
        $.ajax({
            type: "GET",
            url: "{{ route('gudang.show')}}",
            data: {
                'key' : 'edit',
                id: id,
            },
            success: function (response) {
                $(".content-gudang").html(response);
                $("#titleGudang").text(title);
                $("#spinergudang").hide();
            }
        });
            
    });
// Delete
    $(".btnHapusGudang").click(function (e) { 
        e.preventDefault();
        $('.modal-gudang').modal('show');
        $("#spinergudang").show();
        var id = $(this).data('id');
        var title = $(this).data('title');
        $.ajax({
            type: "GET",
            url: "{{ route('gudang.show')}}",
            data: {
                'key' : 'delete',
                id: id,
            },
            success: function (response) {
                $(".content-gudang").html(response);
                $("#titleGudang").text(title);
                $("#spinergudang").hide();
            }
        });
            
    });
</script>



