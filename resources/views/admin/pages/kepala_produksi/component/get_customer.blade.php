Customer
<div class="form-group">
    <select name="customer" id="customer_stok" onchange="getCust()" class="form-control select2">
        <option value="all">Semua</option>
        @foreach ($stok as $item)
            @if ($item->customer_id)
                <option value="{{ $item->customer_id }}" {{ $filter_customer == $item->customer_id ? 'selected' : '' }}>
                    {{ $item->nama ?? '' }}</option>
            @endif
        @endforeach
    </select>
</div>
{{-- {{ var_dump($filter_customer)}} --}}
<script>
    $(".select2").select2({
        theme: "bootstrap4"
    });
</script>
