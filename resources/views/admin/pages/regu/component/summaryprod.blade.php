
{{-- <script>
    $('#search-filter-prod').on('keyup', function() {
        var filter = $(this).val(),
            count = 0;
        $('.filter-name').each(function() {
            if ($(this).text().search(new RegExp(filter, "i")) <
                0) {
                $(this).hide();
            } else {
                $(this).show();
                count++;
            }

        });
    })
</script> --}}
<div class="row">
    <div class="col">
        <div class="form-group">
            <div class="bg-primary p-2 text-center text-light font-weight-bold text-uppercase">Total ABF</div>
            <div class="border p-2 text-center">
                <h5 class="mb-0">{{number_format($totabf,2)}}</h5>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <div class="bg-success p-2 text-center text-light font-weight-bold text-uppercase">Total Chiller</div>
            <div class="border p-2 text-center">
                <h5 class="mb-0">{{number_format($totalchiller,2)}}</h5>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <div class="bg-warning p-2 text-center text-light font-weight-bold text-uppercase">Total Ekspedisi</div>
            <div class="border p-2 text-center">
                <h5 class="mb-0">{{number_format($toteks,2)}}</h5>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <div class="bg-info p-2 text-center text-light font-weight-bold text-uppercase">Total Produksi</div>
            <div class="border p-2 text-center">
                <h5 class="mb-0">{{number_format($totprod,2)}}</h5>
            </div>
        </div>
    </div>
