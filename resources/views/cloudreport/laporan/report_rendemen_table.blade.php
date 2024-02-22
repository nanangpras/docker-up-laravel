<section class="panel">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table default-table" id="export-table">
                <thead>
                    
                    <tr class="text-center">
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Rendemen</th>
                        <th>Rendemen Tangkap</th>
                        <th>Rendemen Kirim</th>
                        <th>Berat RPA</th>
                        <th>Berat Grading</th>
                        <th>Berat Evis</th>
                        <th>Berat Darah Bulu</th>
                        <th>Ekor RPA</th>
                        <th>Ekor Grading</th>
                        <th>Selisih Ekor</th>
                        <th>Jumlah Supplier</th>
                        <th>Jumlah PO Mobil</th>
                        <th>Selesai Potong Mobil</th>
                        <th>Ekor DO</th>
                        <th>Berat DO</th>
                        <th>Ekor Seckle</th>
                        <th>Berat Terima</th>
                        <th>Rerata Terima</th>
                        <th>Susut Tangkap</th>
                        <th>Susut Kirim</th>
                        <th>Susut Sekle</th>
                        <th>Ekoran Grading</th>
                        <th>Selisih Grading-Seckle</th>
                        <th>Rerata Grading</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($rendemen as $i => $val)
                       
                        <tr>
                            <td>{{ ++$i }}</td>
                            <td>{{ date('d/m/y',strtotime($val->tanggal)) }}</td>
                            <td>{{ $val->rendemen_total }}%</td>
                            <td>{{ $val->rendemen_tangkap }}%</td>
                            <td>{{ $val->rendemen_kirim  }}%</td>
                            <td>{{ number_format($val->berat_rpa) }}Kg</td>
                            <td>{{ number_format($val->berat_grading) }}Kg</td>
                            <td>{{ number_format($val->berat_evis) }}Kg</td>
                            <td>{{ number_format($val->darah_bulu) }}Kg</td>
                            <td>{{ number_format($val->ekor_rpa) }}</td>
                            <td>{{ number_format($val->ekor_grading) }}</td>
                            <td>{{ $val->selisih_ekor }}</td>
                            <td>{{ $val->jumlah_supplier }}</td>
                            <td>{{ $val->jumlah_po_mobil }}</td>
                            <td>{{ $val->selesai_potong }}</td>
                            <td>{{ number_format($val->ekor_do) }}</td>
                            <td>{{ number_format($val->berat_do) }}</td>
                            <td>{{ number_format($val->ekoran_seckle) }}</td>
                            <td>{{ number_format($val->kg_terima) }}Kg</td>
                            <td>{{ $val->rerata_terima_lb }}</td>
                            <td>{{ $val->susut_tangkap }}%</td>
                            <td>{{ $val->susut_kirim }}%</td>
                            <td>{{ $val->susut_sekcel }}</td>
                            <td>{{ number_format($val->ekoran_grading) }}</td>
                            <td>{{ $val->selisih_seckel_grading }}</td>
                            <td>{{ $val->rerata_grading }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="9" class="text-center"><b>Total</b></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</section>