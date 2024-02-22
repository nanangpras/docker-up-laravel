<div class="table-responsive mt-3">
    <table width="100%" id="gradingNonLB" class="table default-table">
        <thead>
            <tr>
                <th>No</th>
                <th>No LPAH</th>
                <th>Kandang</th>
                <th>Sopir</th>
                <th>Operator</th>
                <th>Item</th>
                <th>Ekor/Pcs/Pack</th>
                <th>Berat (Kg)</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($gradingnonlb as $i => $row)
                @php
                    $ekornonlb = 0;
                    $beratnonlb = 0;
                @endphp
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $row->no_lpah ?? '###' }} <br>{{$row->prodpur->no_po}} <br>{{ $row->prod_tanggal_potong }}</td>
                    <td>{{ $row->sc_nama_kandang ?? '###' }}</td>
                    <td>{{ $row->sc_pengemudi ?? '###' }}</td>
                    <td>{{ $row->grading_user_nama }}</td>
                    <td>
                        <ul class="pl-3">
                            @foreach ($row->prodpur->purchasing_item as $item)
                                <li>{{ $item->description ." || ".$item->jumlah_ayam."ekr ".$item->berat_ayam."Kg" ?? '' }}</li>
                            @endforeach
                        </ul>
                    </td>
                    <td>
                        @foreach ($row->prodgrad as $grad)
                            @if ($grad->keranjang == '0')
                                @php
                                    $ekornonlb += $grad->total_item;
                                @endphp
                            @endif
                        @endforeach
                        {{ number_format($ekornonlb) }} ekor
                    </td>
                    <td>
                        @foreach ($row->prodgrad as $grad)
                            @if ($grad->keranjang == '0')
                                @php
                                    $beratnonlb += $grad->berat_item;
                                @endphp
                            @endif
                        @endforeach
                        {{ number_format($beratnonlb, 2) }} Kg
                    </td>
                    <td>{{ $row->status_grading }}</td>
                    <td style="width: 100px">

                        @if ($row->grading_status == null)
                            <form action="{{ route('grading.store') }}" method="POST">
                                @csrf <input type="hidden" name="x_code"
                                    value="{{ $row->id }}">
                                <button class="btn btn-sm btn-primary btn-rounded">Proses</button>
                            </form>
                        @endif

                        @if ($row->grading_status == 1)
                            <a href="{{ route('grading.show', $row->id) }}?key=receiptulang" class="btn btn-sm btn-success btn-rounded">Receipt Ulang</a>
                            <a href="{{ route('grading.show', $row->id) }}" class="btn btn-sm btn-warning btn-rounded">Detail</a>
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
                            @if (User::setIjin(33))
                            <form action="{{ route('grading.update', $row->id) }}" method="POST">
                                @csrf @method('patch') <input type="hidden" name="key" value="send">
                                <button type="submit" class="btn btn-sm btn-dark btn-rounded float-left">Selesaikan</button>
                            </form>
                            @endif
                        @endif


                        @if ($row->grading_status != null)
                            <span class="status status-success">IR NS Selesai</span>
                            {{-- @if (User::setIjin(33))
                                <a href="{{ route('checker.produksi', $row->id) }}" class="btn btn-sm btn-info btn-rounded">NS Checker</a>
                            @endif --}}
                        @endif

                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="row">

    <div class="col-md-3 col-sm-4 col-xs-4 col-4">
        <div class="form-group">
            <label for="jumlah">Jumlah</label>
            <div class="input-group">
                <input type="text" value="{{ number_format($total['jumlahnonlb']) }}"
                    name="jumlah" class="form-control text-right bg-white" id="jumlah" readonly>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-4 col-xs-4 col-4">
        <div class="form-group">
            <label for="berat">Berat</label>
            <div class="input-group">
                <input type="text" value="{{ number_format($total['sumberatnonlb'], 2) }}"
                    name="berat" class="form-control text-right bg-white" id="berat" readonly>
            </div>

        </div>
    </div>
    <div class="col-md-3 col-sm-4 col-xs-4 col-4">
        <div class="form-group">
            <label for="ekor">Ekor/Pcs/Pack</label>
            <div class="input-group">
                <input type="text" value="{{ number_format($total['sumekornonlb']) }}"
                    name="ekor" class="form-control text-right bg-white" id="ekor" readonly>
            </div>
        </div>
    </div>
</div>

<script>
    $('#gradingNonLB').DataTable({
        "bInfo": false,
        responsive: true,
        scrollY:        500,
        scrollX:        true,
        scrollCollapse: true,
        paging:         false,
    });
</script>