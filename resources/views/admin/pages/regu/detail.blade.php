@extends('admin.layout.template')

@section('title', 'Detail Produksi Kepala Regu ' . $regu)

@section('header')
<style>
    ol.switches {
        padding-left: 0 !important;
    }

    .switches li {
        position: relative;
        counter-increment: switchCounter;
        list-style-type: none;
    }

    .switches li:not(:last-child) {
        border-bottom: 1px solid var(--gray);
    }

    .switches label {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 7px
    }

    .switches span:last-child {
        position: relative;
        width: 50px;
        height: 26px;
        border-radius: 15px;
        box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.4);
        background: var(--gray);
        transition: all 0.3s;
    }

    .switches span:last-child::before,
    .switches span:last-child::after {
        content: "";
        position: absolute;
    }

    .switches span:last-child::before {
        left: 1px;
        top: 1px;
        width: 24px;
        height: 24px;
        background: var(--white);
        border-radius: 50%;
        z-index: 1;
        transition: transform 0.3s;
    }

    .switches span:last-child::after {
        top: 50%;
        right: 8px;
        width: 12px;
        height: 12px;
        transform: translateY(-50%);
        background: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/162656/uncheck-switcher.svg);
        background-size: 12px 12px;
    }

    .switches [type="checkbox"] {
        position: absolute;
        left: -9999px;
    }

    .switches [type="checkbox"]:checked+label span:last-child {
        background: var(--green);
    }

    .switches [type="checkbox"]:checked+label span:last-child::before {
        transform: translateX(24px);
    }

    .switches [type="checkbox"]:checked+label span:last-child::after {
        width: 14px;
        height: 14px;
        /*right: auto;*/
        left: 8px;
        background-image: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/162656/checkmark-switcher.svg);
        background-size: 14px 14px;
    }
</style>
@endsection

@section('content')
@php
$so = \App\Models\OrderItem::find($data->orderitem_id);
$ns = \App\Models\Netsuite::where('id',$data->netsuite_id)->first();
@endphp
<div class="row mt-3 mb-4">
    <div class="col"><a
            href="{{ $data->orderitem_id ? route('regu.request_view', $data->orderitem_id) : route('regu.index', ['kategori' => $request->kategori]) }}"><i
                class="fa fa-arrow-left"></i> Kembali</a></div>
    <div class="col-10 text-center"><b>Detail Produksi Kepala Regu {{ $regu }}</b></div>
    <div class="col"></div>
</div>

<div class="card">
    <div class="font-weight-bold card-header">Produksi : {{date('d/m/Y', strtotime($data->tanggal))}}</div>
    <div class="card-body">
        <div class="row" id="edit-bb-hasil" style="display: none">
            <div class="col-md-12 pl-md-1 mb-4">
                <div class="card-body">
                    <h5>Hasil Produksi</h5>
                    <div id="hasil_produksi"></div>
                    <a href="javascript:void(0)" onclick="return openTambah()" class="btn btn-red">Tutup</a>
                    <hr>
                </div>
            </div>
        </div>

        {{-- @if ($so && $data->netsuite_send == '0')

        @else --}}
        @if($ns)
            @if(Auth::user()->account_role == 'superadmin')
                <button type="button" id="btnAmbilBB" class="btn btn-primary" data-toggle="modal" data-target="#ambilBB">
                    Tambah Bahan Baku
                </button>
            @endif
        @else
                <button type="button" id="btnAmbilBB" class="btn btn-primary" data-toggle="modal" data-target="#ambilBB">
                    Tambah Bahan Baku
                </button>
        @endif
        {{-- @endif --}}

        <div class="modal fade" id="ambilBB" aria-labelledby="ambilBBLabel" aria-hidden="true">
            <div class="modal-dialog" style="max-width:1200px">
                <form action="{{ route('regu.ambilbb') }}" method="POST">
                    @csrf @method('patch') <input type="hidden" name="produksi" value="{{ $data->id }}">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="ambilBBLabel">Ambil Bahan Baku</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col">
                                    <form action="#" method="GET">
                                        <div class="form-group">
                                            Pencarian Tanggal
                                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' )
                                                min="2023-01-01" @endif name="tanggal" class="form-control"
                                                value="{{ date('Y-m-d') }}" id="pencarian" placeholder="Cari...."
                                                autocomplete="off">
                                        </div>
                                    </form>
                                </div>
                                <div class="col">
                                    Pencarian Kata
                                    <input type="text" id="cari_bb" placeholder="Cari..." class="form-control mb-2">
                                    <input type="hidden" name="kategori" value="{{ $request->kat }}">
                                </div>
                            </div>
                            <label class="btn btn-success">
                                <input id="karkas" type="checkbox" name="karkas"> Karkas / Chiller BB
                            </label>
                            <label class="btn btn-blue">
                                <input id="non-karkas" type="checkbox" name="non-karkas"> Non Karkas / Chiller FG
                            </label>
                            <label class="btn btn-danger">
                                <input id="bb-retur" type="checkbox" name="bb-retur"> Retur
                            </label>
                            <label class="btn btn-warning">
                                <input id="bb-thawing" type="checkbox" name="bb-thawing"> Thawing
                            </label>

                            <label class="btn btn-info">
                                <input id="bb-abf" type="checkbox" name="bb-abf"> Kirim ABF
                            </label>
                            <div id="loading-ambilBB" class="text-center"><i class="fa fa-refresh fa-spin"></i>
                                Loading....</div>
                            <br>
                            <div id="bahanbaku"></div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary btnHiden">Tambah</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- @if ($so && $data->netsuite_send == '0')

        @else --}}
        @if($ns)
            @if(Auth::user()->account_role == 'superadmin')
                <a href="javascript:void(0)" onclick="return openTambah()" id="btnTambahFG" class="btn btn-green">Tambah Hasil
                Produksi</a>
            @endif
        @else
            <a href="javascript:void(0)" onclick="return openTambah()" id="btnTambahFG" class="btn btn-green">Tambah Hasil
                Produksi</a>
        @endif
        
        {{-- @endif --}}

        @if ($data->status == 2)
        <button type="button" class="btn btn-success float-right" id="btnSelesaikan"
            data-id="{{ $data->id }}">Selesaikan</button>
        @endif
        <br>
        <hr>
        <div id="data_produksi"></div>

        <script>
            function openTambah(){
                $('#edit-bb-hasil').toggle();
            }
        </script>

        <div id="loading-detailProduksi" class="text-center"><i class="fa fa-refresh fa-spin"></i> Loading....</div>
    </div>
