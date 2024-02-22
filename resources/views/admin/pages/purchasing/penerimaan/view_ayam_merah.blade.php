@if($download)
@php
    header('Content-Transfer-Encoding: none');
    header("Content-type: application/vnd-ms-excel");
    header("Content-type: application/x-msexcel");
    header("Content-Disposition: attachment; filename=laporan-ayam-merah " . ($tanggal_mulai ?? date('Y-m-d')) . " - " . ($tanggal_selesai ?? date('Y-m-d')) . ".xls");
@endphp
@endif

@if($hidden)
<div class="form-group"> 
    <button type="button" class="btn btn-success exportlaporanayammerah float-right my-2 mx-4"> 
        <i class="fa fa-download icondownload"></i>
        <i class="fa fa-spinner fa-spin spinerloading" style="display:none;"></i> 
        <span id="text">Export Excel</span>
    </button>
</div>
@endif
<section class="panel">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table default-table" id="tableayammerah" border="1">
                <thead>
                    <tr class="text-center">
                        <th width="3%">No</th>
                        <th rowspan="1">TANGGAL</th>
                        <th rowspan="1">VENDOR</th>
                        <th rowspan="1">DRIVER</th>
                        <th rowspan="1">EKSPEDISI</th>
                        <th rowspan="1">JUMLAH DO</th>
                        <th colspan="1">LPAH EXCL AYAM MERAH</th>
                        <th colspan="1">AYAM MERAH</th>
                        <th colspan="1">LPAH</th>
                        <th colspan="1">MATI</th>
                        <th colspan="1">SELISIH</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($produksi as $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration}}</td>
                            <td class="text-center">{{ $item->prod_tanggal_potong}}</td>
                            <td class="text-center">{{ $item->prodpur->purcsupp->nama}}</td>
                            <td class="text-center">{{ $item->sc_pengemudi }}</td>
                            <td class="text-center">{{ $item->po_jenis_ekspedisi }}</td>
                            <td class="text-center">{{ $item->sc_ekor_do }}</td>
                            <td class="text-center">{{ $item->ekoran_seckle }}</td>
                            <td class="text-center">{{ $item->qc_ekor_ayam_merah }}</td>
                            <td class="text-center">{{ $item->ekoran_seckle + $item->qc_ekor_ayam_merah }}</td>
                            <td class="text-center">{{ $item->qc_ekor_ayam_mati}}</td>
                            <td class="text-center">{{ $item->sc_ekor_do - ($item->ekoran_seckle + $item->qc_ekor_ayam_merah) - $item->qc_ekor_ayam_mati }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center"><strong>Tidak Ditemukan Data</strong> </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
<script>
    $(".exportlaporanayammerah").click(function(){
        var tanggal_mulai   = "{{ $tanggal_mulai }}";
        var tanggal_selesai = "{{ $tanggal_selesai }}";
        var jenis_ekspedisi = "{{ $jenis_ekspedisi }}";
        $.ajax({
            url     : "{{ route('laporan.laporanayammerah') }}",
            method  : "GET",
            cache   : false,
            data    :{
                key             : "showData",
                tanggal_mulai   : tanggal_mulai,
                tanggal_selesai : tanggal_selesai,
                jenis_ekspedisi : jenis_ekspedisi
            },
            beforeSend: function() {
                $('.exportlaporanayammerah').attr('disabled');
                $(".icondownload").hide(); 
                $(".spinerloading").show(); 
                $("#text").text('Downloading...');
            },
            success: function(data) {
                $(".exportlaporanayammerah").attr('disabled');
                setTimeout(() => {
                    $("#text").text('Export Excel');
                    $(".spinerloading").hide();
                    $(".icondownload").show();
                    window.location.href = "{{ route('laporan.laporanayammerah') }}?tanggal_mulai=" + tanggal_mulai + "&tanggal_selesai=" + tanggal_selesai + "&jenis_ekspedisi=" + jenis_ekspedisi + "&key=download";
                }, 1000);
            }
        });
    })
</script>
