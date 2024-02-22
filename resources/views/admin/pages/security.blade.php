@extends('admin.layout.template')

@section('title', 'Data Pengiriman Masuk')

@section('header')
    <style>
        .popup-edit-sc {
            z-index: 2000;
        }
    </style>
@endsection

@section('footer')
    <link href="{{asset('plugin')}}/jquery-ui.css" rel="stylesheet">
    <script src="{{asset('plugin')}}/jquery-ui.js"></script>
    {{-- <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script type="text/javascript" src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script> --}}
    <script>
        $(document).ready(function() {
            $(document).on('click', 'input:radio[name=purchase]', function() {
                var tipe = $(this).data('jenis');
                var po = $(this).data('typepo');
                var ukuran = $(this).data('ukuran');
                var pilih = document.getElementById('data_supir');
                var tulis = document.getElementById('tulis_supir');
                var nama_kandang = $('#input_nama_kandang');
                var alamat_kandang = $('#input_alamat_kandang');
                var tidakadasupir = $('#tidakadasupir');
                var sc_no_urut = $('#sc_no_urut');
                var ekor_do = $('#ekor_do');
                var ukuran_do = $('#ukuran_do').val(ukuran);

                if (po == 'PO LB' || po == 'PO Maklon') {
                    sc_no_urut.show();
                } else {
                    sc_no_urut.hide();
                }

                if (tipe == 'kirim') {
                    pilih.style = 'display: none';
                    pilih.name = '';
                    tulis.style = 'display: block';
                    tulis.name = 'supir';
                    nama_kandang.removeClass("background-grey-4");
                    nama_kandang.prop('readonly', false);
                    alamat_kandang.removeClass("background-grey-4");
                    alamat_kandang.prop('readonly', false);
                    tidakadasupir.prop('checked', false);
                } else
                if (tipe == 'other') {
                    pilih.style = 'display: none';
                    pilih.name = '';
                    tulis.style = 'display: block';
                    tulis.name = 'supir';
                    nama_kandang.addClass("background-grey-4");
                    nama_kandang.prop('readonly', false);
                    alamat_kandang.addClass("background-grey-4");
                    alamat_kandang.prop('readonly', false);
                    tidakadasupir.prop('checked', false);
                } else {
                    pilih.style = 'display: block';
                    pilih.name = 'supir';
                    tulis.style = 'display: none';
                    tulis.name = '';
                    nama_kandang.addClass("background-grey-4");
                    nama_kandang.prop('readonly', true);
                    alamat_kandang.addClass("background-grey-4");
                    alamat_kandang.prop('readonly', true);
                    tidakadasupir.prop('checked', false);
                    tidakadasupir.on('change', function() {

                        if (tidakadasupir.prop('checked')) {
                            pilih.style = 'display: none';
                            pilih.name = '';
                            tulis.style = 'display: block';
                            tulis.name = 'supir';
                        } else {
                            pilih.style = 'display: block';
                            pilih.name = 'supir';
                            tulis.style = 'display: none';
                            tulis.name = '';
                        }

                    })
                }
            });
        });

        $("#tulis_supir").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "{{ url('admin/search-driver') }}",
                    data: {
                        cari: request.term
                    },
                    dataType: "json",
                    success: function(data) {
                        console.log(data);
                        response(data);
                    }
                });
            },
            minLength: 1
        });
    </script>
@endsection

