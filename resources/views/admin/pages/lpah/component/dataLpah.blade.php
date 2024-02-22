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
                <th>Keterangan Yield</th>
                <th>Status</th>
                <th>#</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
                @php 
                    $cekLpahStatus = \App\Models\Production::cekLpahStatus($row->id,$row->sc_tanggal_masuk);
                @endphp
                @if($cekLpahStatus == 'OK' || $cekLpahStatus != '2')
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
                        ekor<br>{{ number_format($row->sc_berat_do, 1) }} Kg <br> Rata :
                        {{ $row->sc_rerata_do }} Kg</td>
                    <td>{{ number_format($row->ekoran_seckle) }} ekor <br>
                        {{ number_format($row->lpah_berat_terima, 1) }} Kg <br> Rata :
                        @if ($row->ekoran_seckle > 0) {{ number_format($row->lpah_rerata_terima, 1) }} Kg @endif</td>
                    <td>{{ $row->no_urut }}</td>
                    <td>{{ $row->keterangan_benchmark ?? '-' }}</td>
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
                    <td>
                        @if ($row->sc_status != '0')
                            @if ($row->lpah_status == null)
                                <form action="{{ route('lpah.store') }}" method="POST">
                                    @csrf <input type="hidden" name="x_code" value="{{ $row->id }}">
                                    <button class="btn btn-sm btn-primary btn-rounded mb-1">Proses</button>
                                </form>
                            @endif
                            @if ($row->lpah_status == 1 || $row->lpah_status == 3)
                                <a href="{{ route('lpah.show', $row->id) }}"
                                    class="btn btn-sm btn-warning btn-rounded mb-1">Detail</a>
                            @endif
                            @if ($row->lpah_status == 2)

                                <div style="display:inline-flex">
                                    <a href="{{ route('lpah.show', $row->id) }}"
                                        class="btn btn-sm btn-success btn-rounded mb-1">Edit</a>
                                    &nbsp;

                                    <form action="{{ route('lpah.store', ['key' => 'simpan']) }}" method="POST">
                                        @csrf <input type="hidden" name="x_code" value="{{ $row->id }}">
                                        <button class="btn btn-sm btn-danger btn-rounded mb-1">Simpan</button>
                                    </form>
                                </div>

                            @endif

                            @if ($row->evis_status != null)
                                @if (User::setIjin(33))
                                    <a href="{{ route('checker.produksi', $row->id) }}"
                                        class="btn btn-sm btn-info btn-rounded mb-1">NS Checker</a>
                                @endif
                            @endif
                        @endif
                    </td>
                </tr>
                @endif
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
                    <th>#</th>
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
                        <td>
                            @if ($row->lpah_status == null)
                                <form action="{{ route('lpah.store') }}" method="POST">
                                    @csrf <input type="hidden" name="x_code" value="{{ $row->id }}">
                                    <button class="btn btn-sm btn-primary btn-rounded mb-1">Proses</button>
                                </form>
                            @endif
                            @if ($row->lpah_status == 1)
                                <a href="{{ route('lpah.show', $row->id) }}"
                                    class="btn btn-sm btn-warning btn-rounded mb-1">Detail</a>
                            @endif
                            @if ($row->lpah_status == 2)

                                <div style="display:inline-flex">
                                    <a href="{{ route('lpah.show', $row->id) }}"
                                        class="btn btn-sm btn-success btn-rounded mb-1">Edit</a>
                                    &nbsp;

                                    <form action="{{ route('lpah.store', ['key' => 'selesai']) }}"
                                        method="POST">
                                        @csrf <input type="hidden" name="x_code" value="{{ $row->id }}">
                                        <button class="btn btn-sm btn-danger btn-rounded mb-1">Selesaikan</button>
                                    </form>
                                </div>

                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif