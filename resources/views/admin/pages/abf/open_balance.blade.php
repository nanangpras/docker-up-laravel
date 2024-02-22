<div class="form-group">
    <label for="tanggal_produksi">Tanggal Produksi</label>
    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01" @endif
        id="tanggal_produksi" value="{{ date('Y-m-d') }}" class="form-control">
</div>

<div class="row">
    <div class="col-md-8 pr-md-1">
        <div class="form-group">
            <label for="item_produksi">Item</label>
            <select id="item_produksi" class="form-control select2" data-placeholder="Pilih Item Produksi"
                data-width="100%">
                <option value=""></option>
                @foreach ($item as $row)
                <option value="{{ $row->id }}">{{ $row->nama }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-2 col-6 pr-1 pr-md-0 px-md-1">
        <div class="form-group">
            <label for="qty_item">Qty</label>
            <input type="number" min="1" id="qty_item" class="form-control">
        </div>
    </div>

    <div class="col-md-2 col-6 pl-1">
        <div class="form-group">
            <label for="berat_item">Berat</label>
            <input type="number" min="0" step="0.01" id="berat_item" class="form-control">
        </div>
    </div>
</div>

<div class="form-group">
    <label for="plastik_item">Plastik</label>
    <select id="item_plastik" class="form-control select2" data-placeholder="Pilih Plastik" data-width="100%">
        <option value=""></option>
        @foreach ($plastik as $row)
        <option value="{{ $row->id }}">{{ $row->nama }}</option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <button id="open_balance" type="button" class="btn btn-primary btn-block">Submit</button>
</div>

<script>
    $('.select2').select2({
        theme: 'bootstrap4',
    })
</script>

<script>
    $(document).ready(function() {
        $('#open_balance').click(function() {
            var tanggal_produksi    =  $("#tanggal_produksi").val() ;
            var item_produksi       =  $("#item_produksi").val() ;
            var qty_item            =  $("#qty_item").val() ;
            var berat_item          =  $("#berat_item").val() ;
            var item_plastik        =  $("#item_plastik").val() ;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#open_balance').hide();

            $.ajax({
                url: "{{ route('abf.togudang') }}",
                method: "POST",
                data: {
                    tanggal     :   tanggal_produksi ,
                    item        :   item_produksi ,
                    qty         :   qty_item ,
                    berat       :   berat_item ,
                    item_plastik:   item_plastik ,
                    key         :   'open'
                },

                success: function(data) {
                    if (data.status == 400) {
                        showAlert(data.msg);
                    } else {
                        $("#input_open").load("{{ route('abf.index', ['key' => 'open']) }}") ;
                        $('#abf-stock').load("{{ route('abf.stock') }}") ;
                        showNotif(data.msg);
                    }
                    $('#open_balance').show();
                }
            });
        });
    });
</script>