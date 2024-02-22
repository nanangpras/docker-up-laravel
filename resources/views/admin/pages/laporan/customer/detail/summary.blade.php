<table class="table default-table">
    <tbody>
        <tr>
            <th style="width: 170px">Parent</th>
            <td> @if($data->parent_id) {{ App\Models\Customer::where('id', $data->parent_id)->first()->nama }} @else {{ '#' }} @endif <button class="btn btn-primary" data-toggle="modal" data-target="#editParent">Edit Parent</button> </td>
        </tr>
        <tr>
            <th>Kode</th>
            <td>{{ $data->kode }}</td>
        </tr>
        <tr>
            <th>Nama</th>
            <td>{{ $data->nama }}</td>
        </tr>
        <tr>
            <th>Alamat</th>
            <td>{{ $data->alamat }}</td>
        </tr>
        <tr>
            <th>Marketing</th>
            <td>{{ $data->customermarketing->nama ?? '' }}</td>
        </tr>
        <tr>
            <th>Order</th>
            <td>
                <span data-key="alokasi" class="cursor view_order status status-success">{{ $data->alokasi }} Teralokasi</span>
                <span data-key="pending" class="cursor view_order status status-info">{{ $data->pending }} Pending</span>
            </td>
        </tr>
    </tbody>
</table>

<script>
$(".view_order").on('click', function() {
    var key =   $(this).data('key') ;
    $("#detail_view").load("{{ route('customer.show', [$id, 'key' => 'detail_view']) }}&view=" + key);
})


document.getElementById('inputParent').addEventListener('click', (e) => {
    // console.log('oke')
    const parent = document.getElementById('parent').value
    // console.log(parent)
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: "{{ route('customer.show', [$id]) }}",
        data: {
            id: parent,
            key: 'editParent'
        },
        success: function(data) {
            // console.log(data)
            if (data.message == 'success') {
                showNotif('Berhasil Ubah Parent', setInterval(function(){
                    window.location.reload();
                }, 1000));
            } else {
                showAlert(data.message);
            }
        }
    });
})

$('.select2').select2({
        theme: 'bootstrap4'
    })
</script>


<div class="modal fade" id="editParent" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Parent</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{-- <form action="{{ route('marketing.store') }}" method="post"> --}}
            @csrf
            <div class="modal-body">
                <div class="row">
                    <div class="col">
                        <div class=form-group>
                            <label> Nama Parent </label>
                            <select name="parent" id="parent" class="form-control select2">
                                @foreach($parent as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary" id="inputParent">Simpan</button>
            </div>
            {{-- </form> --}}
        </div>
    </div>
</div>