@extends('admin.layout.template')

@section('title', 'Proses Timbang Produksi Evis')

@section('content')

<div class="row mb-3">
    <div class="col">
        <a href="{{ route('evis.index') }}" class="btn btn-outline btn-sm btn-back">
            <i class="fa fa-arrow-left"></i>Back
        </a>
    </div>
    <div class="col-6 py-1 text-center">
        <b>PROSES TIMBANG PRODUKSI EVIS</b>
    </div>
    <div class="col"></div>
</div>

<form action="#" method="GET">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                Pencarian
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                    @endif name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" id="pencarian"
                    placeholder="Cari...." autocomplete="off">
            </div>
        </div>
    </div>
</form>

<div id="bahanbakudancuk"></div>

<section class="panel">
    <div class="row">
        <div class="col-12 col-sm-12">
            <div class="card card-primary card-outline card-tabs">
                <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="custom-tabs-three-home-tab" data-toggle="pill"
                            href="#custom-tabs-three-home" role="tab" aria-controls="custom-tabs-three-home"
                            aria-selected="true">Timbang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="custom-tabs-three-profile-tab" data-toggle="pill"
                            href="#custom-tabs-three-profile" role="tab" aria-controls="custom-tabs-three-profile"
                            aria-selected="false">Summary</a>
                    </li>
                </ul>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-three-tabContent">
                        <div class="tab-pane fade show active" id="custom-tabs-three-home" role="tabpanel"
                            aria-labelledby="custom-tabs-three-home-tab">
                            <div class="row">
                                <div class="col-lg-4 col-12">
                                    <div class="form-group row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                {{-- <label>No Urut Mobil</label> --}}

                                                <input type="hidden" name="jenis" id="jenis" value="gabungan">
                                                <input type="hidden" name="idedit" id="idedit" value="">
                                                <input type="hidden" class="form-control" name="no_truck" id="no_truk"
                                                    value="Gabungan" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <label>Total Item</label>
                                            <div class="my-3">
                                                <input type="text" id="result" name="result"
                                                    class="form-control label-timbang" readonly>
                                            </div>
                                            <div class="row my-3">
                                                <div class="col pr-3">
                                                    <input type="button"
                                                        class="btn btn-default btn-block form-control tits-calculator"
                                                        value="7" onclick="dis('7')" />
                                                </div>
                                                <div class="col px-1">
                                                    <input type="button"
                                                        class="btn btn-default btn-block form-control tits-calculator"
                                                        value="8" onclick="dis('8')" />
                                                </div>
                                                <div class="col pl-3">
                                                    <input type="button"
                                                        class="btn btn-default btn-block form-control tits-calculator"
                                                        value="9" onclick="dis('9')" />
                                                </div>
                                            </div>
                                            <div class="row my-3">
                                                <div class="col pr-3">
                                                    <input type="button"
                                                        class="btn btn-default btn-block form-control tits-calculator"
                                                        value="4" onclick="dis('4')" />
                                                </div>
                                                <div class="col px-1">
                                                    <input type="button"
                                                        class="btn btn-default btn-block form-control tits-calculator"
                                                        value="5" onclick="dis('5')" />
                                                </div>
                                                <div class="col pl-3">
                                                    <input type="button"
                                                        class="btn btn-default btn-block form-control tits-calculator"
                                                        value="6" onclick="dis('6')" />
                                                </div>
                                            </div>
                                            <div class="row my-3">
                                                <div class="col pr-3">
                                                    <input type="button"
                                                        class="btn btn-default btn-block form-control tits-calculator"
                                                        value="1" onclick="dis('1')" />
                                                </div>
                                                <div class="col px-1">
                                                    <input type="button"
                                                        class="btn btn-default btn-block form-control tits-calculator"
                                                        value="2" onclick="dis('2')" />
                                                </div>
                                                <div class="col pl-3">
                                                    <input type="button"
                                                        class="btn btn-default btn-block form-control tits-calculator"
                                                        value="3" onclick="dis('3')" />
                                                </div>
                                            </div>
                                            <div class="row my-3">
                                                <div class="col pr-3">
                                                    <input type="button"
                                                        class="btn btn-default btn-block form-control tits-calculator"
                                                        value="Clear" onclick="clr()" />
                                                </div>
                                                <div class="col px-1">
                                                    <input type="button"
                                                        class="btn btn-default btn-block form-control tits-calculator"
                                                        value="0" onclick="dis('0')" />
                                                </div>
                                                <div class="col pl-3">
                                                    <input type="button"
                                                        class="btn btn-default btn-block form-control tits-calculator"
                                                        value="." onclick="dis('.')" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <div class="form-group row">
                                                <div class="col-sm-12">
                                                    <label>Berat Item</label>
                                                    <div class="my-3">
                                                        <input type="text" id="berat" name="berat"
                                                            class="form-control label-timbang" readonly>
                                                    </div>
                                                    <div class="row my-3">
                                                        <div class="col pr-3">
                                                            <input type="button" style="font-size: 23px"
                                                                class="btn btn-default btn-block form-control" value="7"
                                                                onclick="disberat('7')" />
                                                        </div>
                                                        <div class="col px-1">
                                                            <input type="button" style="font-size: 23px"
                                                                class="btn btn-default btn-block form-control" value="8"
                                                                onclick="disberat('8')" />
                                                        </div>
                                                        <div class="col pl-3">
                                                            <input type="button" style="font-size: 23px"
                                                                class="btn btn-default btn-block form-control" value="9"
                                                                onclick="disberat('9')" />
                                                        </div>
                                                    </div>
                                                    <div class="row my-3">
                                                        <div class="col pr-3">
                                                            <input type="button" style="font-size: 23px"
                                                                class="btn btn-default btn-block form-control" value="4"
                                                                onclick="disberat('4')" />
                                                        </div>
                                                        <div class="col px-1">
                                                            <input type="button" style="font-size: 23px"
                                                                class="btn btn-default btn-block form-control" value="5"
                                                                onclick="disberat('5')" />
                                                        </div>
                                                        <div class="col pl-3">
                                                            <input type="button" style="font-size: 23px"
                                                                class="btn btn-default btn-block form-control" value="6"
                                                                onclick="disberat('6')" />
                                                        </div>
                                                    </div>
                                                    <div class="row my-3">
                                                        <div class="col pr-3">
                                                            <input type="button" style="font-size: 23px"
                                                                class="btn btn-default btn-block form-control" value="1"
                                                                onclick="disberat('1')" />
                                                        </div>
                                                        <div class="col px-1">
                                                            <input type="button" style="font-size: 23px"
                                                                class="btn btn-default btn-block form-control" value="2"
                                                                onclick="disberat('2')" />
                                                        </div>
                                                        <div class="col pl-3">
                                                            <input type="button" style="font-size: 23px"
                                                                class="btn btn-default btn-block form-control" value="3"
                                                                onclick="disberat('3')" />
                                                        </div>
                                                    </div>
                                                    <div class="row my-3">
                                                        <div class="col pr-3">
                                                            <input type="button" style="font-size: 23px"
                                                                class="btn btn-red btn-block form-control" value="C"
                                                                onclick="clrberat()" />
                                                        </div>
                                                        <div class="col px-1">
                                                            <input type="button" style="font-size: 23px"
                                                                class="btn btn-default btn-block form-control" value="0"
                                                                onclick="disberat('0')" />
                                                        </div>
                                                        <div class="col pl-3">
                                                            <input type="button" style="font-size: 23px"
                                                                class="btn btn-default btn-block form-control" value="."
                                                                onclick="disberat('.')" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8 col-12">
                                    <a href="javascript:void(0)" class="btn btn-neutral"
                                        onclick="return selectCat('Broiler')">Broiler</a>
                                    <a href="javascript:void(0)" class="btn btn-neutral"
                                        onclick="return selectCat('Parent')">Parent</a>
                                    <a href="javascript:void(0)" class="btn btn-neutral"
                                        onclick="return selectCat('Kampung')">Kampung</a>
                                    <a href="javascript:void(0)" class="btn btn-neutral"
                                        onclick="return selectCat('Pejantan')">Pejantan</a>
                                    <hr>
                                    <input type="text" id="evis-search" class="form-control" placeholder="search evis"
                                        style="margin-bottom: 15px">
                                    <div class="form-group radio-toolbar row">
                                        @foreach ($item as $i => $item)
                                        <div class="col-md-3 evis-name">
                                            <div class="form-group">
                                                <input type="radio" name="part" class="part" value="{{ $item->id }}"
                                                    id="{{ $item->id }}">
                                                <label for="{{ $item->id }}">{{ $item->nama }}</label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>

                                    <script>
                                        function selectCat(category){

                                                $('#evis-search').val(category);
                                                count = 0;
                                                    $('.evis-name').each(function() {
                                                        if ($(this).text().search(new RegExp(category, "i")) < 0) {
                                                            $(this).hide();
                                                        } else {
                                                            $(this).show();
                                                            count++;
                                                        }

                                                    });

                                            }

                                            $('#evis-search').on('keyup', function(){
                                                var filter = $(this).val(),
                                                count = 0;
                                                $('.evis-name').each(function() {
                                                    if ($(this).text().search(new RegExp(filter, "i")) < 0) {
                                                        $(this).hide();
                                                    } else {
                                                        $(this).show();
                                                        count++;
                                                    }

                                                });
                                            })
                                    </script>

                                    <style>
                                        @media screen and (min-width: 768px) {
                                            .evis-name {
                                                min-width: 170px;
                                            }
                                        }

                                        @media screen and (max-width: 768px) {
                                            .evis-name {
                                                width: auto;
                                            }
                                        }
                                    </style>

                                    <h6>Peruntukan</h6>
                                    <div class="form-group radio-toolbar row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="radio" name="peruntukan" class="peruntukan"
                                                    value="evissampingan" id="jualsampingan">
                                                <label for="jualsampingan">Jual Sampingan</label>

                                                <input type="radio" name="peruntukan" class="peruntukan"
                                                    value="evisstock" id="stock">
                                                <label for="stock">Stock</label>

                                                <input type="radio" name="peruntukan" class="peruntukan"
                                                    value="evismusnahkan" id="musnahkan">
                                                <label for="musnahkan">Musnahkan</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="radio" name="peruntukan" class="peruntukan"
                                                    value="eviskiriman" id="kiriman">
                                                <label for="kiriman">Kiriman</label>

                                                <input type="radio" name="peruntukan" class="peruntukan"
                                                    value="eviskaryawan" id="karyawan">
                                                <label for="karyawan">Karyawan</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group text-right">
                                        <button type="button" id='add_cart'
                                            class="btn btn-primary btn-block">Timbang</button>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="tab-pane fade" id="custom-tabs-three-profile" role="tabpanel"
                            aria-labelledby="custom-tabs-three-profile-tab">
                            <div class="table-responsive">
                                <div id="gabung"></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="panel">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="form-group row">
                    <div class="col-sm-2 col-4">
                        <div class="form-group">
                            <label>Total Proses</label>
                            <div class="input-group input-group-lg">
                                <input type="text" style=" text-align: right;"
                                    value="{{ number_format($total['jumlah']) }}" name="proses" class="form-control"
                                    id="proses" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2 col-4">
                        <div class="form-group">
                            <label>Total Ekor</label>
                            <div class="input-group input-group-lg">
                                <input type="text" style="text-align: right;"
                                    value="{{ number_format($total['ekor']) }}" name="ekor" class="form-control"
                                    id="ekor" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2 col-4">
                        <div class="form-group">
                            <label>Total Berat</label>
                            <div class="input-group input-group-lg">
                                <input type="text" style="text-align: right;"
                                    value="{{ number_format($total['berat'], 2) }}" name="totalberat"
                                    class="form-control" id="totalerat" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label></label>
                            <form action="{{ route('evis.updategabung') }}" method="POST">
                                @csrf @method('patch')
                                <button type="submit" class="btn-lg mt-1 btn btn-primary btn-block">Selesaikan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


