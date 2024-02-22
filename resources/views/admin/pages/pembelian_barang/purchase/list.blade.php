@if (COUNT($data))
<table class="table default-table">
    <thead>
        <tr>
            <th>NoPR</th>
            <th>Divisi</th>
            <th>SKU</th>
            <th>Item</th>
            <th>Qty</th>
            <th>Harga</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $row)
        <tr>
            <td>{{ $row->pembelian->no_pr ?? "#" }}</td>
            <td>{{ $row->pembelian->divisi ?? "#" }}</td>
            <td>{{ $row->item->sku }}</td>
            <td>{{ $row->item->nama }}</td>
            <td>{{ number_format($row->qty) }}</td>
            <td class="text-right">{{ number_format($row->harga, 2) }}</td>
            <td style="width:70px;">
                <i class="cursor hapus_item fa fa-trash text-danger mr-3" data-id="{{ $row->id }}"></i>
                <i class="fa fa-edit text-primary cursor" data-toggle="modal" data-target="#editPurchase" data-id="{{ $row->id }}" onclick="editPurchase($(this).data('id'))"></i>
            </td>
        </tr>
        @if($row->item->category_id<23)
        <tr>
            <td>PO AYAM</td>
            <td>Berat DO {{$row->berat}}Kg</td>
            <td>Ukuran Ayam : {{$row->ukuran_ayam}}</td>
            <td>Jumlah DO : {{$row->jumlah_do}}</td>
            <td>Harga Cetakan : {{$row->harga_cetakan}}</td>
            <td>Gudang : {{$row->gudang}}</td>
        </tr>
        @endif
        @endforeach
    </tbody>
</table>

@if (Session::get('subsidiary') == 'EBA')
<div class="form-group">
    <div class="border p-2">
        <label for="ongkir">Ongkos Kirim</label>
        <div class="input-group mb-2">
            <div class="input-group-prepend">
            <div class="input-group-text">Rp</div>
            </div>
            <input type="number" id="ongkir" autocomplete="off" min="0" step="0.01" placeholder="Tuliskan Ongkos Kirim" id="ongkir" class="form-control">
        </div>
        <div class="form-group">
            @php 
                $item_ongkir = App\Models\Item::where('nama', 'like', '%BIAYA KIRIM%')->get();
            @endphp
            TYPE ONGKIR
            <select name="ongkir_sku" class="form-control" id="ongkir_sku">
                @foreach($item_ongkir as $ok)
                    <option value="{{$ok->sku}}">{{$ok->nama}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
@endif
<div class="row">
    <div class="col pr-1">
        <button type="button" data-tipe="draff" class="btn btn-block btn-warning submit_pembelian">Draft</button>
    </div>
    <div class="col pl-1">
        <button type="button" data-tipe="simpan" class="btn btn-block btn-primary submit_pembelian">Simpan</button>
    </div>
</div>


<div class="modal fade" id="editPurchase" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form>
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="row ">
                            <input type="hidden" id="idPurchase">
                            <div class="col-12">
                                {{-- Item --}}
                                <input type="hidden" required name="item" id="itemPurchase">
                            </div>
                            <div class="col-12 mt-3">
                                    Qty
                                    <input type="number" required name="qty" id="qtyPurchase" class="form-control px-2" placeholder="Qty" autocomplete="off">
                                </div>
                            <div class="col-12 mt-3">
                                    Harga
                                    <input type="number" required name="harga" id="hargaPurchase" class="form-control px-2" placeholder="Harga" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="updatePurchase">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $('.select2').select2({
        theme: 'bootstrap4',
    })
</script>
<script>
    function editPurchase(id){
        // console.log(id)
        $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('pembelian.purchasestore') }}",
            type: "POST",
            dataType: "JSON",
            data:{
                id:id,
                key: 'get_list',
            },
            success: function(data){
                $('#idPurchase').val(data.id)
                $('#itemPurchase').val(data.item_id)
                $('#qtyPurchase').val(data.qty)
                $('#hargaPurchase').val(data.harga)
            }
        })
    }

    $('#updatePurchase').on('click', function(e){
        e.preventDefault()
        $.ajax({
            url: "{{ route('pembelian.purchasestore') }}",
            type: "POST",
            dataType: "JSON",
            data:{
                id:$('#idPurchase').val(),
                key: 'updatePurchaseList',
                item_id: $('#itemPurchase').val(),
                qty: $('#qtyPurchase').val(),
                harga: $('#hargaPurchase').val(),
            },
            success: function(data){
                if (data.status == '200') {
                    showNotif(data.msg);
                    $('#editPurchase').modal('hide')
                    $("#loading_list").attr('style', 'display: block') ;
                    $("#data_list").load("{{ route('pembelian.purchase', ['key' => 'list']) }}", function() {
                        $("#loading_list").attr('style', 'display: none') ;
                    }) ;

                    // $("#loading_view").attr('style', 'display: block') ;
                    // $("#data_view").load("{{ route('pembelian.purchase', ['key' => 'view']) }}", function() {
                    //     $("#loading_view").attr('style', 'display: none') ;
                    // }) ;
                    loadDataView();

                } else {
                    showAlert(data.msg);
                }
            }
        })
    })
