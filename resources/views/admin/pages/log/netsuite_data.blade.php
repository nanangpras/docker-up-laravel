<div class="table-responsive card-body">
    <div class="row border-bottom" id="activeid">
        <div class="col-md-8 mb-2 pr-sm-1">
            <div class="card">
                <div class="card-header text-center">
                    <b>Range Date Summary</b>
                </div>
                <div class="card-body py-1"  style="background-color:#fcf7eb;">
                    <div class="row mb-3">
                        <div class="col pr-2">
                            <div class="text-center">
                                <div class="small py-2">
                                    Total Transaksi
                                </div>
                            </div>
                        </div>
                        <div class="col pr-2">
                            <div class="text-center">
                                <div class="font-weight-bold py-2">{{number_format($hitung['semua']) ?? "0"}}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-1 px-2">
                        <div class="col px-1" style="cursor: pointer;">
                            <div class="border text-center py-1 buttonNavigation" data-value="6" >
                                <div class="small">
                                    Ditahan
                                </div>
                                <div class="font-weight-bold">{{number_format($hitung['hold']) ?? "0"}}</div>
                            </div>
                        </div>
                        <div class="col px-1" style="cursor: pointer;">
                            <div class="border text-center py-1 buttonNavigation" data-value="5">
                                <div class="small">
                                     Persetujuan
                                </div>
                                <div class="font-weight-bold">{{number_format($hitung['approval']) ?? "0"}}</div>
                            </div>
                        </div>
                        <div class="col px-1" style="cursor: pointer;">
                            <div class="border text-center py-1 buttonNavigation" data-value="3">
                                <div class="small">
                                     Batal
                                </div>
                                <div class="font-weight-bold">{{number_format($hitung['batal']) ?? "0"}}</div>
                            </div>
                        </div>
                        <div class="col px-1" style="cursor: pointer;">
                            <div class="border text-center py-1 buttonNavigation" data-value="1">
                                <div class="small">
                                    Berhasil
                                </div>
                                <div class="font-weight-bold">{{number_format($hitung['sukses']) ?? "0"}}</div>
                            </div>
                        </div>
                        <div class="col px-1" style="cursor: pointer;">
                            <div class="border text-center py-1 buttonNavigation" data-value="0">
                                <div class="small">
                                    Gagal
                                </div>
                                <div class="font-weight-bold">{{number_format($hitung['gagal']) ?? "0"}}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-2 pr-sm-1">
            <div class="card">
                <div class="card-header text-center">
                    <b>On Going Process</b>
                </div>
                <div class="card-body py-1 " style="background-color:#f5fbff;">
                    <div class="row mb-3">
                        <div class="col pr-2">
                            <div class="text-center">
                                <div class="small py-2">
                                    Total On Going Process
                                </div>
                            </div>
                        </div>
                        <div class="col pr-2">
                            <div class="text-center">
                                <div class="font-weight-bold py-2">{{number_format($hitung['totalOGP']) ?? "0"}}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-1 px-2">
                        <div class="col px-1" style="cursor: pointer;">
                            <div class="border text-center py-1 buttonNavigation" data-value="2">
                                <div class="small">
                                    Tertunda
                                </div>
                                <div class="font-weight-bold">{{number_format($hitung['pending']) ?? "0"}}</div>
                            </div>
                        </div>
                        <div class="col px-1" style="cursor: pointer;">
                            <div class="border text-center py-1 buttonNavigation" data-value="4">
                                <div class="small">
                                    Antrian
                                </div>
                                <div class="font-weight-bold">{{number_format($hitung['antrian']) ?? "0"}}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button class="btn btn-red mt-3" id="deleteproses" name="deleteproses">Delete Proses</button> 

    <form method="post" action="{{route('sync.cancel')}}">
        @csrf
        <br>
        <button type="submit" class="btn btn-blue mb-1" name="status" value="approve">Approve Integrasi</button> &nbsp
        <button type="submit" class="btn btn-red mb-1" name="status" value="cancel">Batalkan Integrasi</button> &nbsp
        <button type="submit" class="btn btn-info mb-1" name="status" value="retry">Kirim Ulang</button> &nbsp
        <button type="submit" class="btn btn-success mb-1" name="status" value="completed">Selesaikan</button> &nbsp
        <button type="submit" class="btn btn-warning mb-1" name="status" value="hold">Hold</button> &nbsp
        <a href="{{ route('sync.wo2', ['tanggal_awal' => $tanggal_awal, 'tanggal_akhir' => $tanggal_akhir]) }}" class="btn btn-dark mb-1">WO-2</a> &nbsp

        <div class="pull-right">

            <a href="{{ route('sync.wo_total', ['tanggal_awal' => $tanggal_awal, 'tanggal_akhir' => $tanggal_akhir]) }}" class="btn btn-outline-danger mb-3">WO Total</a>
            <a href="{{ route('sync.wo_control', ['tanggal_awal' => $tanggal_awal, 'tanggal_akhir' => $tanggal_akhir]) }}" class="btn btn-outline-warning mb-3">WO Control</a>
            <a href="{{route('sync.customexport')}}" class="btn btn-success mb-3"><span class="fa fa-file"></span> Export CSV</a>
            <a href="javascript:void(0)" id="load-refresh" class="btn btn-info mb-3"><span class="fa fa-history"></span> Refresh</a>

        </div>
        <br><br>

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

        @php 
            $data_netsuite = $netsuite;
        @endphp
        @if ( !empty( $data_netsuite) )

            @foreach ($data_netsuite as $no => $field_value)
                @include('admin.pages.log.netsuite_one', ($netsuite = $field_value))
            @endforeach
        @endif
    </tbody>

    </table>

    </form>
    <br><br>

    <div class="pull-right">
        TOTAL : {{$data_netsuite->total()}}
    </div>
    <div id="daftar_paginate">
        {{ $data_netsuite->appends($_GET)->links() }}
    </div>

    
