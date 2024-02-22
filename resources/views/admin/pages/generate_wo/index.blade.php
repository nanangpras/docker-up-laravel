@extends('admin.layout.template')

@section('title', 'Generate WO-WOB')

@section('footer')
<script>
    $('.select2').select2({
        theme: 'bootstrap4'
    })
</script>

<script>
    var x = 1;
function addComponent(){
    var row = '';
    row +=  '<div class="row-'+(x)+' row">' ;
    row +=  '    <div class="col-9 pr-1">' ;
    row +=  '        <div class="form-group">' ;
    row +=  '            Item' ;
    row +=  '            <select name="item_component[]" data-width="100%" data-placeholder="Pilih Item" class="form-control select2">' ;
    row +=  '                <option value=""></option>' ;
    row +=  '                @foreach ($item as $row)' ;
    row +=  '                <option value="{{ $row->id }}">{{ $row->nama }}</option>' ;
    row +=  '                @endforeach' ;
    row +=  '            </select>' ;
    row +=  '        </div>' ;
    row +=  '    </div>' ;

    row +=  '    <div class="col-2 px-1">' ;
    row +=  '        <div class="form-group">' ;
    row +=  '            Qty' ;
    row +=  '            <input type="number" name="qty_component[]" class="form-control" step="0.01" min="0" placeholder="Tulis Qty">' ;
    row +=  '        </div>' ;
    row +=  '    </div>' ;

    row +=  '    <div class="col-1 text-center mt-2">' ;
    row +=  '        &nbsp;' ;
    row +=  '        <h6 onclick="deleteComponent('+(x)+')" class="mb-0 cursor"><i class="fa fa-trash text-danger"></i></h6>' ;
    row +=  '    </div>' ;
    row +=  '</div>' ;

    $('.data-component').append(row);
    $('.select2').select2({
        theme: 'bootstrap4'
    })
    x++;
}

function deleteComponent(rowid){
    $('.row-'+rowid).remove();
}
</script>

<script>
    var x = 1;
function addFg(){
    var row = '';
    row +=  '<div class="row-'+(x)+' row">' ;
    row +=  '    <div class="col-9 pr-1">' ;
    row +=  '        <div class="form-group">' ;
    row +=  '            Item' ;
    row +=  '            <select name="item_fg[]" data-width="100%" data-placeholder="Pilih Item" class="form-control select2">' ;
    row +=  '                <option value=""></option>' ;
    row +=  '                @foreach ($item as $row)' ;
    row +=  '                <option value="{{ $row->id }}">{{ $row->nama }}</option>' ;
    row +=  '                @endforeach' ;
    row +=  '            </select>' ;
    row +=  '        </div>' ;
    row +=  '    </div>' ;

    row +=  '    <div class="col-2 px-1">' ;
    row +=  '        <div class="form-group">' ;
    row +=  '            Qty' ;
    row +=  '            <input type="number" name="qty_fg[]" class="form-control" step="0.01" min="0" placeholder="Tulis Qty">' ;
    row +=  '        </div>' ;
    row +=  '    </div>' ;

    row +=  '    <div class="col-1 text-center mt-2">' ;
    row +=  '        &nbsp;' ;
    row +=  '        <h6 onclick="deleteFg('+(x)+')" class="mb-0 cursor"><i class="fa fa-trash text-danger"></i></h6>' ;
    row +=  '    </div>' ;
    row +=  '</div>' ;

    $('.data-fg').append(row);
    $('.select2').select2({
        theme: 'bootstrap4'
    })
    x++;
}

function deleteFg(rowid){
    $('.row-'+rowid).remove();
}
</script>

<script>
    var x = 1;
function addProduct(){
    var row = '';
    row +=  '<div class="row-'+(x)+' row">' ;
    row +=  '    <div class="col-9 pr-1">' ;
    row +=  '        <div class="form-group">' ;
    row +=  '            Item' ;
    row +=  '            <select name="item_product[]" data-width="100%" data-placeholder="Pilih Item" class="form-control select2">' ;
    row +=  '                <option value=""></option>' ;
    row +=  '                @foreach ($item as $row)' ;
    row +=  '                <option value="{{ $row->id }}">{{ $row->nama }}</option>' ;
    row +=  '                @endforeach' ;
    row +=  '            </select>' ;
    row +=  '        </div>' ;
    row +=  '    </div>' ;

    row +=  '    <div class="col-2 px-1">' ;
    row +=  '        <div class="form-group">' ;
    row +=  '            Qty' ;
    row +=  '            <input type="number" name="qty_product[]" class="form-control" step="0.01" min="0" placeholder="Tulis Qty">' ;
    row +=  '        </div>' ;
    row +=  '    </div>' ;

    row +=  '    <div class="col-1 text-center mt-2">' ;
    row +=  '        &nbsp;' ;
    row +=  '        <h6 onclick="deleteProduct('+(x)+')" class="mb-0 cursor"><i class="fa fa-trash text-danger"></i></h6>' ;
    row +=  '    </div>' ;
    row +=  '</div>' ;

    $('.data-product').append(row);
    $('.select2').select2({
        theme: 'bootstrap4'
    })
    x++;
}