</div>


@if($so)
<section class="panel">
    <div class="card-body p-2">
        <span class="stats status-danger">INPUT BY ORDER #{{$so->id}}</span>
        <table class="table table-sm table-striped mb-0">
            <tbody>
                <tr>
                    <th>Nomor SO</th>
                    <td>{{ $so->itemorder->no_so }}</td>
                </tr>
                <tr>
                    <th>Customer</th>
                    <td>{{ $so->itemorder->nama }}</td>
                </tr>
                <tr>
                    <th>Item</th>
                    <td>{{ $so->nama_detail }}</td>
                </tr>
                <tr>
                    <th>Qty/Berat</th>
                    <td><span class="status status-info">{{ number_format($so->qty) }} pcs</span> <span
                            class="status status-success">{{ number_format($so->berat, 2) }} kg</span></td>
                </tr>
                @if ($so->part || $so->bumbu)
                <tr>
                    <th>Tambahan</th>
                    <td>
                        <ul class="mb-0 pl-3">
                            @if ($so->part) <li>PARTING {{ $so->part }}</li> @endif
                            @if ($so->bumbu) <li>BUMBU {{ $so->bumbu }}</li> @endif
                        </ul>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</section>
@endif

@if ($data->status == 2)
<script>
    const btnSelesaikan = document.getElementById('btnSelesaikan');
    btnSelesaikan.addEventListener('click', () => {
        const idProduksi = btnSelesaikan.getAttribute('data-id');
        btnSelesaikan.style.visibility = 'hidden';
        // console.log(idProduksi);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('regu.store') }}",
            method: "POST",
            data: {
                key: 'selesaikan',
                jenis: "{{ $request->kategori }}",
                cast: 'approve',
                id: idProduksi,
            },
            success: function(data) {
                // console.log(data)
                if (data.code == 200) {
                    showNotif('Produksi berhasil diselesaikan');
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000)
                } else {
                    showAlert(data.message)
                }
            }
        });
    })

</script>
@endif


