@extends('admin.layout.template')

@section('title', 'Detail Item SOH')

@section('content')
<div class="row mb-4">
    <div class="col">
        <a href="{{ route('warehouse.index') }}" class="btn btn-outline btn-sm btn-back"> <i
                class="fa fa-arrow-left"></i>
            Back</a>
    </div>
    <div class="col text-center">
        <b>DETAIL ITEM SOH</b>
    </div>
    <div class="col"></div>
</div>

@if (count($history) > 0)
<section class="panel">
    <div class="card-body">
        <div class="table-responsive">
            <h6>History Pembagian Data</h6>
            <table class="table default-table mt-4" width="100%">
                <thead>
                    <tr>
                        <th rowspan="2">#</th>
                        <th rowspan="2">Gudang</th>
                        <th rowspan="2">Konsumen / Sub Item</th>
                        <th rowspan="2">Item</th>
                        <th colspan="2" class="text-center">Tanggal</th>
                        <th colspan="2" class="text-center">Kemasan</th>
                        <th rowspan="2">Asal ABF</th>
                        <th colspan="2" class="text-center">Qty</th>
                        <th rowspan="2">Pallete</th>
                        <th rowspan="2">Expired</th>
                        <th rowspan="2">Stock</th>
                        <th rowspan="2">Type</th>
                        <th rowspan="2"></th>
                    </tr>
                    <tr>
                        <th>Produksi</th>
                        <th>Kemasan</th>
                        <th>Packaging</th>
                        <th>SubPack</th>
                        <th>Qty</th>
                        {{-- <th>qty Sisa</th> --}}
                        <th>Berat</th>
                        {{-- <th>berat Sisa</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach ($history as $item)
                    @php
                    $result = App\Models\Product_gudang::where('table_name', 'product_gudang')
                    ->where('table_id', $item->id)
                    ->get();
                    @endphp
                    <tr>
                        <td>{{$item->id}}</td>
                        <td>
                            @foreach ($gudang as $row)
                            @if ($row->id == $item->gudang_id)
                            {{$row->code}}
                            @endif
                            @endforeach
                        </td>
                        <td>{{$item->konsumen->nama ?? '#'}} <br> {{$item->sub_item}}</td>
                        <td>{{ $item->productitems->nama ?? '' }}</td>
                        <td>{{$item->production_date}}</td>
                        <td>{{$item->tanggal_kemasan ?? ''}}</td>
                        <td>{{$item->packaging ?? ''}}</td>
                        <td>{{$item->subpack ?? ''}}</td>
                        <td>{{$item->asal_abf ?? ''}}</td>
                        <td>{{ number_format($item->qty_awal ?? '0') }}</td>
                        {{-- <td>{{ number_format($item->qty ?? '0') }}</td> --}}
                        <td>{{ number_format($item->berat_awal ?? '0') }}</td>
                        {{-- <td>{{ number_format($item->berat ?? '0') }}</td> --}}
                        <td>{{$item->palete}}</td>
                        <td>{{$item->expired}}</td>
                        <td>{{$item->stock_type}}</td>
                        <td>{{$item->type}}</td>
                        <td><button class="btn btn-primary btn-sm" data-toggle="collapse"
                                data-target="#collapse{{ $item->id }}" aria-expanded="true"
                                aria-controls="collapse{{ $item->id }}">Detail</button></td>
                    </tr>
                    <td colspan="18">
                        <div id="collapse{{ $item->id }}" class="collapse" aria-labelledby="headingOne"
                            data-parent="#accordionListPO">
                            <table>
                                <thead>
                                    <tr>
                                        <th rowspan="2">#</th>
                                        <th rowspan="2">Gudang</th>
                                        <th rowspan="2">Konsumen / Sub Item</th>
                                        <th rowspan="2">Item</th>
                                        <th colspan="2" class="text-center">Tanggal</th>
                                        <th colspan="2" class="text-center">Kemasan</th>
                                        <th rowspan="2">Asal ABF</th>
                                        <th colspan="2" class="text-center">Qty</th>
                                        <th rowspan="2">Pallete</th>
                                        <th rowspan="2">Expired</th>
                                        <th rowspan="2">Stock</th>
                                        <th rowspan="2">Type</th>
                                        <th rowspan="2"></th>
                                    </tr>
                                    <tr>
                                        <th>Produksi</th>
                                        <th>Kemasan</th>
                                        <th>Packaging</th>
                                        <th>SubPack</th>
                                        <th>qty</th>
                                        {{-- <th>qty Sisa</th> --}}
                                        <th>berat</th>
                                        {{-- <th>berat Sisa</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($result as $item)

                                    <tr>
                                        <td>{{$item->id}}</td>
                                        <td>
                                            @foreach ($gudang as $row)
                                            @if ($row->id == $item->gudang_id)
                                            {{$row->code}}
                                            @endif
                                            @endforeach
                                        </td>
                                        <td>{{$item->konsumen->nama ?? '#'}} <br> {{$item->sub_item}}</td>
                                        <td>{{ $item->productitems->nama ?? '' }}</td>
                                        <td>{{$item->production_date}}</td>
                                        <td>{{$item->tanggal_kemasan ?? ''}}</td>
                                        <td>{{$item->packaging ?? ''}}</td>
                                        <td>{{$item->subpack ?? ''}}</td>
                                        <td>{{$item->asal_abf ?? ''}}</td>
                                        <td>{{ number_format($item->qty_awal ?? '0') }}</td>
                                        {{-- <td>{{ number_format($item->qty ?? '0') }}</td> --}}
                                        <td>{{ number_format($item->berat_awal ?? '0') }}</td>
                                        {{-- <td>{{ number_format($item->berat ?? '0') }}</td> --}}
                                        <td>{{$item->palete}}</td>
                                        <td>{{$item->expired}}</td>
                                        <td>{{$item->stock_type}}</td>
                                        <td>{{$item->type}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </td>

                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@endif
<section class="panel px-2 py-2">
    <div class="row mb-3">
        <div class="col">
            <label>Tanggal Awal</label>
            @if (env('NET_SUBSIDIARY', 'CGL')=='CGL')
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif class="form-control" id="tanggal_soh_awal" value="2023-05-27" min="2023-05-27">
            @else
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif class="form-control" id="tanggal_soh_awal" value="2023-05-05" min="2023-05-05">
            @endif
        </div>
        <div class="col">
            <label>Tanggal Akhir</label>
            @if (env('NET_SUBSIDIARY', 'CGL')=='CGL')
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif class="form-control" id="tanggal_soh_akhir" value="{{ $tanggal }}" min="2023-05-27">
            @else
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif class="form-control" id="tanggal_soh_akhir" value="{{ $tanggal }}" min="2023-05-05">
            @endif
        </div>
    </div>
    <div class="row mt-1">
        <div class="col">
            <Label>Status</Label>
            <select class="form-control select2" id="status_detail_soh">
                <option value="semua">Masuk & Keluar</option>
                <option value="masuk">Masuk</option>
                <option value="keluar">Keluar</option>
            </select>
        </div>
    </div>
</section>
<br>
<div id="loadDataDetailSOH">

</div>

{{-- MODAL TAMBAH PLASTIK GROUP --}}
<div class="modal fade" id="plastikModal" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Plastik Group</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{-- <form action="{{ route('abf.storetimbang') }}" method="post"> --}}
                {{-- @csrf --}}
                <input type="hidden" name="key" id="keyPlastikGroup" value="plastikGroup">
                <div class="modal-body">
                    <div class="form-group">
                        Nama Plastik Group
                        <input type="text" id="plastikGroup" name="plastikGroup" placeholder="Tuliskan Plastik Group"
                            class="form-control" autocomplete="off" required>
                    </div>
                    <section class="panel">
                        <div class="card-body">
                            <div id="tablePlastikGroup">

                            </div>
                        </div>
                    </section>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary submitPlastikGroup">Submit</button>
                </div>
                {{--
            </form> --}}
        </div>
    </div>
</div>
{{-- END PLASTIK GROUP --}}

{{-- MODAL TAMBAH ITEM NAME --}}
<div class="modal fade" id="modalSubItem" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Item Name</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{-- <form action="{{ route('abf.storetimbang') }}" method="post"> --}}
                {{-- @csrf --}}
                <input type="hidden" name="key" id="key" value="itemname">
                <div class="modal-body">

                    <div class="form-group">
                        PENCARIAN
                        <input type="text" id="searchItemName" name="searchItemName" placeholder="Tulis Pencarian"
                            class="form-control" autocomplete="off">
                    </div>

                    <section class="panel">
                        <div class="card-body">
                            <div id="tableListItemName">

                            </div>
                        </div>
                    </section>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary submitItemName">Submit</button>
                </div>
                {{--
            </form> --}}
        </div>
    </div>
</div>
{{-- END ITEM NAME --}}

{{-- SCRIPT PLASTIK GROUP --}}
<script>
    var loadPlastikGroup = `    <div class="form-group">
                                        <select name="plastik_group" id="selectPlastikGroup" data-placeholder="Pilih Plastik" class="form-control select2 mt-2" required>
                                            <option value=""></option>
                                            @foreach ($plastikGroup as $plastik)
                                                <option value="{{ $plastik->id }}">{{ $plastik->data }}</option>
                                            @endforeach
                                        </select>
                                    </div>`;

        $("#loadingPlastikGroup").attr('style', 'display: block');
        $('#loadPlastikGroup').append(loadPlastikGroup).after($("#loadingPlastikGroup").attr('style', 'display: none'));
        


        $('.submitPlastikGroup').on('click', function(){
            var key             =   $("#keyPlastikGroup").val() ;
            var plastikGroup    =   $("#plastikGroup").val() ;
            $("#loadingPlastikGroup").attr('style', 'display: block');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('abf.storetimbang') }}",
                data: {
                    key,
                    plastikGroup
                },
                method: 'POST',
                success: function(data){
                    console.log(data)
                    if (data.status == '200') {
                        showNotif(data.msg)
                        $('#selectPlastikGroup').append('<option value="' + data.id + '" selected="selected">' + plastikGroup + '</option>'); 
                        $("#loadingPlastikGroup").attr('style', 'display: none')
                        $("#plastikGroup").val('');
                        $("#tablePlastikGroup").load("{{ route('abf.index') }}?key=loadPlastikGroupPaginate");
                        $('#plastikModal').modal('hide');
                    } else {
                        showAlert(data.msg)
                        $("#loadingPlastikGroup").attr('style', 'display: none')
                    }
                }
            })
        })


        $("#tablePlastikGroup").load("{{ route('abf.index') }}?key=loadPlastikGroupPaginate");

