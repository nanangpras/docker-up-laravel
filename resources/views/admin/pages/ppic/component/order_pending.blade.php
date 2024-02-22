<div id="order_pending"></div>

<script>
    var tanggalend = $('#tanggalend').val();
    $("#order_pending").load(
        "{{ route('ppic.index', ['view' => 'order_pending']) }}&tanggal={{ $tanggal }}&tanggalend=" +
        tanggalend);
</script>

<style>
    #accordionOrderPending .card .card-header {
        padding: 8px;
        text-align: left;
        border-bottom: 0px;
        background: #fafafa;
    }

    #accordionOrderPending .card a {
        color: #000000;
        padding: 0px;
    }

</style>
