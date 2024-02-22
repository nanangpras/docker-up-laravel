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
@isset($NONSORetur)
    @foreach ($NONSORetur as $non)
        <div class="card mb-2 non_so">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <div class="small">Tanggal Retur</div>
                        {{ $non['tanggal_retur'] }}
                        <br>
                        @if($non['status'] == '4' || $non['status'] == '5')
                        <span class="status status-info mt-3">NON INTEGRASI</span>
                        @endif
                    </div>
                    <div class="col">
                        <div class="small">Tanggal Input</div>
                        {{ $non['created_at'] }}
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <div class="small">Nama Customer</div>
                            {{ $non['nama_customer'] }}
                        </div>
                    </div>
                    <div class="col">
                        <div class="small">No SO</div>
                        <span class="status status-danger">NON SO</span>
                    </div>
                    <div class="col">
                        <div class="small">No RA</div>
                        @php
                            $ns = \App\Models\Netsuite::where('tabel_id',$non['id'])->where('label','receipt_return')->where('tabel', 'retur')->first();
                            if($ns){

                                try {
                                    //code...
                                    $resp = json_decode($ns->response, TRUE);
                                    echo "<span class='status status-info'>".$resp[0]['message']."</span>";
                                } catch (\Throwable $th) {
                                    //throw $th;
                                    // echo $th->getMessage();
                                }
                            }

                        @endphp
                        @if ($ns)
                            @if (!empty($ns->failed) && $ns->document_no =="")
                                <div class="status status-danger">
                                    @php
                                        //code...
                                        $resp = json_decode($ns->failed);
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
                                $no    = 1;
                            @endphp
                            @foreach ($ReturItemNONSO as $k => $baris)
                                @if($non['id'] == $baris->retur_id)
                                    @php
                                        $total += $baris->qty;
                                        $berat += $baris->berat;
                                        $cekLog = \App\Models\ Adminedit::where('table_name', 'retur_item')->where('type','retur')->where('activity', 'Retur Salah Item/Tidak Sesuai Pesanan')->where('table_id', $baris->id)->first();
                                        // $retur_item = \App\Models\ReturItem::where('orderitem_id', $baris->id)->first();
                                    @endphp
                                    <tr @if($baris->catatan == 'Salah Item' || $baris->catatan == 'Barang Tidak Sesuai Pesanan/Order')  style="background-color: #FFFF8F" @endif>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $baris->to_item->nama ?? '' }} @if($baris->grade_item) <span class="text-primary pl-2 font-weight-bold uppercase"> // Grade B </span> @endif
                                            @if($baris->catatan == 'Salah Item' || $baris->catatan == 'Barang Tidak Sesuai Pesanan/Order')
                                            <div class="row mt-1">
                                                <div class="col-auto"><span class="text-info"><b>@if($cekLog) *ITEM DITUKAR DARI: <br> {{ $cekLog->data }}  @else *ITEM TIDAK DITUKAR @endif</b></span></div>
                                            </div>
                                            @endif
                                        </td>
                                        <td>{{ $baris->unit }}</td>
                                        <td>{{ $baris->penanganan }}</td>
                                        <td>{{ $baris->qty }}</td>
                                        <td>{{ $baris->berat }}</td>
                                        <td>{{ $baris->catatan }}</td>
                                        <td>{{ $baris->kategori }}</td>
                                        <td>{{ $baris->satuan }}</td>
                                        <td>{{ $baris->todriver->nama ?? '' }}</td>
                                        <th>
                                            @if ($baris->status == 1)
                                                <span class="status status-danger">Belum Selesai</span>
                                            @else
                                                <span class="status status-success">Selesai</span>
                                            @endif
                                        </th>
                                    </tr>
                                    @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($non['status']=="3")
                    <div class="status status-danger">RETUR TELAH DIHAPUS || {{ $baris->updated_at }}</div>
                @else
                <form action="{{ route('retur.destroy') }}" method="post" id="submitBatalRetur{{ $non['id'] }}">
                    @csrf @method('delete') <input type="hidden" name="id" value="{{ $non['id'] }}">
                    <button type="submit" class="btn btn-danger float-right" onclick="batalRetur(event, `{{$non['id'] }}`)">Batal</button>
                </form>
                @endif
                <a href="{{ url('admin/retur/detail', $non['id']) }}" class="btn btn-blue mt-2">Detail</a>
            </div>
        </div>
    @endforeach
@endisset

@foreach ($returnya as $r)
    <div class="card mb-2">
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <div class="small">Tanggal Retur</div>
                    {{ $r['tanggal_retur'] }}
                </div>
                <div class="col">
                    <div class="small">Tanggal Input</div>
                    {{ $r['created_at'] }}
                </div>
                <div class="col">
                    <div class="form-group">
                        <div class="small">Nama Customer</div>
                        {{ $r['nama_customer'] }}
                    </div>
                </div>
                <div class="col">
                    <div class="small">Doc Number</div>
                    <span class="status status-success mb-1">{{ $r['doc_no_so'] }}</span>
                    <span class="status status-warning">{{ $r['doc_no_do'] }}</span>
                </div>
                <div class="col">
                    <div class="small">No RA</div>
                    {!! $r['statusresponsuccess'] !!}
                    {!! $r['statusresponfailed'] !!}
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
                            $no    = 1;
                        @endphp
                        @foreach ($ReturItem as $i => $row)
                            @if($r['id'] == $row->retur_id)
                                @php
                                    $total += $row->qty;
                                    $berat += $row->berat;
                                    $cekLog = \App\Models\ Adminedit::where('table_name', 'retur_item')->where('type','retur')->where('activity', 'Retur Salah Item/Tidak Sesuai Pesanan')->where('table_id', $row->id)->first();
                                    // $retur_item = \App\Models\ReturItem::where('orderitem_id', $row->id)->first();
                                @endphp
                                <tr @if($row->catatan == 'Salah Item' || $row->catatan == 'Barang Tidak Sesuai Pesanan/Order')  style="background-color: #FFFF8F" @endif>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $row->to_item->nama ?? '' }} @if($row->grade_item) <span class="text-primary pl-2 font-weight-bold uppercase"> // Grade B </span> @endif
                                        @if($row->catatan == 'Salah Item' || $row->catatan == 'Barang Tidak Sesuai Pesanan/Order')
                                        <div class="row mt-1">
                                            <div class="col-auto"><span class="text-info"><b>@if($cekLog) *ITEM DITUKAR DARI: <br> {{ $cekLog->data }}  @else *ITEM TIDAK DITUKAR @endif</b></span></div>
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
                                @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($r['status']=="3")
                <div class="status status-danger">RETUR TELAH DIHAPUS || {{ $row->updated_at }}</div>
            @else
            <form action="{{ route('retur.destroy') }}" method="post" id="submitBatalRetur{{ $r['id'] }}">
                @csrf @method('delete') <input type="hidden" name="id" value="{{ $r['id'] }}">
                <button type="submit" class="btn btn-danger float-right" onclick="batalRetur(event, `{{$r['id'] }}`)">Batal</button>
            </form>
            @endif
            <a href="{{ url('admin/retur/detail', $r['id']) }}" class="btn btn-blue mt-2">Detail</a>
        </div>
    </div>
@endforeach

<div id="paginateSummaryRetur" class="mt-1">
{{ $returnya->appends($_GET)->onEachSide(1)->links() }}
</div>

<style>
    .non_so{
        border: 5px solid lightblue;
    }
</style>
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
