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
                        <td>{{ $row->nama }}</td>
                        <td>{{ number_format($row->jumlah) }}</td>
                        <td>{{ number_format($row->kg, 2) }} Kg</td>
                    </tr>
                    @php
                        $ekor = $ekor+$row->jumlah;
                        $berat = $berat+$row->kg;
                    @endphp
                    @endforeach
                    <tr>
                        <td></td>
                        <td>Total</td>
                        <td>{{$ekor}}</td>
                        <td>{{ number_format($berat, 2) }} Kg</td>
                    </tr>
                </tbody>
            </table>
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
                    @foreach ($produksi as $i => $row)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $row->nama }}</td>
                        <td>{{ number_format($row->jumlah) }}</td>
                        <td>{{ number_format($row->kg, 2) }} Kg</td>
                    </tr>
                    @php
                        $ekor = $ekor+$row->jumlah;
                        $berat = $berat+$row->kg;
                    @endphp
                    @endforeach
                    <tr>
                        <td></td>
                        <td>Total</td>
                        <td>{{$ekor}}</td>
                        <td>{{ number_format($berat, 2) }} Kg</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
