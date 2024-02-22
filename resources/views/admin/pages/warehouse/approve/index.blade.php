<table class="table table-sm table-striped table-hover table-bordered">
    <thead class="sticky-top bg-white">
        <tr>
            <th class="text-center">ProduksiID</th>
            <th class="text-center">Tanggal</th>
            <th class="text-center">Regu</th>
            <th class="text-center">Item</th>
            <th class="text-center">Ekor/Pcs/Pack</th>
            <th class="text-center">Berat</th>
            <th class="text-center">#</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $row)
        @php
        $exp = json_decode($row->label) ;
        @endphp
        <tr>
            <td>{{ $row->free_stock->id }}</td>
            <td>{{ $row->free_stock->tanggal }}</td>
            <td>{{ $row->free_stock->regu }}</td>
            <td style="width:400px">
                <div style="width:400px">
                    @if ($row->selonjor) <div class="float-right text-danger font-weight-bold">SELONJOR</div> @endif
                    <div>{{ $row->item->nama }}</div>
                    <div class="row">
                        <div class="col pr-1">
                            @if ($row->kode_produksi)
                            Kode Produksi : {{ $row->kode_produksi }}
                            @endif
                            @if ($row->keranjang)
                                <div>{{ $row->keranjang }} Keranjang</div>
                            @endif
                        </div>
                        <div class="col pl-1 text-right">
                            @if ($row->unit)
                            Unit : {{ $row->unit }}
                            @endif
                        </div>
                    </div>
                    
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

                    @if (isset($exp->additional)) {{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }} {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }} {{ $exp->additional->maras ? 'Tanpa Maras' : '' }} @endif
                    <div class="row mt-1 text-info">
                        <div class="col pr-1">
                            @if ($row->customer_id) <div>Customer : {{ $row->konsumen->nama ?? '-' }}</div> @endif
                            @if (isset($exp->sub_item)) <div>Keterangan : {{ $exp->sub_item }}</div> @endif
                        </div>
                        <div class="col-auto pl-1 text-right">

                            @if (isset($exp->parting->qty)) Parting : {{ $exp->parting->qty }} @endif
                        </div>
                    </div>
                </div>
            </td>
            <td class="text-right">{{ number_format($row->qty) }}</td>
            <td class="text-right">{{ number_format($row->berat, 2) }}</td>
            <td class="text-center">
                @if ($row->tempchiller)
                <div class="status status-success">Selesai</div>
                <br>
                <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#riwayat"
                    onclick="riwayatApprove($(this).data('id'))" data-id="{{ $row->id }}">Riwayat</button>
                @else
                <button class="btn btn-primary btn-sm py-1" data-toggle="modal"
                    data-target="#approve{{ $row->id }}">Approve</button>
                @endif
            </td>
        </tr>

        <div class="modal fade" id="approve{{ $row->id }}" tabindex="-1" aria-labelledby="approve{{ $row->id }}Label"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="approve{{ $row->id }}Label">Approve Produksi</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col pr-1">
                                <div class="form-group">
                                    Tanggal Produksi
                                    <input type="text" value="{{ $row->free_stock->tanggal }}" disabled
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col pl-1">
                                <div class="form-group">
                                    Regu
                                    <input type="text" value="{{ $row->free_stock->regu }}" disabled
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            Item
                            <input type="text" value="{{ $row->item->nama }}" disabled class="form-control">
                            @if ($row->selonjor) <div class="text-danger font-weight-bold">SELONJOR</div> @endif
                            @if ($exp->plastik->jenis)
                            <div class="status status-success">
                                <div class="row">
                                    <div class="col pr-1">
                                        {{ $exp->plastik->jenis }}
                                    </div>
                                    <div class="col-auto pl-1">
                                        @if ($exp->plastik->qty > 0)
                                        <span class="float-right">// {{ $exp->plastik->qty }} Pcs</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if (isset($exp->additional)) {{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }} {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }} {{ $exp->additional->maras ? 'Tanpa Maras' : '' }} @endif
                            <div class="row mt-1 text-info">
                                <div class="col pr-1">
                                    @if ($row->customer_id) <div>Customer : {{ $row->konsumen->nama ?? '-' }}</div>
                                    @endif
                                    @if (isset($exp->sub_item)) <div>Keterangan : {{ $exp->sub_item }}</div> @endif
                                </div>
                                <div class="col-auto pl-1 text-right">

                                    @if (isset($exp->parting->qty)) Parting : {{ $exp->parting->qty }} @endif
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-6 pr-1">
                                    <div class="bg-light p-1 text-center">Qty</div>
                                    <input type="number" id="qty{{ $row->id }}"
                                        class="text-center rounded-0 form-control" value="{{ $row->qty }}"
                                        autocomplete="off">
                                </div>
                                <div class="col-6 pl-1">
                                    <div class="bg-light p-1 text-center">Berat</div>
                                    <input type="number" id="berat{{ $row->id }}"
                                        class="text-center rounded-0 form-control" value="{{ $row->berat }}" step="0.01"
                                        autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary approve_produksi"
                            data-id="{{ $row->id }}">Submit</button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </tbody>
