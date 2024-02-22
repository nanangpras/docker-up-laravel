<section class="panel">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <div id="export-fulfillment">

                <h6> Fulfillment </h6>
                <table class="table default-table">
                    <style>
                        .text {
                            mso-number-format:"\@";
                            border:thin solid black;
                        }
                    </style>
                    <thead>
                        <tr>
                            <th  class="text">No</th>
                            <th  class="text">Nama</th>
                            <th  class="text">Item</th>
                            {{-- <th  class="text">Tanggal Kirim</th> --}}
                            <th  class="text">Order Item</th>
                            <th  class="text">Order Berat</th>
                            <th  class="text">Fulfillment Item</th>
                            <th  class="text">Fulfillment Berat</th>
                            <th  class="text">Keterangan Tidak terkirim</th>
                            <th  class="text">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($fulfillment as $i => $full)
                            @php
                                $berat = 0;
                                $item = 0;
                            @endphp
                            @foreach ($full->daftar_order as $tot)
                                @php
                                    $berat = $berat + $tot->berat;
                                    $item = $item + $tot->qty;
                                @endphp
                            @endforeach
                            <tr>
                                <td  class="text">{{ ++$i }}</td>
                                <td  class="text">{{ $full->nama }}</td>
                                {{-- <td  class="text">{{ $full->tanggal_kirim }}</td> --}}
                                <td  class="text">
                                    @foreach ($full->daftar_order as $detail)
                                        @if ($request->status)
                                            @if ($request->status == 'all')
                                                {{ $detail->line_id }}. {{ $detail->item->nama }} <br>
                                            @endif

                                            @if ($request->status == 'pending')
                                                @if ($detail->status == NULL)
                                                    {{ $detail->line_id }}. {{ $detail->item->nama }} <br>
                                                @endif
                                            @endif

                                            @if ($request->status == 'kirim')
                                                @if ($detail->status > NULL)
                                                    {{ $detail->line_id }}. {{ $detail->item->nama }} <br>
                                                @endif
                                            @endif
                                        @else
                                            {{ $detail->line_id }}. {{ $detail->item->nama }} <br>
                                        @endif
                                    @endforeach
                                </td>
                                <td  class="text">
                                    @foreach ($full->daftar_order as $detail)
                                        @if ($request->status)
                                            @if ($request->status == 'all')
                                                {{ number_format($detail->qty, 0) }} <br>
                                            @endif

                                            @if ($request->status == 'pending')
                                                @if ($detail->status == NULL)
                                                    {{ number_format($detail->qty, 0) }} <br>
                                                @endif
                                            @endif

                                            @if ($request->status == 'kirim')
                                                @if ($detail->status > NULL)
                                                    {{ number_format($detail->qty, 0) }} <br>
                                                @endif
                                            @endif
                                        @else
                                            {{ number_format($detail->qty, 0) }} <br>
                                        @endif
                                    @endforeach
                                </td>
                                <td  class="text">
                                    @foreach ($full->daftar_order as $detail)
                                        @if ($request->status)
                                            @if ($request->status == 'all')
                                                {{ number_format($detail->berat, 2) }} <br>
                                            @endif

                                            @if ($request->status == 'pending')
                                                @if ($detail->status == NULL)
                                                    {{ number_format($detail->berat, 2) }} <br>
                                                @endif
                                            @endif

                                            @if ($request->status == 'kirim')
                                                @if ($detail->status > NULL)
                                                    {{ number_format($detail->berat, 2) }} <br>
                                                @endif
                                            @endif
                                        @else
                                            {{ number_format($detail->berat, 2) }} <br>
                                        @endif
                                    @endforeach
                                </td>
                                <td  class="text">
                                    @foreach ($full->daftar_order as $detail)
                                        @if ($request->status)
                                            @if ($request->status == 'all')
                                                {{ number_format($detail->fulfillment_qty, 0) }} <br>
                                            @endif

                                            @if ($request->status == 'pending')
                                                @if ($detail->status == NULL)
                                                    {{ number_format($detail->fulfillment_qty, 0) }} <br>
                                                @endif
                                            @endif

                                            @if ($request->status == 'kirim')
                                                @if ($detail->status > NULL)
                                                    {{ number_format($detail->fulfillment_qty, 0) }} <br>
                                                @endif
                                            @endif
                                        @else
                                            {{ number_format($detail->fulfillment_qty, 0) }} <br>
                                        @endif
                                    @endforeach
                                </td>
                                <td  class="text">
                                    @foreach ($full->daftar_order as $detail)
                                        @if ($request->status)
                                            @if ($request->status == 'all')
                                                {{ number_format($detail->fulfillment_berat, 2) }} <br>
                                            @endif

                                            @if ($request->status == 'pending')
                                                @if ($detail->status == NULL)
                                                    {{ number_format($detail->fulfillment_berat, 2) }} <br>
                                                @endif
                                            @endif

                                            @if ($request->status == 'kirim')
                                                @if ($detail->status > NULL)
                                                    {{ number_format($detail->fulfillment_berat, 2) }} <br>
                                                @endif
                                            @endif
                                        @else
                                            {{ number_format($detail->fulfillment_berat, 2) }} <br>
                                        @endif
                                    @endforeach
                                </td>
                                <td  class="text">
                                    @foreach ($full->daftar_order as $detail)
                                        @if ($request->status)
                                            @if ($request->status == 'all')
                                                {{ $detail->tidak_terkirim_catatan }} <br>
                                            @endif

                                            @if ($request->status == 'pending')
                                                @if ($detail->status == NULL)
                                                {{ $detail->tidak_terkirim_catatan }} <br>
                                                @endif
                                            @endif

                                            @if ($request->status == 'kirim')
                                                @if ($detail->status > NULL)
                                                {{ $detail->tidak_terkirim_catatan }} <br>
                                                @endif
                                            @endif
                                        @else
                                            {{ $detail->tidak_terkirim_catatan }} <br>
                                        @endif
                                    @endforeach
                                </td>
                                <td  class="text">
                                    @foreach ($full->daftar_order as $detail)
                                        @if ($request->status)
                                            @if ($request->status == 'all')
                                                @if ($detail->status > null)
                                                    <span class="rounded-0 badge badge-info">Terkirim</span><br>
                                                @else
                                                    <span class="rounded-0 badge badge-danger">Pending</span><br>
                                                @endif
                                            @endif

                                            @if ($request->status == 'pending')
                                                @if ($detail->status == NULL)
                                                    <span class="rounded-0 badge badge-danger">Pending</span><br>
                                                @endif
                                            @endif

                                            @if ($request->status == 'kirim')
                                                @if ($detail->status > NULL)
                                                    <span class="rounded-0 badge badge-info">Terkirim</span><br>
                                                @endif
                                            @endif
                                        @else
                                            @if ($detail->status > null)
                                                <span class="rounded-0 badge badge-info">Terkirim</span><br>
                                            @else
                                                <span class="rounded-0 badge badge-danger">Pending</span><br>
                                            @endif
                                        @endif

                                    @endforeach
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>
        </div>

        <form method="post" action="{{route('weekly.export')}}">
            @csrf
            <input name="filename" type="hidden" value="export-fulfillment.xls">
            <textarea name="html" style="display: none" id="html-export-fulfillment"></textarea>
            <button type="submit" id="" class="btn btn-blue">Export</button>
        </form>

        <script>
            $(document).ready(function(){
                var html  = $('#export-fulfillment').html();
                $('#html-export-fulfillment').val(html);
            })
        </script>

    </div>
