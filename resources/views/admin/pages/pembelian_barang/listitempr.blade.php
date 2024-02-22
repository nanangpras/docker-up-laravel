@if ($download == true)
    @php
        header('Content-Transfer-Encoding: none');
        header('Content-type: application/vnd-ms-excel');
        header('Content-type: application/x-msexcel');
        header('Content-Disposition: attachment; filename=Download-Item-Purchase-Request.xls');
    @endphp
    <style>
        th,
        td {
            border: 1px solid #ddd;
        }
    </style>
    
@endif

<section class="panel">
    <div class="card-body p-2">
        <table class="table default-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>SKU</th>
                    <th>InternalID</th>
                    <th>Nama item</th>
                    <th>Unit</th>
                    <th>Subsidiary</th>
                    <th>Kategori</th>
                    <th>Status</th>
                    @if ($download == false)
                    <th></th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($item as $row)
                    <tr>
                        @if ($download == false)
                        <td>{{ $loop->iteration + ($item->currentpage() - 1) * $item->perPage() }}</td>
                        @else
                        <td>{{ $loop->iteration }}</td>
                        @endif
                        <td>{{ $row->sku }}</td>
                        <td>{{ $row->netsuite_internal_id ?? '#' }}</td>
                        <td>{{ $row->nama }}</td>
                        <td>{{ $row->type }}</td>
                        <td>{{ $row->subsidiary }}</td>
                        <td>{{ $row->itemkat->nama }}</td>
                        <td>{{ $row->status==1 ? "Active" : "Inactive"  }}</td>
                        @if ($download == false)
                        <td>
                            <a href="" class="btn btn-blue btn-sm btn-edit-item" data-toggle="modal" data-target="#editUser{{ $row->id }}">Edit</a>
                            <div class="modal fade" id="editUser{{ $row->id }}" tabindex="-1" aria-labelledby="editUser{{ $row->id }}Label" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editUser{{ $row->id }}Label">Update Item</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('item.update') }}" method="POST">
                                        @csrf @method('patch') <input type="hidden" name="id" value="{{ $row->id }}">
                                        <div class="modal-body">
                                            <div class="form-group">
                                                Unit
                                                <input type="text" name="unit" class="form-control" id="unit" placeholder="Tuliskan Unit" value="{{ $row->type }}" required autocomplete="off">
                                            </div>


                                            <div class="form-group">
                                                Status
                                                <select name="status" class="form-control" id="status" required>
                                                    <option value="1" {{ ($row->status == '1') ? 'selected' : '' }}>Aktif</option>
                                                    <option value="0" {{ ($row->status == '0') ? 'selected' : '' }}>Non Aktif</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Save changes</button>
                                        </div>
                                    </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if ($download == false)
        <div id="paginateitemlistpr">
            {{ $item->appends($_GET)->onEachSide(1)->links() }}
        </div>
        @endif
    </div>
</section>
@if ($download == false)
<div class="form-group">
    <button type="button" class="btn btn-success mb-2 float-right downloadItemPR"><i class="fa fa-spinner fa-spin spinerloading" style="display:none;"></i> <span id="text">Download</span></button>
</div>
@endif

@if ($download == false)
<script>
    $('#paginateitemlistpr .pagination a').on('click', function(e) {
        e.preventDefault();
        showNotif('Menunggu');

        url = $(this).attr('href');
        $.ajax({
            url: url,
            method: "GET",
            success: function(response) {
                $('#listitempr').html(response);
            }

        });
    });
    $(".downloadItemPR").on('click', function () {
        $.ajax({
            method: "GET",
            url: "{{ route('pembelian.index', ['key' => 'listitempr']) }}&subkey=download",
            beforeSend: function() {
                $('.downloadItemPR').attr('disabled');
                $(".spinerloading").show(); 
                $("#text").text('Downloading...');
            },
            success: function (response) {
                window.location = "{{ route('pembelian.index', ['key' => 'listitempr']) }}&subkey=download";
                $("#text").text('Download');
            $(".spinerloading").hide();
            }
        });
    });
</script>
@endif