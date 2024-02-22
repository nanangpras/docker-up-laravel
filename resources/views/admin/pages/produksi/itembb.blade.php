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
            <input type="number" name="berat" id="berat" class="form-control" placeholder="Berat">
        </div>
    </div>

    <div class="row">
        <div class="col-9 pr-1">
            <div class="form-group">
                Plastik
                <select name="plastik" id='plastik' class="form-control select2" data-width="100%" data-placeholder="Pilih Plastik">
                    <option value=""></option>
                    <option value="Curah">Curah</option>
                    @foreach($plastik as $p)
                        <option value="{{$p->id}}">{{$p->nama}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-3 pl-1">
            &nbsp;
            <input type="number" name="jumlah_plastik" id="jumlah_plastik" class="form-control" placeholder="Jumlah">
        </div>
    </div>
</div>



<button type="button" class="input_freestock btn btn-sm btn-primary btn-block">Submit</button>

<script>
    $('.select2').select2({
        theme: 'bootstrap4'
    })

</script>
