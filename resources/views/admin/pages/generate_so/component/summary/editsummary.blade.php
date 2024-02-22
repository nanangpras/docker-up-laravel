@extends('admin.layout.template')

@section('title', 'Edit Sales Order')

@section('footer')
<script>
    var jumlahItemEditSO = parseInt($('#jumlahItem').val()) + 1;

$('.select2').select2({
    theme: 'bootstrap4'
})

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

function addRow(){
    var row = '';
    var selected = '';
    row +=  '<div class="row-'+(jumlahItemEditSO)+'">' ;
    row +=  '<input type="hidden" name="ideditdatasummary[]" value="">' ;
    row +=  '<div class="bg-info px-2 text-light text-right"><span class="cursor" onclick="deleteRow('+(jumlahItemEditSO)+')"><i class="fa fa-trash"></i> Hapus</span></div>' ;
    row +=  '<section class="panel">' ;
    row +=  '    <div class="card-body p-2">' ;
    row +=  '       <div class="row">' ;
    row +=  '           <div class="col-12">' ;
    row +=  '               <div class="form-group">' ;
    // row +=  '                   Item <span class="text-danger text-small">*</span>' ;
    // row +=  '                   <select name="item[]" onchange="pilihItem('+(jumlahItemEditSO)+')" data-placeholder="Pilih Item" data-width="100%" class="form-control select2 item" required>' ;
    // row +=  '                       <option value=""></option>' ;
    // row +=  '                       @foreach ($item as $id => $row)' ;
    // row +=  '                       <option value="{{ $row->id }}">{{ $row->code_item }} - {{ $row->nama }}</option>' ;
    // row +=  '                       @endforeach' ;
    // row +=  '                   </select>' ;
    row += '                       Item * <span class="text-danger">(pilih salah satu)</span><br>'
    row += '                              <label class="mt-2 px-2 pt-2 rounded status-info">'
    row += '                                    <input id="frozen'+jumlahItemEditSO+'" data-ke="'+jumlahItemEditSO+'" type="checkbox" name="pilih'+jumlahItemEditSO+'" onclick="checkedit('+jumlahItemEditSO+', '+"'frozen'"+');"> <label for="frozen'+jumlahItemEditSO+'">Frozen</label>'
    row += '                                 </label>'
    row += '                               <label class="mt-2 px-2 pt-2 rounded status-success">'
    row += '                                    <input id="fresh'+jumlahItemEditSO+'" data-ke="'+jumlahItemEditSO+'" type="checkbox" name="pilih'+jumlahItemEditSO+'" onclick="checkedit('+jumlahItemEditSO+', '+"'fresh'"+');"> <label for="fresh'+jumlahItemEditSO+'">Fresh</label>'
    row += '                                </label>';
    row += '                        <div id="untukitem-'+jumlahItemEditSO+'">'
    row += '                        </div>'
    row += '                     <div class="hargakontrak" id="hargakontrak-'+jumlahItemEditSO+'"></div>';
    row +=  '               </div>' ;
    row +=  '           </div>' ;

    row +=  '           <div class="col-4 pr-1 parting-'+jumlahItemEditSO+'" hidden>' ;
    row +=  '               <div class="form-group">' ;
    row +=  '                   Parting <span class="text-danger text-small">*</span>' ;
    row +=  '                   <input type="number" name="parting[]" id="inputParting-'+jumlahItemEditSO+'" placeholder="Parting" autocomplete="off" class="form-control px-1">' ;
    row +=  '               </div>' ;
    row +=  '           </div>' ;

    row +=  '           <div class="col-6 pr-1 qty-'+jumlahItemEditSO+'">' ;
    row +=  '               <div class="form-group">' ;
    row +=  '                   Ekor/Pcs/Pack <span class="text-danger text-small">*</span>' ;
    row +=  '                   <input type="number" name="qty[]" id="inputqty'+jumlahItemEditSO+'" placeholder="Qty" autocomplete="off" class="form-control px-1" required>' ;
    row +=  '               </div>' ;
    row +=  '           </div>' ;

    row +=  '           <div class="col-6 pl-1 berat-'+jumlahItemEditSO+'">' ;
    row +=  '               <div class="form-group">' ;
    row +=  '                   Berat <span class="text-danger text-small">*</span>' ;
    row +=  '                   <input type="number" step="any" name="berat[]" id="inputberat'+jumlahItemEditSO+'" placeholder="Berat" autocomplete="off" class="form-control px-1" required>' ;
    row +=  '               </div>' ;
    row +=  '           </div>' ;
    row +=  '       </div>' ;

    row +=  '                <div class="row">' ;
    row +=  '                    <div class="col-6 pr-1">' ;
    row +=  '                       Harga Satuan<span class="text-danger text-small">*</span>';
    row +=  '                         <div class="input-group">';
    row +=  '                           <div class="input-group-prepend">';
    row +=  '                              <div class="input-group-text">Rp</div>';
    row +=  '                           </div>';
    row +=  '                           <input type="text" name="harga[]" placeholder="Harga" id="inputharga-'+jumlahItemEditSO+'" onkeyup="inputRupiah('+jumlahItemEditSO+')" autocomplete="off" class="form-control input-amount-'+jumlahItemEditSO+'" required required>';
    // row +=  '                           <input type="text" name="harga[]" id="inputharga0" placeholder="Harga" onkeyup="inputRupiah(0)" autocomplete="off" class="form-control input-amount-0 input-harga-kontrak" required>'
    row +=  '                         </div>';
    row +=  '                    </div>' ;
    row +=  '                    <div class="col-6 pl-1">' ;
    row +=  '                        <div class="form-group">' ;
    row +=  '                            Harga Cetakan <span class="text-danger text-small">*</span>' ;
    row +=  '                            <select id="harga_cetakan-'+jumlahItemEditSO+'" name="harga_cetakan[]" data-placeholder="Pilih Cetakan" data-width="100%" class="form-control select2" required>' ;
    row +=  '                                            <option value=""></option>' ;
    row +=  '                                            <option value="1">Kilogram</option>' ;
    row +=  '                                            <option value="2">Ekor/Pcs/Pack</option>' ;
    row +=  '                                        </select>' ;
    row +=  '                        </div>' ;
    row +=  '                    </div>' ;
    row +=  '                </div>' ;

    row +=  '       <div class="row">' ;
    row +=  '           <div class="col plastik-'+jumlahItemEditSO+'">' ;
    row +=  '               <div class="form-group">' ;
    row +=  '                   Plastik' ;
    row +=  '                   <select name="plastik[]" data-placeholder="Pilih Plastik" data-width="100%" class="form-control select2">' ;
    row +=  '                       <option value="">Curah</option>' ;
    row +=  '                       <option value="1">Meyer</option>' ;
    row +=  '                       <option value="2">Avida</option>' ;
    row +=  '                       <option value="3">Polos</option>' ;
    row +=  '                       <option value="4">Bukan Plastik</option>' ;
    row +=  '                       <option value="5">Mojo</option>' ;
    row +=  '                       <option value="6">Other</option>' ;
    row +=  '                   </select>' ;
    row +=  '               </div>' ;
    row +=  '           </div>' ;
    row +=  '           <div class="col-6 pl-1 bumbu-'+jumlahItemEditSO+'" hidden>' ;
    row +=  '               <div class="form-group">' ;
    row +=  '                   Bumbu' ;
    row +=  '                   <input type="text" name="bumbu[]" id="inputBumbu-'+jumlahItemEditSO+'" placeholder="Bumbu" autocomplete="off" class="form-control">' ;
    row +=  '               </div>' ;
    row +=  '           </div>' ;
    row +=  '       </div>' ;

    row +=  '       <div class="form-group">' ;
    row +=  '           Memo Line' ;
    row +=  '           <input type="text" name="memo[]" placeholder="Tuliskan memo" autocomplete="off" class="form-control">' ;
    row +=  '       </div>' ;
    row +=  '       <div class="form-group">' ;
    row +=  '           Internal Memo' ;
    row +=  '           <input type="text" name="internal_memo[]" placeholder="Tuliskan Internal Memo" autocomplete="off" class="form-control">' ;
    row +=  '       </div>' ;
    row +=  '       <div class="form-group">' ;
    row +=  '           Deskripsi' ;
    row +=  '           <input type="text" name="description_item[]" id="descriptionItem-'+jumlahItemEditSO+'" placeholder="Tuliskan Deskripsi" autocomplete="off" class="form-control">' ;
    row +=  '       </div>' ;
    row +=  '   </div>' ;
    row +=  '</div>' ;
    row +=  '</div>' ;

    $('.data-loop').append(row);
    $('.select2').select2({
        theme: 'bootstrap4'
    })
    $('#jumlahItem').val(jumlahItemEditSO)
    jumlahItemEditSO++; 
    
}

