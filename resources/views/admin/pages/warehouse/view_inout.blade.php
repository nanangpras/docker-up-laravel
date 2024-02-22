<div class="row">
    <div class="col">
        <div class="form-group">
            <div class="bg-info p-2 text-center text-light font-weight-bold text-uppercase">Total QTY/Pcs/Ekor</div>
            <div class="border p-2 text-center">
                <h5 class="mb-0">{{ number_format($qty) }}</h5>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="form-group">
            <div class="bg-info p-2 text-center text-light font-weight-bold text-uppercase">Total Berat</div>
            <div class="border p-2 text-center">
                <h5 class="mb-0">{{ number_format($kg, 2) }}</h5>
            </div>
        </div>
    </div>

    <div class="col-auto pl-1">
        <div class="form-group">
            <a href="{{ route('warehouse.inout', array_merge(['key' => 'unduh','jenis' => $jenis, 'tanggal_mulai' => $mulai , 'tanggal_akhir' => $sampai])) }}" class="btn btn-success btn-block rounded-0"><i class="fa fa-file-excel-o"></i> Unduh</a>
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
                @if ($jenis == 'warehouse_keluar')
                <td class="stuck">Tanggal DO</td>
                @else
                <td class="stuck">Tanggal Bongkar</td>
                @endif
                <th>karung Isi</th>
                <th>Qty/Pcs/Ekor</th>
                <th>Berat (Kg)</th>
                <th>Parting</th>
                @if ($jenis == 'warehouse_keluar')
                    <th>No Document</th>
                @endif
                <th>Stock Customer</th>
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
                            <br>{!! $val->item_type ?? '' !!}
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
                    @if ($jenis == 'warehouse_keluar')
                    <td>
                        
                        {{-- <small class="text-uppercase">{{ $val->type }}</small> --}}
                        @if($val->type=="siapkirim")
                            <small class="text-uppercase">{{$val->no_so}}</small>
                            <br> <small class="text-uppercase">{{ App\Models\Order::where('id', $val->order_id)->first()->no_do ?? '' }}</small>
                            <br>
                            {{-- <small class="text-uppercase">Customer DO :</small> --}}
                        
                            <br><span class="status status-success mt-1 small">{{ App\Models\Order::where('id', $val->order_id)->first()->nama ?? ''  }}</span>
                        @endif
                    </td>
                    @endif
                    <td><div style="width: 130px">{{ $val->konsumen->nama ?? '' }}</div></td>

                    <td><div style="width: 130px">{{ $val->sub_item }}</div></td>
                    <td>{{ $val->plastik_group }}</td>
                    <td>{{ $val->subpack }}</td>
                    <td>{{ $val->asal_abf }}</td>
                    <td>{{ $val->label }}</td>

                    <td>
                        @if ($jenis == 'warehouse_masuk')
                            {!! $val->status_gudang ?? '' !!}
                        @else
                            @if($val->status == 2)<div class='status status-danger'>Request Keluar</div>@else <div class='status status-warning'>Keluar</div> @endif
                        @endif
                    </td>
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
                        <div style="width: 100px; display: inline-block;">
                            <a href="{{route('warehouse.tracing', $val->id)}}" class="btn btn-sm btn-blue" target="_blank">Detail</a>
                            <a href="javascript:void(0)" type="button" class="btn btn-sm btn-outline-info btnEditInbound" data-id="{{$val->id}}" data-idabf="{{$val->table_id}}" >Edit</a>
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

<div class="modal fade inbound-modal" id="editInOut" aria-labelledby="editLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editLabelInOut">Edit Inbound</h5>
                <button type="button" class="close btn-closein" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <h5 id="spinerintbound" style="display: none" class="text-center"><i class="fa fa-refresh fa-spin"></i> Loading....</h5>
            <div class="content-inbound"></div>
        </div>
    </div>
</div>

<div class="modal fade outbound-modal" id="editInOut" aria-labelledby="editLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editLabel">Edit Outbound</h5>
                <button type="button" class="close btn-closeout" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <h5 id="spineroutbound" style="display: none" class="text-center"><i class="fa fa-refresh fa-spin"></i> Loading....</h5>
            <div class="content-outbound"></div>
        </div>
    </div>
</div>

<div id="paginate_masuk">
    {{ $masuk->appends($_GET)->onEachSide(1)->links() }}
</div>

<script>
    var hash = window.location.hash;
    
    $(".btnEditInbound").click(function () { 

        if (hash == "#custom-tabs-three-masuk") {
            $('.inbound-modal').modal('show');
            $('.btn-closein').click(function (e) { 
                e.preventDefault();
                $('.inbound-modal').modal('hide');
            });

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
                    if (hash == "#custom-tabs-three-masuk") {
                        $(".content-inbound").html(data);
                        $('.content-inbound').show();   
                        $('#spinerintbound').hide();
                        $('.select2').each(function() {
                            $(this).select2({
                            theme: 'bootstrap4',
                            dropdownParent: $(this).parent()
                            });
                        })
                    }
                }
            });
            
        }
        if (hash == "#custom-tabs-three-keluar") {
            $('.outbound-modal').modal('show');
            $('.btn-closeout').click(function (e) { 
                e.preventDefault();
                $('.outbound-modal').modal('hide');
            });

            $('#spineroutbound').show();
            $('.content-outbound').hide();
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
                    if (hash == "#custom-tabs-three-keluar") {
                        $(".content-outbound").html(data);
                        $('.content-outbound').show();   
                        $('#spineroutbound').hide();
                        $('.select2').each(function() {
                            $(this).select2({
                            theme: 'bootstrap4',
                            dropdownParent: $(this).parent()
                            });
                        })
                    }
                }
            });
        }
        
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
            $('#warehouse-keluar').html(response);
        }

    });
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