</div>
<style>
    .active {
        background-color: #666;
        color: white;
    }
</style>
<script>
$('#daftar_paginate .pagination a').on('click', function(e) {
    e.preventDefault();

    url = $(this).attr('href') ;

    reload_data(url);
});

function reload_data(url){
    $.ajax({
        url: url,
        method: "GET",
        success: function(response) {
            $('#netsuite_data').html(response);
                window.history.pushState('Netsuite', 'Netsuite', (url.replace("key=show&", "")));
                console.log(url);
                $('#load-refresh').on('click', function(){
                    reload_data(url);
                })
        }

    });
}
</script>


<script>
    $('#ns-checkall').on('change',function(){
        if ($('#ns-checkall').is(':checked')){
            $('.ns-checklist').prop('checked', true);
        }else{
            $('.ns-checklist').prop('checked', false);
        }
    })

    var btnContainer    = document.getElementById("activeid");
    var btns            = btnContainer.getElementsByClassName("buttonNavigation");
    for (var i = 0; i < btns.length; i++) {
        btns[i].addEventListener("click", function() {
            var current = document.getElementsByClassName("active");
            current[0].className = current[0].className.replace("active", "");
            this.className += " active";
        });
    }

    $('.buttonNavigation').on('click', function(){
        var status          = $(".active").data("value");
        var tanggal_awal    = "{{ $tanggal_awal }}";
        var tanggal_akhir   = "{{ $tanggal_akhir }}";
        var search          = "{{ $search }}";
        var type            = "{{ $type }}";
        var page            = "{{ $page }}";
        
        url = "{{url('admin/sync')}}?tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir + "&search=" + search+ "&status=" + status+ "&type=" + type + "&page=" + page;
        window.location.href = url;
    });
    
</script>

<script>
    var btnDeleteProses               = document.getElementById('deleteproses')
    btnDeleteProses.addEventListener('click', () => {
        var getData                       = document.querySelectorAll('input[name="selected_id[]"]');
        var result = confirm("Yakin ingin menghapus data?");
        if (result) {
            var values = [];
            for (var i = 0; i < getData.length; i++) {
                if (getData[i].checked == true) {
                    values.push(getData[i].value)
                }
            }
            // values
            fetch(`{{ route('sync.deleteNetsuiteArray') }}`, {
                    headers: {
                        'Content-Type': 'application/json',
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    method: 'POST',
                    credentials: "same-origin",
                    body: JSON.stringify({
                        data: values
                    }),
                }).then((response) => {
                    if(response.ok){
                        return response.json();
                    }
                }).then((result) => {
                    if(result.status == 'success'){
                        showNotif(result.message)
                        setTimeout(() => {
                            url = "{{url('admin/sync')}}?tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir + "&search=" + search+ "&status=" + status+ "&type=" + type + "&page=" + page;
                            window.location.href = url;
                        }, 1500)
                        
                    } else {
                        showAlert(result.message)
                    }

                })
        } else {
            return false;
        }

    })
</script>