
<a href="{{ route('pembelian.riwayat', array_merge(['get' => 'unduh'], $_GET)) }}" class="btn btn-success btn-sm mb-2">Unduh</a>
<section class="panel">
    <div class="card-body p-2">
        <div class="table-responsive">
        <table class="table default-table">
            <thead>
                <tr>
                    <th width="20">No</th>
                    <th width="10%">No PR</th>
                    <th width="10%">Department</th>
                    <th  width="10%">Tanggal</th>
                    <th>SKU</th>
                    <th>Item</th>
                    <th>Keterangan</th>
                    <th>Qty</th>
                    <th>Sisa</th>
                    <th>NO PO</th>
                    <th>Created</th>
                    <th width="50">Aksi</th>
                </tr>
            </thead>
            <tbody class="accordion" id="accordionSummaryPR">
                @foreach ($data as $no => $row)
                    @foreach ($row->list_beli as $i => $list)
                    <tr @if($list->deleted_at) style="background-color:pink" @endif>
                        <td>
                            @if($i==0)<a href="#{{$row->id}}"> {{ $no+1 }}</a>@endif
                        </td>
                        <td>
                            @if($i==0)#{{ $row->no_pr }}@endif
                        </td>
                        <td> {{ $row->divisi }}</td>
                        <td>
                            @if($i==0){{ date('d/m/Y', strtotime($row->tanggal)) }}@endif
                        </td>
                        <td>
                            {{ $list->item->sku ?? "#ITEM DIHAPUS" }}
                        </td>
                        <td>
                            {{ $list->item->nama ?? "#ITEM DIHAPUS" }}
                        </td>
                        <td>
                            {{ $list->keterangan }}
                        </td>
                        <td>
                            {{number_format($list->qty, 1)}}   {{ $list->unit }}
                        </td>
                        <td>
                            {{number_format($list->sisa, 1)}}   {{ $list->unit }}
                        </td>
                        <td>
                            @php
                                $getParent = \App\Models\PembelianList::getParentId($list->pembelian_id, $list->id);
                            @endphp
                            @foreach($row->pr_po as $po)
                                @if($getParent == $po->id)
                                    {{ $po->document_number }}<br>
                                @else
                                @endif
                            @endforeach
                            {{-- @if($i==0)
                                @if(count($row->pr_po))
                                    @foreach($row->pr_po as $po)
                                        {{ $po->document_number }}<br>
                                    @endforeach
                                @else 
                                    #PENDING
                                @endif
                            @endif --}}
                        </td>
                        <td>{{$row->created_at}}</td>
                        <td>
                            @if($i==0)<button class="btn btn-primary" data-toggle="collapse" data-target="#collapseSummaryPR{{ $row->id }}" aria-expanded="true"
                            aria-controls="collapseSummaryPR{{ $row->id }}">Expand Detail</button>
                        <br>
                            @if(User::setIjin('superadmin'))
                                @if(!$row->deleted_at)
                                    <a href="{{ route('pembelian.riwayat', ['key' => 'editsummaryPR']) }}&id={{ $row->id }}"
                                    class="btn btn-success btn-sm mt-2" style="color: white">Edit</a>
                                    <br>
                                        @if(!count($row->pr_po))
                                        <button type="button" data-id="{{ $row->id }}" class="btn btn-danger btn-sm mt-2" onclick="cancelpr($(this).data('id'))">
                                            Batalkan PR
                                        </button>
                                        @endif
                                    @endif
                                @endif
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="12" id="showAccordSummaryPR{{ $row->id }}">
                            <div id="collapseSummaryPR{{ $row->id }}" class="collapse" aria-labelledby="headingOne"
                                data-parent="#accordionSummaryPR">
                                <div class="p-2">
                                    <b>STATUS PR</b>
                                    @php 
                                        $log_pr = App\Models\Adminedit::where('table_name', 'pembelian')
                                                                        ->where('table_id', $row->id)
                                                                        ->orderBy('id', 'desc')
                                                                        ->get();
                                    @endphp
                                    @foreach($log_pr as $logs)
                                        <li>{{$logs->content}}  <span class="pull-right">{{$logs->created_at}}</span></li>
                                    @endforeach
                                    <hr>
                                    <span class="text-small">
                                        Created by {{ $row->user->name ?? '#' }}  || {{ $row->created_at }}
                                    </>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
</section>
<script>
    $('.approve').click(function() {
        var id = $(this).data('id');
        var awal = $("#tanggal_awalSummaryPR").val();
        var akhir = $("#tanggal_akhirSummaryPR").val();
        // var harga   =   $("#harga" + id).val() ;
        // var estimasi=   $("#estimasi" + id).val() ;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.approve').hide();

        $.ajax({
            url: "{{ route('pembelian.store') }}",
            method: "POST",
            data: {
                id: id,
                // harga   :   harga ,
                // estimasi:   estimasi ,
                key: 'approve'
            },
            success: function(data) {
                if (data.status == 400) {
                    showAlert(data.msg);
                } else {
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open');
                    $('#approve' + id).modal('hide');
                    showNotif(data.msg);
                    $("#data_riwayat").load(
                        "{{ route('pembelian.riwayat', ['key' => 'SummaryPR']) }}&awal=" + awal +
                        "&akhir=" + akhir);
                }
                $('.approve').show();
            }
        });
    })
</script>

<script>
    $('.hapus_item').click(function() {
        var id = $(this).data('id');
        var awal = $("#tanggal_awalSummaryPR").val();
        var akhir = $("#tanggal_akhirSummaryPR").val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.hapus_item').hide();

        $.ajax({
            url: "{{ route('pembelian.store') }}",
            method: "POST",
            data: {
                id: id,
                key: 'hapus_item'
            },
            success: function(data) {
                showNotif('List pembelian berhasil dihapus');
                $("#data_riwayat").load(
                    "{{ route('pembelian.riwayat', ['key' => 'SummaryPR']) }}&awal=" + awal +
                    "&akhir=" + akhir);
            }
        });
    })

    $('.batal_semua').click(function() {
        var id = $(this).data('id');
        var awal = $("#tanggal_awalSummaryPR").val();
        var akhir = $("#tanggal_akhirSummaryPR").val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.batal_semua').hide();

        $.ajax({
            url: "{{ route('pembelian.store') }}",
            method: "POST",
            data: {
                id: id,
                key: 'batal_semua'
            },
            success: function(data) {
                showNotif('Daftar pembelian berhasil dihapus');
                $("#data_riwayat").load(
                    "{{ route('pembelian.riwayat', ['key' => 'SummaryPR']) }}&awal=" + awal +
                    "&akhir=" + akhir);
            }
        });
    })


    function cancelpr(id){
        // console.log(id)
        var awal = $("#tanggal_awalSummaryPR").val();
        var akhir = $("#tanggal_akhirSummaryPR").val();
        var result = confirm("Yakin ingin menghapus PR?");
        if (result) {
            $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            });
            $.ajax({
                url: "{{ route('pembelian.store') }}",
                method: "POST",
                data:{
                    id:id,
                    key:'batalkanPR',
                },
                success: function(response) {
                    console.log(response)
                    if(response.status == 200){
                        showNotif(response.msg)
                        // $("#summaryPR").load("{{ route('pembelian.riwayat', ['key' => 'SummaryPR']) }}&awal=" + awal + "&akhir=" + akhir);
                        loadDataSummaryPR()
                    }
                }
            });
        }
    }
</script>

