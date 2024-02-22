<table class="table default-table">
    <thead>
        <tr>
            <th>Nama Plastik Group</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($plastikGroupPaginate as $i => $item)
        <tr>
            <td>{{ $item->data }}</td>
            <td><button type="button" class="btn btn-sm btn-danger btnDeletePlastikGroup" data-list="{{ $item->id }}">Hapus</button></td>
        </tr>
        @endforeach
    </tbody>
</table>
<div id="paginatePlastikGroup">
    {{ $plastikGroupPaginate->appends($_GET)->onEachSide(1)->links() }}
</div>

<script>
    $('#paginatePlastikGroup .pagination a').on('click', function(e) {
    e.preventDefault();
    showNotif('Menunggu');

    url = $(this).attr('href');
    $.ajax({
        url: url,
        method: "GET",
        success: function(response) {
            $('#tablePlastikGroup').html(response);
        }

    });
});



$('.btnDeletePlastikGroup').on('click', function(e) {
    // console.log()
    let idPlastikGroup = $(this).data('list');
    console.log(idPlastikGroup);
    $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
    $.ajax({
        url: "{{ route('abf.index') }}",
        data: {
            key: 'deletePlastikGroup',
            idPlastikGroup
        },
        success: function(data){
            console.log(data)
            if (data.status == '200') {
                showNotif(data.msg)
                $("#plastikGroup").val('');
                $("#tablePlastikGroup").load("{{ route('abf.index') }}?key=loadPlastikGroupPaginate");
                $('#plastikModal').modal('hide');
                $('#loadPlastikGroup').find('option[value='+idPlastikGroup+']').remove();
                // console.log($('#loadPlastikGroup').find('option[value='+idPlastikGroup+']'))
            } 
        }
    })
})
</script>