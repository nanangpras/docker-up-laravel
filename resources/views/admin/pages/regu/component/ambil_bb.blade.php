<button type="button" id="btn_amblbb" class="btn btn-primary btn-block" data-toggle="modal" data-target="#ambilBB"
    style="margin-bottom: 10px">
    Ambil Bahan Baku
</button>

@if ($freestock && count($freestock->listfreestock) > 0)
<div class="mb-3">
    <span class="small">ID {{$freestock->id}} || Tanggal Prod : {{$freestock->tanggal}} || Dibuat :
        {{$freestock->created_at}} || Regu : {{$freestock->regu}} || User : {{$freestock->user_id}}</span>
    <table class="table default-table table-small tabel-bb">
        <thead>
            <th>Nama</th>
            <th>Tanggal BB</th>
            <th>Asal</th>
            <th>Ekor/Pcs/Pack</th>
            <th>Berat</th>
            <th>#</th>
        </thead>
        <tbody>
            @php
            $item = 0;
            $berat = 0;
            @endphp
            @foreach ($freestock->listfreestock as $row)
            @php
            $item += $row->qty;
            $berat += $row->berat;
            @endphp
            <tr>
                <td>{{ $row->chiller->item_name }}<br>@if($row->catatan != null)<span class="status status-info">{{ $row->catatan ?? ''}}@endif</span></td>
                <td>{{ $row->chiller->tanggal_produksi }}</td>
                <td>{{ $row->chiller->tujuan }}</td>
                <td>{{ $row->qty }}</td>
                <td>{{ $row->berat }} Kg</td>
                <td> <i class="fa fa-trash ml-2 hapus_bb text-danger" style="cursor:pointer;"
                        data-id="{{ $row->id }}"></i> <input type="hidden" class="id_bb" value="{{$row->id}}"></td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-center">Total</th>
                <th> {{ $item }} Ekor</th>
                <th>{{ $berat }} Kg</th>
                <th><input id="beratbb" value="{{$berat}}" type="hidden"></th>
            </tr>
        </tfoot>
    </table>
</div>

@else
<div class="alert alert-danger">Item bahan baku belum dipilih</div>
@endif



<div class="modal fade" id="ambilBB" tabindex="-1" aria-labelledby="ambilBBLabel" aria-hidden="true">
    {{-- <div class="modal-dialog modal-lg"> --}}
    <div class="modal-dialog" style="max-width:1200px;">
        <form action="{{ route('regu.ambilbb') }}" method="POST">
            @csrf @method('patch')
            <input type="hidden" name="type_input" id="type_input" value="full_production">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ambilBBLabel">Ambil Bahan Baku</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                Pencarian Tanggal
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) min="2023-01-01"
                                    @endif name="tanggal" class="form-control" value="{{ date('Y-m-d') }}"
                                    id="pencarian" placeholder="Cari...." autocomplete="off">
                            </div>
                        </div>
                        <div class="col">
                            Pencarian Kata
                            <input type="text" id="cari_bb" placeholder="Cari..." class="form-control mb-2">
                            <input type="hidden" name="kategori" value="{{ $request->kat ?? $regu }}">
                            <input type="hidden" name="orderitem" value="{{ $request->orderitem ?? '' }}">
                        </div>
                    </div>
                    <label class="btn btn-success">
                        <input id="karkas" type="checkbox" name="karkas"> Karkas / Chiller BB
                    </label>
                    <label class="btn btn-blue">
                        <input id="non-karkas" type="checkbox" name="non-karkas"> Non Karkas / Chiller FG
                    </label>
                    <label class="btn btn-danger">
                        <input id="bb-retur" type="checkbox" name="bb-retur"> Retur
                    </label>
                    <label class="btn btn-warning">
                        <input id="bb-thawing" type="checkbox" name="bb-thawing"> Thawing
                    </label>

                    <label class="btn btn-info">
                        <input id="bb-abf" type="checkbox" name="bb-abf"> Kirim ABF
                    </label>

                    <div id="loading" class="text-center" style="display: none">
                        <img src="{{ asset('loading.gif') }}" width="20px">
                    </div>
                    <div id="bahanbaku"></div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btnHiden">Tambah</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>

    var delayTimer; 
    $("#btn_amblbb").on('click', function () {
        // $('#loading').show();
        if (delayTimer != null) {
            clearTimeout(delayTimer);
        }
        delayTimer = setTimeout(function() {
            delayTimer = null;  
            //ajax code
            loadingBB();
        }, 2000);  
    });

    // kategori
    $('#pencarian,#karkas,#non-karkas,#bb-retur,#bb-thawing,#bb-abf').on('change', function() {
        // $('#loading').show();
        if (delayTimer != null) {
            clearTimeout(delayTimer);
        }
        delayTimer = setTimeout(function() {
            delayTimer = null;  
            //ajax code
            loadingBB();
        }, 2000);  
    })
    // end kategori
    var queryString     = window.location.search;
    var urlParams       = new URLSearchParams(queryString);
    var kategori        = urlParams.get('kategori');
    function loadingBB(){
        $('#loading').show();
        
        var tanggal         = $("#pencarian").val();
        var cari_bb         = document.getElementById('cari_bb').value;
        var karkas          = $('#karkas').is(':checked');
        var non_karkas      = $('#non-karkas').is(':checked');
        var bb_retur        = $('#bb-retur').is(':checked');
        var bb_thawing      = $('#bb-thawing').is(':checked');
        var bb_abf          = $('#bb-abf').is(':checked');
        var url_bb          = "{{ url('admin/produksi-regu/bahanbaku?tanggal=') }}" + tanggal+ "&karkas=" + karkas + "&non_karkas="+non_karkas+"&bb_retur="+bb_retur+"&bb_thawing="+bb_thawing +"&bb_abf="+bb_abf + "&kategori=" + kategori +"&search=" +encodeURIComponent($("#cari_bb").val());
        
        $("#bahanbaku").load(url_bb, function () { 
            $('#loading').hide() 
        });

    }

    var globalTimeout = null;  

    $('#cari_bb').keyup(function() {
        $('#loading').show()
            if (globalTimeout != null) {
                clearTimeout(globalTimeout);
            }
            globalTimeout = setTimeout(function() {
            globalTimeout = null;  

                //ajax code
            loadingBB()

        }, 2000);  
    })
</script>