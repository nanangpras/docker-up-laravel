<section class="panel" id="panelretur" >
    <div class="card-body">
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <div class="bg-info p-2 text-center text-light font-weight-bold text-uppercase">Total QTY</div>
                    <div class="border p-2 text-center">
                        <h5 class="mb-0">{{ $totalReturQty }}</h5>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="form-group">
                    <div class="bg-warning p-2 text-center text-light font-weight-bold text-uppercase">Total Berat</div>
                    <div class="border p-2 text-center">
                        <h5 class="mb-0">{{ $totalReturBerat }}</h5>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <div class="bg-danger p-2 text-center text-light font-weight-bold text-uppercase">Total Qty FulFillment</div>
                    <div class="border p-2 text-center">
                        <h5 class="mb-0">{{ $totalAllQtyFulFill }}</h5>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <div class="bg-success p-2 text-center text-light font-weight-bold text-uppercase">Persentase Retur</div>
                    <div class="border p-2 text-center">
                        <h5 class="mb-0">{{ $totalqtypercentage }} %</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@foreach ($retur as $r)

    @if ($r->id_so != '')
        <div class="card mb-2">
            <div class="card-body">

                <div class="row">
                    <div class="col">
                        <div class="small">Tanggal Retur</div>
                        {{ $r->tanggal_retur }}
                    </div>

                    <div class="col">
                        <div class="small">Tanggal Input</div>
                        {{ $r->created_at }}
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <div class="small">Nama Customer</div>
                            {{ $r->to_customer->nama ?? '' }}
                        </div>
                    </div>
                    <div class="col">
                        <div class="small">Doc Number</div>
                        <span class="status status-success mb-1">{{ $r->to_order->no_so }}</span>
                        <span class="status status-warning">{{ $r->to_order->no_do }}</span>
                    </div>
                    <div class="col">
                        <div class="small">No RA</div>
                        @php
                            if($r->to_netsuite){

                                try {
                                    //code...
                                    $resp = json_decode($r->to_netsuite->response, TRUE);
                                    echo "<span class='status status-info'>".$resp[0]['message']."</span>";
                                } catch (\Throwable $th) {
                                    //throw $th;
                                    // echo $th->getMessage();
                                }
                            }
                        @endphp
                        @if ($r->to_netsuite)
                        @if (!empty($r->to_netsuite->failed) && $r->to_netsuite->document_no =="")
                            <div class="status status-danger">
                                @php
                                    //code...
                                    $resp = json_decode($r->to_netsuite->failed);
                                @endphp

                                RA Gagal : {{ $resp[0]->message->message ?? '' }}
                            </div>
                        @endif
                    @endif
                    </div>
                </div>
                <div class="table-responsive">

                <table class="table default-table">
                    <thead>
                        <tr>
                            <th width=10px>No</th>
                            <th>Nama Item</th>
                            <th>Tujuan</th>
                            <th>Penanganan</th>
                            <th>Retur Qty</th>
                            <th>Retur Berat</th>
                            <th>Alasan</th>
                            <th>Kategori</th>
                            <th>Satuan</th>
                            <th>Sopir</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total = 0;
                            $berat = 0;
                        @endphp
                        @foreach ($r->to_itemretur as $i => $row)
                            @php
                                $total += $row->qty;
                                $berat += $row->berat;
                            @endphp
                            <tr @if($row->catatan == 'Salah Item' || $row->catatan == 'Barang Tidak Sesuai Pesanan/Order')  style="background-color: #FFFF8F" @endif>
                                <td>{{ ++$i }}</td>
                                <td>{{ $row->to_item->nama ?? '' }} @if($row->grade_item) <span class="text-primary pl-2 font-weight-bold uppercase"> // Grade B </span> @endif
                                    @if($row->catatan == 'Salah Item' || $row->catatan == 'Barang Tidak Sesuai Pesanan/Order') 
                                    <div class="row mt-1">
                                        <div class="col-auto"><span class="text-info"><b>@if($r->getItemTukarRetur) *ITEM DITUKAR DARI: <br> {{ $r->getItemTukarRetur->data }}  @else *ITEM TIDAK DITUKAR @endif</b></span></div>
                                    </div>
                                    @endif
                                </td>
                                <td>{{ $row->unit }}</td>
                                <td>{{ $row->penanganan }}</td>
                                <td>{{ $row->qty }}</td>
                                <td>{{ $row->berat }}</td>
                                <td>{{ $row->catatan }}</td>
                                <td>{{ $row->kategori }}</td>
                                <td>{{ $row->satuan }}</td>
                                <td>{{ $row->todriver->nama ?? '' }}</td>
                                <th>
                                    @if ($row->status == 1)
                                        <span class="status status-danger">Belum Selesai</span>
                                    @else
                                        <span class="status status-success">Selesai</span>
                                    @endif
                                </th>

                            </tr>
                        @endforeach
                    </tbody>
                </table>

                </div>

                @if($r->status=="3")
                    <div class="status status-danger">RETUR TELAH DIHAPUS || {{$row->updated_at}}</div>

                @else
                <form action="{{ route('retur.destroy') }}" method="post" id="submitBatalRetur{{ $r->id }}">
                    @csrf @method('delete') <input type="hidden" name="id" value="{{ $r->id }}">
                    <button type="submit" class="btn btn-danger float-right" onclick="batalRetur(event, {{ $r->id }})">Batal</button>
                </form>

                @endif
                <a href="{{ url('admin/retur/detail', $r->id) }}" class="btn btn-blue mt-2">Detail</a>

            </div>
        </div>
    @else


    <div class="card mb-2">
        <div class="card-body">

            <div class="row">
                <div class="col">
                    <div class="small">Tanggal Retur</div>
                    {{ $r->tanggal_retur }}
                    <br>
                    @if($r->status == '4' || $r->status == '5')
                    <span class="status status-info mt-3">NON INTEGRASI</span>
                    @endif
                </div>

                <div class="col">
                    <div class="small">Tanggal Input</div>
                    {{ $r->created_at }}
                </div>
                <div class="col">
                    <div class="form-group">
                        <div class="small">Nama Customer</div>
                        <td>{{ $r->to_customer->nama ?? '' }}</td>
                    </div>
                </div>
                <div class="col">
                    <div class="small">No SO</div>
                    <span class="status status-danger">NON SO</span>
                </div>
                <div class="col">
                    <div class="small">NO RA</div>
                        @php
                            if($r->to_netsuite){

                                try {
                                    //code...
                                    $resp = json_decode($r->to_netsuite->response, TRUE);
                                    echo "<span class='status status-info'>".$resp[0]['message']."</span>";
                                } catch (\Throwable $th) {
                                    //throw $th;
                                    // echo $th->getMessage();
                                }
                            }

                        @endphp
                        @if ($r->to_netsuite)
                            @if (!empty($r->to_netsuite->failed) && $r->to_netsuite->document_no =="")
                                <div class="status status-danger">
                                    @php
                                        //code...
                                        $resp = json_decode($r->to_netsuite->failed);
                                    @endphp

                                    RA Gagal : {{ $resp[0]->message->message ?? '' }}
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <div class="card-body">
            <div class="table-responsive">

            <table class="table default-table">
                <thead>
                    <tr>
                        <th width=10px>No</th>
                        <th>Customer</th>
                        <th>Tujuan</th>
                        <th>Penanganan</th>
                        <th>Retur Qty</th>
                        <th>Retur Berat</th>
                        <th>Alasan</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th>Sopir</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($r->to_itemretur as $i => $row)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $row->to_item->nama ?? '' }}</td>
                            <td>{{ $row->unit ?? '' }}</td>
                            <td>{{ $row->penanganan ?? '' }}</td>
                            <td>{{ $row->qty ?? '' }}</td>
                            <td>{{ $row->berat ?? '' }}</td>
                            <td>{{ $row->catatan }}</td>
                            <td>{{ $row->kategori }}</td>
                            <td>{{ $row->satuan }}</td>
                            <td>{{ $row->todriver->nama ?? '' }}</td>
                            <th>
                                @if ($row->status == 1)
                                    <span class="status status-danger">Belum Selesai</span>
                                @else
                                    <span class="status status-success">Selesai</span>
                                @endif
                            </th>

                        </tr>
                    @endforeach
                </tbody>
            </table>

            </div>

            @if($r->status=="3")
                    <div class="status status-danger">RETUR TELAH DIHAPUS || {{$row->updated_at}}</div>
            @else
                <form action="{{ route('retur.destroy') }}" method="post" id="submitBatalRetur{{ $r->id }}">
                    @csrf @method('delete') <input type="hidden" name="id" value="{{ $r->id }}">
                    <button type="submit" class="btn btn-danger float-right" onsubmit="batalRetur(event, {{ $r->id }})">Batal</button>
                </form>
                <a href="{{ url('admin/retur/detail', $r->id) }}" class="btn btn-blue">Detail</a>
            @endif
            </div>
        </div>
    </div>



    @endif

