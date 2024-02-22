<section class="panel">
    <div class="card-body">
        <div class="accordion" id="accordionListPO">
            <div class="table-responsive">
                <table class="table default-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>SKU</th>
                            <th>Nama Item</th>
                            <th>Jumlah Data</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    
                    @foreach ($data as $row)
                        @php
                        $item_list =  App\Models\Pembelianlist::where('headbeli_id', NULL)
                            ->where('pembelian_list.status', 1)
                            ->where('pembelian_list.sisa', '>', 0)
                            ->whereBetween('pembelian_list.created_at', [$tanggal_mulai_view." 00:00:00", $tanggal_akhir_view." 23:59:59"])
                            ->where('item_id',$row->itemID)
                            ->get();
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration + ($data->currentpage() - 1) * $data->perPage() }}</td>
                            <td>{{ $row->item->sku }}</td>
                            <td>{{ $row->item->nama ?? '#' }}</td>
                            <td><span class="status status-info">{{ count($item_list) }} Item</span></td>
                            <td><button class="btn btn-primary" data-toggle="collapse" data-target="#collapse{{ $row->id }}" aria-expanded="true"
                                aria-controls="collapse{{ $row->id }}">Expand Detail
                            </button>
                            </td>
                        </tr>
                        <td colspan="5"><div id="collapse{{ $row->id }}" class="collapse" aria-labelledby="headingOne" data-parent="#accordionListPO">
                            <div class="p-0">
                                @foreach ($item_list as $item)
                                <input type="hidden" id="id_history_peritem{{ $item->id }}" value="{{ $item->id }}}" data-id="" class="id_history_peritem">
                                <div class="p-1 mt-1">
                                    <div class="cursor mb-1" data-toggle="" data-target="#accept{{ $item->id }}" id="validateheader{{ $item->id }}" onclick="validateheader({{ $item->id }},{{ $row->id }},{{ $item->item_id }})">
                                        <div class="border p-1">
                                            <div class="row">
                                                <div class="col pr-1">
                                                    <div class="row">
                                                        <div class="col">
                                                            Divisi: <label class="rounded status-succes text-uppercase">{{ $item->pembelian->divisi }}</label><br>
                                                        </div>
                                                        <div class="col-auto text-center">
                                                            <label class="rounded status-succes text-uppercase">NO PR: {{ $item->pembelian->no_pr }}</label>
                                                        </div>
                                                        <div class="col-auto text-right">
                                                            Qty : {{ number_format($item->sisa) }} {{ $item->unit }}<br>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-auto pl-1">
                                                    <i class="fa fa-chevron-right mt-3 text-info"></i>
                                                </div>
                                    
                                            </div>
                                        </div>
                                        @if ($item->keterangan || $item->link_url)
                                        <div class="border border-top-0 px-1">
                                            @if ($item->keterangan)
                                            <div>Keterangan : {{ $item->keterangan }}</div>
                                            @endif
                                            @if ($item->link_url)
                                            <div>URL : {{ $item->link_url }}</div>
                                            @endif
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="modal fade" id="accept{{ $item->id }}" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="accept{{ $item->id }}Label" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="accept{{ $item->id }}Label">Item Pembelian</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="border-bottom px-2 py-1">
                                                    <div class="row">
                                                        <div class="col-3 font-weight-bold">
                                                            SKU
                                                        </div>
                                                        <div class="col">
                                                            {{ $item->item->sku ?? ''  }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="border-bottom px-2 py-1">
                                                    <div class="row">
                                                        <div class="col-3 font-weight-bold">
                                                            Item
                                                        </div>
                                                        <div class="col">
                                                            {{ $item->item->nama ?? ''  }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="border-bottom px-2 py-1">
                                                    <div class="row">
                                                        <div class="col-3 font-weight-bold">
                                                            Qty / Unit
                                                        </div>
                                                        <div class="col">
                                                            {{ $item->sisa }} {{ $item->unit }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="border-bottom px-2 py-1">
                                                    <div class="row">
                                                        <div class="col-3 font-weight-bold">
                                                            Keterangan (INTERNAL)
                                                        </div>
                                                        <div class="col">
                                                            {{ $item->keterangan }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="border-bottom px-2 py-1">
                                                    <div class="row">
                                                        <div class="col-3 font-weight-bold">
                                                            URL
                                                        </div>
                                                        <div class="col">
                                                            {{ $item->link_url }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="historyperitem{{ $item->id }}">

                                                </div>
                    
                                                <div class="row my-3">
                                                    <div class="col pr-1">
                                                        <div class="form-group">
                                                            <label for="qty{{ $item->id }}">Qty</label>
                                                            <div class="input-group">
                                                                <input type="number" id="qty{{ $item->id }}" class="form-control rounded-0 p-1" autocomplete="off" min="1" max="{{ $item->sisa }}" placeholder="Tulis Qty">
                                                                <div class="input-group-prepend">
                                                                <div class="input-group-text">{{ $item->unit }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col pl-1">
                                                        <div class="form-group">
                                                            <label for="harga{{ $item->id }}">Harga Unit</label>
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                <div class="input-group-text">Rp</div>
                                                                </div>
                                                                <input type="number" id="harga{{ $item->id }}" class="form-control rounded-0 p-1" autocomplete="off" min="0" step="0.01" placeholder="Total Harga">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                    
                    
                                                @php 
                                                    $data_item = App\Models\Item::find($item->item_id);
                                                @endphp
                                                @if($data_item)
                                                @if($data_item->category_id<23)
                                                <div class="row my-3">
                                                    
                                                    <div class="col pr-1">
                                                        <div class="form-group">
                                                            <label for="berat{{ $item->id }}">Berat DO</label>
                                                            <div class="input-group">
                                                                <input type="number" id="berat{{ $item->id }}" class="form-control rounded-0 p-1" autocomplete="off" min="1" placeholder="Tulis Berat">
                                                                <div class="input-group-prepend">
                                                                <div class="input-group-text">Kg</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col pl-1">
                                                        <div class="form-group">
                                                            <label for="ukuran_ayam{{ $item->id }}">Ukuran Ayam</label>
                                                            <div class="input-group">
                                                                <select class="form-control" id="ukuran_ayam{{$item->id}}">
                                                                    <option value="1"> < 1.1 </option>
                                                                    <option value="2"> 1.1-1.3 </option>
                                                                    <option value="3"> 1.2-1.4 </option>
                                                                    <option value="4"> 1.3-1.5 </option>
                                                                    <option value="5"> 1.4-1.6 </option>
                                                                    <option value="6"> 1.7-1.9 </option>
                                                                    <option value="7"> 1.8-2.0 </option>
                                                                    <option value="8"> 1.9-2.1 </option>
                                                                    <option value="9"> 2.0 Up</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row my-3">
                                                    <div class="col pr-1">
                                                        <div class="form-group">
                                                            <label for="jumlah_do{{ $item->id }}">Jumlah DO</label>
                                                            <div class="input-group">
                                                                <input type="number" id="jumlah_do{{ $item->id }}" class="form-control rounded-0 p-1" autocomplete="off" min="1" placeholder="DO">
                                                                <div class="input-group-prepend">
                                                                <div class="input-group-text">Mbl</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col pl-1">
                                                        <div class="form-group">
                                                            <label for="unit_cetakan{{ $item->id }}">Harga Cetakan</label>
                                                            <div class="input-group">
                                                                <select class="form-control" id="unit_cetakan{{$item->id}}">
                                                                    <option value="1"> Kg </option>
                                                                    <option value="2"> Ekor/Pcs/Pack </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                                @endif
                                                <div class="col px-1">
                                                    <div class="form-group">
                                                        <label for="keterangan{{ $item->id }}">Keterangan <span class="red text-small">* DIISI DENGAN MEREK/SERI PRODUK (TAMPIL DI PO NS)</span></label>
                                                        <div class="input-group">
                                                            <input type="text" id="keterangan{{ $item->id }}" value="" class="form-control rounded-0 p-1" autocomplete="off" placeholder="Keterangan">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col px-1">
                                                    <div class="form-group">
                                                        <label for="gudang{{ $item->id }}">Gudang</label>
                                                        <div class="input-group">
                                                            <select class="form-control" id="gudang{{$item->id}}">
                                                                <option value=""> - Pilih Gudang - </option>
                                                                @php 
                                                                    $gudang = App\Models\Gudang::where('subsidiary', Session::get('subsidiary'))
                                                                                    ->where('code', 'not like', '%chiller%')
                                                                                    ->where('code', 'not like', '%storage%')
                                                                                    // ->where('kategori', NULL)
                                                                                    ->get();
                                                                @endphp
                                                                @foreach($gudang as $g)
                                                                <option value="{{$g->netsuite_internal_id}}" @if($g->code == Session::get('subsidiary').' - Sparepart') selected @endif>  {{$g->code}} </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                <button type="button" data-id="{{ $item->id }}" class="tambah_item btn btn-primary">Submit</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div></td>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>


<div id="paginate_pembelian">
    {{ $data->appends($_GET)->onEachSide(1)->links() }}
</div>

<script>
$('#paginate_pembelian .pagination a').on('click', function(e) {
    e.preventDefault();
    showNotif('Menunggu');

    url = $(this).attr('href');
    $.ajax({
        url: url,
        method: "GET",
        success: function(response) {
            $('#data_view').html(response);
        }

    });
});
</script>


<script>
$(".tambah_item").on('click', function() {
    var id                  =   $(this).data('id') ;
    var qty                 =   $("#qty" + id).val() ;
    var berat               =   $("#berat" + id).val() ;
    var harga               =   $("#harga" + id).val() ;
    var estimasi            =   $("#estimasi" + id).val() ;
    var jumlah_do           =   $("#jumlah_do" + id).val() ;
    var ukuran_ayam         =   $("#ukuran_ayam" + id).val() ;
    var unit_cetakan        =   $("#unit_cetakan" + id).val() ;
    var gudang              =   $("#gudang" + id).val() ;
    var keterangan          =   $("#keterangan" + id).val() ;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: "{{ route('pembelian.purchasestore') }}",
        method: "POST",
        data: {
            id          :   id ,
            qty         :   qty ,
            berat       :   berat ,
            harga       :   harga ,
            estimasi    :   estimasi ,
            jumlah_do           :   jumlah_do ,
            ukuran_ayam         :   ukuran_ayam ,
            unit_cetakan        :   unit_cetakan ,
            gudang              :   gudang ,
            keterangan              :   keterangan ,
            key         :   'tambah_item' ,
        },
        success: function(data) {
            if (data.status == 400) {
                showAlert(data.msg);
            } else {
                showNotif(data.msg);
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');

                $("#loading_list").attr('style', 'display: block') ;
                $("#data_list").load("{{ route('pembelian.purchase', ['key' => 'list']) }}", function() {
                    $("#loading_list").attr('style', 'display: none') ;
                }) ;
                
                $("#purchase-info").load("{{ route('pembelian.purchase', ['key' => 'info']) }}") ;
                loadDataPerItem()
            }
        }
    });
})
</script>

<script>
    function validateheader(idrowitem, row, item){
        if(!"{{ $header }}"){
            $('#validateheader'+idrowitem).attr('data-toggle', '')
            showAlert('Silahkan bikin header terlebih dahulu');
        } else {
            $('#historyperitem'+idrowitem).load("{{ route('pembelian.purchase', ['key' => 'historyPO']) }}&subkey=peritem&item_id="+item+"&idrow="+idrowitem)
            $('#validateheader'+idrowitem).attr('data-toggle', 'modal')
        }
    }
</script>