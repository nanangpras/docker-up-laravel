<section class="panel">
    <div class="row">
        <div class="col-12 col-sm-12">
            <div class="card card-primary card-outline card-tabs">
                <ul class="nav nav-tabs" id="custom-tabs-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link tab-link" id="custom-tabs-orders-tab" data-toggle="pill"
                            href="#custom-tabs-orders" role="tab" aria-controls="custom-tabs-orders"
                            aria-selected="true">
                            Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link tab-link" id="custom-tabs-chiller-tab" data-toggle="pill"
                            href="#custom-tabs-chiller" role="tab" aria-controls="custom-tabs-chiller"
                            aria-selected="false">
                            Sisa Chiller
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link tab-link" id="custom-tabs-evaluasi-tab" data-toggle="pill"
                            href="#custom-tabs-evaluasi" role="tab" aria-controls="custom-tabs-evaluasi"
                            aria-selected="false">
                            Evaluasi Produksi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link tab-link" id="custom-tabs-gudang-tab" data-toggle="pill"
                            href="#custom-tabs-gudang" role="tab" aria-controls="custom-tabs-gudang"
                            aria-selected="false">
                            Gudang
                        </a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link tab-link" id="custom-tabs-freestock-tab" data-toggle="pill"
                            href="#custom-tabs-freestock" role="tab" aria-controls="custom-tabs-freestock"
                            aria-selected="false">Free Stock</a>
                    </li> --}}
                    {{-- <li class="nav-item">
                        <a class="nav-link tab-link" id="custom-tabs-life-tab" data-toggle="pill"
                            href="#custom-tabs-life" role="tab" aria-controls="custom-tabs-life"
                            aria-selected="false">Purchase Lainnya</a>
                    </li> --}}
                    <li class="nav-item">
                        <a class="nav-link tab-link" id="custom-tabs-ukuran-tab" data-toggle="pill"
                            href="#custom-tabs-ukuran" role="tab" aria-controls="custom-tabs-ukuran"
                            aria-selected="false">
                            Ukuran Ayam
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link tab-link" id="custom-tabs-nonlb-tab" data-toggle="pill"
                            href="#custom-tabs-nonlb" role="tab" aria-controls="custom-tabs-nonlb"
                            aria-selected="false">
                            PO Non LB
                        </a>
                    </li>
                </ul>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-tabContent">
                        <div class="tab-pane fade active show" id="custom-tabs-orders" role="tabpanel"
                            aria-labelledby="custom-tabs-orders-tab">
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
                            @error('tanggal') <div class="small text-danger">{{ message }}</div> @enderror
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
                                        {{-- <th>Aksi</th> --}}
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
                                        {{-- <td>
                                            @if ($chill->status == 2)
                                            <button type="submit" class="btn btn-primary btn-sm toabf"
                                                data-chiller="{{ $chill->id }}"> Kirim ke
                                                ABF</button>
                                            @else
                                            <button type="submit" class="btn btn-success btn-sm toabf" disabled>
                                                Selesai</button>
                                            @endif
                                        </td> --}}
                                    </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
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
                                                <td>{{ number_format($chill->stock_item) }} ekor</td>
                                                <td>{{ number_format($chill->stock_berat, 2) }} Kg</td>
                                                <td>{{ number_format($chill->request_pending) }} ekor</td>
                                                <td>{{ $chill->tanggal_produksi }}</td>
                                                <td class="pt-1 pb-0">
                                                    <input type="hidden" name="x_code[]" value="{{ $chill->id }}">
                                                    <input type="number" name="qty[]" class="form-control" id="qty"
                                                        placeholder="JUMLAH {{ $chill->item_name }}" autocomplete="off">
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

                    <div class="tab-pane fade" id="custom-tabs-ukuran" role="tabpanel"
                        aria-labelledby="custom-tabs-ukuran-tab">
                        <div id="ukuran"></div>
                    </div>

                    <div class="tab-pane" id="custom-tabs-nonlb" role="tabpanel"
                        aria-labelledby="custom-tabs-nonlb-tab">
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed mattis nulla sit amet eros
                            semper, eget dapibus odio mollis. Sed quis mauris accumsan, fermentum nunc eu, molestie
                            ante. Praesent a nunc eu velit mattis semper in vel nulla. Etiam eu neque vel augue aliquet
                            sollicitudin gravida lobortis dolor. Aliquam quam urna, consectetur consequat tellus et,
                            gravida feugiat odio. Mauris sodales convallis tellus eget vehicula. Nullam hendrerit id est
                            in accumsan. Maecenas massa nisi, varius vitae libero et, cursus imperdiet arcu. Morbi eget
                            tortor tristique, porta metus at, placerat justo.</p>
                        <p>Pellentesque ex eros, congue id aliquam ac, tempor a nulla. Pellentesque ornare urna a elit
                            venenatis consequat eget non arcu. Proin molestie mauris quam, interdum aliquam turpis
                            pretium ut. Aenean facilisis tincidunt enim in bibendum. Aenean eu leo ex. Nunc ullamcorper
                            tempor lobortis. Integer sagittis nisl in nibh malesuada vulputate. Nam malesuada
                            scelerisque dolor, a iaculis velit. Nam lobortis fermentum metus id facilisis. Morbi sit
                            amet pharetra magna, a ultrices justo. Etiam nec sapien eget ex laoreet hendrerit id at
                            mauris. Vivamus nisl odio, elementum sed turpis ac, vulputate sagittis diam. Cras luctus
                            tristique felis, id sagittis magna congue et.</p>
                        <p>Phasellus porttitor lorem eu nisi pretium sodales. Morbi congue vehicula nisi non
                            ullamcorper. Donec eleifend feugiat diam, a commodo eros sollicitudin ut. Nulla sagittis
                            velit eget erat pharetra lobortis. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                            In egestas tortor at turpis volutpat, sit amet egestas est tristique. Donec luctus tellus et
                            tellus semper ullamcorper. Sed purus justo, pellentesque ut turpis a, porttitor semper dui.
                            Nunc blandit faucibus tellus eu consectetur. Maecenas sit amet tristique nunc, nec
                            condimentum ante. Suspendisse sollicitudin lobortis fermentum. Aliquam porta rhoncus metus
                            eu sodales.</p>
                        <p>Nullam sed risus ornare, facilisis turpis vitae, ultricies elit. Quisque ut cursus purus.
                            Proin dolor metus, tincidunt ut viverra id, efficitur ut eros. Fusce molestie turpis sed
                            nisl viverra sodales. Aliquam arcu lorem, vestibulum eget scelerisque quis, porta eu metus.
                            Suspendisse vulputate scelerisque ultricies. Lorem ipsum dolor sit amet, consectetur
                            adipiscing elit. Nunc cursus consequat sagittis. Sed iaculis arcu id massa porta hendrerit.
                            Duis elementum urna ac egestas molestie. Duis ullamcorper feugiat ipsum. Aenean et suscipit
                            magna. Nunc in ante vitae turpis aliquet volutpat. Nulla consequat consequat massa aliquam
                            sagittis. Duis dignissim dignissim iaculis. Donec finibus non ligula ac gravida.</p>
                        <p>Sed blandit purus vitae malesuada feugiat. Pellentesque cursus quis diam in imperdiet. Lorem
                            ipsum dolor sit amet, consectetur adipiscing elit. Quisque in nulla sed quam tempor
                            tincidunt a eu odio. Proin id arcu eget lorem aliquet porta. Vestibulum a efficitur dolor.
                            Sed enim massa, hendrerit eget velit quis, varius accumsan sem.</p>
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

    getKpListOrderPending();

    $('#tanggal').on('change', function() {
        var tanggal = $(this).val();
        console.log("{{ url('admin/kepala-produksi/show?tanggal=') }}" + tanggal);
        $("#show").load("{{ url('admin/kepala-produksi/show?tanggal=') }}" + tanggal);
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

</script>