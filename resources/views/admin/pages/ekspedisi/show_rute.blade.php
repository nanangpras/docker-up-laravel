<table class="table default-table">
    <thead>
        <tr>
            <th>Nomor SO</th>
            <th>Tanggal</th>
            <th>Qty</th>
            <th>Berat</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @php
            $total_qty =0;
            $total_berat =0;
        @endphp
        @foreach ($rute as $row)
        @php
            $total_qty += $row->qty;
            $total_berat += $row->berat
        @endphp
        <tr>
            <td>
                {{ $row->no_so }}<br>
                <span class="green small">{{ $row->marketing_so->socustomer->nama }}</span><br>
                <span class="text-danger">
                    {{ $row->marketing_so->status == 3 ? 'Verified' : 'Unverified' }}
                </span>
                
                @if($row->marketing_so->memo) <br><span class="red small">{{ $row->marketing_so->memo }}</span> @endif
            </td>
            <td>{{ date('d/m/Y',strtotime($row->marketing_so->tanggal_kirim ?? "")) }}</td>
            <td class="text-right">{{ $row->qty }}</td>
            <td class="text-right">{{ number_format($row->berat, 2) }}</td>
            <td><button class="btn btn-outline-danger hapus_do rounded-0" data-id="{{ $row->id }}"><div class="fa fa-arrow-left"></div></button></td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2">Total</td>
            <td class="text-right">{{$total_qty}}</td>
            <td class="text-right">{{number_format($total_berat,2)}}</td>
        </tr>
    </tfoot>
</table>


<script>
    $('.hapus_do').click(function() {
        var id              =   $(this).data('id') ;
        var tanggal_kirim   =   $("#tanggal_kirim").val() ;
        var cari            =   encodeURIComponent($("#cari").val()) ;
        var ekspedisi       =   "{{ $request->id ?? '' }}" ;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.hapus_do').hide() ;

        $.ajax({
            url: "{{ route('ekspedisi.store') }}",
            method: "POST",
            data: {
                id          :   id ,
                ekspedisi   :   ekspedisi ,
                key         :   'batal_do'
            },
            success: function(data) {
                if (data.status == 400) {
                    showAlert(data.msg);
                } else {
                    showNotif(data.msg);
                    $("#data_so").load("{{ route('ekspedisi.index', ['key' => 'sales_order']) }}&id=" + ekspedisi + "&tanggal_kirim=" + tanggal_kirim + "&cari=" + cari) ;
                    $("#show_rute").load("{{ route('ekspedisi.index', ['key' => 'show_rute']) }}&id=" + ekspedisi);
                }
                $('.hapus_do').show() ;
            }
        });
    })
</script>


