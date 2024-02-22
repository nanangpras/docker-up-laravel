<h5>Bahan Baku Produksi</h5>
@if ($freestock)
    <div class="mb-3">
        @php
            $item = 0;
            $berat = 0;
        @endphp
        @foreach ($freestock->listfreestock as $row)
            @php
                $item += $row->qty;
                $berat += $row->berat;
            @endphp
            <div class="border-bottom p-1">
                <div class="row">
                    <div class="col-5 pr-1">
                        {{ $row->chiller->item_name }}
                    </div>
                    <div class="col-3 pl-1">
                        ({{ $row->qty }} Ekor)
                    </div>
                    <div class="col-3 pl-1">
                        ({{ $row->berat }} Kg)
                    </div>
                    <div class="col-1 pl-1">
                        <i class="fa fa-trash ml-2 hapus_bb text-danger" style="cursor:pointer;" data-id="{{ $row->id }}"></i>
                    </div>
                </div>

            </div>
        @endforeach
        <div class="row">
            <div class="col-5 pr-1">
                <b class="text-center">TOTAL</b>
            </div>
            <div class="col-3 pl-1">
                <b class="text-center">{{ $item }} Ekor</b>
            </div>
            <div class="col-3 pl-1">
                <input type="hidden" name="beratbb" id="beratbb" value="{{ $berat }}">
                <b class="text-center">{{ $berat }} Kg</b>
            </div>
        </div>
    </div>
@endif


<button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#ambilBB" style="margin-bottom: 10px">
    Ambil Bahan Baku
</button>

<div class="modal fade" id="ambilBB" tabindex="-1" aria-labelledby="ambilBBLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('produksi.ambilBB') }}" method="POST">
            @csrf @method('patch')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ambilBBLabel">Ambil Bahan Baku</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="kategori" value="{{ $regu }}">
                    <table class="table default-table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Tanggal Potong</th>
                                <th>Qty (Ekor)</th>
                                <th>Berat (kg)</th>
                                <th>Pengambilan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bahan_baku as $row)
                            <tr>
                                <td> {{ $row->item_name }}</td>
                                <td>{{ date('d/m/y', strtotime($row->tanggal_potong)) }}</td>
                                <td>{{ $row->stock_item }}</td>
                                <td>{{ $row->stock_berat }}</td>
                                <td>
                                    <input type="hidden" name="x_code[]" value="{{ $row->id }}">
                                    <input type="number" name="qty[]" style="width: 100px" class="form-control.form-control-sm" step="0.01" placeholder="Ekor">
                                    <input type="number" name="berat[]" style="width: 100px" class="form-control.form-control-sm" step="0.01" placeholder="Berat">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btnHiden">Tambah</button>
                </div>
            </div>
        </form>
    </div>
</div>
