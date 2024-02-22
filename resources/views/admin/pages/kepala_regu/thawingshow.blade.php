<table class="table table-sm table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Item</th>
            <th>Sub</th>
            <th>Ekor/Pcs/Pack</th>
            <th>Berat</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $i => $thawing)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $thawing->productitems->nama }}</td>
                <td> {{ $thawing->sub_item ?? 'Free Stock' }} </td>
                <td>{{ number_format($thawing->qty, 0) }}</td>
                <td>{{ number_format($thawing->berat, 2) }}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-primary px-2" data-toggle="modal"
                        data-target="#modal{{ $thawing->id }}">
                        Thawing
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

@foreach ($data as $i => $thawing)
    <div class="modal fade" id="modal{{ $thawing->id }}" tabindex="-1"
        aria-labelledby="modal{{ $thawing->id }}Label" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('thawing.store') }}" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal{{ $thawing->id }}Label">REQUEST THAWING</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    QTY
                                    <input type="number" name="qty" class="form-control" required>
                                </div>
                            </div>

                            <div class="col">
                                <div class="form-group">
                                    BERAT
                                    <input type="number" name="berat" class="form-control" step="0.01" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    @csrf <input type="hidden" name="x_code" value="{{ $thawing->id }}">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">OK</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endforeach
