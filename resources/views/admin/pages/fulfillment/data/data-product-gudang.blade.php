<br>
<div class="table-outer">
    <div class="table-inner">
        Pengalokasian dari <span class="status status-info">CS Frozen</span>
        <input type="hidden" name="lokasi_asal" value="frozen">
        <table class="table default-table tableFixHead table-xl">

            <thead>
                <tr>
                    <th><input type="checkbox" id="fzCheckbox" class="form-control" onchange="setChillerfz(this.id)"></th>  
                    <th>No</th>
                    <th>Nama</th>
                    <th>Tanggal </th>
                    <th>Lokasi</th>
                    <th>Qty</th>
                    <th>Berat (Kg)</th>
                    <th>Jml Krg</th>
                    <th>Pengambilan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stock as $i => $val)
                    @php
                        $sisaQty        = $val->sisaQty;
                        $sisaBerat      = number_format((float)$val->sisaBerat, 2, '.', ''); 
                    @endphp
                    <tr>
                        <td><input type="checkbox" name="countCheckboxFz[]" id="{{ $loop->iteration }}-{{ $val->id }}" class="form-control" onchange="setChillerfz(this.id)"></td>
                        <td>{{ ++$i }}</td>
                        <td>{{ $val->nama ?? '#' }}
                            @if($val->grade_item)<br> <span class="text-primary font-weight-bold uppercase"> // Grade B </span> @endif
                            <br>
                        <span class="green">{{ $val->customer_name }}</span></td>
                        <td>{{ date('d/m/y', strtotime($val->production_date)) }}<br>{{$val->production_code}}</td>
                        <td>{{ $val->kode_gudang }}</td>
                        <td id="getQtyfz{{ $loop->iteration }}-{{ $val->id }}">
                            {{ $sisaQty }}
                        </td>
                        <td id="getBeratfz{{ $loop->iteration }}-{{ $val->id }}">
                            {{ $sisaBerat }}
                        </td>
                        <td id="getKarungfz{{ $loop->iteration }}-{{ $val->id }}">
                            {{ $val->karung_awal - $val->total_keranjang }}
                        </td>
                        <td>
                            <input type="hidden" name="x_code[]" value="{{ $val->id }}">
                            <div style="max-width: 200px!important">
                                <div class="val">
                                    <div class="col pl-1 mb-1">
                                        <input type="number" name="qty[]" style="max-width: 150px" id="setQtyfz{{ $loop->iteration }}-{{ $val->id }}" 
                                        class="p-1 form-control form-control-sm" placeholder="Ekor" min="1" max="{{ $sisaQty }}">
                                    </div>
                                    
                                    <div class="col px-1 mb-1">
                                        <input type="number" name="berat[]" style="max-width: 150px" id="setBeratfz{{ $loop->iteration }}-{{ $val->id }}" class="p-1 form-control form-control-sm" step="0.01" placeholder="Berat" min="0.01" max="{{ $sisaBerat }}">
                                    </div>
                                    
                                    <div class="col pl-1 mb-1"><input type="number" name="keranjang[]" style="max-width: 150px" id="setKarungfz{{ $loop->iteration }}-{{ $val->id }}" class="p-1 form-control berat_ambil form-control-sm" step="0.01" placeholder="Krj/Krg/Krtn" min="0"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="9">
                            <div class="float-right text-secondary small">ID-{{ $val->id }}</div>
                            @if ($val->packaging)
                            <span class="blue">{{ $val->packaging }}</span><br>
                            @endif
                            @if ($val->sub_item)
                            <span class="green">{{ $val->customer_id ? $val->customer_name . ' - ' : '' }} {{ $val->sub_item }}</span><br>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>
</div>

<script>
    setChillerfz = id => {
        if (id == 'fzCheckbox') {
            const checkboxes                    = document.querySelectorAll('input[name="countCheckboxFz[]"]');
            const setQty                        = document.getElementsByName('qty[]')
            const setBerat                      = document.getElementsByName('berat[]')
            const setKarung                     = document.getElementsByName('keranjang[]')

            if(document.getElementById(id).checked == true){
                for (var i = 0; i < checkboxes.length; i++) {

                    if (checkboxes[i].type == 'checkbox')
                        checkboxes[i].checked = true;

                        for (let x = 0; x < setQty.length; x++ ) {
                            const getBerat      = document.getElementsByName('berat[]')[x].id.replace("setBeratfz", '')
                            const getQty        = document.getElementsByName('qty[]')[x].id.replace("setQtyfz", '')
                            const getKarung     = document.getElementsByName('keranjang[]')[x].id.replace("setKarungfz", '')

                            const beratValue    = document.getElementById('getBeratfz'+ getBerat).innerHTML.replace(/\$|,/g, '')
                            const qtyValue      = document.getElementById('getQtyfz'+ getQty).innerHTML.replace(/\$|,/g, '')
                            // const karungValue   = document.getElementById('getKarungfz'+ getKarung).innerHTML.replace(/\$|,/g, '')
                            let karungValue      = ''
                            if(document.getElementById('getKarungfz'+ getKarung).innerHTML != '#') {
                                karungValue    = document.getElementById('getKarungfz'+ getKarung).innerHTML.replace(/\$|,/g, '')
                            }

                            document.getElementsByName('berat[]')[x].value          = beratValue
                            document.getElementsByName('qty[]')[x].value            = qtyValue
                            document.getElementsByName('keranjang[]')[x].value      = karungValue
                        }

                    }
                } else {

                for (var i = 0; i < checkboxes.length; i++) {
                    if (checkboxes[i].type == 'checkbox')
                        checkboxes[i].checked = false;
                    for(let x = 0; x < setQty.length; x++ ){
                        document.getElementsByName('qty[]')[x].value    = ''
                        document.getElementsByName('berat[]')[x].value  = ''
                        document.getElementsByName('keranjang[]')[x].value  = ''
                    }
                }
            }
        } else {
            const test = document.getElementById(id).checked

            if (test == true) {
                document.getElementById('setBeratfz'+ id).value     = document.getElementById('getBeratfz'+ id).innerHTML.replace(/\$|,/g, '')
                document.getElementById('setQtyfz'+ id).value       = document.getElementById('getQtyfz'+ id).innerHTML.replace(/\$|,/g, '')
                // document.getElementById('setKarungfz'+ id).value    = document.getElementById('getKarungfz'+ id).innerHTML.replace(/\$|,/g, '')
                if (document.getElementById('setKarungfz' + id).value != '') {
                    // console.log(document.getElementById('setKarungfz' +id).value)
                    document.getElementById('setKarungfz' + id).value     =  document.getElementById('getKarungfz'+ id).innerHTML.replace(/\$|,/g, '')
                    console.log(document.getElementById('setKarungfz' + id).value)
                }
            } else {
                document.getElementById('setBeratfz'+ id).value   = ''
                document.getElementById('setQtyfz'+ id).value     = ''
                document.getElementById('setKarungfz'+ id).value     = ''
            }

        }
    }
</script>

<style>
    /* Fix table head */
    .tableFixHead    {
        overflow: auto;
        height: 100px;
    }
    .tableFixHead th {
        position: sticky;
        top: 0;
        z-index: 2000;
    }

    /* Just common table stuff. */
    table  {
        border-collapse: collapse;
        width: 100%; }
    th, td {
        padding: 8px 16px;
    }
    th     {
        background:#eee;
    }
    .table-outer{
        max-height: 500px;
        overflow-y: scroll;
    }
</style>

