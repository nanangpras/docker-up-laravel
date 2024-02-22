@extends('admin.layout.template')

@section('title', 'Data Bumbu ')

@section('content')
    <div class="alert alert-success" id="alert" role="alert" style="display:none;">
    </div>
    <div class="alert alert-danger" id="alert-fail" role="alert" style="display:none;">
    </div>
    <div class="row mb-4">
        <div class="col">
            <a href="{{ route('dashboard') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
                Back</a>

        </div>
        <div class="col text-center">
            <b>Data Bumbu </b>
        </div>
        <div class="col"></div>
    </div>

    <section class="panel">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3 mt-4">
                    <button class="btn btn-outline-success btn-sm" data-toggle="modal" data-target="#tambahBumbu"> <i class="fa fa-plus-circle"></i> Tambah Bumbu</button>
                    {{-- <button class="btn btn-primary" id="download-item"> <i class="fa fa-download"></i> <i class="fa fa-spinner fa-spin spinerloading" style="display:none;"></i> <span id="text">Export Excel</span> </button> --}}
                </div>
            </div>
            {{-- <h5 id="loading" style="display: none" class="text-center"><i class="fa fa-refresh fa-spin"></i> Loading....</h5> --}}
            <div class="table-responsive">
                <table class="table table-sm default-table dataTable text-center">
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
                                    <button class="btn btn-outline-warning rounded-0" id="btnBumbu" 
                                            data-toggle="modal" 
                                            data-target="#dataBumbu" 
                                            data-title="Edit Bumbu"
                                            data-remote="{{route('bumbu.edit',$row->id)}}">
                                    <i class="fa fa-pencil"></i> Edit </button>
                                    @if(!$row->customer_bumbu)
                                    <button class="btn btn-outline-danger rounded-0" id="btnDelete" 
                                            data-toggle="modal" 
                                            data-target="#dataBumbu" 
                                            data-title="Peringatan"
                                            data-remote="{{route('bumbu.show',$row->id)}}"> 
                                    <i class="fa fa-times"></i> Hapus </button>
                                    @endif
                                </td>
                            </tr>
                            <td colspan="5">
                                <div id="collapse{{$row->id}}" class="collapse" aria-labelledby="headingOne">
                                    <div class="row">
                                        <div class="col-md-4 mb-3 mt-4 ml-auto">
                                            <button class="btn btn-outline-success btn-sm float-right" 
                                            data-toggle="modal" 
                                            data-target="#dataCustomers" 
                                            data-title="Tambah Customer"
                                            data-id="{{$row->id}}"
                                            id="btnCustomer"
                                            data-remote="{{route('bumbu.create')}}"> <i class="fa fa-plus-circle"></i> Tambah Customer</button>
                                        </div>
                                    </div>
                                    <div class="p-1">
                                            <div class="row">
                                                <div class="col">
                                                    <table class="table table-sm dataTable text-center">
                                                        <thead>
                                                            <tr>
                                                                <th>No</th>
                                                                <th>Customer</th>
                                                                <th>Status Bumbu</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($row->customer_bumbu as $list)
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>{{ $list->customers->nama }}</td>
                                                                <td>
                                                                    <input type="checkbox" class="statusToggle" name="status_bumbu" data-toggle="toggle" data-on="active" data-off="inactive" data-size="sm" data-width="100" data-bumbu-id="{{ $list->id }}" data-status="{{ $list->status_bumbu }}">
                                                                </td>
                                                                <td>
                                                                    @php
                                                                    $showDeleteButton = true; // Setel defaultnya ke true
                                                                    @endphp
                                                                    @foreach ($row->bumbu_detail as $detail)
                                                                        @if($detail->customer_bumbu_id ==  $list->id)
                                                                            @php
                                                                            $showDeleteButton = false; // Ada kecocokan, jadi jangan tampilkan tombol delete
                                                                            @endphp
                                                                        @endif
                                                                    @endforeach
                                                                    
                                                                    @if ($showDeleteButton)
                                                                    <button class="btn btn-outline-danger btn-sm" data-toggle="modal"
                                                                            data-title="Delete Customer"  
                                                                            data-target="#dataBumbu" 
                                                                            data-remote="{{route('bumbu.show',$list->id)}}" 
                                                                            data-id="{{$list->id}}" 
                                                                            id="btnDeleteCustomer"> 
                                                                    <i class="fa fa-times"></i> Hapus</button>
                                                                    @endif
                                                                </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
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

        </div>
    </section> 
    

    {{-- modal tambah --}}
    <div class="modal fade" id="tambahBumbu" aria-labelledby="tambahBumbuLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahAlasanLabel">Tambah Bumbu</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('bumbu.store') }}" method="post">
                    @csrf
                    <input type="hidden" name="key" value="bumnbu_admin">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="namabumbu">Nama Bumbu</label>
                            <input type="text" name="nama" id="namabumbu" placeholder="Tuliskan nama bumbu" class="form-control" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="customer_id">Customer</label>
                            <select name="customer_id[]" id="customer_id" class="form-control form-control-sm customers" multiple="multiple">
                                <option value="" disabled selected>Pilih Customer</option>
                                @foreach ($customer as $customerItem)
                                    <option value="{{ $customerItem->id }}">{{ $customerItem->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- modal edit --}}
    <div class="modal fade" id="dataBumbu" aria-labelledby="editDataBumbu" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="title-modal"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div id="content-bumbu"></div>
            </div>
        </div>
    </div>

    {{-- modal tambah customer --}}
    <div class="modal fade" id="dataCustomers" aria-labelledby="tambahCustomerLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title-customer" id="titleCustomer"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div id="content-customer"></div>
            </div>
        </div>
    </div>

