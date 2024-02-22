<section class="panel">
    <div class="card-body">
        <table class="table default-table">
            <tbody>
                @foreach ($abf as $row)
                <tr>
                    <td>
                        @if ($row->asal_tujuan == 'kepala_produksi')
                        <ul>
                            @if ($row->abf_chiller->chillertofreestocktemp)
                            @php
                                $ft         =   $row->abf_chiller->chillertofreestocktemp->free_stock ;
                                $ft_temp    =   $row->abf_chiller->chillertofreestocktemp ;
                                $ext        =   json_decode($ft_temp->label) ;
                            @endphp
                            <li>
                                <div class="border p-1 mb-1">
                                    <b>{{ $ft->tanggal }} - KEPALA REGU {{ strtoupper($ft->regu) }}</b><br>
                                    {{ $ft_temp->item->nama }} <span class="status status-info">{{ number_format($ft_temp->qty) }} pcs</span> <span class="status status-warning">{{ $ft_temp->berat }} kg</span><br>
                                    {{ $row->plastik_sku }} - {{ $row->plastik_nama }} <span class="status status-success">{{ number_format($row->plastik_qty) }} pcs</span><br>
                                    @if ($ext)
                                        
                                        @if ($ext->parting->qty ?? '')
                                        <span class="status status-info">PARTING : {{ $ext->parting->qty }}</span>
                                        @endif
                                        @if ($ext->sub_item ?? '')
                                        <span class="status status-danger">CUSTOMER : {{ $ext->sub_item }}</span>
                                        @endif
                                    @endif
                                </div>
                            </li>
                            @endif
                        @endif

                            <ul>
                                @if ($row->asal_tujuan == 'free_stock')
                                <li>
                                    @if ($row->abf_freetemp)
                                        @php
                                            $freestock  =   $row->abf_freetemp->free_stock ;
                                            $temp       =   $row->abf_freetemp ;
                                            $label      =   json_decode($temp->label) ;
                                        @endphp
                                        <div class="border p-1">
                                            <b>{{ $freestock->tanggal }} - KEPALA REGU {{ strtoupper($freestock->regu) }}</b><br>
                                            {{ $temp->item->nama }} <span class="status status-info">{{ number_format($temp->qty) }} pcs</span> <span class="status status-warning">{{ number_format($temp->berat, 2) }} kg</span><br>
                                            {{ $row->plastik_sku }} - {{ $row->plastik_nama }} <span class="status status-success">{{ number_format($row->plastik_qty) }} pcs</span><br>
                                            @if ($label)
                                                @if ($label->parting->qty ?? '')
                                                <span class="status status-info">PARTING : {{ $label->parting->qty }}</span>
                                                @endif
                                                @if ($label->sub_item ?? '')
                                                <span class="status status-danger">CUSTOMER : {{ $label->sub_item }}</span>
                                                @endif
                                            @endif
                                        </div>
                                    @endif
                                </li>
                                @endif

                                @if ($row->asal_tujuan == 'kepala_produksi')
                                <li>
                                    @php
                                        $chiller    =   $row->abf_chiller ;
                                    @endphp
                                    <div class="border p-1">
                                        <b>{{ $chiller->tanggal_produksi }} - CHILLER</b><br>
                                        <a href="{{ route('chiller.show', $chiller->id) }}" target="_blank">{{ $chiller->id }}</a> {{ $chiller->item_name }} <span class="status status-info">{{ number_format($chiller->qty_item) }} pcs</span> <span class="status status-warning">{{ number_format($chiller->berat_item, 2) }} kg</span>
                                    </div>
                                </li>
                                @endif

                                @if ($row->asal_tujuan == 'retur')
                                @php
                                    $retur      =   $row->returitem->to_retur ;
                                    $retur_item =   $row->returitem ;
                                @endphp
                                <li>
                                    <div class="border p-1">
                                        <b>{{ $retur->tanggal_retur }} - RETUR</b><br>
                                        {{ $retur_item->to_item->nama }} <span class="status status-info">{{ number_format($retur_item->qty) }} pcs</span> <span class="status status-warning">{{ number_format($retur_item->berat, 2) }} kg</span><br>
                                        <span class="status status-success">{{ $retur->to_customer->nama }}</span>
                                        <span class="status status-danger">{{ $retur_item->catatan }}</span>
                                    </div>
                                </li>
                                @endif

                                <ul>
                                    <li>
                                        <div class="border p-1 mt-1">
                                            <b>{{ $row->tanggal_masuk }} - ABF</b><br>
                                            {{ $row->item_name }} <span class="status status-info">{{ number_format($row->qty_awal) }} pcs</span> <span class="status status-warning">{{ number_format($row->berat_awal, 2) }} kg</span>
                                        </div>
                                    </li>

                                    @if (COUNT($row->hasil_timbang_selesai))
                                    <ul>
                                        @foreach ($row->hasil_timbang_selesai as $item)
                                        <li>
                                            <div class="border p-1 mt-1">
                                                <b>{{ $item->production_date }} - {{ strtoupper($item->productgudang->code) }}</b><br>
                                                {{ $item->nama }}<br>
                                                Palete {{ $item->palete }} || Expired {{ $item->expired }} Bulan || Jenis : {{ $item->stock_type }}<br>
                                                <span class="status status-info">{{ number_format($item->qty_awal) }} pcs</span> <span class="status status-warning">{{ number_format($item->berat_awal, 2) }} kg</span>
                                                @if ($item->sub_item) <span class="status status-danger">CUSTOMER : {{ $item->sub_item }}</span> @endif
                                            </div>
                                        </li>

                                            @if (COUNT($item->productthawing))
                                                <ul>
                                                    @foreach ($item->productthawing as $list)
                                                    <li>
                                                        <div class="border p-1 mt-1">
                                                            <b>{{ $list->production_date }} - THAWING</b><br>
                                                            {{ $list->nama }}
                                                            <span class="status status-info">{{ number_format($list->qty_awal) }} pcs</span> <span class="status status-warning">{{ number_format($list->berat_awal, 2) }} kg</span>
                                                        </div>
                                                    </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        @endforeach
                                    </ul>
                                    @endif
                                </ul>
                            </ul>

                        @if ($row->asal_tujuan == 'kepala_produksi')
                        </ul>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="paginate">
            {{ $abf->appends($_GET)->onEachSide(1)->links() }}
        </div>
    </div>
</section>

<script>
$('.paginate .pagination a').on('click', function(e) {
    e.preventDefault();
    $('#text-notif').html('Loading...');
    $('#topbar-notification').fadeIn();

    url = $(this).attr('href');
    $.ajax({
        url: url,
        method: "GET",
        success: function(response) {
            $('#data_view').html(response).after($('#topbar-notification').fadeOut());
        }

    });
});
</script>

