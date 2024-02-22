<table width="100%" cellspacing="0" cellpadding="3" border="1" style="font-size: 12px">
    <tr>
        @if (Session::get('subsidiary') == 'CGL')
        <td rowspan="2" colspan="2" style="text-align: center;" width="100px"><img src="logo.png" width="120px">
        @else
        <td rowspan="2" colspan="2" style="text-align: center;" width="100px"><img src="EBA_export.png" width="120px">
        @endif
        <td rowspan="2" colspan="5" style="font-size: 14px; text-align: center"><b><u>BERITA ACARA PEMERIKSAAN
                    NEKROPSI</u></b></td>
        <td width="200px">No : </td>
    </tr>
    <tr>
        <td style="text-align: center">{{ $isi->nomor_surat }}</td>
    </tr>
    <tr>
        <td colspan="8" height="10px"></td>
    </tr>
    <tr>
        <td colspan="8">A. Keterangan Bahan Baku Ayam Hidup</td>
    </tr>
    <tr>
        <td>Tanggal penangkapan</td>
        <td width="10px" style="text-align: center">:</td>
        <td colspan="6">{{ date('d-m-Y', strtotime('-1 day', strtotime($nekropsi->prodpur->tanggal_potong))) }}</td>
    </tr>
    <tr>
        <td>Asal Farm</td>
        <td width="10px" style="text-align: center">:</td>
        <td colspan="6">{{ $nekropsi->sc_nama_kandang }}</td>
    </tr>
    <tr>
        <td>Alamat kandang</td>
        <td width="10px" style="text-align: center">:</td>
        <td colspan="6">{{ $nekropsi->sc_alamat_kandang }}</td>
    </tr>
    <tr>
        <td>Sopir</td>
        <td width="10px" style="text-align: center">:</td>
        <td colspan="6">{{ $nekropsi->sc_pengemudi }}</td>
    </tr>
    <tr>
        <td>No. DO</td>
        <td width="10px" style="text-align: center">:</td>
        <td colspan="6">{{ $nekropsi->no_do }}</td>
    </tr>
    <tr>
        <td>Jumlah ayam hidup</td>
        <td width="10px" style="text-align: center">:</td>
        <td colspan="6">{{ number_format($nekropsi->ekoran_seckle, 0) }}</td>
    </tr>
    <tr>
        <td>Jumlah ayam mati</td>
        <td width="10px" style="text-align: center">:</td>
        <td colspan="6">{{ number_format($nekropsi->qc_ekor_ayam_mati, 0) }} Ekor |
            {{ $nekropsi->qc_persen_ayam_mati }} %</td>
    </tr>
    <tr>
        <td>Hasil Nekropsi</td>
        <td width="10px" style="text-align: center">:</td>
        <td colspan="6"></td>
    </tr>
    <tr>
        <td></td>
        <td width="10px" style="text-align: center"></td>
        <td colspan="3">Mata</td>
        <td colspan="2"></td>
        <td>{{ $isi->sp_mata }}</td>
    </tr>
    <tr>
        <td></td>
        <td width="10px" style="text-align: center"></td>
        <td colspan="3">Hidung/Paruh</td>
        <td colspan="2"></td>
        <td>{{ $isi->sp_hidung }}</td>
    </tr>
    <tr>
        <td></td>
        <td width="10px" style="text-align: center"></td>
        <td colspan="3">Trachea</td>
        <td colspan="2"></td>
        <td>{{ $isi->sp_trakea }}</td>
    </tr>
    <tr>
        <td></td>
        <td width="10px" style="text-align: center"></td>
        <td colspan="3">Paru-paru</td>
        <td colspan="2"></td>
        <td>{{ $isi->sp_paru }}</td>
    </tr>
    <tr>
        <td></td>
        <td width="10px" style="text-align: center"></td>
        <td colspan="3">Air sacc (Kantung Udara)</td>
        <td colspan="2"></td>
        <td>{{ $isi->sp_kantung_udara }}</td>
    </tr>
    <tr>
        <td height="10px"></td>
        <td width="10px" style="text-align: center"></td>
        <td colspan="6"></td>
    </tr>
    <tr>
        <td></td>
        <td width="10px" style="text-align: center"></td>
        <td colspan="3">Jantung</td>
        <td colspan="2"></td>
        <td>{{ $isi->sp_jantung }}</td>
    </tr>
    <tr>
        <td height="10px"></td>
        <td width="10px" style="text-align: center"></td>
        <td colspan="6"></td>
    </tr>
    <tr>
        <td></td>
        <td width="10px" style="text-align: center"></td>
        <td colspan="3">Tembolok</td>
        <td colspan="2"></td>
        <td>{{ $isi->sp_tembolok }}</td>
    </tr>
    <tr>
        <td></td>
        <td width="10px" style="text-align: center"></td>
        <td colspan="3">Proventriculus</td>
        <td colspan="2"></td>
        <td>{{ $isi->sp_proventriculus }}</td>
    </tr>
    <tr>
        <td></td>
        <td width="10px" style="text-align: center"></td>
        <td colspan="3">Lambung (Ventriculuc)</td>
        <td colspan="2"></td>
        <td>{{ $isi->sp_lambung }}</td>
    </tr>
    <tr>
        <td></td>
        <td width="10px" style="text-align: center"></td>
        <td colspan="3">Usus</td>
        <td colspan="2"></td>
        <td>{{ $isi->sp_usus }}</td>
    </tr>
    <tr>
        <td></td>
        <td width="10px" style="text-align: center"></td>
        <td colspan="3">Hati</td>
        <td colspan="2"></td>
        <td>{{ $isi->sp_hati }}</td>
    </tr>
    <tr>
        <td></td>
        <td width="10px" style="text-align: center"></td>
        <td colspan="3">Limpa</td>
        <td colspan="2"></td>
        <td>{{ $isi->sp_limpa }}</td>
    </tr>
    <tr>
        <td height="10px"></td>
        <td width="10px" style="text-align: center"></td>
        <td colspan="6"></td>
    </tr>
    <tr>
        <td></td>
        <td width="10px" style="text-align: center"></td>
        <td colspan="3">Bursa Fabricius</td>
        <td colspan="2"></td>
        <td>{{ $isi->sp_fabricius }}</td>
    </tr>
    <tr>
        <td colspan="8" height="10px"></td>
    </tr>
    <tr>
        <td height="10px">B. Diagnosa</td>
        <td width="10px" style="text-align: center">:</td>
        <td colspan="6" style="text-align: justify">{{ $isi->diagnosa }}</td>
    </tr>
    <tr>
        <td colspan="8" height="10px"></td>
    </tr>
    <tr>
        <td colspan="8">C. Penangung Jawab</td>
    </tr>
    <tr>
        <td></td>
        <td width="10px" style="text-align: center">:</td>
        <td colspan="" width="50px">Nama</td>
        <td colspan="">:</td>
        <td rowspan="2"></td>
        <td colspan="" width="50px">Nama</td>
        <td colspan="">:</td>
        <td rowspan="2"></td>
    </tr>
    <tr>
        <td></td>
        <td width="10px" style="text-align: center"></td>
        <td colspan="">Bagian</td>
        <td colspan="">:</td>
        <td colspan="">Bagian</td>
        <td colspan="">:</td>
    </tr>
    <tr>
        <td colspan="8" height="10px"></td>
    </tr>
</table>
