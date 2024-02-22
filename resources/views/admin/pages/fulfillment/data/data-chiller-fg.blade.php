<br>
<div class="table-outer">
    <div class="table-inner">
        Pengalokasian dari <span class="status status-success">Chiller FG</span>
        <input type="hidden" name="lokasi_asal" value="chillerfg">
        <table class="table default-table tableFixHead table-small">
            <thead>
                <tr>
                    <th><input type="checkbox" id="fgCheckbox" class="form-control" onchange="setChillerfg(this.id)"></th>  
                    <th>Nama</th>
                    <th>Tanggal</th>
                    <th>Qty</th>
                    <th>Berat (kg)</th>
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
                        <td><input type="checkbox" name="countCheckboxFg[]" id="{{ $loop->iteration }}-{{ $row->id }}" class="form-control" onchange="setChillerfg(this.id)"></td>
                        <td>{{ $row->item_name }}
                            @if($row->kategori=="1") 
                            <span class="status status-danger">[ABF]</span>
                            @elseif($row->kategori=="2") 
                            <span class="status status-warning">[EKSPEDISI]</span>
                            @elseif($row->kategori=="3") 
                            <span class="status status-warning">[TITIP CS]</span>
                            @else 
                            <span class="status status-info">[CHILLER]</span> 
                            @endif
                        </td>
                        <td>{{ date('d/m/y',strtotime($row->tanggal_produksi)) }}</td>
                        <td id="getQtyfg{{ $loop->iteration }}-{{ $row->id }}">{{ $sisaQty }}</td>
                        <td id="getBeratfg{{ $loop->iteration }}-{{ $row->id }}">{{ $sisaBerat }}</td>
                        <td id="getKeranjangfg{{ $loop->iteration }}-{{ $row->id }}">{{ $sisaKeranjang }}</td>
                        <td>
                            <input type="hidden" name="x_code[]" value="{{ $row->id }}">
                            <div style="max-width: 200px!important">
                                <div class="row">
                                    <div class="col pr-1"><input type="number" style="max-width: 150px" name="qty[]" id="setQtyfg{{ $loop->iteration }}-{{ $row->id }}" class="p-1 form-control qty_item form-control-sm" placeholder="Ekor" min="1" max="{{ $sisaQty }}"></div>
                                    <div class="col px-1"><input type="number" style="max-width: 150px" name="berat[]" id="setBeratfg{{ $loop->iteration }}-{{ $row->id }}" class="p-1 form-control berat_item form-control-sm" step="0.01" placeholder="Berat" min="0.01" max="{{ $sisaBerat }}"></div>
                                    <div class="col pl-1"><input type="number" style="max-width: 150px" name="keranjang[]" id="setKeranjangfg{{ $loop->iteration }}-{{ $row->id }}" class="p-1 form-control berat_ambil form-control-sm" step="0.01" placeholder="Krj/Krg/Krtn" min="0"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7" class="border-bottom small">
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
    setChillerfg = id => {
        if (id == 'fgCheckbox') {
            const checkboxes                    =  document.querySelectorAll('input[name="countCheckboxFg[]"');
            // const cekCheckbox                   = document.querySelectorAll('input[name="countCheckboxFg[]"');
            // console.log(cekCheckbox.length)
            const setQty                        = document.getElementsByName('qty[]')
            const setBerat                      = document.getElementsByName('berat[]')
            const setKeranjang                  = document.getElementsByName('keranjang[]')

            if(document.getElementById(id).checked == true){
                for (var i = 0; i < checkboxes.length; i++) {

                    if (checkboxes[i].type == 'checkbox')
                        checkboxes[i].checked = true;

                        for (let x = 0; x < setQty.length; x++ ) {
                            const getBerat      = document.getElementsByName('berat[]')[x].id.replace("setBeratfg", '')
                            const getQty        = document.getElementsByName('qty[]')[x].id.replace("setQtyfg", '')
                            const getKeranjang  = document.getElementsByName('keranjang[]')[x].id.replace("setKeranjangfg", '')

                            const beratValue        = document.getElementById('getBeratfg'+ getBerat).innerHTML.replace(/\$|,/g, '')
                            const qtyValue          = document.getElementById('getQtyfg'+ getQty).innerHTML.replace(/\$|,/g, '')
                            let keranjangValue      = ''
                            if(document.getElementById('getKeranjangfg'+ getKeranjang).innerHTML != '#') {
                                keranjangValue    = document.getElementById('getKeranjangfg'+ getKeranjang).innerHTML.replace(/\$|,/g, '')
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
                        document.getElementsByName('qty[]')[x].value        = ''
                        document.getElementsByName('berat[]')[x].value      = ''
                        document.getElementsByName('keranjang[]')[x].value  = ''
                    }
                }
            }
        } else {
            const test = document.getElementById(id).checked

            if (test == true) {
                document.getElementById('setBeratfg'+ id).value         = document.getElementById('getBeratfg'+ id).innerHTML.replace(/\$|,/g, '')
                document.getElementById('setQtyfg'+ id).value           = document.getElementById('getQtyfg'+ id).innerHTML.replace(/\$|,/g, '')
                if (document.getElementById('setKeranjangfg' + id).value != '') {
                    // console.log(document.getElementById('setKeranjangfg' +id).value)
                    document.getElementById('setKeranjangfg' + id).value     =  document.getElementById('getKeranjangfg'+ id).innerHTML.replace(/\$|,/g, '')
                }
            } else {
                document.getElementById('setBeratfg'+ id).value         = ''
                document.getElementById('setQtyfg'+ id).value           = ''
                document.getElementById('setKeranjangfg'+ id).value     = ''
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

