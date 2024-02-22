@extends('admin.layout.template')

@section('title', 'Chiller')

@section('content')

    <div class="row mb-4">
        <div class="col">
            <a href="{{ route('chiller.index') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
                Back</a>
        </div>
        <div class="col py-2 text-center">
            <b>TUKAR ITEM</b>
        </div>
        <div class="col"></div>
    </div>

    <section class="panel">
        <div class="card-body">
            <form action="{{ route('chiller.storetukar', $data->id) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="">Item</label>
                            <input type="hidden" name="iditemlama" value="{{ $data->item_id }}">
                            <input type="text" name="itemlama" class="form-control" value="{{ $data->item_name }}"
                                readonly>
                        </div>
                        <div class="form-group">
                            <label for="">Berat</label>
                            <input type="text" name="beratlama" class="form-control" value="{{ $data->stock_berat }}"
                                readonly>
                        </div>
                        <div class="form-group">
                            <label for="">Qty</label>
                            <input type="text" name="qtylama" class="form-control" value="{{ $data->stock_item }}"
                                readonly>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="">Item</label>
                            <select name="itembaru" id="itembaru" class="form-control select2" required>
                                <option value="">Pilih Item</option>
                                @foreach ($item as $it)
                                    <option value="{{ $it->id }}">{{ $it->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Berat</label>
                            <input type="text" name="beratbaru" class="form-control" value="">
                        </div>
                        <div class="form-group">
                            <label for="">Qty</label>
                            <input type="text" name="qtybaru" class="form-control" value="">
                        </div>
                    </div>

                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Simpan</button>
                </div>
            </form>
        </div>
    </section>

    <script>
        $('.select2').select2({
            theme: 'bootstrap4'
        });
    </script>
@stop

@section('js')

@endsection
