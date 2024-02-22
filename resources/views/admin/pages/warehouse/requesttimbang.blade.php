@extends('admin.layout.template')

@section('title', 'Ambil Stock Warehouse')



@section('content')
<div class="row mb-4">
    <div class="col">
        <a href="{{ route('thawingproses.index') }}" class="btn btn-outline btn-sm btn-back"> <i
                class="fa fa-arrow-left"></i>Back</a>
    </div>
    <div class="col-7 py-1 text-center">
        <b>AMBIL STOCK WAREHOUSE</b>
    </div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">
        <p><b>Tanggal Request :</b> {{ $data->tanggal_request }} </p>
        <p><b>Tanggal Input Request :</b> {{ $data->created_at }} </p>
        <div class="row">
            <div class="col">
                <b>Request Item :</b>
                <table class="table default-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Berat</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (json_decode($data->item) as $i => $item)
                        <tr data-id="{{$item->item}}" data-qty="{{ $item->qty }}" data-berat="{{ $item->berat }}">
                            <td>{{ ++$i }}</td>
                            <td>{{ App\Models\Item::find($item->item)->nama }}</td>
                            <td>{{ number_format($item->qty) }}</td>
                            <td>{{ number_format($item->berat, 2) }}</td>
                            <td>{{$item->keterangan ?? "-"}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col">
                <b>Stock Diambil :</b>
                <div id="loading-thawinglist" class="text-center mb-2">
                    <img src="{{ asset('loading.gif') }}" style="width: 30px">
                </div>
                <div id="requestthawinglist"></div>
            </div>
        </div>

        <form action="{{ route('warehouse.request_thawingproses', $data->id) }}" method="post">
            @csrf @method('patch')
            <button class="btn btn-block btn-success" id="selesaikan">Simpan Thawing</button>
        </form>

        <div class="my-3">
            <b>Ambil Stock :</b>
            <div class="row">
                <div class="col pr-1">
                    <input type="date" min="{!! Applib::BatasMinimalTanggal() !!}" class="form-control change-date" name="tanggal" id="tanggal" value="{{ $tanggal }}">
                </div>
                <div class="col pl-1">
                    <input type="date" min="{!! Applib::BatasMinimalTanggal() !!}" class="form-control change-date" name="tanggal_akhir" id="tanggal_akhir" value="{{ $akhir }}">
                </div>
            </div>

            <div class="form-group mt-2">
                <label for="cari">Pencarian</label>
                <select name="cari" id="cari" data-placeholder="Pilih Item" data-width="100%"
                    class="form-control select2" required>
                    <option value=""></option>
                    @foreach (json_decode($data->item) as $i => $item)
                    <option value="{{ App\Models\Item::find($item->item)->id }}" data-beratTabel="{{$item->berat}}" data-qtyTabel="{{$item->qty}}">{{ App\Models\Item::find($item->item)->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div id="loadingrequestthawing" class="text-center" style="display: none">
                <img src="{{ asset('loading.gif') }}" width="30px">
            </div>
            <div class="mt-3" id="data_stock"></div>
        </div>


    </div>
</section>
<script>
    $('.select2').select2({
        theme: 'bootstrap4'
    })
    
    $(document).ready(function() {
        $(document).on('click', '#selesaikan', function() {
            $("#selesaikan").hide() ;
        })
    });

    $('#loadingrequestthawing').show();
    $("#data_stock").load("{{ route('warehouse.request_thawing', [$data->id, 'key' => 'data_stock']) }}", function(){
        $('#loadingrequestthawing').hide();
    }) ;
    $("#requestthawinglist").load("{{ route('warehouse.request_thawing', [$data->id, 'key' => 'requestthawinglist']) }}", function(){
        $('#loading-thawinglist').hide();
    }) ;

    var qtyTabel;
    var beratTabel;
    
    $(document).ready(function() {
        var selectedOption  = $(this).find("option:selected");
            qtyTabel            = selectedOption.data("qtytabel");
            beratTabel          = selectedOption.data("berattabel");
        $('#cari,#tanggal,#tanggal_akhir').on('change', function() {
            $('#loadingrequestthawing').show();
            var tanggal         =   $("#tanggal").val();
            var tanggal_akhir   =   $("#tanggal_akhir").val();
            var cari            =   $("#cari").val();
            selectedOption      =   $(this).find("option:selected");
            qtyTabel            =   selectedOption.data("qtytabel");
            beratTabel          =   selectedOption.data("berattabel");
            $("#data_stock").load("{{ route('warehouse.request_thawing', [$data->id, 'key' => 'data_stock']) }}&tanggal=" + tanggal + "&akhir=" + tanggal_akhir + "&cari=" + encodeURIComponent(cari), function(){
                $('#loadingrequestthawing').hide();
            });
        });

        $(document).on('click', '.ambil_stock', function() {
            $('#loading-thawinglist').show();
            var id              =   $(this).data('id') ;
            var berat           =   $("#berat" + id).val() ;
            var qty             =   $("#qty" + id).val() ;
            var tanggal         =   $("#tanggal").val();
            var tanggal_akhir   =   $("#tanggal_akhir").val();
            cari                =   $("#cari").val();

            console.log(qtyTabel)
            console.log(beratTabel)

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(".ambil_stock").hide() ;

            if(cari){
                if (berat <= 0 && qty <= 0) {
                    showAlert('Berat dan Qty harus lebih dari 0')
                    $(".ambil_stock").show() ;
                } else if (berat <= 0){
                    showAlert('Berat harus lebih dari 0')
                    $(".ambil_stock").show() ;
                } else if (qty <= 0) {
                    showAlert('Qty harus lebih dari 0')
                    $(".ambil_stock").show() ;
                // } else if (qty > qtyTabel){
                //     showAlert('Qty melebihi batas')
                //     $(".ambil_stock").show() ;
                // } else if(berat > beratTabel){
                //     showAlert('Berat melebihi batas')
                //     $(".ambil_stock").show() ;
                } else {
                    $.ajax({
                        url: "{{ route('warehouse.request_thawingstore', $data->id) }}",
                        method: "POST",
                        data: {
                            id      :   id,
                            berat   :   berat,
                            qty     :   qty,
                        },
                        success: function(data) {
                            // console.log(data)
                            if (data.status == 400) {
                                showAlert(data.msg);
                            } else {
                                $("#data_stock").load("{{ route('warehouse.request_thawing', [$data->id, 'key' => 'data_stock']) }}&tanggal=" + tanggal + "&akhir=" + tanggal_akhir + "&cari=" + encodeURIComponent(cari));
                                $("#requestthawinglist").load("{{ route('warehouse.request_thawing', [$data->id, 'key' => 'requestthawinglist']) }}", function (){
                                    $('#loading-thawinglist').hide();
                                }) ;
                                showNotif('Stock berhasil diambil');
                            }
                            $(".ambil_stock").show() ;
                            $('#loading-thawinglist').hide();
                        }
                    });
                }
            }

            
        })
    });

    $(document).ready(function() {
        $(document).on('click', '.hapus_item', function() {
            $('#loading-thawinglist').show();
            var id              =   $(this).data('id') ;
            var tanggal         =   $("#tanggal").val();
            var tanggal_akhir   =   $("#tanggal_akhir").val();
            var cari            =   $("#cari").val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(".hapus_item").hide() ;
            $.ajax({
                url: "{{ route('warehouse.request_thawingdestroy', $data->id) }}",
                method: "DELETE",
                data: {
                    id      :   id,
                },
                success: function(data) {
                    if (data.status == 400) {
                        showAlert(data.msg);
                    } else {
                        $("#data_stock").load("{{ route('warehouse.request_thawing', [$data->id, 'key' => 'data_stock']) }}&tanggal=" + tanggal + "&akhir=" + tanggal_akhir + "&cari=" + encodeURIComponent(cari));
                        $("#requestthawinglist").load("{{ route('warehouse.request_thawing', [$data->id, 'key' => 'requestthawinglist']) }}", function (){
                            $('#loading-thawinglist').hide();
                        }) ;
                        showNotif('Hapus stock diambil berhasil');
                    }
                    $(".hapus_item").show() ;
                    $('#loading-thawinglist').hide();
                }
            });
        })
    });
</script>
@endsection