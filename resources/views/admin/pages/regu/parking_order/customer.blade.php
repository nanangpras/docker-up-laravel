<label for="filter_customer_parking_order">Pencarian Customer</label>
<select name="filter_customer_parking_order" id="filter_customer_parking_order" onchange="customer_parking_order()" class="form-control select2">
    <option value="">Semua</option>
    @foreach ($customer as $data)
        <option value="{{ $data->id_customer }}" {{ $data->id_customer == $customer_parking_order ? 'selected' : '' }}>{{ $data->nama_customer }}</option>
    @endforeach
</select>
<script>
    $(".select2").select2({
        theme: "bootstrap4"
    });
</script>