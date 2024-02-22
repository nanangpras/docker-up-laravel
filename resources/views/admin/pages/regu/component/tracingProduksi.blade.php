@extends('admin.layout.template')

@section('title', 'Tracing Produksi')

@section('content')
<div class="row mb-4">
    <div class="col">
        <a href="{{ route('regu.index') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
            Back</a>
    </div>
    <div class="col text-center">
        <b>Tracing Produksi</b>
    </div>
    <div class="col"></div>
</div>
<section class="panel">
    <div class="card-body">
        <div class="row mt-2">
            <div class="col">
                <div class="form-group">
                    <div class="form-group">
                        Tanggal Awal
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif id="tanggal_awal" name="awal" value="{{date('Y-m-d')}}"
                            class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <div class="form-group">
                        Tanggal Akhir
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif id="tanggal_akhir" name="akhir" value="{{date('Y-m-d')}}"
                            class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="form-group">
                    <b>Customer</b>
                    <select class="form-control select2" name="customer" id="customer"
                        data-placeholder="Pilih Customer">
                        <option value="semuaCustomer">Semua</option>
                        @foreach ($customer as $id => $row)
                        <option value="{{ $row->id }}"> {{ $row->kode }} - {{ $row->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col">
                <div class="form-group">
                    <b>Item</b>
                    <select class="form-control select2" name="item" id="item" data-placeholder="Pilih Item">
                        <option value="semuaItem">Semua</option>
                        @foreach ($item as $row)
                        <option value="{{ $row->nama }}"> {{ $row->sku }} - {{ $row->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <b>Regu</b>
                    <select class="form-control select2" name="regu" id="changeregu">
                        <option value="all">- Semua Regu -</option>
                        <option value="byproduct">Byproduct</option>
                        <option value="parting">Parting</option>
                        <option value="whole">Whole</option>
                        <option value="marinasi">M</option>
                        <option value="boneless">Boneless</option>
                        <option value="frozen">Frozen</option>
                    </select>
                </div>
            </div>

            <div class="col">
                <div class="form-group">
                    <b>Action</b><br>
                    <button type="submit" class="btn btn-blue downloadDataTracing"><i
                            class="fa fa-spinner fa-spin spinerloading" style="display:none;"></i> <span
                            id="text">Download</span></button>
                </div>
            </div>

        </div>
    </div>
</section>

<section class="panel">
    <div class="card-body">
        <h5 class="text-center loading-exportcsv"><i class="fa fa-refresh fa-spin"></i> Loading....</h5>
        <div id="dataTracing"></div>
    </div>
</section>


<script>
    $('.select2').select2({
        theme: 'bootstrap4'
    })

    var item            = encodeURIComponent($("#item").val());
    var customer        = $("#customer").val();
    var awal            = $("#tanggal_awal").val();
    var akhir           = $("#tanggal_akhir").val();
    var regu            = $("#changeregu").val();

    // $("#item, #customer, #tanggal_awal, #tanggal_akhir").on('change', () => {
    //     loadExportTracing()
    // })
    $("#item, #customer, #tanggal_awal, #tanggal_akhir, #changeregu").on('change', () => {
        setTimeout(function(){
            loadExportTracing();
        },1500)
    })
    
    loadExportTracing();

    function loadExportTracing() {
        $(".loading-exportcsv").show();
        item            = encodeURIComponent($("#item").val());
        regu            = $("#changeregu").val();
        customer        = $("#customer").val();
        awal            = $("#tanggal_awal").val();
        akhir           = $("#tanggal_akhir").val();

        $("#dataTracing").load("{{ route('produksi.summaryprod', ['key' => 'viewExportTracing']) }}&item=" + item + "&customer=" + customer + "&tanggal_awal=" + awal + "&tanggal_akhir=" + akhir + "&regu=" + regu + "&subKey=viewDataTracing" , () => {
            $(".loading-exportcsv").hide();
        })
    }


    $(".downloadDataTracing").on('click', () => {
        item            = encodeURIComponent($("#item").val());
        regu            = encodeURIComponent($("#changeregu").val());
        customer        = $("#customer").val();
        awal            = $("#tanggal_awal").val();
        akhir           = $("#tanggal_akhir").val();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: "{{ route('produksi.summaryprod', ['key' => 'viewExportTracing']) }}&item=" + item + "&customer=" + customer + "&tanggal_awal=" + awal + "&tanggal_akhir=" + akhir + "&regu=" + regu + "&subKey=downloadDataTracing",
            method: "GET",
            beforeSend: function() {
                alert('Perhatian ! Untuk mengurangi beban server anda hanya diijinkan mendownload 1000 Data');
                $('.downloadDataTracing').attr('disabled');
                $(".spinerloading").show(); 
                $("#text").text('Downloading...');
            },
            success: function(data) {
                window.location.href = "{{ route('produksi.summaryprod', ['key' => 'viewExportTracing']) }}&item=" + item + "&customer=" + customer + "&tanggal_awal=" + awal + "&tanggal_akhir=" + akhir + "&regu=" + regu + "&subKey=downloadDataTracing";
                // window.location = "{{ route('produksi.summaryprod', ['key' => 'viewExportTracing']) }}&item=" + item + "&customer=" + customer + "&tanggal_awal=" + awal + "&tanggal_akhir=" + akhir + "&subKey=downloadDataTracing"
                $("#text").text('Download');
                $(".spinerloading").hide();
            }
        });
    })    

</script>



@stop