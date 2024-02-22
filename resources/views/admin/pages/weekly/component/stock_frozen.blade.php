<div class="table-responsive">
    <div  id="table-bb-frozen">

<table class="table default-table table-sm" width="100%">
     <style>
        .text{
            mso-number-format:"\@";
            border:thin solid black;
        }
    </style>
    <thead>
        <tr class="text-center">
            <th class="text" rowspan="3">Tanggal</th>
            {{-- <th class="text" colspan="6">Produksi Frozen</th> --}}
            <th class="text" colspan="{{(count($item)*2)}}">(MEYER PROTEINDO PRAKARSA & CITRAGUNA LESTARI)</th>
        </tr>
        <tr class="text-center">
            <th class="text" colspan="2">Ayam Fresh</th>
            <th class="text" colspan="2">Ayam Lama</th>
            <th class="text" colspan="2">Total</th>
            @foreach ($item as $it)
                <th class="text" colspan="3">{{ $it->nama }}</th>
            @endforeach
        </tr>
        <tr class="text-center">
            <th class="text">Ekor/Pcs/Pack</th>
            <th class="text">Kg</th>
            <th class="text">Ekor/Pcs/Pack</th>
            <th class="text">Kg</th>
            <th class="text">Ekor/Pcs/Pack</th>
            <th class="text">Kg</th>
            @foreach ($item as $it)
                <th class="text" colspan="">Ekor/Pcs/Pack</th>
                <th class="text" colspan="">Kg</th>
                <th class="text"> Customer</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @php
            $qtybaru = 0;
            $beratbaru = 0;
            $qtylama = 0;
            $beratlama = 0;
            $totalqty = 0;
            $totalberat = 0;
        @endphp

    @foreach($range_tanggal as $i => $t)
    @php
        $tanggal_kosong = true;
    @endphp
        @foreach ($data as $val)
            @if($t==$val->tanggal)
            @php
                $tanggal_kosong = false;
            @endphp
            @php
                $qtybaru += $val->qtybaru;
                $beratbaru += $val->beratbaru;
                $qtylama += $val->qtylama;
                $beratlama += $val->beratlama;
                $totalqty += $val->totalqty;
                $totalberat += $val->totalberat;
            @endphp
            <tr>

                <td class="text" >{{ date('d/m/Y', strtotime($val->tanggal)) }}</td>
                <td class="text" >{{ number_format($val->qtybaru) }}</td>
                <td class="text" >{{ number_format($val->beratbaru,1) }}</td>
                <td class="text" >{{ number_format($val->qtylama) }}</td>
                <td class="text" >{{ number_format($val->beratlama,1) }}</td>
                <td class="text" >{{ number_format($val->totalqty) }}</td>
                <td class="text" >{{ number_format($val->totalberat,1) }}</td>
                @php
                    $select_urut = 0;
                    $table_table = "";
                @endphp
                @foreach ($item as $urut => $tem)
                    @php
                        $table_table = "";
                    @endphp
                    @foreach ($detail as $urut_detail => $det)
                        @if(($val->tanggal == $det->tanggal) && ($tem->nama == $det->nama))
                            @php
                                $exp    =   json_decode($det->label) ;
                                $select_urut = $urut;
                                $table_table = "<td class='text' >".number_format($det->qty)."</td><td class='text' >".number_format($det->berat,1)."</td>"."</td><td class='text' >".$exp->sub_item."</td>";
                            @endphp
                        @endif
                    @endforeach

                    @if($table_table=="")
                        <td class='text' >0</td>
                        <td class='text' >0</td>
                        <td class='text' >0</td>
                    @else
                        {!!$table_table!!}
                    @endif
                @endforeach
            </tr>

            @endif
        @endforeach

        @if($tanggal_kosong==true)

            <tr>

                <td class="text" >{{ date('d/m/Y', strtotime($t)) }}</td>
                <td class="text" >0</td>
                <td class="text" >0</td>
                <td class="text" >0</td>
                <td class="text" >0</td>
                <td class="text" >0</td>
                <td class="text" >0</td>
                @php
                    $select_urut = 0;
                    $table_table = "";
                @endphp
                @foreach ($item as $urut => $tem)
                    @php
                        $table_table = "";
                    @endphp
                    @foreach ($detail as $urut_detail => $det)
                        @if(($t == $det->tanggal) && ($tem->nama == $det->nama))
                            @php
                                $select_urut = $urut;
                                $table_table = "<td class='text' >0</td><td class='text' >0</td>";
                            @endphp
                        @endif
                    @endforeach

                    @if($table_table=="")
                        <td class='text'>0</td>
                        <td class='text'>0</td>
                    @else
                        {!!$table_table!!}
                    @endif
                @endforeach
            </tr>
        @endif
    @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th class="text" colspan="">TOTAL</th>
            <th class="text">{{ number_format($qtybaru) }}</th>
            <th class="text">{{ number_format($beratbaru,1) }}</th>
            <th class="text">{{ number_format($qtylama) }}</th>
            <th class="text">{{ number_format($beratlama,1) }}</th>
            <th class="text">{{ number_format($totalqty) }}</th>
            <th class="text">{{ number_format($totalberat,1) }}</th>
        </tr>
    </tfoot>
</table>

</div>

<form method="post" action="{{route('weekly.export')}}">
    @csrf
    <input name="filename" type="hidden" value="export-weekly-bb-frozen.xls">
    <textarea name="html" style="display: none" id="html-bb-frozen"></textarea>
    <button type="submit" id="export-bb-frozen" class="btn btn-blue">Export</button>
</form>

<script>
    $(document).ready(function(){
        var html  = $('#table-bb-frozen').html();
        $('#html-bb-frozen').val(html);
    })
</script>
