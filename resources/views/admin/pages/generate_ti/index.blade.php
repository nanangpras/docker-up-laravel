@extends('admin.layout.template')

@section('title', 'Generate TI')

@section('footer')
<script>
    $('.select2').select2({
        theme: 'bootstrap4'
    })
</script>

<script>
    var x = 1;
function addRow(){
    var row = '';
    row +=  '<div class="row-'+(x)+' row">' ;
    row +=  '    <div class="col-9 pr-1">' ;
    row +=  '        <div class="form-group">' ;
    row +=  '            Item' ;
    row +=  '            <select name="item_from[]" data-width="100%" data-placeholder="Pilih Item" class="form-control select2">' ;
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
    row +=  '            <input type="number" name="qty_from[]" class="form-control" step="0.01" min="0" placeholder="Tulis Qty">' ;
    row +=  '        </div>' ;
    row +=  '    </div>' ;

    row +=  '    <div class="col-1 text-center mt-2">' ;
    row +=  '        &nbsp;' ;
    row +=  '        <h6 onclick="deleteRow('+(x)+')" class="mb-0 cursor"><i class="fa fa-trash text-danger"></i></h6>' ;
    row +=  '    </div>' ;
    row +=  '</div>' ;

    $('.data-from').append(row);
    $('.select2').select2({
        theme: 'bootstrap4'
    })
    x++;
}

function deleteRow(rowid){
    $('.row-'+rowid).remove();
}
</script>
@endsection

@section('content')
<div class="mb-4 font-weight-bold text-center text-uppercase">
    Generate TI
</div>

<form action="{{ route('generateti.store') }}" method="post">
    @csrf
    <div class="row">
        <div class="col-lg-8">
            <section class="panel">
                <div class="card-header font-weight-bold">
                    <div class="cursor float-right" onclick="addRow()"><i class="fa fa-plus"></i> Tambah</div>
                    Item Transfer
                </div>
                <div class="card-body">
                    <div class="data-from">
                        <div class="row">
                            <div class="col-9 pr-1">
                                <div class="form-group">
                                    Item
                                    <select name="item_from[]" data-width="100%" data-placeholder="Pilih Item"
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
                                    <input type="number" name="qty_from[]" class="form-control" step="0.01" min="0"
                                        placeholder="Tulis Qty">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="col-lg-4">
            <section class="panel">
                <div class="card-body">
                    <div class="form-group">
                        Gudang From
                        <select name="gudang_from" data-width="100%" data-placeholder="Pilih Gudang"
                            class="form-control select2" required>
                            <option value=""></option>
                            @foreach ($gudang as $row)
                            <option value="{{ $row->id }}">{{ $row->code }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        Gudang To
                        <select name="gudang_to" data-width="100%" data-placeholder="Pilih Gudang"
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
                            min="2023-01-01" @endif name="tanggal" class="form-control" required
                            value="{{date('Y-m-d')}}">
                    </div>

                    <button class="btn btn-block btn-primary">Submit</button>
                </div>
            </section>
        </div>
    </div>
</form>
@endsection