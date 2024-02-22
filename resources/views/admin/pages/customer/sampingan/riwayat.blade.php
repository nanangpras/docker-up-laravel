
<div class="table-responsive">
    <div class="form-group">
        Pencarian Customer
        <select name="search_customer_sampingan" id="search_customer_sampingan" class="form-control select2" data-placeholder="Pilih Customer" data-width="100%" onchange="search_customer_sampingan()">
            <option value="0">Semua</option>
            @foreach ($customer as $row)
            <option value="{{ $row->id }}" {{ $row->id == $id_cust ? 'selected' : '' }}>{{ $row->kode }}. {{ $row->nama }}</option>
            @endforeach
        </select>
    </div>
    <table class="table table-sm table-bordered table-striped">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
            <tr>
                <td>{{ $row->kode }}</td>
                <td>{{ $row->nama }}</td>
                <td>{{ COUNT($row->customersampingan->where('deleted_at' , NULL)) == 0 ? 'Tidak Aktif' : 'Aktif' }}</td>
                <td>
                    @if (COUNT($row->customersampingan->where('deleted_at' , NULL)) > 0)<button class="btn btn-outline-info rounded-0 px-2 py-0" data-toggle="modal" data-target="#showRiwayat" onclick="openModal( {{ $row->id }} )">Lihat</button> @endif
                    <button class="{{ COUNT($row->customersampingan->where('deleted_at' , NULL)) == 0 ? 'btn btn-outline-success' : 'btn btn-outline-danger'}} rounded-0 px-2 py-0" onclick="hapusCustomerSampingan({{ $row->id }}, {{ COUNT($row->customersampingan->where('deleted_at' , NULL)) == 0 ? '`aktif`' : '`nonaktif`' }})">{{ COUNT($row->customersampingan->where('deleted_at' , NULL)) == 0 ? 'Aktifkan Customer' : 'Nonaktifkan Customer' }}
                    </button>
                </td>
            </tr>

            @endforeach
        </tbody>
    </table>
</div>

{{-- MODAL --}}
<div class="modal fade" id="showRiwayat" data-backdrop="static"  data-keyboard="false"  aria-labelledby="showRiwayatLabel" aria-hidden="true">
    <div class="modal-dialog">
        <h5 id="loadingListItemSampingan" class="text-center"><i class="fa fa-refresh fa-spin"></i> Loading....</h5>
        <div class="modal-content">
            <input type="hidden" value="" id="customerRiwayatSampingan">
            <div id="listItemSampingan">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary closeModalRiwayatSampingan" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary simpanListItemSampingan">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>
{{-- END MODAL --}}



<div class="paginate_sampingan">
    {{ $data->appends($_GET)->onEachSide(1)->links() }}
</div>


<script>
    $(".select2").select2({
        theme: "bootstrap4"
    });
</script>
    
<script>
$('.paginate_sampingan .pagination a').on('click', function(e) {
    e.preventDefault();
    showNotif('Menunggu');

    url = $(this).attr('href');
    $.ajax({
        url: url,
        method: "GET",
        success: function(response) {
            $('#data_riwayat').html(response);
        }

    });
});

function hapusItemSampingan(id, key){
    $.ajax({
        url: "{{ route('hargakontrak.index') }}",
        data:{
            _token:"{{ csrf_token() }}",
            id:id,
            customer: key,
            key:'hapusItemSampingan',
        },
        success: function(response) {
            if (response.status == 200) {
                if (response.reload == 'true') {
                    $('#showRiwayat').modal('hide');
                    $("#data_riwayat").load("{{ route('hargakontrak.index', ['key' => 'riwayatCustomerSampingan']) }}") ;
                    showNotif(response.msg);
                } else {
                    showNotif(response.msg);
                    $('#listItemSampingan').load("{{ route('hargakontrak.index') }}?customer=" + key + "&key=listItemSampingan");
                }
            } else {
                showAlert(response.msg);
            }
        }
    });
}

function hapusCustomerSampingan(id, key){
    $.ajax({
        url: "{{ route('hargakontrak.index') }}",
        data:{
            _token:"{{ csrf_token() }}",
            id:id,
            status: key,
            key:'hapusCustomerSampingan',
        },
        success: function(response) {
            if (response.status == 200) {
                showNotif(response.msg);
                $("#data_riwayat").load("{{ route('hargakontrak.index', ['key' => 'riwayatCustomerSampingan']) }}") ;
            } else {
                showAlert(response.msg);
            }
        }
    });
}

function search_customer_sampingan(){
    $('#data_riwayat').load("{{ route('hargakontrak.index') }}?customer="+$('#search_customer_sampingan').val()+"&key=riwayatCustomerSampingan");
}

function openModal(key) {
    $('#customerRiwayatSampingan').val(key);
    $('#listItemSampingan').html('')
    $('#listItemSampingan').load("{{ route('hargakontrak.index') }}?customer=" + key + "&key=listItemSampingan", () => {
        $("#loadingListItemSampingan").hide();
    });
}

$('.simpanListItemSampingan').on('click', function() {
    const cust          =   document.getElementById('customerRiwayatSampingan').value;

    var items           =   document.getElementsByClassName("listItemRiwayatSampingan");
    var item            =   [];
    for(var i = 0; i < items.length; ++i) {
        item.push(parseFloat(items[i].value));
    }

    if (item.includes('') || item.includes(NaN)) {
        showAlert('Terdapat item yang belum dipilih!');
        return false;
    }

    var qtys            =   document.getElementsByClassName("listQtyRiwayatSampingan");
    var qty             =   [];
    for(var i = 0; i < qtys.length; ++i) {
        qty.push(parseFloat(qtys[i].value ? qtys[i].value : 0));
    }

    var berats          =   document.getElementsByClassName("listBeratRiwayatSampingan");
    var berat           =   [];

    for(var i = 0; i < berats.length; ++i) {
        berat.push(parseFloat(berats[i].value ? berats[i].value : 0));
    }


    var result = confirm("Yakin submit Item Sampingan?");

    if(result){
        $(".simpanListItemSampingan").hide() ;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    
        $.ajax({
            url: "{{ route('hargakontrak.store', ['key' => 'storeCustomerSampingan']) }}",
            method: "POST",
            data: {
                cust,
                qty,
                berat,
                item,
            },
            success: function(data) {
                if (data.status == 400) {
                    showAlert(data.msg) ;
                } else {
                    $("#showRiwayat").modal('hide');
                    $('#listItemSampingan').load("{{ route('hargakontrak.index') }}?customer=" + cust + "&key=listItemSampingan");
                    showNotif(data.msg);
                }
                $(".simpanListItemSampingan").show() ;
            }
        });
    } else {
        return false;
    }
})


</script>