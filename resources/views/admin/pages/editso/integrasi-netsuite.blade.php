@if (($bbwithstatussatu > 0) || $ns)
    @if ($ns)
        @if($ns->status=="2") <div class="status status-info"> Proses Integrasi <span class="fa fa-refresh fa-spin"></span></div>@endif
        @if (!empty($ns->failed))
            <div class="status status-danger">
                @php
                    $resp = json_decode($ns->failed);
                @endphp

                Gagal : {{ $resp[0]->message->message ?? $resp[0]->message }}
            </div>
            <a href="{{ route('editso.kirimfulfill') }}?order_id={{ $order->id }}&key=kirimUlangCreditLimit" class="btn btn-info btn-sm btnHiden pull-left mr-1 mt-1">Kirim Ulang Credit Limit</a>
        @endif
    @endif

    @if ($bbwithstatussatu)
        <a href="{{ route('editso.kirimti') }}?order_id={{ $order->id }}" class="btn btn-blue btn-sm btnHiden pull-right mr-1 mt-1 kirimtitambahan">Kirim TI (Fulfill di NS)</a>
        <a href="{{ route('editso.kirimfulfill') }}?order_id={{ $order->id }}" class="btn btn-green btn-sm btnHiden pull-right mr-1 mt-1 kirimfulfilltambahan" data-orderid="{{ $order->id }}">Kirim Fulfill Tambahan</a>
    @endif
    {{-- @else
        <span class='status status-success'>NO DO : {{$order->no_do}}</span>
    @endif --}}
@endif
<script>
    $(".btnHiden").click(function(){
        $('.kirimfulfilltambahan').addClass('disabled');
        $('.kirimtitambahan').addClass('disabled');
        $('#text-notif').html('Sedang Diproses ...');
        $('#topbar-notification').fadeIn();
    })
</script>