function checkedit(id, item){
    console.log($('#jumlahItem').val());
    $("#selectitem"+id).remove();
    $('#untukitem-'+id).empty();
    $('input[name="pilih'+id+'"]').on('click', function(e) {
        $('input[name="pilih'+id+'"]').prop('checked', false);
        $(this).prop('checked', true);
    });

    if(item == 'fresh'){
        $('#untukitem-'+id).append(`
            <select name="item[]" data-ke="${id}" id="selectitem${id}" onchange="pilihItem(${id})" data-placeholder="Pilih Item" data-width="100%" class="form-control select2 item fresh" required>
                <option value=""></option>
                @foreach ($fresh as $id => $row)
                @php 
                    $tengah = (integer)substr(preg_replace('/[^0-9]/', '', $row->nama), 0,2) ?? "0";
                    if($tengah!=0){
                        $tengah = $tengah+1;
                    }
                @endphp
                <option value="{{ $row->id }}" data-item="{{ $row->nama }}" data-beratkali="{{$tengah ?? 0}}">{{ $row->sku }} - {{ $row->nama }}</option>
                @endforeach
            </select>
        `);
        $('.select2').select2({
            theme: 'bootstrap4'
        })
    } else {
        $('#untukitem-'+id).append(`
            <select name="item[]" data-ke="${id}" id="selectitem${id}"  onchange="pilihItem(${id})" data-placeholder="Pilih Item" data-width="100%" class="form-control select2 item frozen" required>
                <option value=""></option>
                @foreach ($frozen as $id => $row)
                @php 
                    $tengah = (integer)substr(preg_replace('/[^0-9]/', '', $row->nama), 0,2) ?? "0";
                    if($tengah!=0){
                        $tengah = $tengah+1;
                    }
                @endphp
                <option value="{{ $row->id }}" data-item="{{ $row->nama }}" data-beratkali="{{$tengah}}">{{ $row->sku }} - {{ $row->nama }}</option>
                @endforeach
            </select>
        `);
        $('.select2').select2({
            theme: 'bootstrap4'
        })
    }
}