</div>
{{-- <!-- <a href="{{ route('produksi.summaryprod', ['key' => 'unduh']) }}&regu={{ $regu }}&tanggal={{ $tanggal }}&tanggalend={{ $tanggalend }}&filterabf={{ $filterabf }}&filterekspedisi={{ $filterekspedisi }}&filterchiller={{ $filterchiller }}" class="btn btn-success mb-2 float-right unduhsummaryproduksi"><i class="fa fa-download"></i>  Unduh</a> --> --}}
<button type="button" class="btn btn-success mb-2 float-right unduhsummaryproduksi"><i class="fa fa-download"></i> Unduh</button>
{{-- <button type="button" class="btn btn-blue mb-2 float-right exporttracingproduksi mr-3"> Export Excel Tracing </button> --}}
<table class="table default-table table-small table-hover" id="filterdata">
    <thead>
        <th>Hasil Produksi</th>
        <th>Customer</th>
        <th>Tanggal Produksi</th>
        <th class="text-center">Ekor/Pcs/Pack</th>
        <th class="text-center">Berat</th>
    </thead>
    <tbody>
        @php
            $qty = 0;
            $berat = 0;
            $totalekor = 0;
            $totalquantity = 0;
        @endphp
        @foreach ($freestock as $no => $row)

            @foreach ($row->freetemp as $no => $item)
                @php
                    $qty += $item->qty;
                    $berat += $item->berat;
                    $exp = json_decode($item->label);
                @endphp
                @if($filterabf == 'true' && $filterchiller == 'false')
                    <tr class="filter-name" @if($item->kategori == '0' || $item->kategori == '3' || $item->kategori == 'NULL') class="display-none" style="display:none" @endif >
                        <td >
                            @php 
                                $codeAbf = $item->freetempchiller->ambil_abf ?? '';
                            @endphp
                            @if($codeAbf !== '')
                                @foreach($codeAbf as $kode)
                                <div class="float-right text-secondary small">#ABF-{{ $kode->id }}</div>
                                @endforeach
                            @else
                                <div class="float-right text-secondary small">#ABF-#</div>
                            @endif
                            @if ($item->created_at != $item->updated_at)
                                {{-- <span class="status status-warning px-2 py-1">  </span> --}}
                                <button type="button" class="status status-warning px-2 py-1" data-toggle="modal"
                                    data-target="#info-log" data-id="{{ $item->id }}"
                                    data-item="{{ $item->item->nama }}"
                                    onclick="logsummary($(this).data('id'), $(this).data('item'));">EDIT</button>
                            @endif

                            @if ($item->kategori == '1')
                                <span class="status status-danger">[ABF] </span>
                            @elseif($item->kategori == '2')
                                <span class="status status-warning">[EKSPEDISI] </span>
                            @else
                                <span class="status status-info">[CHILLER] </span>
                            @endif

                            {{ $item->item->nama ?? '' }}
                            <div class="row">
                                <div class="col pr-1">
                                    @if ($item->kode_produksi)
                                        Kode Produksi : {{ $item->kode_produksi }}
                                    @endif
                                    @if ($item->keranjang)
                                        <div>{{ $item->keranjang }} Keranjang</div>
                                    @endif
                                </div>
                                <div class="col pl-1 text-right">
                                    @if ($item->unit)
                                    Unit : {{ $item->unit }}
                                    @endif
                                </div>
                            </div>

                            <div class="status status-success">
                                <div class="row">
                                    <div class="col pr-1">
                                        {{ $item->plastik_nama }}
                                    </div>
                                    <div class="col-auto pl-1">
                                        <span class="float-right">// {{ $item->plastik_qty }} Pcs</span>
                                    </div>
                                </div>
                            </div>
                    

                            @if ($exp->additional)
                                <div>{{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }}
                                    {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }}
                                    {{ $exp->additional->maras ? 'Tanpa Maras' : '' }}</div>
                            @endif
                            <div class="row mt-1 text-info">
                                <div class="col pr-1">
                                    @if ($exp->sub_item ?? '')
                                        <div>Customer : {{ $exp->sub_item }}</div>
                                    @endif
                                </div>
                                <div class="col-auto pl-1 text-right">
                                    @if ($exp->parting->qty)
                                        Parting : {{ $exp->parting->qty }}
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{$item->konsumen->nama ?? ''}}</td>
                        <td>{{$row->tanggal ?? ''}}</td>
                        <td class="text-center">{{ number_format($item->qty) }}</td>
                        <td class="text-center">{{ number_format($item->berat, 2) }} Kg</td>
                    </tr>
                @elseif($filterchiller == 'true' && $filterabf == 'false')
                    <tr class="filter-name" @if($item->kategori == '1') class="display-none" style="display:none" @endif>
                        <td >
                            <div class="float-right text-secondary small">#CHIL-{{ $item->freetempchiller->id ?? '#'}}</div>
                            @if ($item->created_at != $item->updated_at)
                                {{-- <span class="status status-warning px-2 py-1">  </span> --}}
                                <button type="button" class="status status-warning px-2 py-1" data-toggle="modal"
                                    data-target="#info-log" data-id="{{ $item->id }}"
                                    data-item="{{ $item->item->nama }}"
                                    onclick="logsummary($(this).data('id'), $(this).data('item'));">EDIT</button>
                            @endif

                            @if ($item->kategori == '1')
                                <span class="status status-danger">[ABF] </span>
                            @elseif($item->kategori == '2')
                                <span class="status status-warning">[EKSPEDISI] </span>
                            @else
                                <span class="status status-info">[CHILLER] </span>
                            @endif

                            {{ $item->item->nama ?? '' }}
                            <div class="row">
                                <div class="col pr-1">
                                    @if ($item->kode_produksi)
                                        Kode Produksi : {{ $item->kode_produksi }}
                                    @endif
                                    @if ($item->keranjang)
                                        <div>{{ $item->keranjang }} Keranjang</div>
                                    @endif
                                </div>
                                <div class="col pl-1 text-right">
                                    @if ($item->unit)
                                    Unit : {{ $item->unit }}
                                    @endif
                                </div>
                            </div>

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

                            @if ($exp->additional)
                                <div>{{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }}
                                    {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }}
                                    {{ $exp->additional->maras ? 'Tanpa Maras' : '' }}</div>
                            @endif
                            <div class="row mt-1 text-info">
                                <div class="col pr-1">
                                    @if ($exp->sub_item ?? '')
                                        <div>Customer : {{ $exp->sub_item }}</div>
                                    @endif
                                </div>
                                <div class="col-auto pl-1 text-right">
                                    @if ($exp->parting->qty)
                                        Parting : {{ $exp->parting->qty }}
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{$item->konsumen->nama ?? ''}}</td>
                        <td>{{$row->tanggal ?? ''}}</td>
                        <td class="text-center">{{ number_format($item->qty) }}</td>
                        <td class="text-center">{{ number_format($item->berat, 2) }} Kg</td>
                    </tr>
                @elseif($filterchiller == 'true' && $filterabf == 'true')
                    <tr class="filter-name" >
                        <td>
                            @if($item->kategori == '1')
                                @php $codeAbf = $item->freetempchiller->ambil_abf ?? ''; @endphp
                                @if($codeAbf !== '')
                                    @foreach($codeAbf as $kode)
                                    <div class="float-right text-secondary small">#ABF-{{ $kode->id }}</div>
                                    @endforeach
                                @else
                                    <div class="float-right text-secondary small">#ABF-#</div>
                                @endif
                            @elseif($item->kategori == '0' || $item->kategori == '3' || $item->kategori == 'NULL')
                                <div class="float-right text-secondary small">#CHIL-{{ $item->freetempchiller->id ?? '#'}}</div>
                            @endif
                            @if ($item->created_at != $item->updated_at)
                                {{-- <span class="status status-warning px-2 py-1">  </span> --}}
                                <button type="button" class="status status-warning px-2 py-1" data-toggle="modal"
                                    data-target="#info-log" data-id="{{ $item->id }}"
                                    data-item="{{ $item->item->nama }}"
                                    onclick="logsummary($(this).data('id'), $(this).data('item'));">EDIT</button>
                            @endif

                            @if ($item->kategori == '1')
                                <span class="status status-danger">[ABF] </span>
                            @elseif($item->kategori == '2')
                                <span class="status status-warning">[EKSPEDISI] </span>
                            @else
                                <span class="status status-info">[CHILLER] </span>
                            @endif

                            {{ $item->item->nama ?? '' }}
                            <div class="row">
                                <div class="col pr-1">
                                    @if ($item->kode_produksi)
                                        Kode Produksi : {{ $item->kode_produksi }}
                                    @endif
                                    @if ($item->keranjang)
                                        <div>{{ $item->keranjang }} Keranjang</div>
                                    @endif
                                </div>
                                <div class="col pl-1 text-right">
                                    @if ($item->unit)
                                    Unit : {{ $item->unit }}
                                    @endif
                                </div>
                            </div>

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

                            @if ($exp->additional)
                                <div>{{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }}
                                    {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }}
                                    {{ $exp->additional->maras ? 'Tanpa Maras' : '' }}</div>
                            @endif
                            <div class="row mt-1 text-info">
                                <div class="col pr-1">
                                    @if ($exp->sub_item ?? '')
                                        <div>Customer : {{ $exp->sub_item }}</div>
                                    @endif
                                </div>
                                <div class="col-auto pl-1 text-right">
                                    @if ($exp->parting->qty)
                                        Parting : {{ $exp->parting->qty }}
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{$item->konsumen->nama ?? ''}}</td>
                        <td>{{$row->tanggal ?? ''}}</td>
                        <td class="text-center">{{ number_format($item->qty) }}</td>
                        <td class="text-center">{{ number_format($item->berat, 2) }} Kg</td>
                    </tr>
                @else
                    <tr class="filter-name">
                        <td>
                            @if($item->kategori == '1')
                                @php $codeAbf = $item->freetempchiller->ambil_abf ?? ''; @endphp
                                @if($codeAbf !== '')
                                    @foreach($codeAbf as $kode)
                                    <div class="float-right text-secondary small">#ABF-{{ $kode->id }}</div>
                                    @endforeach
                                @else
                                    <div class="float-right text-secondary small">#ABF-#</div>
                                @endif
                            @elseif($item->kategori == '0' || $item->kategori == '3' || $item->kategori == 'NULL')
                                <div class="float-right text-secondary small">#CHIL-{{ $item->freetempchiller->id ?? '#'}}</div>
                            @endif
                            @if ($item->created_at != $item->updated_at)
                                {{-- <span class="status status-warning px-2 py-1">  </span> --}}
                                <button type="button" class="status status-warning px-2 py-1" data-toggle="modal"
                                    data-target="#info-log" data-id="{{ $item->id }}"
                                    data-item="{{ $item->item->nama }}"
                                    onclick="logsummary($(this).data('id'), $(this).data('item'));">EDIT</button>
                            @endif

                            @if ($item->kategori == '1')
                                <span class="status status-danger">[ABF] </span>
                            @elseif($item->kategori == '2')
                                <span class="status status-warning">[EKSPEDISI] </span>
                            @else
                                <span class="status status-info">[CHILLER] </span>
                            @endif

                            {{ $item->item->nama ?? '' }}
                            <div class="row">
                                <div class="col pr-1">
                                    @if ($item->kode_produksi)
                                        Kode Produksi : {{ $item->kode_produksi }}
                                    @endif
                                    @if ($item->keranjang)
                                        <div>{{ $item->keranjang }} Keranjang</div>
                                    @endif
                                </div>
                                <div class="col pl-1 text-right">
                                    @if ($item->unit)
                                    Unit : {{ $item->unit }}
                                    @endif
                                </div>
                            </div>

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

                            @if ($exp->additional)
                                <div>{{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }}
                                    {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }}
                                    {{ $exp->additional->maras ? 'Tanpa Maras' : '' }}</div>
                            @endif
                            <div class="row mt-1 text-info">
                                <div class="col pr-1">
                                    @if ($exp->sub_item ?? '')
                                        <div>Customer : {{ $exp->sub_item }}</div>
                                    @endif
                                </div>
                                <div class="col-auto pl-1 text-right">
                                    @if ($exp->parting->qty)
                                        Parting : {{ $exp->parting->qty }}
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{$item->konsumen->nama ?? ''}}</td>
                        <td>{{$row->tanggal ?? ''}}</td>
                        <td class="text-center">{{ number_format($item->qty) }}</td>
                        <td class="text-center">{{ number_format($item->berat, 2) }} Kg</td>
                    </tr>
                @endif
                @php  
                    if($filterabf == 'true' && $filterchiller=='true' && $filterekspedisi=='true'){
                        $totalekor      += $item->qty; 
                        $totalquantity  += $item->berat;
                    }
                    else if($filterabf == 'true' && $filterchiller=='true' && $filterekspedisi=='false'){
                        if($item->kategori == '1' || $item->kategori == '0' || $item->kategori == '3'){
                            $totalekor      += $item->qty; 
                            $totalquantity  += $item->berat;
                        }
                    }
                    else if($filterabf == 'true' && $filterchiller == 'false' && $filterekspedisi =='false'){
                        if($item->kategori == '1'){
                            $totalekor      += $item->qty; 
                            $totalquantity  += $item->berat;
                        }
                    }
                    else if($filterabf == 'true' && $filterchiller == 'false' && $filterekspedisi =='true'){
                        if($item->kategori == '1' || $item->kategori == '2'){
                            $totalekor      += $item->qty; 
                            $totalquantity  += $item->berat;;
                        }
                    }
                    else if($filterabf == 'false' && $filterchiller == 'true' && $filterekspedisi =='true'){
                        if($item->kategori == '2' || $item->kategori == '0' || $item->kategori == '3'){
                            $totalekor      += $item->qty; 
                            $totalquantity  += $item->berat;
                        }
                    }
                    else if($filterabf == 'false' && $filterchiller == 'true' && $filterekspedisi =='false'){
                        if($item->kategori == '0' || $item->kategori == '3'){
                            $totalekor      += $item->qty; 
                            $totalquantity  += $item->berat;
                        }
                    }
                    else if($filterabf == 'false' && $filterchiller == 'false' && $filterekspedisi =='true'){
                        if($item->kategori == '2'){
                            $totalekor      += $item->qty; 
                            $totalquantity  += $item->berat;
                        }
                    } 
                    
                    else{
                        $totalekor      += $item->qty; 
                        $totalquantity  += $item->berat;   
                    }
                    
                @endphp
            @endforeach
        @endforeach
    </tbody>
