@extends('admin.layout.template')

@section('title', 'Data Yield Benchmark')

@section('content')
    <div class="row mb-4">
        <div class="col">
            <a href="{{ route('dashboard') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
                Back</a>

        </div>
        <div class="col text-center">
            <b>Data Yield Benchmark</b>
        </div>
        <div class="col"></div>
    </div>
    
    <section class="panel">
        <div class="card-body">
            {{-- <button type="button" class="btn btn-outline-success btn-sm mb-1" data-toggle="modal" data-target="#exampleModal">Tambah Ukuran Yield Benchmark</button> --}}
            </h5>
            <div class="table-responsive">
                <table class="table table-sm default-table dataTable">
                    <thead>
                        <tr>
                            <th>Ukuran</th>
                            <th>Jenis Ayam</th>
                            <th>Yield Benchmark</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        @foreach ($data as $i => $row)
                        @php
                        if ($row['ukuran_ayam'] == '&lt; 1.1') {
                            $ukuranAlias = '11';
                        } 
                        else if ($row['ukuran_ayam'] == '1.1 - 1.3') {
                                $ukuranAlias = '1113';
                        }
                        else if ($row['ukuran_ayam'] == '1.2 - 1.4') {
                                $ukuranAlias = '1214';
                        }
                        else if ($row['ukuran_ayam'] == '1.2 - 1.5') {
                                $ukuranAlias = '1215';
                        }
                        else if ($row['ukuran_ayam'] == '1.3 - 1.5') {
                                $ukuranAlias = '1315';
                        }
                        else if ($row['ukuran_ayam'] == '1.3 - 1.6') {
                                $ukuranAlias = '1316';
                        }
                        else if ($row['ukuran_ayam'] == '1.4 - 1.6') {
                                $ukuranAlias = '1416';
                        }
                        else if ($row['ukuran_ayam'] == '1.4 - 1.7') {
                                $ukuranAlias = '1417';
                        }
                        else if ($row['ukuran_ayam'] == '1.5 - 1.7') {
                                $ukuranAlias = '1517';
                        }
                        else if ($row['ukuran_ayam'] == '1.5 - 1.8') {
                                $ukuranAlias = '1518';
                        }
                        else if ($row['ukuran_ayam'] == '1.6 - 1.8') {
                                $ukuranAlias = '1618';
                        }
                        else if ($row['ukuran_ayam'] == '1.7 - 1.9') {
                                $ukuranAlias = '1719';
                        }
                        else if ($row['ukuran_ayam'] == '1.8 - 2.0') {
                                $ukuranAlias = '1820';
                        }
                        else if ($row['ukuran_ayam'] == '1.9 - 2.1') {
                                $ukuranAlias = '1921';
                        }
                        else if ($row['ukuran_ayam'] == '2.0 - 2.2') {
                                $ukuranAlias = '2022';
                        }
                        else if ($row['ukuran_ayam'] == '2.2 up') {
                                $ukuranAlias = '22';
                        }


                        // else if ($row['ukuran_ayam'] == '2.0-2.5') {
                        //         $ukuranAlias = '2025';
                        // }

                        else if ($row['ukuran_ayam'] == '2.0 - 2.5' || $row['ukuran_ayam'] == '2.0-2.5') {
                                $ukuranAlias = '2025';
                        }
                        else if ($row['ukuran_ayam'] == '2.5-3.0' || $row['ukuran_ayam'] == '2.5 - 3.0') {
                                $ukuranAlias = '2530';
                        }
                        // else if ($row['ukuran_ayam'] == '2.5 - 3.0') {
                        //         $ukuranAlias = '2530';
                        // }
                        
                        else if ($row['ukuran_ayam'] == '3.0 up') {
                                $ukuranAlias = '30';
                        }
                        else if ($row['ukuran_ayam'] == '4.0 up') {
                                $ukuranAlias = '40';
                        }

                        @endphp
                            <tr>

                                <td>{{ $row['ukuran_ayam'] == '&lt; 1.1' ? '<1.1' : $row['ukuran_ayam'] }}</td>
                                <td>{{ $row['jenis_ayam'] }}</td>
                                
                                @php
                                    $getDataYield = App\Models\Adminedit::where('activity', 'input_yield')->where('content', $row['jenis_ayam'])->where('type', $row['ukuran_ayam'])->first();
                                    if ($getDataYield) {
                                        $decodeYield = json_decode($getDataYield->data);
                                    }

                                @endphp

                                @if ($getDataYield)
                                <td>Yield Karkas: {{ $decodeYield->yield_karkas }}<br>
                                    Yield Evis: {{ $decodeYield->yield_evis }}</td>
                                @else
                                    
                                <td>Yield Karkas: {{ $row->yield_karkas ?? '-' }}<br>
                                    Yield Evis: {{ $row->yield_evis ?? '-' }}</td>
                                @endif
                                <td>
                                    <button class="btn btn-outline-warning rounded-0 btn-block" id="btnItem-{{ $loop->iteration }}-{{ $ukuranAlias }}" data-toggle="modal"
                                        data-target="#editDataYield" data-jenis="{{ $row['jenis_ayam'] }}" onclick="loadItem({{ $loop->iteration }},{{ $ukuranAlias }})">Edit</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="modal fade" id="editDataYield" aria-hidden="true">
                <div class="modal-dialog" style="width: 800px;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Edit Item</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div id="content_edit_item"></div>
                    </div>
                </div>
            </div>
            <script>
            
                function loadItem(noUrut, ukuranAyam) {
                    console.log(noUrut, ukuranAyam)
                    console.log($("#btnItem-"+noUrut+'-'+ukuranAyam).attr('data-jenis'));
                    $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    }); 
                    $.ajax({
                        url: "{{ route('item.index') }}",
                        method: "GET",
                        data: {
                            key: 'editYield',
                            jenisAyam : $("#btnItem-"+noUrut+'-'+ukuranAyam).attr('data-jenis'),
                            ukuranAyam: encodeURIComponent(ukuranAyam),
                        },
                        success: function(data) {
                            // console.log(data.data);
                            $("#content_edit_item").html(data);
                        }
                    });
                }
            </script>
            

            {{-- {{ $data->links() }} --}}

        </div>
    </section>


{{-- MODAL TAMBAH  --}}
{{-- <div class="modal fade" id="exampleModal" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Ukuran Yield Benchmark</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
                <input type="hidden" name="key" id="key" value="itemname">
                <div class="modal-body">
                    <div class="form-group">
                        Item Name
                        <input type="text" id="itemname" name="itemname" placeholder="Tuliskan Item Name" class="form-control" autocomplete="off" required>
                    </div>
                    <section class="panel">
                        <div class="card-body">
                            <div id="tableListItemName">

                            </div>
                        </div>
                    </section>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary submitItemName">Submit</button>
                </div>
        </div>
    </div>
</div> --}}
{{-- END ITEM NAME --}}
@stop

@section('header')
    <link rel="stylesheet" type="text/css" href="{{ asset('') }}plugin/DataTables/datatables.min.css" />
@stop

