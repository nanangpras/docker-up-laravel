@if (App\Models\User::setIjin(33) || App\Models\User::setIjin(3) || App\Models\User::setIjin(5))

@php
    $tanggal = Request::get('tanggal') ?? date("Y-m-d");
    $rendemen_data  = App\Models\Production::where('prod_tanggal_potong', $tanggal)
    ->where('sc_status', '1')
    ->whereIn('evis_status', ['1', '2', '3'])
    ->whereIn('grading_status', ['1', '2', '3'])
    ->orderBy('prod_tanggal_potong')
    ->orderBy('no_urut', 'DESC')->get();
@endphp


<div class="rendemen-outer">
    <div class="scroll-rendemen">
        @foreach ($rendemen_data as $x => $arr)
        @php
            $grading_berat = 0;
            $grading_item = 0;
            $evis_berat = 0;
            $evis_ekor = 0;
        @endphp
        @php
            $summary = \App\Models\Grading::where('trans_id', $arr->id)
                ->where('keranjang', 0)
                ->orderBy('id', 'DESC')
                ->get();
            $grading_berat = 0;
            $grading_item = 0;
            foreach ($summary as $sumary) {
                $grading_berat += $sumary->berat_item;
                $grading_item += $sumary->total_item;
            }
        @endphp
        @php
            $evis = \App\Models\Evis::where('production_id', $arr->id)->get();
            $evis_berat = 0;
            $evis_ekor = 0;
            foreach ($evis as $evs) {
                $evis_ekor += $evs->stock_item;
                $evis_berat += $evs->berat_stock;
            }
        @endphp
        <div class="mx-1">
            <div class="card mb-2" style="width: 200px; height: 200px; font-size: 9pt; {{ $x == 0 ? 'border: 1px solid #007bff;' : '' }} ">
                <div class="card-header">
                    @if(App\Models\User::setIjin(33))  <a href="{{url('admin/produksi/'.$arr->id)}}" target="_blank">Mobil {{ $arr->no_urut }} <span class="fa fa-share"></span></a> @else Mobil {{ $arr->no_urut }}  @endif
                    @if($arr->grading_status==1 && $arr->evis_status==1)
                    <span class="status status-info"> Selesai </span>
                    @else
                    <span class="status status-danger"> Proses </span>
                    @endif
                </div>
                <div class="card-body p-2">
                    <div class="border-bottom p-1">
                        <span class="float-right font-weight-bold">
                            @if ($arr->ekoran_seckle != 0)
                                {{ number_format($arr->lpah_berat_terima / $arr->ekoran_seckle, 2) ?? '###' }}
                            @endif
                        </span>
                        Rataan Terima
                    </div>
                    <div class="border-bottom p-1">
                        <span class="float-right font-weight-bold">
                            {{-- {{ $arr->prodpur->purchasing_item[0]->jenis_ayam ?? $arr->prodpur->jenis_ayam }} --}}
                            @if ($arr->prodpur->ukuran_ayam == '&lt; 1.1') {{ '<1.1' }} @else {{ $arr->prodpur->ukuran_ayam }} @endif
                        </span>
                        Ukuran
                    </div>

                    {{-- QUERY BENCHMARK --}}
                    @php
                        $yield_produksi = $arr->prod_yield_produksi ;
                        $getDataYield = App\Models\Adminedit::where('activity', 'input_yield')->where('content', $arr->prodpur->purchasing_item[0]->jenis_ayam )->where('type', $arr->prodpur->ukuran_ayam)->first();
                        if ($getDataYield) {
                            $decodeYield = json_decode($getDataYield->data);
                            if ($decodeYield) {
                                $dataPecahYieldKarkas = (explode(" - ", $decodeYield->yield_karkas));
                                $dataPecahYieldEvis   = (explode(" - ", $decodeYield->yield_evis));
                            }
                        }
                    @endphp


                    <div class="border-bottom p-1">

                        @if ($getDataYield) 
                            @if ($dataPecahYieldKarkas[0] && $dataPecahYieldKarkas[1])
                                <span class="float-right font-weight-bold @if($yield_produksi>= str_replace('%','', $dataPecahYieldKarkas[0]) && $yield_produksi<= str_replace('%','', $dataPecahYieldKarkas[1])) green @else red @endif">{{ number_format($yield_produksi, 2) }} %</span>

                                Yield Produksi
                            @else
                                <span class="status status-danger"><b>Benchmark Yield Karkas belum diinput </b></span>
                            @endif

                        @else

                            @if (env('NET_SUBSIDIARY') == 'EBA')
                            
                                <span class="float-right font-weight-bold @if($yield_produksi>=73 && $yield_produksi<=75) green @else red @endif">{{ number_format($yield_produksi, 2) }} %</span>
                                Yield Produksi
                            @else
                                <span class="float-right font-weight-bold @if($yield_produksi>=72 && $yield_produksi<=74) green @else red @endif">{{ number_format($yield_produksi, 2) }} %</span>
                                Yield Produksi
                            @endif

                        @endif

                    </div>

                    <div class="border-bottom p-1">
                        @php
                            if($arr->lpah_berat_terima != 0){
                                $yield_evis = ($evis_berat / $arr->lpah_berat_terima) * 100;
                            } else {
                                $yield_evis = 0 ;
                            };
                        @endphp

                        @if ($getDataYield) 
                            @if ($dataPecahYieldEvis[0] && $dataPecahYieldEvis[1])
                                <span class="float-right font-weight-bold @if($yield_evis>= str_replace('%','', $dataPecahYieldEvis[0]) && $yield_evis<= str_replace('%','', $dataPecahYieldEvis[1])) green @else red @endif ">{{ number_format($yield_evis, 2) }} %</span>
                                Yield Evis
                            @else
                                <span class="status status-danger"><b>Benchmark Yield Evis belum diinput </b></span>

                            @endif
                        @else
                            <span class="float-right font-weight-bold @if($yield_evis>=20 && $yield_evis<=22) green @else red @endif ">{{ number_format($yield_evis, 2) }} %</span>
                            Yield Evis
                        @endif

                    </div>



                    <div class="border-bottom p-1">
                        <span class="float-right font-weight-bold">
                            @if (env('NET_SUBSIDIARY') == 'EBA')
                                @if ($arr->prodpur->type_ekspedisi == 'tangkap')
                                    {{ number_format($arr->sc_berat_do != 0 ? ($grading_berat / $arr->sc_berat_do) * 100 : '0', 2) }}
                                @else
                                    {{ number_format($arr->lpah_berat_terima != 0 ? ($grading_berat / $arr->lpah_berat_terima) * 100 : '0', 2) }}
                                @endif
                                %
                            @else
                                @if ($arr->prodpur->type_ekspedisi == 'tangkap')
                                    <span class="float-right font-weight-bold @if($arr->sc_berat_do != 0 ? ($grading_berat / $arr->sc_berat_do) * 100 : '0' >= 72) green @else red @endif">{{ number_format($arr->sc_berat_do != 0 ? ($grading_berat / $arr->sc_berat_do) * 100 : '0',2) }} %</span>
                                @else
                                    <span class="float-right font-weight-bold @if($arr->lpah_berat_terima != 0 ? ($grading_berat / $arr->lpah_berat_terima) * 100 : '0' >= 68) green @else red @endif">{{ number_format($arr->lpah_berat_terima != 0 ? ($grading_berat / $arr->lpah_berat_terima) * 100 : '0', 2) }} %</span>
                                @endif
                            @endif
                        </span>
                        Rendemen
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
