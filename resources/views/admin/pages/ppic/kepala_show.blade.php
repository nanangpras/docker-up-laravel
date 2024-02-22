<section class="panel">
    <div class="row">
        <div class="col-12 col-sm-12">
            <div class="card card-primary card-outline card-tabs">
                <ul class="nav nav-tabs" id="custom-tabs-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link tab-link" id="custom-tabs-orders-tab" data-toggle="pill"
                            href="#custom-tabs-orders" role="tab" aria-controls="custom-tabs-orders"
                            aria-selected="true">Orders</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link tab-link" id="custom-tabs-pending-tab" data-toggle="pill"
                            href="#custom-tabs-pending" role="tab" aria-controls="custom-tabs-pending"
                            aria-selected="false">Order Pending</a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link tab-link" id="custom-tabs-bahanbaku-tab" data-toggle="pill"
                            href="#custom-tabs-bahanbaku" role="tab" aria-controls="custom-tabs-bahanbaku"
                            aria-selected="false">Bahan Baku</a>
                    </li> -->
                    {{-- <li class="nav-item">
                        <a class="nav-link tab-link" id="custom-tabs-fullfilment-tab" data-toggle="pill"
                            href="#custom-tabs-fullfilment" role="tab" aria-controls="custom-tabs-fullfilment"
                            aria-selected="false">Order Fullfilment</a>
                    </li> --}}
                    <li class="nav-item">
                        <a class="nav-link tab-link" id="custom-tabs-chiller-tab" data-toggle="pill"
                            href="#custom-tabs-chiller" role="tab" aria-controls="custom-tabs-chiller"
                            aria-selected="false">Sisa Chiller</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link tab-link" id="custom-tabs-chiller-fg-tab" data-toggle="pill"
                            href="#custom-tabs-chiller-fg" role="tab" aria-controls="custom-tabs-chiller-fg"
                            aria-selected="false">FG Chiller</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link tab-link" id="custom-tabs-chiller-penyiapan-tab" data-toggle="pill"
                            href="#custom-tabs-chiller-penyiapan" role="tab"
                            aria-controls="custom-tabs-chiller-penyiapan" aria-selected="false">Chiller Penyiapan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link tab-link" id="custom-tabs-evaluasi-tab" data-toggle="pill"
                            href="#custom-tabs-evaluasi" role="tab" aria-controls="custom-tabs-evaluasi"
                            aria-selected="false">Evaluasi Produksi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link tab-link" id="custom-tabs-gudang-tab" data-toggle="pill"
                            href="#custom-tabs-gudang" role="tab" aria-controls="custom-tabs-gudang"
                            aria-selected="false">Gudang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link tab-link" id="custom-tabs-nonlb-tab" data-toggle="pill"
                            href="#custom-tabs-nonlb" role="tab" aria-controls="custom-tabs-nonlb"
                            aria-selected="false">PO Non LB</a>
                    </li>

                    <li class="nav-item">
                    <li class="nav-item">
                        <a class="nav-link tab-link" id="custom-tabs-lb-tab" data-toggle="pill" href="#custom-tabs-lb"
                            role="tab" aria-controls="custom-tabs-lb" aria-selected="false">PO LB</a>
                    </li>
                    <a class="nav-link tab-link" id="custom-tabs-ukuran-tab" data-toggle="pill"
                        href="#custom-tabs-ukuran" role="tab" aria-controls="custom-tabs-ukuran"
                        aria-selected="false">Ukuran Ayam</a>
                    </li>
                </ul>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-tabContent">
                        <div class="tab-pane fade active show" id="custom-tabs-orders" role="tabpanel"
                            aria-labelledby="custom-tabs-orders-tab">
                            <div class="form-group">
                                <label for="">Filter</label>
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif name="tanggal" class="form-control" id="tanggal"
                                    placeholder="Tuliskan " value="{{ $tanggal }}" autocomplete="off">
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    @foreach ($order as $i => $val)

                                    @php
                                    $qty = 0;
                                    $berat = 0;
                                    $sum = 0;
                                    @endphp

                                    <table class="table default-table">
                                        <thead>
                                            <tr>
                                                <th width="35%">{{ $val->nama }}</th>
                                                <th>QTY</th>
                                                <th>Berat</th>
                                                <th>Persen Qty</th>
                                                <th>Persen Berat</th>
                                                <th width="15%">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach (Order::item_order($val->id) as $i => $item)
                                            @php
                                            $qty += $item->qty;
                                            $berat += $item->berat;
                                            $total = 0;
                                            $totalberat = 0;
                                            $persen = 0;
                                            $persenberat = 0;
                                            $idchill = '';
                                            @endphp
                                            @foreach (Order::bahan_baku($val->id, $item->id) as $bahan)
                                            @php
                                            $total += $bahan->bb_item;
                                            $totalberat += $bahan->bb_berat;
                                            $persen = $item->qty != 0 ? ($total / $item->qty) * 100 : 0;
                                            $persenberat = $item->berat != 0 ? ($totalberat / $item->berat) * 100 : 0;
                                            $idchill = $bahan->chiller_alokasi;
                                            @endphp
                                            @endforeach
                                            <tr>
                                                <td>{{ $item->nama_detail }}</td>
                                                <td>{{ $item->qty ?? '0' }}</td>
                                                <td>{{ $item->berat ?? '0' }}</td>
                                                <td>{{ number_format($persen, 2) }}%</td>
                                                <td>{{ number_format($persenberat, 2) }}%</td>
                                                <td>{!!$val->status_order!!} </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td>Total</td>
                                                <td>{{ number_format($qty) }}</td>
                                                <td>{{ number_format($berat, 2) }}</td>
                                                <td></td>
                                                <td></td>
                                                <td>
                                                    @if ($val->status == 2)
                                                    <button type="button" data-selesai="{{ $val->id }}"
                                                        class="btn btn-primary btn-block selesaiproses">
                                                        Selesaikan</button>
                                                    @else
                                                    <button type="button" data-selesai="{{ $val->id }}"
                                                        class="btn btn-success btn-block " disabled>
                                                        Selesai</button>
                                                    @endif
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>

                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="custom-tabs-pending" role="tabpanel"
                            aria-labelledby="custom-tabs-pending-tab">
                            <div id="kp-list-order-pending"></div>
                        </div>

                        <div class="tab-pane fade" id="custom-tabs-fullfilment" role="tabpanel"
                            aria-labelledby="custom-tabs-fullfilment-tab">
                            <div class="table-responsive">
                                <table width="100%" id="kategori" class="table default-table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th width="200px">Status</th>
                                            <th width="125px">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($fulfillment as $i => $full)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $full->nama }}</td>
                                            <td>
                                                <div class="progress">
                                                    @php
                                                    $cuk = '';
                                                    if (Orderitem::persen_order($full->id) < 50) { $cuk='bg-danger' ; }
                                                        @endphp <div
                                                        class="progress-bar progress-bar-striped progress-bar-animated {{ $cuk }}"
                                                        role="progressbar" aria-valuemin="0" aria-valuemax="100"
                                                        style="width: {{ Orderitem::persen_order($full->id) }}%">
                                                </div>
                            </div>
                            </td>
                            <td>{{ number_format(Orderitem::persen_order($full->id), 2) }} %</td>
                            </tr>
                            @endforeach
                            </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="custom-tabs-chiller" role="tabpanel"
                        aria-labelledby="custom-tabs-chiller-tab">
                        <div class="form-group">
                            <label for="">Filter</label>
                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                min="2023-01-01" @endif name="tanggal" class="form-control" id="tanggal"
                                placeholder="Tuliskan " value="{{ $tanggal }}" autocomplete="off">
                            @error('tanggal') <div class="small text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="table-responsive">
                            <table width="100%" id="kategori" class="table default-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Ekor/Pcs/Pack</th>
                                        <th>Berat</th>
                                        <th>Asal</th>
                                        <th>Tanggal Bahan Baku</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($chiller as $i => $chill)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ $chill->item_name }}</td>
                                        <td>{{ $chill->stock_item }} ekor</td>
                                        <td>{{ $chill->stock_berat }} Kg</td>
                                        <td>{{ $chill->tujuan }}</td>
                                        <td>{{ $chill->tanggal_produksi }}</td>
                                        <td>
                                            @if ($chill->status == 2)
                                            <div class="row" style="padding: 10px">
                                                <div class="col px-1">
                                                    <input type="number" id="kirim_jumlah{{ $chill->id }}"
                                                        placeholder="Qty" class="form-control">
                                                </div>
                                                <div class="col px-1">
                                                    <input type="number" id="kirim_berat{{ $chill->id }}"
                                                        placeholder="Berat" class="form-control">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col pr-1">
                                                    <select name="plastik{{ $chill->id }}" id="plastik{{ $chill->id }}"
                                                        data-placeholder="Pilih Plastik" data-width="100%"
                                                        class="form-control select2">
                                                        <option value=""></option>
                                                        <option value="Curah">Curah</option>
                                                        @foreach ($plastik as $row)
                                                        <option value="{{ $row->id }}">
                                                            {{ $row->nama }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-3 px-1">
                                                    <input type="number" id="jumlah{{ $chill->id }}" placeholder="Qty"
                                                        class="form-control">
                                                </div>
                                                <div class="col-auto pl-1">
                                                    <button type="submit" class="btn btn-primary toabf"
                                                        data-chiller="{{ $chill->id }}"> Kirim ke
                                                        ABF</button>
                                                </div>
                                            </div>
                                            @else
                                            <button class="btn btn-success btn-sm" disabled>Selesai</button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach

                                </tbody>
                            </table>
                            {{ $chiller->render() }}
                        </div>
                    </div>

                    <div class="tab-pane fade" id="custom-tabs-chiller-fg" role="tabpanel"
                        aria-labelledby="custom-tabs-chiller-fg-tab">
                        <div class="form-group">
                            <label for="">Filter</label>
                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                min="2023-01-01" @endif name="tanggal" class="form-control" id="tanggal_fg"
                                placeholder="Tuliskan " value="{{ $tanggal }}" autocomplete="off">
                            @error('tanggal') <div class="small text-danger">{{ message }}</div> @enderror
                        </div>

                        <div id="chiller_fg"></div>
                    </div>

                    <div class="tab-pane fade" id="custom-tabs-chiller-penyiapan" role="tabpanel"
                        aria-labelledby="custom-tabs-chiller-penyiapan-tab">
                        <div class="form-group">
                            <label for="">Filter</label>
                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                min="2023-01-01" @endif name="tanggal" class="form-control" id="tanggal_penyiapan"
                                placeholder="Tuliskan " value="{{ $tanggal }}" autocomplete="off">
                            @error('tanggal') <div class="small text-danger">{{ message }}</div> @enderror
                        </div>

                        <div id="chiller_penyiapan"></div>
                    </div>

                    <div class="tab-pane fade" id="custom-tabs-evaluasi" role="tabpanel"
                        aria-labelledby="custom-tabs-evaluasi-tab">
                        <div id="evaluasi"></div>
                    </div>

                    <div class="tab-pane fade" id="custom-tabs-gudang" role="tabpanel"
                        aria-labelledby="custom-tabs-gudang-tab">
                        <div class="table-responsive">
                            <table width="100%" id="kategori" class="table default-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Standing</th>
                                        <th>Kode</th>
                                        <th>Kemasan</th>
                                        <th>Lokasi</th>
                                        <th>Ekor/Pcs/Pack</th>
                                        <th>Berat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>


                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="custom-tabs-bahanbaku" role="tabpanel"
                        aria-labelledby="custom-tabs-bahanbaku-tab">
                        <div class="accordion" id="accordionExample">
                            <div id="show_data"></div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="custom-tabs-freestock" role="tabpanel"
                        aria-labelledby="custom-tabs-freestock-tab">
                        <div class="accordion" id="accordionExample">
                            <div class="table-responsive">
                                <form action="{{ route('kepalaproduksi.storefreestock') }}" method="POST">
                                    @csrf @method('patch')
                                    <table width="100%" id="kategori" class="table default-table">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>Ekor/Pcs/Pack</th>
                                                <th>Berat</th>
                                                <th>Request Pending</th>
                                                <th>Tanggal Bahan Baku</th>
                                                <th>Ambil Bahan Baku</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($chiller as $i => $chill)
                                            <tr>
                                                <td>{{ ++$i }}</td>
                                                <td>{{ $chill->item_name }}</td>
                                                <td>{{ $chill->stock_item }} ekor</td>
                                                <td>{{ $chill->stock_berat }} Kg</td>
                                                <td>{{ number_format($chill->request_pending) }} ekor</td>
                                                <td>{{ $chill->tanggal_produksi }}</td>
                                                <td class="pt-1 pb-0">
                                                    <input type="hidden" name="x_code[]" value="{{ $chill->id }}">
                                                    <input type="number" name="qty[]" class="form-control"
                                                        id="qty{{ $i }}" placeholder="JUMLAH {{ $chill->item_name }}"
                                                        autocomplete="off">
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>


                                    <div class="form-group">
                                        Kategori Produksi
                                        <select name="kategori" class="form-control" id="kategori" required>
                                            <option value="" disabled selected hidden>Pilih Kategori Produksi
                                            </option>
                                            <option value="5">Boneles</option>
                                            <option value="2">Parting</option>
                                            <option value="3">Parting M</option>
                                            <option value="1">Whole Chicken</option>
                                            <option value="8">Frozen Parting</option>
                                            <option value="9">Frozen Parting M</option>
                                            <option value="10">Frozen Sampingan</option>
                                            <option value="11">Frozen Boneless</option>
                                        </select>
                                        @error('kategori') <div class="small text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>


                                    <div class="form-group text-right">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="custom-tabs-nonlb" role="tabpanel"
                        aria-labelledby="custom-tabs-nonlb-tab">
                        <div class="form-group">
                            <label for="">Filter</label>
                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                min="2023-01-01" @endif name="tanggal" class="form-control" id="tanggal_nonlb"
                                placeholder="Tuliskan " value="{{ $tanggal }}" autocomplete="off">
                            @error('tanggal') <div class="small text-danger">{{ message }}</div> @enderror
                        </div>
                        <div id="nonlb"></div>
                    </div>
                    <div class="tab-pane fade" id="custom-tabs-lb" role="tabpanel" aria-labelledby="custom-tabs-lb-tab">
                        <div class="form-group">
                            <label for="">Filter</label>
                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                min="2023-01-01" @endif name="tanggal" class="form-control" id="tanggal_lb"
                                placeholder="Tuliskan " value="{{ $tanggal }}" autocomplete="off">
                            @error('tanggal') <div class="small text-danger">{{ message }}</div> @enderror
                        </div>
                        <div id="lb"></div>
                    </div>
                    <div class="tab-pane fade" id="custom-tabs-ukuran" role="tabpanel"
                        aria-labelledby="custom-tabs-ukuran-tab">
                        <div id="ukuran"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</section>
<script>
    // $("#show_data").load("{{ route('kepalaproduksi.bahanbakushow') }}");
    $("#lainnya").load("{{ route('kepalaproduksi.lainnya') }}");
    $("#evaluasi").load("{{ route('kepalaproduksi.evaluasi') }}");
    $("#ukuran").load("{{ route('kepalaproduksi.ukuran') }}");
    $("#chiller_fg").load("{{ route('ppic.chiller_fg') }}");
    $("#chiller_penyiapan").load("{{ route('ppic.chiller_penyiapan') }}");
    $("#nonlb").load("{{ route('ppic.nonlb') }}");
    $("#lb").load("{{ route('ppic.lb') }}");

    getKpListOrderPending();

    $('#tanggal').on('change', function() {
        var tanggal = $(this).val();
        console.log("{{ url('admin/ppic/show?tanggal=') }}" + tanggal);
        $("#show").load("{{ url('admin/ppic/show?tanggal=') }}" + tanggal);
    })

    $('#tanggal_fg').on('change', function() {
        var tanggal = $(this).val();
        console.log("{{ url('admin/ppic/chiller-fg?tanggal=') }}" + tanggal);
        $("#chiller_fg").load("{{ url('admin/ppic/chiller-fg?tanggal=') }}" + tanggal);
    })
    $('#tanggal_penyiapan').on('change', function() {
        var tanggal = $(this).val();
        console.log("{{ url('admin/ppic/chiller-penyiapan?tanggal=') }}" + tanggal);
        $("#chiller_penyiapan").load("{{ url('admin/ppic/chiller-penyiapan?tanggal=') }}" + tanggal);
    })
    $('#tanggal_nonlb').on('change', function() {
        var tanggal = $(this).val();
        console.log("{{ url('admin/ppic/nonlb?tanggal=') }}" + tanggal);
        $("#nonlb").load("{{ url('admin/ppic/nonlb?tanggal=') }}" + tanggal);
    })

    $('#tanggal_lb').on('change', function() {
        var tanggal = $(this).val();
        console.log("{{ url('admin/ppic/lb?tanggal=') }}" + tanggal);
        $("#lb").load("{{ url('admin/ppic/lb?tanggal=') }}" + tanggal);
    })

    function getKpListOrderPending() {
        var url = "{{ url('admin/kepala-produksi/orderpendingshow') }}";
        $.ajax({
            url: url,
            method: "GET",
            success: function(response) {

                $('#kp-list-order-pending').html(response);
            }

        });

    }

    var hash = window.location.hash.substr(1);
    var href = window.location.href;

    deafultPage();

    function deafultPage() {
        if (hash == undefined || hash == "") {
            hash = "custom-tabs-orders";
        }

        $('.nav-item a[href="#' + hash + '"]').addClass('active').siblings().removeClass('active');
        $('#' + hash).addClass('active show').siblings().removeClass('active show');
    }


    $('.tab-link').click(function(e) {
        e.preventDefault();
        status = $(this).attr('aria-controls');
        window.location.hash = status;
        href = window.location.href;

    });

    $('.select2').select2({
        theme: 'bootstrap4'
    });
</script>