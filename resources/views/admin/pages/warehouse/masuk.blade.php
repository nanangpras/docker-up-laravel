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
            <a href="{{ route('warehouse.masuk', array_merge(['key' => 'unduh'], $_GET)) }}" class="btn btn-success btn-block rounded-0"><i class="fa fa-file-excel-o"></i> Unduh</a>
        </div>
    </div>
</div>

<div class="table-responsive table-height">
    <table class="table table-bordered table-striped table-hover table-sticky" border="1">
        <thead>
            <tr>
                <td class="stuck">No</td>
                {{-- <td>ID</td> --}}
                <td class="stuck">Nama</td>
                <td class="stuck">Kode</td>
                <td class="stuck">Tanggal Bongkar</td>
                <th>karung Isi</th>
                <th>Qty/Pcs/Ekor</th>
                <th>Berat (Kg)</th>
                <th>Parting</th>
                <th>Customer</th>
                <th>Sub Item</th>
                <th>Packaging</th>
                <th>SubPack</th>
                <th>ABF</th>
                <th>Label</th>
                <th>Status</th>
                <th>Tujuan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($masuk as $val)
            {{-- @php
                $data_abf       = \App\Models\Abf::find($val->table_id);
                $cek_netsuite   = \App\Models\Netsuite::where('document_code', 'like', '%'.$data_abf->id.'%')->where('label', 'like', '%abf%')->get();
                // dd($cek_netsuite);
                @endphp --}}
                <tr>
                    <th class="stuck">{{$loop->iteration+($masuk->currentpage() - 1) * $masuk->perPage()}}</th>
                    {{-- <th>{{ $val->id ?? '#' }}</th> --}}
                    <th class="stuck">
                        <div style="width: 280px">
                            {{ $val->nama ?? '' }}
                            @if ($val->selonjor)
                            <div class="font-weight-bold text-danger">SELONJOR</div>
                            @endif
                            @if ($val->barang_titipan)
                            <div class="font-weight-bold text-primary">BARANG TITIPAN</div>
                            @endif
                        </div>
                    </th>
                    <th class="stuck">{{ $val->production_code }}</th>
                    <th class="stuck">{{ date('d/m/y', strtotime($val->production_date)) }}</th>
                    <td class="text-right">{{ $val->karung_isi }}</td>
                    <td class="text-right">{{ number_format($val->qty_awal ?? '0') }}</td>
                    <td class="text-right">{{ number_format(($val->berat_awal ?? '0'), 2) }}</td>
                    <td>{{ $val->parting }}</td>
                    <td><div style="width: 130px">{{ $val->konsumen->nama ?? "" }}</div></td>
                    <td><div style="width: 130px">{{ $val->sub_item }}</div></td>
                    <td>{{ $val->plastik_group }}</td>
                    <td>{{ $val->subpack }}</td>
                    <td>{{ $val->asal_abf }}</td>
                    <td>{{ $val->label }}</td>

                    <td>{!! $val->status_gudang ?? '' !!}</td>
                    <td>
                        @if ($val->status != 1)
                            <div style="width: 130px">{{ $val->productgudang->code ?? '' }}</div>
                        @else
                            <div class="form-group">
                                <select name="waretujuan" class="form-input-table" id="waretujuan">
                                    <option value="" disabled selected hidden>Pilih</option>
                                    @foreach ($warehouse as $ware)
                                        <option value="{{ $ware->id }}" @if ($val->gudang_id == $ware->id) selected @endif>{{ $ware->code }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </td>
                    <td>
                        <div style="width: 100px">
                            <a href="{{route('warehouse.tracing', $val->id)}}" class="btn btn-sm btn-blue" target="_blank">Detail</a>
                            <a href="javascript:void(0)" type="button" data-toggle="modal" data-target="#editInbound" class="btn btn-outline-info btnEditInbound" data-id="{{$val->id}}" data-idabf="{{$val->table_id}}" >Edit</a>
                            {{-- <button class="btn btn-outline-info" data-toggle="modal" data-target="#edit{{ $val->id }}">Edit</button> --}}
                            @if ($val->status == 1)
                            <button type="submit" class="btn btn-primary btn-sm terimagudang" data-kode="{{ $val->id }}">Terima</button>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="modal fade" id="editInbound" aria-labelledby="editLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editLabel">Edit Inbound</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <h5 id="spinerintbound" style="display: none" class="text-center"><i class="fa fa-refresh fa-spin"></i> Loading....</h5>
            <div class="content-inbound"></div>
        </div>
    </div>
</div>

<div id="paginate_masuk">
    {{ $masuk->appends($_GET)->onEachSide(1)->links() }}
</div>

<script>
    $(".btnEditInbound").click(function () { 
        $('#spinerintbound').show();
        $('.content-inbound').hide();
        // e.preventDefault();
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
             $(".content-inbound").html(data);
             $('.content-inbound').show();   
             $('#spinerintbound').hide();
            }
        });
        
    });
</script>
<script>
$('#paginate_masuk .pagination a').on('click', function(e) {
    e.preventDefault();
    showNotif('Menunggu');

    url = $(this).attr('href');
    $.ajax({
        url: url,
        method: "GET",
        success: function(response) {
            $('#warehouse-masuk').html(response);
        }

    });
});
</script>

<script>
$('.select2').select2({
    theme: 'bootstrap4',
});
</script>

<style>
    .table-sticky>thead>tr>th,
    .table-sticky>thead>tr>td {
        background: #009688;
        color: #fff;
        position: sticky;
    }

    .table-height {
        height: 800px;
        display: block;
        overflow: scroll;
        width: 100%;
    }

    table {
        border-collapse: collapse;
        border-spacing: 0;
    }

    .table-sticky thead {
        position: sticky;
        top: 0px;
        z-index: 1;
    }

    .table-sticky thead td {
        position: sticky;
        top: 0px;
        left: 0;
        z-index: 4;
        background-color: #f9fbfd;
        color: #95aac9;
    }

    .table-sticky tbody th {
        position: sticky;
        left: 0;
        background-color: white;
        z-index: 0;

    }

    /* .table-sticky tbody th {
    position: sticky;
    background-color: #95aac9;
    z-index: 0;
} */

    tbody th.stuck:nth-child(1) {
        left: 0px;
    }
    tbody th.stuck:nth-child(2) {
        left: 42px;
    }



    tbody th.stuck:nth-child(3) {
        left: 330px;
    }
    tbody th.stuck:nth-child(4) {
        left: 380px;
    }



    thead td.stuck:nth-child(1) {
        left: 0px;

    }
    thead td.stuck:nth-child(2) {
        left: 42px;

    }

    thead td.stuck:nth-child(3) {
        left: 330px;

    }
    thead td.stuck:nth-child(4) {
        left: 380px;

    }

    /* thead tr:nth-child(1) th {
    position: sticky; top: 0;
}
thead tr:nth-child(2) th {
    position: sticky; top: 40px;
} */

    /* .table-bordered>thead>tr>th,
.table-bordered>tbody>tr>th,
.table-bordered>thead>tr>td,
.table-bordered>tbody>tr>td {
 border: 1px solid #ddd;
} */

</style>
