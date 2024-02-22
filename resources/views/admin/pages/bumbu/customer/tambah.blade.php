<form action="{{ route('bumbu.store') }}" method="post">
    @csrf
    @method('post')
    <input type="hidden" name="key" value="tambah_customer">
    <input type="hidden" name="bumbu_id" id="bumbu_id" value="">

    <div class="modal-body">
        <div class="form-group">
            <label for="customer_id">Customer</label>
            <select name="customer_id[]" id="customer_id" class="form-control form-control-sm" multiple>
                @foreach ($customer as $customerItem)
                    @php
                        $isDisabled = false;
                        foreach ($existCustomer as $existingCustomer) {
                            if ($existingCustomer->customer_id == $customerItem->id) {
                                $isDisabled = true;
                                break;
                            }
                        }
                    @endphp
                    <option value="{{ $customerItem->id }}" {{ $isDisabled ? 'disabled' : '' }}>{{ $customerItem->nama }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</form>