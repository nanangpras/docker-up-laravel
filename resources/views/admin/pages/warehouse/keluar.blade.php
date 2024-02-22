<div class="row">
    <div class="col">
        <div class="form-group">
            <div class="bg-info p-2 text-center text-light font-weight-bold text-uppercase">Total QTY/Pcs/Ekor</div>
            <div class="border p-2 text-center">
                <h5 class="mb-0">{{ number_format($result['qty']) }}</h5>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="form-group">
            <div class="bg-info p-2 text-center text-light font-weight-bold text-uppercase">Total Berat</div>
            <div class="border p-2 text-center">
                <h5 class="mb-0">{{ number_format($result['kg'], 2) }}</h5>
            </div>
        </div>
    </div>

    <div class="col-auto pl-1">
        <div class="form-group">
            <a href="{{ route('warehouse.keluar', array_merge(['key' => 'unduh'], $_GET)) }}" class="btn btn-success btn-block rounded-0"><i class="fa fa-file-excel-o"></i> Unduh</a>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table width="100%" class="table default-table">
        <thead>
            <tr>
                <th>No</th>
                <th>ID</th>
                <th>Kode</th>
                <th>Tanggal</th>
                <th>Nama</th>
                <th>Parting</th>
                <th>No Document</th>
                <th>Packaging</th>
                <th>Sub Item</th>
                <th>Lokasi</th>
                <th>Qty/Pcs/Ekr</th>
                <th>Berat (Kg)</th>
                <th>Status</th>
                <th>#</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($keluar as $i => $val)
                <tr>
                    <td>{{$loop->iteration+($keluar->currentpage() - 1) * $keluar->perPage()}}</td>
                    <td>{{ $val->id }}</td>
                    <td>{{ $val->production_code }}</td>
                    <td>{{ date('d/m/y', strtotime($val->production_date)) }}</td>
                    <td>{{ $val->productitems->nama ?? '' }}</td>
                    <td>{{ $val->parting ?? '' }}</td>
                    <td>
                        {{ $val->type }}
                        @if($val->type=="siapkirim")
                            <br> {{$val->no_so}}
                            <br> {{ App\Models\Order::where('id', $val->order_id)->first()->no_do ?? '' }}
                        @endif
                    </td>
                    <td>{{ $val->plastik_group }}</td>
                    <td>{{ $val->sub_item }}</td>
                    <td>{{ $val->productgudang->code ?? ''}}</td>
                    <td class="text-right">{{ $val->qty ?: '0' }}</td>
                    <td class="text-right">{{ $val->berat ?: '0' }}</td>
                    <td>
                        @if($val->status == 2)<div class='status status-danger'>Request Keluar</div>@else <div class='status status-warning'>Keluar</div> @endif
                    </td>
                    <td>
                        <div style="width: 100px">
                            <a href="{{route('warehouse.tracing', $val->id)}}" class="btn btn-sm btn-blue" target="_blank">Detail</a>
                            <a href="javascript:void(0)" type="button" data-toggle="modal" data-target="#editOutbound" class="btn btn-outline-info btnEditOutbound" data-id="{{$val->id}}" data-idabf="{{$val->table_id}}" >Edit</a>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="modal fade" id="editOutbound" aria-labelledby="editLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editLabel">Edit Outbound</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <h5 id="spinerintbound" style="display: none" class="text-center"><i class="fa fa-refresh fa-spin"></i> Loading....</h5>
            <div class="content-outbound"></div>
        </div>
    </div>
</div>

<div id="paginate_keluar">
    {{ $keluar->appends($_GET)->onEachSide(1)->links() }}
</div>

<script>
    $(".btnEditOutbound").click(function () { 
        $('#spinerintbound').show();
        $('.content-outbound').hide();
        var id      = $(this).data("id");
        var idabf   = $(this).data("idabf");
        $.ajax({
            type: "GET",
            url: "{{route('warehouse.edit_inout')}}",
            data: {
                id    : id,
                idabf : idabf
            },
            success: function (data) {
             $(".content-outbound").html(data);   
             $('.content-outbound').show();   
             $('#spinerintbound').hide();
            }
        });
        
    });
</script>

<script>
$('#paginate_keluar .pagination a').on('click', function(e) {
    e.preventDefault();
    showNotif('Menunggu');

    url = $(this).attr('href');
    $.ajax({
        url: url,
        method: "GET",
        success: function(response) {
            $('#warehouse-keluar').html(response);
        }

    });
});
</script>
