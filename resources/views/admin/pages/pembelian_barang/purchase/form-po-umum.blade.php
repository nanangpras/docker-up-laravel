<section class="panel">
    <div class="card-body">

        <div class="form-group">
            Supplier
            <select id="supplierpo" class="form-control select2" data-placeholder="Pilih Supplier" data-width="100%"
                required>
                <option value=""></option>
                @foreach ($supplier as $row)
                <option value="{{ $row->id }}" {{ $header ? ($header->supplier_id == $row->id ? 'selected' : '') : ''
                    }}>{{ $row->nama }}</option>
                @endforeach
            </select>


        </div>
        <div class="form-group">
            Nama Vendor *(KHUSUS ONE TIME VENDOR)
            <input type="text" id="vendor_name" name="vendor_name" class="form-control"
                value="{{ $header->vendor_name ?? ''}}" placeholder="Diisi Khusus One Time Vendor">
        </div>

        <div class="form-group">
            Type PO
            <select id="type_po" class="form-control select2" data-placeholder="Pilih Form" data-width="100%" required>
                <option {{ $header ? ($header->type_po == 'PO Asset' ? 'selected' : '') : '' }} value="PO Asset">PO
                    Asset</option>
                <option {{ $header ? ($header->type_po == 'PO Other Inventory' ? 'selected' : '') : '' }} value="PO
                    Other Inventory">PO Other Inventory</option>
                <option {{ $header ? ($header->type_po == 'PO Packaging' ? 'selected' : '') : '' }} value="PO
                    Packaging">PO Packaging</option>
            </select>
        </div>
        <div class="form-group">
            Form PO
            <select id="form_id" class="form-control select2" data-placeholder="Pilih Form" data-width="100%" required>
                <option value="{{ Session::get('subsidiary') == 'CGL' ? '132' : '157' }}">{{ Session::get('subsidiary')
                    }} - Form Purchase Order Non Ayam</option>
            </select>
        </div>
        <div class="form-group">
            Jenis Ekspedisi
            <select id="jenis_ekspedisi" class="form-control select2" data-placeholder="Pilih Form" data-width="100%"
                required>
                <option value="Other">Other</option>
            </select>
        </div>

        <div class="row">
            <div class="col">
                <div class="form-group">
                    Tanggal PO
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif id="tanggal" name="tanggal" class="form-control"
                        value="{{ $header->tanggal ?? date('Y-m-d') }}" min="{{date('Y-m-d')}}" required>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    Tanggal Kirim
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif id="tanggal_kirim" name="tanggal_kirim" class="form-control"
                        value="{{ $header->tanggal_kirim ?? date('Y-m-d', strtotime('+1 day')) }}"
                        min="{{date('Y-m-d')}}" required>
                </div>
            </div>
        </div>
        <div class="form-group">
            Link File
            <textarea id="link_url" type="text" class="form-control" name="link_url"
                value="{{ $header->link_url ?? '' }}"
                placeholder="https://drive.google.com/diasuhdkahs991823ku2hiuh/123i123hu98/1293">{{ $header->link_url ?? '' }}</textarea>
        </div>
        <div class="form-group">
            Franko / Loko
            <select id="franko_loko" name="franko_loko" class="form-control" data-placeholder="Pilih Form"
                data-width="100%" required>
                <option {{ $header ? ($header->franco_loco == "" ? "selected" : "") : "" }} value="">- Pilih Alamat -
                </option>
                <option {{ $header ? ($header->franco_loco == "Toko" ? "selected" : "") : "" }} value="Toko">Toko
                </option>
                <option {{ $header ? ($header->franco_loco == "Jl. KH. Wachid Hasyim, Sawo, Kec. Jetis, Mojokerto" ?
                    "selected" : "") : "" }} value="Jl. KH. Wachid Hasyim, Sawo, Kec. Jetis, Mojokerto">Jl. KH. Wachid
                    Hasyim, Sawo, Kec. Jetis, Mojokerto</option>
            </select>
        </div>

        <div class="form-group">
            Memo
            <textarea id="keterangan" rows="2" placeholder="Tuliskan Memo (opsional)"
                class="form-control">{{ $header->memo ?? "" }}</textarea>
        </div>
        <div class="form-group">
            Created by {{\App\Models\User::find($header->user_id ?? "")->name ?? ""}} || {{$header->created_at ?? ""}}
        </div>

        @if (!$header)
        <button class="btn btn-block btn-primary" id="buat_header">Simpan Vendor PO</button>
        @else
        <button class="btn btn-block btn-outline-info" id="header_po">Update Header PO</button>
        <button class="btn btn-block btn-outline-danger" id="batal_header">Batal</button>
        @endif

    </div>
</section>

<script>
    $('.select2').select2({
    theme: 'bootstrap4',
})
</script>