function deleteRow(rowid,idSOList){
    // console.log(idSOList ?? '')
    var id_so = "{{$data->id}}";
    if(idSOList != undefined){
        $.ajax({
            url: "{{ route('buatso.destroy') }}",
            type: "POST",
            data: {
                idSOList: idSOList,
                id_so: id_so,
                _token: '{{ csrf_token() }}'
            },
            dataType: "JSON",
            success: function(data) {
                // console.log(data)
                if(data.status == 'success'){
                    $('.row-'+rowid).remove();
                    showNotif(data.msg)
                }
            }
        });
    } else {
        $('.row-'+rowid).remove();
    }
}

function pilihItem(rowid){
    // console.log(rowid)
    let nama_item = $('.row-'+rowid).find('.item option:selected').text()
    
    var konsumen = $(".customer option:selected").val() ;
    var item    = $('.row-'+rowid).find('.item option:selected').val();
    var harga_kontrak = 0;
    // var unit = '';
    let tanggalawal = $("#tanggal_so").val();
    let tanggalakhir = $("#tanggal_kirim").val();
    $("#hargakontrak-" + rowid).load("{{ route('buatso.index', ['key' => 'harga_kontrak']) }}&customer=" + konsumen + "&item=" + item + "&tanggalawal=" +  tanggalawal + "&tanggalakhir=" + tanggalakhir, function(){
        harga_kontrak   = $('#harga-'+item).val();
        unit            = $('#unit-'+item).val();

        var harga_auto = document.getElementById("inputharga-"+rowid);
        // $('#inputharga-'+rowid).val(harga_kontrak);

        if(harga_kontrak === undefined){
            harga_auto.value =0;  
        } else {
            harga_auto.value = harga_kontrak;
        }
        
        console.log(harga_kontrak);
        console.log(unit);
        
        $('#harga_cetakan-'+rowid).val(unit).trigger('change');

    });
    // if(nama_item.includes('PARTING MARINASI')){
    if(nama_item.includes('PARTING (M)')){
        $('.bumbu-'+rowid).attr('hidden',false)
        $('.parting-'+rowid).attr('hidden',false)
        $('.plastik-'+rowid).removeClass('col')
        $('.parting-'+rowid).removeClass('col')
        $('.qty-'+rowid).removeClass('col-6 pr-1')
        $('.berat-'+rowid).removeClass('col-6 pl-1')
        $('.qty-'+rowid).addClass('col-4 px-1')
        $('.berat-'+rowid).addClass('col-4 pl-1')
        $('.plastik-'+rowid).addClass('col-6 pr-1')

    // } else if(nama_item.includes('MARINASI')){
    } else if(nama_item.includes('(M)')){
        $('.bumbu-'+rowid).attr('hidden',false)
        $('.parting-'+rowid).attr('hidden',true)
        $('#inputParting-'+rowid).val('')
        $('.plastik-'+rowid).removeClass('col')
        $('.plastik-'+rowid).addClass('col-6 pr-1')

    } else if(nama_item.includes('PART') || nama_item.includes('PARTING')){
        console.log(rowid)
        $('.bumbu-'+rowid).attr('hidden',true)
        $('#inputBumbu-'+rowid).val('')
        $('.parting-'+rowid).attr('hidden',false)
        $('.parting-'+rowid).removeClass('col')
        $('.qty-'+rowid).removeClass('col-6 pr-1')
        $('.berat-'+rowid).removeClass('col-6 pl-1')
        $('.plastik-'+rowid).removeClass('col-6 pr-1')
        $('.plastik-'+rowid).addClass('col')
        $('.qty-'+rowid).addClass('col-4 px-1')
        $('.berat-'+rowid).addClass('col-4 pl-1')
    } else {
        $('.bumbu-'+rowid).attr('hidden',true)
        $('#inputBumbu-'+rowid).val('')
        $('.parting-'+rowid).attr('hidden',true)
        $('#inputParting-'+rowid).val('')
        $('.qty-'+rowid).removeClass('col-4 px-1')
        $('.berat-'+rowid).removeClass('col-4 pl-1')
        $('.qty-'+rowid).addClass('col-6 pr-1')
        $('.berat-'+rowid).addClass('col-6 pl-1')
        $('.plastik-'+rowid).removeClass('col-6 pr-1')
        $('.plastik-'+rowid).addClass('col')
    }


    $("#descriptionItem-"+rowid).val($('option:selected', $('#selectitem'+rowid)).attr('data-item'))

    var selected_berat_kali = 0;
    var total_berat = 0;
    console.log($('option:selected', $('#selectitem'+rowid)).attr('data-beratkali'));
    
    selected_berat_kali = $('option:selected', $('#selectitem'+rowid)).attr('data-beratkali');
    
    // console.log($('#inputqty'+rowid).val());
    
    if(selected_berat_kali!=0){
        $('#inputqty'+rowid).on('keyup', function(){
    
            console.log($('#inputqty'+rowid).val());
    
            if($('#inputqty'+rowid).val()!=undefined && $('#inputqty'+rowid).val()>0){
                total_berat = (selected_berat_kali/10)*$('#inputqty'+rowid).val();
                $('#inputberat'+rowid).val(total_berat.toFixed(1));
            }
        })
    }    

}

