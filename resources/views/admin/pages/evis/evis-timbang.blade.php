@extends('admin.layout.template')

@section('header')
    <style>
        .radio-toolbar input[type="radio"] {
            opacity: 0;
            position: fixed;
            width: 0;
        }

        .radio-toolbar label {
            display: inline-block;
            background-color: #fff;
            padding: 8px 10px;
            font-family: sans-serif, Arial;
            font-size: 13px;
            border: 1px solid #444;
            border-radius: 5px;
            width: 100%;
            text-align: center
        }

        .radio-toolbar input[type="radio"]:checked+label {
            background-color: #bfb;
            border-color: #4c4;
        }

    </style>
@endsection

@section('title', 'Data Proses Evis')

@section('content')

    <div class="row py-4">
        <div class="col">
            <a href="{{ route('evis.index') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
                Back</a>
        </div>
        <div class="col-6 text-center">
            <b>PROSES TIMBANG EVIS</b>
        </div>
        <div class="col"></div>
    </div>

    <section class="panel">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3 col-6 mb-3">
                    <div class="small"><b>TANGGAL POTONG</b></div>
                    {{ $data->sc_tanggal_masuk }}
                </div>

                <div class="col-lg-3 col-6 mb-3">
                    <div class="small"><b>NOMOR URUT</b></div>
                    {{ $data->no_urut }}
                </div>

                <div class="col-lg-3 col-6 mb-3">
                    <div class="small"><b>JUMLAH AYAM LPAH</b></div>
                    {{ number_format($data->total_bersih_lpah) }} EKOR
                </div>

                <div class="col-lg-3 col-6 mb-3">
                    <div id="prosentase"></div>
                </div>
            </div>
            <div class="row">
                <div class="col"></div>
                <div class="col">
                    <div class="row">
                        <div class="col pr-1">
                            <div class="form-group">
                                Nama Petugas
                                <input type="text" value="{{ $data->evis_user_name }}" id="nama_petugas" class="form-control form-control-sm" placeholder="Tulis Nama Petugas">
                            </div>
                        </div>
                        <div class="col-auto pl-1">
                            &nbsp;
                            <button class="btn btn-block btn-primary" id="input_petugas">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="panel">
        <div class="card-body">
            <div class="border-bottom pb-2 mb-3">
                <div id="cart"></div>
            </div>
            @if ($data->evis_status == 2)
                <div class="tab-pane fade show active" id="custom-tabs-three-timbang" role="tabpanel"
                    aria-labelledby="custom-tabs-three-timbang-tab">
                    <button type="button" class="btn btn-block btn-danger text-light mb-3" id='btn_back'
                        style="display: none">
                        <b>Batalkan Edit Data</b>
                    </button>
                    <div class="row">

                        <div class="col-lg-4 col-12">
                            <label>Jenis Item</label>
                            <div class="form-group radio-toolbar">
                                @php
                                    if ($data->prodpur->purchasing_item[0]->keterangan == 'AYAM HIDUP PEJANTAN (RM)') {
                                        $evis =DataOption::getOption('evis_pejantan');
                                        $data_evis = explode(',', $evis);
                                    } else if ($data->prodpur->purchasing_item[0]->keterangan == 'AYAM HIDUP PARENT (RM)') {
                                        $evis =DataOption::getOption('evis_parent');
                                        $data_evis = explode(',', $evis);
                                    } else if ($data->prodpur->purchasing_item[0]->keterangan == 'AYAM HIDUP KAMPUNG (RM)') {
                                        $evis =DataOption::getOption('evis_kampung');
                                        $data_evis = explode(',', $evis);
                                    } else {
                                        $evis = DataOption::getOption('evis_' . ($data->prodpur->jenis_ayam ?? 'broiler'));
                                        $data_evis = explode(',', $evis);
                                    }
                                    // dd($data_evis);
                                @endphp
                                <div class="row">
                                    @for ($i = 0; $i < count($data_evis); $i++)
                                        @php
                                            $item = App\Models\Item::where('sku', (int) $data_evis[$i])->first();
                                        @endphp
                                        <div class="col-lg-12 col-sm-6 col-12">
                                            <input type="radio" name="part" class="part" value="{{ $item->id ?? '' }}" id="evis{{ $data_evis[$i] }}">
                                            <label for="evis{{ $data_evis[$i] }}">{{ $item->nama ?? '' }}</label>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6 col-sm-6 col-6">
                            <input type="hidden" name="x_code" id="x_code" value="{{ $data->id }}">
                            <input type="hidden" name="jenis" id="jenis" value="mobil">
                            <input type="hidden" name="idedit" id="idedit" value="">
                            <label>Total Ekor</label>
                            <input type="text" id="result" name="result"
                                class="text-right form-control label-timbang" readonly>
                            <div class="row my-3">
                                <div class="col pr-1 pr-lg-3">
                                    <input type="button" class="btn btn-default btn-block form-control tits-calculator" value="7" onclick="dis('7')" />
                                </div>
                                <div class="col px-1 px-lg-1">
                                    <input type="button" class="btn btn-default btn-block form-control tits-calculator" value="8" onclick="dis('8')" />
                                </div>
                                <div class="col pl-1 pl-lg-3">
                                    <input type="button" class="btn btn-default btn-block form-control tits-calculator" value="9" onclick="dis('9')" />
                                </div>
                            </div>
                            <div class="row my-3">
                                <div class="col pr-1 pr-lg-3">
                                    <input type="button" class="btn btn-default btn-block form-control tits-calculator" value="4" onclick="dis('4')" />
                                </div>
                                <div class="col px-1 px-lg-1">
                                    <input type="button" class="btn btn-default btn-block form-control tits-calculator" value="5" onclick="dis('5')" />
                                </div>
                                <div class="col pl-1 pl-lg-3">
                                    <input type="button" class="btn btn-default btn-block form-control tits-calculator" value="6" onclick="dis('6')" />
                                </div>
                            </div>
                            <div class="row my-3">
                                <div class="col pr-1 pr-lg-3">
                                    <input type="button" class="btn btn-default btn-block form-control tits-calculator" value="1" onclick="dis('1')" />
                                </div>
                                <div class="col px-1 px-lg-1">
                                    <input type="button" class="btn btn-default btn-block form-control tits-calculator" value="2" onclick="dis('2')" />
                                </div>
                                <div class="col pl-1 pl-lg-3">
                                    <input type="button" class="btn btn-default btn-block form-control tits-calculator" value="3" onclick="dis('3')" />
                                </div>
                            </div>
                            <div class="row my-3">
                                <div class="col pr-1 pr-lg-3">
                                    <input type="button" class="btn btn-red btn-block form-control tits-calculator" value="C" onclick="clr()" />
                                </div>
                                <div class="col px-1 px-lg-1">
                                    <input type="button" class="btn btn-default btn-block form-control tits-calculator" value="0" onclick="dis('0')" />
                                </div>
                                <div class="col pl-1 pl-lg-3">
                                    <input type="button" class="btn btn-default btn-block form-control tits-calculator" value="." onclick="dis('.')" />
                                </div>
                            </div>

                        </div>

                        <div class="col-lg-4 col-md-6 col-sm-6 col-6">
                            <label>Berat</label>
                            <input type="text" id="berat" name="berat" class="form-control label-timbang"
                                readonly>
                            <div class="row my-3">
                                <div class="col pr-1 pr-lg-3">
                                    <input type="button" class="btn btn-default btn-block form-control tits-calculator" value="7" onclick="disberat('7')" />
                                </div>
                                <div class="col px-1 px-lg-1">
                                    <input type="button" class="btn btn-default btn-block form-control tits-calculator" value="8" onclick="disberat('8')" />
                                </div>
                                <div class="col pl-1 pl-lg-3">
                                    <input type="button" class="btn btn-default btn-block form-control tits-calculator" value="9" onclick="disberat('9')" />
                                </div>
                            </div>
                            <div class="row my-3">
                                <div class="col pr-1 pr-lg-3">
                                    <input type="button" class="btn btn-default btn-block form-control tits-calculator" value="4" onclick="disberat('4')" />
                                </div>
                                <div class="col px-1 px-lg-1">
                                    <input type="button" class="btn btn-default btn-block form-control tits-calculator" value="5" onclick="disberat('5')" />
                                </div>
                                <div class="col pl-1 pl-lg-3">
                                    <input type="button" class="btn btn-default btn-block form-control tits-calculator" value="6" onclick="disberat('6')" />
                                </div>
                            </div>
                            <div class="row my-3">
                                <div class="col pr-1 pr-lg-3">
                                    <input type="button" class="btn btn-default btn-block form-control tits-calculator" value="1" onclick="disberat('1')" />
                                </div>
                                <div class="col px-1 px-lg-1">
                                    <input type="button" class="btn btn-default btn-block form-control tits-calculator" value="2" onclick="disberat('2')" />
                                </div>
                                <div class="col pl-1 pl-lg-3">
                                    <input type="button" class="btn btn-default btn-block form-control tits-calculator" value="3" onclick="disberat('3')" />
                                </div>
                            </div>
                            <div class="row my-3">
                                <div class="col pr-1 pr-lg-3">
                                    <input type="button" class="btn btn-red btn-block form-control tits-calculator" value="C" onclick="clrberat()" />
                                </div>
                                <div class="col px-1 px-lg-1">
                                    <input type="button" class="btn btn-default btn-block form-control tits-calculator" value="0" onclick="disberat('0')" />
                                </div>
                                <div class="col pl-1 pl-lg-3">
                                    <input type="button" class="btn btn-default btn-block form-control tits-calculator" value="." onclick="disberat('.')" />
                                </div>
                            </div>
                        </div>

                    </div>
                    <button type="button" id='add_cart' class="btn mt-4 btn-primary btn-block">Timbang</button>
                </div>
            @endif
        </div>
    </section>

    @if ($data->evis_status == 2)
        <section class="panel rounded">
            <div class="card-body">
                <form action="{{ route('evis.update', $data->id) }}" method="POST">
                    @csrf @method('patch')
                    <button type="submit" class="btn-sm btn btn-success btn-block">Simpan</button>
                </form>
            </div>
        </section>
    @endif

    @if ($data->evis_status == 3)
        <section class="panel rounded">
            <div class="card-body">
                <div class="row">
                    <div class="col pr-1">
                        <form action="{{ route('evis.update', $data->id) }}" method="POST">
                            @csrf @method('patch') <input type="hidden" name="key" value="back">
                            <button type="submit" class="btn-sm btn btn-warning btn-block">Edit</button>
                        </form>
                    </div>
                    <div class="col pl-1">
                        <form action="{{ route('evis.update', $data->id) }}" method="POST">
                            @csrf @method('patch') <input type="hidden" name="key" value="send">
                            <button type="submit" class="btn-sm btn btn-dark btn-block">Selesaikan</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    @endif