<script>
    $("#buat_header").on('click', function() {
    var supplier        =   $("#supplierpo").val() ;
    var tanggal         =   $("#tanggal").val() ;
    var keterangan      =   $("#keterangan").val() ;
    var form_id         =   $("#form_id").val() ;
    var type_po         =   $("#type_po").val() ;
    var tanggal_kirim   =   $("#tanggal_kirim").val() ;
    var jenis_ekspedisi =   $("#jenis_ekspedisi").val() ;
    var franko_loko     =   $("#franko_loko").val() ;
    var link_url        =   $("#link_url").val() ;
    var vendor_name     =   $("#vendor_name").val() ;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $("#buat_header").hide() ;

    $.ajax({
        url: "{{ route('pembelian.purchasestore') }}",
        method: "POST",
        data: {
            supplier        :   supplier ,
            tanggal         :   tanggal ,
            type_po         :   type_po ,
            keterangan      :   keterangan ,
            tanggal_kirim   :   tanggal_kirim ,
            form_id         :   form_id ,
            jenis_ekspedisi :   jenis_ekspedisi ,
            franko_loko     :   franko_loko ,
            link_url        :   link_url ,
            vendor_name     :   vendor_name ,
            key             :   'buat_header' ,
        },
        success: function(data) {
            if (data.status == 400) {
                showAlert(data.msg);
            } else {
                showNotif(data.msg);
                // loadDataView();
                if($('input[id="peritempr"]:checked').length > 0){
                    loadDataPerItem()
                } else {
                    loadDataView()
                }
                $("#loading_list").attr('style', 'display: block') ;
                $("#data_list").load("{{ route('pembelian.purchase', ['key' => 'list']) }}", function() {
                    $("#loading_list").attr('style', 'display: none') ;
                }) ;

                $("#data_summary").load("{{ route('pembelian.purchase', ['key' => 'summary']) }}") ;
                $("#purchase-info").load("{{ route('pembelian.purchase', ['key' => 'info']) }}") ;
            }
            $("#buat_header").show() ;
        }
    });
})
</script>

<script>
    $("#batal_header").on('click', function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $("#batal_header").hide() ;

    $.ajax({
        url: "{{ route('pembelian.purchasestore') }}",
        method: "POST",
        data: {
            key :   'batal_header' ,
        },
        success: function(data) {
            if (data.status == 400) {
                showAlert(data.msg);
            } else {
                showNotif(data.msg);
                // loadDataView();
                if($('input[id="peritempr"]:checked').length > 0){
                    loadDataPerItem()
                } else {
                    loadDataView()
                }
                $("#loading_list").attr('style', 'display: block') ;
                $("#data_list").load("{{ route('pembelian.purchase', ['key' => 'list']) }}", function() {
                    $("#loading_list").attr('style', 'display: none') ;
                }) ;

                $("#data_summary").load("{{ route('pembelian.purchase', ['key' => 'summary']) }}") ;
                $("#purchase-info").load("{{ route('pembelian.purchase', ['key' => 'info']) }}") ;
            }
            $("#batal_header").show() ;
        }
    });
})
</script>

<script>
    $('#header_po').on('click', function() {
    var supplier        =   $("#supplierpo").val() ;
    var tanggal         =   $("#tanggal").val() ;
    var keterangan      =   $("#keterangan").val() ;
    var form_id         =   $("#form_id").val() ;
    var type_po         =   $("#type_po").val() ;
    var tanggal_kirim   =   $("#tanggal_kirim").val() ;
    var jenis_ekspedisi =   $("#jenis_ekspedisi").val() ;
    var franko_loko     =   $("#franko_loko").val() ;
    var link_url        =   $("#link_url").val() ;
    var vendor_name     =   $("#vendor_name").val() ;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $("#buat_header").hide() ;

    $.ajax({
        url: "{{ route('pembelian.purchasestore') }}",
        method: "POST",
        data: {
            supplier        :   supplier ,
            tanggal         :   tanggal ,
            type_po         :   type_po ,
            keterangan      :   keterangan ,
            tanggal_kirim   :   tanggal_kirim ,
            form_id         :   form_id ,
            jenis_ekspedisi :   jenis_ekspedisi ,
            franko_loko     :   franko_loko ,
            link_url        :   link_url ,
            vendor_name     :   vendor_name ,
            key             :   'update_header' ,
        },
        success: function(data) {
            if (data.status == 400) {
                showAlert(data.msg);
            } else {
                showNotif(data.msg);
                // loadDataView();
                if($('input[id="peritempr"]:checked').length > 0){
                    loadDataPerItem()
                } else {
                    loadDataView()
                }
                $("#loading_list").attr('style', 'display: block') ;
                $("#data_list").load("{{ route('pembelian.purchase', ['key' => 'list']) }}", function() {
                    $("#loading_list").attr('style', 'display: none') ;
                }) ;

                $("#data_summary").load("{{ route('pembelian.purchase', ['key' => 'summary']) }}") ;
                $("#purchase-info").load("{{ route('pembelian.purchase', ['key' => 'info']) }}") ;
            }
            $("#buat_header").show() ;
        }
    });
 })
</script>