<div class="modal-body modalInbound">
    <input type="hidden" value="{{$data_edit->id}}">
    <div class="form-group">
        Nama Item
        @if(User::setIjin('superadmin'))
        <select name="namaitem" class="form-control selectInbound namaItem{{ $data_edit->id }}"
            data-placeholder="Pilih Item" data-width="100%" required>
            <option value=""></option>
            @foreach ($item_list as $item)
            <option value="{{ $item->id }}" {{$item->id == $data_edit->product_id ? 'selected' : ''}}>{{ $item->nama }}
            </option>
            @endforeach
        </select>
        @else
        <input type="text" id="namaitem" class="form-control" value="{{ $data_edit->productitems->nama }}" readonly>
        @endif
    </div>

    <div class="form-group plastik">
        Packaging
        <select name="packaging" data-placeholder="Pilih Item Name" data-width="100%"
            class="form-control selectInbound selectPackaging{{ $data_edit->id }}" required>
            @foreach ($plastik as $p)
            <option value="{{ $p->nama }}" {{$data_edit->packaging == $p->nama ? 'selected' : ''}}>{{ $p->nama }} -
                {{ $p->subsidiary }}{{ $p->netsuite_internal_id }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        Sub Packaging
        <select name="subpack" class="form-control selectInbound mt-2 subpack{{ $data_edit->id }}" data-width="100%"
            required>
            <option value="NONE" {{$data_edit->subpack == 'NONE' ? 'selected' : ''}}>NONE</option>
            <option value="KARUNG MERAH" {{$data_edit->subpack == 'KARUNG MERAH' ? 'selected' : ''}}>KARUNG MERAH
            </option>
            <option value="KARUNG HIJAU" {{$data_edit->subpack == 'KARUNG HIJAU' ? 'selected' : ''}}>KARUNG HIJAU
            </option>
            <option value="KARUNG POLOS" {{$data_edit->subpack == 'KARUNG POLOS' ? 'selected' : ''}}>KARUNG POLOS
            </option>
            <option value="KARTON" {{$data_edit->subpack == 'KARTON' ? 'selected' : ''}}>KARTON</option>
            <option value="POLOS" {{$data_edit->subpack == 'POLOS' ? 'selected' : ''}}>POLOS</option>
        </select>
    </div>
    <div class="form-group plastikgroup">
        <div class="small mb-2">Plastik (AVIDA,POLOS,MEYER,MOJO)</div>
        <button type="button" class="btn btn-outline-success btn-sm mb-1" data-toggle="modal"
            data-target="#plastikModal">Tambah Plastik Group</button>
        <select name="plastik" data-placeholder="Pilih Plastik"
            class="form-control selectInbound mt-2 plastik-group plastik{{ $data_edit->id }}" required>
            <option value=""></option>
            @foreach ($plastikGroup as $plastik)
            <option value="{{ $plastik->data }}" {{ $plastik->data == $data_edit->plastik_group ? 'selected' : ''}}>{{ $plastik->data }}</option>
            @endforeach
        </select>
        {{-- <input type="text" name="plastik[]" id="plastik{{ $val->id }}" class="form-control" max=""
            placeholder="Isi Karung" value="{{ $val->plastik_group }}" autocomplete="off"> --}}
    </div>
    <div class="form-group">
        <div class="small mb-2">Customer</div>
        <select name="customer" data-placeholder="Pilih Customer"
            class="form-control selectInbound mt-2 customer-group selectCustomer{{ $data_edit->id }}" required>
            <option value="NONE">NONE</option>
            @foreach ($customer as $cst)
            <option value="{{ $cst->id }}" {{$data_edit->customer_id == $cst->id ? 'selected' : ''}}>{{ $cst->nama }} -
                {{ $cst->kode }}</option>
            @endforeach
        </select>
    </div>
    <input type="hidden" class="cek_wo" value="{{$cek_netsuite_wo}}">
    <input type="hidden" class="user" value="{{App\Models\User::setIjin('superadmin')}}">
    {{-- {{$cek_netsuite_wo}} --}}
    {{-- @if ($cek_netsuite)
    <p>tidak bisa di edit lagi</p>
    @else --}}
    <div class="row">

        <div class="col karung">
            <div class="small mb-2">Karung</div>
            <select name="karung" data-placeholder="Pilih Item Name"
                class="form-control selectInbound mt-2 karung{{ $data_edit->id }}" required>
                <option value="Curah">None</option>
                @foreach ($karung as $krg)
                <option value="{{ $krg->sku }}" {{ $krg->sku == $data_edit->karung ? 'selected' : ''}}>{{ $krg->nama }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col">
            <div class="form-group">
                <div class="small mb-2">Qty Karung</div>
                <input type="number" name="karung_qty" class="form-control karung_qty{{ $data_edit->id }}" max=""
                    placeholder="Qty" value="{{ $data_edit->karung_qty }}" autocomplete="off">
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <div class="small mb-2">Isi Karung</div>
                <input type="number" name="karung_isi[]" class="form-control karung_isi{{ $data_edit->id }}" max=""
                    placeholder="Isi Karung" value="{{ $data_edit->karung_isi }}" autocomplete="off">
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="small mb-2">Sub Item / Keterangan</div>
        <button type="button" class="btn btn-outline-success btn-sm mb-2" data-toggle="modal"
            data-target="#modalSubItemInOut">Tambah Item Name</button>
        <select name="subitem" data-placeholder="Pilih Item Name"
            class="form-control selectInbound sub_item{{ $data_edit->id }}">
            <option value=""></option>
            <option value="NONE" {{ $data_edit->sub_item == 'NONE' ? 'selected' : '' }}>NONE</option>
            @foreach ($sub_item as $name)
            <option value="{{ $name->data }}" {{ $name->data == $data_edit->sub_item ? 'selected' : '' }}>{{ $name->data }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        Tanggal
        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01" @endif
            class="form-control production_date{{ $data_edit->id }}" id="production_date"
            value="{{ $data_edit->production_date }}">
    </div>

    <div class="row">
        <div class="col pr-1">
            <div class="form-group">
                Qty Awal
                <input type="number" value="{{ $data_edit->qty_awal }}" class="form-control qtyAwal{{ $data_edit->id }}"
                    id="qtyAwal" @if(!User::setIjin('superadmin')) readonly @endif>
            </div>
        </div>
        <div class="col pl-1">
            <div class="form-group">
                Berat Awal
                <input type="number" value="{{ $data_edit->berat_awal }}"
                    class="form-control beratAwal{{ $data_edit->id }}" id="beratAwal" step="0.01" @if(!User::setIjin('superadmin')) readonly @endif>
            </div>
        </div>
    </div>

    <div class="form-group">
        Parting
        <input type="number" value="{{ $data_edit->parting }}" class="form-control parting_inbound{{ $data_edit->id }}"
            id="parting_inbound" step="0.01">
    </div>

    <div class="row">
        <div class="col pr-1">
            <div class="form-group">
                Lokasi
                <select data-width="100%" class="form-control selectInbound lokasi{{ $data_edit->id }}">
                    @foreach ($warehouse as $row)
                    <option value="{{ $row->id }}" {{ $data_edit->gudang_id == $row->id ? 'selected' : '' }}>{{ $row->code }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col pl-1">
            <div class="form-group">
                ABF
                <select data-width="100%" class="form-control selectInbound abf{{ $data_edit->id }}">
                    <option value="abf_1" {{ $data_edit->asal_abf == 'abf_1' ? 'selected' : '' }}>ABF 1</option>
                    <option value="abf_2" {{ $data_edit->asal_abf == 'abf_2' ? 'selected' : '' }}>ABF 2</option>
                    <option value="abf_3" {{ $data_edit->asal_abf == 'abf_3' ? 'selected' : '' }}>ABF 3</option>
                    <option value="abf_4" {{ $data_edit->asal_abf == 'abf_4' ? 'selected' : '' }}>ABF 4</option>
                </select>
            </div>
        </div>
    </div>

    <div class="form-group">
        <input type="checkbox" id="titipan" {{ $data_edit->barang_titipan ? 'checked' : '' }}>
        <label for="titipan">Barang Titipan</label>
    </div>
    {{-- @endif --}}

</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary btn-close closeBtnInout closeBtnInout" data-dismiss="modal"
        id="btnCloseInOut" id="btnCloseInOut">Close</button>
    <button type="button" data-id="{{$data_edit->id}}" class="btn btn-primary update_inbound">Save</button>
</div>

{{-- MODAL TAMBAH PLASTIK GROUP --}}
<div class="modal fade" id="plastikModal" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Plastik Group</h5>
                <button type="button" class="close" id="close-plastik" aria-label="Close">
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
                    <button type="button" class="btn btn-secondary" id="close-button-plastik">Close</button>
                    <button type="submit" class="btn btn-primary submitPlastikGroup">Submit</button>
                </div>
                {{--
            </form> --}}
        </div>
    </div>
</div>
{{-- END PLASTIK GROUP --}}

{{-- MODAL TAMBAH ITEM NAME --}}
<div class="modal fade" id="modalSubItemInOut" aria-labelledby="exampleModalLabel" aria-hidden="true"
    data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Item Name</h5>
                {{-- <button type="button" class="close" id="btn-close-item" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button> --}}
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
                    <button type="button" class="btn btn-secondary" id="exit-btnitem"
                        data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary submitItemName">Submit</button>
                </div>
                {{--
            </form> --}}
        </div>
    </div>
</div>
{{-- END ITEM NAME --}}


{{-- <script>
    $(".btnEditInbound").click(function () { 
        $('#spinerintbound').show();
        $('.content-inbound').hide();
        $('#plastikModal').hide();
        // e.preventDefault();
        var id      = $(this).data("id");
        var idabf   = $(this).data("idabf");
        $.ajax({
            type: "GET",
            url: "{{route('abf.index')}}",
            data: {
                'key' : 'loadPlastikGroupPaginate',
            },
            success: function (data) {
             $(".content-inbound").html(data);
             $('.content-inbound').show();   
             $('#spinerintbound').hide();
             $('#plastikModal').hide();
            }
        });
        
    });
</script> --}}

{{-- SCRIPT PLASTIK GROUP --}}
<script>
    var loadPlastikGroup = `    <div class="form-group">
                                    <select name="plastik_group" data-placeholder="Pilih Plastik" class="form-control selectInbound mt-2 selectPlastikGroup" required>
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

    $('#close-button-plastik, #close-plastik').click(function (e) { 
        e.preventDefault();
        $('#plastikModal').modal('hide');
    });

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

        $('#btn-close-item, #exit-btnitem').click(function (e) { 
            e.preventDefault();

            $('#modalSubItemInOut').remove();
            $('#modalSubItemInOut').modal().hide();
            
        });


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
                        $('#modalSubItemInOut').modal('hide');
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

<script>
    $(document).ready(function() {

        $('.selectInbound').each(function() {
            $(this).select2({
            theme: 'bootstrap4',
            dropdownParent: $(this).parent()
            });
        })
    });
    $("#btnCloseInOut").click(function (e) { 
        e.preventDefault();
        $('.inbound-modal').modal('hide');
        $('.outbound-modal').modal('hide');
    });
    var cekwo = $(".cek_wo").val();
    var user  = $(".user").val();
    if (cekwo !== '') {
        $(".namaitem").prop('disabled', true);
        $(".selectPackaging").prop('disabled', true);
        $(".subpack").prop('disabled', true);
        $(".sub_item").prop('disabled', true);
        $("#production_date").prop('disabled', true);
        $("#qty").prop('disabled', true);
        $("#berat").prop('disabled', true);
        $("#qtyAwal").prop('disabled', true);
        $("#beratAwal").prop('disabled', true);
        $(".lokasi").prop('disabled', true);
        $(".abf").prop('disabled', true);
        $("#titipan").prop('disabled', true);
    }
    if (cekwo !== '' && user == '1') {
        $(".namaitem").prop('disabled', false);
        $(".selectPackaging").prop('disabled', false);
        $(".subpack").prop('disabled', false);
        $(".sub_item").prop('disabled', false);
        $("#production_date").prop('disabled', false);
        $("#qty").prop('disabled', false);
        $("#berat").prop('disabled', false);
        $("#qtyAwal").prop('disabled', false);
        $("#beratAwal").prop('disabled', false);
        $(".lokasi").prop('disabled', false);
        $(".abf").prop('disabled', false);
        $("#titipan").prop('disabled', false);
    }
    
    var hash = window.location.hash;
$(".update_inbound").on('click', function() {
    // YANG PAKE SELECT2
    var id                  =   $(this).data("id") ;
    var namaItem            =   $('.namaItem'+id).val();
    console.log(namaItem)
    var sub_item            =   $(".sub_item"+id).val() ;
    var lokasi_dg           =   $(".lokasi"+id).val() ;
    var abf                 =   $(".abf"+id).val() ;
    var customer            =   $(".selectCustomer"+id).val();
    var subpack             =   $(".subpack"+id).val();
    var plastik             =   $(".plastik"+id).val();
    var karung              =   $(".karung"+id).val();
    var selectPackaging     =   $(".selectPackaging"+id).val();
    
    
    // YANG GAPAKE SELECT2
    var karung_isi          =   $(".karung_isi"+id).val();
    // var qty                 =   $(".qty"+id).val() ;
    // var berat               =   $(".berat"+id).val() ;
    var qtyAwal             =   $(".qtyAwal"+id).val() ;
    var beratAwal           =   $(".beratAwal"+id).val() ;
    var titipan             =   $(".titipan" + ":checked"+id).val() ;
    var tgl_prod            =   $(".production_date"+id).val();
    var karung_qty          =   $(".karung_qty"+id).val();
    var parting             =   $(".parting_inbound"+id).val();

    var mulai               =   $("#tanggal_mulai_inbound").val() ;
    var akhir               =   $("#tanggal_akhir_inbound").val() ;
    var lokasi              =   $("#lokasi_gudang_inbound").val() ;
    var filter              =   encodeURIComponent($("#filter_stock_inbound").val());

        // $(".update_inbound").hide() ;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

    // console.log(namaItem)
    // console.log(sub_item)
    // console.log(lokasi_dg)
    // console.log(abf)
    // console.log(customer)
    // console.log(subpack)
    // console.log(plastik)
    // console.log(karung)
    // console.log(selectPackaging)
    // console.log(karung_isi)
    // console.log(qtyAwal)
    // console.log(beratAwal)
    // console.log(titipan)
    // console.log(tgl_prod)
    // console.log(karung_qty)
    // console.log(parting)

    $.ajax({
        url: "{{ route('warehouse.update_stock') }}",
        method: "PATCH",
        data: {
            id          :   id ,
            sub_item    :   sub_item ,
            lokasi      :   lokasi_dg ,
            abf         :   abf ,
            titipan     :   titipan ,
            tgl_produksi:   tgl_prod,
            jenis         : 'warehouse_inout',
            customer,
            plastik,
            karung,
            qtyAwal,
            beratAwal,
            karung_isi,
            subpack,
            karung_qty,
            selectPackaging,
            parting,
            namaItem
            

            },
            success: function(data) {
                console.log(data)
                if (data.status == 400) {
                    showAlert(data.msg) ;
                } else {
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open');
                    // $('#inbound-modal').hide();
                    showNotif(data.msg);
                    if(hash === "#custom-tabs-three-masuk"){
                        LoadDataInbound();
                        // var jenis = "warehouse_masuk"
                        // $("#warehouse-masuk").load("{{ route('warehouse.inout') }}?tanggal_mulai=" + mulai + "&tanggal_akhir=" + akhir + "&jenis=" + jenis + "&id="+id ) ;
                    }
                    if(hash === "#custom-tabs-three-keluar"){
                        // var jenis = "warehouse_keluar"
                        LoadDataOutbound();
                        // $("#warehouse-keluar").load("{{ route('warehouse.inout') }}?tanggal_mulai=" + mulai + "&tanggal_akhir=" + akhir + "&jenis=" + jenis + "&id="+id ) ;
                    }
                }
                $(".update_inbound").show();
            }
        });
    })
</script>