@if (COUNT($dataChillers))
<table class="table default-table">
    <thead>
        <tr>
            <th rowspan="2">Nama Item</th>
            <th colspan="2" class="text-center">Stock</th>
            <th colspan="2" class="text-center">Gabung</th>
        </tr>
        <tr>
            <th>Item</th>
            <th>Berat</th>
            <th>Item</th>
            <th>Berat</th>
            
        </tr>
    </thead>
    <tbody>
        @foreach ($dataChillers as $row)
            @php
                $countQty   = $row->sisaQty;
                $countBerat = number_format((float)$row->sisaBerat, 2, '.', '');

            @endphp
            @if($countBerat > 0)
            <tr>
                <td style="width: 200px">{{ $row->item_name }} {{$row->id}}<br>{{ $row->asal_tujuan }} </td>
                    <td class="text-right">
                        <div class="qty" id="si">
                            {{ $countQty }}
                        </div>
                        {{-- tampilkan jumlah qty ketika ada --}}
                        @if ($row->total_qty_freestock !== null && $row->total_qty_freestock !== 0 && ($row->status_stock == 1 || $row->status_stock == 2))
                                <div class="text-danger small" id="countQty">
                                    {{ $row->total_qty_freestock }}
                                </div>
                        @endif
                        {{-- @if (App\Models\FreestockList::hitung_diambil($row->id, 'qty', FALSE))
                        <div class="text-danger small">{{ number_format(App\Models\FreestockList::hitung_diambil($row->id, 'qty', FALSE)) }}</div>
                        @endif --}}
                    </td>
                <td class="text-right">
                    <div class="qty" id="si">
                        {{ $countBerat }}
                    </div>
                    {{-- tampilkan jumlah berat ketika ada --}}
                    @if ($row->total_berat_freestock !== null && $row->total_berat_freestock !== 0 && ($row->status_stock == 1 || $row->status_stock == 2))
                            <div class="text-danger small" id="countBerat">
                                {{ number_format($row->total_berat_freestock,2) }}
                            </div>
                    @endif
                    {{-- @if (App\Models\FreestockList::hitung_diambil($row->id, 'berat', FALSE))
                    <div class="text-danger small">{{ number_format(App\Models\FreestockList::hitung_diambil($row->id, 'berat', FALSE), 2) }}</div>
                    @endif --}}
                </td>
                <td class="pt-1 pb-0" style="width: 80px; padding:0">
                    <input type="number" name="qty[]" style="border:none; background-color: #fffde0; font-size: 12px" class="form-control rounded-0 px-1 py-1 form-control-sm bbitem" id="qty{{ $row->id }}" placeholder="Qty" autocomplete="off" min="0" max="{{ $countQty }}">
                    <span id="qtyError{{ $row->id }}" style="color: red; font-weigth: 500; display: none;">Qty melebihi batas maksimum!</span>
                </td>
                <td class="pt-1 pb-0" style="width: 80px; padding:0">
                    <input type="hidden" class="xcode" name="x_code[]" value="{{ $row->id }}">
                    <input type="hidden" class="xitemid" name="x_item_id[]" value="{{ $row->item_id }}">
                    <input type="number" step="0.01" name="berat[]" style="border:none; background-color: #fffde0; font-size: 12px" class="form-control rounded-0 px-1 py-1 form-control-sm bbberat" id="berat{{ $row->id }}" placeholder="Berat" autocomplete="off" min="0" max="{{ $countBerat }}">
                    <span id="beratError{{ $row->id }}" style="color: red; font-weigth: 500; display: none;">Berat melebihi batas maksimum!</span>
                </td>
            </tr>
            @endif
        @endforeach
    </tbody>
</table>

<button type="button" id="submitBB" class="btn btn-blue">Simpan</button>


<style>
    .btn-neutral.active {
        background-color: #bfb;
        border-color: #4c4;
    }

</style>
@else
<h6 class="text-center">Data Kosong</h6>
@endif

<script>
    $(document).ready(function () {
        // Fungsi untuk menampilkan pesan kesalahan
        function showError(element, message) {
            const errorSpan         = document.getElementById(element);
            errorSpan.textContent   = message;
            errorSpan.style.display = "inline";
        }
    
        // Fungsi untuk menyembunyikan pesan kesalahan
        function hideError(element) {
            const errorSpan         = document.getElementById(element);
            errorSpan.style.display = "none";
        }
    
        // Fungsi untuk memeriksa apakah nilai "Qty" dan "Berat" melebihi batas maksimum
        function validateInputs() {
            // const rows      = <?php echo json_encode($chiller); ?>;
            const rows      = {!! json_encode($dataChillers) !!}
            let isValid     = true;
            let minQty      = 0;
            let minBerat    = 0;
    
            for (const row of rows) {
                const maxQty        = row.sisaQty;
                const maxBerat      = row.sisaBerat;
                const qtyInput      = document.getElementById("qty" + row.id);
                const beratInput    = document.getElementById("berat" + row.id);
                const enteredQty    = parseFloat(qtyInput.value);
                const enteredBerat  = parseFloat(beratInput.value);
    
                // validasi nilai ketika minimum dan maksimum
                if (enteredQty <= 0) {
                    showError("qtyError" + row.id, "Qty tidak boleh kurang dari 1!");
                    isValid = false;
                } else if (enteredQty > maxQty) {
                    showError("qtyError" + row.id, "Qty tidak boleh lebih dari " + maxQty + " !");
                    isValid = false;
                } else {
                    hideError("qtyError" + row.id);
                }            
    
                if (enteredBerat <= 0) {
                    showError("beratError" + row.id, "Berat tidak boleh kurang dari 1!");
                    isValid = false;
                } else if (enteredBerat > maxBerat.toFixed(2)) {
                    showError("beratError" + row.id, "Berat tidak boleh lebih dari " + maxBerat.toFixed(2) + " !");
                    isValid = false;
                } else {
                    hideError("beratError" + row.id);
                }
                // end
            }
    
            return isValid;
        }
    
        // Tambahkan event listener untuk memeriksa validitas saat tombol "Simpan" ditekan
        $("#submitBB").click(function() {
            if (validateInputs()) {
                return;
            }
        });
    })
                
</script>
