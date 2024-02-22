<section class="panel">
    <div class="card-body">
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <div class="bg-info p-2 text-center text-light font-weight-bold text-uppercase">Total QTY</div>
                    <div class="border p-2 text-center">
                        <h5 class="mb-0">{{ number_format($total_qty) }}</h5>
                    </div>
                </div>
            </div>
        
            <div class="col">
                <div class="form-group">
                    <div class="bg-info p-2 text-center text-light font-weight-bold text-uppercase">Total Berat</div>
                    <div class="border p-2 text-center">
                        <h5 class="mb-0">{{ number_format($total_berat,2) }}</h5>
                    </div>
                </div>
            </div><div class="col">
                <div class="form-group">
                    <div class="bg-warning p-2 text-center text-light font-weight-bold text-uppercase">Sisa QTY</div>
                    <div class="border p-2 text-center">
                        <h5 class="mb-0">{{number_format($sisa_qty_abf)}}</h5>
                    </div>
                </div>
            </div>
        
            <div class="col">
                <div class="form-group">
                    <div class="bg-warning p-2 text-center text-light font-weight-bold text-uppercase">Sisa Berat</div>
                    <div class="border p-2 text-center">
                        <h5 class="mb-0">{{number_format($sisa_berat_abf,2)}}</h5>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <div class="bg-danger p-2 text-center text-light font-weight-bold text-uppercase">Masuk ABF QTY</div>
                    <div class="border p-2 text-center">
                        <h5 class="mb-0">{{number_format($total_qty-$sisa_qty_abf)}}</h5>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <div class="bg-danger p-2 text-center text-light font-weight-bold text-uppercase">Masuk ABF Berat</div>
                    <div class="border p-2 text-center">
                        <h5 class="mb-0">{{number_format($total_berat-$sisa_berat_abf,2)}}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<input type="hidden" class="form-control" value="{{ $total_qty ?? '0'}}" id="totaldataoutboundabf" readonly="readonly">

