{{-- <div class="my-3 font-weight-bold text-center">Daftar BOM</div> --}}

<section class="panel">
    <div class="card-body">
        <div class="accordion" id="accordionActualOrder">
            @if ($dataDiff != '') 
                @foreach ($dataActual as $row)
                <div class="card">
                    <div class="card-header" id="headingOne">
                        <div data-toggle="collapse" data-target="#collapse{{ $row->id }}" aria-expanded="true" aria-controls="collapse{{ $row->id }}">
                            {{ $row->no_so }} || {{ $row->socustomer->nama }}
                        </div>
                    </div>
                    
                    <div id="collapse{{ $row->id }}" class="collapse" aria-labelledby="headingOne" data-parent="#accordionActualOrder">
                        <div class="card-body p-2">
                            <div class="border-bottom p-1">
                                <table class="table default-table">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th>Qty</th>
                                            <th>Berat</th>
                                            <th>Parting</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($row->itemActual as $key => $item)
                                        <tr 
                                        @if (isset($dataDiff[$key]))
                                            @if($dataDiff[$key] == $item->line_id) style="background-color: #FFFF8F" @endif
                                        @endif
                                        >
                                            <td>{{ $item->item_nama ?? '' }}</td>
                                            <td>{{ $item->qty ?? ''}}</td>
                                            <td>{{ $item->berat ?? ''}}</td>
                                            <td>{{ $item->parting ?? ''}}</td>
                                            <td>@if (isset($dataDiff[$key]))
                                                    @if($dataDiff[$key] == $item->line_id) 
                                                    Belum Ada di Produksi 
                                                    @endif
                                                @else 
                                                    Ada di Produksi 
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            @else
                <h2 class="text-center"> DATA KOSONG </h2>
            @endif
        </div>
    </div>
</section>