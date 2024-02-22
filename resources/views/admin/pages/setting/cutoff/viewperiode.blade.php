<table class="table table-striped table-bordered">
    <thead>
        <th>No</th>
        <th>Dibuat</th>
        <th>Table</th>
        <th>Data</th>
    </thead>
    <tbody>
        @foreach($master as $data)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $data->created_at }}</td>
                <td>{{ $data->table_name }}</td>
                <td>{{ $data->content }}</td>
            </tr>
        @endforeach
    </tbody>
</table>