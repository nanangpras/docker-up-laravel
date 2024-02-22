<div class="form-group">
    Item Name
    <input type="text" id="itemname" name="itemname" placeholder="Tuliskan Item Name" class="form-control" autocomplete="off" required>
</div>

<table class="table default-table">
    <thead>
        <tr>
            <th>Item Name</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($itemPaginate as $i => $item)
        <tr>
            <td>{{ $item->data }}</td>
            <td><button type="button" class="btn btn-sm btn-danger btnDeleteItemName" data-list="{{ $item->id }}">Hapus</button></td>
        </tr>
        @endforeach
    </tbody>
</table>
<div id="paginateListItem">
    {{ $itemPaginate->appends($_GET)->onEachSide(1)->links() }}
</div>

<script>
    $('#paginateListItem .pagination a').on('click', function(e) {
    e.preventDefault();
    showNotif('Menunggu');

    url = $(this).attr('href');
    $.ajax({
        url: url,
        method: "GET",
        success: function(response) {
            $('#tableListItemName').html(response);
        }

    });
});


$('.btnDeleteItemName').on('click', function(e) {
    // console.log()
    let idItemName = $(this).data('list');
    // console.log(idItemName);
    $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
    $.ajax({
        url: "{{ route('abf.index') }}",
        data: {
            key: 'deleteItemName',
            idItemName
        },
        success: function(data){
            console.log(data)
            if (data.status == '200') {
                showNotif(data.msg)
                $("#itemname").val('');
                $("#tableListItemName").load("{{ route('abf.index') }}?key=loadItemNamePaginate");
                $('#exampleModal').modal('hide');
                $('#loadItemName').find('option[value='+idItemName+']').remove();
            }  else {
                showAlert(data.msg)
            }
        }
    })
})
</script>