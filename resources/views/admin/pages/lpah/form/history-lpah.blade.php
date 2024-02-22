@php
$jsondata = App\Models\Adminedit::where('table_id', $id)
    ->where('table_name', 'productions')
    ->where('type', 'edit')
    ->get();
$json = [];
$dataedit = [];
$lists = [];
@endphp
@foreach ($jsondata as $key => $row)
    <table class="table default-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Waktu Edit </th>
                <th>Riwayat</th>
                <th>Ekor Seckle</th>
                <th>Berat DO</th>
                <th>Ekoran DO</th>
                <th>Keranjang</th>
                <th>Ekor Mati</th>
                <th>Berat Mati</th>
                <th>Ekor Ayam Merah</th>
                <th>Berat Ayam Merah</th>
                <th>Nama Petugas</th>
            </tr>
        </thead>
        <tbody>
            @php
                $json[] = json_decode($row->data, true);
                $dataedit[] = $row->content;
            @endphp

            @if (isset($json[$key]['header']))
                <tr>
                    <td>{{ $key + 1 }}</td>
                    @if ($key == 0)
                        <td>{{ date('d-m-Y H:i:s', strtotime($json[$key]['header']['created_at'])) }}</td>
                    @else
                        <td>{{ $row->created_at }}</td>
                    @endif
                    <td @if ($row->content == 'Penghapusan Item') style="background-color: #fde0dd" @endif>{{ $row->content }}
                    </td>
                    <td @if ($json[$key - 1]['header']['ekoran_seckle'] ?? (false && $json[$key]['header']['ekoran_seckle'] ?? false)) @if ($json[$key]['header']['ekoran_seckle'] != $json[$key - 1]['header']['ekoran_seckle'])
                                        style="background-color: #fde0dd" @endif
                        @endif>{{ $json[$key]['header']['ekoran_seckle'] }}
                    </td>
                    <td @if ($json[$key - 1]['header']['sc_berat_do'] ?? (false && $json[$key]['header']['sc_berat_do'] ?? false)) @if ($json[$key]['header']['sc_berat_do'] != $json[$key - 1]['header']['sc_berat_do'])
                                        style="background-color: #fde0dd" @endif
                        @endif>{{ $json[$key]['header']['sc_berat_do'] }}
                    </td>
                    <td @if ($json[$key - 1]['header']['sc_ekor_do'] ?? (false && $json[$key]['header']['sc_ekor_do'] ?? false)) @if ($json[$key]['header']['sc_ekor_do'] != $json[$key - 1]['header']['sc_ekor_do'])
                                        style="background-color: #fde0dd" @endif
                        @endif>{{ $json[$key]['header']['sc_ekor_do'] }}
                    </td>
                    <td @if ($json[$key - 1]['header']['lpah_jumlah_keranjang'] ?? (false && $json[$key]['header']['lpah_jumlah_keranjang'] ?? false)) @if ($json[$key]['header']['lpah_jumlah_keranjang'] != $json[$key - 1]['header']['lpah_jumlah_keranjang'])
                                        style="background-color: #fde0dd" @endif
                        @endif
                        >{{ $json[$key]['header']['lpah_jumlah_keranjang'] }}</td>

                    <td @if ($json[$key - 1]['header']['qc_ekor_ayam_mati'] ?? (false && $json[$key]['header']['qc_ekor_ayam_mati'] ?? false)) @if ($json[$key]['header']['qc_ekor_ayam_mati'] != $json[$key - 1]['header']['qc_ekor_ayam_mati'])
                                        style="background-color: #fde0dd" @endif
                        @endif
                        >{{ $json[$key]['header']['qc_ekor_ayam_mati'] }} </td>

                    <td @if ($json[$key - 1]['header']['qc_berat_ayam_mati'] ?? (false && $json[$key]['header']['qc_berat_ayam_mati'] ?? false)) @if ($json[$key]['header']['qc_berat_ayam_mati'] != $json[$key - 1]['header']['qc_berat_ayam_mati'])
                                        style="background-color: #fde0dd" @endif
                        @endif
                        >{{ $json[$key]['header']['qc_berat_ayam_mati'] }}</td>

                    <td @if ($json[$key - 1]['header']['qc_ekor_ayam_merah'] ?? (false && $json[$key]['header']['qc_ekor_ayam_merah'] ?? false)) @if ($json[$key]['header']['qc_ekor_ayam_merah'] != $json[$key - 1]['header']['qc_ekor_ayam_merah'])
                                        style="background-color: #fde0dd" @endif
                        @endif
                        >{{ $json[$key]['header']['qc_ekor_ayam_merah'] }}</td>


                    <td @if ($json[$key - 1]['header']['qc_berat_ayam_merah'] ?? (false && $json[$key]['header']['qc_berat_ayam_merah'] ?? false)) @if ($json[$key]['header']['qc_berat_ayam_merah'] != $json[$key - 1]['header']['qc_berat_ayam_merah'])
                                        style="background-color: #fde0dd" @endif
                        @endif
                        >{{ $json[$key]['header']['qc_berat_ayam_merah'] }}</td>
                    <td>{{ $json[$key]['header']['lpah_user_nama'] }}</td>




                </tr>
            @endif
        </tbody>

    </table>
@endforeach
