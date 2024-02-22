@if ($download == true)
    @php
        header('Content-Transfer-Encoding: none');
        header('Content-type: application/vnd-ms-excel');
        header('Content-type: application/x-msexcel');
        header('Content-Disposition: attachment; filename=Retur Per Customer.xls');
    @endphp
@endif
<style>
    th,
    td {
        border: 1px solid #ddd;
    }
</style>

@php
    $countTotalKualitas = App\Models\ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')
                            ->join('items', 'retur_item.item_id', 'items.id')
                            ->where(function($query) use ($jenisitem) {
                                if ($jenisitem == 'sampingan') {
                                    $query->whereIn('items.category_id', ['4', '10', '16']);
                                } else if ($jenisitem == 'nonsampingan') {
                                    $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                }
                            })
                            ->where('retur.no_ra', '!=', null)
                            ->whereIn('retur.status', [1, 2])
                            ->where('kategori', 'Kualitas')
                            ->whereBetween('tanggal_retur', [$tanggal, $tanggal_akhir])
                            ->groupBy('catatan')
                            ->get('catatan');

    $countTotalNonKualitas = App\Models\ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')
                            ->join('items', 'retur_item.item_id', 'items.id')
                            ->where(function($query) use ($jenisitem) {
                                if ($jenisitem == 'sampingan') {
                                    $query->whereIn('items.category_id', ['4', '10', '16']);
                                } else if ($jenisitem == 'nonsampingan') {
                                    $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                }
                            })
                            ->where('retur.no_ra', '!=', null)
                            ->whereIn('retur.status', [1, 2])
                            ->where('kategori', 'Non Kualitas')
                            ->whereBetween('tanggal_retur', [$tanggal, $tanggal_akhir])
                            ->groupBy('catatan')
                            ->get('catatan');
@endphp
<section class="panel">
    <div class="card-body">
        <div class="form-group">
            Jenis Item
            <select name="jenis" data-placeholder="Pilih Jenis" data-width="100%" class="form-control select2" id="jenisitem" required>
                <option value=""></option>
                <option value="semua" {{ $jenisitem == 'semua' ? 'selected': ''}}>Semua</option>
                <option value="sampingan" {{ $jenisitem == 'sampingan' ? 'selected': ''}}>Sampingan</option>
                <option value="nonsampingan" {{ $jenisitem == 'nonsampingan' ? 'selected': ''}}>Non Sampingan</option>
            </select>
        </div>
    </div>
