@extends('admin.layout.template')

@section('title', 'Retur Authorization')

@section('content')
@php
$items = \App\Models\Item::whereIn('category_id', [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20])->get();
@endphp

    <div class="row mb-4">
        <div class="col">
            <a href="{{ route('retur.index') }}#custom-tabs-three-summary" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i> Back</a>
        </div>
        <div class="col-7 col text-center py-2">
            <b class="text-uppercase">RETUR DO</b>
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
                <form method="post" action="{{ route('retur.returdosubmit') }}" class="retur" id="retur">
                {{-- <form> --}}

                    <div class="row">
                        <div class="col">
                            <div class="small">Tanggal Kirim</div>
                            {{ $data->tanggal_kirim }}
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <div class="small">Nama Customer</div>
                                {{ $data->nama }}
                            </div>
                        </div>
                        <div class="col">
                            <div class="small">No SO</div>
                            {{ $data->no_so }}
                        </div>
                        <div class="col">
                            <div class="small">No DO</div>
                            {{ $data->no_do }}
                        </div>
                        <div class="col">
                            <div class="small">Alamat</div>
                            {{ $data->alamat }}
                        </div>
                        <div class="col">
                            <div class="small">Telepon</div>
                            {{ $data->telp }}
                        </div>
                    </div>

                    @csrf
                    <input type="hidden" name="order_id" value="{{ $data->id }}">

                    <div class="data-loop">
                        <section class="panel">
                            <div class="card-body">
                            <div class="row">
                                <div class="col-3 pr-1">
                                    <select name="item[]" class="form-control select2 item" data-placeholder="Pilih Item" data-width="100%" onchange="tujuan()">
                                        <option value=""></option>
                                        @foreach ($orderitem as $val)
                                            @php
                                                $item = \App\Models\Item::where('id', $val->item_id)->get();
                                            @endphp
                                            @foreach ($item as $it)
                                                <option value="{{ $val->id }}" data-category="{{ $it->category_id }}">  
                                                    {{ $it->nama }} 
                                                    @if($val->part) || PART: {{$val->part}} @endif 
                                                    @if($val->bumbu) || BUMBU: {{$val->bumbu}} @endif 
                                                    @if($val->memo) || MEMO: {{$val->memo}} @endif 
                                                    ({{ $val->qty }} Pcs || {{ $val->berat }} Kg)</option>
                                            @endforeach
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-2 px-1">
                                    <div class="form-group">
                                        <select name="returto[]" class="form-control tujuan" id='tujuan_retur-0' onchange="tujuanRetur(0)">
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
                                        <select name="alasan[]" data-placeholder="Pilih Alasan" data-width="100%" id="pilihAlasan-0" class="form-control select2 alasan" onchange="pilihAlasan(0)">
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
                                <div class="col-1 px-1 mt-2" id="input-grade-0" style="display: none;">
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" name="gradeitem[]" value="grade b" id="gradeitem-0"> 
                                            <span class="status status-warning" style="font-size: 12px;"><b>Grade B</b></span> 
                                        </label>
                                    </div>
                                </div>
                                <div class="col-auto pl-1">
                                    <i onclick="addRow()" class="fa fa-plus cursor text-primary pt-2 mt-1"></i>
                                </div>
                            </div>
                            <div class="row" id="tukarAtauTidak-0" style="display: none">
                                <div class="col-7 pr-1">
                                    <label for="item">Apakah ingin tukar item?</label> * <span class="text-danger">(pilih salah satu)</span><br>
                                    <label class="mt-2 px-2 pt-2 rounded status-info">
                                        <input id="yaTukar-0" data-ke="0" type="checkbox" class="pilihTukar0" name="pilihTukar0" onclick="checkTukar('0', 'ya');"> <label for="yaTukar-0">Ya</label>
                                    </label>
                                    <label class="mt-2 px-2 pt-2 rounded status-danger">
                                        <input id="tidakTukar-0" data-ke="0" type="checkbox" class="pilihTukar0" name="pilihTukar0" onclick="checkTukar('0', 'tidak');"> <label for="tidakTukar-0">Tidak</label>
                                    </label>
                                </div>
                                <div class="col-3 pr-1 px-auto" id="untukItemTukar-0" style="display: none">
                                    <div class="form-group">
                                        <label for="">Item Yang Ingin Ditukar</label>
                                        <select name="itemTukar[]" id="itemTukar-0" class="form-control select2" data-placeholder="Pilih Item" data-width="100%">
                                            <option value=""></option>

                                                @foreach ($items as $item)
                                                    <option value="{{ $item->id }}" data-category="{{ $item->category_id }}">  
                                                        {{ $item->nama }} </option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </section>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block btn-sm" id="btnSimpanRetur">Simpan</button>
                </form>
            </div>
        </div>

    </section>

    <script>
        $('#simpan').on('click', function() {
            $(this).hide()
        });

        var x = 1;

        function addRow() {
            var row = '';
            row += '<section class="panel panel-' + (x) + '">';
            row += '<div class="card-body">';
            row += '<div class="row">';
            row += '<div class="col-3 pr-1">';
            row += '<div class="form-group">';
            row += '<select name="item[]" class="form-control select2 item" data-placeholder="Pilih Item" onchange="tujuan()">';
            row += '<option value=""></option>';
            row += '@foreach ($orderitem as $val)';
            row += '@php $item = \App\Models\Item::where("id", $val->item_id)->get(); @endphp';
            row += '@foreach ($item as $it)';
            row += '<option value="{{ $val->id }}">{{ $it->nama }}';
            row += '@if($val->part) || PART: {{$val->part}} @endif';
            row += '@if($val->bumbu) || BUMBU: {{$val->bumbu}} @endif';
            row += '@if($val->memo) || MEMO: {{$val->memo}} @endif ';
            row += '({{ $val->qty }} Pcs || {{ $val->berat }} Kg)</option>';
            row += '@endforeach';
            row += '@endforeach';
            row += '</select>';
            row += '</div>';
            row += '</div>';
            row += '<div class="col-2 px-1">';
            row += '<div class="form-group">';
            row += '<select name="returto[]" class="form-control tujuan" id="tujuan_retur-'+x+'" onchange="tujuanRetur('+x+')"> ';
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
                ' <input type="number" name="returqty[]" class="form-control" max="" placeholder="Qty"value="" autocomplete="off">';
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
            row += '        <select name="alasan[]" data-placeholder="Pilih Alasan" id="pilihAlasan-'+x+'" data-width="100%" class="form-control select2 alasan" onchange="pilihAlasan('+x+')">' ;
            row += '            <option value=""></option>' ;
            row += '            @foreach ($alasan as $row)' ;
            row += '            <option value="{{ $row->id }}">{{ $row->nama }}</option>' ;
            row += '            @endforeach' ;
            row += '        </select>' ;
            row += '    </div>' ;
            row += '</div>' ;
            row += ' <div class="col-1 px-1">';
            row += ' <div class="form-group">';
            row += ' <select name="satuan[]" class="form-control" id="tujuan_retur">';
            row += '<option value="ekor"> Ekor</option>';
            row += '<option value="pack"> Pack</option>';
            row += '<option value="pcs"> Pcs</option>';
            row += '<option value="karung"> Karung</option>';
            row += '</select>';
            row += ' </div>';
            row += ' </div>';
            row += ' <div class="col-1 px-1 mt-2" id="input-grade-'+x+'" style="display: none;">';
            row += '    <div class="form-group">';
            row += '        <label><input type="checkbox" name="gradeitem[]" value="grade b" id="gradeitem-'+x+'"> <span class="status status-warning" style="font-size: 12px;"><b>Grade B</b></span> </label>';
            row += '    </div>';
            row += ' </div>';
            row += ' <div class="col-auto pl-1">';
            row += ' <i onclick="deleteRow(' + (x) + ')" class="fa fa-trash cursor text-danger pt-2 mt-1"></i>';
            row += ' </div>';
            row += '</div>';
            row += '<div class="row" id="tukarAtauTidak-'+x+'" style="display: none">';
            row += ' <div class="col-4 pr-1">';
            row += '    <label for="item">Apakah ingin tukar item?</label> * <span class="text-danger">(pilih salah satu)</span><br>';
            row += '        <label class="mt-2 px-2 pt-2 rounded status-info">';
            row += '           <input id="yaTukar-'+x+'" data-ke="'+x+'" type="checkbox" name="pilihTukar'+x+'" onclick="checkTukar('+"'"+x+"'"+','+"'ya'"+');"> <label for="yaTukar-'+x+'">Ya</label>';
            row += '        </label>';
            row += '        <label class="mt-2 px-2 pt-2 rounded status-danger">';
            row += '           <input id="tidakTukar-'+x+'" data-ke="'+x+'" type="checkbox" name="pilihTukar'+x+'" onclick="checkTukar('+"'"+x+"'"+','+"'tidak'"+');"> <label for="tidakTukar-'+x+'">Tidak</label>';
            row += '        </label>';
            row += '</div>';
            row += '        <div id="untukItemTukar-'+x+'" class="col-3 pr-1 px-auto" style="display: none">';
            row += '     <div class="form-group">';
            row += '     <label for="">Item Yang Ingin Ditukar</label>';
            row += '             <select name="itemTukar[]" class="form-control select2" data-placeholder="Pilih Item" data-width="100%">';
            row += '              <option value=""></option>';
            row += '             @foreach ($items as $item)';
            row += '              <option value="{{ $item->id }}" data-category="{{ $item->category_id }}">';
            row += '              {{ $item->nama }} </option>';
            row += '             @endforeach';
            row += '           </select>';
            row += '           </div>';
            row += '        </div>';
            row += '    </div>';
            row += '  </div>';
            row += '</section>';

            $('.data-loop').append(row);
            $('.select2').select2({theme: 'bootstrap4'});

            jQuery(function() {
                jQuery(document).trigger("enhance");
            });
            x++;
        }

        function deleteRow(rowid) {
            $('.panel-' + rowid).remove();
            x--;
        }
    </script>

    <script>
        pilihAlasan = (id) => {
            const val = document.getElementById('pilihAlasan-'+id).value;
            const fieldTukar =  document.getElementById('tukarAtauTidak-'+id);

            if(val == '29' || val == '28'){
                fieldTukar.style.display = 'block';
                checkTukar = (idTukar, decision) => {
                    const boxYa     =  document.getElementById('yaTukar-'+idTukar);
                    const boxTidak  =  document.getElementById('tidakTukar-'+idTukar);
                    const itemTukar =  document.getElementById('untukItemTukar-'+idTukar);

                    boxYa.addEventListener('change', () => {
                        boxTidak.checked = false;
                        boxYa.checked = true;
                        itemTukar.style.display = 'block';
                    })
                    boxTidak.addEventListener('change', () => {
                        boxYa.checked = false;
                        boxTidak.checked = true;
                        itemTukar.style.display = 'none';
                    })
                }
            } else {
                fieldTukar.style.display = 'none';
            }
        }

        const btnRetur = document.getElementById('btnSimpanRetur');
        btnRetur.addEventListener('click', (e) => {
            e.preventDefault();
            // FLow:
            // 1. Cek jumlah datanya (clear)
            // 2. Cek item mana aja yang value 29 (clear)
            // 3. jika terdapat, cek apakah checkbox nya di klik atau tidak (clear)
            // 4. jika diklik, cek itemnya apakah sudah diisi atau belum.

            for(let i = 0; i < x; i++){
                let cekItemTukar    = document.querySelector('input[name = "pilihTukar'+i+'"]:checked');
                let alasan          = document.getElementById('pilihAlasan-'+i).value
                let itemTukar       = document.getElementById('itemTukar-'+i);
                
                // Cek value yang 29
                if(alasan == '29' || alasan =='28'){
                    // Cek apakah dia ada di klik atau tidak.
                    if(cekItemTukar == null){
                        showAlert('Terdapat pilihan yang belum dipilih!')
                        return false;
                    }
                }
                
            }

            const result = confirm('Yakin submit retur?')

            if( result == true ){
                let item          =  document.getElementsByClassName('item')
                let items         =  [];

                let tujuan        =  document.getElementsByClassName('tujuan')
                let tujuans       =  [];

                let alasan        =  document.getElementsByClassName('alasan')
                let alasans       =  [];
                

                for(let a = 0; a < x; ++a) {
                    items.push(item[a].value);
                    if(item[a].value === "") showAlert('Terdapat item yang belum dipilih')

                    tujuans.push(tujuan[a].value);
                    if(tujuan[a].value === "") showAlert('Terdapat tujuan yang belum dipilih')

                    alasans.push(alasan[a].value);
                    if(alasan[a].value === "") showAlert('Terdapat alasan yang belum dipilih')
                }

                const checkItems  = items.includes(''); // true
                const checkTujuan = tujuans.includes(''); // true
                const checkAlasan = alasans.includes(''); // true

                console.log(checkItems, checkTujuan, checkAlasan)
                if(checkItems === false && checkTujuan === false && checkAlasan === false) {
                    document.getElementById('retur').submit();
                } else {
                    showAlert('Terdapat field yang belum diisi');
                    return false;
                }
                
            } else {
                return false;
            }

        });

        tujuanRetur = (id) => {
            const val = document.getElementById('tujuan_retur-'+id).value;
            console.log(val);
            if (val === 'gudang') {
                $("#input-grade-"+id).show();
            } else {
                $("#input-grade-"+id).hide();
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
            var category = $("#item_select").find(':selected').attr('data-category');

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