$('.edit-input-qty').on('keyup', function(){
    rowid = $(this).attr('data-id');
    console.log(rowid)

    var selected_berat_kali = 0;
    var total_berat = 0;
    
    selected_berat_kali = $('option:selected', $('#selectitem'+rowid)).attr('data-beratkali');
    
    if(selected_berat_kali!=0){
        if($('#inputqty'+rowid).val()!=undefined && $('#inputqty'+rowid).val()>0){
            total_berat = (selected_berat_kali/10)*$('#inputqty'+rowid).val();
            $('#inputberat'+rowid).val(total_berat.toFixed(1));
        }
    }   
})


$(function() {

    let jumlahItem = $('#jumlahItem').val();
    // console.log(jumlahItem)
    for(let i = 1; i <= jumlahItem; i++){
        // addRow();
        let nama_item = $('.row-'+i).find('.item option:selected').text()
        if(nama_item.includes('(M)')){
            $('.bumbu-'+i).attr('hidden',false)
        } else if(nama_item.includes('PART') || nama_item.includes('PARTING')){
            // console.log(i)
            $('.bumbu-'+i).attr('hidden',true)
            $('.plastik-'+i).removeClass('col-6 pr-1')
            $('.plastik-'+i).toggleClass('col')
            // console.log($('.bumbu-'+i))
        } else {
            $('.bumbu-'+i).attr('hidden',true)
            $('.plastik-'+i).removeClass('col-6 pr-1')
            $('.plastik-'+i).toggleClass('col')
        }
    }
})

