<table class="table default-table">
<thead>
    <tr>
        <th width="50">No</th>
        <th>Activity</th>
        <th>Label</th>
        <th>Table</th>
        <th>ID</th>
        <th>Data</th>
        <th>Sync</th>
        <th>Sync Start</th>
        <th>Sync End</th>
        <th width="50">Status</th>
    </tr>
</thead>
<tbody>
        @foreach($logs as $log)
        <tr>
            <td>{{$loop->iteration+($logs->currentpage() - 1) * $logs->perPage()}}</td>
            <td>{{$log->activity}}</td>
            <td>{{$log->label}}</td>
            <td>{{$log->table_name}}</td>
            <td>{{$log->table_id}}</td>
            <td><textarea class="form-control bg-white" readonly style="font-size: 7pt">{{$log->table_data}}</textarea></td>
            <td>{{$log->sync}}</td>
            <td>{{$log->sync_start_at}}</td>
            <td>{{$log->sync_completed_at}}</td>
            <td>
                @if($log->sync_status=="0" || $log->sync_status==null) <span class="status status-info">Pending</span>@endif
                @if($log->sync_status=="1") <span class="status status-danger">Trying</span>@endif
                @if($log->sync_status=="2") <span class="status status-success">Selesai</span>@endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{$logs->links()}}