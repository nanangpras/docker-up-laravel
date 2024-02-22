<div class="row">
    <div class="col">
        <select name="customer" class="form-control selected2" id="customer" onchange='customer_select()' data-width="100%">
            <option value="all">Semua Konsumen</option>
            @foreach ($customer as $row)
            <option value="{{ $row->customer_id }}" {{ $request->customer == $row->customer_id ? 'selected' : '' }}>{{ $row->konsumen->nama }}</option>
            @endforeach
        </select>
    </div>
</div>
