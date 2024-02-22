<label for="customer_dataSO">Customer</label>
<select name="customer_dataSO" id="customer_dataSO" onchange="customer_so()" class="form-control select2">
    <option value="">Semua</option>
    @foreach ($cust as $cst)
        @if ($cst->socustomer)
        <option value="{{$cst->customer_id}}" {{$filterCustomer == $cst->customer_id ? 'selected' : ''}} >{{$cst->socustomer->nama ?? ""}}</option>
        @endif
    @endforeach
</select>

<script>
$(".select2").select2({
    theme: "bootstrap4"
});
</script>
