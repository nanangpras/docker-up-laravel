<section class="panel">
    <form action="{{route('penyiapan.siapKirimExport')}}" method="GET">
        <div class="card-body">
            <div class="form-group">
                Pencarian Tanggal Kirim
                <div class="row mt-2">
                    <div class="col">
                        Awal
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif name="awal" class="form-control"
                            value="{{ $awal ?? date('Y-m-d') }}" id="awalrekaporder" placeholder="Cari...."
                            autocomplete="off">
                    </div>
                    <div class="col">
                        Akhir
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif name="akhir" class="form-control"
                            value="{{ $akhir ?? date('Y-m-d', strtotime('+1 day')) }}" id="akhirrekaporder"
                            placeholder="Cari...." autocomplete="off">
                    </div>

                    <div class="col">
                        Jenis
                        <select class="form-control" name="jenis" id="jenisrekaporder">
                            <option value="">Semua</option>
                            <option value="fresh">fresh</option>
                            <option value="frozen">frozen</option>
                        </select>
                    </div>
                    <div class="col">
                        Keterangan
                        <select class="form-control" name="keterangan" id="keteranganrekaporder">
                            <option value="">Semua</option>
                            <option value="tidak-terkirim">Tidak terkirim</option>
                        </select>
                    </div>
                    <div class="col">
                        Nama Customer
                        <select class="form-control select_cust" name="nama_customer" id="nama_customer">
                            {{-- <option value="semua">Semua</option>
                            @foreach ($customer as $id => $nama)
                            <option value="{{$id}}">{{$nama}}</option>
                            @endforeach --}}
                        </select>
                    </div>
                    <div class="col">
                        Kategori
                        <select class="form-control select2" name="nama_kategori" id="nama_kategori">
                            {{-- <option value="">Semua</option>
                            @foreach ($category as $id => $nama)
                            <option value="{{$id}}">{{$nama}}</option>
                            @endforeach --}}
                        </select>
                    </div>
                    <div class="col">
                        <br>
                        <button type="submit" class="btn btn-blue">Export</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table default-table mt-3">
                        <thead class="sticky-top bg-white">
                            <tr class="text-center">
                                <th rowspan="2" width=10px>No</th>
                                <th rowspan="2">Nama</th>
                                <th rowspan="2">Channel</th>
                                <th colspan="2">Tanggal</th>
                                <th rowspan="2">Keterangan</th>
                                <th rowspan="2">SKU</th>
                                <th rowspan="2">Item</th>
                                <th rowspan="2">Part</th>
                                <th rowspan="2">Bumbu</th>
                                <th rowspan="2">Memo</th>
                                <th colspan="2">Order</th>
                                <th colspan="2">Fulfillment</th>
                                <th colspan="3">Tidak Terkirim</th>
                                <th rowspan="2">Fresh/Frozen</th>
                                <th rowspan="2">Status Terkirim</th>
                                <th rowspan="2">Marketing</th>
                            </tr>
                            <tr>
                                <th class="text-center">SO</th>
                                <th class="text-center">Kirim</th>
                                <th class="text-center">Item</th>
                                <th class="text-center">Berat</th>
                                <th class="text-center">Item</th>
                                <th class="text-center">Berat</th>
                                <th class="text-center">Alasan</th>
                                <th class="text-center">Item</th>
                                <th class="text-center">Berat</th>
                            </tr>
                        </thead>
                        <tbody id="tablerekaporder">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
</section>

<script>
    $('.select2').select2({
    theme: 'bootstrap4'
});
$('.select_cust').select2({
    theme: 'bootstrap4'
});

$('#keteranganrekaporder').on('change', function() {
    rekapOrder();
})
$('#awalrekaporder').on('change', function() {
    rekapOrder();
    customer();
    kategori();
})
$('#akhirrekaporder').on('change', function() {
    rekapOrder();
    customer();
    kategori();
})
$('#jenisrekaporder').on('change', function() {
    rekapOrder();
})

