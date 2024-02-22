<label for="konsumen">Nama Konsumen</label>
<select onchange="konsumen()" id="konsumen" class="form-control select2" data-width="100%">
    <option value="all">Semua Konsumen</option>
    @foreach ($list_order as $row)
    <option value="{{ $row->nama }}" {{ $request->konsumen == $row->nama ? "selected" : "" }}>{{ $row->nama }}</option>
    @endforeach
</select>

<script>
    $('.select2').select2({
        theme: 'bootstrap4',
    })
</script>
