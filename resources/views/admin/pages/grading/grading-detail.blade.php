@extends('admin.layout.template')

@section('title', 'Data Proses Grading')

@section('content')
<div class="row my-4">
    <div class="col">
        <a href="{{ route('grading.index') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
            Back</a>
    </div>
    <div class="col-6 text-center">
        <b>DATA PROSES GRADING</b>
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
        <div class="border-top pt-3">
            <div class="row">
                <div class="col">
                    <div id="kalkulasi"></div>
                    @if ($data->prodpur->jenis_po == 'PO Karkas')
                    <b>Item PO Non LB {{$data->prodpur->no_po}}</b>
                    <ul class="pl-3">
                        @foreach ($data->prodpur->purchasing_item as $item)
                        <li>{{ $item->description ." || ".$item->jumlah_ayam."ekr ".$item->berat_ayam."Kg" ?? '' }}</li>
                        @endforeach
                    </ul>
                    @endif
                </div>

            </div>
            <div class="row">
                <div class="col">
                    &nbsp;
                    Tanggal Bahan Baku
                    @if ($data->prodpur->type_po == 'PO Karkas')
                    @if($data->prodgrad[0] ?? FALSE)
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif
                        value="{{$data->prodgrad[0]->tanggal_potong ? $data->prodgrad->last()->tanggal_potong : $data->prod_tanggal_potong }}"
                        class="form-control form-control-sm" id="tanggal" name="tanggal"><br>
                    @else
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif value="{{$data->prodpur->tanggal_kirim ?? date('Y-m-d')}}"
                        class="form-control form-control-sm" id="tanggal" name="tanggal"><br>
                    @endif
                    <span class="status status-info">*Tanggal Bahan Baku digunakan pada saat timbang dan juga
                        selesaikan</span>
                    <br>
                    <span class="status status-info mb-5">*Pastikan tanggal bahan baku sudah sesuai untuk
                        diselesaikan</span>
                    @else
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif value="{{ $data->grading_selesai ? date("Y-m-d",
                        strtotime($data->grading_selesai)) :
                    $data->prod_tanggal_potong }}" class="form-control
                    form-control-sm" id="tanggal" name="tanggal"><br>
                    @endif
                </div>
                <div class="col">
                    Nama Petugas
                    <input type="text" id="nama_petugas" value="{{ $data->grading_user_nama }}"
                        class="form-control form-control-sm" placeholder="Tulis Nama Petugas">
                    <br>
                    <button class="btn btn-primary btn-block" id="input_petugas">Submit</button>
                </div>
            </div>
        </div>
    </div>
</section>