</script>

<script>
$(".hapus_item").on('click', function() {
    var id  =   $(this).data('id') ;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: "{{ route('pembelian.purchasestore') }}",
        method: "POST",
        data: {
            id  :   id ,
            key :   'hapus_item' ,
        },
        success: function(data) {
            if (data.status == 400) {
                showAlert(data.msg);
            } else {
                showNotif(data.msg);
                // $("#loading_view").attr('style', 'display: block') ;
                // $("#data_view").load("{{ route('pembelian.purchase', ['key' => 'view']) }}", function() {
                //     $("#loading_view").attr('style', 'display: none') ;
                // }) ;
                loadDataView()
                $("#loading_list").attr('style', 'display: block') ;
                $("#data_list").load("{{ route('pembelian.purchase', ['key' => 'list']) }}", function() {
                    $("#loading_list").attr('style', 'display: none') ;
                }) ;
            }
        }
    });
})
</script>

<script>
$(".submit_pembelian").on('click', function() {
    var tipe            =   $(this).data('tipe') ;
    var supplier        =   $("#supplierpo").val() ;
    var tanggal         =   $("#tanggal").val() ;
    var keterangan      =   $("#keterangan").val() ;
    var form_id         =   $("#form_id").val() ;
    var type_po         =   $("#type_po").val() ;
    var tanggal_kirim   =   $("#tanggal_kirim").val() ;
    var jenis_ekspedisi =   $("#jenis_ekspedisi").val() ;
    var franko_loko     =   $("#franko_loko").val() ;
    var link_url        =   $("#link_url").val() ;
    var ongkir          =   $("#ongkir").val() ;
    var vendor_name     =   $("#vendor_name").val() ;
    var ongkir_sku      =   $("#ongkir_sku").val() ;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(".submit_pembelian").hide() ;

    $.ajax({
        url: "{{ route('pembelian.purchasestore') }}",
        method: "POST",
        data: {
            supplier        :   supplier ,
            tanggal         :   tanggal ,
            type_po         :   type_po ,
            keterangan      :   keterangan ,
            tanggal_kirim   :   tanggal_kirim ,
            form_id         :   form_id ,
            jenis_ekspedisi :   jenis_ekspedisi ,
            franko_loko     :   franko_loko ,
            link_url        :   link_url ,
            tipe            :   tipe ,
            vendor_name     :   vendor_name ,
            ongkir          :   ongkir ,
            key             :   'submit_pembelian' ,
            ongkir_sku      :   ongkir_sku 
        },
        success: function(data) {
            if (data.status == 400) {
                showAlert(data.msg);
            } else {
                showNotif(data.msg);
                $("#supplier").val(null).trigger("change");
                $("#tanggal").val("");
                $("#keterangan").val("");
                $("#loading_view").attr('style', 'display: block') ;
                loadDataView();

                $("#loading_list").attr('style', 'display: block') ;
                $("#data_list").load("{{ route('pembelian.purchase', ['key' => 'list']) }}", function() {
                    $("#loading_list").attr('style', 'display: none') ;
                }) ;

                $("#data_summary").load("{{ route('pembelian.purchase', ['key' => 'summary']) }}") ;
                $("#purchase-info").load("{{ route('pembelian.purchase', ['key' => 'info']) }}") ;
                $('.select2').select2({
                    theme: 'bootstrap4',
                })

            }
            $(".submit_pembelian").show() ;
        }
    });
})
</script>
@endif
