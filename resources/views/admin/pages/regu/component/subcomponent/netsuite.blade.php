@if(count($netsuite) > 0)
    <h6>Netsuite Terbentuk</h6>

    <table class="table default-table">
        <thead>
            <tr>
                <th>
                    <input type="checkbox" id="ns-checkall">
                </th>
                <th>ID</th>
                <th>C&U Date</th>
                <th>TransDate</th>
                <th>Label</th>
                <th>Activity</th>
                <th>Location</th>
                <th>IntID</th>
                <th>Paket</th>
                <th width="100px">Data</th>
                <th width="100px">Action</th>
                <th>Response</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($netsuite as $no => $field_value)
                @include('admin.pages.log.netsuite_one', ($netsuite = $field_value))
            @endforeach
        </tbody>
    </table>
@else
    @if (User::setIjin(33))
        @if (isset($progress) < 1)
            @if(env('NET_SUBSIDIARY', 'CGL') != "EBA")
                <h6>Wo Belum dikirim</h6>
                <div class="alert alert-danger">
                    Kirim WO ketika semua produksi sudah selesai, jangan dikirim jika masih proses, WO bersifat global dan sekali kirim.
                </div>
                <form action="{{ route('wo.create') }}" method="GET">
                    <div class="form-group">
                        <input type="hidden" name="tanggal" class="form-control tanggal" id="tanggal-form" value="{{ $tanggal }}" autocomplete="off">
                        <input type="hidden" name="regu" class="form-control" id="regu-form" value="{{ $kategori }}" autocomplete="off">
                        <button type="submit" class="btn btn-blue form-control">Buat WO</button>
                    </div>
                </form>
            @endif
        @endif
    @endif
@endif