<div class="table-responsive table-height">
    <table class="table table-bordered table-striped table-hover table-sticky" border="1">
        <thead>
            <tr>
                <td>No</td>
                <td>Nama</td>
                <td>Kode</td>
                <td>Tanggal Bongkar</td>
                <th>Karung Isi</th>
                <th>Qty/Pcs/Ekor</th>
                <th>Berat (Kg)</th>
                <th>Parting</th>
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
            @foreach ($summary as $no => $item)
                <tr>
                    <th>{{++$no + ($summary->currentpage() - 1) * $summary->perPage()}}</th>
                    <th><div style="width: 100px">
                            {{$item->nama}}
                            <br>{!! $item->item_type ?? '' !!}
                            @if ($item->selonjor)
                                <div class="font-weight-bold text-danger">SELONJOR</div>
                            @endif
                            @if ($item->barang_titipan)
                                <div class="font-weight-bold text-primary">BARANG TITIPAN</div>
                            @endif
                        </div>
                    </th>
                    <th>{{ $item->production_code }}</th>
                    <th>{{ date('d/m/y', strtotime($item->production_date)) }}</th>
                    <td class="text-right">{{ $item->karung_isi }}</td>
                    <td class="text-right">{{ number_format($item->qty_awal ?? '0') }}</td>
                    <td class="text-right">{{ number_format(($item->berat_awal ?? '0'), 2) }}</td>
                    <td>{{$item->parting}}</td>
                    <td>{{ $item->konsumen->nama ?? '' }}</td>
                    <td>{{ $item->sub_item }}</td>
                    <td>{{ $item->plastik_group }}</td>
                    <td>{{ $item->subpack }}</td>
                    <td>{{ $item->asal_abf }}</td>
                    <td>{{ $item->label }}</td>
                    <td>
                        {!! $item->status_gudang ?? '' !!}
                        @if($item->status == 2)<div class='status status-danger'>Request Keluar</div>@else <div class='status status-warning'>Keluar</div> @endif
                        {{-- @if ($item->status != 1)
                            <div style="width: 130px">{{ $item->productgudang->code ?? '' }}</div>
                        @else
                            <div class="form-group">
                                <select name="waretujuan" class="form-input-table" id="waretujuan">
                                    <option value="" disabled selected hidden>Pilih</option>
                                    @foreach ($warehouse as $ware)
                                        <option value="{{ $ware->id }}" @if ($item->gudang_id == $ware->id) selected @endif>{{ $ware->code }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif --}}
                    </td>
                    <td>{{$item->productgudang->code}}</td>
                    {{-- <td>{{$item->production_date}}</td>
                    <td>{{$item->updated_at}}</td> --}}
                    <td>
                        <div style="width: 100px; display: inline-block;">
                            <button href="javascript:void(0)" class="btn btn-sm btn-outline-secondary btnDetailPindah" data-id="{{$item->id}}" data-title="Detail Pindah Gudang" data-toggle="tooltip" data-placement="bottom" title="Detail"><i class="fa fa-eye" aria-hidden="true"></i></button>
                            <button  href="javascript:void(0)" type="button" class="btn btn-sm btn-outline-info btnEditPindah" data-id="{{$item->id}}" data-idabf="{{$item->table_id}}" data-title="Edit Pindah Gudang" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil-square-o"></i></button>
                            <button href="javascript:void(0)" type="button" class="btn btn-sm btn-outline-danger btnHapusPindah" data-id="{{$item->id}}" data-idabf="{{$item->table_id}}" data-title="Delete Pindah Gudang" data-toggle="tooltip" data-placement="bottom" title="Hapus" ><i class="fa fa-trash" aria-hidden="true"></i></button>
                        </div>
                    </td>
                </tr>
            @endforeach
            
        </tbody>
</div>
</table>

<div class="modal fade modal-pindah-gudang" id="editInOut" aria-labelledby="editLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titlePindahGudang"></h5>
                <button type="button" class="close btn-closein" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <h5 id="spinerpindah" style="display: none" class="text-center"><i class="fa fa-refresh fa-spin"></i> Loading....</h5>
            <div class="content-editpindah"></div>
        </div>
    </div>
</div>

<div id="paginate_pindah_gudang">
    {{ $summary->appends($_GET)->onEachSide(1)->links() }}
</div>
<script>
    $('#paginate_pindah_gudang .pagination a').on('click', function(e) {
        e.preventDefault();
        showNotif('Menunggu');
    
        url = $(this).attr('href');
        $.ajax({
            url: url,
            method: "GET",
            success: function(response) {
                $('#result-pindah-gudang').html(response);
            }
    
        });
    });

    // EDIT
    $(".btnEditPindah").click(function (e) { 
        e.preventDefault();
        $('.modal-pindah-gudang').modal('show');
        $("#spinerpindah").show();
        var id = $(this).data('id');
        var title = $(this).data('title');
        $.ajax({
            type: "GET",
            url: "{{ route('pindah.show')}}",
            data: {
                'key' : 'edit',
                id: id,
            },
            success: function (response) {
                $(".content-editpindah").html(response);
                $("#titlePindahGudang").text(title);
                $("#spinerpindah").hide();
            }
        });
        
    });

    // DETAIL
    $(".btnDetailPindah").click(function (e) { 
        e.preventDefault();
        $('.modal-pindah-gudang').modal('show');
        $("#spinerpindah").show();
        var id = $(this).data('id');
        var title = $(this).data('title');
        $.ajax({
            type: "GET",
            url: "{{ route('pindah.show')}}",
            data: {
                'key' : 'detail',
                id: id,
            },
            success: function (response) {
                $(".content-editpindah").html(response);
                $("#titlePindahGudang").text(title);
                $("#spinerpindah").hide();
            }
        });
        
    });
    
    // HAPUS
    $(".btnHapusPindah").click(function (e) { 
        e.preventDefault();
        $('.modal-pindah-gudang').modal('show');
        $("#spinerpindah").show();
        var id = $(this).data('id');
        var title = $(this).data('title');
        $.ajax({
            type: "GET",
            url: "{{ route('pindah.show')}}",
            data: {
                'key' : 'hapus',
                id: id,
            },
            success: function (response) {
                $(".content-editpindah").html(response);
                $("#titlePindahGudang").text(title);
                $("#spinerpindah").hide();
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
        left: 12px;
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
