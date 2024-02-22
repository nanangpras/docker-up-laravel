<div class="table-responsive mt-3">
    @foreach ($purchase as $i => $val)
        <table width="100%" id="kategori" class="table default-table">
            <thead>
                <tr>
                    <th colspan="6"> {{ $val->no_po }} </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="6">Item : <br>
                        <table class="table default-table">
                            @foreach ($val->purchasing_frozen as $no => $itm)
                                @php
                                    $item = \App\Models\Item::item_sku($itm->item_po);
                                @endphp
                                <tr>
                                    <td class="list-record">{{ $no + 1 }}</td>
                                    <td class="list-record">{{ $item->nama }}</td>
                                    <td class="list-record">Rp {{ number_format($itm->harga) }}</td>
                                    <td class="list-record">{{ number_format($itm->berat_ayam, 2) ?? '###' }} kg
                                    </td>
                                    <td class="list-record">{{ number_format($itm->jumlah_ayam) ?? '###' }} Pcs/Ekr
                                    </td>
                                    <td class="list-record">{{ $itm->jenis_ayam ?? '###' }}</td>
                                    <td class="list-record">{{ $item->sku }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                </tr>

                @foreach ($val->purcprod as $no => $prod)
                    <tr>
                        <td>DO {{ ++$no }} / Mobil {{ $no }}</td>
                        <td align="right">{!!$prod->ppic_status!!}</td>
                        <td>{{ $prod->sc_nama_kandang }}</td>
                        <td></td>
                        <td>{{ $prod->prodpur->jenis_po }}</td>
                        <td align="right">
                            @if ($prod->ppic_acc == 1 or $prod->ppic_acc == null)
                                <div class="modal fade popup-edit-sc" id="edit{{ $prod->id }}"
                                    aria-labelledby="edit{{ $prod->id }}Label" aria-hidden="true"
                                    style="text-align: left">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="edit{{ $prod->id }}Label">Edit
                                                    Pengiriman Masuk</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="{{ route('security.update') }}" method="post">
                                                @csrf @method('patch')
                                                <input type="hidden" name="x_code" value="{{ $prod->id }}">
                                                <input type="hidden" name="tanggal" value="{{ $prod->prod_tanggal_potong }}">
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <div class="small">Supplier</div>
                                                                {{ $prod->prodpur->purcsupp->nama }}
                                                            </div>
                                                            <div class="form-group row">
                                                                <label
                                                                    class="col-sm-12 col-form-label">Supir/Kernek</label>
                                                                <div class="col-sm-12">
                                                                    <input type="text" name="supir"
                                                                        class="form-control" placeholder="Supir"
                                                                        value="{{ $prod->sc_pengemudi }}"
                                                                        autocomplete="off">
                                                                </div>
                                                            </div>

                                                            <div class="form-group row">
                                                                <label class="col-sm-12 col-form-label">Nomor DO</label>
                                                                <div class="col-sm-12">
                                                                    <input type="text" name="no_do"
                                                                        class="form-control" placeholder="Nomor DO"
                                                                        value="{{ $prod->no_do }}"
                                                                        autocomplete="off">
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label class="col-sm-12 col-form-label">Ekor DO</label>
                                                                <div class="col-sm-12">
                                                                    <input type="number" name="ekor_do"
                                                                        class="form-control" placeholder="Ekor DO"
                                                                        value="{{ $prod->sc_ekor_do }}"
                                                                        autocomplete="off">
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label class="col-sm-12 col-form-label">Berat DO</label>
                                                                <div class="col-sm-12">
                                                                    <input type="text" name="berat_do"
                                                                        class="form-control" placeholder="Berat DO"
                                                                        value="{{ $prod->sc_berat_do }}"
                                                                        autocomplete="off">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group row">
                                                                <label class="col-sm-12 col-form-label">No
                                                                    Polisi</label>
                                                                <div class="col-sm-12">
                                                                    <input type="text" name="no_polisi"
                                                                        class="form-control" placeholder="No Polisi"
                                                                        value="{{ $prod->sc_no_polisi }}"
                                                                        autocomplete="off">
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label class="col-sm-12 col-form-label">Nama
                                                                    Kandang</label>
                                                                <div class="col-sm-12">
                                                                    <input type="text" name="nama_kandang"
                                                                        class="form-control background-grey-2"
                                                                        placeholder="Nama Kandang"
                                                                        value="{{ $prod->sc_nama_kandang }}"
                                                                        autocomplete="off">
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label class="col-sm-12 col-form-label">Alamat
                                                                    Kandang</label>
                                                                <div class="col-sm-12">
                                                                    <textarea name="alamat_kandang"
                                                                        class="form-control background-grey-2"
                                                                        placeholder="Tulis Alamat Kandang"
                                                                        cols="3">{{ $prod->sc_alamat_kandang }}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row" style="display: none">
                                                                <label class="col-sm-12 col-form-label">Alasan
                                                                    Perubahan</label>
                                                                <div class="col-sm-12">
                                                                    <textarea name="alasan"
                                                                        class="form-control background-grey-2"
                                                                        placeholder="Tulis Alasan Perubahan" required
                                                                        cols="3">Input oleh PPIC</textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Edit</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <a href="" class="btn btn-neutral" data-toggle="modal"
                                    data-target="#edit{{ $prod->id }}">Input Security</a>
                                <button type="submit" class="btn btn-info terimanonlb" data-tanggal="{{ $prod->prod_tanggal_potong }}" data-id="{{ $prod->id }}"
                                    data-tujuan="abf">ABF</button>

                            @else
                                <button type="submit" class="btn btn-danger batalnonlb"
                                    data-id="{{ $prod->id }}">Batal</button>
                            @endif
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    @endforeach
</div>

<script>
    $('.terimanonlb').click(function() {
        var id = $(this).data('id');
        var tujuan = $(this).data('tujuan');
        var tanggal = $(this).data('tanggal');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: "{{ route('ppic.ppic_acc') }}",
            method: "POST",
            data: {
                id: id,
                tujuan: tujuan,
                tanggal: tanggal
            },
            success: function(data) {
                showNotif('Berhasil');
                $('#warehouse-nonlb').load("{{ route('warehouse.nonlb') }}")
            }
        });
    })

    $('.batalnonlb').click(function() {
        var id = $(this).data('id');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: "{{ route('ppic.ppic_batal') }}",
            method: "POST",
            data: {
                id: id,
            },
            success: function(data) {
                showNotif('Berhasil');
                $('#warehouse-nonlb').load("{{ route('warehouse.nonlb') }}")
            }
        });
    })

</script>