</section>
<div style="overflow-x:auto;">
    <table class="table table-sm table-striped table-bordered">
        <thead>
            <tr>
                <th class="text-center text-bold"
                    colspan="{{ 5 + count($countTotalKualitas) + count($countTotalNonKualitas) }}">Jumlah Retur (KG)</th>
            </tr>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Customer</th>
                <th rowspan="2">Parent</th>
                <th class="text-center" colspan="{{ count($countTotalKualitas) > 0 ? count($countTotalKualitas) : 1 }}">Kualitas</th>
                <th class="text-center" colspan="{{ count($countTotalNonKualitas) > 0 ? count($countTotalNonKualitas) : 1 }}">Non Kualitas</th>
                <th rowspan="2">Total Retur</th>
                <th rowspan="2">Total Pengiriman</th>
                <th rowspan="2">% Retur</th>
            </tr>
            <tr>
                @if (count($countTotalKualitas) > 0)
                    @foreach ($countTotalKualitas as $kualitas)
                        <th>{{ $kualitas->catatan }}</th>
                    @endforeach
                @else 
                    <th>-</th>
                @endif
                @if (count($countTotalNonKualitas) > 0)
                    @foreach ($countTotalNonKualitas as $nonKualitas)
                        <th>{{ $nonKualitas->catatan }}</th>
                    @endforeach
                @else
                    <th>-</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($exportPerCustomer as $key => $value)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $value->to_customer->nama }}</td>
                    <td>{{ App\Models\Customer::where('id', $value->to_customer->parent_id)->first()->nama ?? '-' }}
                    </td>
                    @if (count($countTotalKualitas) > 0)
                        @foreach ($countTotalKualitas as $perKualitas)
                            <td>{{ number_format(App\Models\ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')
                            ->join('items', 'retur_item.item_id', 'items.id')
                            ->where(function($query) use ($jenisitem) {
                                if ($jenisitem == 'sampingan') {
                                    $query->whereIn('items.category_id', ['4', '10', '16']);
                                } else if ($jenisitem == 'nonsampingan') {
                                    $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                }
                            })
                            ->where('retur.no_ra', '!=', null)
                            ->whereIn('retur.status', [1, 2])
                            ->where('catatan', $perKualitas->catatan)
                            ->where('customer_id', $value->customer_id)
                            ->whereBetween('tanggal_retur', [$tanggal, $tanggal_akhir])
                            ->sum('berat'),2,',','.') ?? '' }}
                            </td>
                        @endforeach
                    @else 
                        <td>-</td>
                    @endif

                    @if (count($countTotalNonKualitas) > 0)
                        @foreach ($countTotalNonKualitas as $perNonKualitas)
                            <td>{{ number_format(App\Models\ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')
                            ->join('items', 'retur_item.item_id', 'items.id')
                            ->where(function($query) use ($jenisitem) {
                                if ($jenisitem == 'sampingan') {
                                    $query->whereIn('items.category_id', ['4', '10', '16']);
                                } else if ($jenisitem == 'nonsampingan') {
                                    $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                }
                            })
                            ->where('retur.no_ra', '!=', null)
                            ->whereIn('retur.status', [1, 2])
                            ->where('catatan', $perNonKualitas->catatan)
                            ->where('customer_id', $value->customer_id)
                            ->whereBetween('tanggal_retur', [$tanggal, $tanggal_akhir])
                            ->sum('berat'),2,',','.') ?? '' }}
                            </td>
                        @endforeach
                    @else 
                        <td>-</td>
                    @endif

                    <td>{{ number_format(App\Models\ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')
                            ->join('items', 'retur_item.item_id', 'items.id')
                            ->where(function($query) use ($jenisitem) {
                                if ($jenisitem == 'sampingan') {
                                    $query->whereIn('items.category_id', ['4', '10', '16']);
                                } else if ($jenisitem == 'nonsampingan') {
                                    $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                }
                            })
                            ->where('retur.no_ra', '!=', null)->whereIn('retur.status', [1, 2])->where('customer_id', $value->customer_id)
                            ->whereBetween('tanggal_retur', [$tanggal, $tanggal_akhir])
                            ->sum('berat'),2,',','.') ?? '' }}
                    </td>
                    <td>{{ number_format(App\Models\OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
                            ->join('items', 'order_items.item_id', 'items.id')
                            ->where(function($query) use ($jenisitem) {
                                if ($jenisitem == 'sampingan') {
                                    // $query->whereIn('items.category_id', ['4', '10', '16']);
                                    $query->whereIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                } else if ($jenisitem == 'nonsampingan') {
                                    $query->whereNotIn(
                                        'nama_detail',
                                        [
                                            "AMPELA BERSIH BROILER",
                                            "AY - S",
                                            "HATI AMPELA BERSIH BROILER",
                                            "HATI AMPELA KOTOR BROILER",
                                            "HATI AMPELA KOTOR BROILER FROZEN",
                                            "HATI BERSIH BROILER",
                                            "KAKI BERSIH BROILER",
                                            "KAKI KOTOR BROILER",
                                            "KEPALA LEHER BROILER",
                                            "USUS BROILER",
                                            "TEMBOLOK"
                                        ]
                                    );
                                    // $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                    $query->whereNotIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                    // $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                }
                            })
                            ->where('orders.nama', $value->to_customer->nama)
                            ->whereBetween('orders.tanggal_kirim', [$tanggal, $tanggal_akhir])
                            ->sum('order_items.fulfillment_berat'),2,',','.') }}
                    </td>
                    <td>
                        @if (App\Models\ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')
                        ->join('items', 'retur_item.item_id', 'items.id')
                        ->where(function($query) use ($jenisitem) {
                            if ($jenisitem == 'sampingan') {
                                $query->whereIn('items.category_id', ['4', '10', '16']);
                            } else if ($jenisitem == 'nonsampingan') {
                                $query->whereNotIn('items.category_id', ['4', '10', '16']);
                            }
                        })
                        ->where('retur.no_ra', '!=', null)->whereIn('retur.status', [1, 2])
                        ->where('customer_id', $value->customer_id)
                        ->whereBetween('tanggal_retur', [$tanggal, $tanggal_akhir])
                        ->sum('berat') !== 0 &&
                            App\Models\OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
                            ->join('items', 'order_items.item_id', 'items.id')
                            ->where(function($query) use ($jenisitem) {
                                if ($jenisitem == 'sampingan') {
                                    $query->whereIn('items.category_id', ['4', '10', '16']);
                                } else if ($jenisitem == 'nonsampingan') {
                                    $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                }
                            })
                            ->where('orders.nama', $value->to_customer->nama)
                            ->whereBetween('orders.tanggal_kirim', [$tanggal, $tanggal_akhir])
                            ->sum('order_items.fulfillment_berat') !== 0)
                            {{ number_format((App\Models\ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')
                            ->join('items', 'retur_item.item_id', 'items.id')
                            ->where(function($query) use ($jenisitem) {
                                if ($jenisitem == 'sampingan') {
                                    $query->whereIn('items.category_id', ['4', '10', '16']);
                                } else if ($jenisitem == 'nonsampingan') {
                                    $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                }
                            })
                            ->where('retur.no_ra', '!=', null)->where('customer_id', $value->customer_id)
                            ->whereBetween('tanggal_retur', [$tanggal, $tanggal_akhir])
                            ->sum('berat') /App\Models\OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
                            ->join('items', 'order_items.item_id', 'items.id')
                            // ->where(function($query) use ($jenisitem) {
                            //     if ($jenisitem == 'sampingan') {
                            //         $query->whereIn('items.category_id', ['4', '10', '16']);
                            //     } else if ($jenisitem == 'nonsampingan') {
                            //         $query->whereNotIn('items.category_id', ['4', '10', '16']);
                            //     }
                            // })
                            ->where(function($query) use ($jenisitem) {
                                if ($jenisitem == 'sampingan') {
                                    // $query->whereIn('items.category_id', ['4', '10', '16']);
                                    $query->whereIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                } else if ($jenisitem == 'nonsampingan') {
                                    $query->whereNotIn(
                                        'nama_detail',
                                        [
                                            "AMPELA BERSIH BROILER",
                                            "AY - S",
                                            "HATI AMPELA BERSIH BROILER",
                                            "HATI AMPELA KOTOR BROILER",
                                            "HATI AMPELA KOTOR BROILER FROZEN",
                                            "HATI BERSIH BROILER",
                                            "KAKI BERSIH BROILER",
                                            "KAKI KOTOR BROILER",
                                            "KEPALA LEHER BROILER",
                                            "USUS BROILER",
                                            "TEMBOLOK"
                                        ]
                                    );
                                    // $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                    $query->whereNotIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                    // $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                }
                            })
                            ->where('orders.nama', $value->to_customer->nama)
                            ->whereBetween('orders.tanggal_kirim', [$tanggal, $tanggal_akhir])
                            ->sum('order_items.fulfillment_berat')) *100,2,',','.') }}
                            %
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">TOTAL</td>
                @if (count($countTotalKualitas) > 0)
                    @foreach ($countTotalKualitas as $perKualitas)
                        <td>{{ number_format(App\Models\ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')
                        ->join('items', 'retur_item.item_id', 'items.id')
                        ->where(function($query) use ($jenisitem) {
                            if ($jenisitem == 'sampingan') {
                                $query->whereIn('items.category_id', ['4', '10', '16']);
                            } else if ($jenisitem == 'nonsampingan') {
                                $query->whereNotIn('items.category_id', ['4', '10', '16']);
                            }
                        })
                        ->where('retur.no_ra', '!=', null)->whereIn('retur.status', [1, 2])
                        ->where('catatan', $perKualitas->catatan)
                        ->whereBetween('tanggal_retur', [$tanggal, $tanggal_akhir])
                        ->sum('berat'),2,',','.') }}
                        </td>
                    @endforeach
                @else 
                    <td>-</td>
                @endif

                @if (count($countTotalNonKualitas) > 0)
                    @foreach ($countTotalNonKualitas as $perNonKualitas)
                        <td>{{ number_format(App\Models\ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')
                        ->join('items', 'retur_item.item_id', 'items.id')
                        ->where(function($query) use ($jenisitem) {
                            if ($jenisitem == 'sampingan') {
                                $query->whereIn('items.category_id', ['4', '10', '16']);
                            } else if ($jenisitem == 'nonsampingan') {
                                $query->whereNotIn('items.category_id', ['4', '10', '16']);
                            }
                        })
                        ->where('retur.no_ra', '!=', null)->whereIn('retur.status', [1, 2])
                        ->where('catatan', $perNonKualitas->catatan)
                        ->whereBetween('tanggal_retur', [$tanggal, $tanggal_akhir])->sum('berat'),2,',','.') }}
                        </td>
                    @endforeach
                @else 
                    <td>-</td>
                @endif
                <td>{{ number_format(App\Models\ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')
                        ->join('items', 'retur_item.item_id', 'items.id')
                        ->where(function($query) use ($jenisitem) {
                            if ($jenisitem == 'sampingan') {
                                $query->whereIn('items.category_id', ['4', '10', '16']);
                            } else if ($jenisitem == 'nonsampingan') {
                                $query->whereNotIn('items.category_id', ['4', '10', '16']);
                            }
                        })
                        ->where('retur.no_ra', '!=', null)->whereIn('retur.status', [1, 2])
                        ->whereBetween('tanggal_retur', [$tanggal, $tanggal_akhir])->sum('berat'),2,',','.') }}
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<script>
    $("#jenisitem").on("change", function() {
        loadExportRetur();
    })


    $('.select2').select2({
        theme: 'bootstrap4'
    });
</script>