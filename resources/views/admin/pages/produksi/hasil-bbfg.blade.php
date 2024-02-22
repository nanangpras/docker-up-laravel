@extends('admin.layout.template')

@section('title', 'Laporan Bahan Baku Finish Good')

@section('content')
    <div class="row mb-4">
        <div class="col">
            <a href="" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
                Back</a>
        </div>
        <div class="col text-center">
            <b>LAPORAN BAHAN BAKU DAN FINISH GOOD</b>
        </div>
        <div class="col"></div>
    </div>

    <section class="panel">
        <div class="card card-primary card-outline">
            <div class="card-body">

                <form action="{{ route('laporanproduksi.hasilbbfg') }}" method="get">
                    <div class="row">
                        <div class="col-md-6 col-sm-4 col-xs-6">
                            Pencarian
                            <input id="tanggalbbfg" type="date" class="form-control change-date" name="tanggal" value="{{ $tanggal }}">
                        </div>
                    </div>
                </form>
                <br>
                <div class="row">
                    <div class="col-md-6 col-sm-4 col-xs-6">
                        {{-- <a href="{{ route('laporanproduksi.index',array_merge(['key'=>'export'],$_GET)) }}" class="btn btn-blue">Export Excel</a> --}}
                        <a href="{{ route('laporanproduksi.hasilbbfg',array_merge(['key'=>'export'],$_GET)) }}" id="export_report" type="button" class="btn btn-blue"> Export</a>
                    </div>
                </div>
                <br>
                <div id="loading" class="text-center"><img src="{{ asset('loading.gif') }}" style="width: 18px">  Loading...</div>
                <br>
                <div class="row">
                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table default-table" width="100%">
                                <thead>
                                    <tr>
                                        <th class="text-info" colspan="4">Bahan Baku</th>
                                    </tr>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Item</th>
                                        <th>Ekor/Pcs/Pack</th>
                                        <th>Berat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $ekor = 0;
                                        $berat = 0;
                                    @endphp
                                    @foreach ($bahan_baku as $i => $row)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>
                                                {{ $row->nama }}
                                                @if ($row->type == 'hasil-produksi')
                                                    <span class="status status-info">FG</span>
                                                @elseif($row->type == 'bahan-baku')
                                                    <span class="status status-danger">BB</span>
                                                @endif
                                            </td>
                                            <td>{{ number_format($row->jumlah) }}</td>
                                            <td>{{ number_format($row->kg, 2) }} Kg</td>
                                        </tr>
                                        @php
                                            $ekor += $row->jumlah;
                                            $berat += $row->kg;
                                        @endphp
                                    @endforeach
                                    <tr>
                                        <td></td>
                                        <td>Total</td>
                                        <td>{{ $ekor }}</td>
                                        <td>{{ number_format($berat, 2) }} Kg</td>
                                    </tr>
                                </tbody>
                            </table>
                            @php
                                $total_bb = $berat;
                            @endphp
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table default-table" width="100%">
                                <thead>
                                    <tr>
                                        <th class="text-info" colspan="4">Hasil Produksi</th>
                                    </tr>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Item</th>
                                        <th>Ekor/Pcs/Pack</th>
                                        <th>Berat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $ekor = 0;
                                        $berat = 0;
                                    @endphp
                                    @foreach ($hasil_produksi as $i => $row)
                                        @php
                                            foreach ($bom as $item) {
                                                $bom_item = \App\Models\BomItem::where('sku', $row->sku)
                                                    ->where('bom_id', $item->id)
                                                    ->first();
                                            
                                                $item_cat = \App\Models\Item::find($row->item_id);
                                            
                                                $type = ($item_cat->category_id == 4 or $item_cat->category_id == 6 or $item_cat->category_id == 10 or $item_cat->category_id == 16) ? 'By Product' : 'Finished Goods';
                                                if ($bom_item) {
                                                    $type = $bom_item->kategori;
                                                }
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $row->nama }}
                                                {{-- Finished Goods --}}
                                                @if ($type == 'Finished Goods')
                                                    <span class="status status-success">{{ $type }}</span>
                                                @else
                                                    <span class="status status-warning">{{ $type }}</span>
                                                @endif
                                            </td>
                                            <td>{{ number_format($row->jumlah) }}</td>
                                            <td>{{ number_format($row->kg, 2) }} Kg</td>
                                        </tr>
                                        @php
                                            $ekor = $ekor + $row->jumlah;
                                            $berat = $berat + $row->kg;
                                        @endphp
                                    @endforeach
                                    <tr>
                                        <td></td>
                                        <td>Total</td>
                                        <td>{{ $ekor }}</td>
                                        <td>{{ number_format($berat, 2) }} Kg</td>
                                    </tr>
                                </tbody>
                            </table>
                            @php
                                $total_fg = $berat;
                            @endphp

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
@section('footer')
    <script>
        $("#export_report").click(function () { 
            // alert('ok');
            
        });
        $('#loading').hide();
        $("#tanggalbbfg").on('change', function() {
            // var tanggal = $(this).val();
            $(this).closest("form").submit();
            $('#loading').show();
        });
    </script>
@stop
