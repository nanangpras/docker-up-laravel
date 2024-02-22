<div class="table-responsive" >
    <table class="table table-sm default-table text-center" id="table-data">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                {{-- <th>Pcs/Karton</th> --}}
                <th>Berat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $i => $row)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $row->nama }}</td>
                    
                    {{-- <td>{{ $row->stock ?? '0'}}</td> --}}
                    <td>{{ $row->berat ?? '0'}} Kg</td>
                    <td>
                        <button class="btn btn-primary" data-toggle="collapse" data-target="#collapse{{ $row->id }}" aria-expanded="true"
                            aria-controls="collapse{{ $row->id }}">Expand Record
                        </button>
                        <button class="btn btn-success" id="tambahBtnBumbu" 
                                data-toggle="modal" 
                                data-target="#tambahBumbu" 
                                data-title="Tambah Bumbu"
                                data-nama="{{ $row->nama }}"
                                data-id ="{{ $row->id }}"
                                data-remote="{{route('bumbu.edit',$row->id)}}">
                        <i class="fa fa-plus-circle"></i> Tambah Data </button>
                        
                    </td>
                </tr>
                <td colspan="5">
                    <div id="collapse{{ $row->id }}" class="collapse" aria-labelledby="headingOne" data-parent="#accordionListPO">
                        <div class="p-1">
                                <div class="row">
                                    <div class="col">
                                        <table class="table table-sm text-center">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Tujuan</th>
                                                    <th>Status</th>
                                                    <th>Berat (Kg)</th>
                                                    <th>Customer</th>
                                                    <th>Tanggal</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            @if ($searchBumbu == true)
                                            <tbody>
                                                @php
                                                    $getDataDetail     = App\Models\BumbuDetail::where('bumbu_id', $row->id)
                                                                        ->where(function($query) use ($status) {
                                                                            if ($status != 'semua') {
                                                                                $query->where('status', $status);
                                                                            }
                                                                        })
                                                                        ->whereBetween('tanggal', [$tgl_awal, $tgl_akhir])->get();
                                                @endphp
                                                @foreach ($getDataDetail as $item)
                                                    <tr 
                                                        @if ($item->status =='masuk')
                                                            class="table-primary"
                                                        @else
                                                            class="table-danger"
                                                        @endif
                                                    >
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{$item->regu ?? 'Gudang'}}</td>
                                                    <td>{{$item->status}}</td>
                                                    <td>{{$item->berat}} Kg</td>
                                                    <td>
                                                        @if ($item->customer_bumbu)
                                                            {{$item->customer_bumbu->customers->nama}}
                                                        @else
                                                            -
                                                        @endif    
                                                    </td>
                                                    <td>{{$item->tanggal}}</td>
                                                    <td>
                                                        @if(auth()->user()->account_role == 'superadmin')
                                                            @if($item->regu !== "marinasi")
                                                                <button class="btn btn-outline-success btn-sm" data-toggle="modal" 
                                                                        data-title="Edit Bumbu Record" 
                                                                        data-target="#bumbuRecord" 
                                                                        data-remote="{{route('bumbu.edit',$item->id)}}" 
                                                                        data-id="{{$item->id}}" 
                                                                        id="btnEditRecord"> 
                                                                <i class="fa fa-pencil"></i> Edit</button>
                                                                <button class="btn btn-outline-danger btn-sm" data-toggle="modal"
                                                                        data-title="Delete Bumbu Record"  
                                                                        data-target="#bumbuRecord" 
                                                                        data-remote="{{route('bumbu.show',$item->id)}}" 
                                                                        data-id="{{$item->id}}" 
                                                                        id="btnDeleteRecord"> 
                                                                <i class="fa fa-times"></i> Hapus</button>
                                                            @endif
                                                        @endif
                                                    </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            @else
                                            <tbody>
                                                @foreach ($row->bumbu_detail as $item)
                                                    <tr 
                                                        @if ($item->status =='masuk')
                                                            class="table-primary"
                                                        @else
                                                            class="table-danger"
                                                        @endif
                                                    >
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{$item->regu ?? 'Gudang'}}</td>
                                                    <td>{{$item->status}}</td>
                                                    <td>{{$item->berat}} Kg</td>
                                                    <td>
                                                        @if ($item->customer_bumbu)
                                                            {{$item->customer_bumbu->customers->nama}}
                                                        @else
                                                            -
                                                        @endif    
                                                    </td>
                                                    <td>{{$item->tanggal}}</td>
                                                    <td>
                                                        @if(auth()->user()->account_role == 'superadmin')
                                                            @if($item->regu !== "marinasi")
                                                                <button class="btn btn-outline-success btn-sm" data-toggle="modal" 
                                                                        data-title="Edit Bumbu Record" 
                                                                        data-target="#bumbuRecord" 
                                                                        data-remote="{{route('bumbu.edit',$item->id)}}" 
                                                                        data-id="{{$item->id}}" 
                                                                        id="btnEditRecord"> 
                                                                <i class="fa fa-pencil"></i> Edit</button>
                                                                <button class="btn btn-outline-danger btn-sm" data-toggle="modal"
                                                                        data-title="Delete Bumbu Record"  
                                                                        data-target="#bumbuRecord" 
                                                                        data-remote="{{route('bumbu.show',$item->id)}}" 
                                                                        data-id="{{$item->id}}" 
                                                                        id="btnDeleteRecord"> 
                                                                <i class="fa fa-times"></i> Hapus</button>
                                                            @endif
                                                        @endif
                                                    </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>

                                            @endif
                                        </table>

                                    </div>
                                </div>
                            {{-- </div> --}}
                        </div>
                    </div>
                </td>
            @endforeach
        </tbody>
    </table>