@stop
@section('footer')
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
            $("#gabung").load("{{ route('evis.cartgabung') }}");
            $("#bahanbakudancuk").load("{{ route('evis.cartbahanbaku') }}");

            $('#pencarian').on('change', function(){
                var tanggal = $(this).val();
                console.log("{{url('admin/evis/gabung/bahanbaku?tanggal=')}}"+tanggal);
                $("#bahanbakudancuk").load("{{url('admin/evis/gabung/bahanbaku?tanggal=')}}"+tanggal);
            })

            // Tambah cart
            $('#add_cart').click(function() {
                var berat = $('#berat').val();
                var result = $('#result').val();
                var part = $('.part:checked').val();
                var peruntukan = $('.peruntukan:checked').val();
                var jumlah_keranjang = $('.keranjang:checked').val();
                var jenis = $('#jenis').val();
                var idedit = $('#idedit').val();
                var item = new Array();

                $('.purchase:checked').each(function() {
                    item.push(this.value);
                });

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ route('evis.addgabung') }}",
                    method: "POST",
                    data: {
                        idedit: idedit,
                        berat: berat,
                        result: result,
                        part: part,
                        peruntukan: peruntukan,
                        jumlah_keranjang: jumlah_keranjang,
                        jenis: jenis,
                        item: item
                    },
                    success: function(data) {
                        $('#result').val('').focus();;
                        $('#jumlah').val('');
                        $('#berat').val('');
                        $('input[type="radio"]').prop('checked', false);
                        $('#custom-tabs-three-profile-tab').tab('show');
                        $('#gabung').load("{{ route('evis.cartgabung') }}");
                        $("#bahanbakudancuk").load("{{ route('evis.cartbahanbaku') }}");
                    }
                });
            });

            // Edit cart
            $(document).on('click', '.edit_cart', function() {
                var row_id = $(this).data('kode');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ route('evis.gabungedit') }}",
                    method: "POST",
                    data: {
                        row_id: row_id
                    },
                    success: function(data) {
                        $('#custom-tabs-three-home-tab').tab('show');
                        $('[name="result"]').val(data.total_item);
                        $('[name="idedit"]').val(data.id);
                        $('input:radio[name=part][value=' + data.item_id + ']')[0].checked =
                            true;
                        $('input:radio[name=peruntukan][value=' + data.peruntukan + ']')[0]
                            .checked = true;
                        $('[name="jumlah"]').val(data.berat_item);
                        $('input:radio[name=jumlah_keranjang][value=' + data.keranjang + ']')[0]
                            .checked = true;
                        document.getElementById('add_cart').innerHTML = 'Ubah';

                    }
                });
            });
        });

</script>
@endsection