<script>
    var tanggal     = $("#pencarian").val();
    var cari_bb     = document.getElementById('cari_bb').value
    var non_karkas  = $('#non-karkas').is(':checked');
    var bb_retur  = $('#bb-retur').is(':checked');
    var bb_thawing  = $('#bb-thawing').is(':checked');
    var bb_abf  = $('#bb-abf').is(':checked');
    var url_bb      = "{{ url('admin/produksi-regu/bahanbaku?tanggal=') }}" + tanggal+"&non_karkas="+non_karkas+"&bb_retur="+bb_retur+"&bb_thawing="+bb_thawing+"&bb_abf="+bb_abf + "&search=" + encodeURIComponent($("#cari_bb").val());

    $("#btnAmbilBB").on('click', () => {
        loadingBB();
    })

    $('#pencarian').on('keyup', function() {
        loadingBB();
    })

    $('#karkas').on('change', function() {
        loadingBB();
    })
    $('#non-karkas').on('change', function() {
        loadingBB();
    })
    $('#bb-retur').on('change', function() {
        loadingBB();
    })
    $('#bb-thawing').on('change', function() {
        loadingBB();
    })
    $('#bb-abf').on('change', function() {
        loadingBB();
    })


    function loadingBB(){
        $("#loading-ambilBB").show();
        tanggal     = $("#pencarian").val();
        cari_bb     = document.getElementById('cari_bb').value
        karkas      = $('#karkas').is(':checked');
        non_karkas  = $('#non-karkas').is(':checked');
        bb_retur    = $('#bb-retur').is(':checked');
        bb_thawing  = $('#bb-thawing').is(':checked');
        bb_abf      = $('#bb-abf').is(':checked');
        url_bb      = "{{ url('admin/produksi-regu/bahanbaku?tanggal=') }}" + tanggal+"&karkas="+karkas+"&non_karkas="+non_karkas+"&bb_retur="+bb_retur+"&bb_thawing="+bb_thawing+"&bb_abf="+bb_abf + "&search=" + encodeURIComponent($("#cari_bb").val());

        $("#bahanbaku").load(url_bb, function() {
            $("#loading-ambilBB").hide();
        });
    }

    var cariBbTimeout = null;

    $('#cari_bb').on('keyup', function(){
        $("#loading-ambilBB").show();
        if (cariBbTimeout != null) {
            clearTimeout(cariBbTimeout);
        }
        cariBbTimeout = setTimeout(function() {
            cariBbTimeout = null;
            //ajax code
            loadingBB()
        }, 1000);
    })
</script>


<script>
    $("#btnTambahFG").on('click', function() {
        $("#hasil_produksi").load("{{ route('regu.index', ['key' => 'hasil_produksi']) }}&kat={{ $request->kategori }}&produksi={{ $data->id }}");
    })


    function load_all(){
        $("#data_produksi").load("{{ route('regu.index', ['key' => 'data_produksi']) }}&kat={{ $request->kategori }}&produksi={{ $data->id }}", function() {
            $("#loading-detailProduksi").hide();
        });
    }

    load_all();
</script>

<script>
    $(document).on('click', '.input_freestock', function() {
        var produksi = "{{ $request->produksi }}";
        var plastik = $('#plastik').val();
        var jumlah_plastik = $('#jumlah_plastik').val();
        var parting = $('#part').val();
        var item = $('#itemfree').val();
        var berat = $('#berat').val();
        var jumlah = $('#jumlah').val();

        var itemtunggir = $('#itemtunggir').val();
        var berattunggir = $('#berattunggir').val();
        var jumlahtunggir = $('#jumlahtunggir').val();

        var itemmaras = $('#itemmaras').val();
        var beratmaras = $('#beratmaras').val();
        var jumlahmaras = $('#jumlahmaras').val();

        var itemlemak = $('#itemlemak').val();
        var beratlemak = $('#beratlemak').val();
        var jumlahlemak = $('#jumlahlemak').val();

        var sub_item = $('#sub_item').val();
        var customer = $('#customer').val();

        var unit            =   $('#unit').val();
        var jumlah_keranjang=   $('#jumlah_keranjang').val();
        var kode_produksi   =   $('#kode_produksi').val();

        var tujuan_produksi = $('input[name="tujuan_produksi"]:checked').val();
        var selonjor        =   $("#selonjor:checked").val() ;

        var additional = [];
        $('.additional').each(function() {
            if ($(this).is(":checked")) {
                additional.push($(this).val());
            }
        });

        if (item == '') {
            showAlert('Item wajib dipilih');
        } else {
            if (tujuan_produksi == 1) {
                if (plastik != 'Curah') {
                    if (jumlah_plastik > 0) {
                        var next = 'TRUE';
                    }
                } else {
                    // if (jumlah_plastik > 0) {
                        var next = 'TRUE';
                    // }
                }
            } 
            else {
                if (plastik == 'Curah') {
                    var next = 'TRUE';
                } else {
                    if (jumlah_plastik > 0) {
                        var next = 'TRUE';
                    }
                }
            }


            if (next != 'TRUE') {
                showAlert('Lengkapi data plastik');
            } else {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "{{ route('regu.store') }}",
                    method: "POST",
                    data: {
                        jenis: "{{ $request->kategori }}",
                        act:'tambahan',
                        produksi: produksi,
                        item: item,
                        berat: berat,
                        jumlah: jumlah,
                        itemtunggir: itemtunggir,
                        berattunggir: berattunggir,
                        jumlahtunggir: jumlahtunggir,
                        itemlemak: itemlemak,
                        beratlemak: beratlemak,
                        jumlahlemak: jumlahlemak,
                        itemmaras: itemmaras,
                        beratmaras: beratmaras,
                        jumlahmaras: jumlahmaras,
                        parting: parting,
                        plastik: plastik,
                        jumlah_plastik: jumlah_plastik,
                        additional: additional,
                        tujuan_produksi: tujuan_produksi,
                        sub_item: sub_item,
                        customer: customer,
                        selonjor: selonjor,
                        unit:unit,
                        jumlah_keranjang:jumlah_keranjang,
                        kode_produksi:kode_produksi,
                    },
                    success: function(data) {
                        if (data.status == 400) {
                            showAlert(data.msg);
                        } else {
                            $('#plastik').val('');
                            $('#jumlah_plastik').val('');
                            $('#part').val('');
                            $('#itemfree').val('');
                            $('#berat').val('');
                            $('#jumlah').val('');

                            $('#itemtunggir').val('');
                            $('#berattunggir').val('');
                            $('#jumlahtunggir').val('');

                            $('#itemmaras').val('');
                            $('#beratmaras').val('');
                            $('#jumlahmaras').val('');

                            $('#itemlemak').val('');
                            $('#beratlemak').val('');
                            $('#jumlahlemak').val('');

                            $('#sub_item').val('');
                            $('#customer').val('');

                            $('#unit').val('');
                            $('#jumlah_keranjang').val('');
                            $('#kode_produksi').val('');

                            // $('input[name="tujuan_produksi"]:checked').val('');
                            $("#selonjor:checked").val('') ;
                            $('.modal-backdrop').remove();
                            $('body').removeClass('modal-open');
                            load_all();
                            showNotif('Produksi berhasil ditambahkan');
                        }
                    }
                });
            }

        }

    })
