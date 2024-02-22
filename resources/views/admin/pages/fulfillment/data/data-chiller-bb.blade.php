<br>
<div class="table-outer">
    <div class="table-inner">
        Pengalokasian dari <span class="status status-danger">Chiller Bahan Baku</span>
        <input type="hidden" name="lokasi_asal" value="sampingan">
        <table class="table default-table tableFixHead table-small">
            <thead>
                <tr>
                    <th><input type="checkbox" id="bbCheckbox" class="form-control" onchange="setChillerbb(this.id)"></th>  
                    <th>Nama</th>
                    <th>Tanggal</th>
                    <th>Qty</th>
                    <th>Berat(kg)</th>
                    <th>Jml Krj</th>
                    <th>Pengambilan</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $berat  = 0;
                    $qty    = 0;
                @endphp
                @foreach ($produk as $row)
                    @php 
                        $sisaQty        = $row->sisaQty;
                        $sisaBerat      = number_format((float)$row->sisaBerat, 2, '.', ''); 
                        $sisaKeranjang  = $row->keranjang - $row->total_keranjang ?? '#';
                    @endphp
                    <tr>
                        <td><input type="checkbox" name="countCheckboxbb[]" id="{{ $loop->iteration }}-{{ $row->id }}" class="form-control" onchange="setChillerbb(this.id)"></td>
                        <td> {{ $row->item_name }}
                            <div class="small">
                                <div class="float-right text-secondary text-right">
                                    ID-{{ $row->id }}<br>
                                    @if ($row->kode_produksi)
                                        {{ $row->kode_produksi }}
                                    @endif
                                </div>
                                @if ($row->selonjor)
                                <div class="font-weight-bold text-danger">SELONJOR</div>
                                @endif

                                @php
                                    $exp = json_decode($row->label);
                                @endphp
                                @if($exp)
                                    @if ($exp->additional ?? false) {{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }} {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }} {{ $exp->additional->maras ? 'Tanpa Maras' : '' }} @endif
                                    @if ($exp->parting->qty ?? false) <div class="blue">PART : {{ $exp->parting->qty }} </div> @endif
                                @endif

                                <div class="status status-success">
                                    <div class="row">
                                        <div class="col pr-1">
                                            {{ $row->plastik_nama }}
                                        </div>
                                        <div class="col-auto pl-1">
                                            <span class="float-right">// {{ $row->plastik_qty }} Pcs</span>
                                        </div>
                                    </div>
                                </div>
                        

                                <div class="green text-uppercase">{{ $row->customer_name }} - {{ $exp->sub_item ?? '' }}</div>

                                @if ($row->asal_tujuan == 'retur')
                                    <span class="blue">RETUR : {{ $row->label }}</span>
                                @endif

                                @if ($row->keranjang)
                                    {{ $row->keranjang }} KERANJANG
                                @endif
                            </div>
                        </td>
                        <td>{{ date('d/m/y', strtotime($row->tanggal_produksi)) }}</td>
                        <td id="getQtybb{{ $loop->iteration }}-{{ $row->id }}">{{ $sisaQty }}</td>
                        <td id="getBeratbb{{ $loop->iteration }}-{{ $row->id }}">{{ $sisaBerat }}</td>
                        <td id="getKeranjangbb{{ $loop->iteration }}-{{ $row->id }}">{{ $sisaKeranjang }}</td>
                        <td>
                            <input type="hidden" name="x_code[]" value="{{ $row->id }}">
                            <div style="max-width: 200px!important">
                                <div class="row">
                                    <div class="col pr-1">
                                        <input type="number" name="qty[]" style="max-width: 150px" id="setQtybb{{ $loop->iteration }}-{{ $row->id }}"  class="p-1 form-control qty_ambil form-control-sm" placeholder="Ekor" min="0" max="{{ $sisaQty }}">
                                    </div>
                                    <div class="col px-1">
                                        <input type="number" name="berat[]" style="max-width: 150px" id="setBeratbb{{ $loop->iteration }}-{{ $row->id }}" class="p-1 form-control berat_ambil form-control-sm" step="0.01" placeholder="Berat" min="0.01" max="{{ $sisaBerat }}">
                                    </div>
                                    <div class="col pl-1">
                                        <input type="number" name="keranjang[]" style="max-width: 150px" id="setKeranjangbb{{ $loop->iteration }}-{{ $row->id }}" class="p-1 form-control berat_ambil form-control-sm" step="0.01" placeholder="Krg/Krj" min="0">
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>

                    @php
                        $berat  = $berat + $row->stock_berat;
                        $qty    = $qty + $row->stock_item;
                    @endphp

                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    setChillerbb = id => {
        if (id == 'bbCheckbox') {
            const checkboxes                    = document.querySelectorAll('input[name="countCheckboxbb[]"]');
            const setQty                        = document.getElementsByName('qty[]')
            const setBeratbb                    = document.getElementsByName('berat[]')
            const setKeranjangbb                = document.getElementsByName('keranjang[]')

            if(document.getElementById(id).checked == true){
                for (var i = 0; i < checkboxes.length; i++) {

                    if (checkboxes[i].type == 'checkbox')
                        checkboxes[i].checked = true;

                        for (let x = 0; x < setQty.length; x++ ) {
                            const getBerat      = document.getElementsByName('berat[]')[x].id.replace("setBeratbb", '')
                            const getQty        = document.getElementsByName('qty[]')[x].id.replace("setQtybb", '')
                            const getKeranjang  = document.getElementsByName('keranjang[]')[x].id.replace("setKeranjangbb", '')

                            const beratValue        = document.getElementById('getBeratbb'+ getBerat).innerHTML.replace(/\$|,/g, '')
                            const qtyValue          = document.getElementById('getQtybb'+ getQty).innerHTML.replace(/\$|,/g, '')
                            let keranjangValue      = ''
                            if(document.getElementById('getKeranjangbb'+ getKeranjang).innerHTML != '#') {
                                keranjangValue    = document.getElementById('getKeranjangbb'+ getKeranjang).innerHTML.replace(/\$|,/g, '')
                            }

                            document.getElementsByName('berat[]')[x].value          = beratValue
                            document.getElementsByName('qty[]')[x].value            = qtyValue
                            document.getElementsByName('keranjang[]')[x].value      = keranjangValue
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
                document.getElementById('setBeratbb'+ id).value         = document.getElementById('getBeratbb'+ id).innerHTML.replace(/\$|,/g, '')
                document.getElementById('setQtybb'+ id).value           = document.getElementById('getQtybb'+ id).innerHTML.replace(/\$|,/g, '')
                // document.getElementById('setKeranjangbb'+ id).value     = document.getElementById('getKeranjangbb'+ id).innerHTML.replace(/\$|,/g, '')
                if (document.getElementById('setKeranjangbb' + id).value != '') {
                    // console.log(document.getElementById('setKeranjangbb' +id).value)
                    document.getElementById('setKeranjangbb' + id).value     =  document.getElementById('getKeranjangbb'+ id).innerHTML.replace(/\$|,/g, '')
                }
            } else {
                document.getElementById('setBeratbb'+ id).value         = ''
                document.getElementById('setQtybb'+ id).value           = ''
                document.getElementById('setKeranjangbb'+ id).value     = ''
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