</table>

<div class="modal fade" id="riwayat" aria-labelledby="riwayatLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="riwayatLabel">Riwayat Approve Produksi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="nodatariwayat"><div id="nodataresult"></div></div>
                <div id="riwayat_produksi" hidden>
                    <h5>Produksi</h5>
                    <div id="data_riwayat_produksi">
                        <div class="row mb-3">
                            <div class="col px-1">
                                Qty
                                <input type="number" id="qtyproduksi" value="" class="form-control form-control-sm p-1"
                                    placeholder="Qty" autocomplete="off" readonly>
                            </div>
                            <div class="col pl-0">
                                Berat
                                <input type="number" id="beratproduksi" value=""
                                    class="form-control form-control-sm p-1" step="0.01" placeholder="Berat"
                                    autocomplete="off" readonly>
                            </div>
                        </div>
                    </div>
                    <h5>Konfirmasi Approve</h5>
                    <div id="data_riwayat_konfirmasi">
                        <div class="row">
                            <div class="col px-1">
                                Qty
                                <input type="number" id="qtykonfirmasi" value=""
                                    class="form-control form-control-sm p-1" placeholder="Qty" autocomplete="off" readonly>
                            </div>
                            <div class="col pl-0">
                                Berat
                                <input type="number" id="beratkonfirmasi" value=""
                                class="form-control form-control-sm p-1" step="0.01" placeholder="Berat"
                                autocomplete="off" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(".approve_produksi").on('click', function() {
    var id      =   $(this).data('id') ;
    var qty     =   $("#qty" + id).val() ;
    var berat   =   $("#berat" + id).val() ;

    var mulai   =   $("#tanggal_mulai").val();
    var akhir   =   $("#tanggal_akhir").val();

    $(".approve_produksi").hide() ;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: "{{ route('warehouse.store') }}",
        method: "POST",
        data: {
            id          :   id ,
            qty         :   qty ,
            berat       :   berat ,
            key         :   'approve'
        },
        success: function(data) {
            if (data.status == 400) {
                showAlert(data.msg) ;
            } else {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                showNotif(data.msg);
                $("#{{ $request->key }}").load("{{ route('warehouse.index', ['key' => $request->key]) }}&tanggal_mulai=" + mulai + "&tanggal_akhir=" + akhir) ;
            }
            $(".approve_produksi").show() ;
        }
    });
});

function riwayatApprove(id){
    $('#nodataresult').remove();
    $.ajax({
        url: "{{ route('warehouse.index') }}",
        data: {
            id          :   id ,
            key         :   'riwayat_approve'
        },
        success: function(data) {
            if(data.hasil > 0){
                $('#riwayat_produksi').attr('hidden', false);
                let res = $.parseJSON(data.data.data)
                console.log(res)
                $('#qtyproduksi').val(res.data_produksi.qty)
                $('#beratproduksi').val(res.data_produksi.berat)
                $('#qtykonfirmasi').val(res.data_konfirmasi.qty)
                $('#beratkonfirmasi').val(res.data_konfirmasi.berat)
            } else {
                $('#riwayat_produksi').attr('hidden', true);
                $('#nodatariwayat').append('<div class="col-12 text-center" id="nodataresult">Tidak ada data</div>');
            }
        }
    })
}
</script>
