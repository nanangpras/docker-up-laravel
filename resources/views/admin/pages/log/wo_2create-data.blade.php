<section class="panel">
    <div class="card-body">
        <table class="table default-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Regu</th>
                    <th>Total BB</th>
                    <th>Total FG</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $i => $row)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $row->tanggal }}</td>
                    <td>{{ $row->regu }}</td>
                    @php
                        $total_bb = 0;
                        $total_fg = 0;
                        $get_total_bb = App\Models\FreestockList::select(DB::raw("SUM(berat) AS kg"), DB::raw("SUM(qty) AS jumlah"))
                                                                ->leftJoin('items', 'items.id', '=', 'free_stocklist.item_id')
                                                                ->leftJoin('chiller', 'chiller.id', '=', 'free_stocklist.chiller_id')
                                                                ->leftJoin('free_stock', 'free_stock.id', '=', 'free_stocklist.freestock_id')
                                                                ->where('free_stock.regu', $row->regu)
                                                                ->where('free_stock.status', '3')
                                                                ->where('free_stock.tanggal', $row->tanggal)
                                                                ->where('free_stock.netsuite_id', null)
                                                                ->whereNull('free_stock.netsuite_send')
                                                                ->orderBy('items.nama')
                                                                ->groupBy('items.nama')
                                                                ->groupBy('chiller.type')
                                                                ->get();
                        foreach ($get_total_bb as $item) {
                            $total_bb += $item->kg;
                        }

                        $get_total_fg = App\Models\FreestockTemp::select(DB::raw("SUM(qty) AS jumlah"), DB::raw("SUM(berat) AS kg"))
                       
                                                                ->leftJoin('items', 'items.id', '=', 'free_stocktemp.item_id')
                                                                ->leftJoin('free_stock', 'free_stock.id', '=', 'free_stocktemp.freestock_id')

                                                                ->where('free_stock.regu', $row->regu)
                                                                ->where('free_stock.status', '3')
                                                                ->where('free_stock.tanggal', $row->tanggal)
                                                                ->where('free_stock.netsuite_id', null)

                                                                ->whereNull('free_stock.netsuite_send')
                                                                ->orderBy('items.nama')
                                                                ->groupBy('items.nama')
                                                                ->get() ;
                        foreach ($get_total_fg as $fg) {
                            $total_fg += $fg->kg;
                        }
                    @endphp
                        {{-- {{ dd($total_bb)}} --}}
                        <td>{{ $total_bb }}</td>
                    <td>{{ $total_fg }}</td>
                    <td>
                        <a href="{{ route('wo.create', ['tanggal' => $row->tanggal, 'regu' => $row->regu]) }}" class="btn btn-success">Buat WO</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
