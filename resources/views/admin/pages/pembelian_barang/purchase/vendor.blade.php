<select name="vendorPO" id="vendorPO" onchange="vendorPO()" class="form-control select2">
    <option value="">Semua</option>
    @foreach ($vendorPO as $vendor)
    <option value="{{$vendor->supplier_id}}" {{$vendorRequest == $vendor->supplier_id ? 'selected' : ''}} >{{$vendor->supplier->nama ?? ''}}</option>
    @endforeach
</select>

<script>
$(".select2").select2({
    theme: "bootstrap4"
});
</script>