</script>
{{-- END SCRIPT PLASTIK GROUP --}}

{{-- SCRIPT PENCARIAN ITEM NAME--}}
<script>
    $("#searchItemName").on('keyup', function() {
            var itemName =  encodeURIComponent($(this).val());
            $("#tableListItemName").load("{{ route('abf.index') }}?key=loadItemNamePaginate&subKey=searchItemName&search="+itemName);
    
        })
</script>

{{-- END SCRIPT PENCARIAN ITEM NAME --}}

{{-- SCRIPT ITEM NAME --}}
<script>
    var loadItemName = `    <div class="form-group">
                                        <select name="subitem" id="selectSubItem" data-placeholder="Pilih Item Name" class="form-control select2 mt-2" required>
                                            <option value=""></option>
                                            <option value="NONE">NONE</option>
    
                                            @foreach ($sub_item_name as $name)
                                                <option value="{{ $name->id }}">{{ $name->data }}</option>
                                            @endforeach
                                        </select>
                                    </div>`;
    
            $("#loadingItemName").attr('style', 'display: block');
            $('#loadItemName').append(loadItemName).after($("#loadingItemName").attr('style', 'display: none'));
            
    
    
            $('.submitItemName').on('click', function(){
                var key         =   $("#key").val() ;
                var itemname    =   $("#itemname").val() ;
                $("#loadingItemName").attr('style', 'display: block');
    
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ route('abf.storetimbang') }}",
                    data: {
                        key,
                        itemname
                    },
                    method: 'POST',
                    success: function(data){
                        console.log(data)
                        if (data.status == '200') {
                            showNotif(data.msg)
                            $('#selectSubItem').append('<option value="' + data.id + '" selected="selected">' + itemname + '</option>'); 
                            $("#loadingItemName").attr('style', 'display: none')
                            $("#itemname").val('');
                            $("#tableListItemName").load("{{ route('abf.index') }}?key=loadItemNamePaginate");
                            $('#exampleModal').modal('hide');
                        } else {
                            showAlert(data.msg)
                            $("#loadingItemName").attr('style', 'display: none')
                        }
                    }
                })
            })
    
    
            $("#tableListItemName").load("{{ route('abf.index') }}?key=loadItemNamePaginate");
    
