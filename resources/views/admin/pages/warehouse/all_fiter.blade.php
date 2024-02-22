@php
function tgl_indo($tgl)
{
    $tanggal = substr($tgl, 8, 2);
    $bulan = getBulan(substr($tgl, 5, 2));
    $tahun = substr($tgl, 0, 4);
    return $tanggal . ' ' . $bulan . ' ' . $tahun;
}

function getBulan($bln)
{
    switch ($bln) {
        case 1:
            return 'Januari';
            break;
        case 2:
            return 'Februari';
            break;
        case 3:
            return 'Maret';
            break;
        case 4:
            return 'April';
            break;
        case 5:
            return 'Mei';
            break;
        case 6:
            return 'Juni';
            break;
        case 7:
            return 'Juli';
            break;
        case 8:
            return 'Agustus';
            break;
        case 9:
            return 'September';
            break;
        case 10:
            return 'Oktober';
            break;
        case 11:
            return 'November';
            break;
        case 12:
            return 'Desember';
            break;
    }
}
$date_1 = Carbon\Carbon::create($tahun, $bulan)
    ->startOfMonth()
    ->format('Y-m-d');
$date_2 = Carbon\Carbon::create($tahun, $bulan)
    ->lastOfMonth()
    ->format('Y-m-d');

$period = new DatePeriod(new DateTime($date_1), new DateInterval('P1D'), new DateTime($date_2 . '+1 day'));
@endphp
<div class="table-responsive">
    <table width="100%" class="table default-table">
        <thead>
            <tr id="main">
                <th rowspan="2">No</th>
                <th rowspan="2">ID</th>
                <th rowspan="2">Nama</th>
                <th rowspan="2">Parting</th>
                <th rowspan="2">Konsumen / Sub Item</th>
                <th rowspan="2">Plastik</th>
                <th rowspan="2">Lokasi</th>
                @foreach ($period as $date)
                    <th colspan="3">{{ tgl_indo($date->format('Y-m-d')) }}</th>
                @endforeach
            </tr>
            <tr id="sub">
                @foreach ($period as $date)
                    <th>Masuk</th>
                    <th>Keluar</th>
                    <th>Total</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($stock as $i => $val)
                @php
                    $p = Product_gudang::allFilter($date_1, 'hasil', $tahun, $bulan, $search, $gudang, $val->packaging ?? null, $val->productitems->id ?? null, $val->productgudang->id ?? null, $val->konsumen->nama ?? null, $val->sub_item ?? null, $val->customerid ?? null);
                @endphp
                <tr>
                    <td>{{ $loop->iteration + ($stock->currentpage() - 1) * $stock->perPage() }}</td>
                    <td>{{ $val->id ?? '#' }}</td>
                    <td>
                        {{ $val->productitems->nama ?? '#' }}
                        @if ($val->selonjor)
                            <div class="font-weight-bold text-danger">SELONJOR</div>
                        @endif
                    </td>
                    <td>{{ $val->parting }}</td>
                    <td>{{ $val->konsumen->nama ?? '' }}<br>@if ($val->sub_item) Keterangan : {{ $val->sub_item }} @endif
                    </td>
                    <td>{{ $val->plastik_group ?? 'Tidak Ada' }}</td>
                    <td>{{ $val->productgudang->code ?? '#' }}</td>
                    @foreach ($period as $date)
                        <td>{{ $datamasuk = Product_gudang::allFilter($date->format('Y-m-d'),'masuk',$tahun,$bulan,$search,$gudang,$val->packaging ?? null,$val->productitems->id ?? null,$val->productgudang->id ?? null,$val->konsumen->nama ?? null,$val->sub_item ?? null,$val->customerid ?? null) }}
                        </td>
                        <td>{{ $datakeluar = Product_gudang::allFilter($date->format('Y-m-d'),'keluar',$tahun,$bulan,$search,$gudang,$val->packaging ?? null,$val->productitems->id ?? null,$val->productgudang->id ?? null,$val->konsumen->nama ?? null,$val->sub_item ?? null,$val->customerid ?? null) }}
                        </td>
                        <td>{{ $p += $datamasuk - $datakeluar }}</td>
                    @endforeach
                </tr>
            @endforeach

        </tbody>
    </table>
</div>

<div id="paginate_stock">
    {{ $stock->appends($_GET)->onEachSide(1)->links() }}
</div>

<script>
    $('#paginate_stock .pagination a').on('click', function(e) {
        e.preventDefault();
        showNotif('Menunggu');

        url = $(this).attr('href');
        $.ajax({
            url: url,
            method: "GET",
            success: function(response) {
                $('#warehouse-all-filter').html(response);
            }
        });
    });
</script>
