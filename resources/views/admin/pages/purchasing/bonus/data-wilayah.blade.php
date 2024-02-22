<select name="wilayah" id="wilayahfilter" onchange="wilayahfilter()" class="form-control select2">
    <option value="">Semua</option>
    @foreach ($wilayah as $wilayah)
        <option value="{{ $wilayah->sc_wilayah }}" {{ ($wilayah->sc_wilayah == $data_wilayah ? 'selected' : '') }}>{{ $wilayah->sc_wilayah }}</option>
    @endforeach
</select>

<script>
    $('.select2').select2({
        theme: 'bootstrap4'
    });
</script>


