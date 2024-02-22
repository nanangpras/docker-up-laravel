<div class="card mb-3">
    <div class="card-body">
        <div class="row">
            <div class="col-lg-2 col-md-3 col-6">
                <div class="form-group">
                    <b>Nomor PO</b>
                    <div>{{ $data->prodpur->no_po ?? '' }}</div>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <div class="form-group">
                    <b>Supplier</b>
                    <div>{{ $data->prodpur->purcsupp->nama ?? '' }}</div>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <div class="form-group">
                    <b>Daerah</b>
                    <div>{{ $data->prodpur->wilayah_daerah ?? '' }}</div>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <div class="form-group">
                    <b>Tipe PO</b>
                    <div>{{ $data->prodpur->type_po ?? '' }}</div>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <div class="form-group">
                    <b>Ekspedisi</b>
                    <div>{{ $data->prodpur->type_ekspedisi ?? '' }}</div>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <div class="form-group">
                    <b>Tanggal Potong</b>
                    <div>{{ date('d/m/y', strtotime($data->prodpur->tanggal_potong)) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card mb-3">
    <div class="card-body">
        <div class="form-group">

            @php
            $infoReceipt = App\Models\PurchaseItem::where('tujuan', '!=' , NULL)->where('purchasing_id',
            $data->prodpur->id)->orderBy('status', 'desc')->first();
            @endphp
            <b>Total Receipt: @if($infoReceipt) <span class="status status-info">{{ $infoReceipt->status == NULL ? 1 : $infoReceipt->status }}x Receipt</span> @else <span class="status status-info">Belum Receipt
                    @endif</b>
            <br>

            @if ($data->prodpur->type_po == 'PO LB')
            <ol style="margin:0; padding-left: 15px">
                <li>AYAM UKURAN @if ($data->prodpur->ukuran_ayam == '&lt; 1.1') {{ '<1.1' }} @else {{ $data->
                        prodpur->ukuran_ayam }} @endif
                        @foreach ($data->prodpur->purchasing_item->where('tujuan', NULL) as $item)
                        <br><span class="status status-success">{{ number_format($item->jumlah_ayam) }}
                            Ekor</span> || <span class="status status-info">{{ number_format($item->berat_ayam, 2) }}
                            Kg</span>
                        @endforeach
                </li>
            </ol>
            @else
            <ol style="margin:0; padding-left: 15px">
                <div class="row">
                    @php
                    $getChunk;
                    // if (count($data->prodpur->purchasing_item->where('tujuan', NULL)) != 0) {
                    // $getChunk = count($data->prodpur->purchasing_item->where('tujuan', NULL));
                    // } else if ((count($data->prodpur->purchasing_item->where('tujuan', NULL)) == 0)) {
                    // $countChunk = count($data->prodpur->purchasing_item->where('status', 1));
                    // if ($countChunk == 0 ) {
                    // $getChunk = count($data->prodpur->purchasing_item->where('tujuan', '!=', NULL));
                    // } else {
                    // $getChunk = count($data->prodpur->purchasing_item->where('status', 1));
                    // }
                    // }
                    $getChunk = $data->prodpur->purchasing_item->where('status', '!=',
                    NULL)->sortBy('status')->groupBy('status', 'description');
                    @endphp
                    {{-- {{ $getChunk }} --}}
                    @foreach ($getChunk as $key => $chunk)
                    <div class="col">
                        <br>
                        <b>Receipt Ke {{ $key }}</b>
                        @foreach($data->prodpur->purchasing_item->where('status', $key) as $item)
                        @php
                        $chiller = App\Models\Chiller::where('production_id', $data->id)->first();
                        $abf = App\Models\Abf::where('table_name','purchase_item')->where('table_id',$item->id)->first();
                        @endphp
                        <li>
                            {{ \App\Models\Item::item_sku($item->item_po)->nama }}<br>
                            <span class="status status-success">{{ number_format($item->terima_jumlah_item) }}
                                {{ ($item->type_po == "PO LB" || $item->type_po == "PO Maklon") ? "Ekor" : "Pcs" }}
                            </span> ||
                            <span class="status status-info">{{ number_format($item->terima_berat_item, 2) }}
                                Kg
                            </span> 
                            {{-- ||
                            @if($chiller)
                            <I class="fa fa-edit text-primary px-1 edit-no-lb" data-toggle="modal"
                                data-target="#edit-non-karkas" data-idchiler="{{$chiller->id}}"
                                data-idpurchaseitem="{{$item->id}}" data-idpurchase="{{$item->purchasing_id}}"
                                data-jumlah="{{$item->terima_jumlah_item}}"
                                data-berat="{{$item->terima_berat_item}}"></I>
                            @elseif($abf)
                            <I class="fa fa-edit text-primary px-1 edit-no-lb" data-toggle="modal"
                                data-target="#edit-non-karkas" data-idabf="{{$abf->id}}"
                                data-idpurchaseitem="{{$item->id}}" data-idpurchase="{{$item->purchasing_id}}"
                                data-jumlah="{{$item->terima_jumlah_item}}"
                                data-berat="{{$item->terima_berat_item}}"></I>
                            @endif --}}
                        </li>
                        @endforeach
                    </div>
                    @endforeach
                </div>
            </ol>
            @endif
        </div>
    </div>
</div>

<form id="form-submit-nonkarkas" enctype="multipart/form-data" method="POST" action="">
    <table class="table default-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th width="100px">Qty PO</th>
                <th width="100px">Berat PO</th>
                @if($data->ppic_tujuan != 'abf')
                <th>Tujuan</th>
                @endif
                <th width="100px">Total Qty Terima</th>
                <th width="100px">Total Berat Terima</th>
                <th width="100px">Qty Terima</th>
                <th width="100px">Berat Terima</th>
            </tr>
        </thead>
        <tbody>
            @csrf
            <input type="hidden" name="production_id" class="production_id" value="{{$data->id}}">
            <input type="hidden" name="tujuan" class="tujuan" value="{{$data->ppic_tujuan}}">

            @foreach($data->prodpur->purchasing_item2 as $no => $p_item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    @php
                    
                    $chiller        = App\Models\Chiller::where('production_id', $data->id)->where('item_name',App\Models\Item::item_sku($p_item->item_po)->nama)->first();
                    
                    $abf            = App\Models\Abf::where('production_id', $data->id)->first();
                    
                    $sumTotalJumlah = App\Models\PurchaseItem::where('item_po', $p_item->item_po)->where('description', $p_item->description)->where('keterangan', $p_item->keterangan)->where('purchasing_id', $p_item->purchasing_id)->groupBy('description', 'keterangan')->sum('terima_jumlah_item');
                    
                    $sumTotalBerat  = App\Models\PurchaseItem::where('item_po', $p_item->item_po)->where('description', $p_item->description)->where('keterangan', $p_item->keterangan)->where('purchasing_id', $p_item->purchasing_id)->groupBy('description', 'keterangan')->sum('terima_berat_item');
                    
                    $sumJumlah      = $p_item->jumlah_ayam;
                    $sumBerat       = $p_item->berat_ayam;
                    
                    @endphp
                    
                    @if($chiller)
                    <a href="{{ url('admin/chiller/'. $chiller->id) }}">{{ App\Models\Item::item_sku($p_item->item_po)->nama }} - {{ $p_item->id }}</a>
                    @else
                    {{ App\Models\Item::item_sku($p_item->item_po)->nama }} - {{ $p_item->id }}
                    @endif
                    <input type="hidden" name="po_item_id[]" class="item" value="{{$p_item->id}}">
                </td>
                <td> {{ number_format($p_item->jumlah_ayam) }}</td>
                <td>{{ number_format($p_item->berat_ayam, 2) }}</td>

                @if ($data->ppic_tujuan != 'abf')
                <td>
                    <select name="arah[]" class="arah form-control py-0 px-1 rounded-0">
                        <option value="" disabled hidden selected>Pilih Tujuan</option>
                        <option value="hasil">Hasil Produksi</option>
                        <option value="evis">Evis</option>
                        <option value="abf">ABF</option>
                    </select>
                </td>
                @endif

                <td> {{ number_format($sumTotalJumlah) }}</td>
                <td> {{ number_format($sumTotalBerat, 2) }}</td>
                {{-- <td>{{ number_format($p_item->terima_jumlah_item) }}</td>
                <td>{{ number_format($p_item->terima_berat_item,2) }}</td> --}}
                <td><input type="number" name="qty[]" class="form-control rounded-0 text-class qty" autocomplete="off"
                        min="0" placeholder="Qty" max="{{$p_item->jumlah_ayam}}"></td>
                <td><input type="number" name="berat[]" class="form-control rounded-0 text-class berat"
                        autocomplete="off" min="0" step="0.01" placeholder="Berat" max="{{$p_item->berat_ayam}}"></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="col-12 col-md-6 pl-md-1">
        &nbsp;
        Tanggal Bahan Baku
        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01" @endif
            value="{{$data->prodpur->tanggal_potong}}" class="form-control" id="tanggal"><br>
        <button class="btn btn-block btn-primary" id="submit-non-karkas">Submit</button>
    </div>

</form>
@if (User::setIjin('superadmin'))
<section class="panel mt-3">
    <div class="card-body">
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
                @php
                $ns = \App\Models\Netsuite::where('tabel_id', 'like', '%'.$data->id.'%')->where('tabel',
                'productions')->get();
                @endphp
                @foreach ($ns as $i => $n)
                @include('admin.pages.log.netsuite_one', ($netsuite = $n))
                @endforeach
            </tbody>
        </table>
    </div>
</section>

@endif

<script>
    $('.edit-no-lb').on('click', function () {
        var id          = $(this).attr('data-idpurchaseitem');
        var idpurchase  = $(this).attr('data-idpurchase');
        var idchiller   = $(this).attr('data-idchiler');
        var idabf       = $(this).attr('data-idabf');
        var jumlah      = $(this).attr('data-jumlah');
        var berat       = $(this).attr('data-berat');

        $('#id-nonlb-edit').val(id);
        $('#id-purchase').val(idpurchase);
        $('#id-chiller-edit').val(idchiller);
        $('#id-abf-edit').val(idabf);
        $('#item-nonlb-edit').val(jumlah);
        $('#berat-nonlb-edit').val(berat);
    });
</script>
<div class="modal fade" id="edit-non-karkas" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="hasilLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="hasilLabel">Edit Penerimaan Non LB</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('nonkarkas.update') }}" method="post">
                @csrf @method('patch')
                <input type="hidden" name="idpurchaseitem" value="" id="id-nonlb-edit">
                <input type="hidden" name="idpurchase" value="" id="id-purchase">
                <input type="hidden" name="idchiller" value="" id="id-chiller-edit">
                <input type="hidden" name="idabfedit" value="" id="id-abf-edit">
                <div class="modal-body">

                    <div class="row">
                        <div class="col pr-1">
                            <div class="form-group">
                                Ekor/Qty
                                <input type="number" name="jumlah" value="" class="form-control" id="item-nonlb-edit">
                            </div>
                        </div>
                        <div class="col pl-1">
                            <div class="form-group">
                                Berat
                                <input type="number" name="berat" value="" step="0.01" class="form-control"
                                    id="berat-nonlb-edit">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Edit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $("#form-submit-nonkarkas").submit(function(e) {
        e.preventDefault();
        var production_id   =  $('.production_id').val();
        var tujuan          =  $('.tujuan').val();
        var qty             =  document.getElementsByClassName("qty");
        var berat           =  document.getElementsByClassName("berat");
        var item            =  document.getElementsByClassName("item");
        var tanggal         =  $("#tanggal").val();

        if (tujuan != 'abf') {
            var arah            =  document.getElementsByClassName("arah");
        }
        var berat_id        =  [];
        var item_id         =  [];
        var qty_id          =  [];
        var arah7an         =  [];
        for (var i = 0; i < qty.length; ++i) {
        //     if (berat[i].value == '' || berat[i].value == 0 || berat[i] == undefined) {
        //         showAlert('Terdapat berat yang kosong!');
        //         return false;
        //     }

            if (tujuan != 'abf') {
                arah7an.push(arah[i].value);
            }
            qty_id.push(qty[i].value);
            berat_id.push(berat[i].value);
            item_id.push(item[i].value);
        }
        

        // console.log(berat_id)

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: "{{ route('nonkarkas.store') }}",
            method: "POST",
            data: {
                qty             :   qty_id,
                berat           :   berat_id,
                item            :   item_id,
                tujuan          :   tujuan,
                arah7an         :   arah7an,
                production_id   :   production_id,
                tanggal         :   tanggal
            },
            success: function(data) {

                if (data.status == 400) {
                    showAlert(data.msg);
                } else if (data.status == 200) {
                    $("#input_data").load("{{ route('nonkarkas.show', [$data->id, 'key' => 'input_data']) }}")
                    showNotif('Silahkan input data item yang baru');
                } else {
                    $("#input_data").load("{{ route('nonkarkas.show', [$data->id, 'key' => 'input_data']) }}")
                    showNotif('Berhasil Simpan');
                }
            }
        });

    });

</script>