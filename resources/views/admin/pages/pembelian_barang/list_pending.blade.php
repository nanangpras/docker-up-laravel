<section class="panel">
    <div class="card-body p-2">
        <span class="text-danger mb-3">*Request Yang Belum Terselesaikan</span>
        <table class="table default-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No PR</th>
                    <th>Divisi</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pembelian as $row)
                <tr>
                    <td>{{ $loop->iteration + ($pembelian->currentpage() - 1) * $pembelian->perPage() }}</td>      
                    <td>{{ $row->no_pr }}</td>      
                    <td>{{ $row->divisi }}</td>      
                    <td>{{ $row->tanggal }}</td>      
                    <td><a href="{{ route('pembelian.index', ['id' => $row->id]) }}" class="btn btn-primary">Detail
                    </a></td>      
                </tr>
                @endforeach
            </tbody>
        </table>
        <div id="paginate_list">
            {{ $pembelian->appends($_GET)->onEachSide(1)->links() }}
        </div>
    </div>
</section>

<script>
    $('#paginate_list .pagination a').on('click', function(e) {
        e.preventDefault();
        showNotif('Menunggu');
    
        url = $(this).attr('href');
        $.ajax({
            url: url,
            method: "GET",
            success: function(response) {
                $('#listPendingPR').html(response);
            }
    
        });
    });
    </script>