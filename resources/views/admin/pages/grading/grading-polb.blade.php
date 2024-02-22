<a href="{{ route('grading.index', ['key' => 'unduhdata']) }}&tanggalawal={{ $tanggalawal }}&tanggalakhir={{ $tanggalakhir }}" class="btn btn-sm btn-success">Unduh Data</a>
<div class="table-responsive">

    <table width="100%" id="gradingLB" class="table default-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Supplier</th>
                <th>No LPAH</th>
                <th>No Mobil</th>
                <th>Operator</th>
                <th>Ukuran Ayam</th>
                <th>Ekor/Pcs/Pack</th>
                <th>Berat</th>
                <th>Rerata</th>
                <th>Normal (%)</th>
                <th>Memar (%)</th>
                <th>Keterangan Yield</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $countTotal     = 0;
                $totalBerat     = 0;
                $totalEkor      = 0;
            @endphp
            @foreach ($grading as $i => $row)
            @php 
                $cekLpahStatus = \App\Models\Production::cekLpahStatus($row->id,$row->sc_tanggal_masuk);
            @endphp
            @if($cekLpahStatus == 'OK' || $cekLpahStatus != '2')
                @php
                    $ekor           = 0;
                    $berat          = 0;
                    $countTotal     = $countTotal + 1;
                @endphp
                <tr>
                    <td>{{ $row->lpah_tanggal_potong }}</td>
                    <td>{{ $row->prodpur->purcsupp->nama }} <br>{{$row->prodpur->no_po}}
                        @if($row->prodpur->tanggal_potong!=$row->prod_tanggal_potong)
                            <br><span class="status status-info">MOBIL LAMA</span>
                        @endif
                    </td>
                    <td>{{ $row->no_lpah }}</td>
                    <td>{{ $row->no_urut }}</td>
                    <td>{{ $row->grading_user_nama }}</td>
                    <td>{{ $row->prodpur->ukuran_ayam }}</td>
                    <td>
                        @foreach ($row->prodgrad as $grad)
                            @if ($grad->keranjang == '0')
                                @php
                                    $ekor += $grad->total_item;
                                @endphp
                            @endif
                        @endforeach
                        {{ number_format($ekor) }} ekor
                    </td>
                    <td>
                        @foreach ($row->prodgrad as $grad)
                            @if ($grad->keranjang == '0')
                                @php
                                    $berat += $grad->berat_item;
                                @endphp
                            @endif
                        @endforeach
                        {{ number_format($berat, 2) }} Kg
                    </td>
                    <td>{{ $ekor ? number_format($berat / $ekor, 2) : 0 }} Kg</td>
                    <td>{{ App\Models\Grading::ProsentaseGradingNormal($row->id,$row->grading_status) }} %</td>
                    <td>{{ App\Models\Grading::ProsentaseGradingMemar($row->id,$row->grading_status) }} %</td>
                    <td>{{ $row->keterangan_benchmark ?? '-' }}</td>
                    <td>
                        @if($row->grading_status==1)
                            <span class="status status-success">Selesai</span>
                            @if($row->wo_netsuite_status==1)
                                <br><span class="status status-danger">NSTerkirim</span>
                            @endif
                        @elseif($row->grading_status==2)
                            <span class="status status-other">Proses</span>
                        @elseif($row->grading_status==3)
                            <span class="status status-warning">Checker</span>
                        @else
                            <span class="status status-info">Pending</span>
                        @endif

                        @if($row->prod_pending=="1")
                        <br><span class="status status-danger">POTunda</span>
                        @endif

                    </td>
                    <td>

                        @if ($row->grading_status == null)
                            <form action="{{ route('grading.store') }}" method="POST">
                                @csrf <input type="hidden" name="x_code" value="{{ $row->id }}">
                                <button class="btn btn-sm btn-primary btn-rounded">Proses</button>
                            </form>
                        @endif

                        @if ($row->grading_status == 1)
                            <a href="{{ route('grading.show', $row->id) }}" class="btn btn-sm btn-warning btn-rounded">Detail</a>
                            {{-- <a href="{{ route('grading.show', $row->id) }}?key=receiptulang" class="btn btn-sm btn-success btn-rounded">Receipt Ulang</a> --}}
                        @endif

                        @if ($row->grading_status == 2)
                            <div style="display:inline-flex">
                                <a href="{{ route('grading.show', $row->id) }}" class="btn btn-sm btn-success btn-rounded">Edit</a> &nbsp;
                                <form action="{{ route('grading.update', $row->id) }}" method="POST">
                                    @csrf @method('patch')
                                    <button type="submit" class="btn btn-sm btn-danger btn-rounded">Simpan</button>
                                </form>
                            </div>
                        @endif

                        @if ($row->grading_status == 3)
                            <a href="{{ route('grading.show', $row->id) }}" class="btn btn-sm btn-warning btn-rounded mr-2 float-left">Detail</a>

                            <form action="{{ route('grading.update', $row->id) }}" method="POST">
                                @csrf @method('patch') <input type="hidden" name="key" value="send">
                                <button type="submit" class="btn btn-sm btn-dark btn-rounded float-left">Selesaikan</button>
                            </form>

                        @endif


                        @if ($row->grading_status != null)
                            @if (User::setIjin(33))
                                <a href="{{ route('checker.produksi', $row->id) }}" class="btn btn-sm btn-info btn-rounded">NS Checker</a>
                            @endif
                        @endif

                    </td>
                </tr>
                @php
                    $totalBerat     += $berat;
                    $totalEkor      += $ekor;
                @endphp
            @endif
            @endforeach
        </tbody>
    </table>

</div>

<div class="form-group row mt-2">

    <div class="col-sm-2 col-4 pr-1">
        <div class="form-group">
            <label for="countTotal">Jumlah</label>
            <div class="input-group">
                <input type="text" style=" text-align: right;"
                    value="{{ number_format($countTotal) }}"
                    id="countTotal"
                    class="form-control form-control-lg text-right bg-white" readonly>
            </div>
        </div>
    </div>
    <div class="col-sm-2 col-4 px-1">
        <div class="form-group">
            <label for="berat">Berat</label>
            <div class="input-group">
                <input type="text" style="text-align: right;"
                    value="{{ number_format($totalBerat, 2) }}"
                    id="berat"
                    class="form-control form-control-lg text-right bg-white" readonly>
            </div>
        </div>
    </div>
    <div class="col-sm-2 col-4 pl-1">
        <div class="form-group">
            <label for="ekor_pcs">Ekor/Pcs/Pack</label>
            <div class="input-group">
                <input type="text" style="text-align: right;"
                    value="{{ number_format($totalEkor) }}"
                    id="ekor_pcs"
                    class="form-control form-control-lg text-right bg-white" readonly>
            </div>
        </div>
    </div>
    <div class="col-md-2 d-none d-md-block">
        <div class="form-group">
            <!-- <label></label> -->
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <div class="d-none d-sm-block mt-2">&nbsp;</div>
        </div>
    </div>
</div>

<script>
    $('#gradingLB').DataTable({
        "bInfo": false,
        responsive: true,
        scrollY:        500,
        scrollX:        true,
        scrollCollapse: true,
        paging:         false,
    });

    $('a[data-toggle="pill"]').on('shown.bs.tab', function(e){
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });
</script>