$("#nama_customer").on('change', function () {
    rekapOrder();
    kategori();
});
$("#nama_kategori").on('change', function () {
    rekapOrder();
});

rekapOrder();
customer();
kategori();

function rekapOrder(){
    var awal = $('#awalrekaporder').val();
    var akhir = $('#akhirrekaporder').val();
    var jenis = $('#jenisrekaporder').val();
    var keterangan = $('#keteranganrekaporder').val();
    var customer = $("#nama_customer").val();
    var kategori = $("#nama_kategori").val();
    var url = "{{ route('penyiapan.siapKirimData') }}";
    $.ajax({
        url: url,
        type: "GET",
        data: {
            awal: awal,
            akhir: akhir,
            jenis_export: jenis,
            keterangan: keterangan,
            nama_customer : customer,
            nama_kategori : kategori,
            key: 'json'
        },
        success: function(result){
            // console.log('order',result);
            $('#tablerekaporder').html('')
            if(result.length > 0){
                $.each(result, function(index, value) {
                    $('#tablerekaporder').append(
                        `<tr>
                            <td>${value[0] ?? ''}</td>    
                            <td>${value[3] ?? ''}<br>
                            <span class="small">${value[1] ?? ''}</span>
                            <br>
                            <span class="small">${value[2] ?? ''}</span>
                            </td>    
                            <td>${value[4] ?? ''}</td>    
                            <td>${value[5] ?? ''}</td>    
                            <td>${value[6] ?? ''}</td>    
                            <td>${value[7] ?? ''}</td>    
                            <td>${value[8] ?? ''}</td>    
                            <td>${value[9] ?? ''}</td>    
                            <td>${value[10] ?? ''}</td>    
                            <td>${value[11] ?? ''}</td>    
                            <td>${value[12] ?? ''}</td>    
                            <td>${value[13] ?? ''}</td>    
                            <td>${value[14] ?? ''}</td>    
                            <td>${value[15] ?? ''}</td>    
                            <td>${value[16] ?? ''}</td>    
                            <td>${value[18] ?? ''}</td>    
                            <td>${value[19] ?? ''}</td>    
                            <td>${value[20] ?? ''}</td> 
                            <td>${value[17] ?? ''}</td>    
                            <td>${value[21] ?? ''}</td>    
                            <td>${value[22] ?? ''}</td>    
                        </tr>`
                    )
                });
            }
        }
    });
}

function customer() {
    var awal = $('#awalrekaporder').val();
    var akhir = $('#akhirrekaporder').val();

    $.ajax({
        type: "GET",
        url: "{{ route('penyiapan.siapKirimData') }}",
        data: {
            awal: awal,
            akhir: akhir,
            key: 'json_customer'
            // key: 'json_category'
        },
        success: function (data) {
            console.log('customer',data);
            if (data) {
                $("#nama_customer").empty();
                $('#nama_customer').append(
                    '<option disabled selected>-Pilih Customer-</option>');
                $('#nama_customer').append(
                    '<option name="nama_customer" value="semua">Semua</option>');
                $.each(data, function(key, val) {
                $('#nama_customer').append(
                    '<option name="nama_customer" value ="' +
                    val.id + '">' + val.nama + '</option>')
                });    
            }
        }
    });
}
function kategori() {
    var awal = $('#awalrekaporder').val();
    var akhir = $('#akhirrekaporder').val();

    $.ajax({
        type: "GET",
        url: "{{ route('penyiapan.siapKirimData') }}",
        data: {
            awal: awal,
            akhir: akhir,
            key: 'json_category'
        },
        success: function (data) {
            console.log('kategori',data);
            if (data) {
                $("#nama_kategori").empty();
                $('#nama_kategori').append(
                    '<option disabled selected>-Pilih Kategori-</option>');
                $('#nama_kategori').append(
                    '<option name="nama_kategori" value="semua">Semua</option>');
                $.each(data, function(key, val) {
                $('#nama_kategori').append(
                    '<option name="nama_kategori" value ="' +
                    val.id_category + '">' + val.nama_category + '</option>')
                });    
            }
        }
    });
}

</script>