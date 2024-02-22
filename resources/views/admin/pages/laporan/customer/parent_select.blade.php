<select id="parent" class="form-control select2" data-width="100%" onchange="pilih_parent()">
    <option value="all">Semua Parent</option>
    @foreach ($data as $row)
    <option value="{{ $row }}">{{ $row }}</option>
    @endforeach
</select>

<script>
$('.select2').select2({
    theme: 'bootstrap4'
});
</script>