</div>
<script>

    // tambah bumbu

    $(".table tbody").on('click','#tambahBtnBumbu', function () {
        let id = $(this).attr('data-id');
        let nama = $(this).attr('data-nama');

        console.log(id, nama)

        $('#tambahBumbu').toggleClass('is-active');
        $(".title-bumbu").text(nama);
        $("#bumbu_id").val(id);

        $('#customer_bumbu_id option:first').prop('selected', true);
        $('#customer_bumbu_id option').hide();

        $('#customer_bumbu_id option').each(function () {
            var selectedBumbuId = $(this).data('bumbu');
            var statusBumbu = $(this).attr('data-statusBumbu');
            if (id == selectedBumbuId && statusBumbu == "active") {
                $(this).show();
                $(this).prop('selected', true)
            }
        });
    });

    $("#btnCancel").on('click', function(){
        $("#tmbh_bumbu").trigger("reset");
    })

    // button edit
    $(".table tbody").on("click","#btnEditRecord", function () {
        let href = $(this).attr('data-remote');
        let title = $(this).attr('data-title');
        $.ajax({
            type: "GET",
            url : href,
            data : {
                'key' : 'edit_gudang'
            },
            success: function (response) {
                $("#content-bumbu").html(response);
                $("#title-modal").text(title);
            }
        });
    });

    // button delete
    $(".table tbody").on("click","#btnDeleteRecord", function () {
        let href = $(this).attr('data-remote');
        let title = $(this).attr('data-title');
        $.ajax({
            type: "GET",
            url : href,
            data : {
                'key' : 'bumbu_gudang'
            },
            success: function (response) {
                $("#content-bumbu").html(response);
                $("#title-modal").text(title);
            }
        });
    });

    $('#status').on('change', function () {
        var selectedStatus = $(this).val();
        if (selectedStatus == 'keluar') {
            $('#regu-keluar').show();
            $('#customer').show();
        } else {
            $('#regu-keluar').hide();
            $('#customer').hide();
        }
    });
    
    $('.select2').select2({
        theme: 'bootstrap4'
    })

</script>