</script>
{{-- END SCRIPT ITEM NAME --}}


{{-- SCRIPT PENCARIAN DETAIL --}}

<script>
    $("#tanggal_soh_awal").on('change', function() {
        searchDetailSOH()
    })

    $("#tanggal_soh_akhir").on('change', function() {
        searchDetailSOH()
    })

    $("#status_detail_soh").on('change', function() {
        searchDetailSOH()
    })

    searchDetailSOH()

    function searchDetailSOH() {
        var gudang              =   "{{ $gudangReq }}"
        var customer            =   encodeURIComponent("{{ $customerReq }}")
        var packagingReq        =   encodeURIComponent("{{ $packagingReq }}")
        var item_name           =   encodeURIComponent("{{ $item_name }}")
        var partingReq          =   encodeURIComponent("{{ $partingReq }}")
        var sub_itemReq         =   encodeURIComponent("{{ $sub_itemReq }}")
        var customerReq         =   encodeURIComponent("{{ $customerReq }}")
        var grade_itemReq       =   encodeURIComponent("{{ $grade_itemReq }}")
        var tanggal_soh_awal    =   $("#tanggal_soh_awal").val();
        var tanggal_soh_akhir   =   $("#tanggal_soh_akhir").val();
        var status_detail_soh   =   $("#status_detail_soh").val();
        $("#loadDataDetailSOH").load("{{ route('warehouse.soh_detail') }}?key=searchDetailSOH&tanggal_soh_awal="+tanggal_soh_awal+"&tanggal_soh_akhir="+tanggal_soh_akhir+"&status_detail_soh="+status_detail_soh
        +"&gudang="+gudang+"&item="+item_name+"&plastik_group="+packagingReq+"&parting="+partingReq+"&sub_item="+sub_itemReq+"&customer="+customerReq+"&grade_item="+grade_itemReq)
    }

</script>


{{-- END SCRIPT PENCARIAN DETAIL --}}
@stop