</script>

<script>
    $('#selesaikan').on('click', function(e) {
    for(let i = 0; i < jumlahItemEditSO; i++){
        if ($('input[name="pilih'+i+'"]').length){
            if($('input[name="pilih'+i+'"]').filter(':checked').length < 1){
                showAlert('Terdapat item yang belum dipilih!')
                return false;
            }
        }
    }

    var hargaMins      =   document.getElementsByName('harga[]');
    var hargaMin       =   [];
    for(var i = 0; i < hargaMins.length; ++i) {
        // console.log(hargaMins[i].value.replace(/\./g, ""))
        if (hargaMins[i].value.replace(/\./g, "") == 0) {
            showAlert('Terdapat harga Rp 0');
            return false;
        }
    }

    var result = confirm("Yakin submit sales order?");
    if (result === true) {
        $('#selesaikan').submit();
    } else {
        return false;
    }
})

</script>
@endsection

@section('content')
<div class="col"><a href="{{ route('buatso.index') }}#summary"><i class="fa fa-arrow-left"></i> Kembali</a></div>
<div class="my-4 text-center font-weight-bold text-uppercase">Edit Sales Order</div>

<div class="tab-content mt-2">
    <div class="tab-pane fade show active" id="input" role="tabpanel" aria-labelledby="input-tab">
        <input type="hidden" id="jumlahItem" value="{{ count($data_so_list) }}">
        <form action="{{ route('buatso.update') }}" method="post">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <section class="panel">
                        <div class="card-header font-weight-bold">Informasi Transaksi</div>
                        <div class="card-body">
                            <input type="hidden" name="idsummary" value="{{ $data->id }}">
                            <div class="form-group">
                                Customer
                                <select name="customer" data-placeholder="Pilih Customer" data-width="100%"
                                    class="form-control select2 customer" required readonly>
                                    <option value="{{$data->customer_id}}"> {{$data->socustomer->nama ?? ""}}</option>
                                </select>
                            </div>
                            {{-- <div class="form-group">
                                Sales Channel
                                <select name="sales_channel" data-placeholder="Pilih Sales Channel" data-width="100%"
                                    class="form-control select2" required>
                                    <option value="">- Sales Channel -</option>
                                    <option value="Trading" {{ $data->sales_channel == 'Trading' ? 'selected' : ''
                                        }}>Trading</option>
                                    <option value="Catering Industry" {{ $data->sales_channel == 'Catering Industry' ?
                                        'selected' : ''}}>Catering Industry</option>
                                    <option value="Hotel" {{ $data->sales_channel == 'Hotel' ? 'selected' : '' }}>Hotel
                                    </option>
                                    <option value="Moden Market" {{ $data->sales_channel == 'Moden Market' ? 'selected'
                                        : '' }}>Modern Market</option>
                                    <option value="Restaurant" {{ $data->sales_channel == 'Restaurant' ? 'selected' : ''
                                        }}>Restaurant</option>
                                    <option value="Fried Chicken" {{ $data->sales_channel == 'Fried Chicken' ?
                                        'selected' : ''}}>Fried Chicken</option>
                                    <option value="E-Commerce" {{ $data->sales_channel == 'E-Commerce' ? 'selected' :
                                        ''}}>E-Commerce</option>
                                    <option value="Trading LP" {{ $data->sales_channel == 'Trading LP' ? 'selected' :
                                        ''}}>Trading LP</option>
                                    <option value="Other" {{ $data->sales_channel == 'Other' ? 'selected' : ''}}>Other
                                    </option>
                                </select>
                            </div> --}}
                            {{-- <div class="form-group">
                                Alamat Customer
                                <select name="customer_address" data-placeholder="Pilih Alamat" data-width="100%"
                                    class="form-control select2">
                                    <option value="">- Alamat Customer -</option>
                                </select>
                            </div> --}}

                            <div class="row">
                                <div class="col pr-1">
                                    <div class="form-group">
                                        Tanggal Sales Order
                                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) min="2023-01-01"
                                            @endif name="tanggal_so" id="tanggal_so" class="form-control"
                                            value="{{ $data->tanggal_so }}" required>
                                    </div>
                                </div>

                                <div class="col pl-1">
                                    <div class="form-group">
                                        Tanggal Kirim
                                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) min="2023-01-01"
                                            @endif name="tanggal_kirim" id="tanggal_kirim" class="form-control"
                                            value="{{ $data->tanggal_kirim }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                PO Customer
                                <input type="text" name="po_number" placeholder="Tuliskan NO PO" autocomplete="off"
                                    class="form-control" value="{{ $data->po_number }}">
                            </div>
                            <div class="form-group">
                                Memo Header
                                <textarea name="memo_head" id="" cols="30" rows="10"
                                    class="form-control">{{ $data->memo }}</textarea>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="col-md-6">
                    <div class="data-loop">
                        @foreach ($data_so_list as $dataSO)
                        <div class="row-{{ $loop->iteration }}">
                            <input type="hidden" id="ideditdatasummary" name="ideditdatasummary[]"
                                value="{{ $dataSO->id }}">
                            <div class="bg-info px-2 py-2 text-light text-right"><span class="cursor"
                                    onclick="deleteRow({{ $loop->iteration }},{{ $dataSO->id }})"><i
                                        class="fa fa-trash"></i> Hapus</span></div>
                            <section class="panel">
                                <div class="card-body p-2">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                @php
                                                $tengah = (integer)substr(preg_replace('/[^0-9]/', '',
                                                $dataSO->item_nama), 0,2) ?? "0";
                                                if($tengah!=0){
                                                $tengah = $tengah+1;
                                                }
                                                @endphp
                                                Item <span class="text-danger text-small">*Item tidak bisa diganti, bisa
                                                    buat item baru</span>
                                                <select name="item[]" id="selectitem{{$loop->iteration}}"
                                                    onchange="pilihItem({{ $loop->iteration }})"
                                                    data-placeholder="Pilih Item" data-width="100%"
                                                    class="form-control item" required>
                                                    <option value="{{ $dataSO->item_id }}" data-beratkali="{{$tengah}}">
                                                        {{ $dataSO->item_nama }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        @if(!$dataSO->parting == '')
                                        <div class="col-4 pr-1 parting-{{ $loop->iteration }}">
                                            <div class="form-group">
                                                Parting
                                                <input type="number" name="parting[]"
                                                    id="inputParting-{{ $loop->iteration }}" placeholder="Parting"
                                                    autocomplete="off" class="form-control px-1"
                                                    value="{{ $dataSO->parting }}" readonly>
                                                <span class="text-danger text-small">*Potongan tidak bisa diganti</span>
                                            </div>
                                        </div>

                                        <div class="col-4 px-1 qty-{{ $loop->iteration }}">
                                            <div class="form-group">
                                                Ekor/Pcs/Pack <span class="text-danger text-small">*</span>
                                                <input type="number" name="qty[]" placeholder="Qty"
                                                    id="inputqty{{$loop->iteration}}" autocomplete="off"
                                                    data-id="{{$loop->iteration}}"
                                                    class="form-control edit-input-qty px-1" value="{{ $dataSO->qty}}"
                                                    required>
                                            </div>
                                        </div>

                                        <div class="col-4 pl-1 berat-{{ $loop->iteration }}">
                                            <div class="form-group">
                                                Berat <span class="text-danger text-small">*</span>
                                                <input type="number" name="berat[]" step="any" placeholder="Berat"
                                                    id="inputberat{{$loop->iteration}}" autocomplete="off"
                                                    class="form-control px-1" value="{{ $dataSO->berat }}" required>
                                            </div>
                                        </div>

                                        @else
                                        <div class="col-4 pr-1 parting-{{ $loop->iteration }}" hidden>
                                            <div class="form-group">
                                                Parting <span class="text-danger text-small">*Potongan tidak bisa
                                                    diganti</span>
                                                <input type="number" name="parting[]"
                                                    id="inputParting-{{ $loop->iteration }}" placeholder="Parting"
                                                    autocomplete="off" class="form-control px-1"
                                                    value="{{ $dataSO->parting }}">
                                            </div>
                                        </div>
                                        <div class="col-6 pr-1 qty-{{ $loop->iteration }}">
                                            <div class="form-group">
                                                Ekor/Pcs/Qty <span class="text-danger text-small">*</span>
                                                <input type="number" name="qty[]" id="inputqty{{$loop->iteration}}"
                                                    data-id="{{$loop->iteration}}" placeholder="Qty" autocomplete="off"
                                                    class="form-control edit-input-qty px-1" value="{{ $dataSO->qty}}"
                                                    required>
                                            </div>
                                        </div>

                                        <div class="col-6 pl-1 berat-{{ $loop->iteration }}">
                                            <div class="form-group">
                                                Berat <span class="text-danger text-small">*</span>
                                                <input type="number" name="berat[]" id="inputberat{{$loop->iteration}}"
                                                    step="any" placeholder="Berat" autocomplete="off"
                                                    class="form-control px-1" value="{{ $dataSO->berat }}" required>
                                            </div>
                                        </div>

                                        @endif
                                    </div>

                                    <div class="row">
                                        <div class="col-6 pr-1">
                                            Harga Satuan<span class="text-danger text-small">*</span>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">Rp</div>
                                                </div>
                                                <input type="text" name="harga[]" placeholder="Harga"
                                                    onkeyup="inputRupiah({{ $loop->iteration }})" autocomplete="off"
                                                    class="form-control input-amount-{{ $loop->iteration }}" required
                                                    value="{{ number_format($dataSO->harga, 0, ',', '.') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-6 pl-1">
                                            <div class="form-group">
                                                Harga Cetakan <span class="text-danger text-small">*</span>
                                                <select name="harga_cetakan[]" data-placeholder="Pilih Cetakan"
                                                    data-width="100%" class="form-control select2" required>
                                                    <option value=""></option>
                                                    <option value="1" {{ $dataSO->harga_cetakan == '1' ? 'selected' : ''
                                                        }}>Kilogram</option>
                                                    <option value="2" {{ $dataSO->harga_cetakan == '2' ? 'selected' : ''
                                                        }}>Ekoran</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row bumbusectionawal-{{ $loop->iteration }}">

                                        <div class="col-6 pr-1 plastik-{{ $loop->iteration }}">
                                            <div class="form-group">
                                                Plastik
                                                <select name="plastik[]" data-placeholder="Pilih Plastik"
                                                    data-width="100%" class="form-control select2">
                                                    <option value=""></option>
                                                    {{-- <option value="" {{ $dataSO->plastik == '' ? 'selected' : ''
                                                        }}>Curah</option> --}}
                                                    <option value="1" {{ $dataSO->plastik == '1' ? 'selected' : ''
                                                        }}>Meyer</option>
                                                    <option value="2" {{ $dataSO->plastik == '2' ? 'selected' : ''
                                                        }}>Avida</option>
                                                    <option value="3" {{ $dataSO->plastik == '3' ? 'selected' : ''
                                                        }}>Polos</option>
                                                    <option value="4" {{ $dataSO->plastik == '4' ? 'selected' : ''
                                                        }}>Curah</option>
                                                    <option value="5" {{ $dataSO->plastik == '5' ? 'selected' : ''
                                                        }}>Mojo</option>
                                                    <option value="6" {{ $dataSO->plastik == '6' ? 'selected' : ''
                                                        }}>Other</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-6 pl-1 bumbu-{{ $loop->iteration }}">
                                            <div class="form-group">
                                                Bumbu
                                                <input type="text" name="bumbu[]" id="inputBumbu-{{ $loop->iteration }}"
                                                    placeholder="Bumbu" autocomplete="off" class="form-control"
                                                    value="{{ $dataSO->bumbu }}">
                                            </div>
                                        </div>

                                    </div>

                                    <div class="form-group">
                                        Memo Line <span class="text-danger text-small">* Informasi Ekor/Qty/Pack edit di
                                            sini</span>
                                        <input type="text" name="memo[]" placeholder="Tuliskan memo" autocomplete="off"
                                            class="form-control" value="{{ $dataSO->memo }}">
                                    </div>

                                    <div class="form-group">
                                        Internal Memo
                                        <input type="text" name="internal_memo[]" placeholder="Tuliskan Internal Memo"
                                            autocomplete="off" class="form-control"
                                            value="{{ $dataSO->internal_memo }}">
                                    </div>

                                    <div class="form-group">
                                        Deskripsi
                                        <input type="text" name="description_item[]" placeholder="Tuliskan Deskripsi"
                                            autocomplete="off" class="form-control"
                                            value="{{ $dataSO->description_item }}">
                                    </div>
                                </div>
                            </section>
                        </div>
                        @endforeach
                    </div>

                    <span onclick="addRow()" class="cursor btn btn-green btn-sm"><i class="fa fa-plus"></i>
                        Tambah</span>
                    <span class="small text-danger text-small float-right">*) Wajib Diisi</span>
                    <br><br>
                </div>
                <div class="col-md-12">
                    <div class="card-footer text-right">
                        <button class="btn btn-blue form-control" id="selesaikan">UPDATE</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@php
$ns = App\Models\Netsuite::where('tabel_id', $data->id)
->where('record_type', 'sales_order')
->first();
@endphp

@if(User::setIjin('superadmin'))
<hr>

<table class="table default-table">
    <thead>
        <tr>
            <th>
                <input type="checkbox" id="ns-checkall">
            </th>
            <th>ID</th>
            <th>C&U Date</th>
            <th>TransDate</th>
            <th>Label</th>
            <th>Activity</th>
            <th>Location</th>
            <th>IntID</th>
            <th>Paket</th>
            <th width="100px">Data</th>
            <th width="100px">Action</th>
            <th>Response</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @if($ns ?? false)
        @include('admin.pages.log.netsuite_one', ($netsuite = $ns))
        @endif

    </tbody>
</table>
@endif

@endsection