@extends('admin.layout.template')

@section('title', 'Harga Kontrak')

@section('footer')
<script>
    $(".select2").select2({
    theme: "bootstrap4"
});
</script>

<script>
    $("#data_riwayat").load("{{ route('hargakontrak.index', ['key' => 'riwayat']) }}") ;
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

    function formatAmountNoDecimals( number ) {
        var rgx = /(\d+)(\d{3})/;
        while( rgx.test( number ) ) {
        number = number.replace( rgx, '$1' + '.' + '$2' );
        }
        return number;
    }

    function formatAmount( number ) {

        // remove all the characters except the numeric values
        number = number.replace(/[^0-9]/g, '');
        number.substring( number.length - 2, number.length );

        // set the precision
        number = new Number( number );
        number = number.toFixed( 2 );    // only works with the "."

        // change the splitter to ","
        number = number.replace( /\./g, ',' );

        // format the amount
        x = number.split( ',' );
        x1 = x[0];
        x2 = x.length > 1 ? ',' + x[1] : '';

        return formatAmountNoDecimals( x1 );
    }

    function inputRupiah(e) {
        $('.input-amount-'+e).val(formatAmount($('.input-amount-'+e).val()));
    }
</script>

<script>
    $("#submit_harga").on('click', function() {
    var result = confirm("Yakin submit Harga Kontrak?");
    if(result){

        // CEK HARGA YANG KOSONG
        var hargaMins      =   document.getElementsByClassName("t_harga");
        var hargaMin       =   [];
        for(var i = 0; i < hargaMins.length; ++i) {
            console.log(hargaMins[i].value.replace(/\./g, ""))
            if (hargaMins[i].value.replace(/\./g, "") == 0) {
                showAlert('Terdapat harga Rp 0');
                return false;
            }
        }

        var cust        =   document.getElementsByClassName("t_customer");
        var customer    =   [];
        for(var i = 0; i < cust.length; ++i) {
            customer.push(parseFloat(cust[i].value));
        }
    
        var items       =   document.getElementsByClassName("t_item");
        var item        =   [];
        for(var i = 0; i < items.length; ++i) {
            item.push(parseFloat(items[i].value));
        }
    
        var prices      =   document.getElementsByClassName("t_harga");
        var harga       =   [];
        for(var i = 0; i < prices.length; ++i) {
            harga.push(parseFloat(hargaMins[i].value.replace(/\./g, "")));
        }
    
        var units       =   document.getElementsByClassName("t_unit");
        var unit        =   [];
        for(var i = 0; i < units.length; ++i) {
            unit.push(units[i].value);
        }
    
        var qtys        =   document.getElementsByClassName("t_qty");
        var qty         =   [];
        for(var i = 0; i < qtys.length; ++i) {
            qty.push(parseFloat(qtys[i].value ? qtys[i].value : 0));
        }
    
        // var start       =   document.getElementsByClassName("t_mulai");
        // var mulai       =   [];
        // for(var i = 0; i < start.length; ++i) {
        //     mulai.push(start[i].value);
        // }
    
        // var ends        =   document.getElementsByClassName("t_akhir");
        // var akhir       =   [];
        // for(var i = 0; i < ends.length; ++i) {
        //     akhir.push(ends[i].value);
        // }

        let mulai       = document.getElementById("t_mulai").value;
        let akhir       = document.getElementById("t_akhir").value;
    
        var note        =   document.getElementsByClassName("t_keterangan");
        var keterangan  =   [];
        for(var i = 0; i < note.length; ++i) {
            keterangan.push(note[i].value ? note[i].value : '');
        }
    
        $("#submit_harga").hide() ;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    
        $.ajax({
            url: "{{ route('hargakontrak.store') }}",
            method: "POST",
            data: {
                customer    :   customer ,
                item        :   item ,
                harga       :   harga ,
                unit        :   unit ,
                qty         :   qty ,
                mulai       :   mulai ,
                akhir       :   akhir ,
                keterangan  :   keterangan ,
            },
            success: function(data) {
                if (data.status == 400) {
                    showAlert(data.msg) ;
                } else {
                    $(".t_customer").val("").trigger('change') ;
                    $(".t_item").val("").trigger('change') ;
                    $(".t_harga").val("") ;
                    $(".t_unit").val("").trigger('change') ;
                    $(".t_qty").val("") ;
                    $(".t_mulai").val("") ;
                    $(".t_akhir").val("") ;
                    $(".t_keterangan").val("") ;
                    $('.temporary').remove();
                    $("#data_riwayat").load("{{ route('hargakontrak.index', ['key' => 'riwayat']) }}") ;
                    showNotif(data.msg);
                }
                $("#submit_harga").show() ;
            }
        });
    } else {
        return false;
    }
})
</script>

<script>
    x = 1 ;
