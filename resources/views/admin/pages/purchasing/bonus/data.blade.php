<div class="table-responsive">
    <table class="table default-table">
        <thead>
            <tr>
                <th class="text">No</th>
                <th class="text">Tanggal</th>
                <th class="text">Supir</th>
                <th class="text">Ekspedisi</th>
                <th class="text">Nama Kandang</th>
                <th class="text">Wilayah</th>
                <th class="text">Ekor DO</th>
                <th class="text">Berat DO</th>
                <th class="text">% Susut</th>
                <th class="text">% Toleransi</th>
                <th class="text">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $no => $row)
            @php
                $toleransi  =   App\Models\Target::where('alamat', 'like', '%' . preg_replace('/\s+/', '', $row->sc_wilayah) . '%')->orderBy('id', 'DESC')->first()->target ?? 0 ;
                // $nama_suplier = App\Models\Supplier::where('nama', 'like', '%' . preg_replace('/\s+/', '', $row->sc_nama_kandang).'%')->first();
                if($row->sc_nama_kandang == 'CGF' || $row->sc_nama_kandang == 'Citra Giandra Farms' || $row->sc_nama_kandang == 'Citra Giandra Farms PT'){
                    $supplierNama  = strtoupper("Citra Giandra Farms PT");
                }else{
                    $supplierNama  = $row->sc_nama_kandang;
                }
            @endphp
            <tr>
                <td class="text">{{ ++$no }}</td>
                <td class="text">{{ $row->sc_tanggal_masuk }}</td>
                <td class="text">{{ $row->sc_pengemudi }}</td>
                <td class="text">{{ $row->prodpur->type_ekspedisi }}</td>
                <td class="text">{{ $supplierNama }}</td>
                <td class="text">{{ $row->sc_wilayah }}</td>
                <td class="text">{{ number_format($row->sc_ekor_do) }}</td>
                <td class="text">{{ number_format($row->sc_berat_do, 2) }}</td>
                <td class="text">{{ number_format($row->lpah_persen_susut, 2) }}</td>
                <td class="text">{{ number_format($row->lpah_persen_susut ? $toleransi : 0, 2) }}</td>
                <td class="text">
                    @if ($row->lpah_persen_susut && $toleransi)
                        @if ($toleransi >= $row->lpah_persen_susut)
                            <span class='text-success'>IN</span>
                        @else
                            <span class='text-danger'>OUT</span>
                        @endif
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
