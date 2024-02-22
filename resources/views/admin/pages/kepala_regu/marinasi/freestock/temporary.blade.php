@foreach ($data as $row)
<div class="border-bottom">
    <div class="row">
        <div class="col">{{ $row->stocklist->chiller->item_name }}</div>
        <div class="col">{{ $row->item->nama }}</div>
        <div class="col-2">{{ $row->qty }}</div>
        <div class="col-1">
            <span data-id="{{ $row->freestock_id }}" data-list="{{ $row->freestocklist_id }}" data-item="{{ $row->item_id }}" data-qty="{{ $row->qty }}" class="del_temporary text-danger fa fa-trash"></span>
        </div>
    </div>
</div>
@endforeach
