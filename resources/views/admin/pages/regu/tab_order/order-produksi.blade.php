{{-- @extends('admin.layout.template')

@section('title', 'Order Produksi ')
@section('content')
    <div class="row">
        <div class="col"></div>
        <div class="col-7">
            <div class="mb-4 text-center text-uppercase">
                <b>Order Produksi {{ date('d/m/Y', strtotime($tanggal)) }}</b>
            </div>
        </div>
        <div class="col"></div>
    </div>
    @php
        $qty = 0;
        $verifikasi = 0;
    @endphp
    <div class="row">
        <div class="col">
            <div class="section">
                <div class="card-body">
                    <div style="display: inline-flex">
                        <h6 class="mr-3 mt-1">Tanggal Kirim : </h6>
                        @foreach ($nextday as $date)
                            <form action="{{ route('regu.order_produksi') }}" method="get">
                                <button type="submit" name="tanggal_kirim" value="{{ $date }}"
                                    class="btn btn{{ $tanggal == $date ? '' : '-outline' }}-primary mr-2"
                                    style="margin-bottom: 5px;">
                                    {{ date('d/m/y', strtotime($date)) }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
    {{-- <section class="panel"> --}}
        <div class="card-body">
            <div class="card mb-3">
                <div class="card-body p-2">
                    <div class="row">
                        <div class="col-sm-4 col-lg mb-2 pr-sm-1">
                            <div class="card">
                                <div class="card-header">Total Customer</div>
                                <div class="card-body p-2">
                                    <div class="row mb-1">
                                        <div class="col ">
                                            <div class="border text-center">
                                                <div class="font-weight-bold">{{$summary['total_customer']}}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 col-lg mb-2 px-sm-1">
                            <div class="card">
                                <div class="card-header">Total Qty</div>
                                <div class="card-body p-2">
                                    <div class="row mb-1">
                                        <div class="col ">
                                            <div class="border text-center">
                                                <div class="font-weight-bold" id="totalqty">{{$summary['total_qty']}}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 col-lg mb-2 px-sm-1">
                            <div class="card">
                                <div class="card-header">Total Berat</div>
                                <div class="card-body p-2">
                                    <div class="row mb-1">
                                        <div class="col ">
                                            <div class="border text-center">
                                                <div class="font-weight-bold">{{number_format($summary['total_berat'],2) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 col-lg mb-2 pl-sm-1">
                            <div class="card">
                                <div class="card-header">Total Order</div>
                                <div class="row">
                                    <div class="col">
                                        <div class="border text-center">
                                            <div class="font-weight-bold" id="totalorder">{{$summary['total_order']}}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-2">
                                    <div class="row mb-1">
                                        <div class="col pr-1">
                                            <div class="border text-center" style="background-color: aliceblue">
                                                <div class="small">Pending</div>
                                                <div class="font-weight-bold" id="totalverifikasi">{{$summary['total_pending']}}</div>
                                            </div>
                                        </div>
                                        <div class="col pl-1">
                                            <div class="border text-center" style="background-color: rgb(244, 244, 218)">
                                                <div class="small">Verifikasi</div>
                                                <div class="font-weight-bold" id="totalorderterverifikasi">{{$summary['total_verifikasi']}}</div>
                                            </div>
                                        </div>
                                        <div class="col pr-1">
                                            <div class="border text-center" style="background-color: rgb(249, 210, 210)">
                                                <div class="small">Batal</div>
                                                <div class="font-weight-bold" id="totalorderterverifikasi">{{$summary['total_batal']}}</div>
                                            </div>
                                        </div>
                                        <div class="col pl-1">
                                            <div class="border text-center" style="background-color: rgb(210, 210, 249)">
                                                <div class="small">Edit</div>
                                                <div class="font-weight-bold" id="totalorderterverifikasi">{{$summary['total_edit']}}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    {{-- </section> --}}

    {{-- <a href="{{ route('regu.index', array_merge($_GET, ['key' => 'parking_orders', 'get' => 'unduh'])) }}" class="btn btn-success btn-sm float-right mb-2">Unduh</a> --}}
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm default-table dataTable">
                <thead>
                    <tr>
                        <th class="text-center" rowspan="2">No</th>
                        <th class="text-center" rowspan="2">Kirim</th>
                        <th class="text-center" rowspan="2">Nomor SO</th>
                        <th class="text-center" rowspan="2">Customer</th>
                        <th class="text-center" rowspan="2">Item</th>
                        <th class="text-center" rowspan="2">Kategori</th>
                        <th class="text-center" rowspan="2">Bumbu</th>
                        <th class="text-center" rowspan="2">Memo</th>
                        <th class="text-center" rowspan="2">Plastik</th>
                        <th class="text-center" rowspan="2">Status</th>
                        <th class="text-center" rowspan="2">Edit</th>
                        <th class="text-center" colspan="4">Order</th>
                    </tr>
                    <tr>
                        <th class="text-center">Part</th>
                        <th class="text-center">Ekor/Pcs</th>
                        <th class="text-center">Pack</th>
                        <th class="text-center">Berat</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $qty=0;
                    @endphp
                    @foreach ($data as $row)
                    <tr @if ($row->status_so_order == '1')  {{-- pending---}}
                            @if ( $row->edited == '1')  
                                style="background-color: #f5e6e8"
                            @elseif($row->edited == '2')
                                style="background-color: #d5c6e0"
                            @elseif($row->edited == '3')
                                style="background-color: #aaa1c8"
                            @elseif($row->edited == '4')
                                style="background-color: #967aa1"
                            @else
                                style="background-color: aliceblue"
                            @endif
                        @endif
                        @if ($row->status_so_order == '3')  {{-- verifed---}}
                            @if ( $row->edited == '1')  
                                style="background-color: #f5e6e8"
                            @elseif($row->edited == '2')
                                style="background-color: #d5c6e0"
                            @elseif($row->edited == '3')
                                style="background-color: #aaa1c8"
                            @elseif($row->edited == '4')
                                style="background-color: #967aa1"
                            @else
                                style="background-color: rgb(244, 244, 218)"
                            @endif
                        @endif
                        @if ($row->status_so_order == '0')  {{-- batal---}}
                            style="background-color: rgb(249, 210, 210)"
                        @endif>
                        <td>{{ $loop->iteration + ($data->currentpage() - 1) * $data->perPage()  }}</td>
                        {{-- <td>{{ $row->tanggal_kirim }}</td> --}}
                        <td>{{ date('d/m/Y', strtotime($row->tanggal_kirim)) }}</td>
                        <td>{{ $row->no_so }}</td>
                        <td>{{ $row->nama_customer }}</td>
                        <td>{{ $row->item_nama }}</td>
                        <td>{{ App\Models\Category::where('id', App\Models\Item::where('nama', $row->item_nama)->first()->category_id)->first()->nama }}</td>
                        <td>{{ $row->bumbu }}</td>
                        <td>{{ $row->memo }}</td>
                        <td>
                            @if ($row->plastik == '1') Meyer @endif
                            @if ($row->plastik == '2') Avida @endif
                            @if ($row->plastik == '3') Polos @endif
                            @if ($row->plastik == '4') Curah @endif
                            @if ($row->plastik == '5') Mojo @endif
                            @if ($row->plastik == '6') Other @endif
                        </td>
                        <td>
                            @if ($row->status_so_order == '1')
                                <span class="status status-info text-font"> <strong>Pending</strong> </span>
                            @endif
                            @if ($row->status_so_order == '3')
                                <span class="status status-success text-font"><strong>Verified</strong></span>
                            @endif
                            @if ($row->status_so_order == '0')
                            <span class="status status-danger text-font"><strong>Void/Batal</strong></span>
                            @endif
                        </td>
                        <td> 
                            @if ($row->edited > 0)
                                Edit {{$row->edited}}
                            @else
                            
                            @endif 
                        </td>
                        <td class="text-right">{{$row->parting}}</td>
                        <td class="text-right">
                            @if(strpos(strtolower($row->memo), "ekor") || strtolower($row->memo) == "ekor"){{ number_format($row->qty) }} 
                            @php $qty += $row->qty @endphp
                            @endif
                        </td>
                        <td class="text-right">@if(strpos(strtolower($row->memo), "pack") || strtolower($row->memo) == "pack"){{ number_format($row->qty) }} @php $qty += $row->qty @endphp @endif</td>
                        <td class="text-right">{{$row->berat}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div id="paginate_produksi_order">
    {{ $data->appends($_GET)->onEachSide(1)->links() }}
</div>
<script>
    $('#paginate_produksi_order .pagination a').on('click', function(e) {
        e.preventDefault();
        showNotif('Menunggu');

        url = $(this).attr('href');
        $.ajax({
            url: url,
            method: "GET",
            success: function(response) {
                $('#data-order-produksi').html(response);
            }
        });
    });
    
    
</script>
{{-- @stop --}}
