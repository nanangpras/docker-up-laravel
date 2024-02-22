<div class="border p-2">
   <div class="row mb-2">
       <div class="col-9 pr-1">
           <select name="itemfree" class="form-control select2" id="itemfree">
               <option value="" disabled selected hidden>Pilih Boneless Chicken</option>
                   @foreach ($item as $id => $list)
                       <option value="{{ $list->id }}">{{ $list->nama }}</option>
                   @endforeach
           </select>
        </div>
        <div class="col-3 pl-1">
            <input type="number" name="asulah" id="asulah" class="form-control" placeholder="Berat">
        </div>
    </div>
</div>

<button type="button" class="input_freebonles btn btn-sm btn-primary btn-block">Submit</button>

<script>
    $('.select2').select2({
        theme: 'bootstrap4'
    })

</script>
