@extends('admin.layout.template')

@section('title', 'Kepala Produksi')

@section('header')
    <style>


    </style>
@endsection

@section('content')

<div class="row mb-4">
    <div class="col">
        <a href="{{ route('kepalaproduksi.index') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
            Back</a>
    </div>
    <div class="col text-center">
        <b>Bahan Baku Bonless</b>
    </div>
    <div class="col"></div>
</div>

<div class="row">
    <div class="col-6">

        @php
            $idorder = 0;
        @endphp
        @foreach ($bonless as $i => $val)
        @php
            $idorder = $val->id;
        @endphp
            <div class="card card-primary card-outline">
                <div class="card-header">
                    Customer : {{ $val->nama }}
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            @php
                                $qty = 0;
                                $berat = 0;
                            @endphp
                            @foreach (Order::item_order($val->id, 'bonless') as $i => $item)
                                @php
                                    $qty += $item->qty;
                                    $berat += $item->berat;
                                @endphp
                                <div class="form-group radio-toolbar">
                                    <div class="btn-group-toggle" data-toggle="buttons">
                                        <label class="btn btn-block text-left"
                                            for="od-{{ $item->id }}">
                                            <input type="checkbox" autocomplete="off" id="od-{{ $item->id }}"
                                                onclick='' data-jenis='' value="{{ $item->id }}" name="purchase">
                                            {{ $item->nama_detail }} <span class="pull-right"> Qty : <span class="label label-rounded-grey">{{$qty}}</span> Berat : <span class="label label-rounded-grey">{{$berat}}</span></span>
                                        </label>
                                    </div>
                                </div>

                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="col-6">
        <div class="card card-primary card-outline">
            @php
                $idbahan = 0;
            @endphp
            @foreach ($bahanbaku as $j => $bacok)
                @php
                    $idbahan = $bacok->id;
                @endphp
                <div class="card-header">
                    Bahan Baku {{ $bacok->id }}
                </div>
            @endforeach
            <div class="card-body">

                @foreach ($bhnbb as $i => $bb)
                    <div class="radio-toolbar">

                            <label>
                            {{ $bb[0]->item_name }}

                            <div class="pull-right">
                                Qty <span class="label label-rounded-grey">{{ $bb[0]->qty_item ?? "0" }} </span>&nbsp
                                Berat <span class="label label-rounded-grey">{{ $bb[0]->berat_item ?? "0" }} kg </span>
                            </div>
                            </label>
                    </div>
                @endforeach
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block" data-toggle="modal" data-target="#banahbaku">Tambah
            Bahan
            Baku</button>
    </div>
</div>
<br>
<div class="row">
    <div class="col-12">
        <button type="submit" class="btn btn-primary btn-block prosesbonles" data-bahan="{{ $idbahan }}" data-order="{{ $idorder }}">Selesaikan</button>
    </div>
</div>


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
                        <div class="card">
                            <a class="btn btn-link" data-toggle="collapse"
                                data-target="#collapse{{ $row->id }}" aria-expanded="true"
                                aria-controls="collapse{{ $row->id }}">
                                <div class="card-header text-left" id="heading{{ $row->id }}">
                                    {{ $row->nama }}
                                </div>
                            </a>
                            <div id="collapse{{ $row->id }}" class="collapse"
                                aria-labelledby="heading{{ $row->id }}" data-parent="#accordion">
                                <div class="card-body">
                                    @foreach ($row->list_item_bonless as $item)
                                        <div class="border-bottom py-1">
                                            <div class="row">
                                                <div class="col pt-2">{{ $item->tanggal_produksi }}</div>
                                                <div class="col pt-2">{{ $item->stock_item }} ekor</div>
                                                <div class="col pt-2">{{ $item->stock_berat }} kg</div>
                                                <div class="col">
                                                    <input type="hidden" value="{{ $row->id }}" class="ncode"
                                                        name="n_code[]">
                                                    <input type="hidden" value="{{ $item->id }}" class="xcode"
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
                    url: "{{ route('kepalaproduksi.storeboneles') }}",
                    method: "POST",
                    data: {
                        x_code: x_code,
                        qty: qty
                    },
                    success: function(data) {

                        $('#banahbaku').modal('hide');
                        $('.qty').val('');
                        // $("#show").load("{{ route('kepalaproduksi.kepalashow') }}");
                        // $("#show_data").load("{{ route('kepalaproduksi.bahanbakushow') }}");
                        $("#show_bb").load("{{ route('kepalaproduksi.bahanbakubonless') }}");
                        location.reload();

                    }
                });
            });

            $(document).on('click', '.prosesbonles', function() {
                var bahan = $(this).data('bahan');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ route('kepalaproduksi.prosesbonless') }}",
                    method: "POST",
                    data: {
                        bahan: bahan
                    },
                    success: function(data) {
                        $("#show_bb").load("{{ route('kepalaproduksi.bahanbakubonless') }}");
                        location.reload();
                    }
                });
            });
        </script>

@stop
