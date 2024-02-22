@extends('admin.layout.template')

@section('title', 'Penerimaan Masuk')

@section('content')
<div class="text-center mt-3 mb-4">
    <b>Penerimaan Masuk</b>
</div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-md">
                    <div class="row">

                        <div class="col-4 col-md-12">
                            <div class="form-group">
                                <div class="small">Tanggal Potong</div>
                                <b>{{ $data->lpah_tanggal_potong ?? '###' }}</b>
                            </div>
                        </div>

                        <div class="col-4 col-md-12">
                            <div class="form-group">
                                <div class="small">No Urut Mobil</div>
                                <b>{{ $data->no_urut }}</b> |
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#modalnomor"> <span
                                        class="fa fa-edit"></span> Edit </a>
                            </div>
                        </div>

                        <div class="col-4 col-md-12">
                            <div class="form-group">
                                <div class="small">Ukuran</div>
                                <b>@if ($data->prodpur->ukuran_ayam == '&lt; 1.1') {{ '<1.1' }} @else {{ $data->prodpur->ukuran_ayam }} @endif</b>
                            </div>
                        </div>

                        <div class="modal fade" id="modalnomor" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="{{ route('lpah.update', [$data->id, 'key' => 'updateurut']) }}" method="post">
                                    @csrf @method('patch') <input type="hidden" name="id" value="{{ $data->id }}" required>
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalLabel">EDIT NOMOR URUT</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col">
                                                    <div class="form-group">
                                                        Nomor Urut
                                                        <input type="number" name="nourut" class="form-control" value="{{ $data->no_urut ?? '' }}" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">OK</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-12 col-md">
                    <div class="row">

                        <div class="col-6 col-md-12">
                            <div class="form-group">
                                <div class="small">Nomor PO</div>
                                <b>{{ $data->prodpur->no_po ?? '###' }}</b>
                            </div>
                        </div>

                        <div class="col-6 col-md-12">
                            <div class="form-group">
                                <div class="small">Supir</div>
                                <b>{{ $data->sc_pengemudi }}</b>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-12 col-md">
                    <div class="row">

                        <div class="col-6 col-md-12">
                            <div class="form-group">
                                <div class="small">Wilayah</div>
                                <b class="text-capitalize">{{ $data->sc_wilayah ?? '####' }}</b>
                            </div>
                        </div>

                        <div class="col-6 col-md-12">
                            <div class="form-group">
                                <div class="small">No Polisi</div>
                                <b>{{ $data->sc_no_polisi }}</b>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-12 col-md">
                    <div class="row">

                        <div class="col-6 col-md-12">
                            <div class="form-group">
                                <div class="small">Jenis Ekspedisi</div>
                                <b class="text-capitalize">{{ $data->po_jenis_ekspedisi }}</b>
                            </div>
                        </div>

                        <div class="col-6 col-md-12">
                            <div class="form-group">
                                <div class="small">Kondisi Ayam</div>
                                <b>{{ $data->kondisi_ayam }}</b>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-12 col-md">
                    <div class="row">

                        <div class="col-6 col-md-12">
                            <div class="form-group">
                                <div class="small">Berat DO</div>
                                <b>{{ number_format($data->sc_berat_do, 2) }} Kg</b>
                            </div>
                        </div>

                        <div class="col-6 col-md-12">
                            <div class="form-group">
                                <div class="small">Total DO</div>
                                <b>{{ number_format($data->sc_ekor_do) }} Ekor</b>
                            </div>
                        </div>

                        <div class="col-6 col-md-12">
                            <a href="javascript:void(0)" data-toggle="modal" data-target="#modal"> <span class="fa fa-edit"></span> Edit DO </a>
                        </div>

                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{ route('lpah.updatedo', $data->id) }}" method="post">
                        @csrf @method('put') <input type="hidden" name="id" value="{{ $data->id }}" required>
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalLabel">EDIT DO DITERIMA</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            Berat DO
                                            <input type="number" name="sc_berat_do" class="form-control" value="{{ $data->sc_berat_do ?? '' }}" step="0.01" required>
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="form-group">
                                            Total DO
                                            <input type="number" name="sc_ekor_do" class="form-control" value="{{ $data->sc_ekor_do ?? '' }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">OK</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row border-top pt-3">
                
                <div class="col col-sm-12">
                    <div class="row">
                        <div class="col pr-1">
                            <div class="form-group">
                                <div class="small">Masuk</div>
                                <input type="time" value="{{ date('H:i', strtotime($data->sc_jam_masuk)) ?? date('H:i') }}" id="sc_jam_masuk" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="col pr-1">
                            <div class="form-group">
                                <div class="small">Bongkar</div>
                                <input type="time" value="{{ date('H:i', strtotime($data->lpah_jam_bongkar)) ?? date('H:i') }}" id="jam_bongkar" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="col pr-1">
                            <div class="form-group">
                                <div class="small">Selesai</div>
                                <input type="time" value="{{ date('H:i', strtotime($data->lpah_jam_selesai)) ?? date('H:i') }}" id="jam_selesai" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="col pl-1">
                            <div class="form-group">
                                <div class="small">Nama Petugas</div>
                                <input type="text" id="nama_petugas" class="form-control form-control-sm" value="{{ $data->lpah_user_nama }}" placeholder="Nama Petugas" required>
                            </div>
                        </div>
                        <div class="col-auto pr-1">
                            <div class="small">&nbsp;</div>
                            <button class="btn btn-block btn-primary input_bongkar">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="susut"></div>

    <div class="row mb-3">

        <div class="col-md-3 pr-md-1 mb-4">
            <div class="card">
                <div class="card-body p-2">
                    <div id='timbangisi'></div>
                </div>
            </div>
        </div>

        <div class="mb-4 px-md-1 col-md-6">
            <div class="card mx-lg-5">
                <div class="card-body mx-4 p-0 pb-3">
                    <div class="form-group mt-3 text-center">
                        <b>TIMBANGAN</b>
                    </div>

                    <div class="form-group">
                        <b>Variable</b>
                        <div class="form-group radio-toolbar row">
                            <div class="col">
                                <div class="form-group mb-0">
                                    <input type="radio" name="type" class="part" value="isi" id='isi'>
                                    <label for="isi">ISI</label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group mb-0">
                                    <input type="radio" name="type" class="part" value="kosong" id='kosong'>
                                    <label for="kosong">KOSONG</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="my-3">
                            <input type="text" id="result" name="result"
                                class="text-right form-control bg-white label-timbang" readonly required>
                        </div>
                        <div class="row my-3">
                            <div class="col pr-3">
                                <input type="button" class="btn btn-default btn-block form-control py-2 tits-calculator"
                                    value="7" onclick="dis('7')" />
                            </div>
                            <div class="col px-1">
                                <input type="button" class="btn btn-default btn-block form-control py-2 tits-calculator"
                                    value="8" onclick="dis('8')" />
                            </div>
                            <div class="col pl-3">
                                <input type="button" class="btn btn-default btn-block form-control py-2 tits-calculator"
                                    value="9" onclick="dis('9')" />
                            </div>
                        </div>
                        <div class="row my-3">
                            <div class="col pr-3">
                                <input type="button" class="btn btn-default btn-block form-control py-2 tits-calculator"
                                    value="4" onclick="dis('4')" />
                            </div>
                            <div class="col px-1">
                                <input type="button" class="btn btn-default btn-block form-control py-2 tits-calculator"
                                    value="5" onclick="dis('5')" />
                            </div>
                            <div class="col pl-3">
                                <input type="button" class="btn btn-default btn-block form-control py-2 tits-calculator"
                                    value="6" onclick="dis('6')" />
                            </div>
                        </div>
                        <div class="row my-3">
                            <div class="col pr-3">
                                <input type="button" class="btn btn-default btn-block form-control py-2 tits-calculator"
                                    value="1" onclick="dis('1')" />
                            </div>
                            <div class="col px-1">
                                <input type="button" class="btn btn-default btn-block form-control py-2 tits-calculator"
                                    value="2" onclick="dis('2')" />
                            </div>
                            <div class="col pl-3">
                                <input type="button" class="btn btn-default btn-block form-control py-2 tits-calculator"
                                    value="3" onclick="dis('3')" />
                            </div>
                        </div>
                        <div class="row my-3">
                            <div class="col pr-3">
                                <input type="button" class="btn btn-default btn-block form-control py-2 tits-calculator"
                                    value="C" onclick="clr()" />
                            </div>
                            <div class="col px-1">
                                <input type="button" class="btn btn-default btn-block form-control py-2 tits-calculator"
                                    value="0" onclick="dis('0')" />
                            </div>
                            <div class="col pl-3">
                                <input type="button" class="btn btn-default btn-block form-control py-2 tits-calculator"
                                    value="." onclick="dis('.')" />
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-4" id="submit">
                        <button type="button" class="add_cart btn btn-block btn-primary">Submit</button>
                    </div>


                </div>
            </div>
        </div>

        <div class="col-md-3 pl-md-1 mb-4">
            <div class="card">
                <div class="card-body p-2">
                    <div id='timbangkosong'></div>
                </div>
            </div>
        </div>
    </div>

    <br> <small class="status status-warning mt-1 small">*KLIK SIMPAN JIKA SUDAH SELESAI MELAKUKAN TIMBANG / UPDATE AGAR RATA-RATA DAN BERAT TERIMA TERHITUNG / TERUPDATE</small>
    <div class="text-right mb-3">

        <form action="{{ route('lpah.store', ['key' => 'simpan']) }}" method="POST">
            @csrf <input type="hidden" name="x_code" value="{{ $data->id }}">
            <a href="{{ route('lpah.index') }}" class="px-4 ml-3 btn btn-outline-primary">Keluar</a>
            <button class="btn btn-danger btn-rounded">Simpan</button>
        </form>

    </div>
