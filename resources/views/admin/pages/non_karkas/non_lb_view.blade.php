<div class="table-responsive mt-3">
    @foreach ($purchase as $i => $val)

    @if($val->deleted_at)
        <div style="border: 2px solid red; padding: 3px">
    @endif
    <table width="100%" id="kategori" class="table default-table">
        <thead>
            <tr>
                <th colspan="6">
                    <div class="float-right">Tanggal : {{ $val->tanggal_potong }}</div>
                    {{$val->no_po}} &nbsp
                     @if($val->deleted_at) <span class="status status-danger">CLOSED</span>@endif
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="6">Item : <br>
                    <table class="table default-table">
                        @php
                            $tipe   =   0 ;
                            $kg     =   0 ;
                            $pcs    =   0 ;
                        @endphp
                        @foreach($val->purchasing_item2 as $no => $itm)
                            @php
                                $item   =   \App\Models\Item::item_sku($itm->item_po);
                                $tipe   +=  stripos($item->nama, 'FROZEN') ;
                                $kg     +=  $itm->berat_ayam ;
                                $pcs    +=  $itm->jumlah_ayam ;
                            @endphp
                        <tr>
                            <td class="list-record">{{$no+1}}</td>
                            <td class="list-record">{{$item->nama}}</td>
                            <td class="list-record text-right">{{ number_format($itm->berat_ayam, 2) ?? "###"}} kg</td>
                            <td class="list-record text-right">{{ number_format($itm->jumlah_ayam) ?? "###"}} Pcs/Ekr</td>
                            <td class="list-record">{{$itm->jenis_ayam ?? "###"}}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <th colspan="2">Total</th>
                            <th class="text-right">{{ number_format($kg, 2) }} kg</th>
                            <th class="text-right">{{ number_format($pcs) }} Pcs/Ekr</th>
                            <th></th>
                        </tr>
                        <tr>
                            <td colspan="6">
                                @foreach ($val->purcprod as $row)
                                    @if ($row->sc_ekor_do)
                                    <div class="mb-1">
                                        <div>Info Input Security :</div>
                                        No Urut : {{ $row->no_urut }} || Nomor DO : {{ number_format($row->no_do) }} || Ekor DO : {{ number_format($row->sc_ekor_do) }} || Berat DO : {{ number_format($row->sc_berat_do, 2) }} || No. Polisi : {{ $row->sc_no_polisi }}
                                    </div>
                                    @endif
                                @endforeach
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            @foreach($val->purcprod as $no => $prod)
            <tr>
                <td>DO {{ ++$no }} / Mobil {{ $no }}</td>
                <td align="right">{!!$prod->ppic_status!!}</td>
                <td>{{ $prod->sc_nama_kandang }}</td>
                <td></td>
                <td>{{ $prod->prodpur->jenis_po }}</td>
                <td align="right">
                    @if ($prod->ppic_acc == 1 or $prod->ppic_acc == null)


                    <div class="modal fade popup-edit-sc" id="edit{{ $prod->id }}"
                        aria-labelledby="edit{{ $prod->id }}Label" aria-hidden="true" style="text-align: left">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="edit{{ $prod->id }}Label">Edit
                                        Pengiriman Masuk</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="{{ route('security.update') }}" method="post">
                                    @csrf @method('patch')
                                    <input type="hidden" name="x_code" value="{{ $prod->id }}">
                                    <input type="hidden" name="tanggal" value="{{ $tanggalawal }}">
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="small">Supplier</div>
                                                    {{ $prod->prodpur->purcsupp->nama }}
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-12 col-form-label">Supir/Kernek</label>
                                                    <div class="col-sm-12">
                                                        <input type="text" name="supir" class="form-control"
                                                            placeholder="Supir" value="{{ $prod->sc_pengemudi }}"
                                                            autocomplete="off">
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-12 col-form-label">Nomor DO</label>
                                                    <div class="col-sm-12">
                                                        <input type="text" name="no_do" class="form-control"
                                                            placeholder="Nomor DO" value="{{ $prod->no_do }}"
                                                            autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-12 col-form-label">Ekor DO</label>
                                                    <div class="col-sm-12">
                                                        <input type="number" name="ekor_do" class="form-control"
                                                            placeholder="Ekor DO" value="{{ $prod->sc_ekor_do }}"
                                                            autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-12 col-form-label">Berat DO</label>
                                                    <div class="col-sm-12">
                                                        <input type="text" name="berat_do" class="form-control"
                                                            placeholder="Berat DO" value="{{ $prod->sc_berat_do }}"
                                                            autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                {{-- <div class="form-group">
                                                    <div class="small">Ukuran Ayam</div>
                                                    {{ $prod->prodpur->ukuran_ayam }}
                                                </div> --}}
                                                <div class="form-group row">
                                                    <label class="col-sm-12 col-form-label">No
                                                        Polisi</label>
                                                    <div class="col-sm-12">
                                                        <input type="text" name="no_polisi" class="form-control"
                                                            placeholder="No Polisi" value="{{ $prod->sc_no_polisi }}"
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
                                                            value="{{ $prod->sc_nama_kandang }}" autocomplete="off">
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
                                                    <label class="col-sm-12 col-form-label">Alasan Perubahan</label>
                                                    <div class="col-sm-12">
                                                        <textarea name="alasan" class="form-control background-grey-2"
                                                            placeholder="Tulis Alasan Perubahan" required
                                                            cols="3">Input oleh PPIC</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Edit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                        @if (User::setIjin(23))
                            <a href="" class="btn btn-neutral" data-toggle="modal" data-target="#edit{{ $prod->id }}">Input Security</a>
                            @if ($prod->prodpur->jenis_po == 'PO Karkas' || $prod->prodpur->jenis_po == 'PO Non Karkas')
                                @if ($tipe > 0)
                                <button type="submit" class="btn btn-info terimanonlb" data-id="{{ $prod->id }}" data-tujuan="abf">ABF</button>
                                @else
                                <button type="submit" class="btn btn-danger terimanonlb" data-id="{{ $prod->id }} " data-tujuan="grading">Grading</button>
                                @endif
                            @endif
                        @endif

                        @if ($prod->prodpur->jenis_po != 'PO Karkas')
                        <button type="submit" class="btn btn-warning terimanonlb" data-id="{{ $prod->id }} " data-tujuan="chiller">Chiller FG</button>
                        @endif
                    @else
                        @if ($prod->ppic_acc == 2)
                        <button type="submit" class="btn btn-danger batalnonlb" data-id="{{ $prod->id }}">Batal</button>
                        @endif
                    @endif
                </td>
            </tr>
            @endforeach

        </tbody>
    </table>

     @if($val->deleted_at)
        </div>
     @endif

    @endforeach
    <div class="paginate_nonlb">
        {{ $purchase->appends($_GET)->onEachSide(1)->links() }}
    </div>
