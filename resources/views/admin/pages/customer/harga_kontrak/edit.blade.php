@extends('admin.layout.template')

@section('title', 'Edit Harga Kontrak')

@section('footer')
<script>
    $(".select2").select2({
            theme: "bootstrap4"
        });
</script>

<script>
    $("#updatekontrak").on('click', function() {
            var item        =   $("#item").val() ;
            var harga       =   $("#harga").val() ;
            var unit        =   $("#unit").val() ;
            var qty         =   $("#qty").val() ;
            var mulai       =   $("#mulai").val() ;
            var akhir       =   $("#akhir").val() ;
            var keterangan  =   $("#keterangan").val() ;
            var customer    =   $("#customer").val() ;
            var id          =   $("#id").val() ;

            $("#updatekontrak").hide() ;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('hargakontrak.store') }}",
                method: "POST",
                data: {
                    key         :   'update',
                    customer    :   customer ,
                    item        :   item ,
                    harga       :   harga ,
                    unit        :   unit ,
                    qty         :   qty ,
                    mulai       :   mulai ,
                    akhir       :   akhir ,
                    keterangan  :   keterangan ,
                    id          :   id ,
                },
                success: function(data) {
                    if (data.status == 200) {
                        showNotif(data.msg) ;
                    } else {
                        showAlert(data.msg) ;
                    }
                    $("#updatekontrak").show() ;
                }
            });
        })
</script>

@endsection

@section('content')
<div class="col"><a href="{{ route('hargakontrak.index') }}#riwayat"><i class="fa fa-arrow-left"></i>
        Kembali</a></div>
<div class="my-4 text-center font-weight-bold text-uppercase">Edit Harga Kontrak</div>

<section class="panel">
    <div class="card-body">
        <div class="tab-content">
            <input type="hidden" name="key" id="update" value="update">
            <input type="hidden" name="id" id="id" value="{{ $data->id }}">
            <div class="form-group">
                <label for="customer">Customer</label>
                <div id="customer-loop">
                    <div class="row">
                        <div class="col pr-1">
                            <select name="customer" id="customer" data-width="100%" data-placeholder="Data Customer"
                                class="customer form-control select2">
                                <option value=""></option>
                                @foreach ($customer as $row)
                                <option value="{{ $row->id }}" {{ $data->customer_id == $row->id ? 'selected' : '' }}>{{ $row->kode }}. {{ $row->nama }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="item">Item</label>
                <select name="item" id="item" data-width="100%" data-placeholder="Pilih Item"
                    class="form-control select2">
                    <option value=""></option>
                    @foreach ($item as $row)
                    <option value="{{ $row->id }}" {{ $data->item_id == $row->id ? ' selected="selected"' : '' }}>{{ $row->sku }}. {{ $row->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="row">
                <div class="col pr-1">
                    <div class="form-group">
                        <label for="harga">Harga</label>
                        <input type="number" name="harga" value="{{ $data->harga }}" id="harga"
                            placeholder="Tuliskan Harga" min="0" class="form-control">
                    </div>
                </div>

                <div class="col px-1">
                    <label for="unit">Unit</label>
                    <select name="unit" id="unit" data-width="100%" data-placeholder="Pilih Unit"
                        class="form-control select2">
                        <option value=""></option>
                        <option value="Ekor" {{ $data->unit == 'Ekor' ? 'selected' : '' }}>Ekor</option>
                        <option value="Kg" {{ $data->unit == 'Kg' ? 'selected' : '' }}>Kg</option>
                    </select>
                </div>

                <div class="col pl-1">
                    <div class="form-group">
                        <label for="qty">Min. Qty</label>
                        <input type="number" value="{{ $data->min_qty }}" name="qty" id="qty"
                            placeholder="Tuliskan Min. Qty" min="0" class="form-control">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col pr-1">
                    <div class="form-group">
                        <label for="mulai">Tanggal Mulai</label>
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif name="mulai" id="mulai" value="{{ $data->mulai }}"
                            class="form-control">
                    </div>
                </div>

                <div class="col pl-1">
                    <div class="form-group">
                        <label for="akhir">Tanggal Berakhir</label>
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif name="akhir" id="akhir" class="form-control" value={{ $data->sampai}}>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea id="keterangan" rows="3" class="form-control">{{ $data->keterangan ?? '' }}</textarea>
            </div>
            <button id="updatekontrak" class="btn btn-block btn-primary">Submit</button>
        </div>
    </div>
</section>
@endsection