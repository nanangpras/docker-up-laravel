@foreach ($data as $row)
<div class="border-bottom">
    <div class="row">
        <div class="col-8 pr-1">{{ $row->item->nama }}</div>
        <div class="col-2 px-1">{{ $row->qty }}</div>
        <div class="col-auto text-right pl-1">
            <span data-id="{{ $row->id }}" data-list="{{ $row->freestocklist_id }}" data-item="{{ $row->item_id }}" data-qty="{{ $row->qty }}" class="del_temporary text-danger fa fa-trash"></span>
        </div>
    </div>
</div>
@endforeach