<section class="panel">
    <div class="row">

        <div class="col-12 col-sm-12">
            <div class="card card-primary card-outline card-tabs">
                <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                    @if ($data->grading_status == 2)
                    <li class="nav-item">
                        <a class="nav-link tab-link" id="custom-tabs-three-home-tab" data-toggle="pill"
                            href="#custom-tabs-three-home" role="tab" aria-controls="custom-tabs-three-home"
                            aria-selected="true">Timbang</a>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link  tab-link active" id="custom-tabs-three-profile-tab" data-toggle="pill"
                            href="#custom-tabs-three-profile" role="tab" aria-controls="custom-tabs-three-profile"
                            aria-selected="false">Summary</a>

                    </li>
                </ul>

                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-three-tabContent">
                        @if ($data->grading_status == 2)
                        <div class="tab-pane fade" id="custom-tabs-three-home" role="tabpanel"
                            aria-labelledby="custom-tabs-three-home-tab">

                            <div class="radio-toolbar">
                                <div class="row">
                                    <div class="col-3">
                                        <div class="form-group">
                                            <input type="radio" name="jenis" value="normal" class="jenis" id="normal">
                                            <label for="normal">Normal</label>

                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <input type="radio" name="jenis" value="memar" class="jenis" id="memar">
                                            <label for="memar">Memar</label>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <input type="radio" name="jenis" value="utuh" class="jenis" id="utuh">
                                            <label for="utuh">Utuh</label>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <input type="radio" name="jenis" value="pejantan" class="jenis"
                                                id="pejantan">
                                            <label for="pejantan">Pejantan</label>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <input type="radio" name="jenis" value="parent" class="jenis"
                                                id="parent">
                                            <label for="parent">Parent</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <label>Ukuran Ayam</label>
                            <div class="radio-toolbar">
                                <div id="jenisayam"></div>
                            </div>

                            <br>
                            <div class="form-group">
                                Keterangan
                                <input type="text" name="keterangan" class="form-control" id="keterangan"
                                    placeholder="Tuliskan Keterangan" value="" autocomplete="off">
                            </div>


                            <div class="row mt-4 mb-4">
                                <div class="col-6 pr-2">
                                    <input type="hidden" name="x_code" id="x_code" value="{{ $data->id }}">
                                    <input type="hidden" name="idedit" id="idedit" value="">
                                    <label>Total Ekor</label>
                                    <div class="my-3">
                                        <input type="text" id="result" name="result"
                                            class="form-control bg-white label-timbang" readonly required>
                                    </div>
                                    <div class="row my-3">
                                        <div class="col pr-1 pr-lg-3">
                                            <input type="button"
                                                class="btn btn-default btn-block form-control tits-calculator" value="7"
                                                onclick="dis('7')" />
                                        </div>
                                        <div class="col px-1 px-lg-1">
                                            <input type="button"
                                                class="btn btn-default btn-block form-control tits-calculator" value="8"
                                                onclick="dis('8')" />
                                        </div>
                                        <div class="col pl-1 pl-lg-3">
                                            <input type="button"
                                                class="btn btn-default btn-block form-control tits-calculator" value="9"
                                                onclick="dis('9')" />
                                        </div>
                                    </div>
                                    <div class="row my-3">
                                        <div class="col pr-1 pr-lg-3">
                                            <input type="button"
                                                class="btn btn-default btn-block form-control tits-calculator" value="4"
                                                onclick="dis('4')" />
                                        </div>
                                        <div class="col px-1 px-lg-1">
                                            <input type="button"
                                                class="btn btn-default btn-block form-control tits-calculator" value="5"
                                                onclick="dis('5')" />
                                        </div>
                                        <div class="col pl-1 pl-lg-3">
                                            <input type="button"
                                                class="btn btn-default btn-block form-control tits-calculator" value="6"
                                                onclick="dis('6')" />
                                        </div>
                                    </div>
                                    <div class="row my-3">
                                        <div class="col pr-1 pr-lg-3">
                                            <input type="button"
                                                class="btn btn-default btn-block form-control tits-calculator" value="1"
                                                onclick="dis('1')" />
                                        </div>
                                        <div class="col px-1 px-lg-1">
                                            <input type="button"
                                                class="btn btn-default btn-block form-control tits-calculator" value="2"
                                                onclick="dis('2')" />
                                        </div>
                                        <div class="col pl-1 pl-lg-3">
                                            <input type="button"
                                                class="btn btn-default btn-block form-control tits-calculator" value="3"
                                                onclick="dis('3')" />
                                        </div>
                                    </div>
                                    <div class="row my-3">
                                        <div class="col pr-1 pr-lg-3">
                                            <input type="button"
                                                class="btn btn-red btn-block form-control tits-calculator" value="C"
                                                onclick="clr()" />
                                        </div>
                                        <div class="col px-1 px-lg-1">
                                            <input type="button"
                                                class="btn btn-default btn-block form-control tits-calculator" value="0"
                                                onclick="dis('0')" />
                                        </div>
                                        <div class="col pl-1 pl-lg-3">
                                            <input type="button"
                                                class="btn btn-default btn-block form-control tits-calculator" value="."
                                                onclick="dis('.')" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 pl-2">
                                    <label>Berat</label>
                                    <div class="my-3">
                                        <input type="text" id="berat" name="berat"
                                            class="form-control bg-white label-timbang" onkeyup="beratbersih()" readonly
                                            required>
                                    </div>
                                    <div class="row my-3">
                                        <div class="col pr-1 pr-lg-3">
                                            <input type="button"
                                                class="btn btn-default btn-block form-control tits-calculator" value="7"
                                                onclick="disberat('7')" />
                                        </div>
                                        <div class="col px-1 px-lg-1">
                                            <input type="button"
                                                class="btn btn-default btn-block form-control tits-calculator" value="8"
                                                onclick="disberat('8')" />
                                        </div>
                                        <div class="col pl-1 pl-lg-3">
                                            <input type="button"
                                                class="btn btn-default btn-block form-control tits-calculator" value="9"
                                                onclick="disberat('9')" />
                                        </div>
                                    </div>
                                    <div class="row my-3">
                                        <div class="col pr-1 pr-lg-3">
                                            <input type="button"
                                                class="btn btn-default btn-block form-control tits-calculator" value="4"
                                                onclick="disberat('4')" />
                                        </div>
                                        <div class="col px-1 px-lg-1">
                                            <input type="button"
                                                class="btn btn-default btn-block form-control tits-calculator" value="5"
                                                onclick="disberat('5')" />
                                        </div>
                                        <div class="col pl-1 pl-lg-3">
                                            <input type="button"
                                                class="btn btn-default btn-block form-control tits-calculator" value="6"
                                                onclick="disberat('6')" />
                                        </div>
                                    </div>
                                    <div class="row my-3">
                                        <div class="col pr-1 pr-lg-3">
                                            <input type="button"
                                                class="btn btn-default btn-block form-control tits-calculator" value="1"
                                                onclick="disberat('1')" />
                                        </div>
                                        <div class="col px-1 px-lg-1">
                                            <input type="button"
                                                class="btn btn-default btn-block form-control tits-calculator" value="2"
                                                onclick="disberat('2')" />
                                        </div>
                                        <div class="col pl-1 pl-lg-3">
                                            <input type="button"
                                                class="btn btn-default btn-block form-control tits-calculator" value="3"
                                                onclick="disberat('3')" />
                                        </div>
                                    </div>
                                    <div class="row my-3">
                                        <div class="col pr-1 pr-lg-3">
                                            <input type="button"
                                                class="btn btn-red btn-block form-control tits-calculator" value="C"
                                                onclick="clrberat()" />
                                        </div>
                                        <div class="col px-1 px-lg-1">
                                            <input type="button"
                                                class="btn btn-default btn-block form-control tits-calculator" value="0"
                                                onclick="disberat('0')" />
                                        </div>
                                        <div class="col pl-1 pl-lg-3">
                                            <input type="button"
                                                class="btn btn-default btn-block form-control tits-calculator" value="."
                                                onclick="disberat('.')" />
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="form-group text-right">
                                        @if ($data->grading_status == 2)
                                        <button type="button" id='add_cart'
                                            class="btn btn-primary btn-block">Timbang</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="tab-pane fade show active" id="custom-tabs-three-profile" role="tabpanel"
                            aria-labelledby="custom-tabs-three-profile-tab">

                            <div class="table-responsive">


                                <table width="100%" id="cart" class="table default-table"></table>

                            </div>
                            @php
                            $ns = \App\Models\Netsuite::where('document_code',
                            $data->no_po)->whereNotNull('document_no')->where('tabel_id', $data->id )->where('tabel',
                            'productions')->get();
                            @endphp
                            @if(count($ns) < 1) @if(Auth::user()->account_role == 'superadmin' or User::setijin(33) or
                                User::setIjin(5))
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary btn-sm p-0 px-1 disabled" disabled
                                        data-toggle="modal" data-target="#tambah" style="display: none">Tambah Item
                                    </button>
                                </div>
                                @endif
                                @else
                                @if(Auth::user()->account_role == 'superadmin')
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary btn-sm p-0 px-1 disabled" disabled
                                        data-toggle="modal" data-target="#tambah" style="display: none">Tambah Item
                                    </button>
                                </div>
                                @endif
                                @endif
                                {{--
                                <div class="modal fade" id="tambah" data-backdrop="tambah" aria-labelledby="tambahLabel"
                                    aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Tambah Data Grading</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="{{ route('checker.addgrading') }}" method="post">
                                                <div class="modal-body">
                                                    @csrf <input type="hidden" name="idproduksi" value="{{ $data->id }}"">
                                                    <div class=" form-group">
                                                    <label for="">Item</label>
                                                    <select name="item" class="form-control select2" id="item"
                                                        data-placeholder="Pilih Item" data-width="100%">
                                                        <option value=""></option>
                                                        @foreach ($item as $it)
                                                        <option value="{{ $it->id }}">{{ $it->nama }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label for="">Ekor/Pcs/Pack</label>
                                                    <input type="text" name="qty" class="form-control" id="qty"
                                                        placeholder="Tuliskan " value="" autocomplete="off">
                                                </div>
                                                <div class="form-group">
                                                    <label for="">Berat</label>
                                                    <input type="text" name="berat" class="form-control" id="berat"
                                                        placeholder="Tuliskan " value="" autocomplete="off">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@if ($data->grading_status == 2)
<div id="hasil"></div>
@endif
@if($data->prodpur->type_po == 'PO Karkas')
@if($data->grading_status == 2)
<form action="{{ route('grading.update', $data->id) }}" method="POST">
    @csrf @method('patch') <input type="hidden" name="key" value="send">
    @if($data->prodgrad[0] ?? FALSE)
    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01" @endif
        hidden
        value="{{ $data->prodgrad[0]->tanggal_potong ? $data->prodgrad->last()->tanggal_potong : $data->prod_tanggal_potong }}"
        id="tanggalGradingReceipt" name="tanggal">
    @else
    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01" @endif
        hidden value="{{$data->prodpur->tanggal_kirim ?? date('Y-m-d')}}" id="tanggalGradingReceipt" name="tanggal">
    @endif

    <button type="submit" class="btn btn-dark btn-block">Selesaikan</button>
</form>
@endif
@endif


@if ($data->grading_status == 3)
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col">
                <form action="{{ route('grading.update', $data->id) }}" method="POST">
                    @csrf @method('patch') <input type="hidden" name="key" value="edit">
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif hidden value="{{ $data->grading_selesai ? date("Y-m-d", strtotime($data->grading_selesai)) : $data->prod_tanggal_potong }}" id="tanggalGradingEdit" name="tanggal">
                    <button class="btn btn-warning btn-block">Edit</button>
                </form>
            </div>
            {{-- @if (User::setIjin(33)) --}}
            <div class="col">
                <form action="{{ route('grading.update', $data->id) }}" method="POST">
                    @csrf @method('patch')
                    <input type="hidden" name="key" value="send">
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif hidden value="{{ $data->grading_selesai ? date("Y-m-d", strtotime($data->grading_selesai)) : $data->prod_tanggal_potong }}" id="tanggalGrading" name="tanggal">
                    <button class="btn btn-dark btn-block">Selesaikan</button>
                </form>
            </div>
            {{-- @endif --}}
        </div>
    </div>
</div>
@endif

@if (User::setIjin('superadmin'))
<br>
<form action="{{ route('grading.injectGradingIR', $data->id) }}" method="POST">
    @csrf @method('patch')
    <button type="submit" class="btn btn-warning btn-block">Inject IR *Khusus Item yang belum terbentuk IR</button>

</form>
@endif


@if ($data->prodpur->jenis_po == 'PO Karkas')

@if (User::setIjin('superadmin'))
<section class="panel mt-3">
    <div class="card-body">
        <table class="table default-table">
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" id="ns-checkall">
                    </th>
                    <th>ID</th>
                    <th>C&U Date</th>
                    <th>TransDate</th>
                    <th>Label</th>
                    <th>Activity</th>
                    <th>Location</th>
                    <th>IntID</th>
                    <th>Paket</th>
                    <th width="100px">Data</th>
                    <th width="100px">Action</th>
                    <th>Response</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @php
                $ns = \App\Models\Netsuite::where('document_code', 'like', '%'.$data->no_po.'%')->get();
                @endphp
                @foreach ($ns as $i => $n)
                @include('admin.pages.log.netsuite_one', ($netsuite = $n))
                @endforeach
            </tbody>
        </table>
    </div>
</section>

@endif
@endif
@stop



@section('footer')

<script>
    $('#tanggal').on('change', function() {
            const tanggal = $('#tanggal').val();
            $('#tanggalGrading').attr('value', tanggal);
            $('#tanggalGradingEdit').attr('value', tanggal);
            $('#tanggalGradingSimpan').attr('value', tanggal);
            $('#tanggalGradingReceipt').attr('value', tanggal);
            console.log(tanggal, $('#tanggalGradingReceipt').val())
        })

        $('.select2').select2({
            dropdownParent: $("#tambah"),
            theme: 'bootstrap4',
        })
</script>

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

        function beratbersih() {
            var berat = document.getElementById("berat").value;

            if (berat != 0) {

                if ($('.keranjang:checked').val()) {
                    var total = berat - $('.keranjang:checked').val();
                } else {
                    var total = 0;
                }

            } else {
                var total = 0;
            }

            $('#jumlah').val(total);
        }
</script>
<script>
    $(document).ready(function() {

            function selectradio() {
                var jen = $('.jenis:checked').val();
                console.log(jen);

                if (jen == 'memar') {
                    $('#jenisayam').load("{{ route('grading.memar', $data->id) }}");
                } else if (jen == 'normal') {
                    $('#jenisayam').load("{{ route('grading.normal', $data->id) }}");
                } else if (jen == 'pejantan') {
                    $('#jenisayam').load("{{ route('grading.pejantan', $data->id) }}");
                } else if (jen == 'parent') {
                    $('#jenisayam').load("{{ route('grading.parent', $data->id) }}");
                } else {
                    $('#jenisayam').load("{{ route('grading.utuh', $data->id) }}");
                }

            }

            $('.jenis').change(function() {

                selectradio();

            });

            // Tambah cart
            $('#add_cart').click(function() {
                var berat = $('#berat').val();
                var x_code = $('#x_code').val();
                var result = $('#result').val();
                var part = $('.part:checked').val();
                var jenis = $('.jenis:checked').val();
                var idedit = $('#idedit').val();
                var keterangan = $('#keterangan').val();
                const tanggalKarkas =  $('#tanggal').val();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $('#add_cart').hide();

                $.ajax({
                    url: "{{ route('grading.add', $data->id) }}",
                    method: "POST",
                    data: {
                        idedit: idedit,
                        berat: berat,
                        x_code: x_code,
                        result: result,
                        part: part,
                        jenis: jenis,
                        keterangan: keterangan,
                        tanggalKarkas: tanggalKarkas,
                    },

                    success: function(data) {
                        if (data.status == 400) {
                            showAlert(data.msg);
                            $('#add_cart').show();
                        } else {
                            $("#prosentase").load(
                                "{{ route('laporan.prosentase', $data->id) }}");
                            $('#add_cart').html("Timbang");
                            $('#cart').load("{{ route('grading.cart', $data->id) }}");
                            $('#kalkulasi').load(
                                "{{ route('grading.kalkulasi', $data->id) }}");
                            $('#hasil').load("{{ route('grading.result', $data->id) }}?receiptUlang={{ $receiptUlang }}");
                            $('#result').val('');
                            $('#idedit').val('');
                            $('#jumlah').val('');
                            $('#berat').val('');
                            $('#keterangan').val('');
                            selectradio();
                            showNotif("Item ditimbang")
                            $('#add_cart').show();
                        }
                    }
                });
            });
        });

        $(document).ready(function() {
            // Menampilkan cart
            $('#cart').load("{{ route('grading.cart', $data->id) }}");
            $('#kalkulasi').load("{{ route('grading.kalkulasi', $data->id) }}");
            $('#hasil').load("{{ route('grading.result', $data->id) }}?receiptUlang={{ $receiptUlang }}");
        });

        $(document).ready(function() {
            $(document).on('click', '.edit_cart', function() {
                var row_id = $(this).data('kode');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ route('grading.edit', $data->id) }}",
                    method: "POST",
                    data: {
                        row_id: row_id
                    },
                    success: function(data) {
                        if (data.jenis_karkas == 'memar') {
                            $('#jenisayam').load(
                                "{{ route('grading.memar', $data->id) }}?select=" + data
                                .item_id);
                        } else if(data.jenis_karkas =='pejantan') {
                            $('#jenisayam').load(
                                "{{ route('grading.pejantan', $data->id) }}?select=" + data
                                .item_id);
                        } else if(data.jenis_karkas =='normal') {
                            $('#jenisayam').load(
                                "{{ route('grading.normal', $data->id) }}?select=" + data
                                .item_id);
                        } else if(data.jenis_karkas =='parent') {
                            $('#jenisayam').load(
                                "{{ route('grading.parent', $data->id) }}?select=" + data
                                .item_id);
                        } else if(data.jenis_karkas =='utuh') {
                            $('#jenisayam').load(
                                "{{ route('grading.utuh', $data->id) }}?select=" + data
                                .item_id);
                        }
                        $('#custom-tabs-three-home-tab').tab('show');
                        $('[name="result"]').val(data.total_item);
                        $('[name="berat"]').val(data.berat_item);
                        $('[name="idedit"]').val(data.id);
                        $('input:radio[name=jenis][value=' + data.jenis_karkas + ']')[0]
                            .checked = true;
                    }
                });
            });
        });


        $(document).ready(function() {
            $(document).on('click', '#input_petugas', function() {
                var nama_petugas = $("#nama_petugas").val();

                if (nama_petugas == '') {
                    showAlert("Nama petugas wajib diisikan");
                } else {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        url: "{{ route('grading.edit', [$data->id, 'key' => 'petugas']) }}",
                        method: "POST",
                        data: {
                            nama_petugas: nama_petugas
                        },
                        success: function(data) {
                            showNotif("Petugas berhasil diperbaharui");
                        }
                    });
                }
            });
        });
</script>

<script>
    $(document).ready(function() {
            $(document).on('click', '.hapus_cart', function() {
                var id = $(this).data('id');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "{{ route('grading.destroy', $data->id) }}",
                    method: "DELETE",
                    data: {
                        id: id
                    },
                    success: function(data) {
                        window.location.reload("{{ route('grading.show', $data->id) }}")
                    }
                });
            });
        });
</script>
@endsection