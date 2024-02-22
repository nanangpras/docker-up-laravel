
<div class="table-responsive">
    <div class="form-group">
        Pencarian Customer
        <select name="search_customer_hargakontrak" id="search_customer_hargakontrak" class="form-control select2" data-placeholder="Pilih Customer" data-width="100%" onchange="search_customer_hargakontrak()">
            <option value=""></option>
            @foreach ($customer as $row)
                <option value="{{ $row->id }}" {{ $row->id == $id_cust ? 'selected' : '' }}>{{ $row->kode }}. {{ $row->nama }}</option>
            @endforeach
        </select>
    </div>
    <table class="table table-sm table-bordered table-striped">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
            <tr>
                <td>{{ $row->kode }}</td>
                <td>{{ $row->nama }}</td>
                <td><button class="btn btn-outline-info rounded-0 px-2 py-0" data-toggle="modal" data-target="#showRiwayat{{ $row->id }}">Lihat</button></td>
            </tr>

            <div class="modal fade" id="showRiwayat{{ $row->id }}" tabindex="-1" aria-labelledby="showRiwayat{{ $row->id }}Label" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="showRiwayat{{ $row->id }}Label">Detail Harga Kontrak</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <b>{{ $row->kode }} - {{ $row->nama }}</b>
                            <div class="border-top mt-2 pt-2">
                                @foreach ($row->harga_kontrak as $list)
                                    <div class="border-bottom pb-1 mb-1" id="data-{{ $list->id }}">
                                        <div class="float-right">{{ number_format($list->harga) }}<br>{{ $row->unit }}</div>
                                        {{ $list->item->sku  ?? "#ITEM DIHAPUS" }} - {{ $list->item->nama ?? "ITEMDIHAPUS" }}
                                        <div class="text-info">
                                            @if ($list->min_order)
                                            (Min Order {{ $list->min_order }})
                                            @endif
                                            {{ $list->mulai }} - {{ $list->sampai }}
                                            <button class="btn btn-outline-danger rounded-0 px-2 py-0 float-right ml-1" data-id="{{ $list->id }}" onclick="hapusKontrak($(this).data('id'))">Hapus</button>
                                            <a href="{{ route('hargakontrak.index', ['key' => 'edit'])}}&id={{ $list->id }}"type="button" class="btn btn-outline-info rounded-0 px-2 py-0 float-right">Edit</a>
                                        </div>
                                        @if ($list->keterangan)
                                            <i class="fa fa-info"></i> {{ $list->keterangan }}
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </tbody>
    </table>
</div>

<div class="paginate_kontrak">
    {{ $data->appends($_GET)->onEachSide(1)->links() }}
</div>


<script>
    $(".select2").select2({
        theme: "bootstrap4"
    });
</script>
    
<script>
$('.paginate_kontrak .pagination a').on('click', function(e) {
    e.preventDefault();
    showNotif('Menunggu');

    url = $(this).attr('href');
    $.ajax({
        url: url,
        method: "GET",
        success: function(response) {
            $('#data_riwayat').html(response);
        }

    });
});

function hapusKontrak(id){
    var result = confirm("Yakin ingin menghapus Kontrak?");
    if (result) {
        // console.log(id)
        $.ajax({
            url: "{{ route('hargakontrak.index') }}",
            data:{
                _token:"{{ csrf_token() }}",
                id:id,
                key:'hapusKontrak',
            },
            success: function(response) {
                // console.log(response)
                if (response.status == 200) {
                    $('#data-'+id).remove();
                    showNotif(response.msg);
                } else {
                    showNotif(response.msg);
                }
            }
        });
    }
}

function search_customer_hargakontrak(){
    $('#data_riwayat').load("{{ route('hargakontrak.index') }}?customer="+$('#search_customer_hargakontrak').val()+"&key=riwayat");
}

</script>
