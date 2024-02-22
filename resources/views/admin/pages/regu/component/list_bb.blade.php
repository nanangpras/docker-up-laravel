<div class="table-responsive">
    <div class="pull-right mb-2">
        <span id="qty-diambil" class="status status-success">Qty Diambil 0</span>
        <span id="berat-diambil" class="status status-success">Berat Diambil 0
    </div>
    <table class="table default-table" width="100%">
        <thead>
            <tr>
                <th rowspan="2">Nama</th>
                <th rowspan="2">Tanggal</th>
                <th rowspan="2">Asal</th>
                {{-- @if(env('NET_SUBSIDIARY', 'EBA')=="EBA") --}}
                <th rowspan="2">Qty</th>
                <th rowspan="2">Berat</th>
                {{-- @endif --}}
                <th colspan="2">
                    <div>Pengambilan</div>
                </th>
                <th rowspan="2">
                    <div>Keterangan</div>
                </th>
            </tr>
        </thead>
        <tbody>
            @php
                $berat = 0;
                $qty = 0;
            @endphp
            @foreach ($bahan_baku as $no => $row)
                @php
                    $sisaQty        = $row->sisaQty;
                    $sisaBerat      = number_format((float)$row->sisaBerat, 2, '.', '');
                    // $sisaBerat      = $row->sisaBerat;
                @endphp
                @if($sisaBerat > 0)
                <tr>
                    <td style="width:30%">
                        {{$loop->iteration+($bahan_baku->currentpage() - 1) * $bahan_baku->perPage()}}.
                        <!-- {{$no+1}}. -->
                        {{ $row->item_name }} <a href="{{url('admin/chiller/'.$row->id)}}" target="_blank"><span class="fa fa-share"></span></a>

                        @if($row->kategori=="1")
                        <span class="status status-danger">[ABF]</span>
                        @elseif($row->kategori=="2")
                        <span class="status status-warning">[EKSPEDISI]</span>
                        @elseif($row->kategori=="3")
                        <span class="status status-warning">[TITIP CS]</span>
                        @else
                        <span class="status status-info">[CHILLER]</span>
                        @endif

                        @if ($row->asal_tujuan == 'retur')
                            <br><span class="status status-info">{{ $row->label }}</span>
                        @endif
                        @php
                            $diambil_berat = \App\Models\FreestockList::hitung_diambil($row->id, 'berat') ?? 0;
                            $diambil_qty = \App\Models\FreestockList::hitung_diambil($row->id, 'qty') ?? 0;
                        @endphp
                        @if ($diambil_berat > 0)
                            <br><span class="status status-danger">Sudah diambil ({{ $diambil_qty }} Ekor ||
                                {{ $diambil_berat }} Kg)</span>
                        @endif

                        @if ($row->type == 'hasil-produksi')
                            @if ($row->selonjor)<div class="font-weight-bold text-danger">SELONJOR</div>@endif
                            <div class="text-primary">{{ $row->customer_id ? $row->nama : "" }}</div>
                            @php
                                $exp = json_decode($row->label);
                            @endphp
                            @if ($exp)
                                @if (isset($exp->additional)) {{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }} {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }} {{ $exp->additional->maras ? 'Tanpa Maras' : '' }} @endif
                                <div class="row">
                                    <div class="text-success col pr-1">Keterangan : {{ $exp->sub_item ?? '' }}</div>
                                    <div class="text-primary col-auto pl-1 text-right">@if (isset($exp->parting->qty)) Part : {{ $exp->parting->qty }} @endif</div>
                                </div>
                            @endif
                        @endif
                    </td>
                    <td style="width:10%">@if ($row->tanggal_produksi != ''){{ date('d/m/y', strtotime($row->tanggal_produksi)) }}@endif</td>
                    <td style="width:10%">
                        {{ $row->asal_tujuan ?? '#' }}
                        <div class="small text-secondary">ID-{{ $row->id }}</div>
                    </td>
                    {{-- @if(env('NET_SUBSIDIARY', 'EBA')=="EBA") --}}
                    <td style="width:10%">{{ $sisaQty }}</td>
                    <td style="width:10%">{{ $sisaBerat }}</td>
                    {{-- @endif --}}
                    {{-- @if ($row->asal_tujuan == 'retur')
                        <td style="width:10%">{{ number_format($row->stock_item) }}</td>
                        <td style="width:10%">{{ number_format($row->stock_berat, 2) }}</td>
                    @else
                        <td></td>
                        <td></td>
                    @endif --}}
                    <td style="width:10%">
                        {{-- @if ($row->stock_berat <= 0 && $row->asal_tujuan == 'retur')

                        @else --}}
                        <input type="hidden" name="x_code[]" value="{{ $row->id }}">
                        <div >
                            <div class="row">
                                <div class="col pr-1">
                                    <input type="number" name="qty[]" style="width: 50px" class="p-1 form-control form-control-sm input-qty" placeholder="Ekor" id="inputQty" min="0" @if($kategori !== 'boneless') max="{{ $sisaQty }}" @endif>
                                    {{-- <input type="number" name="qty[]" style="width: 50px" class="p-1 form-control form-control-sm input-qty" placeholder="Ekor" min="0" max="{{ $row->stock_item - $row->total_qty_ambil }}"> --}}
                                </div>

                            </div>
                        </div>
                        {{-- @endif --}}
                    </td>
                    <td width="10%">
                        <div class="row">
                            <div class="col pr-1">
                                <input type="number" name="berat[]" style="width: 50px" class="p-1 form-control form-control-sm input-berat" step="0.01" placeholder="Berat" min="0" max="{{ $sisaBerat }}">
                            </div>
                        </div>
                    </td>
                    <td style="width:20%">
                        <div class="col pl-1">
                            <input type="text" name="catatan[]" style="width: 200px" class="p-1 form-control form-control-sm input-md" placeholder="Catatan">
                        </div>
                    </td>
                </tr>

                @php
                    $berat = $berat + $row->stock_berat;
                    $qty = $qty + $row->stock_item;
                @endphp
                @endif

            @endforeach
        </tbody>
    </table>
</div>

<div id="bb_paginate">
    {{ $bahan_baku->appends($_GET)->onEachSide(1)->links() }}
</div>

<script>
    var sum_qty = 0;
    $('.input-qty').on('keyup', function() {
        sum_qty = 0;
        $('.input-qty').each(function() {
            if ($(this).val().length > 0) {
                sum_qty += parseFloat($(this).val());
                // console.log(sum_qty);
            }
        });

        $('#qty-diambil').html("Qty diambil " + sum_qty + " Ekor");
    })
    var sum_berat = 0;
    $('.input-berat').on('keyup', function() {
        sum_berat = 0;
        $('.input-berat').each(function() {
            if ($(this).val().length > 0) {
                sum_berat += parseFloat($(this).val());
                // console.log(sum_berat);
            }
        });

        $('#berat-diambil').html("Berat diambil " + sum_berat + " Kg");
    })

    $('#bb_paginate .pagination a').on('click', function(e) {
        e.preventDefault();
        showNotif('Menunggu');
        var inputID     = "{{ $inputID }}"
        url = $(this).attr('href');

        if(inputID === '' || inputID === null || inputID === undefined){
            $.ajax({
                url: url,
                method: "GET",
                success: function(response) {
                    $('#bahanbaku').html(response);
                }

            });
        }else{
            $.ajax({
                url: url,
                method: "GET",
                success: function(response) {
                    $('#bahanbaku-fg'+ inputID).html(response);
                }

            });
        }
    });
</script>
