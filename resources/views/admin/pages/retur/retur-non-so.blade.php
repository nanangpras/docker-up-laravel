@extends('admin.layout.template')

@section('title', 'Retur Authorization')

@section('content')

    <div class="row mb-4">
        <div class="col">
            <a href="{{ route('retur.index') }}#custom-tabs-three-summary" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>Back</a>
        </div>
        <div class="col-7 col text-center py-2">
            <b class="text-uppercase">@if(!isset($key)) RETUR NON SO @else RETUR NON INTEGRASI @endif</b>
        </div>
        <div class="col text-right"><button class="btn btn-outline-success btn-sm" data-toggle="modal" data-target="#tambahAlasan">Tambah Alasan</button></div>
    </div>

    <div class="modal fade" id="tambahAlasan" aria-labelledby="tambahAlasanLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahAlasanLabel">Tambah Alasan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('retur.store') }}" method="post">
                    @csrf <input type="hidden" name="key" value="alasan">
                    <div class="modal-body">
                        <div class="form-group">
                            Kelompok
                            <select name="jenis" data-placeholder="Pilih Jenis" data-width="100%" class="form-control select2" required>
                                <option value=""></option>
                                <option value="Bau">Bau</option>
                                <option value="Memar">Memar</option>
                                <option value="Patah">Patah</option>
                                <option value="Warna Tidak Standar">Warna Tidak Standar</option>
                                <option value="Kualitas lain-lain">Kualitas lain-lain</option>
                                <option value="Non Kualitas">Non Kualitas</option>
                                <option value="Packing bermasalah">Packing bermasalah</option>
                                <option value="Produk tidk sesuai order">Produk tidk sesuai order</option>
                                <option value="Salah order">Salah order</option>
                                <option value="Tidak terkirim">Tidak terkirim</option>
                                <option value="Masalah Internal Konsumen">Masalah Internal Konsumen</option>
                            </select>
                        </div>

                        <div class="form-group">
                            Nama Alasan
                            <input type="text" name="alasan" placeholder="Tuliskan nama alasan" class="form-control" autocomplete="off" required>
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

    <section class="panel">
        <div class="card mb-2">
            <div class="card-body">
                <form method="post" action="{{ route('retur.nonsosubmit') }}" id="retur" class="retur">
                    @if(isset($key)) <input type="hidden" name="nonnetsuite" value="nonnetsuite"> @endif
                    <div class="form-group mb-2">
                        Customer
                        <select name="customer_id" class="form-control select2" id="customer_id" data-placeholder="Pilih Customer" data-width="100%">
                            <option value=""></option>
                            @foreach ($customer as $cus)
                                <option value="{{ $cus->id }}">{{ $cus->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    @csrf
                    <input type="hidden" name="order_id" value="">

                    <div class="data-loop">
                        <div class="row">
                            <div class="col-3 pr-1">
                                <select name="item[]" class="form-control select2" data-placeholder="Pilih Item" data-width="100%" id="item_select" onchange="tujuan()">
                                    <option value=""></option>
                                    @foreach ($item as $it)
                                        <option value="{{ $it->id }}" data-category="{{ $it->category_id }}">{{ $it->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-2 px-1">
                                <div class="form-group">
                                    <select name="returto[]" class="form-control" id='tujuan_retur-0' onchange="tujuanRetur(0)">
                                        <option value="" disabled selected hidden>Pilih Tujuan</option>
                                        <option value="produksi">Reproses Produksi</option>
                                        <option value="chillerfg">Sampingan</option>
                                        <option value="gudang">Kembali Ke Freezer</option>
                                        <option value="musnahkan">Musnahkan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-1 px-1">
                                <div class="form-group">
                                    <input type="number" name="returqty[]" class="form-control" max="" placeholder="Qty "
                                        value="" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-1 px-1">
                                <div class="form-group">
                                    <input type="number" name="returberat[]" class="form-control" step="0.01" max=""
                                        placeholder="Berat " value="" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-2 px-1">
                                <div class="form-group">
                                    <select name="alasan[]" data-placeholder="Pilih Alasan" data-width="100%" class="form-control alasan select2">
                                        <option value=""></option>
                                        @foreach ($alasan as $row)
                                        <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-1 px-1">
                                <div class="form-group">
                                    <select name="satuan[]" class="form-control">
                                        <option value="ekor"> Ekor</option>
                                        <option value="pcs"> Pcs</option>
                                        <option value="pack"> Pack</option>
                                        <option value="karung"> Karung</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-1 px-1 mt-2" id="gradeitem-0" style="display: none;">
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" name="gradeitem[]" value="grade b">
                                        <span class="status status-warning" style="font-size: 13px;"><b>Grade B</b></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-auto pl-1">
                                <i onclick="addRow()" class="fa fa-plus cursor text-primary pt-2 mt-1"></i>
                            </div>
                        </div>
                        {{-- <div class="form-group">
                            <div class="row">
                               
                            </div>
                        </div> --}}
                    </div>

                    <button type="submit" id="simpan" class="btn btn-primary btn-block btn-sm">Simpan</button>
                </form>
            </div>
        </div>

    </section>

    <script>

        // $('#simpan').on('click', function(){$(this).hide()});

        var x = 1;

        function addRow() {
            var row = '';
            row += '<div class="row-' + (x) + '">';
            row += '<div class="row">';
            row += ' <div class="col-3 pr-1">';
            row += ' <div class="form-group">';
            row += '<select name="item[]" class="form-control select2" data-placeholder="Pilih Item" data-width="100%" onchange="tujuan()" >';
            row += '<option value=""></option>';
            row += '@foreach ($item as $it)';
            row += '<option value="{{ $it->id }}" data-category="{{ $it->category_id }}">{{ $it->nama_alias }}</option>';
            row += '@endforeach';
            row += '</select>';
            row += '</div>';
            row += '</div>';
            row += '<div class="col-2 px-1">';
            row += '<div class="form-group">';
            row += '<select name="returto[]" class="form-control" id="tujuan_retur-'+x+'" onchange="tujuanRetur('+x+')">';
            row += '<option value="" disabled selected hidden>Pilih Tujuan</option>';
            row += '<option value="produksi">Reproses Produksi</option>';
            row += '<option value="chillerfg">Sampingan</option>';
            row += '<option value="gudang">Kembali Ke Freezer</option>';
            row += '<option value="musnahkan">Musnahkan</option>';
            row += '</select>';
            row += '</div>';
            row += '</div>';
            row += ' <div class="col-1 px-1">';
            row += ' <div class="form-group">';
            row +=
                ' <input type="number" name="returqty[]" class="form-control" min="" max="" placeholder="Qty"value="" autocomplete="off">';
            row += ' </div>';
            row += ' </div>';
            row += ' <div class="col-1 px-1">';
            row += ' <div class="form-group">';
            row +=
                ' <input type="number" name="returberat[]" class="form-control" step="0.01" min="" max="" placeholder="Berat" value="" autocomplete="off">';
            row += ' </div>';
            row += ' </div>';
            row += '<div class="col-2 px-1">' ;
            row += '    <div class="form-group">' ;
            row += '        <select name="alasan[]" data-placeholder="Pilih Alasan" data-width="100%" class="form-control alasan select2">' ;
            row += '            <option value=""></option>' ;
            row += '            @foreach ($alasan as $row)' ;
            row += '            <option value="{{ $row->id }}">{{ $row->nama }}</option>' ;
            row += '            @endforeach' ;
            row += '        </select>' ;
            row += '    </div>' ;
            row += '</div>' ;
            row += ' <div class="col-1 px-1">';
            row += '    <div class="form-group">';
            row += '        <select name="satuan[]" class="form-control">';
            row += '            <option value="ekor"> Ekor</option>';
            row += '            <option value="pcs"> Pcs</option>';
            row += '            <option value="pack"> Pack</option>';
            row += '            <option value="karung"> Karung</option>';
            row += '        </select>';
            row += '    </div>';
            row += ' </div>';
            row += ' <div class="col-1 px-1 mt-2" id="gradeitem-'+x+'" style="display:none;">';
            row += '    <div class="form-group">';
            row += '        <label>';
            row += '            <input type="checkbox" name="gradeitem[]" value="grade b">';
            row += '             <span class="status status-warning" style="font-size: 13px;"><b>Grade B</b></span> ';
            row += '        </label>';
            row += '    </div>';
            row += ' </div>';
            row += ' <div class="col-auto pl-1">';
            row += ' <i onclick="deleteRow(' + (x) + ')" class="fa fa-trash cursor text-danger pt-2 mt-1"></i>';
            row += ' </div>';
            row += '</div>';

            row += '</div>';

            $('.data-loop').append(row);
            $(function() {
                $('.select2').each(function() {
                    $(this).select2({
                        theme: 'bootstrap4',
                        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass(
                            'w-100') ? '100%' : 'style',
                        placeholder: $(this).data('placeholder'),
                        allowClear: Boolean($(this).data('allow-clear')),
                        closeOnSelect: !$(this).attr('multiple'),
                    });
                });
            });

            jQuery(function() {
                jQuery(document).trigger("enhance");
            });
            x++;
        }

        function pilihParent(event, parent) {
            parent.parent().parent().parent().find('.alasan').empty()
            let parents = parent.val()
            $.ajax({
                url: "{{ route('retur.nonso', ['key' => 'parent']) }}",
                type: "GET",
                data: {
                    parents
                },
                success: res => {
                    res.forEach(row => {
                        parent.parent().parent().parent().find('.alasan').append(
                            `<option value="${row.id}">${row.nama}</option>`
                        )
                    })
                }
            })
        }

        function deleteRow(rowid) {
            $('.row-' + rowid).remove();
        }

        tujuanRetur = (id) => {
            const val = document.getElementById('tujuan_retur-'+id).value;
            console.log(val);
            if (val === 'gudang') {
                $("#gradeitem-"+id).show();
            } else {
                $("#gradeitem-"+id).hide();
            }
        }
    </script>

@stop

@section('footer')
    <script>
        $('.select2').select2({
            theme: 'bootstrap4'
        });

        $("#datacustomer").load("{{ route('retur.customer') }}");
        $("#itemretur").load("{{ route('retur.itemretur') }}");


        function tujuan() {
            var category    =   $("#item_select").find(':selected').attr('data-category') ;

            if (category == 1 || category == 7) {
                $("#tujuan_retur option[value='chillerbb']").show();
                $("#tujuan_retur option[value='chillerfg']").hide();
            } else {
                $("#tujuan_retur option[value='chillerfg']").show();
                $("#tujuan_retur option[value='chillerbb']").hide();
            }
        }
    </script>
@endsection