@stop

@section('header')
    <link rel="stylesheet" type="text/css" href="{{ asset('') }}plugin/DataTables/datatables.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@stop

@section('footer')
    <script type="text/javascript" src="{{ asset('') }}plugin/DataTables/datatables.min.js"></script>
    <script>
        $(document).ready( function () {
            // $('.dataTable').DataTable();

            $("#customer_id").select2();

            $(".table tbody").on("click", "#btnCustomer", function () {
                let id = $(this).attr('data-id');
                let href = $(this).attr('data-remote');
                let title = $(this).attr('data-title');
                // console.log(title);

                $.ajax({
                    type: "GET",
                    url: href,
                    data: {
                        'key': 'customer',
                        bumbuId: id,
                    },
                    success: function (response) {
                        $("#content-customer").html(response);
                        $("#titleCustomer").text(title);
                        $("#content-customer #bumbu_id").val(id);
                        $("#content-customer #customer_id").select2();
                    }
                });
            });

             // delete customer
             $(".table tbody").on("click","#btnDeleteCustomer", function () {
                let href = $(this).attr('data-remote');
                let title = $(this).attr('data-title');
                $.ajax({
                    type: "GET",
                    url : href,
                    data :{
                        'key' : 'delete_customer'
                    },
                    success: function (response) {
                        $("#content-bumbu").html(response);
                        $("#title-modal").text(title);
                    }
                });
            });


            // button edit
            $(".table tbody").on("click","#btnBumbu", function () {
                let href = $(this).attr('data-remote');
                let title = $(this).attr('data-title');
                $.ajax({
                    type: "GET",
                    url : href,
                    data : {
                        'key' : 'edit_admin'
                    },
                    success: function (response) {
                        $("#content-bumbu").html(response);
                        $("#title-modal").text(title);
                    }
                });
            });

            // button delete
            $(".table tbody").on("click","#btnDelete", function () {
                let href = $(this).attr('data-remote');
                let title = $(this).attr('data-title');
                $.ajax({
                    type: "GET",
                    url : href,
                    data :{
                        'key' : 'bumbu_admin'
                    },
                    success: function (response) {
                        $("#content-bumbu").html(response);
                        $("#title-modal").text(title);
                    }
                });
            });

           

        // toogle status
                $(".statusToggle").each(function () {
                    var statusToggle = $(this);

                    // Tangkap data dari atribut data pada elemen toggle
                    var bumbuId = statusToggle.data("bumbu-id");
                    var currentStatus = statusToggle.data("status");

                    if (currentStatus === "active") {
                        statusToggle.bootstrapToggle("on");
                    } else {
                        statusToggle.bootstrapToggle("off");
                    }

                    // set status
                    statusToggle.change(function() {
                        var newStatus = $(this).prop("checked") ? 1 : 0;
                        var csrfToken = $('meta[name="csrf-token"]').attr('content');

                    $.ajax({
                        type: "PATCH",
                        url: "bumbu/" + bumbuId,
                        data: {
                            key: 'status_bumbu',
                            bumbu_id: bumbuId,
                            status_bumbu: newStatus,
                            _token: csrfToken
                        },
                        success: function(response) {
                            if(response.success){
                                $('#alert').text(response.message);
                                $('#alert').show(1000);
                                setTimeout(() => {
                                    $('#alert').hide(1000);         
                                }, 5000);
                            } else {
                                $('#alert-fail').text(response.message);
                                $('#alert-fail').show(1000);
                                setTimeout(() => {
                                    $('#alert-fail').hide(1000);         
                                }, 5000);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Gagal memperbarui status: " + error);
                        }
                    });
                });
        });


            
        } );
    </script>
@stop
