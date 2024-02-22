{{-- <div class="card-header font-weight-bold text-uppercase">History Edit</div> --}}
<section class="panel">
    <div class="card-body p-2">
        <p class="text-bold">
        </p>
        <table class="table default-table mb-0">
            <thead>
                <tr >
                    <th rowspan="2">No</th>
                    <th rowspan="2">User</th>
                    <th rowspan="2">TIMESTAMP</th>
                    <th colspan="2">Sebelum Edit</th>
                    <th colspan="2">Setelah Edit</th>
                </tr>
                <tr>
                    <th>Qty</th>
                    <th>Berat</th>
                    <th>Qty</th>
                    <th>Berat</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($adminEdit as $key => $item)
                <?php
                    $dataEdit= json_decode($item->data ,true);
                    $beforeUpdate=$dataEdit['before_update'];
                    $afterUpdate= $dataEdit['after_update'];
                    $username =\App\Models\User::find($item->user_id);
                ?>
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $username->name }}</td>
                    <td>{{ $item->created_at }}</td>
                    <td>{{ number_format($beforeUpdate['qty'], 2)  }}</td>
                    <td>{{ number_format($beforeUpdate['berat'], 2)  }}</td>
                    <td class="{{ ($beforeUpdate['qty'] != $afterUpdate['qty'] ? 'table-warning' : '' ) }}">{{ number_format($afterUpdate['qty'], 2)  }}</td>
                    <td class="{{ ($beforeUpdate['berat'] != $afterUpdate['berat'] ? 'table-warning' : '' ) }}">{{ number_format($afterUpdate['berat'], 2)  }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