@endforeach

<div id="paginateSummaryRetur" class="mt-1">
{{ $retur->appends($_GET)->onEachSide(1)->links() }}
</div>

<script>

$('#paginateSummaryRetur .pagination a').on('click', function(e) {
    e.preventDefault();
    $('#text-notif').html('Menunggu...');
    $('#topbar-notification').fadeIn();

    url = $(this).attr('href');
    $.ajax({
        url: url,
        method: "GET",
        success: function(response) {
            $('#retur-summary').html(response).after($('#topbar-notification').fadeOut());
        }

    });
});
    


batalRetur = (e, id) => {
    e.preventDefault();
    var result = confirm("Yakin ingin batalkan retur?");
    if (result) {

        const cekValidasi = fetch("{{ route('retur.destroy') }}", {
            headers: {
                'Content-Type': 'application/json',
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            method: "DELETE",
            credentials: "same-origin",
            body: JSON.stringify({
                key: "cekAlurData",
                id: id,
            }),
        }).then((response) => {
            if(response.ok){
                return response.json();
            }
        }).then((result) => {
            if (result.status == 200) {
                document.getElementById('submitBatalRetur' + id).submit();
            } else { 
                showAlert(result.message)
                return false;
            }
        })
    } else {
        e.preventDefault()
        return false;
    }
}
</script>
