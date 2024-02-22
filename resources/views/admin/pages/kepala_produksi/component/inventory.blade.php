<div class="row">
    <div class="col-lg-2 col-md-3">
        <div class="radio-toolbar">
            <div class="row">
                {{-- @foreach ($cold as $row)
                    <div class="col-4 col-md-12">
                        <div class="mb-2">
                            <input type="radio" name="inventory" class="inventory" value="{{ $row->id }}" id="inv{{ $row->id }}">
                            <label for="inv{{ $row->id }}">{{ $row->code }}</label>
                        </div>
                    </div>
                @endforeach --}}
                @foreach ($cold1 as $row)
                    <div class="col-4 col-md-12">
                        <div class="mb-2">
                            <input type="radio" name="inventory" class="inventory" value="{{ $row->id }}" id="inv{{ $row->id }}">
                            <label for="inv{{ $row->id }}">{{ $row->code }}</label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-lg-10 col-md-9">
        <div id="showinven"></div>
    </div>
</div>

<script>
    var id = "";
    var tanggal_pindah = "";

    $('.inventory').change(function() {
        id = $(this).val();
        tanggal_pindah = $('#tanggal').val();

        console.log("{{ url('admin/kepala-produksi/inventoryshow?id=') }}" + id + "&tanggal=" + tanggal_pindah);
        $("#showinven").load("{{ url('admin/kepala-produksi/inventoryshow?id=') }}" + id + "&tanggal=" + tanggal_pindah);
    })
</script>
