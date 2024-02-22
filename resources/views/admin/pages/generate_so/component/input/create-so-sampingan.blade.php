<section class="panel">
    <div class="card-body">
        <span class="status status-danger">*Harus pilih customer dulu</span>
        <div class="row">
            <div class="col-md-6 mb-3">
                <br>
                <label for="customer">Customer</label>
                <div id="customer-loop">
                    <div class="row">
                        <div class="col pr-1">
                            <select name="customer" data-width="100%" data-placeholder="Data Customer"
                                class="t_customer form-control select2 mb-2" onchange="changeCustomer(this, 0)">
                                <option value=""></option>
                                @foreach ($customerSampingan as $row)
                                <option value="{{ $row->id }}">{{ $row->kode }}. {{ $row->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-auto pl-1">
                            <button onclick="addCustomerRetail(0)" class="btn btn-primary addCustomerRetail-0"
                                disabled><i class="fa fa-check"></i></button>
                            <button onclick="addCustomer()" class="btn btn-primary"><i class="fa fa-plus"></i></button>
                        </div>
                    </div>

                    <div id="listHistoryCustomerRetail-0" class="mt-2">
                    </div>

                    <div id="listItemCustomerRetail-0" class="mb-2 pb-1 mb-2 mt-2" style="border-bottom: 3px solid">
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div id="item-loop">
                    <div class="border p-2 mb-2">

                        <button type='button' class="btn btn-primary btn-sm mb-2" data-toggle="modal"
                            data-target="#daftarItemSampingan">Daftar Item Sampingan</button>
                        <a href="{{ route('hargakontrak.index', ['key' => 'customerSampingan']) }}#input" type='button'
                            class="btn btn-info btn-sm mb-2">Master Customer Sampingan</a>


                        <div class="modal fade" id="daftarItemSampingan">
                            <div class="modal-dialog">
                                <div class="modal-content">

                                    <!-- Modal Header -->
                                    <div class="modal-header">
                                        <h4 class="modal-title">Daftar Item Sampingan</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>

                                    <!-- Modal body -->
                                    <div class="table-outer p-2">
                                        <div class="table-inner">
                                            <table class="table default-table table-small">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th>Nama</th>
                                                        <th>Harga</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($itemSampingan as $row)
                                                    <tr>
                                                        <td><input type="checkbox" name="chk[]" value="{{ $row->sku }}"
                                                                class="form-control mt-1" @foreach ($checkedItem as
                                                                $chkItem) @if ($row->sku == $chkItem)
                                                            {{ 'checked' }}
                                                            @endif
                                                            @endforeach></td>
                                                        <td> {{ $row->nama }} </td>

                                                        <td>
                                                            <div style="max-width: 200px!important">
                                                                <div class="row">
                                                                    <div class="col pr-1">
                                                                        <input type="text" name="hargaSOSampingan[]"
                                                                            style="max-width: 150px"
                                                                            id="setHargaSampingan-{{ $row->sku }}"
                                                                            class="p-1 form-control form-control-sm input-amount-{{ $row->sku }}"
                                                                            onkeyup="inputRupiah({{ $row->sku }})"
                                                                            placeholder="Harga" @foreach ($checkedItem
                                                                            as $key=> $chkItem2)
                                                                        @if ($row->sku == $chkItem2)
                                                                        @if ($hargaItem[$key] ?? FALSE)
                                                                        value="{{ number_format($hargaItem[$key], 0,
                                                                        ',', '.') }}"
                                                                        @endif
                                                                        @endif
                                                                        @endforeach>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Modal footer -->
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary" id="saveItemSampingan">Save
                                            Changes</button>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{-- <div class="col">
                                <div class="form-group">
                                    <label for="mulai">Tanggal Sales Order</label>
                                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) min="2023-01-01"
                                        @endif class="form-control" id="tanggalSOSampingan" value="{{ date("Y-m-d")
                                        }}">
                                </div>
                            </div> --}}

                            <div class="col ">
                                <div class="form-group">
                                    <label for="akhir">Tanggal Kirim</label>
                                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) min="2023-01-01"
                                        @endif class="form-control" id="tanggalKirimSampingan"
                                        value="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                </div>
                            </div>
                        </div>
                        <span class="text-danger">* Tanggal digunakan untuk setiap customer</span>
                    </div>
                </div>

                <button id="submitSOSampingan" class="btn btn-block btn-primary">Submit</button>
            </div>
        </div>
    </div>
</section>

{{-- BUAT SAMPINGAN --}}

