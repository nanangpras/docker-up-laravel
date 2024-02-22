<label for="">Pencarian marketing</label>
<select name="filter_marketing_parking_order" id="filter_marketing_parking_order" onchange="marketing_parking_order()" class="form-control select2">
    <option value="">Semua</option>
    @foreach ($marketing as $data)
        <option value="{{ $data->id_user }}" {{ $data->id_user == $marketing_parking_order ? 'selected' : '' }}>{{ $data->nama_marketing }}</option>
    @endforeach
</select>
<script>
    $(".select2").select2({
        theme: "bootstrap4"
    });
</script>