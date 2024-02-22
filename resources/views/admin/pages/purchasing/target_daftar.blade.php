<section class="panel">
    <div class="card-body">
        <table class="table default-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Alamat</th>
                    <th>Toleransi</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $i => $row)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $row->alamat }}</td>
                    <td>{{ $row->target }}</td>
                    <td class="text-right">
                        <button class="btn btn-danger hapus_target btn-sm" data-id="{{ $row->id }}"><i class="fa fa-trash"></i></button>
                        <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#toleransi{{ $row->id }}"><i class="fa fa-edit"></i></button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

@foreach ($data as $row)
<div class="modal fade" id="toleransi{{ $row->id }}" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="toleransi{{ $row->id }}Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('purchasing.targetupdate') }}" method="post">
                @csrf @method('patch') <input type="hidden" name="x_code" value="{{ $row->id }}">
                <div class="modal-header">
                    <h5 class="modal-title" id="toleransi{{ $row->id }}Label">Edit Toleransi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        Alamat
                        <input type="text" name="alamat" autocomplete="off" value="{{ $row->alamat }}" class="form-control">
                    </div>
                    <div class="form-group">
                        Target
                        <input type="number" step="0.01" name="target" value="{{ $row->target }}" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Edit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
