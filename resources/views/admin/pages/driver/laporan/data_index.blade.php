<div class="card mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table default-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Driver</th>
                        <th>Jenis Driver</th>
                        {{-- <th>Nomor Polisi</th> --}}
                        <th>Ambil</th>
                        <th>Delivery</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $i => $row)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $row->nama }}</td>
                        @if($row->driver_kirim == 1)
                        <td>Driver Kirim</td>
                        @else
                        <td>Driver Tangkap</td>
                        @endif
                        <td>{{ Driver::hitungKerja($row->id, 'tangkap') }}</td>
                        <td>{{ Driver::hitungKerja($row->id, 'kirim') }}</td>
                        <td>
                            <a href="{{ route('driver.detail_laporan', $row->id) }}"
                                class="btn py-0 btn-sm btn-primary">Detail</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $data->appends($_GET)->onEachSide(1)->links() }}
    </div>
</div>

<script>
    $('.pagination a').on('click', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        $.ajax({
            url: url,
            method: "GET",
            success: function(response) {
                $('#loadindexlaporandriver').html(response);
            }
        });
    });

</script>