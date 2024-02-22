<div class="table-responsive">
    <div id="table-lama">
        <table class="default-table table" border="1" width="100%">
            <style>
                .text {
                    mso-number-format: "\@";
                    border: thin solid black;
                }
                .right{
                    text-align: end;
                }
                .center{
                    text-align: center;
                }
                .table thead th {
                    vertical-align: middle;
                    border-bottom: 2px solid #dee2e6;
                }
            </style>
            <thead>
                <tr>
                    <th class="text center" rowspan="3" colspan="1" width="10%">Tanggal</th>
                    <th class="text center" colspan="6" >Ayam Lama</th>
                </tr>
                <tr>
                    {{-- <th class="text center" colspan="2" > Hari Yang Sama</th> --}}
                    <th class="text center" colspan="2" > Kemarin</th>
                    <th class="text center" colspan="2" > = 2 Hari</th>
                    <th class="text center" colspan="2" > > 2 Hari </th>
                </tr>
                <tr>
                    {{-- <th class="text center" >Ekor</th>
                    <th class="text center" >Berat</th> --}}
                    <th class="text center" >Ekor</th>
                    <th class="text center" >Berat</th>
                    <th class="text center" >Ekor</th>
                    <th class="text center" >Berat</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($range_tanggal as $i => $val)
                    <tr>
                        <td class="center" > {{ date('Y-m-d',strtotime($val)) }}</td>
                        @php
                            $tanggal_1 = \App\Models\FreestockList::hitung_bb_ayam_lama($val, date('Y-m-d',strtotime('-1 days', strtotime($val))));
                            $tanggal_2 = \App\Models\FreestockList::hitung_bb_ayam_lama($val, date('Y-m-d',strtotime('-2 days', strtotime($val))));
                            $tanggal_3 = \App\Models\FreestockList::hitung_bb_ayam_lama_lebih2hari($val, 'lebihlama');
                            // $tanggal_3 = \App\Models\FreestockList::hitung_bb_ayam_lama($val, date('Y-m-d',strtotime('-3 days', strtotime($val))));
                        @endphp
                        <td class="center">
                                @if ($tanggal_1[0] ?? FALSE)
                                {{ number_format($tanggal_1[0]->lama_qty, 1) ?? "0"}}
                                @else
                                0
                                @endif


                        </td>
                        <td class="center">
                                @if ($tanggal_1[0] ?? FALSE)
                                {{ number_format($tanggal_1[0]->lama_berat, 1) ?? "0"}}
                                @else
                                0
                                @endif


                        </td>
                        <td class="center">
                            @if ($tanggal_2[0] ?? FALSE)
                                {{ number_format($tanggal_2[0]->lama_qty, 1) ?? "0"}}
                                @else
                                0
                            @endif
                        </td>
                        <td class="center">
                            @if ($tanggal_2[0] ?? FALSE)
                                {{ number_format($tanggal_2[0]->lama_berat, 1) ?? "0"}}
                                @else
                                0
                            @endif
                        </td>
                        <td class="center">
                            @if ($tanggal_3[0] ?? FALSE)
                                {{ number_format($tanggal_3[0]->lama_qty, 1) ?? "0"}}
                                @else
                                0
                            @endif
                        </td>
                        <td class="center">
                            @if ($tanggal_3[0] ?? FALSE)
                                {{ number_format($tanggal_3[0]->lama_berat, 1) ?? "0"}}
                                @else
                                0
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<form method="post" action="{{route('weekly.export')}}">
    @csrf
    <input name="filename" type="hidden" value="export-weekly-ayam-lama.xls">
    <textarea name="html" style="display: none" id="html-ayam-lama"></textarea>
    <button type="submit" id="export-ayam-lama" class="btn btn-blue">Export</button>
</form>

<script>
    $(document).ready(function(){
        var html  = $('#table-lama').html();
        $('#html-ayam-lama').val(html);
    })
</script>
