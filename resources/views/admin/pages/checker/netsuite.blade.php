   <form method="post" action="{{route('sync.cancel')}}">
        @csrf
        <br>
        <button type="submit" class="btn btn-blue mb-1" name="status" value="approve">Approve Integrasi</button> &nbsp
        <button type="submit" class="btn btn-red mb-1" name="status" value="cancel">Batalkan Integrasi</button> &nbsp
        <button type="submit" class="btn btn-info mb-1" name="status" value="retry">Kirim Ulang</button> &nbsp
        <button type="submit" class="btn btn-success mb-1" name="status" value="completed">Selesaikan</button> &nbsp
        <button type="submit" class="btn btn-warning mb-1" name="status" value="hold">Hold</button> &nbsp
        <hr>
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
             </form>