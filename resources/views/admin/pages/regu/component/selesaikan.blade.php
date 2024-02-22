@if ($freestock)
    @if (COUNT($freestock->listfreestock) || (COUNT($freestock->freetemp)))

    {{-- <label><input type="checkbox" id="netsuite_send"> <span class="status status-danger" style="font-size: 15px;"><b>Tidak Proses WO</b></span> </label> --}}
    <div class="border-bottom mb-4 pb-4">
        <button type="submit" class="btn btn-success selesaikan btn-block mt-3 btnHiden">
            Simpan
        </button>
    </div>
    @endif
@endif
