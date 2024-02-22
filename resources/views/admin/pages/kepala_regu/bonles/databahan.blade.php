<div class="card-body">
@foreach ($data as $i => $bb)
    @foreach ($bb->listfreestock as $cuk)
     <div class="radio-toolbar">
             <label>
                 {{ $cuk->item->nama }}
                 <div class="pull-right">
                    Berat <span class="label label-rounded-grey">{{ $cuk->qty ?? "0" }} kg </span>
             </div>
            </label>
        </div>
    @endforeach
 @endforeach
</div>
