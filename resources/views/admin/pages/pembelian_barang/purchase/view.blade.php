<section class="panel">
    <div class="card-body">
        <div class="accordion" id="accordionListPO">
            <div class="table-responsive">
                <table class="table default-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No PR</th>
                            <th>Divisi</th>
                            <th>Jumlah Data</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($data as $row)
                        @php
                        $item_list =  App\Models\Pembelianlist::
                            where('pembelian_list.headbeli_id', NULL)
                            ->where('pembelian_list.status', 1)
                            ->where('pembelian_list.sisa', '>', 0)
                            ->whereBetween('pembelian_list.created_at', [$tanggal_mulai_view." 00:00:00", $tanggal_akhir_view." 23:59:59"])
                            ->where('pembelian_list.pembelian_id',$row->id)
                            ->get();
                        @endphp
                            @include('admin.pages.pembelian_barang.purchase.itemsisaperPR')
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>


<div id="paginate_pembelian">
    {{ $data->appends($_GET)->onEachSide(1)->links() }}
</div>

<script>
$('#paginate_pembelian .pagination a').on('click', function(e) {
    e.preventDefault();
    showNotif('Menunggu');

    url = $(this).attr('href');
    $.ajax({
        url: url,
        method: "GET",
        success: function(response) {
            $('#data_view').html(response);
        }

    });
});
</script>

<script>
$(".tambah_item").on('click', function() {
    var id              =   $(this).data('id') ;
    var qty             =   $("#qty" + id).val() ;
    var berat           =   $("#berat" + id).val() ;
    var harga           =   $("#harga" + id).val() ;
    var estimasi        =   $("#estimasi" + id).val() ;
    var jumlah_do       =   $("#jumlah_do" + id).val() ;
    var ukuran_ayam     =   $("#ukuran_ayam" + id).val() ;
    var unit_cetakan    =   $("#unit_cetakan" + id).val() ;
    var gudang          =   $("#gudang" + id).val() ;
    var keterangan          =   $("#keterangan" + id).val() ;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: "{{ route('pembelian.purchasestore') }}",
        method: "POST",
        data: {
            id              :   id ,
            qty             :   qty ,
            berat           :   berat ,
            harga           :   harga ,
            estimasi        :   estimasi ,
            jumlah_do       :   jumlah_do ,
            ukuran_ayam     :   ukuran_ayam ,
            unit_cetakan    :   unit_cetakan ,
            gudang          :   gudang ,
            keterangan      :   keterangan ,
            key             :   'tambah_item' ,
        },
        success: function(data) {
            if (data.status == 400) {
                showAlert(data.msg);
            } else {
                showNotif(data.msg);
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');

                $("#loading_list").attr('style', 'display: block') ;
                $("#data_list").load("{{ route('pembelian.purchase', ['key' => 'list']) }}", function() {
                    $("#loading_list").attr('style', 'display: none') ;
                }) ;

                $("#purchase-info").load("{{ route('pembelian.purchase', ['key' => 'info']) }}") ;
                loadDataView()
            }
        }
    });
})
</script>
<script>
    function validateheader(idrowpr, row, item){
        if(!"{{ $header }}"){
            $('#validateheader'+idrowpr).attr('data-toggle', '')
            showAlert('Silahkan bikin header terlebih dahulu');
        } else {
            $('#historyperPR'+idrowpr).load("{{ route('pembelian.purchase', ['key' => 'historyPO']) }}&subkey=perPR&item_id="+item+"&idrow="+idrowpr)
            $('#validateheader'+idrowpr).attr('data-toggle', 'modal')
        }
    }
</script>
