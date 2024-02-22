<div class="row">
    <div class="col-md-12">
        <a href="{{ route('kepalaproduksi.hasilpotong', ['key' => 'unduh']) }}&tanggal={{ $tanggal }}&tanggalend={{ $tanggalend }}" class="btn btn-success mb-2 float-right unduhselisihlpahgrading"><i class="fa fa-download"></i>  Unduh</a>
        <div class="table-responsive">
            <table class="table default-table" width="100%">
                <thead>
                    <tr>
                        <th rowspan="2" width="5%" class="text-center">No</th>
                        <th rowspan="2" width="10%" class="text-center">Tanggal</th>
                        <th rowspan="2" width="15%" class="text-center">SUPPLIER</th>
                        <th rowspan="2" width="5%" class="text-center">Mobil</th>
                        <th colspan="3" width="20%" class="text-center">KENYATAAN DITERIMA</th>
                        <th colspan="2" width="15%" class="text-center">EVIS KEPALA</th>
                        <th colspan="2" width="15%" class="text-center">GRADING</th>
                        <th rowspan="2" width="15%" class="text-center">SELISIH EKOR LPAH & GRADING</th>
                    </tr>
                    <tr class="text-center">
                        <th class="text-center">Ekor/Pcs/Pack</th>
                        <th class="text-center">Kg</th>
                        <th class="text-center">Rata2 Kg</th>
                        <th class="text-center">Ekor</th>
                        <th class="text-center">Kg(total)</th>
                        <th class="text-center">Ekor</th>
                        <th class="text-center">Kg</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totallpah      = 0;
                        $totalevis      = 0;
                        $totalgrading   = 0;
                        $totalselisih   = 0;
                        $totalkgterima  = 0;
                        $totalkgevis    = 0;
                        $totalkggrading = 0;
                    @endphp
                    @php
                        $no = 1;
                    @endphp
                    @foreach($selisih as $i => $row)
                        @php 
                            $cekLpahStatus = \App\Models\Production::cekLpahStatus($row->id,$row->sc_tanggal_masuk);
                        @endphp
                        @if($cekLpahStatus == 'OK' || $cekLpahStatus != '2')
                        <tr>
                            <td class="text-center">{{ $no }}</td>
                            <td class="text-center">{{ $row->prod_tanggal_potong}}</td>
                            <td class="text-center">{{ $row->prodpur->purcsupp->nama ?? "SUPPLIER TIDAK DITEMUKAN" }} </td>
                            <td class="text-center">{{ $row->no_urut}}</td>
                            <td class="text-center" style="background: #fde0dd">{{ number_format($row->ekoran_seckle) }}</td>
                            <td class="text-center">{{ number_format($row->lpah_berat_terima, 2) }}</td>
                            <td class="text-center">{{ $row->lpah_rerata_terima }}</td>
                            <td class="text-center">{{ number_format($row->qty_evis_production) }}</td>
                            <td class="text-center">{{ number_format($row->berat_evis_production, 2) }}</td>
                            <td class="text-center" style="background: #fde0dd">{{ number_format($row->qty_grading) }}</td>
                            <td class="text-center">{{ number_format($row->berat_item_grading, 2) }}</td>
                            <td class="text-center">{{ number_format($row->selisih_lpah_grading) }}</td>
                        </tr>
                        @php  
                            $totallpah += $row->ekoran_seckle; 
                            $totalevis += $row->qty_evis_production; 
                            $totalgrading += $row->qty_grading; 
                            $totalselisih += $row->selisih_lpah_grading; 
                            $totalkgterima += $row->lpah_berat_terima; 
                            $totalkgevis += $row->berat_evis_production; 
                            $totalkggrading += $row->berat_item_grading; 
                        @endphp
                        @php 
                            ++$no;
                        @endphp
                        @endif
                    @endforeach
                    <tr>
                        <td class="text-center" colspan="2"></td>
                        <td class="text-center"><strong> Jumlah </strong></td>
                        <td class="text-center"><strong></strong></td>
                        <td class="text-center" style="background: #fde0dd"><strong>{{ number_format($totallpah) }}</strong></td>
                        <td class="text-center"><strong>{{ number_format($totalkgterima, 2) }}</strong></td>
                        <td class="text-center"><strong></strong></td>
                        <td class="text-center" ><strong>{{ number_format($totalevis) }}</strong></td>
                        <td class="text-center"><strong>{{ number_format($totalkgevis,2) }}</strong></td>
                        <td class="text-center" style="background: #fde0dd"><strong>{{ number_format($totalgrading) }}</strong></td>
                        <td class="text-center"><strong>{{ number_format($totalkggrading, 2) }}</strong></td>
                        <td class="text-center" style="background: #fde0dd"><strong>{{ number_format($totalselisih) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>