function deleteProduct(rowid){
    $('.row-'+rowid).remove();
}
</script>
@endsection

@section('content')
<div class="mb-4 font-weight-bold text-uppercase text-center">GENERATE WO-WOB</div>

<form action="{{ route('generatewowob.store') }}" method="post">
    @csrf
    <div class="row">
        <div class="col-md-8">
            <section class="panel">
                <div class="card-header font-weight-bold">
                    <div class="float-right cursor" onclick="addComponent()"><i class="fa fa-plus"></i> Tambah</div>
                    Component
                </div>
                <div class="card-body">
                    <div class="data-component">
                        <div class="row">
                            <div class="col-9 pr-1">
                                <div class="form-group">
                                    Item
                                    <select name="item_component[]" data-width="100%" data-placeholder="Pilih Item"
                                        class="form-control select2">
                                        <option value=""></option>
                                        @foreach ($item as $row)
                                        <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-2 px-1">
                                <div class="form-group">
                                    Qty
                                    <input type="number" name="qty_component[]" class="form-control" step="0.01" min="0"
                                        placeholder="Tulis Qty">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="panel">
                <div class="card-header font-weight-bold">
                    <div class="float-right cursor" onclick="addFg()"><i class="fa fa-plus"></i> Tambah</div>
                    Finished Goods
                </div>
                <div class="card-body">
                    <div class="data-fg">
                        <div class="row">
                            <div class="col-9 pr-1">
                                <div class="form-group">
                                    Item
                                    <select name="item_fg[]" data-width="100%" data-placeholder="Pilih Item"
                                        class="form-control select2">
                                        <option value=""></option>
                                        @foreach ($item as $row)
                                        <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-2 px-1">
                                <div class="form-group">
                                    Qty
                                    <input type="number" name="qty_fg[]" class="form-control" step="0.01" min="0"
                                        placeholder="Tulis Qty">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="panel">
                <div class="card-header font-weight-bold">
                    <div class="float-right cursor" onclick="addProduct()"><i class="fa fa-plus"></i> Tambah</div>
                    By Product
                </div>
                <div class="card-body">
                    <div class="data-product">
                        <div class="row">
                            <div class="col-9 pr-1">
                                <div class="form-group">
                                    Item
                                    <select name="item_product[]" data-width="100%" data-placeholder="Pilih Item"
                                        class="form-control select2">
                                        <option value=""></option>
                                        @foreach ($item as $row)
                                        <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-2 px-1">
                                <div class="form-group">
                                    Qty
                                    <input type="number" name="qty_product[]" class="form-control" step="0.01" min="0"
                                        placeholder="Tulis Qty">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="col-md-4">
            <section class="panel">
                <div class="card-body">
                    <div class="form-group">
                        Work Order
                        <select name="wo" data-width="100%" data-placeholder="Pilih WO" class="form-control select2"
                            required>
                            <option value=""></option>
                            <option value="wo-1">WO 1</option>
                            <option value="wo-2">WO 2</option>
                            <option value="wo-3">WO 3</option>
                            <option value="wo-4">WO 4</option>
                            <option value="wo-5">WO 5</option>
                            <option value="wo-6">WO 6</option>
                            <option value="wo-7">WO 7</option>
                        </select>
                    </div>

                    <div class="form-group">
                        Item Assembly
                        <select name="assembly" data-width="100%" data-placeholder="Pilih Assembly"
                            class="form-control select2" required>
                            <option value=""></option>
                            @foreach ($bom as $row)
                            <option value="{{ $row->id }}">{{ $row->bom_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        Lokasi
                        <select name="lokasi" data-width="100%" data-placeholder="Pilih Lokasi"
                            class="form-control select2" required>
                            <option value=""></option>
                            @foreach ($gudang as $row)
                            <option value="{{ $row->id }}">{{ $row->code }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        Tanggal
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif name="tanggal" class="form-control" value="{{ date("Y-m-d") }}"
                            required>
                    </div>

                    <button type="submit" class="btn btn-block btn-primary">Submit</button>
                </div>
            </section>
        </div>
    </div>
</form>
@endsection