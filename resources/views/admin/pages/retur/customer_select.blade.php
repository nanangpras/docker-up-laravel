<select name='customer_data' id='customer_data' onchange='konsumen_pilih()' class='form-control select2'>
    <option value='all'>Semua</option>
    @foreach ($retur->groupBy('customer_id')->get() as $list)
        {{-- @php
            $cust   =   App\Models\Order::where('netsuite_internal_id', $list->id_so)->first();
        @endphp
        @if ($cust)
            <option value="{{ $cust->customer_id }}" {{ ($cust->customer_id == $request->customer ? 'selected' : '') }}>{{ $cust->nama }}</option>
        @endif --}}
        @php 
            $user = \App\Models\Customer::where('id', $list->customer_id)->first();
        @endphp
        @if($user)
            <option value="{{$user->id}}" {{ ($user->id == $request->customer_id ? 'selected' : '') }}>{{$user->nama}}</option>
        @endif
    @endforeach
</select>

<script>
$('.select2').select2({
    theme: 'bootstrap4'
});
</script>
