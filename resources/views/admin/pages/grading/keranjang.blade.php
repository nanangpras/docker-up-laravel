<table class="table default-table">
    <thead>
        <tr>
            <th class="text-center">No</th>
            <th class="text-center">Nama</th>
            <th class="text-center">Tanggal Bahan Baku</th>
            <th class="text-center">Ekor/Pcs/Pack</th>
            <th class="text-center">Berat</th>
            <th class="text-center">Rataan</th>
            <th class="text-center">Keterangan</th>
            {{-- @if (($prod->grading_status == 2) OR (Auth::user()->account_role == 'superadmin')) --}}
            <th class="text-center">Aksi</th>
            {{-- @endif --}}
        </tr>
    </thead>
    <tbody>
        @php
            $ekor   =   0 ;
            $berat  =   0 ;
            $err    =   '';
        @endphp
        @foreach ($data as $i => $row)
        @php
            $ekor   +=  $row->total_item ;
            $berat  +=  $row->berat_item ;
        @endphp
            <tr>
                <td class="text-center">{{ ++$i }}</td>
                <td class="text-center">{{ $row->graditem->nama ?? '###' }}
                    @if ($row->status == 1) 

                    <span class="status status-info">Sudah di Receipt</span>
                    
                    <a href="{{ url('admin/chiller/' . App\Models\Grading::urlChiller($row->trans_id, $row->berat_item, $row->total_item, $row->tanggal_potong, $row->item_id, $row->gradprod->no_po)  ) }}" target="_blank">
                        <span class="fa fa-share"></span>
                    </a>

                    @endif    
                </td>
                <td class="text-center">{{ $row->tanggal_potong }}  
                    @if ($prod->prodpur->jenis_po == 'PO Karkas')     
                        @if ($row->status != 1)                     
                        <button type="button" onclick="getTanggalPONONLB({{ $row->id }}, '{{ $row->tanggal_potong }}')" class="btn btn-primary ml-2" data-toggle="modal" data-target="#exampleModal">
                            <i class="fa fa-edit"></i>
                        </button>
                        @endif
                        <button type="button" id="btnHistory" class="btn btn-warning btnHistory" data-id="{{$row->id}}" data-toggle="modal" data-target="#modalHistory">
                            <i class="fa fa-eye"></i>
                        </button>
                    @endif
                </td>
                <td class="text-center">{{ number_format($row->total_item) }}</td>
                <td class="text-center">{{ number_format($row->berat_item, 2) }}</td>
                <td class="text-center">
                @if (($row->berat_item > 0) AND ($row->total_item > 0))
                @php
                    $hitung_rerata  =   ($row->berat_item / $row->total_item) ;
                @endphp
                    {{ number_format($hitung_rerata, 2) }}

                    @if (substr($row->graditem->nama, -5) == '03-04')
                        @if (($hitung_rerata >= 0.30) && ($hitung_rerata <= 0.40))
                            <span class='status status-success'>Sesuai</span>
                        @else
                            <span class='status status-danger'>Cek Kembali</span>
                            @php
                                $err    .=   $row->graditem->nama . "<br>" ;
                            @endphp
                        @endif
                    @endif
                    @if (substr($row->graditem->nama, -5) == '04-05')
                        @if (($hitung_rerata >= 0.40) && ($hitung_rerata <= 0.50))
                            <span class='status status-success'>Sesuai</span>
                        @else
                            <span class='status status-danger'>Cek Kembali</span>
                            @php
                                $err    .=   $row->graditem->nama . "<br>" ;
                            @endphp
                        @endif
                    @endif
                    @if (substr($row->graditem->nama, -5) == '05-06')
                        @if (($hitung_rerata >= 0.50) && ($hitung_rerata <= 0.60))
                            <span class='status status-success'>Sesuai</span>
                        @else
                            <span class='status status-danger'>Cek Kembali</span>
                            @php
                                $err    .=   $row->graditem->nama . "<br>" ;
                            @endphp
                        @endif
                    @endif
                    @if (substr($row->graditem->nama, -5) == '06-07')
                        @if (($hitung_rerata >= 0.60) && ($hitung_rerata <= 0.70))
                            <span class='status status-success'>Sesuai</span>
                        @else
                            <span class='status status-danger'>Cek Kembali</span>
                            @php
                                $err    .=   $row->graditem->nama . "<br>" ;
                            @endphp
                        @endif
                    @endif
                    @if (substr($row->graditem->nama, -5) == '07-08')
                        @if (($hitung_rerata >= 0.70) && ($hitung_rerata <= 0.80))
                            <span class='status status-success'>Sesuai</span>
                        @else
                            <span class='status status-danger'>Cek Kembali</span>
                            @php
                                $err    .=   $row->graditem->nama . "<br>" ;
                            @endphp
                        @endif
                    @endif
                    @if (substr($row->graditem->nama, -5) == '08-09')
                        @if (($hitung_rerata >= 0.80) && ($hitung_rerata <= 0.90))
                            <span class='status status-success'>Sesuai</span>
                        @else
                            <span class='status status-danger'>Cek Kembali</span>
                            @php
                                $err    .=   $row->graditem->nama . "<br>" ;
                            @endphp
                        @endif
                    @endif
                    @if (substr($row->graditem->nama, -5) == '09-10')
                        @if (($hitung_rerata >= 0.90) && ($hitung_rerata <= 1.00))
                            <span class='status status-success'>Sesuai</span>
                        @else
                            <span class='status status-danger'>Cek Kembali</span>
                            @php
                                $err    .=   $row->graditem->nama . "<br>" ;
                            @endphp
                        @endif
                    @endif
                    @if (substr($row->graditem->nama, -5) == '10-11')
                        @if (($hitung_rerata >= 1.00) && ($hitung_rerata <= 1.10))
                            <span class='status status-success'>Sesuai</span>
                        @else
                            <span class='status status-danger'>Cek Kembali</span>
                            @php
                                $err    .=   $row->graditem->nama . "<br>" ;
                            @endphp
                        @endif
                    @endif
                    @if (substr($row->graditem->nama, -5) == '11-12')
                        @if (($hitung_rerata >= 1.10) && ($hitung_rerata <= 1.20))
                            <span class='status status-success'>Sesuai</span>
                        @else
                            <span class='status status-danger'>Cek Kembali</span>
                            @php
                                $err    .=   $row->graditem->nama . "<br>" ;
                            @endphp
                        @endif
                    @endif
                    @if (substr($row->graditem->nama, -5) == '12-13')
                        @if (($hitung_rerata >= 1.20) && ($hitung_rerata <= 1.30))
                            <span class='status status-success'>Sesuai</span>
                        @else
                            <span class='status status-danger'>Cek Kembali</span>
                            @php
                                $err    .=   $row->graditem->nama . "<br>" ;
                            @endphp
                        @endif
                    @endif
                    @if (substr($row->graditem->nama, -5) == '13-14')
                        @if (($hitung_rerata >= 1.30) && ($hitung_rerata <= 1.40))
                            <span class='status status-success'>Sesuai</span>
                        @else
                            <span class='status status-danger'>Cek Kembali</span>
                            @php
                                $err    .=   $row->graditem->nama . "<br>" ;
                            @endphp
                        @endif
                    @endif
                    @if (substr($row->graditem->nama, -5) == '14-15')
                        @if (($hitung_rerata >= 1.40) && ($hitung_rerata <= 1.50))
                            <span class='status status-success'>Sesuai</span>
                        @else
                            <span class='status status-danger'>Cek Kembali</span>
                            @php
                                $err    .=   $row->graditem->nama . "<br>" ;
                            @endphp
                        @endif
                    @endif
                    @if (substr($row->graditem->nama, -5) == '15-16')
                        @if (($hitung_rerata >= 1.50) && ($hitung_rerata <= 1.60))
                            <span class='status status-success'>Sesuai</span>
                        @else
                            <span class='status status-danger'>Cek Kembali</span>
                            @php
                                $err    .=   $row->graditem->nama . "<br>" ;
                            @endphp
                        @endif
                    @endif
                    @if (substr($row->graditem->nama, -5) == '16-17')
                        @if (($hitung_rerata >= 1.60) && ($hitung_rerata <= 1.70))
                            <span class='status status-success'>Sesuai</span>
                        @else
                            <span class='status status-danger'>Cek Kembali</span>
                            @php
                                $err    .=   $row->graditem->nama . "<br>" ;
                            @endphp
                        @endif
                    @endif
                    @if (substr($row->graditem->nama, -5) == '17-18')
                        @if (($hitung_rerata >= 1.70) && ($hitung_rerata <= 1.80))
                            <span class='status status-success'>Sesuai</span>
                        @else
                            <span class='status status-danger'>Cek Kembali</span>
                            @php
                                $err    .=   $row->graditem->nama . "<br>" ;
                            @endphp
                        @endif
                    @endif
                    @if (substr($row->graditem->nama, -5) == '18-19')
                        @if (($hitung_rerata >= 1.80) && ($hitung_rerata <= 1.90))
                            <span class='status status-success'>Sesuai</span>
                        @else
                            <span class='status status-danger'>Cek Kembali</span>
                            @php
                                $err    .=   $row->graditem->nama . "<br>" ;
                            @endphp
                        @endif
                    @endif
                    @if (substr($row->graditem->nama, -5) == '19-20')
                        @if (($hitung_rerata >= 1.90) && ($hitung_rerata <= 2.00))
                            <span class='status status-success'>Sesuai</span>
                        @else
                            <span class='status status-danger'>Cek Kembali</span>
                            @php
                                $err    .=   $row->graditem->nama . "<br>" ;
                            @endphp
                        @endif
                    @endif
                    @if (substr($row->graditem->nama, -5) == '20-21')
                        @if (($hitung_rerata >= 2.00) && ($hitung_rerata <= 2.10))
                            <span class='status status-success'>Sesuai</span>
                        @else
                            <span class='status status-danger'>Cek Kembali</span>
                            @php
                                $err    .=   $row->graditem->nama . "<br>" ;
                            @endphp
                        @endif
                    @endif
                    @if (substr($row->graditem->nama, -5) == '21-22')
                        @if (($hitung_rerata >= 2.10) && ($hitung_rerata <= 2.20))
                            <span class='status status-success'>Sesuai</span>
                        @else
                            <span class='status status-danger'>Cek Kembali</span>
                            @php
                                $err    .=   $row->graditem->nama . "<br>" ;
                            @endphp
                        @endif
                    @endif
                    @if (substr($row->graditem->nama, -5) == '22-23')
                        @if (($hitung_rerata >= 2.20) && ($hitung_rerata <= 2.30))
                            <span class='status status-success'>Sesuai</span>
                        @else
                            <span class='status status-danger'>Cek Kembali</span>
                            @php
                                $err    .=   $row->graditem->nama . "<br>" ;
                            @endphp
                        @endif
                    @endif
                    @if (substr($row->graditem->nama, -5) == '23-24')
                        @if (($hitung_rerata >= 2.30) && ($hitung_rerata <= 2.40))
                            <span class='status status-success'>Sesuai</span>
                        @else
                            <span class='status status-danger'>Cek Kembali</span>
                            @php
                                $err    .=   $row->graditem->nama . "<br>" ;
                            @endphp
                        @endif
                    @endif
                    @if (substr($row->graditem->nama, -5) == '24-25')
                        @if (($hitung_rerata >= 2.40) && ($hitung_rerata <= 2.50))
                            <span class='status status-success'>Sesuai</span>
                        @else
                            <span class='status status-danger'>Cek Kembali</span>
                            @php
                                $err    .=   $row->graditem->nama . "<br>" ;
                            @endphp
                        @endif
                    @endif
                @endif
                </td>
                <td class="text-center">{{ $row->keterangan }}</td>
                <td>
                    @if ($prod->grading_status == 2)
                    <div class="text-center">
                        @if ($row->status != 1) 
                        <button type="button" class="btn btn-primary btn-sm p-0 px-1 edit_cart" data-kode="{{ $row->id }}">
                            <i class="fa fa-edit"></i>
                        </button>

                        <button class="btn btn-danger btn-sm p-0 px-1 hapus_cart" data-id="{{ $row->id }}"><i class="fa fa-trash"></i></button>
                        @endif
                    </div>
                    @else
                        <div class="text-center">
                            {{ $row->status }}
                            @if ($row->status != 1) 
                            <button type="button" class="btn btn-primary btn-sm p-0 px-1" data-toggle="modal" data-target="#static{{ $row->id }}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm p-0 px-1 hapus_cart" data-id="{{ $row->id }}"><i class="fa fa-trash"></i></button>
                            @endif
                        </div>
                        <div class="modal fade" id="static{{ $row->id }}" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="static{{ $row->id }}Label" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Ubah Data Grading</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('grading.ubah', $prod->id) }}" method="post">
                                        @csrf @method('patch') <input type="hidden" name="x_code" value="{{ $row->id }}">
                                        <div class="modal-body">
                                            <div class="form-group">
                                                Item
                                                <div>{{ $row->graditem->nama ?? '###' }}</div>
                                                {{ $row->id }}
                                            </div>

                                            <div class="row">
                                                <div class="col pr-1">
                                                    <div class="form-group">
                                                        Ekor
                                                        <input type="number" value="{{ $row->total_item }}" name="ekor" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col pl-1">
                                                    <div class="form-group">
                                                        Berat
                                                        <input type="number" value="{{ $row->berat_item }}" name="berat" step="0.01" class="form-control">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                Keterangan
                                                <textarea name="keterangan" rows="3" required class="form-control">{{ $row->keterangan }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Ubah</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th class="text-center" colspan="3">Total</th>
            <th class="text-center">{{ number_format($ekor) }}</th>
            <th class="text-center">{{ number_format($berat, 2) }}</th>
            <th colspan="3"><textarea hidden id="error_data">{{ $err }}</textarea></th>
        </tr>
    </tfoot>
</table>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit tanggal PO Non LB</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                
                <div class="row">
                    <div class="col pr-1">
                        <input type="date" id="tanggalBBPONonLB" class="form-control" value="">
                        <input type="hidden" id="idBBPONonLB" class="form-control" value="">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="updateTanggalPONonLB()">Save changes</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalHistory" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">History tanggal PO Non LB</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="data-history"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<script>
    function getTanggalPONONLB(id, tanggal) {
        document.getElementById("tanggalBBPONonLB").value = tanggal;
        document.getElementById("idBBPONonLB").value = id;

    }

    function updateTanggalPONonLB() {
        const id        = document.getElementById("idBBPONonLB").value
        const tanggal   = document.getElementById("tanggalBBPONonLB").value

        // console.log(id, tanggal)
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url     : "{{ route('grading.edit', ['id' => "+id+"]) }}",
            method: "POST",
            data    : {
                key                     : 'updateTanggalPONonLB',
                tanggal                 : tanggal,
                id                      : id
            }, 
            success: function(data){
                if (data.status == 200) {
                    $("#exampleModal").modal('hide');
                    showNotif(data.msg)
                    window.location.reload();
                    // $('#cart').load("{{ route('grading.cart', "+data.idProduction+") }}")
                } else {
                    showAlert(data.msg)
                    
                }
                // onSuccessItem(data, inputKe, jenisItem)
            }
        })


    }

    $(".btnHistory").on("click", function () {
        var historyId = $(this).data("id");
        $.ajax({
            url     : "{{route('grading.cart', ['id' =>" + historyId +"])}}",
            method  : "GET",
            data    : {
                key     : 'history',
                id      : historyId,
            },
            success : function (r) {
                $("#data-history").html(r);
            }
        })
    })
</script>