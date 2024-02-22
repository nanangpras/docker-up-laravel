@extends('admin.layout.template')

@section('title', 'Tracing Item')

@section('content')
<div class="row mb-4">
    <div class="col py-1">
        <a href="{{ route('sync.index') }}"><i class="fa fa-arrow-left"></i> Back</a>
    </div>
    <div class="col-8 py-1 text-center">
        <b class="text-uppercase">TRACING ITEM</b>
    </div>
    <div class="col"></div>
</div>


<style>
    .hidden-form{
        display: none;
    }
</style>

<section class="panel">
    <div class="card-body">
        <form method="get" action="{{route('tracing.index')}}">
            <div class="row">
                <div class="col">
                    <input type="month" name="bulan" value="{{ Request::get('bulan') ?? date('Y-m') }}" class="form-control mb-2">
                </div>
                <div class="col">
                    <select required name="item_id" class="form-control select2" data-placeholder="Pilih Item" data-width="100%">
                        <option value="0">- Pilih Item -</option>
                        @foreach ($item as $i)
                        <option value="{{ $i->id }}" @if(Request::get('item_id')==$i->id) selected @endif>{{ $i->sku }}. {{ $i->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <button type="submit" class="btn btn-blue">Filter</button>
                </div>
            </div>
        </form>
    </div>
    <div class="">
        <div class="table-responsive card-body">
            <div class="table-responsive mt-4" id="table-bb-fresh">
                @php 
                    $item_id    = Request::get('item_id');
                    $bulan      = Request::get('bulan');
                    $item       = App\Models\Item::find($item_id);

                    $start      = $date = strtotime($bulan.'-01');
                    $end        = strtotime($bulan.'-'.date('t',strtotime($bulan)));

                    $data_tanggal = [];
                    while($date < $end){
                        $data_tanggal[] = date('d F Y', $date);
                        $date = strtotime("+1 days", $date);
                    }
                    
                @endphp
                BULAN {{$bulan}} || ITEM : {{$item->nama ?? ""}}<hr>
                <style>
                    tr,td {
                        mso-number-format:"\@";
                        border:thin solid black;
                    }
                </style>
               <table class="table default-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th width="200px">Tanggal</th>
                            <th>BB</th>
                            <th>FG</th>
                            <th>EKSPEDISI</th>
                            <th>ABF</th>
                            <th>CS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($item_id)
                        @foreach($data_tanggal as $no => $tgl)
                        <tr>
                            <td>{{$no+1}}</td>
                            <td>{{date('d/m/y',strtotime($tgl))}}</th>
                            <td>
                                @php 
                                    $bb_total_qty       = 0;
                                    $bb_total_berat     = 0;
                                    $bahanbaku = \App\Models\Chiller::where('tanggal_produksi', date('Y-m-d',strtotime($tgl)))
                                            ->where('item_id', $item_id)
                                            ->where('type', 'bahan-baku')
                                            ->where('jenis', 'masuk')
                                            ->get();
                                @endphp

                                @foreach($bahanbaku as $bb)
                                    <div  style="width: 400px">[CH-{{$bb->id}}] {{$bb->item_name}} [{{$bb->qty_item}} || {{$bb->berat_item}}]</div>
                                    @php 
                                        $bb_total_qty       += $bb->qty_item;
                                        $bb_total_berat     += $bb->berat_item;
                                    @endphp
                                @endforeach
                                <div><b><span class="red">{{$bb_total_qty}} || {{$bb_total_berat}}</span></b></div>
                            </td>
                            <td>
                                @php 
                                    $fg_total_qty       = 0;
                                    $fg_total_berat     = 0;
                                    $finishedgood = \App\Models\Chiller::where('tanggal_produksi', date('Y-m-d',strtotime($tgl)))
                                            ->where('item_id', $item_id)
                                            ->where('type', 'hasil-produksi')
                                            ->where('jenis', 'masuk')
                                            ->get();
                                @endphp

                                @foreach($finishedgood as $fg)
                                    <div  style="width: 600px">[CH-{{$fg->id}}]<a href="{{url('admin/chiller/'.$fg->id)}}" target="_blank"> {{$fg->item_name}} </a> [{{$fg->qty_item}} || {{$fg->berat_item}}]
                                    
                                        @if($fg->kategori=="1")
                                        <span class="red pull-right">[ABF]</span>
                                        @elseif($fg->kategori=="2")
                                        <span class="yellow pull-right">[EKSPEDISI]</span>
                                        @elseif($fg->kategori=="3")
                                        <span class="green pull-right">[TITIP CS]</span>
                                        @else
                                        <span class="blue pull-right">[CHILLER]</span>
                                        @endif
                                    </div>
                                    @php 
                                        $fg_total_qty       += $fg->qty_item;
                                        $fg_total_berat     += $fg->berat_item;
                                    @endphp
                                @endforeach
                                <div><b><span class="red">{{$fg_total_qty}} || {{$fg_total_berat}}</span></b></div>
                            </td>
                            <td>
                                @php 
                                    $ekspedisi_total_qty       = 0;
                                    $ekspedisi_total_berat     = 0;
                                    $ekspedisi = \App\Models\Chiller::where('tanggal_produksi', date('Y-m-d',strtotime($tgl)))
                                            ->where('item_id', $item_id)
                                            ->where('table_name', 'order_bahanbaku')
                                            ->where('jenis', 'keluar')
                                            ->get();
                                            
                                @endphp

                                {{-- {{$ekspedisi}} --}}

                                @foreach($ekspedisi as $eks)
                                     <div  style="width: 600px">[CH-{{$eks->id}}]<a href="{{url('admin/chiller/'.$eks->id)}}" target="_blank"> {{$eks->item_name}} </a> [{{$eks->qty_item}} || {{$eks->berat_item}}] {{$eks->chillerorderbb->id ?? "#"}}
                                    
                                    @php 
                                        $ekspedisi_total_qty       += $eks->qty_item;
                                        $ekspedisi_total_berat     += $eks->berat_item;
                                    @endphp

                                    </div>
                                @endforeach
                                <div><b><span class="red">{{$ekspedisi_total_qty}} || {{$ekspedisi_total_berat}}</span></b></div>
                            </td>
                            <td>
                                @php 
                                    $abf_total_qty       = 0;
                                    $abf_total_berat     = 0;
                                    $abf = \App\Models\Abf::where('tanggal_masuk', date('Y-m-d',strtotime($tgl)))
                                            ->where('item_id', $item_id)
                                            ->where('jenis', 'masuk')
                                            ->get();
                                @endphp

                                @foreach($abf as $abf_)
                                    <div  style="width: 600px">
                                        @if($abf_->table_name=="chiller")
                                            [CH-{{$abf_->table_id}}]
                                        @endif
                                        [ABF-{{$abf_->id}}] <a href="{{url('admin/abf/timbang/'.$abf_->id)}}" target="_blank"> {{$abf_->item_name}}</a> [{{$abf_->qty_awal}} || {{$abf_->berat_awal}}]
                                        
                                        @if($abf_->type=="gabungan")
                                            @if($abf_->parent_abf=="")
                                               <span class="status status-success">HASILGABUNG</span>
                                            @endif
                                        @endif

                                        @if($abf_->parent_abf!="")
                                            <span class="status status-danger">DIGABUNG KE <a href="{url('admin/abf/timbang/'.$abf_->parent_abf)}}">{{$abf_->parent_abf}}</a></span>
                                        @endif
                                    </div>
                                    @php 
                                        $abf_total_qty       += $abf_->qty_awal;
                                        $abf_total_berat     += $abf_->berat_awal;
                                    @endphp
                                @endforeach
                                <div><b><span class="red">{{$abf_total_qty}} || {{$abf_total_berat}}</span></b></div>
                            </td>
                            <td>
                                @php 
                                    $wh_total_qty       = 0;
                                    $wh_total_berat     = 0;
                                    $it                 = App\Models\Item::where('nama', $item->nama.' FROZEN')->first();
                                    if(!$it){
                                        $it                 = App\Models\Item::where('nama', $item->nama)->first();
                                    }
                                    $wh = \App\Models\Product_gudang::where('production_date', date('Y-m-d',strtotime($tgl)))
                                            ->where('product_id', ($it->id ?? 0))
                                            ->where('jenis_trans', 'masuk')
                                            ->get();
                                @endphp

                                @foreach($wh as $whg)
                                    <div  style="width: 600px">
                                        @if($whg->table_name=="abf")
                                            [ABF-{{$whg->table_id}}]
                                        @endif
                                        [WH-{{$whg->id}}] <a href="{{url('admin/warehouse/tracing/'.$whg->id)}}" target="_blank">{{$whg->nama}}</a> [{{$whg->qty_awal}} || {{$whg->berat_awal}}]</div>
                                    @php 
                                        $wh_total_qty       += $whg->qty_awal;
                                        $wh_total_berat     += $whg->berat_awal;
                                    @endphp
                                @endforeach
                                <div><b><span class="red">{{$wh_total_qty}} || {{$wh_total_berat}}</span></b></div>
                            </td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
               </table>
            </div>
        </div>
    </div>
</section>

<form method="post" action="{{route('weekly.export')}}">
    @csrf
    <input name="filename" type="hidden" value="export-tracing-item.xls">
    <textarea name="html" style="display: none" id="html-bb-fresh"></textarea>
    <button type="submit" id="export-bb-fresh" class="btn btn-blue">Export</button>
</form>

<script>
    $(document).ready(function(){
        var html  = $('#table-bb-fresh').html();
        $('#html-bb-fresh').val(html);
    })
</script>


{{-- <div class="grid-container">
  <div class="grid">
    <div class="grid-col grid-col--fixed-left">
      <div class="grid-item grid-item--header">
        <p>TANGGAL</p>
      </div>
        @foreach($data_tanggal as $no => $tgl)
            <div class="grid-item">
                <p>{{date('d/m/y',strtotime($tgl))}}</p>
            </div>
        @endforeach
    </div>

    <div class="grid-col">
      <div class="grid-item grid-item--header">
        <p>BB</p>
      </div>
        @foreach($data_tanggal as $no => $tgl)
        @php 
            $bb_total_qty       = 0;
            $bb_total_berat     = 0;
            $bahanbaku = \App\Models\Chiller::where('tanggal_produksi', date('Y-m-d',strtotime($tgl)))
                    ->where('item_id', $item_id)
                    ->where('type', 'bahan-baku')
                    ->where('jenis', 'masuk')
                    ->get();
        @endphp

        @foreach($bahanbaku as $bb)
            
            @php 
                $bb_total_qty       += $bb->qty_item;
                $bb_total_berat     += $bb->berat_item;
            @endphp

            <div class="grid-item">
                <p>[CH-{{$bb->id}}] {{$bb->item_name}} [{{$bb->qty_item}} || {{$bb->berat_item}}]</p>
            </div>
            
        @endforeach
        
        
         @endforeach
      
    </div>
    <div class="grid-col">
      <div class="grid-item grid-item--header">
        <p>FG</p>
      </div>
      <div class="grid-item">
        <p>P</p>
      </div>
    </div>
    <div class="grid-col">
      <div class="grid-item grid-item--header">
        <p>EKSPEDISI</p>
      </div>
      <div class="grid-item">
        <p>P</p>
      </div>
    </div>
    <div class="grid-col">
      <div class="grid-item grid-item--header">
        <p>ABF</p>
      </div>
      <div class="grid-item">
        <p>P</p>
      </div>
    </div>
    <div class="grid-col">
      <div class="grid-item grid-item--header">
        <p>CS</p>
      </div>
      <div class="grid-item">
        <p>P</p>
      </div>
    </div>


  </div>
</div> --}}

<style>
    .grid-container {
  display: grid; /* This is a (hacky) way to make the .grid element size to fit its content */
  overflow: auto;
  height: 300px;
  width: 600px;
}
.grid {
  display: flex;
  flex-wrap: nowrap;
}
.grid-col {
  width: 150px;
  min-width: 150px;
}

.grid-item--header {
  height: 100px;
  min-height: 100px;
  position: sticky;
  position: -webkit-sticky;
  background: white;
  top: 0;
}

.grid-col--fixed-left {
  position: sticky;
  left: 0;
  z-index: 9998;
  background: white;
}
.grid-col--fixed-right {
  position: sticky;
  right: 0;
  z-index: 9998;
  background: white;
}

.grid-item {
  height: 50px;
  border: 1px solid gray;
}

</style>

@stop

@section('footer')
<script>
    $('.select2').select2({
        theme: 'bootstrap4',
    })
</script>
@endsection
