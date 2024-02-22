 @foreach ($bonlesselesai as $row)
     <div class="row">
         <div class="col">
             <div class="card mb-4">
                 <div class="card-header">
                     Boneless Bahan Baku
                 </div>
                 <div class="card-body">
                     <div class="border-bottom">
                         <div class="row">
                             <div class="col"><b>Nama</b></div>
                             <div class="col"><b>Mobil</b></div>
                             <div class="col"><b>Tgl Potong</b></div>
                             <div class="col-2"><b>Berat</b></div>
                         </div>
                     </div>
                     @foreach ($row->listfreestock as $f)
                         <div class="border-bottom">
                             <div class="row">
                                 <div class="col">{{ $f->chiller->item_name }}</div>
                                 <div class="col">{{ $f->chiller->no_mobil }}</div>
                                 <div class="col">{{ date('d/m/y', strtotime($f->chiller->tanggal_potong)) }}
                                 </div>
                                 <div class="col-2">{{ $f->qty }} Kg</div>
                             </div>
                         </div>
                     @endforeach
                 </div>
             </div>
         </div>
         <div class="col">
             <div class="card mb-4">
                 <div class="card-header">
                     Hasil Produksi
                 </div>
                 <div class="card-body">
                     @foreach ($row->freetemp as $f)
                         <div class="border-bottom">
                             <div class="row">
                                 <div class="col">{{ $f->item->nama }}</div>
                                 <div class="col-2">{{ $f->qty }} Kg</div>
                             </div>
                         </div>
                     @endforeach
                 </div>
             </div>
         </div>
     </div>
 @endforeach
