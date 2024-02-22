@extends('admin.layout.template')

@section('title', 'Kepala Regu')

@section('content')
    <div class="row mb-4">
        <div class="col">
            <a href="{{ route('kepalaregu.boneles') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
                Back
            </a>
        </div>
        <div class="col text-center">
            <b>Preparation Kepala Regu Bonless</b>
        </div>
        <div class="col"></div>
    </div>
    <section class="panel">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            Bahan Baku
                        </div>
                        <div id="databahan"></div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block" data-toggle="modal" data-target="#banahbaku">Tambah
                        Bahan Baku
                    </button>
                </div>
                <div class="col-md-6">

                    @php
                        $idorder = 0;
                    @endphp
                    @foreach ($data as $i => $val)
                        @php
                            $idorder = $val->id;
                        @endphp

                        <input type="hidden" name="idfreestock" class="form-control" id="idfreestock"
                            placeholder="Tuliskan " value="{{ $idorder }}" autocomplete="off">

                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                Stock Bahan Baku Boneless
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group radio-toolbar">
                                            <div class="row">
                                                <div class="col-4 pr-1">
                                                    <div class="form-group">
                                                        <input type="radio" name="jenis" value="broiler" class="jenis" id="broiler">
                                                        <label for="broiler">Broiler</label>
                                                    </div>
                                                </div>
                                                <div class="col-4 px-1">
                                                    <div class="form-group">
                                                        <input type="radio" name="jenis" value="pejantan" class="jenis" id="pejantan">
                                                        <label for="pejantan">Pejantan</label>
                                                    </div>
                                                </div>
                                                <div class="col-4 pl-1">
                                                    <div class="form-group">
                                                        <input type="radio" name="jenis" value="kampung" class="jenis" id="kampung">
                                                        <label for="kampung">Kampung</label>

                                                    </div>
                                                </div>
                                                <div class="col-4 pr-1">
                                                    <div class="form-group">
                                                        <input type="radio" name="jenis" value="parent" class="jenis" id="parent">
                                                        <label for="parent">Parent</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="jenisayam"></div>
                                        <hr>
                                        <div id="temporary"></div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <br>
            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-block prosesbonles"
                        data-bb="{{ $idorder }}">Selesaikan</button>
                </div>
            </div>
        </div>
    </section>

    <section class="panel">
        <div class="card-body">
            <h4>Hasil Produksi</h4>
            <div id="hasilproduksibonless"></div>
        </div>
    </section>


    <div class="modal fade" id="banahbaku" tabindex="-1" role="dialog" aria-labelledby="banahbakuLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="banahbakuLabel">Stock Bahan Baku</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @foreach ($bahanbonles as $i => $row)
                        <div class="accordion" id="accordion">
                            <a class="btn btn-link p-2 bg-light border-bottom mt-2 w-100" data-toggle="collapse" data-target="#collapse{{ $row->id }}" aria-expanded="true" aria-controls="collapse{{ $row->id }}">
                                <div class="text-left" id="heading{{ $row->id }}">
                                    {{ $row->nama }}
                                </div>
                            </a>
                            <div id="collapse{{ $row->id }}" class="collapse" aria-labelledby="heading{{ $row->id }}" data-parent="#accordion">
                                <div class="p-2">
                                    @foreach ($row->list_item_bonless as $item)
                                        <div class="border-bottom py-1">
                                            <div class="row">
                                                <div class="col pr-1 pt-2">{{ $item->tanggal_produksi }}</div>
                                                <div class="col px-1 pt-2">{{ $item->stock_item }} ekor</div>
                                                <div class="col px-1 pt-2">{{ $item->stock_berat }} kg</div>
                                                <div class="col-auto pl-1">
                                                    <input type="hidden" value="{{ $row->id }}" class="ncode" name="n_code[]">
                                                    <input type="hidden" value="{{ $item->id }}" class="xcode" name="x_code[]">
                                                    <input type="number" style="width: 100px" min="0" name="qty[]" class="qty" id="qty" class="form-control form-control-sm">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="form-group">
                        <hr>
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                Free Stock
                            </div>
                            <div class="card-body">
                                @foreach ($free as $i => $free)
                                    <div class="border-bottom py-1">
                                        <div class="row">
                                            <div class="col pt-2">{{ $free->chillitem->nama }}</div>
                                            <div class="col pt-2">{{ $free->tanggal_potong }}</div>
                                            <div class="col pt-2">{{ $free->stock_item }} ekor</div>
                                            <div class="col pt-2">{{ $free->stock_berat }} kg</div>
                                            <div class="col">
                                                <input type="hidden" value="{{ $free->id }}" class="xcode"
                                                    name="x_code[]">
                                                <input type="number" min="0" name="qty[]" class="qty" id="qty"
                                                    class="form-control form-control-sm">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary bahanbaku">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $("#temporary").load("{{ route('kepalaregu.temporary') }}");
        $("#databahan").load("{{ route('kepalaregu.databahan') }}");
        $("#hasilproduksibonless").load("{{ route('kepalaregu.hasilproduksibonless') }}");

        $(document).on('change', '.jenis', function() {
            var jen = $('.jenis:checked').val();
            console.log(jen);

            if (jen == 'broiler') {
                $('#jenisayam').load("{{ route('kepalaregu.broiler') }}");
            } else if (jen == 'pejantan') {
                $('#jenisayam').load("{{ route('kepalaregu.pejantan') }}");
            } else if (jen == 'kampung') {
                $('#jenisayam').load("{{ route('kepalaregu.kampung') }}");
            } else if (jen == 'parent') {
                $('#jenisayam').load("{{ route('kepalaregu.parent') }}");
            }
        });

        $(document).on('click', '.bahanbaku', function() {
            var xcode = document.getElementsByClassName("xcode");
            var ncode = $('.ncode').val();
            var qty = $('#qty').val();
            var DB_nom = document.getElementsByClassName("qty");
            var qty = [];
            var x_code = [];

            for (var i = 0; i < DB_nom.length; ++i) {
                x_code.push(xcode[i].value);
                qty.push(DB_nom[i].value);
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            console.log(qty);
            console.log(x_code);
            $.ajax({
                url: "{{ route('kepalaregu.storeboneles') }}",
                method: "POST",
                data: {
                    x_code: x_code,
                    qty: qty
                },
                success: function(data) {

                    $('#banahbaku').modal('hide');
                    $('.qty').val('');
                    $("#show_bb").load("{{ route('kepalaregu.bahanbakubonless') }}");
                    $("#databahan").load("{{ route('kepalaregu.databahan') }}");
                    location.reload();

                }
            });
        });

        $(document).on('click', '.prosesbonles', function() {
            var bahan = $(this).data('bb');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('kepalaregu.prosesbonless') }}",
                method: "POST",
                data: {
                    bahan: bahan
                },
                success: function(data) {
                    $("#show_bb").load("{{ route('kepalaregu.bahanbakubonless') }}");
                    $("#databahan").load("{{ route('kepalaregu.databahan') }}");
                    $("#temporary").load("{{ route('kepalaregu.temporary') }}");
                    $("#hasilproduksibonless").load("{{ route('kepalaregu.hasilproduksibonless') }}");
                    // location.reload();
                }
            });
        });

        $(document).on('click', '.input_freebonles', function() {
            var idfree = $('#idfreestock').val();
            var itemfree = $("#itemfree").val();
            var asulah = $("#asulah").val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('kepalaregu.bonelessreestockstore') }}",
                method: "POST",
                data: {
                    item: itemfree,
                    qty: asulah,
                    id: idfree
                },
                success: function(data) {
                    $("#temporary").load("{{ route('kepalaregu.temporary') }}");
                    $("#itemfree").val('');
                    $("#asulah").val('');
                }
            });
        })

        $(document).on('click', '.del_temporary', function() {
            var row_id = $(this).data('id');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('kepalaregu.bonelessfreestockdelete') }}",
                method: "POST",
                data: {
                    row_id: row_id
                },
                success: function(data) {
                    $("#temporary").load("{{ route('kepalaregu.temporary') }}");
                }
            });
        })

    </script>



@stop
