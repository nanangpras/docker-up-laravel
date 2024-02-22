<section class="panel">
    <div class="row">
        <div class="col-12 col-sm-12">
            <div class="card card-primary card-outline card-tabs">
                <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link tab-sub-link" id="custom-tabs-three-perting-tab" data-toggle="pill" href="#custom-tabs-three-perting" role="tab" aria-controls="custom-tabs-three-perting" aria-selected="true">
                            Parting
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link tab-sub-link" id="custom-tabs-three-marinasi-tab" data-toggle="pill" href="#custom-tabs-three-marinasi" role="tab" aria-controls="custom-tabs-three-marinasi" aria-selected="false">
                            Parting M
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link tab-sub-link" id="custom-tabs-three-whole-tab" data-toggle="pill" href="#custom-tabs-three-whole" role="tab" aria-controls="custom-tabs-three-whole" aria-selected="false">
                            Whole Chicken
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link tab-sub-link" id="custom-tabs-three-frozen-tab" data-toggle="pill" href="#custom-tabs-three-frozen" role="tab" aria-controls="custom-tabs-three-frozen" aria-selected="false">
                            Frozen
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link tab-sub-link" id="custom-tabs-three-bonless-tab" data-toggle="pill" href="#custom-tabs-three-bonless" role="tab" aria-controls="custom-tabs-three-bonless" aria-selected="false">
                            Bonless
                        </a>
                    </li>
                </ul>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-three-tabContent">
                        <div class="tab-pane fade show active" id="custom-tabs-three-perting" role="tabpanel" aria-labelledby="custom-tabs-three-perting-tab">
                            @foreach ($parting as $i => $val)
                                <div class="card card-primary card-outline">
                                    <div class="card-header">
                                        Customer : {{ $val->nama }}
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @csrf <input type="hidden" name="xcode" id="xcode"
                                                value="{{ $val->id }}">
                                            <div class="col-8">
                                                @php
                                                    $qty = 0;
                                                    $berat = 0;
                                                @endphp
                                                @foreach (Order::item_order($val->id, 'parting') as $i => $item)
                                                    @php
                                                        $qty += $item->qty;
                                                        $berat += $item->berat;
                                                        $total = 0;
                                                        $persen = 0;
                                                        $idchill = '';
                                                    @endphp
                                                    @foreach (Order::bahan_baku($val->id, $item->id) as $bahan)
                                                        @php
                                                            $total += $bahan->bb_item;
                                                            $persen = $item->qty != 0 ? ($total / $item->qty) * 100 : 0;
                                                            $idchill = $bahan->chiller_alokasi;
                                                        @endphp
                                                    @endforeach

                                                    <div class="radio-toolbar">
                                                        <input type="radio" id="do-{{ $item->id }}"
                                                            onclick='' data-jenis='' value="{{ $item->id }}"
                                                            name="purchase" required>
                                                        <label for="do-{{ $item->id }}">
                                                            {{ $item->nama_detail }}
                                                            <span class=" pull-right">
                                                                Qty : <span
                                                                    class="label label-rounded-grey">{{ $item->qty ?? '0' }}
                                                                </span> &nbsp
                                                                Berat : <span
                                                                    class="label label-rounded-grey">{{ $item->berat ?? '0' }}
                                                                    kg </span> &nbsp
                                                                Total : <span
                                                                    class="label label-rounded-grey">{{ $total }}</span>
                                                                &nbsp
                                                                @if ($item->status == null)
                                                                    <a href="{{ route('kepalaproduksi.partingdetail', ['customer' => $val->id, 'item' => $item->id]) }}"
                                                                        class='btn btn-primary btn-sm'> Request
                                                                    </a>
                                                                @else
                                                                    <button type="button"
                                                                        data-kode="{{ $item->id }}"
                                                                        class="btn btn-success btn-sm"
                                                                        disabled>Diambil</button>
                                                                @endif

                                                            </span>
                                                        </label>
                                                        </>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group row">
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label>Qty</label>
                                                            <div class="input-group input-group-lg">
                                                                <input type="text"
                                                                    value="{{ number_format($qty) }}"
                                                                    name="jumlah"
                                                                    class="text-right form-control" id="qty"
                                                                    readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label>Berat</label>
                                                            <div class="input-group input-group-lg">
                                                                <input type="text"
                                                                    value="{{ number_format($berat, 2) }}"
                                                                    name="berat" class="text-right form-control"
                                                                    readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- <div class="form-group row">
                                                    <div class="col">
                                                        <div class="form-group">
                                                            @if ($val->status >= 3)
                                                                <button type="button"
                                                                    data-selesai="{{ $val->id }}"
                                                                    class="btn btn-success btn-block selesai"
                                                                    disabled>
                                                                    Selesaikan</button>
                                                            @else
                                                                <button type="button"
                                                                    data-selesai="{{ $val->id }}"
                                                                    class="btn btn-success btn-block selesai">
                                                                    Selesaikan</button>
                                                            @endif

                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                    </div>
                                                </div> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                            @endforeach
                        </div>

                        <div class="tab-pane fade" id="custom-tabs-three-marinasi" role="tabpanel" aria-labelledby="custom-tabs-three-marinasi-tab">
                            @foreach ($marinasi as $i => $val)
                                <div class="card card-primary card-outline">
                                    <div class="card-header">
                                        Customer : {{ $val->nama }}
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @csrf <input type="hidden" name="xcode" id="xcode"
                                                value="{{ $val->id }}">
                                            <div class="col-8">
                                                @php
                                                    $qty = 0;
                                                    $berat = 0;
                                                @endphp
                                                @foreach (Order::item_order($val->id, 'marinasi') as $i => $item)
                                                    @php
                                                        $qty += $item->qty;
                                                        $berat += $item->berat;
                                                        $total = 0;
                                                        $persen = 0;
                                                        $idchill = '';
                                                    @endphp
                                                    @foreach (Order::bahan_baku($val->id, $item->id) as $bahan)
                                                        @php
                                                            $total += $bahan->bb_item;
                                                            $persen = $item->qty != 0 ? ($total / $item->qty) * 100 : 0;
                                                            $idchill = $bahan->chiller_alokasi;
                                                        @endphp
                                                    @endforeach

                                                    <div class="radio-toolbar">
                                                        <input type="radio" id="do-{{ $item->id }}"
                                                            onclick='' data-jenis=''
                                                            value="{{ $item->id }}" name="purchase"
                                                            required>
                                                        <label for="do-{{ $item->id }}">
                                                            {{ $item->nama_detail }}
                                                            <span class=" pull-right">
                                                                Qty : <span
                                                                    class="label label-rounded-grey">{{ $item->qty ?? '0' }}
                                                                </span> &nbsp
                                                                Berat : <span
                                                                    class="label label-rounded-grey">{{ $item->berat ?? '0' }}
                                                                    kg </span> &nbsp
                                                                Total : <span
                                                                    class="label label-rounded-grey">{{ $total }}</span>
                                                                &nbsp
                                                                @if ($item->status == null)
                                                                    <a href="{{ route('kepalaproduksi.marinasidetail', ['customer' => $val->id, 'item' => $item->id]) }}"
                                                                        class='btn btn-primary btn-sm'>Request</a>
                                                                @else
                                                                    <button type="button"
                                                                        data-kode="{{ $item->id }}"
                                                                        class="btn btn-success"
                                                                        disabled>Diambil</button>
                                                                @endif

                                                            </span>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group row">
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label>Qty</label>
                                                            <div class="input-group input-group-lg">
                                                                <input type="text"
                                                                    value="{{ number_format($qty) }}"
                                                                    name="jumlah"
                                                                    class="text-right form-control" id="qty"
                                                                    readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label>Berat</label>
                                                            <div class="input-group input-group-lg">
                                                                <input type="text"
                                                                    value="{{ number_format($berat, 2) }}"
                                                                    name="berat" class="text-right form-control"
                                                                    readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                            @endforeach
                        </div>

                        <div class="tab-pane fade" id="custom-tabs-three-whole" role="tabpanel" aria-labelledby="custom-tabs-three-whole-tab">
                            @foreach ($whole as $i => $val)
                                <div class="card card-primary card-outline">
                                    <div class="card-header">
                                        Customer : {{ $val->nama }}
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @csrf <input type="hidden" name="xcode" id="xcode"
                                                value="{{ $val->id }}">
                                            <div class="col-8">
                                                @php
                                                    $qty = 0;
                                                    $berat = 0;
                                                @endphp
                                                @foreach (Order::item_order($val->id, 'whole') as $i => $item)
                                                    @php
                                                        $qty += $item->qty;
                                                        $berat += $item->berat;
                                                        $total = 0;
                                                        $persen = 0;
                                                        $idchill = '';
                                                    @endphp
                                                    @foreach (Order::bahan_baku($val->id, $item->id) as $bahan)
                                                        @php
                                                            $total += $bahan->bb_item;
                                                            $persen = $item->qty != 0 ? ($total / $item->qty) * 100 : 0;
                                                            $idchill = $bahan->chiller_alokasi;
                                                        @endphp
                                                    @endforeach

                                                    <div class="radio-toolbar">
                                                        <input type="radio" id="do-{{ $item->id }}"
                                                            onclick='' data-jenis=''
                                                            value="{{ $item->id }}" name="purchase"
                                                            required>
                                                        <label for="do-{{ $item->id }}">
                                                            {{ $item->nama_detail }}
                                                            <span class=" pull-right">
                                                                Qty : <span
                                                                    class="label label-rounded-grey">{{ $item->qty ?? '0' }}
                                                                </span> &nbsp
                                                                Berat : <span
                                                                    class="label label-rounded-grey">{{ $item->berat ?? '0' }}
                                                                    kg </span> &nbsp
                                                                Total : <span
                                                                    class="label label-rounded-grey">{{ $total }}</span>
                                                                &nbsp
                                                                @if ($item->status == null)
                                                                    <a href="{{ route('kepalaproduksi.wholedetail', ['customer' => $val->id, 'item' => $item->id]) }}"
                                                                        class='btn btn-primary btn-sm'>Request</a>
                                                                @else
                                                                    <button type="button"
                                                                        data-kode="{{ $item->id }}"
                                                                        class="btn btn-success btn-sm"
                                                                        disabled>Diambil</button>
                                                                @endif

                                                            </span>
                                                        </label>
                                                        </>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group row">
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label>Qty</label>
                                                            <div class="input-group input-group-lg">
                                                                <input type="text"
                                                                    value="{{ number_format($qty) }}"
                                                                    name="jumlah"
                                                                    class="text-right form-control" id="qty"
                                                                    readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label>Berat</label>
                                                            <div class="input-group input-group-lg">
                                                                <input type="text"
                                                                    value="{{ number_format($berat, 2) }}"
                                                                    name="berat" class="text-right form-control"
                                                                    readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                            @endforeach
                        </div>

                        <div class="tab-pane fade" id="custom-tabs-three-frozen" role="tabpanel" aria-labelledby="custom-tabs-three-frozen-tab">
                            @foreach ($frozen as $i => $val)
                                <div class="card card-primary card-outline">
                                    <div class="card-header">
                                        Customer : {{ $val->nama }}
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @csrf <input type="hidden" name="xcode" id="xcode"
                                                value="{{ $val->id }}">
                                            <div class="col-8">
                                                @php
                                                    $qty = 0;
                                                    $berat = 0;
                                                @endphp
                                                @foreach (Order::item_order($val->id, 'frozen') as $i => $item)
                                                    @php
                                                        $qty += $item->qty;
                                                        $berat += $item->berat;
                                                        $total = 0;
                                                        $persen = 0;
                                                        $idchill = '';
                                                    @endphp
                                                    @foreach (Order::bahan_baku($val->id, $item->id) as $bahan)
                                                        @php
                                                            $total += $bahan->bb_item;
                                                            $persen = $item->qty != 0 ? ($total / $item->qty) * 100 : 0;
                                                            $idchill = $bahan->chiller_alokasi;
                                                        @endphp
                                                    @endforeach

                                                    <div class="radio-toolbar">
                                                        <input type="radio" id="do-{{ $item->id }}"
                                                            onclick='' data-jenis=''
                                                            value="{{ $item->id }}" name="purchase"
                                                            required>
                                                        <label for="do-{{ $item->id }}">
                                                            {{ $item->nama_detail }}
                                                            <span class=" pull-right">
                                                                Qty : <span
                                                                    class="label label-rounded-grey">{{ $item->qty ?? '0' }}
                                                                </span> &nbsp
                                                                Berat : <span
                                                                    class="label label-rounded-grey">{{ $item->berat ?? '0' }}
                                                                    kg </span> &nbsp
                                                                Total : <span
                                                                    class="label label-rounded-grey">{{ $total }}</span>
                                                                &nbsp
                                                                @if ($item->status == null)
                                                                    <a href="{{ route('kepalaproduksi.frozendetail', ['customer' => $val->id, 'item' => $item->id]) }}"
                                                                        class='btn btn-primary btn-sm'>Request</a>
                                                                @else
                                                                    <button type="button"
                                                                        data-kode="{{ $item->id }}"
                                                                        class="btn btn-success btn-sm"
                                                                        disabled>Diambil</button>
                                                                @endif

                                                            </span>
                                                        </label>
                                                        </>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group row">
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label>Qty</label>
                                                            <div class="input-group input-group-lg">
                                                                <input type="text"
                                                                    value="{{ number_format($qty) }}"
                                                                    name="jumlah"
                                                                    class="text-right form-control" id="qty"
                                                                    readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label>Berat</label>
                                                            <div class="input-group input-group-lg">
                                                                <input type="text"
                                                                    value="{{ number_format($berat, 2) }}"
                                                                    name="berat" class="text-right form-control"
                                                                    readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                            @endforeach
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-three-bonless" role="tabpanel"
                            aria-labelledby="custom-tabs-three-bonless-tab">
                            <div id="show_bb"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                                    @foreach ($row->list_item as $item)
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
<style>
    #accordion .card .card-header {
        padding: 8px;
        text-align: left;
        border-bottom: 0px;
        background: #fafafa;
    }

    #accordion .card a {
        color: #000000;
        padding: 0px;
    }

</style>
<script>
    $("#show_bb").load("{{ route('kepalaproduksi.bahanbakubonless') }}");
</script>
