@extends('admin.layout.template')

@section('title', 'Purchase PO LB')

@section('footer')
<script>
    $('.select2').select2({
        theme: 'bootstrap4',
    })

</script>
@endsection

@section('content')
<div class="row my-4">
    <div class="col"><a href="{{ route('pembelian.purchase') }}"><i class="fa fa-arrow-left"></i> Kembali</a>
    </div>
    <div class="col-8 font-weight-bold text-uppercase text-center">Edit Purchase Pembelian Barang</div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data"
            action="{{route('pembelian.purchasestore', ['key' => 'updatePOLB'])}}">
            @csrf
            <input type="hidden" value="{{ $data->id }}" name="idEditPOLB">
            <div class="row">
                <div class="col">
                    <div class="form-group">
                       <label for="supplier">Supplier</label>
                        <select id="supplier" name="supplier" class="form-control select2"
                            data-placeholder="Pilih Supplier" data-width="100%" required>
                            <option value=""></option>
                            @foreach ($supplier as $row)
                            <option value="{{ $row->id }}" {{ $row->id == $data->supplier_id ? 'selected' : '' }}>
                                {{ $row->nama }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col pr-1">
                            <div class="form-group">
                                <label for="type_po">Type PO</label>
                                <select id="type_po" class="form-control" name="type_po" data-placeholder="Pilih Form"
                                    data-width="100%" required>
                                    <option value="PO LB" {{ $data->type_po == 'PO LB' ? 'selected' : '' }}>PO LB
                                    </option>
                                    <option value="PO Transit" {{ $data->type_po == 'PO Transit' ? 'selected' : '' }}>PO
                                        Transit</option>
                                </select>
                            </div>
                        </div>
                        <div class="col pl-1">
                            <div class="form-group">
                                <label for="form_id">Form PO</label>
                                <select id="form_id" class="form-control" name="form_id" data-placeholder="Pilih Form"
                                    data-width="100%" required readonly>
                                    <option value="{{ Session::get('subsidiary') == 'CGL' ? '131' : '156' }}">{{ Session::get('subsidiary') }} - Form Purchase Order Ayam</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col pr-1">
                            <div class="form-group">
                                <label for="tanggal">Tanggal PO</label>
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif id="tanggal" name="tanggal" class="form-control"
                                    value="{{ $data->tanggal ?? date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col pl-1">
                            <div class="form-group">
                                <label for="tanggal_kirim">Tanggal Kirim</label>
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif id="tanggal_kirim" name="tanggal_kirim" class="form-control"
                                    value="{{ $data->tanggal_kirim ?? date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col pr-1">
                            <div class="form-group">
                                <label for="jenis_ekspedisi">Jenis Ekspedisi</label>
                                <select id="jenis_ekspedisi" class="form-control" name="jenis_ekspedisi"
                                    data-placeholder="Pilih Form" data-width="100%" required>
                                    <option value="Tangkap" {{ $data->jenis_ekspedisi == 'Tangkap' ? 'selected' : ''
                                        }}>Tangkap
                                    </option>
                                    <option value="Kirim" {{ $data->jenis_ekspedisi == 'Kirim' ? 'selected' : ''
                                        }}>Kirim
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="link_url">Link File</label>
                        <textarea id="link_url" type="text" class="form-control" name="url_link" value=""
                            placeholder="https://drive.google.com/diasuhdkahs991823ku2hiuh/123i123hu98/1293">{{ $data->link_url }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="memo">Memo</label>
                        <textarea id="keterangan" name="memo" rows="2" placeholder="Tuliskan Memo (opsional)"
                            class="form-control">{{ $data->memo }}</textarea>
                    </div>


                </div>
                @foreach ($list as $row)
                <div class="col">
                    <input type="hidden" name="idListPOLB" value="{{ $row->id }}">
                        <div class="form-group">
                            <label for="items">Item</label>
                            <select name="item" class="form-control select2" data-width="100%" data-placeholder="Pilih Item" id="items">
                                <option value=""></option>
                                @foreach ($items as $item)
                                <option value="{{ $item->id }}" {{ ($row->item_id == $item->id) ? 'selected' : '' }}>
                                    {{ $item->nama }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    <div class="row mt-2">
                        <div class="col pr-1">
                            <div class="form-group">
                                <label>Harga</label>
                                <div class="input-group">
                                    <input type="text" id="berat" class="form-control rounded-0 p-1 input-amount"
                                        autocomplete="off" placeholder="Harga"
                                        value="{{ number_format($row->harga, 0, ',', '.') }}" name="harga" required>
                                </div>
                            </div>
                        </div>
                        <div class="col pl-1">
                            <div class="form-group">
                                <label>Harga Cetakan</label>
                                <div class="input-group">
                                    <select class="form-control" name="unit_cetakan">
                                        <option value="1" {{ $row->unit_cetakan == '1' ? 'selected' : '' }}> Kg
                                        </option>
                                        <option value="2" {{ $row->unit_cetakan == '2' ? 'selected' : '' }}>
                                            Ekor/Pcs/Pack </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col pr-1">
                            <div class="form-group">
                                <label>Jumlah DO</label>
                                <div class="input-group">
                                    <input type="number" id="jumlah_do" class="form-control rounded-0 p-1"
                                        autocomplete="off" min="1" placeholder="DO" value="{{ $row->jumlah_do }}"
                                        name="jumlah_do">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">Rit/Mobil</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col pl-1">
                            <div class="form-group">
                                <label>Ukuran Ayam</label>
                                <div class="input-group">
                                    <select class="form-control" id="ukuran_ayam" name="ukuran_ayam">
                                        <option value="1" {{ $row->ukuran_ayam == 1 ? 'selected' : '' }}> < 1.1
                                                </option>
                                        <option value="2" {{ $row->ukuran_ayam == 2 ? 'selected' : '' }}> 1.1-1.3
                                        </option>
                                        <option value="3" {{ $row->ukuran_ayam == 3 ? 'selected' : '' }}> 1.2-1.4
                                        </option>
                                        <option value="4" {{ $row->ukuran_ayam == 4 ? 'selected' : '' }}> 1.3-1.5
                                        </option>
                                        <option value="5" {{ $row->ukuran_ayam == 5 ? 'selected' : '' }}> 1.4-1.6
                                        </option>
                                        <option value="6" {{ $row->ukuran_ayam == 6 ? 'selected' : '' }}> 1.5-1.7
                                        </option>
                                        <option value="7" {{ $row->ukuran_ayam == 7 ? 'selected' : '' }}> 1.6-1.8
                                        </option>
                                        <option value="8" {{ $row->ukuran_ayam == 8 ? 'selected' : '' }}> 1.7-1.9
                                        </option>
                                        <option value="9" {{ $row->ukuran_ayam == 9 ? 'selected' : '' }}> 1.8-2.0
                                        </option>
                                        <option value="10" {{ $row->ukuran_ayam == 10 ? 'selected' : '' }}> 1.9-2.1
                                        </option>
                                        {{-- <option value="11" {{ $row->ukuran_ayam == 11 ? 'selected' : '' }}> 2.0-2.2
                                        </option>
                                        <option value="12" {{ $row->ukuran_ayam == 12 ? 'selected' : '' }}> 2.2 Up
                                        </option> --}}
                                        {{-- <option value="13" {{ $row->ukuran_ayam == 13 ? 'selected' : '' }}> 1.2-1.5
                                        </option> --}}
                                        <option value="15" {{ $row->ukuran_ayam == 15 ? 'selected' : '' }}> 1.3-1.6
                                        </option>
                                        <option value="16" {{ $row->ukuran_ayam == 16 ? 'selected' : '' }}> 1.4-1.7
                                        </option>
                                        <option value="17" {{ $row->ukuran_ayam == 17 ? 'selected' : '' }}> 1.5-1.8
                                        </option>
                                        <option value="18" {{ $row->ukuran_ayam == 18 ? 'selected' : '' }}> 2.0-2.5
                                        </option>
                                        <option value="19" {{ $row->ukuran_ayam == 19 ? 'selected' : '' }}> 2.5-3.0
                                        </option>
                                        <option value="20" {{ $row->ukuran_ayam == 20 ? 'selected' : '' }}> 3.0 Up
                                        </option>
                                        <option value="21" {{ $row->ukuran_ayam == 21 ? 'selected' : '' }}> 4.0 Up
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col pr-1">
                            <div class="form-group">
                                <label>Ekor DO</label>
                                <div class="input-group">
                                    <input type="number" id="ekor" class="form-control rounded-0 p-1" autocomplete="off"
                                        min="1" placeholder="Tulis Ekor" value="{{ $row->qty }}" name="qty">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">Ekor/Pcs/Pack</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col pl-1">
                            <div class="form-group">
                                <label>Berat DO</label>
                                <div class="input-group">
                                    <input type="number" id="beratdo" class="form-control rounded-0 p-1"
                                        autocomplete="off" min="1" placeholder="Tulis Berat" value="{{ $row->berat }}"
                                        name="berat">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">Kg</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                @endforeach
            </div>
            <button type="submit" class="btn btn-green btn-block"
                onclick="return confirm('Submit PO? pastikan data sudah benar')">UPDATE PO</button>
        </form>
    </div>
</section>


<script>
    var jumlah_do   = 0;
    var ukuran_ayam = 5;
    var ekor_do     = 0;
    var berat_do    = 0;
    $('#jumlah_do').on('keyup', function(){
        jumlah_do = $(this).val();
        recalculateDO()
    })
    $('#ukuran_ayam').on('change', function(){
        ukuran_ayam = $(this).val();
        recalculateDO()
    })

    function recalculateDO(){

        berat_do    = 0;
        ekor_do     = 0;

         if(ukuran_ayam=="1"){
            ekor_do  = 2400;
            berat_do = 3500;
        }
        if(ukuran_ayam=="2" || ukuran_ayam=="3" || ukuran_ayam=="4" || ukuran_ayam=="13" || ukuran_ayam=="14"){
            ekor_do  = 2300;
            berat_do = 3500;
        }
        if(ukuran_ayam=="5" || ukuran_ayam=="6" || ukuran_ayam=="16" || ukuran_ayam=="15" || ukuran_ayam=="17"){
            ekor_do  = 2200;
            berat_do = 4000;
        }
        if(ukuran_ayam=="7" || ukuran_ayam=="8"){
            ekor_do  = 2000;
            berat_do = 4000;
        }
        if(ukuran_ayam=="10"){
            ekor_do  = 2000;
            berat_do = 4000;
        }
        if(ukuran_ayam=="9" || ukuran_ayam=="11" || ukuran_ayam=="12"){
            ekor_do  = 1700;
            berat_do = 4000;
        }

        $('#ekor').val(jumlah_do*ekor_do);
        $('#beratdo').val(berat_do*jumlah_do);
        console.log(berat_do)

    }

    function inputRupiahPOLB(e) {
            $('.input-amountPOLB-' + e).val(formatAmount($('.input-amountPOLB-' + e).val()));
        }
</script>

@endsection