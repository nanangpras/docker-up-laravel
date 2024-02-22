@if (count($data) == 0)
    <form action="{{ route('retur.storecustomer') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="">Customer</label>
            <select name="customer" class="form-control select2" id="customer">
                <option value="" disabled selected hidden>Pilih </option>
                @foreach ($customer as $cus)
                    <option value="{{ $cus->id }}">{{ $cus->nama }}</option>
                @endforeach
            </select>
            @error('customer') <div class="small text-danger">{{ message }}</div>
            @enderror

        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
@endif

<table class="table default-table">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Alamat</th>
            <th>SO</th>
            <th>#</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $row)
        <tr>
            <td></td>
            <td>{{ $row->to_customer->nama }}</td>
            <td>{{ $row->to_customer->alamat }}</td>
            <td>{{ \App\Models\Order::where('id_so', $row->id_so)->first()->no_so ?? 'Non SO' }}</td>
            <td><button class="btn btn-link text-danger p-0 ml-1 deletecustomer"
                data-id="{{ $row->id }}" type="button">
                <i class="fa fa-trash"></i>
            </button></td>
        </tr>
        @endforeach
    </tbody>
</table>

<script>
    $('.select2').select2({
        theme: 'bootstrap4'
    });
    $('.deletecustomer').click(function() {
        var id = $(this).data('id');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: "{{ route('retur.deletecus') }}",
            method: "POST",
            data: {
                id: id
            },
            success: function(data) {
                showNotif('Berhasil Delete');

                $("#datacustomer").load("{{ route('retur.customer') }}");
                $("#itemretur").load("{{ route('retur.itemretur') }}");
            }
        })
    })
</script>