@stop
@section('footer')
    <script>
        $("#prosentase").load("{{ route('laporan.prosentase', $data->id) }}");
    </script>

    <script>
        //function that display value
        function dis(val) {
            document.getElementById("result").value += val
        }

        function disberat(val) {
            document.getElementById("berat").value += val
        }

        //function that evaluates the digit and return result
        function solve() {
            let x = document.getElementById("result").value
            let y = eval(x)
            document.getElementById("result").value = y
        }

        //function that clear the display
        function clr() {
            document.getElementById("result").value = ""
        }

        function clrberat() {
            document.getElementById("berat").value = ""
        }

        $(document).ready(function() {

            // Menampilkan
            $("#show_order").load("{{ route('evis.order') }}");
            $('#hasil').load("{{ route('evis.result', $data->id) }}");


            // Tambah cart
            $('#add_cart').click(function() {
                var row_id  =   $('#x_code').val();
                var idedit  =   $('#idedit').val();
                var berat   =   $('#berat').val();
                var result  =   $('#result').val();
                var part    =   $('.part:checked').val();
                var jenis   =   $('#jenis').val();

                if (part == undefined) {
                    showAlert('Jenis item belum dipilih')
                } else {
                    if (berat < 0.1) {
                        showAlert('Berat tidak boleh kosong')
                    } else {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });

                        $('#add_cart').hide();

                        $.ajax({
                            url: "{{ route('evis.add', $data->id) }}",
                            method: "POST",
                            data: {
                                idedit  :   idedit,
                                row_id  :   row_id,
                                berat   :   berat,
                                result  :   result,
                                part    :   part,
                                jenis   :   jenis
                            },

                            success: function(data) {
                                $("#prosentase").load("{{ route('laporan.prosentase', $data->id) }}");
                                $('#add_cart').html("Timbang");
                                $('#idedit').val("");
                                $('#cart').load("{{ route('evis.cart', $data->id) }}");
                                $('#hasil').load("{{ route('evis.result', $data->id) }}");
                                $('#result').val('');
                                $('#berat').val('');
                                $('input[type="radio"]').prop('checked', false);
                                $('#custom-tabs-three-summary-tab').tab('show');
                                document.getElementById('btn_back').style = 'display: none';
                                $('#add_cart').show();
                            }
                        });
                    }
                }

            });
        });

        $(document).ready(function() {
            // Menampilkan cart
            $('#cart').load("{{ route('evis.cart', $data->id) }}");
        });

        $(document).ready(function() {
            $(document).on('click', '#btn_back', function() {
                document.getElementById('btn_back').style = 'display: none';
                $('#add_cart').html("Timbang");
                $('#idedit').val("");
                $('#cart').load("{{ route('evis.cart', $data->id) }}");
                $('#hasil').load("{{ route('evis.result', $data->id) }}");
                $('#result').val('');
                $('#berat').val('');
                $('input[type="radio"]').prop('checked', false);
            });
        });


        $(document).ready(function() {
            // Edit cart
            $(document).on('click', '.edit_cart', function() {
                var row_id = $(this).data('kode');
                document.getElementById('btn_back').style = 'display: block';

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ route('evis.edit', $data->id) }}",
                    method: "POST",
                    data: {
                        row_id: row_id
                    },
                    success: function(data) {
                        $('#custom-tabs-three-timbang-tab').tab('show');
                        $('#hasil').load("{{ route('evis.result', $data->id) }}");
                        $('#idedit').val(data.id);
                        $('[name="result"]').val(data.total_item);
                        $('#berat').val(data.berat_item);
                        $('input:radio[name=part][value=' + data.item_id + ']')[0].checked =
                            true;
                        $('#berat_bersih').val(data.berat_stock);
                        document.getElementById('add_cart').innerHTML = 'Ubah';
                    }
                });
            });
        });


        $(document).ready(function() {
            $(document).on('click', '#input_petugas', function() {
                var nama_petugas    =   $("#nama_petugas").val() ;

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "{{ route('evis.edit', [$data->id, 'key' => 'petugas']) }}",
                    method: "POST",
                    data: {
                        nama_petugas:   nama_petugas ,
                    },
                    success: function(data) {
                        showNotif("Petugas berhasil diperbaharui");
                    }
                });
            });
        });
    </script>
@endsection
