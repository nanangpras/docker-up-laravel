<section class="panel">
    <div class="card-body">
        {{-- <div class="form-group">
            <button type="submit" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Tambah
                Category</button>
        </div> --}}
        <table class="table default-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Slug</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $i => $val)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $val->nama }}</td>
                        <td>{{ $val->slug }}</td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

<div class="modal fade " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="nama">Nama</label>
                    <input type="text" name="nama" class="form-control" id="nama" placeholder="Tuliskan " value=""
                        autocomplete="off" required>
                    @error('nama') <div class="small text-danger">{{ message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label for="slug">Slug</label>
                    <input type="text" name="slug" class="form-control" id="slug" placeholder="Tuliskan " value=""
                        autocomplete="off" required>
                    @error('slug') <div class="small text-danger">{{ message }}</div> @enderror
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
