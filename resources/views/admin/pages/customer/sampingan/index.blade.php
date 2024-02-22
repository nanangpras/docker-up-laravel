@extends('admin.layout.template')

@section('title', 'Customer Sampingan')

@section('footer')
<script>
$(".select2").select2({
    theme: "bootstrap4"
});
</script>

<script>
$("#data_riwayat").load("{{ route('hargakontrak.index', ['key' => 'riwayatCustomerSampingan']) }}") ;
    var hash = window.location.hash.substr(1);
    var href = window.location.href;

    deafultPage();

    function deafultPage() {
        if (hash == undefined || hash == "") {
            hash = "input";
        }

        $('#' + hash + '-tab').addClass('active').siblings().removeClass('active');
        $('#' + hash).addClass('active show').siblings().removeClass('active show');

    }


    $('.tab-link').click(function(e) {
        e.preventDefault();
        status = $(this).attr('aria-controls');
        window.location.hash = status;
        href = window.location.href;

    });
</script>

<script>
$("#submitCustomerSampingan").on('click', function() {
    const cust          =   document.getElementById('customerSampingan').value;
    if (cust == '' || cust == undefined) {
        showAlert('Silahkan pilih customer!');
        return false;
    }

    var items           =   document.getElementsByClassName("t_item");
    var item            =   [];
    for(var i = 0; i < items.length; ++i) {
        item.push(parseFloat(items[i].value));
    }

    if (item.includes('') || item.includes(NaN)) {
        showAlert('Terdapat item yang belum dipilih!');
        return false;
    }

    var qtys            =   document.getElementsByClassName("t_qty");
    var qty             =   [];
    for(var i = 0; i < qtys.length; ++i) {
        qty.push(parseFloat(qtys[i].value ? qtys[i].value : 0));
    }

    var berats          =   document.getElementsByClassName("t_berat");
    var berat           =   [];

    for(var i = 0; i < berats.length; ++i) {
        berat.push(parseFloat(berats[i].value ? berats[i].value : 0));
    }


    var result = confirm("Yakin submit Customer Sampingan?");

    if(result){
        $("#submitCustomerSampingan").hide() ;
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
                    $(".t_customer").val("").trigger('change') ;
                    $(".t_item").val("").trigger('change') ;
                    $(".t_qty").val("") ;
                    $(".t_berat").val("") ;
                    $("#data_riwayat").load("{{ route('hargakontrak.index', ['key' => 'riwayatCustomerSampingan']) }}") ;
                    showNotif(data.msg);
                }
                $("#submitCustomerSampingan").show() ;
            }
        });
    } else {
        return false;
    }
})
</script>


<script>
y = 1 ;
function addItem() {
    row     =   '' ;

    row +=  '<div class="temporary">' ;
    row +=  '   <div class="bg-danger text-right cursor px-1 text-light row-' + (y) + '" onclick="deleteItem(' + (y) + ')">' ;
    row +=  '       <i class="fa fa-trash"></i> Hapus' ;
    row +=  '   </div>' ;
    row +=  '   <div class="border p-2 mb-2 row-' + (y) + '">' ;
    row +=  '       <div class="form-group">';
    row +=  '           <div id="untukitem-' + y + '">';
    row +=  '            <select name="t_item" id="itemSampingan-' + y + '" class="form-control select2 t_item" data-placeholder="Pilih Item">';
    row +=  '                <option value=""></option>';
    row +=  '                @foreach ($itemSampingan as $item)';
    row +=  '                   <option  option value="{{ $item->id }}">{{ $item->nama }}</option>';
    row +=  '                @endforeach';
    row +=  '           </select>';
    row +=  '       </div>';
    row +=  '   </div>';
    row +=  '   <div class="row">' ;
    row +=  '        <div class="col pr-1">' ;
    row +=  '            <div class="form-group">' ;
    row +=  '                <label for="qty">Qty Ekor/pcs/pack</label>' ;
    row +=  '                <input type="number" placeholder="Tuliskan Qty" min="0" class="form-control t_qty">' ;
    row +=  '            </div>' ;
    row +=  '        </div>' ;
    row +=  '        <div class="col pl-1">' ;
    row +=  '            <div class="form-group">' ;
    row +=  '                <label for="berat">Berat</label>' ;
    row +=  '                <input type="number" placeholder="Tuliskan Berat" min="0" class="form-control t_berat">' ;
    row +=  '            </div>' ;
    row +=  '        </div>' ;
    row +=  '    </div>' ;
    row +=  '   </div>' ;
    row +=  '</div>' ;


    $('#item-loop').append(row);
    $('.select2').select2({
        theme: 'bootstrap4'
    })

    y++;
}

function deleteItem(rowid){
    $('.row-'+rowid).remove();
}

</script>
@endsection

@section('content')
<div class="my-4 text-center font-weight-bold text-uppercase">Customer Sampingan</div>
<div class="col text-right">
    <a href="{{ route('buatso.index') }}#sampingan" class="btn btn-sm btn-success" target="_blank">Input SO Sampingan</a>
</div>
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link tab-link" id="input-tab" data-toggle="tab" href="#input" role="tab" aria-controls="input" aria-selected="true">Input</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link tab-link" id="riwayat-tab" data-toggle="tab" href="#riwayat" role="tab" aria-controls="riwayat" aria-selected="false">Riwayat</a>
    </li>
</ul>

<section class="panel">
    <div class="card-body">
        <div class="tab-content">
            <div class="tab-pane fade show active" id="input" role="tabpanel" aria-labelledby="input-tab">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="customer">Customer</label>
                        <div id="customer-loop">
                            <div class="row">
                                <div class="col pr-1">
                                    <select name="customer" id="customerSampingan" data-width="100%" data-placeholder="Data Customer" class="t_customer form-control select2" required>
                                        <option value=""></option>
                                        @foreach ($customer as $row)
                                        <option value="{{ $row->id }}">{{ $row->kode }}. {{ $row->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div id="item-loop">
                            <div class="border p-2 mb-2">
                                <div class="form-group">
                                    <div id="untukitem-0">
                                        <select name="t_item" id="itemSampingan-0" class="form-control select2 t_item" data-placeholder="Pilih Item">
                                            <option value=""></option>
                                            @foreach ($itemSampingan as $item)
                                                <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col pr-1">
                                        <div class="form-group">
                                            <label for="qty">Qty Ekor/pcs/pack</label>
                                            <input type="number" placeholder="Tuliskan Qty" min="0" class="form-control t_qty">
                                        </div>
                                    </div>
                                    <div class="col pl-1">
                                        <div class="form-group">
                                            <label for="berat">Berat</label>
                                            <input type="number" placeholder="Tuliskan Berat" min="0" class="form-control t_berat">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button class="btn btn-block btn-outline-success rounded-0 p-1 mb-4" onclick="addItem()">Tambah Item</button>

                        <button id="submitCustomerSampingan" class="btn btn-block btn-primary">Submit</button>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="riwayat" role="tabpanel" aria-labelledby="riwayat-tab">
                <div id="data_riwayat"></div>
            </div>
        </div>
    </div>
</section>
@endsection