<script>
    /**********************************************************************/
    /*                                                                    */
    /*              INI BAGIAN TAB  INPUT ORDERAN SAMPINGAN               */
    /*                                                                    */
    /**********************************************************************/
    
    // Semoga yang liat bisa baca
    $('.select2').select2({
        theme: 'bootstrap4'
    })

    ac = 1 ;
    function addCustomer() {
        row     =   '' ;
        row     +=  '   <div class="row mt-2 temporary row-' + (ac) + '">';
        row     +=  '    <div class="col pr-1">';
        row     +=  '        <select data-width="100%" data-placeholder="Data Customer" class="t_customer form-control select2 addCustomerRetail-' + (ac) + '" onchange="changeCustomer(this, ' + ac + ')">';
        row     +=  '            <option value=""></option>';
        row     +=  '            @foreach ($customerSampingan as $row)';
        row     +=  '            <option value="{{ $row->id }}">{{ $row->kode }}. {{ $row->nama }}</option>';
        row     +=  '            @endforeach';
        row     +=  '        </select>';
        row     +=  '    </div>';
    
        row     +=  '    <div class="col-auto pl-1">';
        row     +=  '        <button onclick="addCustomerRetail(' + ac + ')" class="btn btn-primary addCustomerRetail-' + ac + '" disabled><i class="fa fa-check"></i></button>';
        row     +=  '        <button onclick="deleteCustomer(' + (ac) + ')" class="btn btn-danger"><i class="fa fa-trash"></i></button>';
        row     +=  '    </div>';
        row     +=  '   </div>';
        // row     +=  '   <div class="border-bottom pb-1 mt-2 mb-2 parentItemRetail-' + (ac) + '">' ;
        // row     +=  ' <div class="border-bottom pb-1 mt-2 mb-2">' ;
        row     +=  '       <div id="listHistoryCustomerRetail-' + (ac) + '"  class="mt-2">' ;
        row     +=  '       </div>';
        row     +=  '       <div id="listItemCustomerRetail-' + (ac) + '" class="mb-2 pb-1 mb-2 mt-2" style="border-bottom: 3px solid">' ;
        row     +=  '       </div>';
        // row     +=  '</div>';
    
    
        $('#customer-loop').append(row);
        $('.select2').select2({
            theme: 'bootstrap4'
        })
    
        ac++;
    }


    function changeCustomer(customer, keys) {
        // console.log(keys)

        if (customer.value == undefined || customer.value == '') {
            return;
            // $(".addCustomerRetail-"+ keys).attr('disabled', true)
        }
        $(".addCustomerRetail-"+ keys).attr('disabled', false)
        $(".addCustomerRetail-"+ keys).attr('onclick', "addCustomerRetail("+ keys+ ", "+ customer.value +", 0,0,0,0)")
        $(".addCustomerRetail-"+ keys).attr('data-id', '0')

        // CEK HISTORY RETAIL CUSTOMER TERSEBUT
        const custValue = customer.value;
        // $("#listHistoryCustomerRetail").load("{{ route('buatso.index', ['key' => 'cekHistoryItemRetailCustomer']) }}&custValue="+ custValue);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('buatso.index') }}",
            data: {
                custValue,
                key: 'cekHistoryItemRetailCustomer',
                keys
            },
            success: function(data) {
                $("#listHistoryCustomerRetail-"+keys).html(data)
            }
        });
    }

    function setCheckbox(customer, keys, id) {
        const custValue = customer;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('buatso.index') }}",
            data: {
                custValue,
                key: 'cekItemRetailCustomer',
                id
            },
            success: function(data) {
                // console.log(data)
                if (data != 0) { 
                    $('.parentItemRetail-'+ keys).remove();
                    data.forEach(function(listRetail) {
                        addCustomerRetail(keys, custValue, listRetail.item_id, listRetail.harga, listRetail.berat, listRetail.qty)
                    });
                    
                }

            }
        });
    }

    acr = 1;
    function addCustomerRetail(key, customer, idItem, hargaRetail, beratRetail, qtyRetail) {

        // console.log(hargaRetail)
        acrk =         `<div class="row mt-1 pl-3 childItemRetail-${key}-${acr} parentItemRetail-${key}">
                            <div class="col-6 pr-2">
                                <select name="itemRetail" data-width="100%" data-placeholder="Data Item" class="itemRetail form-control select2">
                                    <option value=""></option>
                                    @foreach ($itemRetail as $row)
                                    <option value="{{ $row->id }}" ${idItem == "{{ $row->id }}" ? 'selected' : ''}>{{ $row->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col px-0 pr-2">
                                <div class="form-group">
                                    <input type="number" id="inputQtyRetail-${key}-${acr}" value="${qtyRetail}" name="qtyRetail[]" placeholder="Qty" autocomplete="off" class="form-control px-1 qtyRetail">
                                </div>
                            </div>
                            <div class="col px-0 pr-2">
                                <div class="form-group">
                                    <input type="number" id="inputBeratRetail-${key}-${acr}" value="${beratRetail}" name="beratRetail[]" placeholder="Berat" autocomplete="off" class="form-control px-1 beratRetail">
                                </div>
                            </div>
                            <div class="col-2 px-0 pr-2">
                                <div class="form-group">
                                    <input type="text" id="inputHargaRetail-${key}-${acr}" value="${hargaRetail > 0 ? `${hargaRetail.toLocaleString('id-ID')}` : '' }" onkeyup="inputRupiahRetail(${key}, ${acr})" name="hargaRetail[]" placeholder="Harga" autocomplete="off" class="hargaRetail form-control px-1 input-amountRetail-${key}-${acr}">
                                </div>
                            </div>
                            <div class="col px-0 mt-1 pr-2">
                                <button onclick="deletelistItemRetail(${key}, ${acr})" class="btn btn-danger"><i class="fa fa-trash"></i></button>
                            </div>
                        </div>`

        $('#listItemCustomerRetail-'+key).append(acrk);
        $('.select2').select2({
            theme: 'bootstrap4'
        })
        const getJumlahDataRetail = $(".addCustomerRetail-" + key).attr('data-id')
        $(".addCustomerRetail-" + key).attr('data-id', parseInt(getJumlahDataRetail) + parseInt(1))
        acr++;
    }

    function inputRupiahRetail(parent, child) {
        $('.input-amountRetail-'+parent + '-' + child).val(formatAmount($('.input-amountRetail-'+parent + '-' + child).val()));
    }


    function deletelistItemRetail(parent, child) {
        $('.childItemRetail-' + parent + '-' + child).remove();
        const getJumlahDataRetail = $(".addCustomerRetail-" + parent).attr('data-id')
        $('.addCustomerRetail-' + parent).attr('data-id', parseInt(getJumlahDataRetail) - parseInt(1))
    }
    
    function deleteCustomer(rowid){
        $('.row-' + rowid).remove();
        $('#listItemCustomerRetail-'+ rowid).remove();
    }


    $("#saveItemSampingan").on('click', function (){
        var items = [];
        var harga = [];
        var newHarga = [];

        $.each($("input[name='chk[]']:checked"), function(){
            items.push($(this).val());
            if ($("#setHargaSampingan-" + $(this).val()).val() != '') {
                harga.push($("#setHargaSampingan-" + $(this).val()).val());
            } else {
                harga.push('0')
            }
        });

        // HILANGIN TITIK DI ARRAY HARGA
        harga.map(x => {
            return newHarga.push(x.replace('.',''));
        })

        // SAVE KE OPTION
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('buatso.store') }}",
            method: "POST",
            data: {
                items,
                newHarga,
                key: 'saveItemSampingan',
            },
            success: function(data) {
                if (data.status == 400) {
                    showAlert(data.msg) ;
                } else {
                    showNotif(data.msg);
                }
                $('#daftarItemSampingan').modal('hide');
            }
        });

    })



    $('#submitSOSampingan').on('click', function (){
        var result = confirm("Yakin submit Sampingan?");
        if(result){
            // const tanggalawal   =   $("#tanggalSOSampingan").val();
            const tanggalakhir  =   $("#tanggalKirimSampingan").val();
            var cust            =   document.getElementsByClassName("t_customer");
            var customer        =   [];

            var datas           =   [];

            for(var i = 0; i < cust.length; ++i) {
                customer.push(parseFloat(cust[i].value));
                datas.push($('.addCustomerRetail-' + i).attr('data-id'))
            }

            var qty            =   document.getElementsByClassName("qtyRetail");
            var qtys           =   [];

            for(var i = 0; i < qty.length; ++i) {
                qtys.push(parseFloat(qty[i].value));
            }

            var berat            =   document.getElementsByClassName("beratRetail");
            var berats           =   [];

            for(var i = 0; i < berat.length; ++i) {
                berats.push(parseFloat(berat[i].value));
            }

            var harga            =   document.getElementsByClassName("hargaRetail");
            var hargas           =   [];

            for(var i = 0; i < harga.length; ++i) {
                hargas.push(parseFloat(harga[i].value.replace(/\./g, '')));
            }


            var item            =   document.getElementsByClassName("itemRetail");
            var items           =   [];

            for(var i = 0; i < item.length; ++i) {
                items.push(parseFloat(item[i].value));
            }

            var item            =   document.getElementsByClassName("itemRetail");
            var items           =   [];

            for(var i = 0; i < item.length; ++i) {
                items.push(parseFloat(item[i].value));
            }

            $("#submitSOSampingan").hide() ;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        
            $.ajax({
                url: "{{ route('buatso.store') }}",
                method: "POST",
                data: {
                    customer    :  customer ,
                    // tanggalawal,
                    tanggalakhir,
                    key         :  'SOSampingan',
                    qtys,
                    berats,
                    items,
                    hargas,
                    datas
                },
                success: function(data) {

                    console.log(data)
                    if (data.status == 400) {
                        showAlert(data.msg) ;
                    } else {
                        showNotif(data.msg);
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000)
                        $("#submitSOSampingan").show() ;
                    }
                }
            });

        } else {
            return false;
        }
    })

</script>

{{-- END BUAT SAMPINGAN --}}