function addRow() {
    row     =   '' ;
    row     +=  '<div class="row mt-2 temporary row-' + (x) + '">';
    row     +=  '    <div class="col pr-1">';
    row     +=  '        <select data-width="100%" data-placeholder="Data Customer" class="t_customer form-control select2">';
    row     +=  '            <option value=""></option>';
    row     +=  '            @foreach ($customer as $row)';
    row     +=  '            <option value="{{ $row->id }}">{{ $row->kode }}. {{ $row->nama }}</option>';
    row     +=  '            @endforeach';
    row     +=  '        </select>';
    row     +=  '    </div>';

    row     +=  '    <div class="col-auto pl-1">';
    row     +=  '        <button onclick="deleteRow(' + (x) + ')" class="btn btn-danger"><i class="fa fa-trash"></i></button>';
    row     +=  '    </div>';
    row     +=  '</div>';


    $('#customer-loop').append(row);
    $('.select2').select2({
        theme: 'bootstrap4'
    })

    x++;
}

function deleteRow(rowid){
    $('.row-'+rowid).remove();
}
</script>

<script>
    y = 1 ;
function addItem() {
    row     =   '' ;

    row +=  '<div class="temporary">' ;
    row +=  '<div class="bg-danger text-right cursor px-1 text-light row-' + (y) + '" onclick="deleteItem(' + (y) + ')">' ;
    row +=  '<i class="fa fa-trash"></i> Hapus' ;
    row +=  '</div>' ;
    row +=  '<div class="border p-2 mb-2 row-' + (y) + '">' ;
    row +=  '    <div class="form-group">' ;
    row +=  '     <label for="item">Item</label> * <span class="text-danger">(pilih salah satu)</span><br>';
    row +=  '     <label class="mt-2 px-2 pt-2 rounded status-info">';
    row +=  '         <input id="frozen' + y + '" data-ke="' + y + '" type="checkbox" name="pilih' + y + '" onclick="check('+y+', '+"'frozen'"+');"> <label for="frozen'+y+'">Frozen</label>';
    row +=  '     </label>';
    row +=  '     <label class="mt-2 px-2 pt-2 rounded status-success">';
    row +=  '         <input id="fresh' + y + '" data-ke="' + y + '" type="checkbox" name="pilih' + y + '" onclick="check('+y+', '+"'fresh'"+');"> <label for="fresh'+y+'">Fresh</label>';
    row +=  '     </label>';
    row +=  '     <div id="untukitem-' + y + '"></div>';
    row +=  '    </div>' ;

    row +=  '    <div class="row">' ;
    row +=  '        <div class="col pr-1">' ;
    row +=  '           Harga Satuan <span class="text-danger">*</span>' ;
    row +=  '            <div class="input-group mt-2">' ;
    row +=  '               <div class="input-group-prepend">' ;
    row +=  '                   <div class="input-group-text">Rp</div>' ;
    row +=  '               </div>' ;
    row +=  '                <input type="text" placeholder="Harga" min="0" class="t_harga form-control input-amount-'+y+'"  onkeyup="inputRupiah('+y+')" required>' ;
    row +=  '            </div>' ;
    row +=  '        </div>' ;

    row +=  '        <div class="col px-1">' ;
    row +=  '            <label for="unit">Unit</label>' ;
    row +=  '            <select data-width="100%" data-placeholder="Pilih Unit" class="t_unit form-control select2">' ;
    row +=  '                <option value=""></option>' ;
    row +=  '                <option value="Ekor">Ekor</option>' ;
    row +=  '                <option value="Kg" selected>Kg</option>' ;
    row +=  '            </select>' ;
    row +=  '        </div>' ;

    row +=  '        <div class="col pl-1">' ;
    row +=  '            <div class="form-group">' ;
    row +=  '                <label for="qty">Min. Qty</label>' ;
    row +=  '                <input type="number" placeholder="Tuliskan Min. Qty" min="0" class="form-control t_qty">' ;
    row +=  '            </div>' ;
    row +=  '        </div>' ;
    row +=  '    </div>' ;

    // row +=  '    <div class="row">' ;
    // row +=  '        <div class="col pr-1">' ;
    // row +=  '            <div class="form-group">' ;
    // row +=  '                <label for="mulai">Tanggal Mulai</label>' ;
    // row +=  '                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL')=='CGL') min="2023-01-01" @endif class="t_mulai form-control" value="{{ date("Y-m-d") }}">' ;
    // row +=  '            </div>' ;
    // row +=  '        </div>' ;

    // row +=  '        <div class="col pl-1">' ;
    // row +=  '            <div class="form-group">' ;
    // row +=  '                <label for="akhir">Tanggal Berakhir</label>' ;
    // row +=  '                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL')=='CGL') min="2023-01-01" @endif class="t_akhir form-control" value="{{ date("Y-m-d") }}">' ;
    // row +=  '            </div>' ;
    // row +=  '        </div>' ;
    // row +=  '    </div>' ;

    row +=  '    <div class="form-group">' ;
    row +=  '        <label for="keterangan">Keterangan</label>' ;
    row +=  '        <textarea rows="2" class="t_keterangan form-control" clas="form-control" placeholder="Tuliskan Keterangan"></textarea>' ;
    row +=  '    </div>' ;
    row +=  '</div>' ;
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

function check(input, item){
    $("#selectitem"+input).remove();
    $('#untukitem-'+input).empty();
    $('input[name="pilih'+input+'"]').on('click', function(e) {
        $('input[name="pilih'+input+'"]').prop('checked', false);
        $(this).prop('checked', true);
    });
    if($(this).prop('checked', true)) {
        if(item == 'fresh'){
            $('#untukitem-'+input).append(`
            <select data-width="100%" data-placeholder="Pilih Item" class="form-control select2 t_item">
                <option value=""></option>
                @foreach ($fresh as $row)
                    <option value="{{ $row->id }}">{{ $row->sku }}. {{ $row->nama }}</option>
                @endforeach
            </select>
            `);
            $('.select2').select2({
                theme: 'bootstrap4'
            })
        } else {
            $('#untukitem-'+input).append(`
            <select data-width="100%" data-placeholder="Pilih Item" class="form-control select2 t_item">
                <option value=""></option>
                @foreach ($frozen as $row)
                    <option value="{{ $row->id }}">{{ $row->sku }}. {{ $row->nama }}</option>
                @endforeach
            </select>
            `);
            $('.select2').select2({
                theme: 'bootstrap4'
            })
        }
    }
}
</script>
@endsection

@section('content')
<div class="my-4 text-center font-weight-bold text-uppercase">Harga Kontrak</div>

<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link tab-link" id="input-tab" data-toggle="tab" href="#input" role="tab" aria-controls="input"
            aria-selected="true">Input</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link tab-link" id="riwayat-tab" data-toggle="tab" href="#riwayat" role="tab"
            aria-controls="riwayat" aria-selected="false">Riwayat</a>
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
                                    <select name="customer" data-width="100%" data-placeholder="Data Customer"
                                        class="t_customer form-control select2">
                                        <option value=""></option>
                                        @foreach ($customer as $row)
                                        <option value="{{ $row->id }}">{{ $row->kode }}. {{ $row->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-auto pl-1">
                                    <button onclick="addRow()" class="btn btn-primary"><i
                                            class="fa fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div id="item-loop">
                            <div class="border p-2 mb-2">
                                <div class="row">
                                    <div class="col pr-1">
                                        <div class="form-group">
                                            <label for="mulai">Tanggal Mulai</label>
                                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' )
                                                min="2023-01-01" @endif class="form-control" id="t_mulai"
                                                value="{{ date("Y-m-d") }}">
                                        </div>
                                    </div>

                                    <div class="col pl-1">
                                        <div class="form-group">
                                            <label for="akhir">Tanggal Berakhir</label>
                                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' )
                                                min="2023-01-01" @endif class="form-control" id="t_akhir"
                                                value="{{ date("Y-m-d") }}">
                                        </div>
                                    </div>
                                </div>
                                <span class="text-danger">* Tanggal digunakan untuk setiap customer</span>
                            </div>
                            <div class="border p-2 mb-2">
                                <div class="form-group">

                                    <label for="item">Item</label> * <span class="text-danger">(pilih salah satu)</span><br>
                                    <label class="mt-2 px-2 pt-2 rounded status-info">
                                        <input id="frozen0" data-ke="0" type="checkbox" name="pilih0"
                                            onclick="check($(this).data('ke'), 'frozen');"> <label
                                            for="frozen0">Frozen</label>
                                    </label>
                                    <label class="mt-2 px-2 pt-2 rounded status-success">
                                        <input id="fresh0" data-ke="0" type="checkbox" name="pilih0"
                                            onclick="check($(this).data('ke'), 'fresh');"> <label
                                            for="fresh0">Fresh</label>
                                    </label>
                                    <div id="untukitem-0"></div>
                                </div>

                                <div class="row">
                                    <div class="col pr-1">
                                        Harga Satuan <span class="text-danger">*</span>
                                        <div class="input-group mt-2">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">Rp</div>
                                            </div>
                                            <input type="text" placeholder="Harga"
                                                class="form-control t_harga input-amount-0" onkeyup="inputRupiah(0)"
                                                required>
                                        </div>
                                    </div>

                                    <div class="col px-1">
                                        <label for="unit">Unit</label>
                                        <select data-width="100%" data-placeholder="Pilih Unit"
                                            class="form-control select2 t_unit">
                                            <option value=""></option>
                                            <option value="Ekor">Ekor</option>
                                            <option value="Kg" selected>Kg</option>
                                        </select>
                                    </div>

                                    <div class="col pl-1">
                                        <div class="form-group">
                                            <label for="qty">Min. Qty</label>
                                            <input type="number" placeholder="Tuliskan Min. Qty" min="0"
                                                class="form-control t_qty">
                                        </div>
                                    </div>
                                </div>



                                <div class="form-group">
                                    <label for="keterangan">Keterangan</label>
                                    <textarea rows="2" class="form-control t_keterangan"
                                        placeholder="Tuliskan Keterangan"></textarea>
                                </div>
                            </div>
                        </div>

                        <button class="btn btn-block btn-outline-success rounded-0 p-1 mb-4" onclick="addItem()">Tambah
                            Item</button>

                        <button id="submit_harga" class="btn btn-block btn-primary">Submit</button>
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