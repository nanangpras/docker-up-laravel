@if(count($data) > 0)
<div class="rendemen-outer">
    <div class="scroll-rendemen">
        @foreach ($data as $key => $value)
        @php
            $total_berat = 0;
            $total_qty = 0;
            $total_customer = 0;
            $cari = App\Models\Ekspedisi_rute::where('ekspedisi_id', $value->id)->get();
            foreach($cari as $cari){
                $total_berat += $cari->berat;
                $total_qty += $cari->qty;
            }
            $total_customer = App\Models\Ekspedisi_rute::where('ekspedisi_id', $value->id)->count();
        @endphp
        <div class="mx-1">
            <div class="card mb-2" style="width: 200px; height: 160px; font-size: 9pt; {{ $key == 0 ? 'border: 1px solid #007bff;' : '' }} ">
                <div class="card-header">
                    <span class="status status-info">Nomor Urut: {{ $value->no_urut }}</span>
                </div>
                <div class="card-body p-2">
                    <div class="border-bottom p-1">
                        <span class="float-right font-weight-bold">
                            @if ($total_berat != 0)
                                {{ number_format($total_berat, 2) ?? '###' }}
                            @endif
                        </span>
                        Total Berat
                    </div>
                    <div class="border-bottom p-1">
                        <span class="float-right font-weight-bold">
                        @if ($total_qty != 0)
                            {{ number_format($total_qty, 2) ?? '###' }}
                        @endif
                        </span>
                        Total Qty
                    </div>
                    <div class="border-bottom p-1">
                        <span class="float-right font-weight-bold">
                        @if ($total_customer != 0)
                            {{ number_format($total_customer) ?? '###' }}
                        @endif
                        </span>
                        Total Customer
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif