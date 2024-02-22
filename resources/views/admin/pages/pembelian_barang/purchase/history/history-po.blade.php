@if (COUNT($history))
<section class="panel">
            @if($subkey == 'perPR' || $subkey == 'peritem' || $subkey == 'PO Karkas Frozen' || $subkey == 'PO Non Karkas')
    <div class="accordion" id="accordion_po{{ $idrow }}">
        <div class="card">
            <div class="card-header" id="headingOne{{ $idrow }}">
                <div data-toggle="collapse" data-target="#collapse_po_umum{{ $idrow }}" aria-expanded="true" aria-controls="collapse{{ $idrow }}">
                    History Pembelian {{ $items->nama ?? '' }}
                </div>
            </div>

            <div id="collapse_po_umum{{ $idrow }}" class="collapse" aria-labelledby="headingOne{{ $idrow }}" data-parent="#accordion_po{{ $idrow }}">
            @else 
    <div class="accordion" id="accordion_po">
        <div class="card">
            <div class="card-header" id="headingOne">
                <div data-toggle="collapse" data-target="#collapse" aria-expanded="true" aria-controls="collapse{{ $idrow }}">
                    History Pembelian {{ $subkey }}
                </div>
            </div>

            <div id="collapse" class="collapse" aria-labelledby="headingOne{{ $idrow }}" data-parent="#accordion_po">
            @endif

                <div class="card-body">
                    <div class="border-bottom">
                        <table class="table default-table">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Supplier</th>
                                    <th>Harga</th>
                                    <th>Tanggal PO</th>
                                    <th>Tanggal Kirim</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($history as $row)
                                    <tr>
                                        <td>{{ $loop->iteration + ($history->currentpage() - 1) * $history->perPage() }}
                                        </td>
                                        <td>{{ $row->headbeli->supplier->nama ?? '' }}</td>
                                        <td class="text-right">{{ number_format($row->harga) }}</td>
                                        <td>{{ $row->headbeli->tanggal }}</td>
                                        <td>{{ $row->headbeli->tanggal_kirim }}</td>
                                    </tr>
                                @empty
                                    <p>Tidak ada data history pembelian</p>
                                @endforelse
                            </tbody>
                        </table>
                        @if($subkey == 'perPR' || $subkey == 'peritem'  || $subkey == 'PO Karkas Frozen' || $subkey == 'PO Non Karkas')
                        <div id="paginate_history_po{{ $idrow }}" class="mt-1">
                        @else
                        <div id="paginate_history_po" class="mt-1">
                        @endif
                            {{ $history->appends($_GET)->onEachSide(1)->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@else
@if(!$subkey == 'PO Karkas Frozen'  || !$subkey == 'PO Non Karkas')
<br>
@endif
<span class="status status-info mt-2">*Tidak ada data history pembelian {{ $items->nama ?? '' }}</span>
@endif


@if($subkey == 'perPR' || $subkey == 'peritem' || $subkey == 'PO Karkas Frozen' || $subkey == 'PO Non Karkas')
<script>
    
    var subkey      = "{{ $subkey }}"
    var id_paginate = "{{ $idrow }}"
    $('#paginate_history_po'+id_paginate+' .pagination a').on('click', function(e) {
        e.preventDefault();

        showNotif('Menunggu');
        url = $(this).attr('href');
        $.ajax({
            url: url,
            method: "GET",
            success: function(response) {
                if(subkey == 'perPR'){
                    $('#historyperPR'+ id_paginate).html(response);
                    $('#collapse_po_umum'+id_paginate).addClass('show');
                } else if(subkey == 'peritem') {
                    $('#historyperitem'+ id_paginate).html(response);
                    $('#collapse_po_umum'+id_paginate).addClass('show');
                } else if(subkey == 'PO Non Karkas'){
                    $('#history_po_nonkarkas-'+ id_paginate).html(response);
                    $('#collapse_po_umum'+id_paginate).addClass('show');
                } else if(subkey == 'PO Karkas Frozen') {
                    $('#history_po_karkasfrozen-'+ id_paginate).html(response);
                    $('#collapse_po_umum'+id_paginate).addClass('show');
                }
            }
        });
    });
</script>

@else

<script>
    $('#paginate_history_po .pagination a').on('click', function(e) {
        e.preventDefault();
        showNotif('Menunggu');
        url = $(this).attr('href');
        $.ajax({
            url: url,
            method: "GET",
            success: function(response) {
                if("{{ $subkey }}" == 'PO LB'){
                    $('#history_po_lb').html(response);
                    $('#collapse').addClass('show');
                } else if("{{ $subkey }}" == 'PO Karkas'){
                    $('#history_po_karkas').html(response);
                    $('#collapse').addClass('show');
                // } else if("{{ $subkey }}" == 'PO Non Karkas'){
                //     $('#history_po_nonkarkas').html(response);
                //     $('#collapse').addClass('show');
                }
            }
        });
    })
</script>

@endif
