<table class="table default-table">
    <thead>
        <tr>
            <th colspan="10">Report Arus Grading - Produksi</th>
        </tr>
        <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">Uk. Karkas</th>
            <th class="text-center" colspan="2">{{ Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}</th>
            <th class="text-center" colspan="2">{{ Carbon\Carbon::parse($tanggal)->addDays(-1)->format('d/m/Y') }}</th>
            <th class="text-center" colspan="2">{{ Carbon\Carbon::parse($tanggal)->addDays(-2)->format('d/m/Y') }}</th>
            <th class="text-center" colspan="2">{{ Carbon\Carbon::parse($tanggal)->addDays(-3)->format('d/m/Y') }}</th>
        </tr>
        <tr>
            <th class="text-center">Ekor/Pcs/Pack</th>
            <th class="text-center">Kg</th>
            <th class="text-center">Ekor/Pcs/Pack</th>
            <th class="text-center">Kg</th>
            <th class="text-center">Ekor/Pcs/Pack</th>
            <th class="text-center">Kg</th>
            <th class="text-center">Ekor/Pcs/Pack</th>
            <th class="text-center">Kg</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($chiller as $i => $row)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $row->item_name }}</td>
            <td class="text-right">
               {!! App\Models\Chiller::hitung_chiller('qty', 'gradinggabungan', $row->item_id, Carbon\Carbon::parse($tanggal)) !!}
            </td>
            <td class="text-right">
               {!! App\Models\Chiller::hitung_chiller('berat', 'gradinggabungan', $row->item_id, Carbon\Carbon::parse($tanggal)) !!}
            </td>
            <td class="text-right">
               {!! App\Models\Chiller::hitung_chiller('qty', 'gradinggabungan', $row->item_id, Carbon\Carbon::parse($tanggal)->addDays(-1)) !!}
            </td>
            <td class="text-right">
               {!! App\Models\Chiller::hitung_chiller('berat', 'gradinggabungan', $row->item_id, Carbon\Carbon::parse($tanggal)->addDays(-1)) !!}
            </td>
            <td class="text-right">
               {!! App\Models\Chiller::hitung_chiller('qty', 'gradinggabungan', $row->item_id, Carbon\Carbon::parse($tanggal)->addDays(-2)) !!}
            </td>
            <td class="text-right">
               {!! App\Models\Chiller::hitung_chiller('berat', 'gradinggabungan', $row->item_id, Carbon\Carbon::parse($tanggal)->addDays(-2)) !!}
            </td>
            <td class="text-right">
               {!! App\Models\Chiller::hitung_chiller('qty', 'gradinggabungan', $row->item_id, Carbon\Carbon::parse($tanggal)->addDays(-3)) !!}
            </td>
            <td class="text-right">
               {!! App\Models\Chiller::hitung_chiller('berat', 'gradinggabungan', $row->item_id, Carbon\Carbon::parse($tanggal)->addDays(-3)) !!}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<table class="table default-table mt-4">
    <thead>
        <tr>
            <th colspan="10">Report Arus Thawing - Produksi</th>
        </tr>
        <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">Uk. Karkas</th>
            <th class="text-center" colspan="2">{{ Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}</th>
            <th class="text-center" colspan="2">{{ Carbon\Carbon::parse($tanggal)->addDays(-1)->format('d/m/Y') }}</th>
            <th class="text-center" colspan="2">{{ Carbon\Carbon::parse($tanggal)->addDays(-2)->format('d/m/Y') }}</th>
            <th class="text-center" colspan="2">{{ Carbon\Carbon::parse($tanggal)->addDays(-3)->format('d/m/Y') }}</th>
        </tr>
        <tr>
            <th class="text-center">Ekor/Pcs/Pack</th>
            <th class="text-center">Kg</th>
            <th class="text-center">Ekor/Pcs/Pack</th>
            <th class="text-center">Kg</th>
            <th class="text-center">Ekor/Pcs/Pack</th>
            <th class="text-center">Kg</th>
            <th class="text-center">Ekor/Pcs/Pack</th>
            <th class="text-center">Kg</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($thawing as $i => $row)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $row->item_name }}</td>
            <td class="text-right">
               {!! App\Models\Chiller::hitung_chiller('qty', 'thawing', $row->item_id, Carbon\Carbon::parse($tanggal)) !!}
            </td>
            <td class="text-right">
               {!! App\Models\Chiller::hitung_chiller('berat', 'thawing', $row->item_id, Carbon\Carbon::parse($tanggal)) !!}
            </td>
            <td class="text-right">
               {!! App\Models\Chiller::hitung_chiller('qty', 'thawing', $row->item_id, Carbon\Carbon::parse($tanggal)->addDays(-1)) !!}
            </td>
            <td class="text-right">
               {!! App\Models\Chiller::hitung_chiller('berat', 'thawing', $row->item_id, Carbon\Carbon::parse($tanggal)->addDays(-1)) !!}
            </td>
            <td class="text-right">
               {!! App\Models\Chiller::hitung_chiller('qty', 'thawing', $row->item_id, Carbon\Carbon::parse($tanggal)->addDays(-2)) !!}
            </td>
            <td class="text-right">
               {!! App\Models\Chiller::hitung_chiller('berat', 'thawing', $row->item_id, Carbon\Carbon::parse($tanggal)->addDays(-2)) !!}
            </td>
            <td class="text-right">
               {!! App\Models\Chiller::hitung_chiller('qty', 'thawing', $row->item_id, Carbon\Carbon::parse($tanggal)->addDays(-3)) !!}
            </td>
            <td class="text-right">
               {!! App\Models\Chiller::hitung_chiller('berat', 'thawing', $row->item_id, Carbon\Carbon::parse($tanggal)->addDays(-3)) !!}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