<table class="table default-table" width="100%" id="LBabfTable">
    <thead>
        <tr>
            
            <th width="10px"><input type="checkbox" id="abf-checkall"></th>
            <th>Nama</th>
            <th>Item</th>
            <th>Jenis</th>
            {{-- <th>Packaging</th> --}}
            <th>Asal</th>
            <th>Tanggal</th>
            <th>Qty</th>
            <th>Berat</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($abf_diterima as $i => $row)
            <tr>
                <td>
                    {{-- <form method="POST" action="{{route('abf.abf_gabung_item')}}" enctype="multipart/form-data"> --}}
                        {{-- @csrf --}}
                    <input type="checkbox" name="selected_id[]" value="{{$row->id}}" class="abf-checklist">
                    {{-- </form> --}}
                </td>
                <td>{{$loop->iteration+($abf_diterima->currentpage() - 1) * $abf_diterima->perPage()}}</td>
                <td>
                    <div class="float-right text-secondary small">#ABF{{ $row->id }}</div>
                    {{ $row->item_name }} @if(count($row->hasil_timbang_selesai) > 0) <a href="{{ route('warehouse.index', ['id' => $row->id, 'search' => 'yes']) }}#custom-tabs-three-masuk" target="_blank"><span class="fa fa-share"></span></a> @endif @if($row->grade_item) <span class="text-primary pl-2 font-weight-bold uppercase"> // Grade B </span> @endif
                        @if($row->asal_tujuan == 'retur') 
                        @php
                            $retur      = App\Models\Retur::where('id', $row->table_id)->first();
                            $customer   = App\Models\Customer::where('id', $retur->customer_id)->first()->nama;
                        @endphp
                            <div class="row mt-1">
                                <div class="col-auto"><span class="status status-info">NO RA : {{ $retur->no_ra ?? '' }}</span></div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-auto"><span class="text-info">Customer : {{ $customer ?? '' }}</span></div>
                            </div>
                        @endif
                    @if ($row->selonjor)
                    <br><span class="text-danger font-weight-bold">SELONJOR</span>
                    @endif
                    @if ($row->table_name == 'chiller')
                        @if($row->abf_chiller->regu == 'byproduct')
                                @if ($row->customer_id)<br><span class="text-info">Customer : {{ $row->konsumen->nama ?? '' }}</span> @endif

                                <div class="status status-success">
                                    <div class="row">
                                        <div class="col pr-1">
                                            {{ $row->packaging }}
                                        </div>
                                        <div class="col-auto pl-1">
                                            <span class="float-right">// {{ $row->abf_chiller->chillertofreestocktemp->plastik_qty }} Pcs</span>
                                        </div>
                                    </div>
                                </div>
                        @else
                            @if($row->abf_chiller->label ?? false)
                                @php
                                    $exp = json_decode($row->abf_chiller->label);
                                @endphp

                                @if ($row->customer_id)<br><span class="text-info">Customer : {{ $row->konsumen->nama ?? '' }}</span> @endif

                                <div class="status status-success">
                                    <div class="row">
                                        <div class="col pr-1">
                                            {{ $row->abf_chiller->plastik_nama }}
                                        </div>
                                        <div class="col-auto pl-1">
                                            <span class="float-right">// {{ $row->abf_chiller->plastik_qty }} Pcs</span>
                                        </div>
                                    </div>
                                </div>
                                
                                @if ($exp)<br>
                                    @if ($exp)
                                        @if (isset($exp->additional))
                                        {{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }} {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }} {{ $exp->additional->maras ? 'Tanpa Maras' : '' }}
                                        @endif
                                    @endif
                                    <div class="row mt-1 text-info">
                                        <div class="col pr-1">@if ($exp->sub_item ?? '') Keterangan : {{ $exp->sub_item }} @endif</div>
                                        <div class="col-auto pl-1">@if ($exp->parting->qty ?? '') Parting : {{ $exp->parting->qty }} @endif</div>
                                    </div>
                                @endif
                            @endif
                        @endif
                    @endif

                    @if ($row->table_name == 'free_stocktemp')
                        @php
                            $exp = json_decode($row->abf_freetemp->label ?? false);
                        @endphp

                        @if ($row->customer_id)<br><span class="text-info">Customer : {{ $row->konsumen->nama ?? '' }}</span> @endif
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
                        @if ($exp)<br>
                            @if ($exp->additional) {{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }} {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }} {{ $exp->additional->maras ? 'Tanpa Maras' : '' }} @endif
                            <div class="row mt-1 text-info">
                                <div class="col pr-1">@if ($exp->sub_item ?? '') Keterangan : {{ $exp->sub_item }} @endif</div>
                                <div class="col-auto pl-1">@if ($exp->parting->qty ?? '') Parting : {{ $exp->parting->qty }} @endif</div>
                            </div>
                        @endif
                    @endif
                </td>
                <td>
                    @if (strpos($row->item_name, 'FROZEN') !== false)
                        <span class="status status-danger">FROZEN</span>
                    @else
                        <span class="status status-info">FRESH</span>
                    @endif
                </td>
                {{-- <td>{{ $row->packaging }}</td> --}}
                <td>
                    @if($row->asal_tujuan=="kepala_produksi")
                        <span class="status status-warning">Produksi</span>
                    @elseif($row->asal_tujuan=="free_stock")
                        <span class="status status-danger">ReguFrozen</span>
                    @else
                        <span class="status status-info">{{$row->asal_tujuan}}</span>
                    @endif

                    <div style="height: 3px"></div>

                    @if($row->type=="gabungan")
                        <span class="status status-danger">GABUNGAN</span>
                    @endif

                    @if($row->parent_abf!="")
                        <span class="status status-danger">GABUNGKE#ABF{{$row->parent_abf}}</span>
                    @endif
                </td>
                <td>{{ date('d/m/Y', strtotime($row->tanggal_masuk)) }}</td>
                <td>{{ number_format($row->qty_item > 0 ? $row->qty_item : '0') }}</td>
                <td class="text-right">{{ number_format(($row->berat_item > 0 ? $row->berat_item : '0'), 2) }}</td>
                <td>

                    

                    @if(count($row->hasil_timbang)>0)
                        <div style="height: 3px"></div>
                        <span class="status status-danger mb-1">{{count($row->hasil_timbang)}}#Pending</span>
                    @endif
                    @if(count($row->hasil_timbang_selesai)>0)
                        <div style="height: 3px"></div>
                        <span class="status status-info mb-1">{{count($row->hasil_timbang_selesai)}}x Timbang</span>
                    @endif
                    @if($row->berat_awal!=$row->berat_item && $row->berat_item > 0)
                        <div style="height: 3px"></div>
                        <span class="status status-warning">Ditimbang Sebagian</span>
                    @endif

                    @if($row->berat_item <= 0)
                        <div style="height: 3px"></div>
                        <span class="status status-success">Selesai</span>
                    @endif

                    @if ($row->status == 3)
                        <div style="height: 3px"></div>
                        <span class="status status-other">Approval</span>
                    @endif

                </td>
                <td>
                    @if ($row->status_cutoff == 1)
                        <span class="status status-danger">Transaksi sudah ditutup</span>
                    @else
                        @if ($row->status == 1)
                        @if(count($row->hasil_timbang)>0)
                        
                        <div class="form-group">
                            @if (strpos($row->item_name, 'FROZEN') !== false)
                            <form action="{{ route('abf.selesai', $row->id) }}" method="post" id="formSubmitTI">
                                @csrf
                                <div class="form-group">
                                    <span class="status status-info">Tanggal Selesaikan</span>

                                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                        min="2023-01-01" @endif name="tanggal" class="form-control mt-1" id="tanggal"
                                        value="{{ $row->tanggal_masuk }}">

                                </div>
                                <button type="submit" class="btn btn-success btn-block btnSubmitTI"><i
                                        class="fa fa-spinner fa-spin spinerloading"
                                        style="display:none; margin-right:2px;"></i>Selesaikan TI</button>
                            </form>
                            @else
            
                            <form action="{{ route('abf.selesai', $row->id) }}" method="post" id="formSubmitWO">
                                @csrf
                                <div class="form-group">
                                    <span class="status status-info">Tanggal Selesaikan</span>
                                    {{-- <label for="">Tanggal *bisa diganti jika merupakan transaksi backdate</label> --}}
                                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                        min="2023-01-01" @endif name="tanggal" class="form-control mt-1" id="tanggal"
                                        value="{{ $row->tanggal_masuk }}">
                                </div>
                                <button type="submit" class="btn btn-success btn-block btnSubmitWO"><i
                                        class="fa fa-spinner fa-spin spinerloading"
                                        style="display:none; margin-right:2px;"></i>Selesaikan WO</button>
                            </form>
                            @endif
                        </div>
                        
                        <a class="btn btn-primary btn-block btn-sm mb-1" href="{{ route('abf.timbang', $row->id) }}">Timbang</a>
                        
                        <button class="btn btn-secondary btn-block mb-1" data-toggle="collapse" data-target="#timbangSelesai{{ $row->id }}" aria-expanded="true" aria-controls="collapse{{ $row->id }}">Expand Detail</button>

                        @else
                            @if($row->berat_item > 0)
                            {{-- <a class="btn btn-primary btn-sm mb-1" href="{{ route('abf.selesaikan', $row->id) }}">Selesaikan</a> --}}

                            <a class="btn btn-primary btn-block btn-sm mb-1" href="{{ route('abf.timbang', $row->id) }}">Timbang</a>
                            @endif
                        @endif
                        @if($row->berat_awal==$row->berat_item && count($row->hasil_timbang)==0)
                        {{-- <br><a class="red btn-block" href="{{ route('abf.batalkan', $row->id) }}">Batalkan</a> --}}
                        <br> <a href="{{ route('abf.batalkan', $row->id) }}" class="btn btn-danger btn-sm p-0 px-1 btn-block">
                            Batalkan
                        </a>
                        @endif

                        @elseif ($row->status == 2)
                            @if(count($row->hasil_timbang)>0)
                                @if (strpos($row->item_name, 'FROZEN') !== false)
                                <form action="{{ route('abf.selesai', $row->id) }}" method="post" id="formSubmitTI">
                                    @csrf
                                    <div class="form-group">
                                        <span class="status status-info">Tanggal Selesaikan</span>

                                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                            min="2023-01-01" @endif name="tanggal" class="form-control mt-1" id="tanggal"
                                            value="{{ $row->tanggal_masuk }}">

                                    </div>
                                    <button type="submit" class="btn btn-success btn-block btnSubmitTI mb-1"><i
                                            class="fa fa-spinner fa-spin spinerloading"
                                            style="display:none; margin-right:2px;"></i>Selesaikan TI</button>
                                </form>
                                @else
                
                                <form action="{{ route('abf.selesai', $row->id) }}" method="post" id="formSubmitWO">
                                    @csrf
                                    <div class="form-group">
                                        <span class="status status-info">Tanggal Selesaikan</span>
                                        {{-- <label for="">Tanggal *bisa diganti jika merupakan transaksi backdate</label> --}}
                                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                            min="2023-01-01" @endif name="tanggal" class="form-control mt-1" id="tanggal"
                                            value="{{ $row->tanggal_masuk }}">
                                    </div>
                                    <button type="submit" class="btn btn-success btn-block btnSubmitWO mb-1"><i
                                            class="fa fa-spinner fa-spin spinerloading"
                                            style="display:none; margin-right:2px;"></i>Selesaikan WO</button>
                                </form>
                                @endif
                            @endif
                            <div class="row">
                                <div class="col">
                                    <a href="#" class="btn btn-warning btn-sm p-0 px-1 mb-1 editAbf" data-toggle="modal" data-target="#editAbf" title="Edit ABF (Bongkar CS)" data-key= "editlpah" data-id="{{ $row->id }}" data-jenis="lpah">
                                        Edit
                                    </a>
                                </div>
                                <div class="col">
                                    <a class="btn btn-info btn-sm mb-1" href="{{ route('abf.timbang', $row->id) }}">Detail / Timbang</a><br>
                                </div>
                            </div>
                            <button class="btn btn-secondary btn-block mb-1" data-toggle="collapse" data-target="#timbangSelesai{{ $row->id }}" aria-expanded="true" aria-controls="collapse{{ $row->id }}">Expand Detail</button>
                        @elseif ($row->status == 3)
                            <a class="btn btn-danger btn-sm mb-1 btn-block" href="javascript:void(0)" onclick="return approveToABF('{{$row->id}}')">Approve</a>
                            <a href="{{ route('abf.batalkan', $row->id) }}" class="btn btn-danger btn-sm p-0 px-1 btn-block">
                                Batalkan
                            </a>
                        @endif
                    @endif
                    
                </td>

            </tr>
            <td colspan="18">
                <div id="timbangSelesai{{ $row->id }}" class="collapse" aria-labelledby="headingOne"
                    data-parent="#accordionTimbangSelesai">
                    <div class="card card-body px-2 mb-1">
                        <b>Item yang sudah ditimbang:</b>
                        <div class="table-responsive">
                            <table class="table default-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Item</th>
                                        <th>Qty</th>
                                        <th>Berat</th>
                                        <th>Packaging</th>
                                        <th>Sub Item</th>
                                        <th>Parting</th>
                                        <th>Plastik</th>
                                        <th>Karung</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($row->detailTimbangCS as $detail)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $detail->nama }}</td>
                                        <td class="text-right">{{ $detail->qty_awal }}</td>
                                        <td class="text-right">{{ $detail->berat_awal }}</td>
                                        <td>{{ $detail->packaging }}</td>
                                        <td>{{ $detail->sub_item }}</td>
                                        <td>{{ $detail->parting }}</td>
                                        <td>{{ $detail->plastik_group }}</td>
                                        <td>
                                            @php 
                                                $karung = $detail->karung;
                                                if($karung){
                                                    $cekData    = App\Models\Item::where('sku', $detail->karung)->first();
                                                    $namaKarung = $cekData ? $cekData->nama : '';
                                                }
                                            @endphp
                                            {{ $namaKarung ?? ''}}
                                        </td>
                                        <td><span class="status status-{{ $detail->status == 2 ? 'success' : 'warning'}}">{{ $detail->status == 2 ? 'Selesai' : 'Pending'}}</span></td>                                   
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </td>

        @endforeach
    </tbody>
