<div class="row">
    <div class="col-12 col-md-12 col-sm-12 col-xs-12">
        <div class="table-outer">
            <div class="table-inner">
                <div class="table-responsive">
                    <table class="table default-table" id="tbl_logadmin">
                        <thead>
                            <tr align="center">
                                <td>No</td>
                                <td>Tanggal Dibuat</td>
                                <td>Tipe</td>
                                <td>Activity</td>
                                <td>Keterangan</td>
                                <td>Action</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($logadmin as $item)
                                <tr align="center">
                                    <td>{{ $loop->iteration + ($logadmin->currentpage() - 1) * $logadmin->perPage() }}</td>
                                    <td>{{ $item->created_at }}</td>
                                    <td>
                                        @if ($item->type == 'input' || $item->type == 'tambah')
                                            <span class="badge badge-info">{{ $item->type }}</span>
                                        @else
                                            {{ $item->type }}
                                        @endif
                                    </td>
                                    <td>{{ $item->activity }}</td>
                                    <td>{{ $item->content }}</td>
                                    <td>
                                        <button href="{{ route('users.index') }}" type="button"
                                            class="btn btn-primary btnlogadmin"
                                            data-id="{{ $item->id }}">Detail</button>
                                        {{-- <button href="javascript:void(0)" type='button' id="btnlogadmin" class="btn btn-blue" data-toggle="modal" data-riwayat="{{$item->data}}" data-target="#logadmin">Detail</button> --}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div id="paginate_abf_diterima">
                        {{ $logadmin->appends($_GET)->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('#paginate_abf_diterima .pagination a').on('click', function(e) {
    e.preventDefault();
    showNotif('Menunggu');

    url = $(this).attr('href');
    $.ajax({
        url: url,
        method: "GET",
        success: function(response) {
            $('#view_filter').html(response);
        }

    });
});
</script>