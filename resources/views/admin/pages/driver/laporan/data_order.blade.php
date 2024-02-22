<div class="col-md-12">
    <figure class="highcharts-figure">
        <div id="container-bonus"></div>
    </figure>
</div>

<table class="table default-table">
    <tbody>
        <tr>
            <th style="width: 140px">Nama Driver</th>
            <td style="text-align: left">{{ $driver->nama }}</td>
        </tr>
        @if ($driver->kernek)
        <tr>
            <th>Nama Kernek</th>
            <td style="text-align: left">{{ $driver->kernek }}</td>
        </tr>
        @endif
        @if($driver->no_polisi)
        <tr>
            <th>Nomor Polisi</th>
            <td style="text-align: left">{{ $driver->no_polisi }}</td>
        </tr>
        @endif
    </tbody>
</table>
<div class="table-responsive">
    <table class="table default-table">
        <thead>
            <tr>
                <th class="text">No</th>
                <th class="text">Tanggal</th>
                <th class="text">Supir</th>
                <th class="text">Nama Kandang</th>
                <th class="text">Wilayah</th>
                <th class="text">Ekor DO</th>
                <th class="text">Berat DO</th>
                <th class="text">% Susut</th>
                <th class="text">% Toleransi</th>
                <th class="text">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($delivery as $no => $row)
            @php
                $toleransi  =   App\Models\Target::where('alamat', 'like', '%' . preg_replace('/\s+/', '', $row->sc_wilayah) . '%')->orderBy('id', 'DESC')->first()->target ?? 0 ;
            @endphp
            <tr>
                <td class="text">{{ ++$no }}</td>
                <td class="text">{{ $row->sc_tanggal_masuk }}</td>
                <td class="text">{{ $row->sc_pengemudi }}</td>
                <td class="text">{{ $row->sc_nama_kandang }}</td>
                <td class="text">{{ $row->sc_wilayah }}</td>
                <td class="text">{{ number_format($row->sc_ekor_do) }}</td>
                <td class="text">{{ number_format($row->sc_berat_do, 2) }}</td>
                <td class="text">{{ number_format($row->lpah_persen_susut, 2) }}</td>
                <td class="text">{{ number_format($row->lpah_persen_susut ? $toleransi : 0, 2) }}</td>
                <td class="text">
                    @if ($row->lpah_persen_susut && $toleransi)
                        @if ($toleransi >= $row->lpah_persen_susut)
                            <span class='text-success'>IN</span>
                        @else
                            <span class='text-danger'>OUT</span>
                        @endif
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>


{{ $delivery->appends($_GET)->onEachSide(1)->links() }}

<script>
    $('.pagination a').on('click', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        console.log(url);
        $.ajax({
            url: url,
            method: "GET",
            success: function(response) {
                $('#summarypengiriman').html(response);
            }
        });
    });

</script>

<script>
    Highcharts.chart('container-bonus', {
        chart: {
            type: 'line'
        },
        title: {
            text: 'Batas Toleransi'
        },
        subtitle: {
            text: 'Data bedasarkan supir'
        },
        xAxis: {
            categories: {!! $datachart !!}
        },
        yAxis: {
            title: {
                text: 'Jumlah'
            }
        },
        plotOptions: {
            line: {
                dataLabels: {
                    enabled: true
                },
                enableMouseTracking: true
            }
        },
        series: {!! $alokasi !!}
    });
</script>
