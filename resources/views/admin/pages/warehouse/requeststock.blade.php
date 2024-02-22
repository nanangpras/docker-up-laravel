<table width="100%" class="table default-table">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Nama</th>
            <th>Customer</th>
            <th>Sub Item</th>
            <th>Kemasan</th>
            <th>Lokasi</th>
            <th>Qty</th>
            <th>Berat</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($stock as $i => $val)
            @php
                $sisaQty     = $val->sisaQty; 
                $sisaBerat   = number_format((float)$val->sisaBerat, 2, '.', '');
            @endphp

            <tr>
                <td>{{ $loop->iteration+($stock->currentpage() - 1) * $stock->perPage()}}.</td>
                <td>{{ $val->production_date }}</td>
                <td>{{ $val->nama ?? '#' }}</td>
                <td>{{ $val->nama_customer ?? '#' }}</td>
                <td>{{ $val->sub_item }}</td>
                <td>{{ $val->packaging ?? 'Tidak Ada' }}</td>
                <td>{{ $val->code ?? '#' }}</td>
                <td data-sumqty="{{ $sisaQty }}">{{ $sisaQty }} ekor</td>
                <td data-sumberat="{{ $sisaBerat }}">{{ $sisaBerat }} Kg</td>
                <td>
                    <div style="width:130px">
                        <input type="number" class="form-control rounded-0 p-1 float-right" id="berat{{ $val->id }}" placeholder="Berat" step="0.01" style="width:50px" max="{{ $sisaBerat }}">
                        <input type="number" class="form-control rounded-0 p-1" id="qty{{ $val->id }}" placeholder="Qty" style="width:50px" max="{{ $sisaQty }}">
                        <button class="btn mt-1 float-right btn-primary btn-sm ambil_stock" data-id="{{ $val->id }}">Ambil Stock</button>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="paginate_stock">
    {{ $stock->appends($_GET)->onEachSide(1)->links() }}
</div>

<script>
// $(".ambil_stock").on("click", function() {
//     var id = $(this).data("id");
//         var qtyInput = $("#qty" + id);
//         var beratInput = $("#berat" + id);
//         var qtyValue = parseFloat(qtyInput.val());
//         var beratValue = parseFloat(beratInput.val());

//         if (isNaN(qtyValue)) {
//             alert("Nilai Qty tidak valid.");
//             return false;
//         }

//         // Dapatkan sumQty dari atribut data pada elemen <td>
//         var sumQty = parseFloat($("#qty" + id).closest("tr").find("td[data-sumqty]").data("sumqty"));
//         var sumBerat = parseFloat($("#berat" + id).closest("tr").find("td[data-sumberat]").data("sumberat"));

//         // Validasi input qty
//         if (qtyValue > sumQty) {
//             showAlert("Qty yang dimasukkan melebihi jumlah yang tersedia.");
//             return false; // Menghentikan proses
//         } else if( beratValue > sumBerat){
//             showAlert("Berat yang dimasukkan melebihi jumlah yang tersedia.");
//             return false;
//         } else if( qtyValue <= 0 && beratValue <= 0) {
//             showAlert("Berat dan Qty tidak boleh kurang atau sama dengan 0.");
//             return false;
//         } else if( qtyValue <= 0){
//             showAlert("Qty yang dimasukkan melebihi jumlah yang tersedia.");
//             return false;
//         } else if(beratValue <= 0) {
//             showAlert("Berat yang dimasukkan melebihi jumlah yang tersedia.");
//             return false;
//         } else{
//             return true;
//         }
// });

$('.paginate_stock .pagination a').on('click', function(e) {
    e.preventDefault();
    showNotif('Menunggu');

    url = $(this).attr('href');
    $.ajax({
        url: url,
        method: "GET",
        success: function(response) {
            $('#data_stock').html(response);
        }

    });
});
</script>