</section>

{{-- <section class="panel">
    <div class="card-body">
        <div class="row">
            <div class="col-6">
                <label> Ekspedisi</label>
                <table class="table default-table">
                    <thead>
                        <tr>
                            <th  class="text">Total Berat</th>
                            <th  class="text">Total Qty</th>
                            <th  class="text">Total Customer</th>
                            <th  class="text">Total Mobil</th>
                            <th  class="text">Rata rata / Mobil</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ekspedisi as $eks)

                            <tr>
                                <td  class="text">{{ number_format($eks->totalberat, 2) }}</td>
                                <td  class="text">{{ number_format($eks->totalitem, 0) }}</td>
                                <td  class="text">{{ $total['countcustomer'] }} </td>
                                <td  class="text">{{ number_format($eks->count, 0) }}</td>
                                <td  class="text">{{ number_format($total['rataekspedisi'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-6">
                <label> Gudang Masuk</label>
                @foreach ($gudang as $gud)
                    <div class="row">
                        <div class="col-6">
                            <div class="small"><b>Total QTY</b></div>
                            {{ number_format($gud->totalitem, 0) ?? '0' }}
                        </div>
                        <div class="ccol-6">
                            <div class="small"><b>Total Berat</b></div>
                            {{ number_format($gud->totalberat, 2) ?? '0' }}
                        </div>
                    </div>
                @endforeach
                <label> Gudang Bahan Baku</label>
                @foreach ($gudangbb as $gud)
                    <div class="row">
                        <div class="col-6">
                            <div class="small"><b>Total QTY</b></div>
                            {{ number_format($gud->totalitem, 0) ?? '0' }}
                        </div>
                        <div class="ccol-6">
                            <div class="small"><b>Total Berat</b></div>
                            {{ number_format($gud->totalberat, 2) ?? '0' }}
                        </div>
                    </div>
                @endforeach
                <label> Gudang Transaksi</label>
                <table class="table default-table">
                    <thead>
                        <tr>
                            <th  class="text">No</th>
                            <th  class="text">Item</th>
                            <th  class="text">QTY</th>
                            <th  class="text">Berat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($gudangkeluar as $i => $keluar)
                            <tr>
                                <td  class="text">{{ ++$i }}</td>
                                <td  class="text">{{ $keluar->nama_detail }}</td>
                                <td  class="text">{{ number_format($keluar->totalitem, 0) }}</td>
                                <td  class="text">{{ number_format($keluar->totalberat, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section> --}}
