<div id="order_view"></div>

<script>
    var tanggalend = $('#tanggalend').val();
    $("#order_view").load(
        "{{ route('ppic.index', ['view' => 'order_view']) }}&tanggal={{ $tanggal }}&tanggalend=" + tanggalend
        );
</script>

<script>
    $(document).on('click', '.selesaiproses', function() {
        var row_id = $(this).data('selesai');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        console.log(row_id);
        $.ajax({
            url: "{{ route('kepalaproduksi.selesaiproses') }}",
            method: "POST",
            data: {
                row_id: row_id
            },
            success: function(data) {
                $("#order_view").load(
                    "{{ route('ppic.index', ['view' => 'order_view']) }}&tanggal={{ $tanggal }}"
                    );
            }
        });
    })
</script>
