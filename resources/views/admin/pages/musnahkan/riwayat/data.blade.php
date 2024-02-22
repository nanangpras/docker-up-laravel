@foreach ($data as $row)
<section class="panel">
    <div class="card-body">
        <table class="table default-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                    <th>Metode</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $row->tanggal }}</td>
                    <td>{{ $row->keterangan }}</td>
                    <td>{{ $row->type }}</td>
                </tr>
            </tbody>
        </table>

        <table class="table default-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tanggal Item</th>
                    <th>Gudang</th>
                    <th>SKU</th>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Berat</th>
                    @if ($row->status != 3)
                    <th></th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($row->list_data as $i => $item)
                <tr>
                    <td>{{ ++$i }}</td>
                    
                    <td>
                        @if($item->gudang_id == '2' or $item->gudang_id == '4' or $item->gudang_id == '23' or $item->gudang_id == '24')
                        {{ $item->chiller->tanggal_produksi ?? "#" }}
                        @else
                        {{ $item->gudang->production_date ?? "#"}} 
                        @endif
                    </td>
                    <td>{{ $item->warehouse->code ?? 'Chiller' }}</td>
                    <td>{{ $row->type == 'gudang' ? ($item->gudang->productitems->sku ?? $item->chiller->chillitem->sku) : $item->items->sku }}</td>
                    <td>{{ $row->type == 'gudang' ? ($item->gudang->nama ?? $item->chiller->item_name) : $item->items->nama }}</td>
                    <td class="text-right">{{ $item->qty ?? 0 }}</td>
                    <td class="text-right">{{ $item->berat ?? 0 }}</td>
                    @if ($row->status != 3)
                    <td><button class="btn btn-sm btn-danger btn-block rounded-0 batalkan" data-id="{{ $item->id }}">Batal</button></td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="text-right">
            @if ($row->status == 3)
            <button class="btn btn-outline-dark netsuite_rollback" data-id="{{ $row->id }}">Netsuite Rollback</button>
            @else
            <button class="btn btn-outline-primary kirim_netsuite" data-id="{{ $row->id }}">Send To Netsuite</button> &nbsp;
            <button class="btn btn-outline-danger batal_semua" data-id="{{ $row->id }}">Batalkan Semua</button>
            @endif
        </div>

        @if (isset($netsuite))
        <div class="border-top mt-3 pt-3">
            <h6>Netsuite Terbentuk</h6>

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

                    @foreach ($netsuite as $no => $field_value)
                        @include('admin.pages.log.netsuite_one', ($netsuite = $field_value))
                    @endforeach

                </tbody>
            </table>

        </div>
        @endif
    </div>
</section>
@endforeach

<script>
    $('.batalkan').click(function() {
        var id      =   $(this).data('id') ;
        var awal    =   $("#tanggal_awal").val() ;
        var akhir   =   $("#tanggal_akhir").val() ;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.batalkan').hide() ;

        $.ajax({
            url: "{{ route('musnahkan.store') }}",
            method: "POST",
            data: {
                id      :   id,
                key     :   'batal_item'
            },
            success: function(data) {
                showNotif('List berhasil dihapus');
                $("#data_view").load("{{ route('musnahkan.riwayat', ['key' => 'view']) }}&awal=" + awal + "&akhir=" + akhir);
            }
        });
    })


    $('.batal_semua').click(function() {
        var id      =   $(this).data('id') ;
        var awal    =   $("#tanggal_awal").val() ;
        var akhir   =   $("#tanggal_akhir").val() ;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.batal_semua').hide() ;

        $.ajax({
            url: "{{ route('musnahkan.store') }}",
            method: "POST",
            data: {
                id      :   id,
                key     :   'batal_semua'
            },
            success: function(data) {
                if (data.status == 400) {
                    showAlert(data.msg) ;
                } else {
                    showNotif(data.msg);
                    $("#data_view").load("{{ route('musnahkan.riwayat', ['key' => 'view']) }}&awal=" + awal + "&akhir=" + akhir);
                }
                $('.batal_semua').show() ;
            }
        });
    })


    $('.kirim_netsuite').click(function() {
        var id      =   $(this).data('id') ;
        var awal    =   $("#tanggal_awal").val() ;
        var akhir   =   $("#tanggal_akhir").val() ;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.kirim_netsuite').hide() ;

        $.ajax({
            url: "{{ route('musnahkan.store') }}",
            method: "POST",
            data: {
                id      :   id,
                key     :   'kirim_netsuite'
            },
            success: function(data) {
                showNotif('Send to netsuite berhasil');
                $("#data_view").load("{{ route('musnahkan.riwayat', ['key' => 'view']) }}&awal=" + awal + "&akhir=" + akhir);
            }
        });
    })

    $('.netsuite_rollback').click(function() {
        var id      =   $(this).data('id') ;
        var awal    =   $("#tanggal_awal").val() ;
        var akhir   =   $("#tanggal_akhir").val() ;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.netsuite_rollback').hide() ;

        $.ajax({
            url: "{{ route('musnahkan.store') }}",
            method: "POST",
            data: {
                id      :   id,
                key     :   'netsuite_rollback'
            },
            success: function(data) {
                showNotif('Netsuite rollback berhasil');
                $("#data_view").load("{{ route('musnahkan.riwayat', ['key' => 'view']) }}&awal=" + awal + "&akhir=" + akhir);
            }
        });
    })
</script>
