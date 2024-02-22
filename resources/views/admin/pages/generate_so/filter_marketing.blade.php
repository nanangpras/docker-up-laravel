<label for="">Marketing</label>
<select name="marketing_dataSO" id="marketing_dataSO" onchange="marketing_so()" class="form-control select2">
    <option value="">Semua</option>
    @foreach ($cust as $cst)
        @if ($cst->souser)
            <option value="{{$cst->user_id}}" {{$filterMarketing == $cst->user_id ? 'selected' : ''}}>{{ $cst->souser->name ?? ''}}</option>
        @endif
    @endforeach
</select>

<script>
$(".select2").select2({
    theme: "bootstrap4"
});
</script>
