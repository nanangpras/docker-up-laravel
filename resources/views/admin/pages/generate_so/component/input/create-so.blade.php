<form action="{{ route('buatso.store') }}" method="post">
    @csrf
    <div class="row">
        <div class="col-md-6">
            <section class="panel">
                <div class="card-header font-weight-bold">Informasi Transaksi</div>
                <div class="card-body">
                    <div class="form-group">
                        Customer
                        <select name="customer" id="customer" onchange="pilih_konsumen()"
                            data-placeholder="Pilih Customer" data-width="100%" class="form-control select2" required>
                            <option value=""></option>
                            @foreach ($customer as $id => $row)
                            <option value="{{ $row->id }}"> {{ $row->kode }} - {{ $row->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col pr-1">
                            <div class="form-group">
                                Tanggal Sales Order
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif name="tanggal_so" id="tanggal_so" class="form-control"
                                    value="{{date('Y-m-d')}}" required>
                            </div>
                        </div>

                        <div class="col pl-1">
                            <div class="form-group">
                                Tanggal Kirim
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif name="tanggal_kirim" id="tanggal_kirim" class="form-control"
                                    value="{{date('Y-m-d', strtotime('+1 day'))}}" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        PO Customer
                        <input type="text" name="po_number" placeholder="Tuliskan NO PO" autocomplete="off"
                            class="form-control" required>
                        <span class="text-danger text-small">*Wajib diisi, isi dengan strip (-) jika memang tidak ada
                            nomor PO nya</span>
                    </div>
                    <div class="form-group">
                        Memo Header
                        <input type="text" name="memo_head" placeholder="Tuliskan memo" autocomplete="off"
                            class="form-control" id="memo_autocomplete">
                    </div>
                </div>
            </section>
        </div>
        <div class="col-md-6">
            <div id="input_items" style="display: none">
                <div class="bg-info text-light mb-2 px-2">
                    Order Item
                </div>
                <div class="data-loop">
                    <div class="row-0">
                        <section class="panel">
                            <div class="card-body p-2">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            Item * <span class="text-danger">(pilih salah satu)</span><br>
                                            <label class="mt-2 px-2 pt-2 rounded status-info">
                                                <input id="frozen" data-ke="0" type="checkbox" name="pilih0"
                                                    onclick="check($(this).data('ke'), 'frozen');"> <label
                                                    for="frozen">Frozen</label>
                                            </label>
                                            <label class="mt-2 px-2 pt-2 rounded status-success">
                                                <input id="fresh" data-ke="0" type="checkbox" name="pilih0"
                                                    onclick="check($(this).data('ke'), 'fresh');"> <label
                                                    for="fresh">Fresh</label>
                                            </label>
                                            <div id="untukitem-0"></div>
                                            <div class="hargakontrak" id="hargakontrak-0"></div>
                                        </div>
                                    </div>

                                    <div class="col-6 pr-1 qty-0">
                                        <div class="form-group">
                                            Ekor/Pcs/Pack
                                            <input type="number" id="inputqty0" name="qty[]" onchange="setHargaTotal(0)"
                                                onkeyup="setHargaTotal(0)" placeholder="Qty" autocomplete="off"
                                                class="form-control px-1">
                                        </div>
                                    </div>
                                    <div class="col-6 pl-1 unit-0">
                                        <div class="form-group">
                                            Unit
                                            <select name="qty_unit[]" data-placeholder="Unit" data-width="100%"
                                                class="form-control select2">
                                                <option value=""></option>
                                                <option value="Ekor">Ekor</option>
                                                <option value="Pcs">Pcs</option>
                                                <option value="Pack">Pack</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-6 pr-1 berat-0">
                                        <div class="form-group">
                                            Berat <span class="text-danger">*</span>
                                            <input type="number" name="berat[]" onchange="setHargaTotal(0)"
                                                onkeyup="setHargaTotal(0)" id="inputberat0" step="0.01"
                                                placeholder="Berat" autocomplete="off" class="form-control px-1"
                                                required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-4 pr-1">
                                        Harga Satuan <span class="text-danger">*</span>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">Rp</div>
                                            </div>
                                            <input type="text" name="harga[]" id="inputharga0" placeholder="Harga"
                                                onkeyup="inputRupiah(0); setHargaTotal(0)" autocomplete="off"
                                                class="form-control input-amount-0 input-harga-kontrak" required>
                                        </div>
                                    </div>
                                    <div class="col-4 pl-1">
                                        <div class="form-group">
                                            Harga Cetakan <span class="text-danger">*</span>
                                            <select name="harga_cetakan[]" id="hargacetakan0"
                                                data-placeholder="Pilih Cetakan" onchange="setHargaTotal(0)"
                                                data-width="100%" class="form-control" required>
                                                <option value="1">Kilogram</option>
                                                <option value="2">Ekor/Pcs/Pack</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-4 pl-1">
                                        <div class="form-group">
                                            Total Harga
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">Rp</div>
                                                </div>
                                                <input type="text" name="harga_total[]" id="inputhargatotal0"
                                                    placeholder="Harga Total" autocomplete="off"
                                                    class="form-control input-harga-total-0" readonly required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row bumbuitem-0"></div>
                                <div class="row bumbusectionawal-0">
                                    <div class="col plastik-0">
                                        <div class="form-group">
                                            Plastik
                                            <select name="plastik[]" data-placeholder="Pilih Plastik" data-width="100%"
                                                class="form-control select2 plastik-0">
                                                <option value=""></option>
                                                <option value="1">Meyer</option>
                                                <option value="2">Avida</option>
                                                <option value="3">Polos</option>
                                                <option value="4">Curah</option>
                                                <option value="5">Mojo</option>
                                                <option value="6">Other</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-6 pr-1 parting-0" hidden>
                                        <div class="form-group">
                                            Parting <span class="text-danger">*</span>
                                            <input type="number" name="parting[]" id="inputParting-0"
                                                placeholder="Parting" autocomplete="off" class="form-control px-1">
                                        </div>
                                    </div>
                                    <div class="col-6 pl-1 bumbu-0" hidden>
                                        <div class="form-group">
                                            Bumbu
                                            <input type="text" name="bumbu[]" placeholder="Bumbu" autocomplete="off"
                                                class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    Memo Line
                                    <input type="text" name="memo[]" placeholder="Tuliskan memo" autocomplete="off"
                                        class="form-control">
                                </div>
                                <div class="form-group">
                                    Internal Memo
                                    <input type="text" name="internal_memo[]" placeholder="Tuliskan Internal Memo"
                                        autocomplete="off" class="form-control">
                                </div>

                                <div class="form-group">
                                    Deskripsi
                                    <input type="text" name="description_item[]" id="descriptionItem-0"
                                        placeholder="Tuliskan Deskripsi" autocomplete="off" class="form-control">
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
                <span onclick="addRow()" class="cursor btn btn-green btn-sm"><i class="fa fa-plus"></i> Tambah</span>
                <br><br>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card-footer text-right">
                <button class="btn btn-blue form-control" id="selesaikan">SIMPAN</button>
            </div>
        </div>
    </div>
</form>
<link href="{{asset('plugin')}}/jquery-ui.css" rel="stylesheet">
<script src="{{asset('plugin')}}/jquery-ui.js"></script>
{{-- <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script> --}}
<script>
    /**********************************************************************/
    /*                                                                    */
    /*                  INI BAGIAN TAB  INPUT ORDERAN                     */
    /*                                                                    */
    /**********************************************************************/
    $('.select2').select2({
        theme: 'bootstrap4'
    })

    $("#input-tab").on('click', function(){
        //
    });

    var jumlahItemInputSO   = 1;
    let poin                = 0;

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
    function inputTotalRupiah(e) {
        $('.input-harga-total-'+e).val(formatAmount($('.input-harga-total-'+e).val()));
    }

    function addRow(){

        var row = '';
        row +=  '<div class="row-'+(jumlahItemInputSO)+'">' ;
        row +=  '<input type="hidden" name="ideditdatasummary[]" value="">' ;
        row +=  '<div class="bg-info px-2 text-light text-right"><span class="cursor" onclick="deleteRow('+(jumlahItemInputSO)+')"><i class="fa fa-trash"></i> Hapus</span></div>' ;
        row +=  '<section class="panel">' ;
        row +=  '    <div class="card-body p-2">' ;
        row +=  '        <div class="form-group">' ;
        row += '         Item * <span class="text-danger">(pilih salah satu)</span><br>'
        row += '            <label class="mt-2 px-2 pt-2 rounded status-info">'
        row += '                             <input id="frozen'+jumlahItemInputSO+'" data-ke="'+jumlahItemInputSO+'" type="checkbox" name="pilih'+jumlahItemInputSO+'" onclick="check('+jumlahItemInputSO+', '+"'frozen'"+');"> <label for="frozen'+jumlahItemInputSO+'">Frozen</label>'
        row += '                                 </label>'
        row += '                               <label class="mt-2 px-2 pt-2 rounded status-success">'
        row += '                                    <input id="fresh'+jumlahItemInputSO+'" data-ke="'+jumlahItemInputSO+'" type="checkbox" name="pilih'+jumlahItemInputSO+'" onclick="check('+jumlahItemInputSO+', '+"'fresh'"+');"> <label for="fresh'+jumlahItemInputSO+'">Fresh</label>'
        row += '                                </label>'
        row += '                        <div id="untukitem-'+jumlahItemInputSO+'"></div>'
        row += '                        <div class="hargakontrak" id="hargakontrak-'+jumlahItemInputSO+'"></div>'
        row += '                     </div>'



        row +=  '           <div class="row">' ;
        row +=  '               <div class="col-6 pr-1 qty-'+jumlahItemInputSO+'">' ;
        row +=  '                   <div class="form-group">' ;
        row +=  '                       Ekor/Pcs/Pack' ;
        row +=  '                       <input type="number" id="inputqty'+jumlahItemInputSO+'" onchange="setHargaTotal('+jumlahItemInputSO+')" onkeyup="setHargaTotal('+jumlahItemInputSO+')" name="qty[]" placeholder="Qty" autocomplete="off" class="form-control px-1">' ;
        row +=  '                   </div>' ;
        row +=  '               </div>' ;
        row +=  '               <div class="col-6 pl-1 unit-'+jumlahItemInputSO+'">';
        row +=  '                   <div class="form-group">';
        row +=  '                       Unit';
        row +=  '                       <select name="qty_unit[]" data-placeholder="Unit" data-width="100%" class="form-control select2">';
        row +=  '                           <option value=""></option>';
        row +=  '                           <option value="Ekor">Ekor</option>';
        row +=  '                           <option value="Pcs">Pcs</option>';
        row +=  '                           <option value="Pack">Pack</option>';
        row +=  '                       </select>';
        row +=  '                   </div>';
        row +=  '               </div>';

        row +=  '               <div class="col-6 pr-1 berat-'+jumlahItemInputSO+'">' ;
        row +=  '                   <div class="form-group">' ;
        row +=  '                       Berat <span class="text-danger">*</span>' ;
        row +=  '                       <input type="number" name="berat[]" id="inputberat'+jumlahItemInputSO+'" onchange="setHargaTotal('+jumlahItemInputSO+')" onkeyup="setHargaTotal('+jumlahItemInputSO+')" step="0.01" placeholder="Berat" autocomplete="off" class="form-control px-1" required>' ;
        row +=  '                   </div>' ;
        row +=  '               </div>' ;
        row +=  '           </div>' ;

        row +=  '           <div class="row">' ;
        row +=  '              <div class="col-4 pr-1">' ;
        row +=  '                  Harga Satuan<span class="text-danger">*</span>';
        row +=  '                  <div class="input-group">';
        row +=  '                    <div class="input-group-prepend">';
        row +=  '                       <div class="input-group-text">Rp</div>';
        row +=  '                    </div>';
        row +=  '                    <input type="text" name="harga[]" id="inputharga'+jumlahItemInputSO+'" placeholder="Harga" onkeyup="inputRupiah('+jumlahItemInputSO+'); setHargaTotal('+jumlahItemInputSO+')" autocomplete="off" class="form-control input-amount-'+jumlahItemInputSO+'" required>';
        row +=  '                  </div>';
        row +=  '               </div>' ;
        row +=  '               <div class="col-4 px-1">' ;
        row +=  '                 <div class="form-group">' ;
        row +=  '                   Harga Cetakan <span class="text-danger">*</span>' ;
        row +=  '                   <select name="harga_cetakan[]" id="hargacetakan'+jumlahItemInputSO+'" data-placeholder="Pilih Cetakan" onchange="setHargaTotal('+jumlahItemInputSO+')" data-width="100%" class="form-control" required>' ;
        row +=  '                     <option value="1">Kilogram</option>' ;
        row +=  '                     <option value="2">Ekor/Pcs/Pack</option>' ;
        row +=  '                   </select>' ;
        row +=  '                 </div>' ;
        row +=  '               </div>' ;
        row +=  '               <div class="col-4 pl-1">' ;
        row +=  '                 <div class="form-group">' ;
        row +=  '                   Total Harga' ;
        row +=  '                    <input type="text" name="harga_total[]" id="inputhargatotal'+jumlahItemInputSO+'" placeholder="Harga Total" autocomplete="off" class="form-control input-harga-total-'+jumlahItemInputSO+'" readonly required>';
        row +=  '                 </div>' ;
        row +=  '               </div>' ;
        row +=  '            </div>' ;
        row +=  '           <div class="row">' ;
        row +=  '               <div class="col plastik-'+jumlahItemInputSO+'">' ;
        row +=  '                   <div class="form-group">' ;
        row +=  '                       Plastik' ;
        row +=  '                       <select name="plastik[]" data-placeholder="Pilih Plastik" data-width="100%" class="form-control select2">' ;
        row +=  '                           <option value="">Curah</option>' ;
        row +=  '                           <option value="1">Meyer</option>' ;
        row +=  '                           <option value="2">Avida</option>' ;
        row +=  '                           <option value="3">Polos</option>' ;
        row +=  '                           <option value="4">Bukan Plastik</option>' ;
        row +=  '                           <option value="5">Mojo</option>' ;
        row +=  '                           <option value="6">Other</option>' ;
        row +=  '                       </select>' ;
        row +=  '                   </div>' ;
        row +=  '               </div>' ;
        row +=  '           </div>' ;

        row += '       <div class="row">' ;
        row +=  '           <div class="col-6 pr-1 parting-'+jumlahItemInputSO+'" hidden>' ;
        row +=  '               <div class="form-group">' ;
        row +=  '                   Parting <span class="text-danger">*</span>' ;
        row +=  '                   <input type="number" name="parting[]" id="inputParting-'+jumlahItemInputSO+'" placeholder="Parting" autocomplete="off" class="form-control px-1">' ;
        row +=  '               </div>' ;
        row +=  '           </div>' ;
        row +=  '           <div class="col-6 pl-1 bumbu-'+jumlahItemInputSO+'" hidden>' ;
        row +=  '               <div class="form-group">' ;
        row +=  '                   Bumbu' ;
        row +=  '                   <input type="text" name="bumbu[]" id="inputBumbu-'+jumlahItemInputSO+'" placeholder="Bumbu" autocomplete="off" class="form-control">' ;
        row +=  '               </div>' ;
        row +=  '           </div>' ;
        row +=  '      </div>' ;

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
        row +=  '           <input type="text" name="description_item[]" id="descriptionItem-'+jumlahItemInputSO+'" placeholder="Tuliskan Deskripsi" autocomplete="off" class="form-control">' ;
        row +=  '       </div>' ;
        row +=  '   </div>' ;
        row +=  '</div>' ;
        row +=  '</div>' ;

        $('.data-loop').append(row);

        $('.select2').select2({
            theme: 'bootstrap4'
        });

        jumlahItemInputSO++;
        poin++;
    }





    // INI UNTUK ITEM SERING DIORDER
    function getItem(inputKe, jenisItem){
        var konsumen    =   $("#customer").val() ;
        $.ajax({
            url     : "{{ route('buatso.index', ['key' => 'getMostOrdered']) }}",
            method  : "GET",
            data    : {
                customerId           : konsumen,
                type                 : jenisItem
            }, 
            success: function(data){
                onSuccessItem(data, inputKe, jenisItem)
            }
        })
    }


    function onSuccessItem(data, inputKe, jenisItem) {

        let regex = /[^0-9\,]+/g;

        let item = ``;

        item += `<select name="item[]" data-ke="${inputKe}" id="selectitem${inputKe}" onchange="pilihItem(${inputKe})" data-placeholder="Pilih Item" data-width="100%" class="form-control select2 item ${jenisItem}" required>
                        <option value=""></option>
                        <optgroup label="Item Sering di Order">`

        $.each(data.dataMostOrdered, function(k, v){
            let getUkuran = v.item_nama.split(regex);
            item += `<option value="${v.item_id}" data-item="${v.item_nama}" data-beratkali="${getUkuran[2]}">${v.sku} - ${v.item_nama}</option>`
        })
        
        
        item += `</optgroup>`
        item += `<optgroup label="Item Lainnya">`

        $.each(data.dataElseMostOrdered, function(k, v){
            let getUkuranElse = v.nama.split(regex);
            item += `<option value="${v.id}" data-item="${v.nama}" data-beratkali="${getUkuranElse[2]}">${v.sku} - ${v.nama}</option>`
        })

        item += `</optgroup>
                </select>`


        $('#untukitem-' + inputKe).append(item);
        $('.select2').select2({
            theme: 'bootstrap4'
        })
    }



    function check(input, item){
        var konsumen    =   $("#customer").val() ;

        $("#selectitem"+input).remove();
        $('#untukitem-'+input).empty();

        $('input[name="pilih'+input+'"]').on('click', function(e) {
            $('input[name="pilih'+input+'"]').prop('checked', false);
            $(this).prop('checked', true);
        });

        if ($(this).prop('checked', true)) {
            getItem(input, item)

        }
    }



    function deleteRow(rowid){
        $('.row-'+rowid).remove();
    }

    $('#tanggal_so').on('change', function(){
        for(let i = 0; i < jumlahItemInputSO; i++){
            pilihItem(i);
        }
    })

    $('#tanggal_kirim').on('change', function(){
        for(let i = 0; i < jumlahItemInputSO; i++){
            pilihItem(i);
        }
    })


    function pilihItem(rowid) {
        var konsumen= $("#customer").val() ;
        var item    = $('.row-'+rowid).find('.item option:selected').val();
        let tanggalawal = $("#tanggal_so").val();
        let tanggalakhir = $("#tanggal_kirim").val();
        var harga_kontrak = 0;
        $("#hargakontrak-" + rowid).load("{{ route('buatso.index', ['key' => 'harga_kontrak']) }}&customer=" + konsumen + "&item=" + item + "&tanggalawal=" +  tanggalawal + "&tanggalakhir=" + tanggalakhir, function(){
            harga_kontrak = $('#harga-'+item).val();
            // console.log(harga_kontrak);
            if(harga_kontrak !== undefined){
                $('#inputharga'+rowid).attr('readonly','readonly');
            }else{
                $('#inputharga'+rowid).removeAttr('readonly','');
            }
            $('#inputharga'+rowid).val(harga_kontrak);
        });

        // RESET BERAT DAN EKOR KETIKA GANTI ITEM

        $('#inputberat'+rowid).val('');
        $('#inputqty'+rowid).val('');
        $('#inputhargatotal'+rowid).val('');


        // END RESET BERAT DAN EKOR


        let nama_item = $('.row-'+rowid).find('.item option:selected').text()
        // console.log(nama_item)
        // if(nama_item.includes('PARTING MARINASI')){
        if(nama_item.includes('PARTING (M)')){
            $('.bumbu-'+rowid).attr('hidden',false)
            $('.parting-'+rowid).attr('hidden',false)
            $('.parting-'+rowid).removeClass('col')
            $('.bumbu-'+rowid).removeClass('col-6 pr-1')
            $('.bumbu-'+rowid).addClass('col-6 pl-1')

        // } else if(nama_item.includes('MARINASI')){
        } else if(nama_item.includes('(M)')){
            $('.bumbu-'+rowid).attr('hidden',false)
            $('.parting-'+rowid).attr('hidden',true)
            $('#inputParting-'+rowid).val('')
            $('.bumbu-'+rowid).removeClass('col-6 pl-1')
            $('.bumbu-'+rowid).addClass('col-6 pr-1')


        } else if(nama_item.includes('PART') || nama_item.includes('PARTING')){
            // console.log(rowid)
            $('.bumbu-'+rowid).attr('hidden',true)
            $('#inputBumbu-'+rowid).val('')
            $('.parting-'+rowid).attr('hidden',false)
            $('.parting-'+rowid).removeClass('col')

        } else {
            $('.bumbu-'+rowid).attr('hidden',true)
            $('#inputBumbu-'+rowid).val('')
            $('.parting-'+rowid).attr('hidden',true)
            $('#inputParting-'+rowid).val('')

        }


        $("#descriptionItem-"+rowid).val($('option:selected', $('#selectitem'+rowid)).attr('data-item'))

        var selected_berat_kali = 0;
        var total_berat = 0;
        // console.log($('option:selected', $('#selectitem'+rowid)).attr('data-beratkali'));

        selected_berat_kali = $('option:selected', $('#selectitem'+rowid)).attr('data-beratkali');

        // console.log($('#inputqty'+rowid).val());

        if(selected_berat_kali!=0){
            $('#inputqty'+rowid).on('keyup', function(){

                // console.log($('#inputqty'+rowid).val());

                if($('#inputqty'+rowid).val()!=undefined && $('#inputqty'+rowid).val()>0){
                    total_berat = (selected_berat_kali/10)*$('#inputqty'+rowid).val();
                    $('#inputberat'+rowid).val(total_berat.toFixed(1));
                }
            })
        }
    }

    function pilih_konsumen() {
        $("#input_items").attr('style', 'display:block');
        $("select[name='item[]']").val('').trigger('change') ;
        $(".hargakontrak").empty() ;
    }

    $('#selesaikan').on('click', function(e) {
        for(let i = 0; i < jumlahItemInputSO; i++){
            if ($('input[name="pilih'+i+'"]').length){
                if($('input[name="pilih'+i+'"]').filter(':checked').length < 1){
                    showAlert('Terdapat item yang belum dipilih!')
                    return false;
                }
            }


            if ($('#inputberat'+i).val() == '' || $('#inputberat'+i).val() == 0) {
                showAlert('Terdapat berat 0!')
                return false;
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


        let itemBeda = true;
        for(let i = 0; i < jumlahItemInputSO; i++){
            if ($("#descriptionItem-"+i).val() != $('option:selected', $('#selectitem'+i)).attr('data-item')) {
                itemBeda = false;
            }
        }

        if (itemBeda == false) {

            var result = confirm("Terdapat Deskripsi Item yang berbeda dengan Nama Item, Yakin ingin submit?");
            
        } else {

            var result = confirm("Yakin submit sales order?");

        }

        if (result) {
            $('#selesaikan').submit();
        } else {
            if (itemBeda == false) {
                for(let i = 0; i < jumlahItemInputSO; i++){
                    $("#descriptionItem-"+i).addClass('is-invalid');
                }
            } else {
                for(let i = 0; i < jumlahItemInputSO; i++){
                    $("#descriptionItem-"+i).removeClass('is-invalid');
                }
            }
            return false;
        }
    })

    $(document).ready(function() {
        $("#memo_autocomplete").autocomplete({
            source: function(req, res){
                $.ajax({
                    url         : "{{ route('buatso.index') }}",
                    dataType    : "JSON",
                    data        : {
                        q       : req.term,
                        key     : "memo_autocomplete"
                        
                    },
                    success: function(data){
                        res(data);
                    }
                });
            },
            minLength: 1
        });
    });

    function setHargaTotal(rowid) {
        if ($("#hargacetakan" + rowid).val() == 1) {
            let berat       = $("#inputberat" + rowid).val();
            console.log(berat)
            let harga       = $("#inputharga" + rowid).val().replace(/\./g, "");
            let hargaTotal  = parseFloat(berat) * parseInt(harga);


            $("#inputhargatotal" + rowid).val(hargaTotal)
            inputTotalRupiah(rowid)
        } else {
            let qty         = $("#inputqty" + rowid).val();
            let harga       = $("#inputharga" + rowid).val().replace(/\./g, "");
            let hargaTotal  = parseFloat(qty) * parseInt(harga);

            
            $("#inputhargatotal" + rowid).val(hargaTotal)
            inputTotalRupiah(rowid)
        }
    }
</script>