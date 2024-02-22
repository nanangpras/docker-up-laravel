@php
                $item_qty           = 0;
                $item_berat         = 0;
                $item_qty_akhir     = 0;
                $item_berat_akhir   = 0;
                $karung_total       = 0;
            @endphp
            @foreach ($data as $val)
            @php 

                // PENJUMLAHAN

                if ($val->status == 2) {

                    $item_qty           += $val->qty_awal;
                    $item_berat         += $val->berat_awal;
                    $item_qty_akhir     += $val->qty;
                    $item_berat_akhir   += $val->berat;
                    $karung_total       += $val->karung_isi;
                } else {

                    $item_qty           -= $val->qty_awal;
                    $item_berat         -= $val->berat_awal;
                    $item_qty_akhir     -= $val->qty;
                    $item_berat_akhir   -= $val->berat;
                    $karung_total       += $val->karung_isi;
                }

                
            @endphp 
            @endforeach

    <section class="panel">

        <div class="card-body table-responsive">
            {{-- <span class="status status-info">*Warna kuning pada table merupakan data pencarian tanggal SOH</span>
            <br> --}}



            <table width="100%" class="table default-table">
        <thead>
            <tr>
                <th>No</th>
                <th>ID</th>
                <th>ID Gudang Keluar</th>
                <th>Kode</th>
                <th>Tanggal</th>
                <th>Nama</th>
                <th>Karung Isi</th>
                <th>Parting</th>
                <th>Customer</th>
                <th>Sub Item</th>
                <th>Packaging</th>
                <th>SubPack</th>
                <th>Asal ABF</th>
                <th>Label</th>
                <th>Qty/Pcs/Ekor</th>
                <th>Berat (Kg)</th>
                {{-- <th>Qty/Pcs/Ekor Sisa</th>
                <th>Berat (Kg) Sisa</th> --}}
                <th>Status</th>
                <th>Tujuan</th>
                {{-- <th>Trans Date</th> --}}
                <th>Aksi</th>
            </tr>
            <tr style="background-color: #FFFBEB">
                    <th colspan="6">Subtotal</th>
                    <th class="text-center">{{ $karung_total }}</th>
                    <th colspan="7"></th>
                    <th class="text-center">{{ $item_qty }} </th>
                    <th class="text-center">{{ number_format($item_berat,2) }} </th>
                    <th colspan="3"></th>
            </tr>
        </thead>
        <tbody>
            @php
                $item_qty           = 0;
                $item_berat         = 0;
                $item_qty_akhir     = 0;
                $item_berat_akhir   = 0;
            @endphp
            @foreach ($data as $val)
            @php 

                // PENJUMLAHAN

                if ($val->status == 2) {

                    $item_qty           += $val->qty_awal;
                    $item_berat         += $val->berat_awal;
                    $item_qty_akhir     += $val->qty;
                    $item_berat_akhir   += $val->berat;

                } else {

                    $item_qty           -= $val->qty_awal;
                    $item_berat         -= $val->berat_awal;
                    $item_qty_akhir     -= $val->qty;
                    $item_berat_akhir   -= $val->berat;
                }

                
            @endphp 


                <tr @if($tanggal == $val->production_date) style="background-color: #FFFF8F" @endif>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $val->id ?? '#' }}</td>
                    <td>{{ $val->gudang_id_keluar ?? '#' }}</td>
                    <td>{{ $val->production_code }}</td>
                    <td>{{ date('d/m/y', strtotime($val->production_date)) }}</td>
                    <td>
                        <div style="width: 280px">
                            {{ $val->productitems->nama ?? '' }}
                            @if ($val->selonjor)
                            <div class="font-weight-bold text-danger">SELONJOR</div>
                            @endif
                            @if ($val->barang_titipan)
                            <div class="font-weight-bold text-primary">BARANG TITIPAN</div>
                            @endif
                        </div>
                    </td>

                    {{-- KARUNG ISI --}}
                    <td>{{ $val->karung_isi }}</td>
                    {{-- END KARUNG ISI --}}

                    {{-- PARTING --}}
                    <td>{{ $val->parting ?? 0 }}</td>
                    {{-- END PARTING --}}
                    
                    {{-- CUSTOMER --}}
                    <td><div style="width: 130px">{{ $val->konsumen->nama ?? "" }}</div></td>
                    {{-- END CUSTOMER --}}
                    
                    {{-- SUB ITEM / ITEM NAME --}}
                    <td><div style="width: 130px">{{ $val->sub_item }}</div></td>
                    {{-- END SUB ITEM / ITEM NAME--}}
                    
                    {{-- PLASTIK --}}
                    <td>{{ $val->plastik_group }}</td>
                    {{-- PLASTIK --}}

                    {{-- SUB PACK --}}
                    <td>{{ $val->subpack }}</td>
                    {{-- END SUB PACK --}}

                    <td>{{ $val->asal_abf }}</td>

                    <td>{{ $val->label }}</td>

                    <td class="text-right">{{ number_format($val->qty_awal ?? '0') }}</td>
                    <td class="text-right">{{ number_format(($val->berat_awal ?? '0'), 2) }}</td>
                    {{-- <td class="text-right">{{ number_format($val->qty ?? '0') }}</td>
                    <td class="text-right">{{ number_format(($val->berat ?? '0'), 2) }}</td> --}}
                    <td>
                        @if ($val->status == 2)
                            <span class="status status-success">Masuk</span>
                        @elseif ($val->status == 4)
                            <span class="status status-danger">Keluar</span>
                        @endif
                    </td>
                    <td>
                        @if ($val->status != 1)
                            <div style="width: 130px">{{ $val->productgudang->code ?? '' }}</div>
                        @else
                            <div class="form-group">
                                <select name="waretujuan" class="form-input-table" id="waretujuan">
                                    <option value="" disabled selected hidden>Pilih</option>
                                    @foreach ($warehouse as $ware)
                                        <option value="{{ $ware->id }}" @if ($val->gudang_id == $ware->id) selected @endif>{{ $ware->code }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </td>
                    {{-- <td>{{$val->created_at}}</td> --}}
                    <td>
                        <div>
                            <a href="{{route('warehouse.tracing', $val->id)}}" class="btn btn-sm btn-blue" target="_blank">Detail</a>
                            <button class="btn btn-outline-info" data-toggle="modal" data-target="#edit{{ $val->id }}">Edit</button>
                            <a href="{{route('warehouse.soh_edit',$val->id)}}" class="btn btn-sm btn-green" target="_blank"><i class="fa fa-pencil-square-o"></i></a>
                            @if ($val->status == 1)
                            <button type="submit" class="btn btn-primary btn-sm terimagudang" data-kode="{{ $val->id }}">Terima</button>
                            @endif
                        </div>
                    </td>
                </tr>
                

                <div class="modal fade" id="edit{{ $val->id }}" aria-labelledby="edit{{ $val->id }}Label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="edit{{ $val->id }}Label">Edit Inbound</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    Nama Item
                                    <input type="text" class="form-control" value="{{ $val->productitems->nama }}" readonly>
                                </div>

                                {{-- <div class="form-group">
                                    Kemasan
                                    <input type="text" class="form-control" value="{{ $val->packaging }}" readonly>
                                </div>

                                <div class="form-group">
                                    Sub Packaging
                                    <input type="text" class="form-control" value="{{ $val->subpack }}" readonly>
                                </div> --}}
                                <div class="form-group">
                                    <div class="small mb-2">Packaging</div>
                                    <select name="packaging" id="selectPackaging{{ $val->id }}" data-placeholder="Pilih Item Name" class="form-control select2 mt-2" required>
                                        @foreach ($plastik as $p)
                                            <option value="{{ $p->nama }}" {{$val->packaging == $p->nama ? 'selected' : ''}}>{{ $p->nama }} -
                                                {{ $p->subsidiary }}{{ $p->netsuite_internal_id }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <div class="small mb-2">Customer</div>
                                    <select name="customer" id="selectCustomer{{ $val->id }}" data-placeholder="Pilih Customer" class="form-control select2 mt-2" required>
                                        <option value="NONE">NONE</option>
                                        @foreach ($customer as $cst)
                                            <option value="{{ $cst->id }}" {{$val->customer_id == $cst->id ? 'selected' : ''}}>{{ $cst->nama }} -
                                                {{ $cst->kode }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="row">
                                    
                                    <div class="col">
                                        <div class="small mb-2">Karung</div>
                                        <select name="karung[]" id="karung{{ $val->id }}" data-placeholder="Pilih Item Name" class="form-control mt-2" required>
                                            <option value="Curah">None</option>
                                            @foreach ($karung as $krg)
                                                <option value="{{ $krg->sku }}" {{ $krg->sku == $val->karung ? 'selected' : ''}}>{{ $krg->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <div class="small mb-2">Qty Karung</div>
                                            <input type="number" name="karung_qty[]" id="karung_qty{{ $val->id }}" class="form-control" max="" placeholder="Qty" value="{{ $val->karung_qty }}" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <div class="small mb-2">Isi Karung</div>
                                            <input type="number" id="karung_isi{{ $val->id }}" name="karung_isi[]" class="form-control" max="" placeholder="Isi Karung" value="{{ $val->karung_isi }}" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="small mb-2">Plastik (AVIDA,POLOS,MEYER,MOJO)</div>
                                    <button type="button" class="btn btn-outline-success btn-sm mb-3" data-toggle="modal" data-target="#plastikModal">Tambah Plastik Group</button>
                                    <select name="plastik[]" id="plastik{{$val->id}}" data-placeholder="Pilih Plastik" class="form-control select2 mt-2" required>
                                        <option value=""></option>
                                        @foreach ($plastikGroup as $groupPlastik)
                                            <option value="{{ $groupPlastik->data }}" {{ $groupPlastik->data == $val->plastik_group ? 'selected' : ''}}>{{ $groupPlastik->data }}</option>
                                        @endforeach
                                    </select>
                                    {{-- <input type="text" name="plastik[]" id="plastik{{ $val->id }}" class="form-control" max=""placeholder="Isi Karung" value="{{ $val->plastik_group }}" autocomplete="off"> --}}
                                </div>

                                <div class="form-group">
                                    <div class="small mb-2">Sub Item / Keterangan</div>
                                    <button type="button" class="btn btn-outline-success btn-sm mb-2" data-toggle="modal" data-target="#modalSubItem">Tambah Item Name</button>
                                    <select name="subitem" id="sub_item{{ $val->id }}" data-placeholder="Pilih Item Name" class="form-control select2">
                                        <option value=""></option>
                                        <option value="NONE" {{ 'NONE' == $val->sub_item ? 'selected' : '' }}>NONE</option>
                                        @foreach ($sub_item as $name)
                                            <option value="{{ $name->data }}" {{ $name->data == $val->sub_item ? 'selected' : '' }}>{{ $name->data }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col pr-1">
                                        <div class="form-group">
                                            Qty Awal
                                            <input type="number" value="{{ $val->qty_awal }}" class="form-control" id="qtyAwal{{ $val->id }}">
                                        </div>
                                    </div>
                                    <div class="col pl-1">
                                        <div class="form-group">
                                            Berat Awal
                                            <input type="number" value="{{ $val->berat_awal }}" class="form-control" id="beratAwal{{ $val->id }}" step="0.01">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col pr-1">
                                        <div class="form-group">
                                            Qty Sisa
                                            <input type="number" value="{{ $val->qty }}" class="form-control" id="qty{{ $val->id }}">
                                        </div>
                                    </div>
                                    <div class="col pl-1">
                                        <div class="form-group">
                                            Berat Sisa
                                            <input type="number" value="{{ $val->berat }}" class="form-control" id="berat{{ $val->id }}" step="0.01">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    Parting
                                    <input type="number" value="{{ $val->parting }}" class="form-control" id="parting_soh{{ $val->id }}" step="0.01">
                                </div>

                                <div class="row">
                                    <div class="col pr-1">
                                        <div class="form-group">
                                            Lokasi
                                            @php 
                                                $warehouse = App\Models\Gudang::where('subsidiary', env('NET_SUBSIDIARY'))->get();
                                            @endphp
                                            <select data-width="100%" id="lokasi{{ $val->id }}" class="form-control select2">
                                                @foreach ($warehouse as $row)
                                                <option value="{{ $row->id }}" {{ $val->gudang_id == $row->id ? 'selected' : '' }}>{{ $row->code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col pl-1">
                                        <div class="form-group">
                                            ABF
                                            <select data-width="100%" id="abf{{ $val->id }}" class="form-control select2">
                                                <option value="abf_1" {{ $val->asal_abf == 'abf_1' ? 'selected' : '' }}>ABF 1</option>
                                                <option value="abf_2" {{ $val->asal_abf == 'abf_2' ? 'selected' : '' }}>ABF 2</option>
                                                <option value="abf_3" {{ $val->asal_abf == 'abf_3' ? 'selected' : '' }}>ABF 3</option>
                                                <option value="abf_4" {{ $val->asal_abf == 'abf_4' ? 'selected' : '' }}>ABF 4</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <input type="checkbox" id="titipan{{ $val->id }}" {{ $val->barang_titipan ? 'checked' : '' }}>
                                    <label for="titipan{{ $val->id }}">Barang Titipan</label>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button" data-id="{{ $val->id }}" class="btn btn-primary update_inbound">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </tbody>
    </table>

        </div>
    </section>
    <script>
        $(document).ready(function() {
            $('.select2').each(function() {
                $(this).select2({
                    theme: 'bootstrap4',
                    dropdownParent: $(this).parent()
                });
            })
        });

            
        $(".update_inbound").on('click', function() {
            var id                  =   $(this).data("id") ;
            var sub_item            =   $("#sub_item" + id).val() ;
            var qty                 =   $("#qty" + id).val() ;
            var berat               =   $("#berat" + id).val() ;
            var qtyAwal             =   $("#qtyAwal" + id).val() ;
            var beratAwal           =   $("#beratAwal" + id).val() ;
            var lokasi_dg           =   $("#lokasi" + id).val() ;
            var abf                 =   $("#abf" + id).val() ;
            var plastik             =   $("#plastik" + id).val() ;
            var karung_isi          =   $("#karung_isi" + id).val() ;
            var karung_qty          =   $("#karung_qty" + id).val() ;
            var karung              =   $("#karung" + id).val() ;
            var selectPackaging     =   $("#selectPackaging" + id).val() ;
            var customer            =   $("#selectCustomer" + id).val() ;
            var parting             =   $("#parting_soh" + id).val() ;



            var titipan             =   $("#titipan" + id + ":checked").val() ;
            var mulai               =   $("#tanggal_mulai").val();
            var akhir               =   $("#tanggal_akhir").val();
            var lokasi              =   $("#lokasi_gudang").val();
            var filter              =   encodeURIComponent($("#filter_stock").val());

            $(".update_inbound").hide() ;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('warehouse.update_stock') }}",
                method: "PATCH",
                data: {
                    id          :   id ,
                    sub_item    :   sub_item ,
                    qty         :   qty ,
                    berat       :   berat ,
                    lokasi      :   lokasi_dg ,
                    abf         :   abf ,
                    titipan     :   titipan ,
                    selectPackaging,
                    karung,
                    karung_qty,
                    karung_isi,
                    plastik,
                    abf,
                    beratAwal,
                    qtyAwal,
                    customer,
                    parting
                },
                success: function(data) {
                    if (data.status == 400) {
                        showAlert(data.msg) ;
                    } else {
                        showNotif(data.msg);
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000)
                        // $('.modal-backdrop').remove();
                        // $('body').removeClass('modal-open');
                        // $("#warehouse-masuk").load("{{ route('warehouse.masuk') }}?tanggal_mulai=" + mulai + "&tanggal_akhir=" + akhir + "&lokasi=" + lokasi + "&filter=" + filter) ;
                    }
                    $(".update_inbound").show();
                }
            });
        })
    </script>