</table>

<div id="paginateFreestock">
    {{ $freestock->appends($_GET)->onEachSide(1)->links() }}
</div>



<div class="modal fade" id="info-log" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="bbLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bbLabel">Log Edit</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div>Item</div>
                    <b id="log-nama"></b>
                </div>
                <div class="row">
                    <table class="table default-table">
                        <thead>
                            <tr>
                                <th class="text-center">ID Log</th>
                                <th class="text-center">Activity</th>
                                <th class="text-center">Tanggal Edit</th>
                            </tr>
                        </thead>
                        <td colspan="3" class="text-center" style="display:none" id="loading_filter"><i class="fa fa-refresh fa-spin"></i> Loading</td>
                        <tbody id="table_log">


                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function logsummary(id, item) {
        // console.log(item)
        $('#table_log').html('')
        $('#log-nama').text(item)
        $.ajax({
            url: "{{ route('produksi.summaryprod', ['key' => 'logsummary']) }}",
            methode: "POST",
            datatype: "JSON",
            data: {
                id: id
            },
            beforeSend: res => {
                $('#loading_filter').show()
            },
            success: res => {
                $('#loading_filter').hide()
                // console.log(res)
                if (res.length > 0) {
                    res.forEach(row => {
                        let date = new Date(row.created_at).toLocaleString()
                        $('#table_log').append(`
                            <tr>
                                <td class="text-center">${row.id}</td>
                                <td class="text-center">${row.activity}</td>
                                <td class="text-center">${date}</td>
                            </tr>
                        `)
                    })
                } else {
                    $('#table_log').append(`
                        <tr>
                            <td colspan="3" class="text-center">Tidak ada log</td>
                        </tr>
                    `)
                }
            }
        })
    }

    $(".unduhsummaryproduksi").on('click', (e) => {
        e.preventDefault();
        window.location.href = "{{ route('produksi.summaryprod', ['key' => 'unduh']) }}&regu={{ $regu }}&tanggal={{ $tanggal }}&tanggalend={{ $tanggalend }}&filterabf={{ $filterabf }}&filterekspedisi={{ $filterekspedisi }}&filterchiller={{ $filterchiller }}";    
    });

    $(".exporttracingproduksi").on('click', (e) => {
        e.preventDefault();
        window.location.href = "{{ route('produksi.summaryprod', ['key' => 'viewExportTracing']) }}";
    });

</script>

<script>
    $('#paginateFreestock .pagination a').on('click', function(e) {
        e.preventDefault();
        // showNotif('Menunggu');
        $('#text-notif').html('Menunggu...');
        $('#topbar-notification').fadeIn();

        url = $(this).attr('href');
        $.ajax({
            url: url,
            method: "GET",
            success: function(response) {
                $('#list_summaryprod').html(response).after($('#topbar-notification').fadeOut());
            }
        });
    });

    $('#paginateByOrder .pagination a').on('click', function(e) {
        e.preventDefault();
        // showNotif('Menunggu');
        $('#text-notif').html('Menunggu...');
        $('#topbar-notification').fadeIn();

        url = $(this).attr('href');
        $.ajax({
            url: url,
            method: "GET",
            success: function(response) {
                $('#list_summaryprod').html(response).after($('#topbar-notification').fadeOut());
            }
        });
    });
</script>