</table>

{{-- modal EDIT ABF --}}
<div class="modal fade" id="editAbf" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="editAbfLabel" aria-hidden="false">
    <div class="modal-dialog">
        <div id="content_modal_abf"></div>
    </div>
</div>



<div id="paginate_abf_diterima">
    {{ $abf_diterima->appends($_GET)->onEachSide(1)->links() }}
</div>
<br>

<form method="POST" action="{{route('abf.abf_gabung_item')}}" enctype="multipart/form-data" id="formGabungkan"> 
    @csrf
    <input type="hidden" name="selected_id" id="dataSelected">
    <button type="button" class="btn btn-blue btnGabungkan">Gabungkan</button>
</form>


<script>

$('.btnGabungkan').on('click', function() {
    var val = [];
    $(':checkbox:checked').each(function(i){
        val[i] = $(this).val();
    });

    $('#dataSelected').val(val);

    $("#formGabungkan").submit();

});


    
$('#paginate_abf_diterima .pagination a').on('click', function(e) {
    e.preventDefault();
    showNotif('Menunggu');

    url = $(this).attr('href');
    $.ajax({
        url: url,
        method: "GET",
        success: function(response) {
            $('#abf_diterima_view').html(response);
        }

    });
});

$(".editAbf").click(function (e) {
    e.preventDefault();
    var id      = $(this).data('id');
    var key     = $(this).data('key');
    var jenis   = $(this).data('jenis');
    var href    = $(this).attr('href');

    // alert(id);
    $.ajax({
        url : "{{route('abf.abf_diterima')}}",
        type: "GET",
        data: {
            id      : id,
            key     : 'editAbf',
        },
        success: function(data){
            $('#content_modal_abf').html(data);
        }
    });
});

    
</script>