</div>

<script>
    $('.paginate_nonlb .pagination a').on('click', function(e) {
        e.preventDefault();
        showNotif('Menunggu');

        url = $(this).attr('href');
        $.ajax({
            url: url,
            method: "GET",
            success: function(response) {
                $('#daftar_nonlb').html(response);
            }

        });
    });
    $('.terimanonlb').click(function() {
        var id = $(this).data('id');
        var tujuan = $(this).data('tujuan');

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
                tanggal: "{{ $tanggalawal }}"
            },
            success: function(data) {
                showNotif('Berhasil');
                $("#daftar_nonlb").load("{{ route('nonkarkas.index', ['view' => 'non_lb']) }}&tanggalawal={{ $tanggalawal }}");
                $('#timbang_nonkarkas').load("{{ route('nonkarkas.index', ['view' => 'timbang']) }}&tanggalawal={{ $tanggalawal }}&tanggalakhir={{ $tanggalakhir }}");
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
                $("#daftar_nonlb").load("{{ route('nonkarkas.index', ['view' => 'non_lb']) }}&tanggalawal={{ $tanggalawal }}");
                $('#timbang_nonkarkas').load("{{ route('nonkarkas.index', ['view' => 'timbang']) }}&tanggalawal={{ $tanggalawal }}&tanggalakhir={{ $tanggalakhir }}");
            }
        });
    })
</script>
