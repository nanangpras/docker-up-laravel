<section class="panel">
    <div class="row">
        <div class="col-12 col-sm-12">
            <div class="card card-primary card-outline card-tabs">
                <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="custom-tabs-three-orders-tab" data-toggle="pill"
                            href="#custom-tabs-three-orders" role="tab" aria-controls="custom-tabs-three-orders"
                            aria-selected="true">Orders</a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link" id="custom-tabs-three-pending-tab" data-toggle="pill"
                            href="#custom-tabs-three-pending" role="tab" aria-controls="custom-tabs-three-pending"
                            aria-selected="false">Order Pending</a>
                    </li> --}}
                    <li class="nav-item">
                        <a class="nav-link" id="custom-tabs-three-freestock-tab" data-toggle="pill"
                            href="#custom-tabs-three-freestock" role="tab" aria-controls="custom-tabs-three-freestock"
                            aria-selected="false">Free Stock</a>
                    </li>
                </ul>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-three-tabContent">
                        <div class="tab-pane fade show active" id="custom-tabs-three-orders" role="tabpanel"
                            aria-labelledby="custom-tabs-three-orders-tab">
                            <div class="row">
                                <div class="col-12">
                                    @foreach ($order as $i => $val)
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
                                                                    onclick='' data-jenis='' value="{{ $item->id }}"
                                                                    name="purchase" required>
                                                                <label for="do-{{ $item->id }}">
                                                                    {{ $item->nama_detail }}
                                                                    <span class=" pull-right">
                                                                        Qty : <span
                                                                            class="label label-rounded-grey">{{ $item->qty ?? '0' }}
                                                                        </span> &nbsp;
                                                                        Berat : <span
                                                                            class="label label-rounded-grey">{{ $item->berat ?? '0' }}
                                                                            kg </span> &nbsp;
                                                                        Total : <span
                                                                            class="label label-rounded-grey">{{ $total }}</span>
                                                                        &nbsp;
                                                                        @if ($val->status >= 2 and $item->status == 1)
                                                                            <a href="{{ route('kepalaregu.requestdetail', ['customer' => $val->id, 'item' => $item->id]) }}"
                                                                                class='btn btn-primary btn-sm'>
                                                                                Request
                                                                            </a>
                                                                            <button type="button"
                                                                                data-kode="{{ $item->id }}"
                                                                                data-chiller="{{ $val->id }}"
                                                                                class="btn btn-warning btn-sm abf">Kirim
                                                                                ABF</button>
                                                                            <button type="button"
                                                                                data-kode="{{ $item->id }}"
                                                                                data-chiller="{{ $val->id }}"
                                                                                class="btn btn-success btn-sm chiller">Kirim
                                                                                Chiller</button>
                                                                        @elseif ($item->status == 2)
                                                                            <span
                                                                                class="status status-success">Selesai</span>
                                                                        @else
                                                                            @if ($item->item->category_id == '5' || $item->item->category_id == '6')
                                                                                <a href="{{ route('kepalaregu.bahanbakubonless', ['customer' => $val->id, 'item' => $item->id]) }}"
                                                                                    class='btn btn-primary btn-sm'>
                                                                                    Boneless
                                                                                    BB
                                                                                </a>
                                                                            @else
                                                                                <a href="{{ route('kepalaregu.requestdetail', ['customer' => $val->id, 'item' => $item->id]) }}"
                                                                                    class='btn btn-primary btn-sm'>
                                                                                    Request
                                                                                </a>
                                                                            @endif
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
                                                                    <label>Qty Total</label>
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
                                                                    <label>Berat Total</label>
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
                                                                    @if ($item->status == 2)
                                                                        <button type="submit"
                                                                            data-id="{{ $val->id }}"
                                                                            data-customer="{{ $val->id }}"
                                                                            class="btn btn-primary btn-block proses">
                                                                            Proses Semua</button>
                                                                    @else
                                                                        <button type="submit"
                                                                            data-id="{{ $val->id }}"
                                                                            data-customer="{{ $val->id }}"
                                                                            class="btn btn-primary btn-block proses"
                                                                            disabled>
                                                                            Proses Semua</button>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="col">
                                                                <div class="form-group">
                                                                    @if ($val->status == 3)
                                                                        <button type="button"
                                                                            data-selesai="{{ $val->id }}"
                                                                            class="btn btn-success btn-block selesai">
                                                                            Selesaikan</button>
                                                                    @else
                                                                        <button type="button"
                                                                            data-selesai="{{ $val->id }}"
                                                                            class="btn btn-success btn-block selesai"
                                                                            disabled>
                                                                            Selesaikan</button>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div> --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- <div class="tab-pane fade" id="custom-tabs-three-pending" role="tabpanel"
                            aria-labelledby="custom-tabs-three-pending-tab">
                            <div class="row">
                                <div class="col-12">
                                    <div id="pending"></div>
                                </div>
                            </div>
                        </div> --}}

                        <div class="tab-pane fade" id="custom-tabs-three-freestock" role="tabpanel"
                            aria-labelledby="custom-tabs-three-pending-tab">
                            <div id="freestock"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $("#pending").load("{{ route('kepalaregu.marinasipending') }}");
    $("#freestock").load("{{ route('kepalaregu.marinasifreestock') }}");

</script>
