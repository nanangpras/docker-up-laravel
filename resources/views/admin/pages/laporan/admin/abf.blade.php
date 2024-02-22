<div class="table-responsive">
    <table class="table default-table datatable" width="100%" id="abfTable">
        <thead>
            <tr>
                <th width="10px">No</th>
                <th>Nama</th>
                <th>Packaging</th>
                <th>Tanggal</th>
                <th>Qty</th>
                <th>Berat</th>
            </tr>
        </thead>
        {{-- <tbody>
            @foreach ($abf as $i => $row)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>
                        {{ $row->item_name }}
                        @if ($row->table_name == 'chiller')
                            @php
                                $exp = json_decode($row->abf_chiller->label);
                            @endphp

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
                                @if($exp->additional ?? '') 
                                    {{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }} 
                                    {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }} 
                                    {{ $exp->additional->maras ? 'Tanpa Maras' : '' }} 
                                @endif
                                <div class="row mt-1 text-info">
                                    <div class="col pr-1">@if ($exp->sub_item ?? '') Customer : {{ $exp->sub_item }} @endif</div>
                                    <div class="col-auto pl-1">@if ($exp->parting->qty ?? '') Parting : {{ $exp->parting->qty }} @endif</div>
                                </div>
                            @endif
                        @endif

                        @if ($row->table_name == 'free_stocktemp')
                            @php
                                $exp = json_decode($row->abf_freetemp->label ?? false);
                            @endphp

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
                                    <div class="col pr-1">@if ($exp->sub_item ?? '') Customer : {{ $exp->sub_item }} @endif</div>
                                    <div class="col-auto pl-1">@if ($exp->parting->qty ?? '') Parting : {{ $exp->parting->qty }} @endif</div>
                                </div>
                            @endif
                        @endif
                    </td>
                    <td>{{ $row->packaging }}</td>
                    <td>{{ date('d/m/Y', strtotime($row->created_at)) }}</td>
                    <td>{{ number_format($row->qty_item > 0 ? $row->qty_item : '0') }}</td>
                    <td class="text-right">{{ number_format($row->berat_item > 0 ? $row->berat_item : '0', 2) }}
                    </td>

                </tr>
            @endforeach
        </tbody> --}}
    </table>
</div>

<link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css"/>
<script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script>
<script>
    $(document).ready(function() {
        var tanggalmulai    = $("#tanggalstart").val();
        var tanggalselesai  = $("#tanggalend").val();
        $('#abfTable').DataTable({
            "bInfo"         : false,
            responsive      : true,
            scrollY         : 500,
            scrollX         : true,
            scrollCollapse  : true,
            paging          : true,
            searching       : true,
            processing      : true,
			serverSide      : true,
            ajax            : {
                url : "{{ route('laporanadmin.showDataTableAbf') }}",
                type: 'GET',
                cache : false,
                data: {
                    "tglmulai"    : tanggalmulai,
                    "tglselesai"  : tanggalselesai,
                }
            }
        });
        
        $('a[data-toggle="pill"]').on('shown.bs.tab', function(e) {
            $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
        });
    });
</script>

