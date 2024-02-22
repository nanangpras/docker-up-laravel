@php
    header('Content-Transfer-Encoding: none');
    header("Content-type: application/vnd-ms-excel");
    header("Content-type: application/x-msexcel");
    header("Content-Disposition: attachment; filename=$filename");
@endphp
<style>
    .text-center{
        vertical-align: middle; 
        text-align: center;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table default-table" width="100%" border="1">
                <thead>
                    <tr>
                        <th rowspan="2" width="5%" class="text-center">No</th>
                        <th rowspan="2" width="10%" class="text-center">Tanggal</th>
                        <th rowspan="2" width="15%" class="text-center">FARM</th>
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
                            <td class="text-center">{{ $row->sc_nama_kandang}} </td>
                            <td class="text-center">{{ $row->no_urut}}</td>
                            <td class="text-center" style="background: #fde0dd">{{ $row->ekoran_seckle}}</td>
                            <td class="text-center">{{ $row->lpah_berat_terima}}</td>
                            <td class="text-center">{{ $row->lpah_rerata_terima}}</td>
                            <td class="text-center">{{ $row->qty_evis_production}}</td>
                            <td class="text-center">{{ $row->berat_evis_production}}</td>
                            <td class="text-center" style="background: #fde0dd">{{ $row->qty_grading}}</td>
                            <td class="text-center">{{ number_format($row->berat_item_grading,2) }}</td>
                            <td class="text-center">{{ $row->selisih_lpah_grading}}</td>
                        </tr>
                        @php  
                            $totallpah += $row->ekoran_seckle; 
                            $totalevis += $row->qty_evis_production; 
                            $totalgrading += $row->qty_grading; 
                            $totalselisih += $row->selisih_lpah_grading; 
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
                        <td class="text-center" style="background: #fde0dd"><strong>{{ $totallpah }}</strong></td>
                        <td class="text-center" colspan="2"><strong></strong></td>
                        <td class="text-center" ><strong>{{ $totalevis }}</strong></td>
                        <td class="text-center"><strong></strong></td>
                        <td class="text-center" style="background: #fde0dd"><strong>{{ $totalgrading }}</strong></td>
                        <td class="text-center"><strong></strong></td>
                        <td class="text-center" style="background: #fde0dd"><strong>{{ $totalselisih }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>