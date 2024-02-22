<div class="table-responsive mt-4">
    <table width="100%" class="table default-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>No. LPAH / DO</th>
                <th>Supir</th>
                <th>PO</th>
                <th>Jam Masuk</th>
                <th>Operator</th>
                <th>DO Ekor/Berat</th>
                <th>Ekor/Berat</th>
                <th>NoUrut</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
                <tr>
                    <td>{{ $row->prod_tanggal_potong }}</td>
                    <td>{{ $row->prodpur->purcsupp->nama ?? '####' }}<br>{{ $row->no_lpah }}<br>NoDO :
                        {{ $row->no_do }}<br>
                        {{ $row->prodpur->no_po ?? '####' }}
                        @if($row->prodpur->tanggal_potong!=$row->prod_tanggal_potong)
                            <br><span class="status status-info">MOBIL LAMA</span>
                        @endif
                            </td>
                    <td>{{ $row->sc_pengemudi }}<br>{{ $row->sc_no_polisi }}</td>
                    <td>@if ($row->prodpur->ukuran_ayam == '&lt; 1.1') {{ '<1.1' }} @else {{ $row->prodpur->ukuran_ayam }} @endif<br><span
                            class="text-capitalize">{{ $row->po_jenis_ekspedisi }}</span> <br>
                        {{ $row->prodpur->type_po }}</td>
                    <td>{{ date('d/m/y', strtotime($row->sc_tanggal_masuk ?? '')) }}
                        <br>{{ date('H:i', strtotime($row->sc_jam_masuk ?? '00:00')) }} WIB</td>
                    <td>{{ $row->lpah_user_nama }}</td>
                    <td>{{ number_format($row->sc_ekor_do) }}
                        ekor<br>{{ number_format($row->sc_berat_do, 2) }} Kg <br> Rata :
                        {{ $row->sc_rerata_do }} Kg</td>
                    <td>{{ number_format($row->ekoran_seckle) }} ekor <br>
                        {{ number_format($row->lpah_berat_terima, 2) }} Kg <br> Rata :
                        @if ($row->ekoran_seckle > 0) {{ number_format($row->lpah_berat_terima / ($row->ekoran_seckle ?? '1'), 2) }} Kg @endif</td>
                    <td>{{ $row->no_urut }}</td>
                    <td>
                        @if ($row->sc_status == '0')
                            <span class="status status-danger">Dibatalkan</span>
                        @else
                            @if ($row->lpah_status == 1)
                                <span class="status status-success">Selesai</span>
                                @if ($row->lpah_netsuite_status == 1)
                                    <br><span class="status status-danger">NSTerkirim</span>
                                @endif
                            @elseif($row->lpah_status==2)
                                <span class="status status-other">Proses</span>
                            @elseif($row->lpah_status==3)
                                <span class="status status-warning">Checker</span>
                            @else
                                <span class="status status-info">Pending</span>
                            @endif

                            @if($row->prod_pending=="1")
                            <br><span class="status status-danger">POTunda</span>
                            @endif

                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if (count($mobil_lama) > 0)
    <hr>
    <h6>Mobil Lama</h6>
    <div class="table-responsive mt-4">
        <table width="100%" class="table default-table" id="lpahTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No. LPAH / DO</th>
                    <th>Supir</th>
                    <th>Kandang</th>
                    <th>Jam Masuk</th>
                    <th>DO Ekor/Berat</th>
                    <th>Ekor/Berat</th>
                    <th>NoUrut</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($mobil_lama as $i => $row)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $row->prodpur->pursupp->nama ?? '####' }} <br> {{ $row->no_lpah }}<br>DO :
                            {{ $row->no_do }}</td>
                        <td>{{ $row->sc_pengemudi }}<br>{{ $row->sc_no_polisi }}</td>
                        <td>{{ $row->sc_nama_kandang ?? '####' }}<br>{{ $row->prodpur->ukuran_ayam }}<br><span
                                class="text-capitalize">{{ $row->po_jenis_ekspedisi }}</span> <br>
                            {{ $row->prodpur->type_po }}</td>
                        <td>{{ date('d/m/y', strtotime($row->sc_tanggal_masuk ?? '')) }}
                            <br>{{ date('H:i', strtotime($row->sc_jam_masuk ?? '00:00')) }} WIB</td>
                        <td>{{ number_format($row->sc_ekor_do) }}
                            ekor<br>{{ number_format($row->sc_berat_do, 2) }} Kg <br> Rata :
                            {{ $row->sc_rerata_do }} Kg</td>
                        <td>{{ number_format($row->ekoran_seckle) }} ekor <br>
                            {{ number_format($row->lpah_berat_terima, 2) }} Kg <br> Rata :
                            {{ number_format($row->lpah_berat_terima / ($row->ekoran_seckle ?? '1'), 2) }} Kg
                        </td>
                        <td>{{ $row->no_urut }}</td>
                        <td>
                            @if ($row->lpah_status == 1)
                                <span class="status status-success">Selesai</span>
                            @elseif($row->lpah_status==2)
                                <span class="status status-info">Proses</span>
                            @elseif($row->lpah_status==3)
                                <span class="status status-warning">Checkker</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
<hr>


<div class="row mt-3">
    <div class="col-lg-2 col-6">
        <label for="selesai">Selesai</label>
        <input class="form-control form-control-lg text-right bg-white" id="selesai" readonly
            value="{{ number_format($hitung['done']) }}">
    </div>
    <div class="col-lg-2 col-6">
        <label for="pending">Pending</label>
        <input class="form-control form-control-lg text-right bg-white" id="pending" readonly
            value="{{ number_format($hitung['pending']) }}">
    </div>
    <div class="col-lg-2 col-6">
        <label for="berat_total">Berat Total</label>
        <input class="form-control form-control-lg text-right bg-white" id="berat_total" readonly
            value="{{ number_format($hitung['berat_total'], 2) }}">
    </div>
    <div class="col-lg-2 col-6">
        <label for="total_ekor">Total Ekor</label>
        <input class="form-control form-control-lg text-right bg-white" id="total_ekor"readonly
            value="{{ number_format($hitung['total_ekor']) }}">
    </div>
</div>