@endsection

@section('footer')
    <script>
        //function that display value
        function dis(val) {
            document.getElementById("result").value += val
        }

        //function that evaluates the digit and return result
        function solve() {
            let x = document.getElementById("result").value
            let y = eval(x)
            document.getElementById("result").value = y
        }

        //function that clear the display
        function clr() {
            let r = document.getElementById("result").value;
            let v = (r / 10 ^ 0);
            if (v == 0) {
                document.getElementById("result").value = "";
            } else {
                document.getElementById("result").value = v;
            }
        }

        function clrberat() {
            document.getElementById("berat").value = ""
            $('#berat_bersih').val('');
        }
    </script>
    <script>
        $(document).ready(function() {
            // Tambah cart
            $('.add_cart').click(function() {
                var berat = $('#result').val();
                var type = $('input:radio[name=type]:checked').val();

                if (type == undefined) {
                    return showAlert('Variable masih kosong')
                }
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                if (type == '' || berat == '') {
                    return showAlert('Data belum lengkap')
                }

                $('.label-timbang').val('') ;

                $.ajax({
                    url: "{{ route('lpah.add', $data->id) }}",
                    method: "POST",
                    data: {
                        berat: berat,
                        type: type
                    },
                    success: function(data) {
                        $('#cart').load("{{ route('lpah.cart', $data->id) }}");
                        $('#susut').load("{{ route('lpah.susut', $data->id) }}");
                        $('#timbangisi').load("{{ route('lpah.show', [$data->id, 'key' => 'timbangisi']) }}");
                        $('#timbangkosong').load("{{ route('lpah.show', [$data->id, 'key' => 'timbangkosong']) }}");
                        showNotif('Data ditambahkan');
                    }
                });

            });
        });

        $(document).ready(function() {
            $(document).on('click', '.input_bongkar', function() {
                var jam_bongkar     =   $('#jam_bongkar').val();
                var sc_jam_masuk    =   $('#sc_jam_masuk').val();
                var jam_selesai     =   $('#jam_selesai').val();
                var nama_petugas    =   $('#nama_petugas').val();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "{{ route('lpah.jambongkar', $data->id) }}",
                    method: "PATCH",
                    data: {
                        jam_bongkar :   jam_bongkar,
                        sc_jam_masuk :   sc_jam_masuk,
                        nama_petugas:   nama_petugas,
                        jam_selesai :   jam_selesai
                    },
                    success: function(data) {
                        $('#cart').load("{{ route('lpah.cart', $data->id) }}");
                        $('#susut').load("{{ route('lpah.susut', $data->id) }}");
                        $('#timbangisi').load("{{ route('lpah.show', [$data->id, 'key' => 'timbangisi']) }}");
                        $('#timbangkosong').load("{{ route('lpah.show', [$data->id, 'key' => 'timbangkosong']) }}");

                        return showNotif('Jam bongkar dan nama petugas berhasil diperbaharui');
                    }
                });

            });
        });

        $(document).ready(function() {
            $(document).on('click', '.susut_submit', function() {
                var mati                       =   $('#mati').val();
                var matikg                     =   $('#matikg').val();
                var jml_keranjang              =   $('#jml_keranjang').val();
                var ekoran_seckle              =   $('#ekoran_seckle').val();
                var ayammerah                  =   $('#ayammerah').val();
                var ekorayammerah              =   $('#ekorayammerah').val();
                var tembolok                   =   $('#tembolok').val();
                var kebersihanKeranjang        =   $('#kebersihanKeranjang').val();
                var downtime                   =   $('#downtime').val();
                // let hitungAyam                 =  ''
                // if($("#hitung_ayam").is(':checked')){
                //     hitungAyam = 1
                // } else {
                //     hitungAyam = 0
                // }

                if (mati == "" || matikg == "" || jml_keranjang == "" || ekoran_seckle == "" || ekorayammerah == "" || ayammerah == "" || tembolok == "") {
                    return showAlert('Data belum lengkap')
                }

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "{{ route('lpah.updatesusut', $data->id) }}",
                    method: "POST",
                    data: {
                        mati                :   mati,
                        matikg              :   matikg,
                        keranjang           :   jml_keranjang,
                        ekoran_seckle       :   ekoran_seckle,
                        ekorayammerah       :   ekorayammerah,
                        ayammerah           :   ayammerah,
                        tembolok            :   tembolok,
                        kebersihanKeranjang : kebersihanKeranjang,
                        downtime            : downtime,
                        // hitungAyam
                    },
                    success: function(data) {
                        $('#cart').load("{{ route('lpah.cart', $data->id) }}");
                        $('#susut').load("{{ route('lpah.susut', $data->id) }}");
                        $('#timbangisi').load("{{ route('lpah.show', [$data->id, 'key' => 'timbangisi']) }}");
                        $('#timbangkosong').load("{{ route('lpah.show', [$data->id, 'key' => 'timbangkosong']) }}");

                        return showNotif('Data terupdate')
                    }
                });

            });
        });

        $(document).ready(function() {
            // Menampilkan cart
            $('#cart').load("{{ route('lpah.cart', $data->id) }}");
            $('#susut').load("{{ route('lpah.susut', $data->id) }}");

            $('#timbangisi').load("{{ route('lpah.show', [$data->id, 'key' => 'timbangisi']) }}");
            $('#timbangkosong').load("{{ route('lpah.show', [$data->id, 'key' => 'timbangkosong']) }}");
        });

        $(document).ready(function() {
            // Edit cart
            $(document).on('click', '.edit_cart', function() {
                var row_id = $(this).data('id');
                var tipe_timbang = $('#tipe_timbang' + row_id).val();
                var berat = $('#berat' + row_id).val();
                var keranjang = $('#keranjang' + row_id).val();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });


                $.ajax({
                    url: "{{ route('lpah.update', $data->id) }}",
                    method: "PATCH",
                    data: {
                        row_id: row_id,
                        berat: berat,
                        keranjang: keranjang,
                        tipe_timbang: tipe_timbang,
                        key : 'editkeranjang'
                    },
                    success: function(data) {
                        $('.modal-backdrop.show').css('opacity', '0');
                        $('.modal-backdrop').css('z-index', '-1')
                        $('body').removeClass('modal-open');
                        $('#cart').load("{{ route('lpah.cart', $data->id) }}");
                        $('#susut').load("{{ route('lpah.susut', $data->id) }}");
                        $('#timbangisi').load("{{ route('lpah.show', [$data->id, 'key' => 'timbangisi']) }}");
                        $('#timbangkosong').load("{{ route('lpah.show', [$data->id, 'key' => 'timbangkosong']) }}");

                        if (data.status == 400) {
                            showAlert(data.msg);
                        } else {
                            showNotif(data.msg);
                        }
                    }
                });

            });
        });
    </script>
@endsection