@section('content')
    <div class="text-center my-4 text-uppercase"><b>Data Penerimaan Masuk</b></div>

    <section class="panel">
        <div class="card-body">
            <div class="heading-section">

                @if (User::setIjin('superadmin') || $cekEmailProduksi == true)
                    <form action="{{ route('security.index') }}" method="get">
                        <div class="row">
                            <div class="offset-md-8 col-md-4">
                                Pencarian
                                <input type="date"
                                    @if (env('NET_SUBSIDIARY', 'CGL') == 'CGL') onkeydown="return false"
                            min="2023-01-01" @endif
                                    class="form-control change-date" name="tanggal" value="{{ $tanggal }}"
                                    placeholder="Cari...">
                            </div>
                        </div>
                    </form>
                @else
                    @php
                        $tgl_0 = Carbon\Carbon::now()
                            ->add(-1, 'days')
                            ->format('Y-m-d');
                        $now = date('Y-m-d');
                        $tgl_1 = Carbon\Carbon::now()
                            ->add(+1, 'days')
                            ->format('Y-m-d');
                        $data_tgl = [$tgl_0, $now, $tgl_1];
                    @endphp

                    <div style="display: inline-flex">
                        <h6 class="mr-3 mt-1">Tanggal Potong : </h6>
                        @foreach ($data_tgl as $row)
                            <form action="{{ route('security.index') }}" method="get">
                                <button type="submit" name="tanggal" value="{{ $row }}"
                                    class="btn btn{{ $tanggal == $row ? '' : '-outline' }}-primary mr-2"
                                    style="margin-bottom: 5px;">
                                    {{ date('d/m/y', strtotime($row)) }}
                                </button>
                            </form>
                        @endforeach
                    </div>

                @endif
                <hr>
            </div>

            <form action="{{ route('security.store') }}" method="POST" id="simpanSecurity">
                @csrf <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                <div class="row">
                    <div class="col-lg-3 col-md-4 col-sm-5 col-xs-12 pr-sm-1">
                        <div class="scroll-outer mb-4">
                            <div class="scroll-inner">
                                <div class="radio-toolbar">
                                    @foreach ($purchase as $row)
                                    @php 
                                        $cekPurchasingStatus = \App\Models\Purchasing::cekPurchasingStatus($row->id,$row->tanggal_potong);
                                    @endphp
                                    @if($cekPurchasingStatus == 'OK' || $cekPurchasingStatus != '2')
                                        @if ($row->type_ekspedisi == 'kirim')
                                            <div style="padding: 2px !important">
                                                <input type="radio" class="autodata" id="{{ $row->id }}"
                                                    data-jenis='{{ $row->type_ekspedisi }}'
                                                    data-typepo='{{ $row->type_po }}'
                                                    data-ukuran='{{ $row->ukuran_ayam }}' value="{{ $row->id }}"
                                                    name="purchase" {{ old('purchase') == $row->id ? 'checked' : '' }}
                                                    required>
                                                <label class="p-0" for="{{ $row->id }}">
                                                    <div class="btn-block bg-info px-1 text-light text-small">
                                                        Kirim - {{ $row->jumlah_produksi }}</div>
                                                    <div class="p-1">
                                                        <div class="text-small">{{ $row->no_po }}</div>
                                                        {{ $row->purcsupp->nama }} <br>
                                                        {{ $row->ukuran_ayam }}
                                                    </div>
                                                </label>
                                            </div>
                                        @endif

                                        @if ($row->type_ekspedisi == 'tangkap')
                                            <div class="accordion" id="accordionExample">
                                                <div id="headingOne" style="padding: 2px !important">
                                                    <h5 class="mb-0">
                                                        <a class="btn p-0 form-control text-left" data-toggle="collapse"
                                                            data-target="#collapseOne{{ $row->id }}"
                                                            aria-expanded="true" aria-controls="collapseOne">
                                                            @if ($row->type_po == 'PO Maklon')
                                                                <div class="btn-block px-1 text-small text-dark bg-warning">
                                                                    {{ $row->type_po }} - {{ $row->jumlah_produksi }}
                                                                </div>
                                                            @else
                                                                <div class="btn-block px-1 text-small text-light bg-danger">
                                                                    Tangkap - {{ $row->jumlah_produksi }}
                                                                </div>
                                                            @endif
                                                            <div class="p-1">
                                                                <div class="text-small">{{ $row->no_po }}</div>
                                                                {{ $row->purcsupp->nama }} <br>
                                                                {{ $row->ukuran_ayam }}
                                                            </div>
                                                        </a>
                                                    </h5>
                                                </div>

                                                <div id="collapseOne{{ $row->id }}" class="collapse"
                                                    aria-labelledby="headingOne" data-parent="#accordionExample">
                                                    <div class="padding-5">
                                                        <div class="radio-toolbar">
                                                            @foreach (Purchasing::daftar_produksi($row->id) as $i => $row2)
                                                                <input type="hidden" name="production" value="">
                                                                <input type="radio"
                                                                    id="do-{{ $row->id . '-' . $row2->id }}"
                                                                    onclick='return dataSupir("{{ $row2->id }}")'
                                                                    data-ukuran='{{ $row2->prodpur->ukuran_ayam }}'
                                                                    data-jenis='{{ $row2->prodpur->type_ekspedisi }}'
                                                                    data-typepo='{{ $row->type_po }}'
                                                                    data-prod="{{ $row2->id }}"
                                                                    value="{{ $row2->prodpur->id }}" name="purchase"
                                                                    {{ old('purchase') == $row2->prodpur->id ? 'checked' : '' }}
                                                                    required>
                                                                <label for="do-{{ $row->id . '-' . $row2->id }}">
                                                                    DO {{ $i + 1 }} -
                                                                    {{ $row2->proddriver->nama ?? ($row2->sc_pengemudi ?? '') }}
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        @if (count($purchase_lainnya) > 0)
                            <div class="scroll-outer box-border">
                                <div class="scroll-inner">
                                    <div class="radio-toolbar">
                                        @foreach ($purchase_lainnya as $row)
                                            @if (Purchasing::polainnya($row->id) != 0)
                                                <div class="accordion" id="accordionExample">
                                                    <div id="headingOne" style="padding: 2px !important">
                                                        <h5 class="mb-0">
                                                            <a class="btn p-0 form-control text-left" data-toggle="collapse"
                                                                data-target="#collapseOne{{ $row->id }}"
                                                                aria-expanded="true" aria-controls="collapseOne">
                                                                <span
                                                                    class="bg-success btn-block px-1 text-light text-small">{{ $row->jenis_po }}
                                                                    {{ Purchasing::polainnya($row->id) }}</span>
                                                                <div class="p-1">
                                                                    <div class="text-small">{{ $row->no_po }}</div>
                                                                    {{ $row->purcsupp->nama }}<br>
                                                                    {{ substr(\App\Models\Item::item_sku($row->item_po)->nama, 0, 23) ?? '' }}
                                                                </div>
                                                            </a>
                                                        </h5>
                                                    </div>
                                                </div>
                                                <div id="collapseOne{{ $row->id }}" class="collapse"
                                                    aria-labelledby="headingOne" data-parent="#accordionExample">
                                                    <div class="padding-5">
                                                        <div class="radio-toolbar">
                                                            @foreach (Purchasing::daftar_polain($row->id) as $i => $row2)
                                                                <input type="radio"
                                                                    id="do-{{ $row->id . '-' . $row2->id }}"
                                                                    onclick='return dataSupir("{{ $row2->id }}")'
                                                                    data-ukuran='{{ $row2->prodpur->ukuran_ayam }}'
                                                                    data-jenis='{{ $row2->prodpur->type_ekspedisi }}'
                                                                    data-typepo='{{ $row->type_po }}'
                                                                    value="{{ $row2->prodpur->id }}" name="purchase"
                                                                    {{ old('purchase') == $row2->prodpur->id ? 'checked' : '' }}
                                                                    required>
                                                                <label for="do-{{ $row->id . '-' . $row2->id }}">
                                                                    DO {{ $i + 1 }} //
                                                                    {{ \App\Models\Item::item_sku($row->item_po)->nama ?? '' }}
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="col-lg-7 col-md-8 col-sm-7 col-xs-12 pl-sm-1">
                        <div class="box-border">
                            <div class="card-body">
                                <div class="row">

                                    <div class="col-sm-6 pr-sm-1">

                                        <div class="form-group">
                                            <label for="data_supir">Supir/Kernek</label>
                                            <select name="{{ $view ? 'supir' : '' }}" id="data_supir"
                                                style="{{ $view ? '' : 'display:none' }}" class="form-control">
                                                <option value="" disabled selected hidden>Pilih Supir</option>
                                                @foreach ($supir as $id => $row)
                                                    <option value="{{ $id }}"
                                                        {{ $view ? (old('supir') == $id ? 'selected' : '') : '' }}>
                                                        {{ $row }}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" name="supir" class="form-control"
                                                style="{{ $view ? 'display:none' : '' }}" id='tulis_supir'
                                                placeholder="Supir" value="{{ $view ? '' : old('supir') ?? '' }}"
                                                autocomplete="off">
                                            @error('supir')
                                                <div class="small text-danger">{{ $message }}</div>
                                            @enderror

                                            <label class="mt-2 px-2 pt-2 rounded status-info">
                                                <input id="tidakadasupir" type="checkbox"> <label
                                                    for="tidakadasupir">Input
                                                    supir manual</label>
                                            </label>
                                        </div>

                                        <div class="form-group" id="sc_no_urut">
                                            <label for="nourut">Nomor Urut</label>
                                            <input type="number" name="nourut" class="form-control" id="nourut"
                                                placeholder="Nomor Urut "
                                                value="{{ Produksi::nomor_urut($tanggal, 'pending') }}"
                                                autocomplete="off">
                                            <span class="small text-danger">*) Bisa diganti sesuai kebutuhan</span>
                                            @error('nourut')
                                                <div class="small text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="nomor_do">Nomor DO</label>
                                            <input type="number" name="no_do" id="nomor_do" class="form-control"
                                                placeholder="Nomor DO" value="{{ old('no_do') ?? '' }}"
                                                autocomplete="off" required>
                                            @error('no_do')
                                                <div class="small text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="sc_jam_masuk">Jam Masuk</label>
                                            <input type="text" name="sc_jam_masuk" id="sc_jam_masuk"
                                                class="form-control" placeholder="Jam masuk"
                                                value="{{ old('sc_jam_masuk') ?? date('H:m:s') }}" autocomplete="off"
                                                required>
                                            @error('sc_jam_masuk')
                                                <div class="small text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>


                                    </div>

                                    <div class="col-sm-6 pl-sm-1">

                                        <div class="form-group">
                                            <label for="ekor_do">Ekor DO</label>
                                            <input type="hidden" id="ukuran_do">
                                            <input type="number" name="ekor_do" id="ekor_do" class="form-control"
                                                placeholder="Ekor DO" value="{{ old('ekor_do') ?? '' }}"
                                                autocomplete="off">
                                            @error('ekor_do')
                                                <div class="small text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="berat_do">Berat DO</label>
                                            <input type="number" name="berat_do" id="berat_do" step="0.01"
                                                class="form-control" placeholder="Berat DO"
                                                value="{{ old('berat_do') ?? '' }}" autocomplete="off">
                                            @error('berat_do')
                                                <div class="small text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="nomor_polisi">No Polisi</label>
                                            <input type="text" name="no_polisi" id="nomor_polisi"
                                                class="form-control" placeholder="No Polisi"
                                                value="{{ old('no_polisi') ?? '' }}" autocomplete="off"
                                                style="text-transform:uppercase">
                                            @error('no_polisi')
                                                <div class="small text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="input_nama_kandang">Nama Kandang</label>
                                            <input type="text" id="input_nama_kandang" name="nama_kandang"
                                                class="form-control" placeholder="Nama Kandang"
                                                value="{{ old('nama_kandang') ?? '' }}" autocomplete="off"
                                                @if (Session::get('driver') == 'tangkap') readonly @endif>
                                            @error('nama_kandang')
                                                <div class="small text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="input_alamat_kandang">Alamat Kandang</label>
                                            <textarea name="alamat_kandang" id="input_alamat_kandang" class="form-control" placeholder="Tulis Alamat Kandang"
                                                cols="3" @if (Session::get('driver') == 'tangkap') readonly @endif>{{ old('alamat_kandang') }}</textarea>
                                            @error('alamat_kandang')
                                                <div class="small text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                    </div>
                                </div>
                                <div class="form-group mt-3">
                                    <div id="notif-ukuran"></div>
                                    <button type="submit" id="simpan"
                                        class="btn btn-blue form-control">Simpan</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-2 col-12">
                        <div class="row">
                            <div class="col-4 pr-1 pr-md-3 col-sm-4 col-lg-12">
                                <div class="box-border background-grey-1">
                                    <div class="card-body p-2">
                                        No Urut
                                        <h4 class="text-right">{{ Produksi::nomor_urut($tanggal, 'pending') }}</h4>
                                    </div>
                                </div>
                            </div>

                            <div class="col-4 px-1 px-md-3 col-sm-4 col-lg-12">
                                <div class="box-border background-grey-1">
                                    <div class="card-body p-2">
                                        Total DO
                                        <h4 class="text-right">{{ Produksi::hitung_do('total', $tanggal) }}</h4>
                                    </div>
                                </div>
                            </div>

                            <div class="col-4 pl-1 pl-md-3 col-sm-4 col-lg-12">
                                <div class="box-border background-grey-1">
                                    <div class="card-body p-2">
                                        Pending
                                        <h4 class="text-right">{{ Produksi::hitung_do('pending', $tanggal) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </section>

    <section class="panel">
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <a class="nav-item nav-link tab-link" id="tab-id-tab" data-toggle="tab" href="#tab-id" role="tab"
                    aria-controls="tab-id" aria-selected="true">Diterima LB</a>
                <a class="nav-item nav-link tab-link" id="tab-nonlb-tab" data-toggle="tab" href="#tab-nonlb"
                    role="tab" aria-controls="tab-id" aria-selected="true">Diterima Non LB</a>
                <a class="nav-item nav-link tab-link" id="tab-en-tab" data-toggle="tab" href="#tab-en" role="tab"
                    aria-controls="tab-en" aria-selected="false">Pending</a>
            </div>
        </nav>

        <div class="card-body">

            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade mt-3" id="tab-id" role="tabpanel" aria-labelledby="tab-id">
                    <h5>Laporan Diterima LB</h5>
                    <hr>

                    <div class="table-responsive">
                        <table class="table default-table">
                            <thead class="text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Supplier</th>
                                    <th>No DO</th>
                                    <th>Jenis PO</th>
                                    <th>Item</th>
                                    <th>Daerah</th>
                                    <th>Ekspedisi</th>
                                    <th>No Polisi</th>
                                    <th>Supir</th>
                                    <th>Jumlah</th>
                                    <th>Berat</th>
                                    <th>Rerata</th>
                                    <th>Waktu tiba</th>
                                    <th>LPAH</th>
                                    <th>Status</th>
                                    <th>Notif</th>
                                    <th>NoUrut</th>
                                    @if (User::setIjin('superadmin'))
                                        <th>Edit Logs</th>
                                    @endif
                                    <th>Activity</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($diterima as $i => $row)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ $row->prodpur->purcsupp->nama }}
                                            @if ($row->prodpur->tanggal_potong != $row->prod_tanggal_potong)
                                                <br><span class="status status-info">MOBIL LAMA</span>
                                            @endif
                                        </td>
                                        <td>{{ $row->no_do }}</td>
                                        <td>{{ $row->prodpur->type_po }}</td>
                                        <td>
                                            @if ($row->prodpur->ukuran_ayam == '&lt; 1.1')
                                                {{ '<1.1' }}
                                            @else
                                                {{ $row->prodpur->ukuran_ayam }}
                                            @endif
                                        </td>
                                        <td class="text-capitalize">{{ $row->sc_wilayah }}</td>
                                        <td class="text-capitalize">
                                            {{ $row->po_jenis_ekspedisi ?? $row->prodpur->nama_po }}
                                        </td>
                                        <td>{{ $row->sc_no_polisi }}</td>
                                        <td>{{ $row->sc_pengemudi }}</td>
                                        <td>{{ number_format($row->sc_ekor_do) }}</td>
                                        <td>{{ number_format($row->sc_berat_do, 2) }} Kg</td>
                                        <td>{{ number_format($row->sc_rerata_do, 2) }} Kg</td>
                                        <td>{{ date('H:i', strtotime($row->sc_jam_masuk)) }}</td>
                                        <td class="text-center">
                                            <span class="status status-success">Selesai</span>
                                            @if ($row->prod_pending == '1')
                                                <span class="status status-danger">POTunda</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($row->lpah_status == null)
                                                <span class="status status-info">LPAH PENDING</span>
                                            @else
                                                <span class="status status-success">LPAH DIINPUT</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($row->sc_status == '0')
                                                <span class="status status-danger">Dibatalkan</span>
                                            @else
                                                {{-- {!! $row->notif_security !!} --}}
                                                {{ App\Models\Production::setNotifSecurity($row->id) }}
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            {{ $row->no_urut }}
                                        </td>
                                        @if (User::setIjin('superadmin'))
                                            <td>
                                                @foreach ($row->adminedt as $e)
                                                    <li>{{ $e->content }}</li>
                                                @endforeach
                                            </td>
                                        @endif
                                        <td class="text-center">
                                            @if ($row->sc_status != '0')
                                                <a href="" class="btn p-0 btn-link blue" data-toggle="modal"
                                                    data-target="#edit{{ $row->id }}">Edit</a>
                                                @if (User::setIjin('superadmin'))
                                                    <form action="{{ route('security.reset') }}" method="post"
                                                        class="d-inline-block">
                                                        @method('delete')
                                                        @csrf
                                                        <input type="hidden" name="x_code"
                                                            value="{{ $row->id }}">
                                                        {{-- <a href="" class="red" data-toggle="modal"
                                            data-target="#reset{{ $row->id }}">Reset</a> --}}
                                                        <button type="submit"
                                                            class="btn p-0 btn-link text-danger">Reset</button>
                                                    </form>
                                                @endif
                                            @endif
                                            <button class="btn btn-link text-dark" data-toggle="modal"
                                                data-target="#ganti{{ $row->id }}">Tukar Supplier</button>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="ganti{{ $row->id }}" tabindex="-1"
                                        aria-labelledby="ganti{{ $row->id }}Label" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="ganti{{ $row->id }}Label">Tukar
                                                        Supplier</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <form action="{{ route('security.store') }}" method="post">
                                                    @csrf <input type="hidden" name="key" value="tukar_supplier">
                                                    <input type="hidden" name="x_code" value="{{ $row->id }}">
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6 pr-md-1">
                                                                <div class="form-group">
                                                                    Supplier
                                                                    <input type="text" disabled
                                                                        value="{{ $row->prodpur->purcsupp->nama }}"
                                                                        class="form-control">
                                                                </div>

                                                                <div class="form-group">
                                                                    Nomor Urut
                                                                    <input type="text" disabled
                                                                        value="{{ $row->no_urut }}"
                                                                        class="form-control">
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6 pl-md-1">
                                                                Tukar Nomor Urut
                                                                <input type="number" name="no_urut" min="1"
                                                                    autocomplete="off" placeholder="Tulis Nomor Urut"
                                                                    class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary">Save</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="tab-pane fade mt-3" id="tab-nonlb" role="tabpanel" aria-labelledby="tab-npnlb">
                    <h5>Laporan Diterima Non LB</h5>
                    <hr>

                    <div class="table-responsive">
                        <table class="table default-table">
                            <thead class="text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Vendor</th>
                                    <th>Item</th>
                                    <th>DO</th>
                                    <th>Jenis PO</th>
                                    <th>Nama Item</th>
                                    <th>Daerah</th>
                                    <th>Ekspedisi</th>
                                    <th>No Polisi</th>
                                    <th>Supir</th>
                                    <th>Jumlah</th>
                                    <th>Berat</th>
                                    <th>Rerata</th>
                                    <th>Waktu tiba</th>
                                    <th>Status</th>
                                    <th>Notif</th>
                                    @if (User::setIjin('superadmin'))
                                        <th>Edit Logs</th>
                                    @endif
                                    <th>Activity</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($diterimanonlb as $i => $row)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ $row->prodpur->purcsupp->nama }}</td>
                                        <td>
                                            @foreach ($row->prodpur->purchasing_item as $item)
                                                - {{ \App\Models\Item::item_sku($item->item_po)->nama ?? '#' }}<br>
                                                ({{ number_format($item->jumlah_ayam) }} Ekor ||
                                                {{ number_format($item->berat_ayam, 2) }}
                                                Kg)
                                                <br>
                                            @endforeach
                                        </td>
                                        <td>{{ $row->no_do }}</td>
                                        <td>{{ $row->prodpur->type_po }}</td>
                                        <td>
                                            @if ($row->prodpur->ukuran_ayam == '&lt; 1.1')
                                                {{ '<1.1' }}
                                            @else
                                                {{ $row->prodpur->ukuran_ayam }}
                                            @endif
                                        </td>
                                        <td class="text-capitalize">{{ $row->sc_wilayah }}</td>
                                        <td class="text-capitalize">
                                            {{ $row->po_jenis_ekspedisi ?? $row->prodpur->nama_po }}
                                        </td>
                                        <td>{{ $row->sc_no_polisi }}</td>
                                        <td>{{ $row->sc_pengemudi }}</td>
                                        <td>{{ number_format($row->sc_ekor_do) }}</td>
                                        <td>{{ number_format($row->sc_berat_do, 2) }}</td>
                                        <td>{{ number_format($row->sc_rerata_do, 2) }} Kg</td>
                                        <td>{{ date('H:i', strtotime($row->sc_jam_masuk)) }}</td>
                                        <td class="text-center"><span class="status status-success">Selesai</span></td>
                                        <td class="text-center"> {{ App\Models\Production::setNotifSecurity($row->id) }}</td>
                                        @if (User::setIjin('superadmin'))
                                            <td>
                                                @foreach ($row->adminedt as $e)
                                                    <li>{{ $e->content }}</li>
                                                @endforeach
                                            </td>
                                        @endif
                                        <td class="text-center">
                                            <a href="" class="blue" data-toggle="modal"
                                                data-target="#edit{{ $row->id }}">Edit</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>

                @foreach ($diterima as $i => $row)
                    <div class="modal fade popup-edit-sc" id="edit{{ $row->id }}"
                        aria-labelledby="edit{{ $row->id }}Label" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="edit{{ $row->id }}Label">Edit Pengiriman Masuk</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="{{ route('security.update') }}" method="post">
                                    @csrf @method('patch')
                                    <input type="hidden" name="x_code" value="{{ $row->id }}">
                                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">

                                                <div class="form-group">
                                                    <div>Supplier</div>
                                                    {{ $row->prodpur->purcsupp->nama }}
                                                </div>

                                                <div class="form-group">
                                                    Supir/Kernek
                                                    <input type="text" name="supir" class="form-control"
                                                        value="{{ $row->sc_pengemudi }}" autocomplete="off">
                                                </div>

                                                <div class="form-group">
                                                    Nomor Urut
                                                    <input type="number" name="no_urut" class="form-control"
                                                        value="{{ $row->no_urut }}" autocomplete="off">
                                                </div>

                                                <div class="form-group">
                                                    Nomor DO
                                                    <input type="text" name="no_do" class="form-control"
                                                        value="{{ $row->no_do }}" autocomplete="off">
                                                </div>

                                                <div class="form-group">
                                                    Ekor DO
                                                    <input type="number" name="ekor_do" class="form-control"
                                                        value="{{ $row->sc_ekor_do }}" autocomplete="off">
                                                </div>

                                                <div class="form-group">
                                                    Berat DO
                                                    <input type="text" name="berat_do" class="form-control"
                                                        value="{{ $row->sc_berat_do }}" autocomplete="off">
                                                </div>

                                                <div class="form-group">
                                                    Target
                                                    <input type="text" name="target" class="form-control"
                                                        value="{{ $row->sc_pengemudi_target }}" autocomplete="off">
                                                </div>
                                                <div class="form-group">
                                                    Jam Masuk
                                                    <input type="text" name="sc_jam_masuk" class="form-control"
                                                        value="{{ $row->sc_jam_masuk }}" autocomplete="off">
                                                </div>

                                            </div>

                                            <div class="col-md-6">

                                                <div class="form-group">
                                                    <div>Ukuran Ayam</div>
                                                    @if ($row->prodpur->ukuran_ayam == '&lt; 1.1')
                                                        {{ '<1.1' }}
                                                    @else
                                                        {{ $row->prodpur->ukuran_ayam }}
                                                    @endif
                                                </div>

                                                <div class="form-group">
                                                    No Polisi
                                                    <input type="text" name="no_polisi" class="form-control"
                                                        value="{{ $row->sc_no_polisi }}" autocomplete="off">
                                                </div>

                                                <div class="form-group">
                                                    Nama Kandang
                                                    <input type="text" name="nama_kandang"
                                                        class="form-control background-grey-2" placeholder="Nama Kandang"
                                                        value="{{ $row->sc_nama_kandang }}" autocomplete="off">
                                                </div>

                                                <div class="form-group">
                                                    Alamat Kandang
                                                    <textarea name="alamat_kandang" class="form-control background-grey-2" placeholder="Tulis Alamat Kandang"
                                                        cols="3">{{ $row->sc_alamat_kandang }}</textarea>
                                                </div>

                                                <div class="form-group">
                                                    Alasan Perubahan
                                                    <textarea name="alasan" class="form-control background-grey-2" placeholder="Tulis Alasan Perubahan" required
                                                        cols="3"></textarea>
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
                @endforeach

                @foreach ($diterimanonlb as $i => $row)
                    <div class="modal fade popup-edit-sc" id="edit{{ $row->id }}"
                        aria-labelledby="edit{{ $row->id }}Label" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="edit{{ $row->id }}Label">Edit Pengiriman Masuk</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="{{ route('security.update') }}" method="post">
                                    @csrf @method('patch')
                                    <input type="hidden" name="x_code" value="{{ $row->id }}">
                                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">

                                                <div class="form-group">
                                                    <div>Supplier</div>
                                                    {{ $row->prodpur->purcsupp->nama }}
                                                </div>

                                                <div class="form-group">
                                                    Supir/Kernek
                                                    <input type="text" name="supir" class="form-control"
                                                        placeholder="Supir" value="{{ $row->sc_pengemudi }}"
                                                        autocomplete="off">
                                                </div>

                                                <div class="form-group">
                                                    Nomor DO
                                                    <input type="text" name="no_do" class="form-control"
                                                        placeholder="Nomor DO" value="{{ $row->no_do }}"
                                                        autocomplete="off">
                                                </div>

                                                <div class="form-group">
                                                    Ekor DO
                                                    <input type="number" name="ekor_do" class="form-control"
                                                        placeholder="Ekor DO" value="{{ $row->sc_ekor_do }}"
                                                        autocomplete="off">
                                                </div>

                                                <div class="form-group">
                                                    Berat DO
                                                    <input type="text" name="berat_do" class="form-control"
                                                        placeholder="Berat DO" value="{{ $row->sc_berat_do }}"
                                                        autocomplete="off">
                                                </div>
                                            </div>

                                            <div class="col-md-6">

                                                <div class="form-group">
                                                    No Polisi
                                                    <input type="text" name="no_polisi" class="form-control"
                                                        placeholder="No Polisi" value="{{ $row->sc_no_polisi }}"
                                                        autocomplete="off">
                                                </div>

                                                <div class="form-group">
                                                    Nama Kandang
                                                    <input type="text" name="nama_kandang"
                                                        class="form-control background-grey-2" placeholder="Nama Kandang"
                                                        value="{{ $row->sc_nama_kandang }}" autocomplete="off">
                                                </div>

                                                <div class="form-group">
                                                    Alamat Kandang
                                                    <textarea name="alamat_kandang" class="form-control background-grey-2" placeholder="Tulis Alamat Kandang"
                                                        cols="3">{{ $row->sc_alamat_kandang }}</textarea>
                                                </div>

                                                <div class="form-group">
                                                    Alasan Perubahan
                                                    <textarea name="alasan" class="form-control background-grey-2" placeholder="Tulis Alasan Perubahan" required
                                                        cols="3"></textarea>
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
                @endforeach
                <div class="tab-pane fade mt-3" id="tab-en" role="tabpanel" aria-labelledby="tab-en">
                    <h5>Laporan Pending</h5>
                    <hr>
                    <div class="table-responsive">
                        <table class="table default-table">
                            <thead class="text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Supplier</th>
                                    <th>Ukuran Ayam</th>
                                    <th>Daerah</th>
                                    <th>Ekspedisi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($pending as $i => $row)
                                @php 
                                    $cekProductionStatus = \App\Models\Production::cekProductionStatus($row->id,$row->prod_tanggal_potong);
                                @endphp
                                @if($cekProductionStatus == 'OK' || $cekProductionStatus != NULL)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ $row->prodpur->purcsupp->nama }}</td>
                                        <td>
                                            @if ($row->prodpur->ukuran_ayam == '&lt; 1.1')
                                                {{ '<1.1' }}
                                            @else
                                                {{ $row->prodpur->ukuran_ayam }}
                                            @endif
                                        </td>
                                        <td class="text-capitalize">{{ $row->sc_wilayah }}</td>
                                        <td class="text-capitalize">{{ $row->po_jenis_ekspedisi ?? $row->nama_po }}</td>
                                        <td class="text-center"><span class="status status-danger">Pending</span></td>
                                    </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </section>

    <script>
        $("#data_supir").on('change', function() {
            var supir = $(this).val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('security.index') }}",
                method: "GET",
                data: {
                    id: supir,
                    'key': 'supir'
                },
                success: function(data) {
                    $("#nomor_polisi").val(data.no_polisi);
                }
            });
        })
    </script>

    <script>
        function dataSupir(result) {
            var row_id = result;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('security.edit') }}",
                method: "PUT",
                data: {
                    row_id: row_id
                },
                success: function(data) {
                    $('[name="production"]').val(result);
                    $('#tidakadasupir').prop('checked', false);
                    $('[name="supir"]').val(data.pengemudi);
                    $('[name="berat_do"]').val(data.berat_do);
                    $('[name="ekor_do"]').val(data.ekor_do);
                    $('[name="no_polisi"]').val(data.no_polisi);
                    $('[name="nama_kandang"]').val(data.nama_kandang);
                    $('[name="alamat_kandang"]').val(data.alamat_kandang);
                }
            });
        }

        $(".autodata").on('click', function() {
            var auto_id = $(this).attr('id');
            var auto_jenis = $(this).data('jenis');
            var auto_typepo = $(this).data('typepo');
            var auto_ukuran = $(this).data('ukuran');
            if (auto_jenis == 'kirim') {
                $.ajax({
                    url: "{{ route('security.autocomplete') }}",
                    type: "POST",
                    cache: false,
                    dataType: "json",
                    data: {
                        key: "securityautocomplete",
                        "_token": "{{ csrf_token() }}",
                        auto_id: auto_id
                    },
                    success: function(data) {
                        console.log(data)
                        $('[name="nama_kandang"]').val(data.sc_nama_kandang);
                        $('[name="alamat_kandang"]').val(data.sc_alamat_kandang);
                    }
                })
            }
        })
    </script>

    <script>
        var hash = window.location.hash.substr(1);
        var href = window.location.href;

        deafultPage();

        function deafultPage() {
            if (hash == undefined || hash == "") {
                hash = "tab-id";
            }

            $('#' + hash + '-tab').addClass('active').siblings().removeClass('active');
            $('#' + hash).addClass('active show').siblings().removeClass('active show');

        }


        $('.tab-link').click(function(e) {
            e.preventDefault();
            status = $(this).attr('aria-controls');
            window.location.hash = status;
            href = window.location.href;

        });
    </script>


    <script>
        $('.change-date').change(function() {
            $(this).closest("form").submit();
        });
    </script>

    <script>
        var ekor_do = 0;
        var berat_do = 0;
        var rata_do = 0;

        $('#ekor_do').on('keyup', function() {
            hitung_rata_do()
        })

        $('#berat_do').on('keyup', function() {
            hitung_rata_do()
        })

        function hitung_rata_do() {

            ekor_do = $('#ekor_do').val();
            berat_do = $('#berat_do').val();
            ukuran_string = $('#ukuran_do').val();

            console.log(berat_do + " - " + ekor_do);

            ukuran_array = ukuran_string.split(' - ');
            console.log(ukuran_array);

            if ((berat_do != '' || berat_do != 0) && (ekor_do != '' || ekor_do != 0)) {
                rata_do = berat_do / ekor_do;

                console.log('rata :'.rata_do);

                if (ukuran_array[0] != undefined && ukuran_array[1] != undefined) {
                    if ((ukuran_array[0] >= rata_do) && (rata_do <= ukuran_array[1])) {
                        $('#notif-ukuran').html("<div class='alert alert-danger'>Rata-rata DO adalah : " + (rata_do.toFixed(2)) + " TIDAK SESUAI DENGAN PO apakah tetap diproses?</div>");
                    } else {
                        $('#notif-ukuran').html("<div class='alert alert-success'>Rata-rata DO adalah : " + (rata_do.toFixed(2)) + " SESUAI PO</div>");
                    }
                } else {
                    if (ukuran_array[1] == undefined) {
                        if (parseFloat(ukuran_array[0]) >= (rata_do * 10)) {
                            $('#notif-ukuran').html("<div class='alert alert-danger'>Rata-rata DO adalah : " + (rata_do.toFixed(2)) + " TIDAK SESUAI DENGAN PO apakah tetap diproses?</div>");
                        } else {
                            $('#notif-ukuran').html("<div class='alert alert-success'>Rata-rata DO adalah : " + (rata_do.toFixed(2)) + " SESUAI</div>");
                        }
                    }
                }

            } else {
                $('#notif-ukuran').html("");
            }
        }
    </script>

    <script>
        const btnSimpan = document.getElementById('simpan');
        btnSimpan.addEventListener('click', e => {
            e.preventDefault();
            btnSimpan.style.display = 'none';
            if (btnSimpan.style.display == 'none') {
                document.getElementById('simpanSecurity').submit();
            }
        })
    </script>

    <style>
        .text-small {
            font-size: 8pt;
        }
    </style>
@stop
