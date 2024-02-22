@php
$total = 0;
$jumlah_susut = 0;
$total_susut = 0;
@endphp
@foreach ($kandang as $row)
    @if ($row->susut)
        @php
            $total +=
                App\Models\Target::where('alamat', 'like', '%' . $row->sc_wilayah . '%')
                    ->orderBy('id', 'DESC')
                    ->first()->target ?? 0;
            $jumlah_susut = $jumlah_susut + 1;
            $total_susut = $total_susut + $row->susut;
        @endphp
    @endif
@endforeach
@php
$hitung = $total ? $total / $jumlah_susut : 0;
$hitung_susut = $total_susut ? $total_susut / $jumlah_susut : 0;
@endphp

<div class="row mt-4 mb-3">
    <div class="col pr-1">
        <div class="bg-primary text-light p-1 text-center">% Susut ({{ $total_susut }}/{{ $jumlah_susut }})</div>
        <div class="p-2 border text-center bg-light">
            <b id="show-susut">{{ number_format($hitung_susut, 2) }} %</b>
        </div>
    </div>
    <div class="col px-1">
        <div class="bg-primary text-light p-1 text-center">% Toleransi ({{ $total }}/{{ $jumlah_susut }})</div>
        <div class="p-2 border text-center text-light bg-info">

            <b id="show-toleransi">{{ $total > 0 && $jumlah_susut > 0 ? number_format($hitung, 2) : 0 }} %</b>
        </div>
    </div>
    <div class="col pl-1">
        <div class="bg-primary text-light p-1 text-center">Hasil</div>
        <div class="p-2 border text-center text-light {{ $hitung_susut > $hitung ? 'bg-danger' : 'bg-success' }}">
            <b id="show-hasil">{{ $hitung_susut > $hitung ? 'OUT' : 'IN' }}</b>
        </div>
    </div>
</div>
<div class="col-md-12">
    <figure class="highcharts-figure">
        <div id="container-bonus"></div>
    </figure>
</div>

<script>
    var tsusut      = "{{ number_format($hitung_susut, 2) }}"
    var ttoleransi  = "{{ $total > 0 && $jumlah_susut > 0 ? number_format($hitung, 2) : 0 }}"
    var thasil      = "{{ $hitung_susut > $hitung ? 'OUT' : 'IN' }}"

    $("#susut").text(tsusut);
    $("#toleransi").text(ttoleransi);
    $("#hasil").text(thasil);

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
