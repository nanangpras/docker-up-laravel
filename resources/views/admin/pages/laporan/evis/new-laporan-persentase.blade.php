<section class="panel">
    <div class="card-body">
        <table class="table default-table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th rowspan="2">Item</th>
                    <th colspan="3">Hasil Produksi</th>
                    <th rowspan="2">Total Paket&Eceran</th>
                    <th rowspan="2">Stock Frozen</th>
                    <th rowspan="2">Sisa</th>
                    <th rowspan="2">Stock Chiller</th>
                    <th rowspan="2">Selisih Susut</th>
                    <th rowspan="2">%</th>
                </tr>
                <tr>
                    <th>Baru</th>
                    <th>Lama</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($arrTotal as $it)
                    <tr>
                        <td>{{ $it['item'] }}</td>
                        <td>{{ $it['baru'] }}</td>
                        <td>{{ $it['lama'] }}</td>
                        <td>{{ $it['total'] }}</td>
                        <td>0</td>
                        <td>{{ $it['stock_frozen'] }}</td>
                        <td>{{ number_format($it['sisa'])}}</td>
                        <td>{{ number_format($it['stock_chiller'],2) }}</td>
                        <td>{{ number_format($it['sisa'] - $it['stock_chiller'],2)}}</td>
                        <td>%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-body mt-2">
        <div class="table-responsive">
            <table class="default-table" width="2000px">
                <thead>
                    <tr>
                        <th rowspan="2" class="center" width="100px">Tanggal</th>
                        <th colspan="6" class="center">Hasil Produksi</th>
                        <th rowspan="2" class="center">Ati / Ampela Bersih (Kg) </th>
                        <th colspan="6" class="center">Penjualan Barang Baru</th>
                        <th colspan="6" class="center">Sisa / Stok</th>
                    </tr>
                    <tr>
                        <th class="center" width="100px">Ati / Ampela (Kg)</th>
                        <th class="center" width="100px">Ati (Kg)</th>
                        <th class="center" width="100px">Ampela (Kg)</th>
                        <th class="center" width="100px">Kepala (Kg)</th>
                        <th class="center" width="100px">Kaki(Kg)</th>
                        <th class="center" width="100px">Usus(Kg)</th>
                        
                        <th class="center" width="100px">Ati / Ampela (Kg)</th>
                        <th class="center" width="100px">Ati (Kg)</th>
                        <th class="center" width="100px">Ampela (Kg)</th>
                        <th class="center" width="100px">Kepala (Kg)</th>
                        <th class="center" width="100px">Kaki(Kg)</th>
                        <th class="center" width="100px">Usus(Kg)</th>
                    
                        <th class="center" width="100px">Ati / Ampela (Kg)</th>
                        <th class="center" width="100px">Ati (Kg)</th>
                        <th class="center" width="100px">Ampela (Kg)</th>
                        <th class="center" width="100px">Kepala (Kg)</th>
                        <th class="center" width="100px">Kaki(Kg)</th>
                        <th class="center" width="100px">Usus(Kg)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $start          = new DateTime($mulai);
                        $last           = new DateTime($selesai);
                        $last->modify('+1 day');
                        
                        $interval       = DateInterval::createFromDateString ('+1 day') ;
                        
                        $periods        = new DatePeriod($start, $interval, $last) ;
                    @endphp
                    @foreach ($periods as $dt)
                        <tr>
                            <td class="center">
                                {{ $dt->format('Y-m-d') }}
                            </td>
                            <td class="center">
                                @foreach($arrHasilProd['atiampela'] as $aa)
                                    @if($dt->format('Y-m-d') == $aa->tanggal_produksi)
                                        {{ number_format($aa->bb_ati_ampela,2) }}
                                    @endif
                                @endforeach
                            </td>
                            <td class="center">
                                @foreach($arrHasilProd['ati'] as $at)
                                    @if($dt->format('Y-m-d') == $at->tanggal_produksi)
                                        {{ number_format($at->bb_ati,2) }}
                                    @endif
                                @endforeach
                            </td>
                            <td class="center">
                                @foreach($arrHasilProd['ampela'] as $ampel)
                                    @if($dt->format('Y-m-d') == $ampel->tanggal_produksi)
                                        {{ number_format($ampel->bb_ampela,2) }}
                                    @endif
                                @endforeach
                            </td>
                            <td class="center">
                                @foreach($arrHasilProd['kepala'] as $ndas)
                                    @if($dt->format('Y-m-d') == $ndas->tanggal_produksi)
                                        {{ number_format($ndas->bb_kepala,2) }}
                                    @endif
                                @endforeach
                            </td>
                            <td class="center">
                                @foreach($arrHasilProd['kaki'] as $sikil)
                                    @if($dt->format('Y-m-d') == $sikil->tanggal_produksi)
                                        {{ number_format($sikil->bb_kaki,2) }}
                                    @endif
                                @endforeach
                            </td>
                            <td class="center">
                                @foreach($arrHasilProd['usus'] as $uu)
                                    @if($dt->format('Y-m-d') == $uu->tanggal_produksi)
                                        {{ number_format($uu->bb_usus,2) }}
                                    @endif
                                @endforeach
                            </td>
                            <td class="center">
                                @foreach($arrHasilProd['hatibersih'] as $hb)
                                    @if($dt->format('Y-m-d') == $hb->tanggal_produksi)
                                        {{ number_format($hb->bb_hatibersih,2) }}
                                    @endif
                                @endforeach
                            </td>

                            <td class="center">
                                @foreach($arrHasilProd['sell_ati_ampela'] as $saa)
                                    @if($dt->format('Y-m-d') == $saa->tanggal_kirim)
                                        {{ number_format($saa->sell_ati_ampela,2) }}
                                    @endif
                                @endforeach
                            </td>
                            <td class="center">
                                @foreach($arrHasilProd['sell_ati'] as $sa)
                                    @if($dt->format('Y-m-d') == $sa->tanggal_kirim)
                                        {{ number_format($sa->sell_ati,2) }}
                                    @endif
                                @endforeach
                            </td>
                            <td class="center">
                                @foreach($arrHasilProd['sell_ampela'] as $sampela)
                                    @if($dt->format('Y-m-d') == $sampela->tanggal_kirim)
                                        {{ number_format($sampela->sell_ampela,2) }}
                                    
                                    @endif
                                @endforeach
                            </td>
                            <td class="center">
                                @foreach($arrHasilProd['sell_kepala'] as $sk)
                                    @if($dt->format('Y-m-d') == $sk->tanggal_kirim)
                                        {{ number_format($sk->sell_kepala,2) }}
                                    @endif
                                @endforeach
                            </td>
                            <td class="center">
                                @foreach($arrHasilProd['sell_kaki'] as $skk)
                                    @if($dt->format('Y-m-d') == $skk->tanggal_kirim)
                                        {{ number_format($skk->sell_kaki,2) }}
                                    @endif
                                @endforeach
                            </td>
                            <td class="center">
                                @foreach($arrHasilProd['sell_usus'] as $sus)
                                    @if($dt->format('Y-m-d') == $sus->tanggal_kirim)
                                        {{ number_format($sus->sell_usus,2) }}
                                    @endif
                                @endforeach
                            </td>

                            <td class="center">
                                @foreach($arrHasilProd['stock_ati_ampela'] as $staa)
                                    @if($dt->format('Y-m-d') == $staa['tanggal'])
                                        {{ number_format($staa['result'],2) }}
                                    @endif
                                @endforeach
                            </td>
                            <td class="center">
                                @foreach($arrHasilProd['stock_ati'] as $hati)
                                    @if($dt->format('Y-m-d') == $hati['tanggal'])
                                        {{ number_format($hati['result'],2) }}
                                    @endif
                                @endforeach
                            </td>
                            <td class="center">
                                @foreach($arrHasilProd['stock_ampela'] as $ampel)
                                    @if($dt->format('Y-m-d') == $ampel['tanggal'])
                                        {{ number_format($ampel['result'],2) }}
                                    @endif
                                @endforeach
                            </td>
                            <td class="center">
                                @foreach($arrHasilProd['stock_kepala'] as $kpl)
                                    @if($dt->format('Y-m-d') == $kpl['tanggal'])
                                        {{ number_format($kpl['result'],2) }}
                                    @endif
                                @endforeach
                            </td>
                            <td class="center">
                                @foreach($arrHasilProd['stock_kaki'] as $kk)
                                    @if($dt->format('Y-m-d') == $kk['tanggal'])
                                        {{ number_format($kk['result'],2) }}
                                    @endif
                                @endforeach
                            </td>
                            <td class="center">
                                @foreach($arrHasilProd['stock_usus'] as $usus)
                                    @if($dt->format('Y-m-d') == $usus['tanggal'])
                                        {{ number_format($usus['result'],2) }}
                                    @endif
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

<style>
    .center{
        text-align: center;
    }
    .table-responsive{
        overflow-x: auto;
    }
    .color-light-blue{
        background-color: #D9E1F5;
    }
    .color-light-green{
        background-color: #C7DFB2;
    }
    .color-light-yellow{
        background-color: #EDEF6D;
    }
    .color-light-red{
        background-color: #F4CEAF;
    }
</style>