</script>

<script>
    $(document).on('click', '.hapus_bb', function() {
    var row_id = $(this).data('id');
    $(".hapus_bb").hide();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: "{{ route('regu.delete') }}",
        method: "DELETE",
        data: {
            row_id: row_id,
            key: 'bb_detail'
        },
        success: function(data) {
            if (data.status == 400) {
                showAlert(data.msg) ;
            } else {
                load_all();
                showNotif('Bahan baku diambil berhasil dihapus');
            }
            $(".hapus_bb").show();
        }
    });
})
</script>

<script>
    $(document).on('click', '.hapus_fg', function() {
    var row_id  = $(this).data('id');
    var nama    = $(this).data('nama');
    if(confirm( 'Apakah anda yakin mau menghapus item `'+nama+ '` ini?') === true){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('regu.delete') }}",
            method: "DELETE",
            data: {
                row_id: row_id,
                key: 'hapus_fg'
            },
            success: function(data) {
                console.log(data);
                if (data.status == 400) {
                    showAlert(data.msg) ;
                } else {
                    load_all();
                    showNotif('Hasil produksi berhasil dihapus');
                    console.log("oke")
                        $(".hapus_fg").hide();
                }
                $(".hapus_fg").show();
            }
        });
    }
})
</script>

<script>
    $(document).on('click', '.tanggal_produksi', function() {
    var row_id  = $(this).data('id');
    var tanggal = $("#tanggal_produksi").val() ;
    var regu    = $("#grupregu").val() ;
    // var netsuite_send   = "TRUE";

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // if($('#netsuite_send').get(0).checked) {
    //     // something when checked
    //     netsuite_send = "TRUE";
    // } else {
    //     // something else when not
    //     netsuite_send = "FALSE";
    // }

    $.ajax({
        url: "{{ route('regu.store') }}",
        method: "POST",
        data: {
            row_id: row_id,
            tanggal: tanggal,
            regu: regu,
            // netsuite_send : netsuite_send,
            key: 'ubahtanggal'
        },
        success: function(data) {
            if (data.status == 400) {
                showAlert(data.msg) ;
            } else {
                window.location.href = "{{ route('regu.index')}}?&kategori="+regu+"&produksi={{ $data->id }}";
                showNotif('Ubah tanggal dan Regu Berhasil');
            }
        }
    });
})
    $(document).on('click','.detail_batalkan', function () {
        var id = $(this).data('id');
        var regu = "{{ $request->kategori }}";
        // alert(id);

        if(confirm("Batalkan inputan produksi? setelah dibatalkan tidak bisa dikembalikan lagi")){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('regu.store') }}",
                method: "POST",
                data: {
                    key: 'selesaikan',
                    jenis: "{{ $request->kategori }}",
                    cast: 'removed',
                    id: id,
                },
                success: function() {
                    showNotif('Produksi berhasil dibatalkan');
                    window.location.href="{{ route('regu.index')}}?kategori="+regu+"#regu-tabs-request";

                }
            });
        }else{
                showAlert('Cancel');
        }
    });
</script>


@endsection
