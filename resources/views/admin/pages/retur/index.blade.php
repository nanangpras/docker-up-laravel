@extends('admin.layout.template')

@section('title', 'Retur Authorization')

@section('content')

    <div class="row mb-4">
        <div class="col"></div>
        <div class="col text-center">
            <b>RETUR</b>
        </div>
        <div class="col"></div>
    </div>

    <section class="panel">

        <div class="card card-primary card-outline card-tabs">
            <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link tab-link active" id="custom-tabs-three-stock-tab" data-toggle="pill"
                        href="#custom-tabs-three-stock" role="tab" aria-controls="custom-tabs-three-stock"
                        aria-selected="true">Retur</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link tab-link" id="custom-tabs-three-keluar-tab" data-toggle="pill"
                        href="#custom-tabs-three-keluar" role="tab" aria-controls="custom-tabs-three-keluar"
                        aria-selected="false">Summary</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link tab-link" id="custom-tabs-three-driver-tab" data-toggle="pill"
                        href="#custom-tabs-three-driver" role="tab" aria-controls="custom-tabs-three-driver"
                        aria-selected="false">Driver</a>
                </li>
            </ul>
            <div class="card-body">
                <div class="tab-content" id="custom-tabs-three-tabContent">
                    <div class="tab-pane fade show active" id="custom-tabs-three-stock" role="tabpanel"
                        aria-labelledby="custom-tabs-three-stock-tab">
                        <div class="row">
                            <div class="col-lg-12 col-md-12">

                                <div class="form-group">
                                    <div id="datacustomer"></div>
                                </div>

                            </div>
                            <div class="col-lg-12 col-md-12">

                                <div class="form-group">
                                    <div id="itemretur"></div>
                                </div>
                                </form>
                            </div>
                        </div>



                    </div>
                    <div class="tab-pane fade" id="custom-tabs-three-keluar" role="tabpanel"
                        aria-labelledby="custom-tabs-three-keluar-tab">
                        <div class="table-responsive">
                            <table width="100%" class="table default-table dataTable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Customer</th>
                                        <th>Tanggal Retur</th>
                                        <th>Tujuan</th>
                                        <th>Item </th>
                                        <th>Qty</th>
                                        <th>Berat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($summary as $i => $val)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $val->to_customer->nama }}</td>
                                            <td>{{ $val->tanggal_retur }}</td>
                                            <td>
                                                @foreach ($val->to_itemretur as $tujuan)
                                                    {{ $tujuan->tujuan }}<br>
                                                @endforeach
                                            </td>
                                            <td>
                                                @foreach ($val->to_itemretur as $item)
                                                    {{ $item->to_item->nama }}<br>
                                                @endforeach
                                            </td>
                                            <td>
                                                @foreach ($val->to_itemretur as $qty)
                                                    {{ $qty->qty }}<br>
                                                @endforeach
                                            </td>
                                            <td>
                                                @foreach ($val->to_itemretur as $berat)
                                                    {{ $berat->berat }}<br>
                                                @endforeach
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="custom-tabs-three-driver" role="tabpanel"
                        aria-labelledby="custom-tabs-three-driver-tab">
                        <div class="table-responsive">
                            <table id="" class="table table-sm default-table dataTable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>No Polisi</th>
                                        <th>Route</th>
                                        <th>Ekor/Pcs/Pack</th>
                                        <th>Berat</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($driver as $i => $row)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $row->nama }}</td>
                                            <td>{{ $row->expedisi_no_polisi($row->id) }}</td>
                                            <td>{{ $row->summary_route }}</td>
                                            <td>{{ $row->summary_ekor }}</td>
                                            <td>{{ $row->summary_berat }}</td>
                                            <td>
                                                {{ $row->status_ekspedisi }}
                                            </td>
                                            <td class="text-center">
                                                @if ($row->pickup)

                                                    <button type="button" class="btn btn-primary"
                                                        data-toggle="modal"
                                                        data-target="#backretur{{ $row->id }}">
                                                        Input Retur
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>

                                        @if ($row->pickup)
                                            <div class="modal fade" id="backretur{{ $row->id }}"
                                                data-backdrop="static" data-keyboard="false" tabindex="-1"
                                                aria-labelledby="backretur{{ $row->id }}Label"
                                                aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="backretur{{ $row->id }}Label">Input Retur
                                                            </h5>
                                                            <button type="button" class="close"
                                                                data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            @foreach ($row->pengiriman->ekspedisirute as $item)
                                                                <div class="mb-2">
                                                                    <div class="border bg-light p-2">
                                                                        {{ $item->nama }}
                                                                    </div>
                                                                    <div class="border p-2">
                                                                        @foreach (App\Models\OrderItem::where('order_id', $item->order_item_id)->where('retur_tujuan', null)->get() as $list)
                                                                            <form
                                                                                action="{{ route('retur.driverretur') }}"
                                                                                method="post">
                                                                                @csrf
                                                                                <input type="hidden" name="item"
                                                                                    value="{{ $list->id }}">
                                                                                <div class="border p-1">
                                                                                    <div
                                                                                        class="mb-1 font-weight-bold">
                                                                                        {{ $list->nama_detail }}
                                                                                    </div>
                                                                                    <div class="row">
                                                                                        <div class="col pr-1">
                                                                                            <div
                                                                                                class="small">
                                                                                                Qty</div>
                                                                                            <div>
                                                                                                {{ number_format($list->qty) }}
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col px-1">
                                                                                            <div
                                                                                                class="small">
                                                                                                Qty Fulfillment
                                                                                            </div>
                                                                                            <div>
                                                                                                {{ number_format($list->fulfillment_qty) }}
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col px-1">
                                                                                            <div
                                                                                                class="small">
                                                                                                Berat</div>
                                                                                            <div>
                                                                                                {{ number_format($list->berat, 2) }}
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col pl-1">
                                                                                            <div
                                                                                                class="small">
                                                                                                Berat Fulfillment
                                                                                            </div>
                                                                                            <div>
                                                                                                {{ number_format($list->fulfillment_berat, 2) }}
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="bg-light">
                                                                                        <div class="row mt-2">
                                                                                            <div
                                                                                                class="col pr-1">
                                                                                                Retur Qty
                                                                                                <input type="number"
                                                                                                    name="qty"
                                                                                                    autocomplete="off"
                                                                                                    class="form-control form-control-sm p-1">
                                                                                            </div>
                                                                                            <div
                                                                                                class="col px-1">
                                                                                                Retur Berat
                                                                                                <input type="number"
                                                                                                    name="berat"
                                                                                                    autocomplete="off"
                                                                                                    class="form-control form-control-sm p-1">
                                                                                            </div>
                                                                                            <div
                                                                                                class="col-7 pl-1">
                                                                                                Alasan
                                                                                                <input type="text"
                                                                                                    name="alasan"
                                                                                                    autocomplete="off"
                                                                                                    class="form-control form-control-sm">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <button type="submit"
                                                                                        class="btn btn-primary btn-block mt-2">Submit</button>
                                                                                </div>
                                                                            </form>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>

@stop

@section('header')
    <!-- <link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css"/> -->
@stop

@section('footer')
    <!-- <script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script> -->
    <script>
        $('.select2').select2({
            theme: 'bootstrap4'
        });

        $("#datacustomer").load("{{ route('retur.customer') }}");
        $("#itemretur").load("{{ route('retur.itemretur') }}");
    </script>

@stop
