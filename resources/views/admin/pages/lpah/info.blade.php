<div class="row border-bottom">
    <div class="col-md-4 col-6 mb-4">
        <div class="form-group">
            <div class="small">NOMOR PO</div>
            <b>{{ $data->prodpur->no_po ?? '###' }}</b>
        </div>
    </div>
    <div class="col-md-4 col-6">
        <div class="form-group">
            <div class="small">NOMOR MOBIL</div>
            <b>{{ $data->no_urut ?? '###' }}</b>
        </div>
    </div>

    <div class="col-md-4 col-6">
        <div class="form-group">
            <div class="small">SUPPLIER</div>
            <b>{{ $data->prodpur->purcsupp->nama }}</b>
        </div>
    </div>

    <div class="col-md-4 col-6">
        <div class="form-group">
            <div class="small">WILAYAH</div>
            <b class="text-capitalize">{{ $data->sc_wilayah ?? '####' }}</b>
        </div>
    </div>

    <div class="col-md-4 col-6">
        <div class="form-group">
            <div class="small">JENIS EKSPEDISI</div>
            <b class="text-capitalize">{{ $data->po_jenis_ekspedisi }}</b>
        </div>
    </div>


    <div class="col-md-4 col-6">
        <div class="form-group">
            <div class="small">NO POLISI</div>
            <b>{{ $data->sc_no_polisi }}</b>
        </div>
    </div>

    <div class="col-md-4 col-6">
        <div class="form-group">
            <div class="small">EKOR DO</div>
            <b>{{ number_format($data->sc_ekor_do) }} Ekor</b>
        </div>
    </div>

    <div class="col-md-4 col-6">
        <div class="form-group">
            <div class="small">BERAT DO</div>
            <b>{{ number_format($data->sc_berat_do, 1) }} KG</b>
        </div>
    </div>

    <div class="col-md-4 col-6">
        <div class="form-group">
            <div class="small">UKURAN AYAM</div>
            <b>@if ($data->prodpur->ukuran_ayam == '&lt; 1.1') {{ '<1.1' }} @else {{ $data->prodpur->ukuran_ayam }} @endif</b>
        </div>
    </div>

</div>


<div class="row mt-3 pb-2">

    <div class="col-md-4 col-6">
        <div class="form-group">
            <div class="small">EKOR LPAH</div>
            <b>{{ number_format($data->total_lpah) }} Ekor</b>
        </div>
    </div>


    <div class="col-md-4 col-6">
        <div class="form-group">
            <div class="small">BERAT BERSIH LPAH</div>
            <b>{{ $data->lpah_berat_terima }} KG</b>
        </div>
    </div>

</div>


<div class="row mt-3 pb-2 border-bottom">
    <div class="col-md-4 col-6">
        <div class="form-group">
            <div class="small">KERANJANG KOSONG</div>
            <b>{{ number_format($data->berat_keranjang, 1) }} KG</b>
        </div>
    </div>

    <div class="col-md-4 col-6">
        <div class="form-group">
            <div class="small">KERANJANG ISI</div>
            <b>{{ number_format($data->berat_isi, 1) }} KG</b>
        </div>
    </div>
    <div class="col-md-4 col-6">
        <div class="form-group">
            <div class="small">TEMBOLOK</div>
            <b>{{ number_format($data->qc_tembolok, 1) }} KG</b>
        </div>
    </div>
</div>

<div class="row mt-3">

    <div class="col-md-4 col-6">
        <div class="form-group">
            <div class="small">KONDISI AYAM</div>
            <b>{{ $data->kondisi_ayam }}</b>
        </div>
    </div>


    <div class="col-md-4 col-6">
        <div class="form-group">
            <div class="small">AYAM MATI</div>
            <b>{{ number_format($data->qc_ekor_ayam_mati) }} Ekor</b>
        </div>
    </div>

    <div class="col-md-4 col-6">
        <div class="form-group">
            <div class="small">BERAT AYAM MATI</div>
            <b>{{ number_format($data->qc_berat_ayam_mati, 1) }} KG</b>
        </div>
    </div>
</div>
<div class="row mt-3">

    <div class="col-md-4 col-6">
        <div class="form-group">
            <div class="small">RATA RATA TERIMA</div>
            @php
            $pisahKomaRataRata = explode('.', $data->lpah_rerata_terima)
            @endphp

            @if($pisahKomaRataRata[1] ?? FALSE)
                @if (strlen($pisahKomaRataRata[1]) > 1) 
                <b>{{ mb_substr($data->lpah_rerata_terima, 0, -1) }} KG</b>
                @else
                <b>{{ $data->lpah_rerata_terima }} KG</b>
                @endif
            @else
            <b>{{ $data->lpah_rerata_terima }} KG</b>
            @endif


        </div>
    </div>
    <div class="col-md-4 col-6">
        <div class="form-group">
            <div class="small">AYAM MERAH</div>
            <b>{{ $data->qc_ekor_ayam_merah }} Ekor</b>
        </div>
    </div>


    <div class="col-md-4 col-6">
        <div class="form-group">
            <div class="small">AYAM MERAH KG</div>
            <b>{{ number_format($data->qc_berat_ayam_merah, 1) }} KG</b>
        </div>
    </div>

    <div class="col-md-4 col-6">
        
    </div>
</div>

<div class="row mt-3">

    <div class="col-md-4 col-6">
        <div class="form-group">
            <div class="small">TANGGAL POTONG</div>
            <b>{{ $data->prod_tanggal_potong }}</b>
        </div>
    </div>


    <div class="col-md-4 col-6">
        <div class="form-group">
            <div class="small">OPERATOR</div>
            <b>{{ $data->lpah_user_nama }}</b>
        </div>
    </div>

    <div class="col-md-4 col-6">
        <div class="form-group">
            <div class="small">JAM BONGKAR</div>
            <b>{{ $data->lpah_jam_bongkar }}</b>
        </div>
    </div>
    {{-- <div class="col-md-4 col-6">
        <div class="form-group">
            <div class="small">AYAM MERAH</div>
            <b>{{ $data->qc_ekor_ayam_merah }}</b>
        </div>
    </div> --}}
    {{-- <div class="col-md-4 col-6">
        <div class="form-group">
            <div class="small">BERAT AYAM MERAH</div>
            <b>{{ $data->qc_berat_ayam_merah ?? '' }}</b>
        </div>
    </div> --}}
    <div class="col-md-4 col-6">
        <div class="form-group">
            <div class="small">TOTAL KERANJANG</div>
            <b>{{ $data->lpah_jumlah_keranjang ?? ''}}</b>
        </div>
    </div>
</div>
