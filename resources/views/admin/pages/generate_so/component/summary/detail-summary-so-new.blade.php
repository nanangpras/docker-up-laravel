<div class="row mb-4">
    <div class="col">
        <div class="card">
            <div class="card-header">Total Berat</div>
            <div class="card-body p-2">
                <div class="row mb-1">
                    <div class="col ">
                        <div class="border text-center">
                            <div class="font-weight-bold">{{ $hitung['totalberat'] }} Kg</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card">
            <div class="card-header">Total Qty</div>
            <div class="card-body p-2">
                <div class="row mb-1">
                    <div class="col ">
                        <div class="border text-center">
                            <div class="font-weight-bold">{{ number_format($hitung['totalqty']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card">
            <div class="card-header">Berat Cancel</div>
            <div class="card-body p-2">
                <div class="row mb-1">
                    <div class="col ">
                        <div class="border text-center">
                            <div class="font-weight-bold">{{ $hitung['totalbatal'] }} Kg</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card">
            <div class="card-header">Total Frozen</div>
            <div class="card-body p-2">
                <div class="row mb-1">
                    <div class="col ">
                        <div class="border text-center">
                            <div class="font-weight-bold">{{ $hitung['datatotalfrozen'] }} Kg</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card">
            <div class="card-header">Total Fresh</div>
            <div class="card-body p-2">
                <div class="row mb-1">
                    <div class="col ">
                        <div class="border text-center">
                            <div class="font-weight-bold">{{ $hitung['datatotalfresh'] }} Kg</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card">
            <div class="card-header">Total Order</div>
            <div class="card-body p-2">
                <div class="row mb-1">
                    <div class="col ">
                        <div class="border text-center">
                            <div class="font-weight-bold">{{ number_format($hitung['datatotorder']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-4 col-lg mb-2 px-sm-1  ml-lg-3">
        <div class="card">
            <div class="card-header">Pending</div>
            <div class="card-body p-2">
                <div class="row mb-1">
                    <div class="col ">
                        <div class="border text-center">
                            <div class="font-weight-bold">{{ number_format($hitung['datapending']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4 col-lg mb-2 px-sm-1">
        <div class="card">
            <div class="card-header">Batal</div>
            <div class="card-body p-2">
                <div class="row mb-1">
                    <div class="col ">
                        <div class="border text-center">
                            <div class="font-weight-bold">{{ number_format($hitung['databatal']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4 col-lg mb-2 px-sm-1">
        <div class="card">
            <div class="card-header">Edit</div>
            <div class="card-body p-2">
                <div class="row mb-1">
                    <div class="col ">
                        <div class="border text-center">
                            <div class="font-weight-bold">{{ number_format($hitung['dataedit']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4 col-lg mb-2 px-sm-1">
        <div class="card">
            <div class="card-header">Approve</div>
            <div class="card-body p-2">
                <div class="row mb-1">
                    <div class="col ">
                        <div class="border text-center">
                            <div class="font-weight-bold">{{ number_format($hitung['dataapprove']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4 col-lg mb-2 px-sm-1 mr-lg-3">
        <div class="card">
            <div class="card-header">Gagal</div>
            <div class="card-body p-2">
                <div class="row mb-1">
                    <div class="col ">
                        <div class="border text-center">
                            <div class="font-weight-bold">{{ number_format($hitung['datagagal']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4 col-lg mb-2 px-sm-1 mr-lg-3">
        <div class="card">
            <div class="card-header">Hold</div>
            <div class="card-body p-2">
                <div class="row mb-1">
                    <div class="col ">
                        <div class="border text-center">
                            <div class="font-weight-bold">{{ number_format($hitung['datahold']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@if (Session::get('subsidiary') == 'EBA')
    @php 

        $now        = strtotime(date('H:i'));
        $tutup0     = strtotime('15:15');
        $tutup      = strtotime('16:00');
        $tutup2     = strtotime('23:59');


        // echo $now."-";
        // echo $tutup."-";
        // echo $tutup2;

        $edit = true;
    @endphp
@endif

<section class="panel">
    <div class="card-body table-responsive">
        @foreach ($dataGroupBy as $n => $row)
        {{-- {{ date('Y-m-d', strtotime('+1 days')) . " || ". $row->tanggal_kirim }} --}}
        <div class="card card-body px-2 mb-1"
            @if ($row->netsuite_status == NULL && $row->netsuite_id == NULL && $row->status == NULL)
            style="border: 3px solid Bisque; padding: 5px"
            @elseif ($row->status == 0)
                style="border: 3px solid red; padding: 5px"
            @endif>
            <div class="row">
                <div class="col col-sm-12">
                    @if (Session::get('subsidiary') == 'EBA')
                        @if ($now > $tutup0 && $now < $tutup && date('Y-m-d', strtotime('+1 days')) == $row->tanggal_kirim)
                            <div class="alert alert-warning">
                                Pengeditan SO maksimal jam 4 Sore, setelah lewat jam cut off, tidak akan bisa diedit lagi, terima kasih.
                            </div>
                        @elseif (date('Y-m-d', strtotime('+1 days')) == $row->tanggal_kirim && $now > $tutup && $now < $tutup2)

                            <div class="alert alert-danger">
                                Pengeditan SO sudah melewati jam cutoff.
                            </div>
                            @php 
                                $edit = false;
                            @endphp
                        @elseif (date('Y-m-d', strtotime('+1 days')) < $row->tanggal_kirim)
                            @php
                                $edit = true;
                            @endphp
                        @elseif (date('Y-m-d', strtotime('+1 days')) > $row->tanggal_kirim)
                            <div class="alert alert-danger">
                                Pengeditan SO sudah melewati jam cutoff.
                            </div>
                            @php
                                $edit = false;
                            @endphp
                        @endif
                    @else
                        @php
                            $edit = true;
                        @endphp
                    @endif
                    @php
                        $ns = App\Models\Netsuite::where('tabel_id', $row->id)->where('record_type', 'sales_order')->first();
                    @endphp


                    <b>{{ $loop->iteration+($dataGroupBy->currentpage() - 1) * $dataGroupBy->perPage() }}. @if (!empty($ns->failed)) <a href="https://6484226.app.netsuite.com/app/common/entity/custjob.nl?id={{$row->netsuite_internal_id_customers}}" target="_blank">{{ $row->nama_customers ?? "#CUSTOMERHILANG" }}</a> @else {{ $row->nama_customers ?? "#CUSTOMERHILANG" }} @endif  </b> <br>
                    TANGGAL KIRIM : <b>{{ date('d/m/Y', strtotime($row->tanggal_kirim)) }}</b><br>
                    MEMO : <b>{{ $row->memo ?? '#' }}</b> || NO PO : <b>{{ $row->po_number ?? '#' }}</b><br>
                    
                </div>
                <div class="col col-sm-12">
                    NO SO : 
                    @if ($ns)
                    <b><a href="https://6484226.app.netsuite.com/app/accounting/transactions/salesord.nl?id={{$ns->response_id}}&whence=" target="_blank">{{ $row->no_so }}</a></b>

                        @if($row->no_so=="")
                            @if (!empty($ns->failed))
                                <span class="status status-danger">
                                    {{-- {{$ns->failed}} --}}
                                    @php
                                    //code...
                                        $resp = json_decode($ns->failed);
                                    @endphp

                                    @if(is_array($resp))
                                    FAILED : {{ $resp[0]->message ?? '' }} <br>   
                                    @else 
                                    FAILED : {{$ns->failed}}
                                    @endif                        
                                </span>
                            @endif
                        @endif

                    @endif
                    <br>
                    STATUS : 
                    @if($row->status=='2' || $row->status=='1')
                    <span class="status status-info">ON PROCESS</span>
                    @endif
                    {{-- @if($row->status=='3')
                        <span class="status status-success">VERIFIED</span>
                    @endif --}}
                    @if($row->verified != NULL)
                        <span class="status status-success">VERIFIED KE {{ $row->verified }}</span>
                    @endif
                    @if($row->status=='0')
                    <span class="status status-danger">VOID/BATAL</span>
                    @endif

                    @if ($row->netsuite_status == NULL && $row->netsuite_id == NULL && $row->status == NULL)
                    <br><span class="status status-info">MENUNGGU INTEGRASI </span
                    @else
                    @if($row->netsuite_status==0)<br><span class="status status-danger">INTEGRASI GAGAL </span>@endif
                    @endif
                    @if($row->edited>0)<span class="status status-warning">EDIT KE {{$row->edited}}  </span>
                    @endif
                    <br>
                    @if($row->status == '0')
                    <span>WAKTU PEMBATALAN:</span>
                    <span>{{ $row->updated_at }}</span>
                    @endif
                </div>
                <div class="col col-sm-12 text-right">
                    ACTION : 
                    @php
                        $datalog = App\Models\Adminedit::where('table_id', $row->id)->where('table_name', 'marketing_so')->count();
                    @endphp
                    @if($datalog > 0)
                        <a href="javascript:;" class="btn btn-info btn-sm cekriwayatedit" data-toggle="modal" data-target="#riwayatEditSO" title="Riwayat Edit SO" data-idso="{{ $row->id }}" data-subkey="riwayateditSO">
                            Riwayat Edit
                        </a>
                        {{-- <button class="btn btn-danger btn-sm riwayatbatalso" data-toggle="modal" data-target="#riwayatBatalSO" data-id="{{ $row->id }}" data-subkey="riwayatbatalso">
                            Riwayat Batal
                        </button> --}}
                    @endif

                    @if($row->status !== 0)
                        @if($edit)
                            @if($row->status != 0 && $row->status != 2 && $row->status != 1 || $row->netsuite_status==0)
                            <a href="{{ route('buatso.index', ['key' => 'editsummary']) }}&id={{ $row->id }}"
                                type="button" class="btn btn-success btn-sm">
                                Edit
                            </a>
                            @endif
                            {{--<button type="button" data-id="{{ $row->id }}" class="btn btn-danger btn-sm @if($row->netsuite_closed_status != "Closed") disabled @endif" onclick="cancelSO($(this).data('id'))">
                                Batalkan SO
                            </button>--}}
                        @endif
                    @endif
                    @if($row->netsuite_closed_status == "Closed" && $row->status != 0)
                        <button type="button" data-id="{{ $row->id }}" class="btn btn-danger btn-sm " onclick="cancelSO($(this).data('id'))">
                            Batalkan SO
                        </button>
                    @endif
                    @if($ns)
                        @if($ns->status=="6")
                            @if(User::setIjin('41') || User::setIjin('40'))
                            <a href="{{ route('buatso.netsuite_retry', $ns->id) }}"
                                type="button" class="btn btn-blue btn-sm">
                                Proses Ulang
                            </a>
                            @endif
                        @endif
                    @endif
                </div>
            </div>
            <div class="mt-1">
                <table class="table default-table">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>ID</th>
                            <th>SKU</th>
                            <th>Item</th>
                            <th>Parting</th>
                            <th>Qty</th>
                            <th>Berat</th>
                            <th>Plastik</th>
                            <th>Bumbu</th>
                            <th>Memo</th>
                            <th>Internal Memo</th>
                            <th>Description</th>
                            <th>Harga</th>
                            <th>Total</th>
                            <th>#</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php 
                            $total_harga    = 0;
                            $total_berat    = 0;
                            $total_qty      = 0;
                            $no             = 1;
                        @endphp
                        @foreach ($data as $raw)
                            @if($raw->marketing_so_id_so_list == $row->id)
                                @php 
                                    $cekDataList = App\Models\MarketingSOList::cekItemByProduct($raw->item_id_so_list);
                                @endphp
                                <tr
                                @if ($raw->deleted_at_so_list)
                                    style="background-color:#fde0dd"
                                @endif
                                @if(isset($cekDataList[0]))
                                    @if($cekDataList[0] == '4' || $cekDataList[0] == '10')
                                        style="background-color:#e7fddd"
                                    @endif
                                @endif
                                >
                                    <td>{{$no++}}</td>
                                    <td>#{{ $raw->id_so_list }}_{{ $raw->line_id_so_list }}</td>
                                    <td>{{ $raw->sku_items ?? '#' }}</td>
                                    <td>{{ $raw->nama_items ?? $raw->item_nama_so_list }}</td>
                                    <td>{{ $raw->parting_so_list }}</td>
                                    <td class="text-right">{{ number_format($raw->qty_so_list) }}
                                    </td>
                                    <td class="text-right">
                                        {{ number_format($raw->berat_so_list, 2) }}
                                    </td>
                                    <td>
                                        {{ $raw->plastik_so_list }}
                                        @if($raw->plastik_so_list=="")
                                        Curah
                                        @elseif($raw->plastik_so_list=="1")
                                        Meyer
                                        @elseif($raw->plastik_so_list=="2")
                                        Avida
                                        @elseif($raw->plastik_so_list=="3")
                                        Polos
                                        @elseif($raw->plastik_so_list=="4")
                                        Bukan Plastik
                                        @elseif($raw->plastik_so_list=="5")
                                        Mojo
                                        @elseif($raw->plastik_so_list=="5")
                                        Other
                                        @endif
                                    </td>
                                    <td>{{ $raw->bumbu_so_list }}</td>
                                    <td>{{ $raw->memo_so_list }}</td>
                                    <td>{{ $raw->internal_memo_so_list }}</td>
                                    <td>{{ $raw->description_item_so_list }}</td>
                                    <td class="text-right">Rp {{ number_format($raw->harga_so_list) }} ({{$raw->harga_cetakan_so_list == '1' ? 'Kilogram' : 'Ekor/Pcs/Pack'}})</td>
                                    <td class="text-right">
                                        @php 
                                            
                                            if($raw->harga_cetakan_so_list=="1"){
                                                $harga = $raw->harga_so_list*$raw->berat_so_list;
                                            }else{
                                                $harga = $raw->harga_so_list*$raw->qty_so_list;
                                            }
                                            if (!$raw->deleted_at_so_list){
                                                $total_qty      += $raw->qty_so_list;
                                                $total_berat    += $raw->berat_so_list;
                                                $total_harga    = $total_harga + $harga;
                                            }
                                        @endphp
                                        Rp {{ number_format($harga) ?? ""}}
                                    </td>
                                    <td>
                                        @if ($raw->deleted_at_so_list)
                                            <span class="status status-danger">VOID/BATAL</span>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        <tr>
                            <td>Total</td>
                            <td colspan="5" class="text-right">{{ $total_qty }}</td>
                            <td colspan="1" class="text-right">{{ number_format($total_berat,2) }}</td>
                            <td colspan="6" class="text-right">Rp {{ number_format($total_harga) }}</td>
                        </tr>
                    </tbody>
                </table>                            
                <span class="small">ID : <b>#{{ $row->id }}</b> || Tanggal SO: {{ date('d/m/Y', strtotime($row->tanggal_so)) }}</span>
                || 
                <span class="small">Created at : <b>{{ $row->created_at }}</b> || By <b>{{ $row->nama_users ?? '' }}</b></span>
                <div class="pull-right">
                    
                </div>
            </div>
            <div class="">
                @if ($ns)
                    {{-- <hr> --}}
                    {{-- {{strtotime($ns->failed_time)}}
                    {{strtotime($ns->update_time)}}
                    {{strtotime($ns->respon_time)}} --}}
                    @if (!empty($ns->failed))
                        @if((integer)strtotime($ns->failed_time)>(integer)strtotime($ns->update_time))
                        <span class="status status-danger">
                            {{-- {{$ns->failed}} --}}
                            @php
                            //code...
                                $resp = json_decode($ns->failed);
                            @endphp
                            @if(is_array($resp))
                            NETSUITE GAGAL : {{ $resp[0]->message ?? '' }} || {{$ns->failed_time}}<br>   
                            @else 
                            NETSUITE GAGAL : {{$ns->failed}} || {{$ns->failed_time}}
                            @endif                        
                        </span>
                        @endif
                    @endif
                    @if (!empty($ns->resp_update))
                        <span class="status status-warning">
                            @php
                                //code...
                                $resp = json_decode($ns->resp_update);
                            @endphp
                            NETSUITE EDIT : {{$ns->update_time}}<br>
                        </span>
                    @endif
                    @if (!empty($ns->response))
                        <span class="status status-success">
                            @php
                                //code...
                                $resp = json_decode($ns->response);
                            @endphp
                            NETSUITE SUKSES : {{$ns->respon_time}}<br>
                        </span>
                    @endif
                    @if(User::setIjin('superadmin'))
                    <hr>
                    <table class="table default-table">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="ns-checkall">
                                </th>
                                <th>ID</th>
                                <th>C&U Date</th>
                                <th>TransDate</th>
                                <th>Label</th>
                                <th>Activity</th>
                                <th>Location</th>
                                <th>IntID</th>
                                <th>Paket</th>
                                <th width="100px">Data</th>
                                <th width="100px">Action</th>
                                <th>Response</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($ns ?? false)
                            @include('admin.pages.log.netsuite_one', ($netsuite = $ns))
                            @endif
                        </tbody>
                    </table>
                    @endif
                @endif
            </div>
        </div>
        @endforeach
        <div id="paginate_so">
            {{ $dataGroupBy->appends($_GET)->onEachSide(1)->links() }}
        </div>
    </div>
</section>

<div class="modal fade" id="riwayatEditSO" aria-labelledby="riwayatLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 1200px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Riwayat Edit</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="spinerriwayatso" class="text-center" style="display: none">
                    <img src="{{ asset('loading.gif') }}" width="30px">
                </div>
                <div id="content_modal_riwayat_so"></div>
                <hr>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- modal riwayat so --}}
<div class="modal fade" id="riwayatBatalSO" aria-labelledby="riwayatLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 1200px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Riwayat Batal</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="spinerriwayatso" class="text-center" style="display: none">
                    <img src="{{ asset('loading.gif') }}" width="30px">
                </div>
                <div id="modalRiwayatBatal"></div>
                <hr>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $('.select2').select2({
        theme: 'bootstrap4'
    })

    $('#paginate_so .pagination a').on('click', function(e) {
        e.preventDefault();

        url = $(this).attr('href');
        $.ajax({
            url         : url,
            method      : "GET",
            beforeSend  : function(){
                $('#text-notif').text('Loading...');
                $('#topbar-notification').fadeIn();
            },
            success: function(response) {
                $('#data_summary').html(response);
                $('#topbar-notification').fadeOut();
            }
        });
    });

    function cancelSO(id){
        var result = confirm("Yakin ingin menghapus SO?");
        if (result) {
            $.ajax({
                url: "{{ route('buatso.index') }}",
                data:{
                    id:id,
                    _token:"{{ csrf_token() }}",
                    key:'batalkanso',
                },
                success: function(response) {
                    // console.log(response)
                    if(response.status == 1){
                        showNotif(response.msg)
                        loadsummarySO()
                    }
                }
            });
        }
    }

    $(".cekriwayatedit").click(function (e) {
        e.preventDefault();
        var id      = $(this).data('idso');
        var subkey  = $(this).data('subkey');

        $.ajax({
            url : "{{ route('buatso.index', ['key' => 'summary']) }}",
            type: "GET",
            data: {
                id      : id,
                subkey  : subkey,
            },
            success: function(data){
                $('#content_modal_riwayat_so').html(data);
            }
        });
    });

    $(".riwayatbatalso").click(function () {
        var id      = $(this).data("id")
        var subkey  = $(this).data("subkey")
        
        $.ajax({
            url     : "{{ route('buatso.index', ['key' => 'summary']) }}",
            type    : "GET",
            data    : {
                id      : id,
                subkey  : subkey,
            },
            success : function (r) {
                $('#modalRiwayatBatal').html(r);
            }
        })

    })

    // function riwayatEditSO(id){
    //     // console.log(id)
    //     $('#json_data').html('')
    //     $.ajax({
    //         url: "{{ route('buatso.index') }}",
    //         data:{
    //             id:id,
    //             _token:"{{ csrf_token() }}",
    //             key:'riwayat',
    //         },
    //         success: function(response) {
    //             console.log(response)
    //             // if(response.status == 1){
    //             //     $('#nodatariwayat').html(response.html)
    //             //     $('#riwayat').modal('show')
    //             // }   
    //             response.forEach(function(item, index){
    //                 // console.log(item)
    //                 $('#json_data').append(JSON.stringify(item, null, 4) + '\n\n')
    //             })
    //         }
    //     });
    // }
</script>
