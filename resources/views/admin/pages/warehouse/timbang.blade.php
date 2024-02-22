@extends('admin.layout.template')

@section('title', 'Konfirmasi Data Keluar Warehouse')

@section('content')

    <div class="row mb-4">
        <div class="col">
            <a href="{{ route('thawingproses.index') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>Back</a>
        </div>
        <div class="col-7 py-1 text-center">
            <b>KONFIRMASI DATA KELUAR WAREHOUSE</b>
        </div>
        <div class="col"></div>
    </div>

    <section class="panel">
        <div class="card-body">
            <h6>Spesifikasi</h6>
            <div class="row">
                <div class="col-lg-3 col-6 mb-3">
                    <div class="small"><b>Packaging</b></div>
                    {{ $gudang->packaging ?? 'Tidak Ada' }}
                </div>
                <div class="col-lg-3 col-6 mb-3">
                    <div class="small"><b>Lokasi</b></div>
                    {{ $gudang->productgudang->code ?? '' }}
                </div>
                <div class="col-lg-3 col-6 mb-3">
                    <div class="small"><b>Sisa Qty</b></div>
                    {{ number_format($gudangambil->qty,0) ?? 'Tidak Ada' }}
                </div>
                <div class="col-lg-3 col-6 mb-3">
                    <div class="small"><b>Sisa Berat</b></div>
                    {{ number_format($gudangambil->berat,2) ?? 'Tidak Ada' }}
                </div>
            </div>
        </div>
    </section>

    <section class="panel">
        <div class="card-body">
            <form action="{{ route('warehouse.timbang', $gudang->id) }}" method="POST">
                @csrf
                <div class="form-group row">
                    <div class="col-sm-5 pr-sm-1">
                        <div class="row">
                            <div class="col-6 pr-1 pr-sm-3 col-sm-12">
                                <div class="form-group">
                                    <h6>Total Item</h6>
                                    <input type="text" name="result" class="form-control" id="result" placeholder="Tuliskan "
                                        value="{{ $gudang->qty }}" autocomplete="off">
                                    @error('result') <div class="small text-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-6 pl-1 pl-sm-3 col-sm-12">
                                <div class="form-group">
                                    <h6>Berat</h6>
                                    <input type="text" name="berat" class="form-control" id="berat" placeholder="Tuliskan " value="{{ $gudang->berat }}" autocomplete="off">
                                    @error("berat") <div class="small text-danger">{{ message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-sm-7 pl-sm-1">
                        <h6>Nama Item</h6>
                        <div class="form-group">

                            <input type="text" name="item" class="form-control" id="item" placeholder="Tuliskan "
                                value="{{ $gudang->nama ?? '' }}" autocomplete="off" readonly>
                            @error('item') <div class="small text-danger">{{ message }}</div> @enderror
                        </div>

                        <h6>Sub Item</h6>
                        <div class="form-group">
                            <input type="text" name="subitem" class="form-control" id="subitem" placeholder="Tuliskan "
                                value="{{ $gudang->sub_item ?? 'Free Stock' }}" autocomplete="off" readonly>
                            @error('subitem') <div class="small text-danger">{{ message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col">
                        <button type="submit" class="btn btn-primary btn-block">Simpan</button>
                    </div>
                    {{-- <div class="col">
                        <a href="" class="btn btn-info btn-block">Keluar</a>
                    </div> --}}
                </div>
            </form>
        </div>
    </section>

@stop
@section('footer')
    <script>
        //function that display value
        function dis(val) {
            document.getElementById("result").value += val
        }

        //function that evaluates the digit and return result
        function solve() {
            let x = document.getElementById("result").value
            let y = eval(x)
            document.getElementById("result").value = y
        }

        //function that clear the display
        function clr() {
            document.getElementById("result").value = ""
        }

        function clrberat() {
            document.getElementById("berat").value = ""
        }
    </script>
@endsection
