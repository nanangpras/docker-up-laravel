@extends('admin.layout.template')

@section('title', 'Checker Produksi')

@section('content')
@php
$ns = \App\Models\Netsuite::where('document_code', $produksi->no_po)->whereNotNull('document_no')->where('tabel_id', $produksi->id )->where('tabel', 'productions')->get();
@endphp
    <div class="row mb-4">
        <div class="col">
            <a href="{{ route('lpah.index') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-produksiow-left"></i>
                Back</a>
        </div>
        <div class="col-6 py-1 text-center">
            <b>Penerimaan Masuk</b>
        </div>
        <div class="col"></div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="mb-3">


                <div class="row mb-4">
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3">

                                    <h5>Informasi LPAH</h5>
                                    @if ($produksi->lpah_status == 3)
                                        <div class="alert alert-info">Menunggu Konfirmasi Check Data</div>
                                    @elseif ($produksi->lpah_status == 1)
                                        <div class="alert alert-success">Check Data LPAH Selesai</div>
                                    @else
                                        <div class="alert alert-warning">Proses input LPAH belum selesai</div>
                                    @endif

                                </div>

                                <div id="informasi">
                                    <div class="row border-bottom">
                                        <div class="col-md-4 col-6 mb-4">
                                            <div class="form-group">
                                                <div class="small">NOMOR PO</div>
                                                <b>{{ $produksi->prodpur->no_po ?? '###' }}</b>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-6">
                                            <div class="form-group">
                                                <div class="small">NOMOR MOBIL</div>
                                                <b>{{ $produksi->no_urut ?? '###' }}</b>
                                            </div>
                                        </div>
                                    
                                        <div class="col-md-4 col-6">
                                            <div class="form-group">
                                                <div class="small">SUPPLIER</div>
                                                <b>{{ $produksi->prodpur->purcsupp->nama }}</b>
                                            </div>
                                        </div>
                                    
                                        <div class="col-md-4 col-6">
                                            <div class="form-group">
                                                <div class="small">WILAYAH</div>
                                                <b class="text-capitalize">{{ $produksi->sc_wilayah ?? '####' }}</b>
                                            </div>
                                        </div>
                                    
                                        <div class="col-md-4 col-6">
                                            <div class="form-group">
                                                <div class="small">JENIS EKSPEDISI</div>
                                                <b class="text-capitalize">{{ $produksi->po_jenis_ekspedisi }}</b>
                                            </div>
                                        </div>
                                    
                                    
                                        <div class="col-md-4 col-6">
                                            <div class="form-group">
                                                <div class="small">NO POLISI</div>
                                                <b>{{ $produksi->sc_no_polisi }}</b>
                                            </div>
                                        </div>
                                    
                                        <div class="col-md-4 col-6">
                                            <div class="form-group">
                                                <div class="small">EKOR DO</div>
                                                <b>{{ number_format($produksi->sc_ekor_do) }} Ekor</b>
                                            </div>
                                        </div>
                                    
                                        <div class="col-md-4 col-6">
                                            <div class="form-group">
                                                <div class="small">BERAT DO</div>
                                                <b>{{ number_format($produksi->sc_berat_do, 1) }} KG</b>
                                            </div>
                                        </div>
                                    
                                        <div class="col-md-4 col-6">
                                            <div class="form-group">
                                                <div class="small">UKURAN AYAM</div>
                                                <b>@if ($produksi->prodpur->ukuran_ayam == '&lt; 1.1') {{ '<1.1' }} @else {{ $produksi->prodpur->ukuran_ayam }} @endif</b>
                                            </div>
                                        </div>
                                    
                                    </div>
                                    
                                    
                                    <div class="row mt-3 pb-2">
                                    
                                        <div class="col-md-4 col-6">
                                            <div class="form-group">
                                                <div class="small">EKOR LPAH</div>
                                                <b>{{ number_format($produksi->total_lpah) }} Ekor</b>
                                            </div>
                                        </div>
                                    
                                    
                                        <div class="col-md-4 col-6">
                                            <div class="form-group">
                                                <div class="small">BERAT BERSIH LPAH</div>

                                    
                                                <b>{{ $produksi->lpah_berat_terima }} KG</b>
                                            </div>
                                        </div>
                                    
                                    </div>
                                    
                                    
                                    <div class="row mt-3 pb-2 border-bottom">
                                        <div class="col-md-4 col-6">
                                            <div class="form-group">
                                                <div class="small">KERANJANG KOSONG</div>
                                                <b>{{ number_format($produksi->berat_keranjang, 1) }} KG</b>
                                            </div>
                                        </div>
                                    
                                        <div class="col-md-4 col-6">
                                            <div class="form-group">
                                                <div class="small">KERANJANG ISI</div>
                                                <b>{{ number_format($produksi->berat_isi, 1) }} KG</b>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-6">
                                            <div class="form-group">
                                                <div class="small">TEMBOLOK</div>
                                                <b>{{ number_format($produksi->qc_tembolok, 1) }} KG</b>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mt-3">
                                    
                                        <div class="col-md-4 col-6">
                                            <div class="form-group">
                                                <div class="small">KONDISI AYAM</div>
                                                <b>{{ $produksi->kondisi_ayam }}</b>
                                            </div>
                                        </div>
                                    
                                    
                                        <div class="col-md-4 col-6">
                                            <div class="form-group">
                                                <div class="small">AYAM MATI</div>
                                                <b>{{ number_format($produksi->qc_ekor_ayam_mati) }} Ekor</b>
                                            </div>
                                        </div>
                                    
                                        <div class="col-md-4 col-6">
                                            <div class="form-group">
                                                <div class="small">BERAT AYAM MATI</div>
                                                <b>{{ number_format($produksi->qc_berat_ayam_mati, 1) }} KG</b>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                    
                                        <div class="col-md-4 col-6">
                                            <div class="form-group">
                                                <div class="small">RATA RATA TERIMA</div>
                                                @php
                                                $pisahKomaRataRata = explode('.', $produksi->lpah_rerata_terima)
                                                @endphp
                                    
                                                @if($pisahKomaRataRata[1] ?? FALSE)
                                                    @if (strlen($pisahKomaRataRata[1]) > 1) 
                                                    <b>{{ mb_substr($produksi->lpah_rerata_terima, 0, -1) }} KG</b>
                                                    @else
                                                    <b>{{ $produksi->lpah_rerata_terima }} KG</b>
                                                    @endif
                                                @else
                                                <b>{{ $produksi->lpah_rerata_terima }} KG</b>
                                                @endif
                                    
                                    
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-6">
                                            <div class="form-group">
                                                <div class="small">AYAM MERAH</div>
                                                <b>{{ $produksi->qc_ekor_ayam_merah }} Ekor</b>
                                            </div>
                                        </div>
                                    
                                    
                                        <div class="col-md-4 col-6">
                                            <div class="form-group">
                                                <div class="small">AYAM MERAH KG</div>
                                                <b>{{ number_format($produksi->qc_berat_ayam_merah, 1) }} KG</b>
                                            </div>
                                        </div>
                                    
                                        <div class="col-md-4 col-6">
                                            
                                        </div>
                                    </div>
                                    
                                    <div class="row mt-3">
                                    
                                        <div class="col-md-4 col-6">
                                            <div class="form-group">
                                                <div class="small">TANGGAL POTONG</div>
                                                <b>{{ $produksi->prod_tanggal_potong }}</b>
                                            </div>
                                        </div>
                                    
                                    
                                        <div class="col-md-4 col-6">
                                            <div class="form-group">
                                                <div class="small">OPERATOR</div>
                                                <b>{{ $produksi->lpah_user_nama }}</b>
                                            </div>
                                        </div>
                                    
                                        <div class="col-md-4 col-6">
                                            <div class="form-group">
                                                <div class="small">JAM BONGKAR</div>
                                                <b>{{ $produksi->lpah_jam_bongkar }}</b>
                                            </div>
                                        </div>
                                        {{-- <div class="col-md-4 col-6">
                                            <div class="form-group">
                                                <div class="small">AYAM MERAH</div>
                                                <b>{{ $produksi->qc_ekor_ayam_merah }}</b>
                                            </div>
                                        </div> --}}
                                        {{-- <div class="col-md-4 col-6">
                                            <div class="form-group">
                                                <div class="small">BERAT AYAM MERAH</div>
                                                <b>{{ $data->qc_berat_ayam_merah ?? '' }}</b>
                                            </div>
                                        </div> --}}
                                        <div class="col-md-4 col-6">
                                            <div class="form-group">
                                                <div class="small">TOTAL KERANJANG</div>
                                                <b>{{ $produksi->lpah_jumlah_keranjang ?? ''}}</b>
                                            </div>
                                        </div>
                                    </div>
                                    

                                </div>


                                <hr>
                                @if (User::setijin(33))
                                    @if ($produksi->lpah_status == 3)
                                        <form action="{{ route('lpah.store', ['key' => 'selesai']) }}" method="POST">
                                            @csrf <input type="hidden" name="x_code" value="{{ $produksi->id }}">
                                            <button class="float-right btn btn-danger btn-rounded">Selesaikan</button>
                                        </form>

                                        @if(count($ns) < 1)
                                            @if(Auth::user()->account_role == 'superadmin' or User::setijin(33))
                                                <a href="{{ route('lpah.show', [$produksi->id, 'key' => 'edit_checker']) }}" class="btn btn-primary">Edit Data LPAH</a>
                                            @endif
                                        @else
                                            @if(Auth::user()->account_role == 'superadmin')
                                                <a href="{{ route('lpah.show', [$produksi->id, 'key' => 'edit_checker']) }}" class="btn btn-primary">Edit Data LPAH</a>
                                            @endif
                                        @endif

                                        @if ($ceklogedit > 0)
                                        <a href="javascript:void();" class="btn btn-warning btn-md loghistory" title='History' data-toggle="modal" data-target="#modalHistory" data-id="{{$produksi->id}}"> History Edit </a>
                                        @endif
                                    @endif
                                    @if ($produksi->lpah_status == 1)
                                        @if(count($ns) < 1)
                                            @if(Auth::user()->account_role == 'superadmin' or User::setijin(33))
                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editlpahafterdone">Edit Data LPAH</button>
                                            @endif
                                        @else
                                            @if(Auth::user()->account_role == 'superadmin')
                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editlpahafterdone">Edit Data LPAH</button>
                                            @endif
                                        @endif
                                        @if ($ceklogedit > 0)
                                        <a href="javascript:void();" class="btn btn-warning loghistory" title='History' data-toggle="modal" data-target="#modalHistory" data-id="{{$produksi->id}}"> History Edit </a>
                                        @endif
                                    @endif
                                @endif
                                <hr>
                                <div class="modal fade" id="editlpahafterdone" tabindex="-1" role="dialog" aria-labelledby="editlpahafterdoneLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editlpahafterdoneLabel"> Nomor PO : {{ $produksi->no_po}} // <p class="d-inline" style="color:blue;"> ID :  {{ $produksi->id}} </p></h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form role="form" class="form-horizontal">
                                                    <div class="row">
                                                        <div class="col-4">
                                                            <div class="form-group">
                                                                <label for="berat_do" class="col-form-label">Berat DO</label>
                                                                <input type="number" step="0.01" class="form-control cekform" id="berat_do" name="berat_do" placeholder="input berat DO" value="{{ $produksi->sc_berat_do }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-4">
                                                            <div class="form-group">
                                                                <label for="total_do" class="col-form-label">Total DO</label>
                                                                <input type="number" step="0.01" class="form-control cekform" id="total_do" name="total_do" placeholder="input Total DO" value="{{ $produksi->sc_ekor_do }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-4">
                                                            <div class="form-group">
                                                                <label for="ekoran_lpah" class="col-form-label">Ekoran LPAH</label>
                                                                <input type="number" step="0.01" class="form-control cekform" id="ekoran_seckle" name="ekoran_seckle" placeholder="input Total DO" value="{{ $produksi->ekoran_seckle }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Keluar</button>
                                                <button type="button" class="btn btn-primary updatedatalpah" disabled="disabled"><i class="fa fa-spinner fa-spin spinerloading" style="display:none;"></i> Update</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h5>Timbang LPAH</h5>

                                <div>
                                    @php
                                        $isi = 0;
                                        $kosong = 0;
                                        $kr_null = 0;
                                        $kr_isi = 0;
                                    @endphp
                                    @foreach ($produksi->prodlpah as $list)
                                        @php
                                            if ($list->type == 'isi') {
                                                $isi += $list->berat;
                                                $kr_isi += $list->berat > 0 ? 1 : 0;
                                            } else {
                                                $kosong += $list->berat;
                                                $kr_null += $list->berat > 0 ? 1 : 0;
                                            }
                                        @endphp
                                    @endforeach
                                    <div class="row mb-3">
                                        <div class="col-lg-6 col-12 pr-lg-1">
                                            {{ $kr_isi }}x Timbang Keranjang Isi<br>
                                            {{ $kr_null }}x Timbang Keranjang Kosong
                                        </div>
                                        <div class="col-lg-6 col-12 pl-lg-1">
                                            <table>
                                                <tbody>
                                                    <tr>
                                                        <td>Berat Isi</td>
                                                        <td class="text-center" style="width: 20px">:</td>
                                                        <td class="text-right">{{ number_format($isi, 2) }}
                                                            Kg</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Berat Kosong</td>
                                                        <td class="text-center">:</td>
                                                        <td class="text-right">
                                                            {{ number_format($kosong, 2) }} Kg</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="outer" style="max-height: 450px; overflow:scroll">

                                        <table class="table table-sm default-table" id="formlpah">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Total</th>
                                                    <th class="text-center">Tipe</th>
                                                    @if(count($ns) < 1)
                                                        @if(Auth::user()->account_role == 'superadmin' or User::setijin(33))
                                                            <th class="text-center">Edit</th>
                                                        @endif
                                                    @else
                                                        @if(Auth::user()->account_role == 'superadmin')
                                                            <th class="text-center">Edit</th>
                                                        @endif
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($produksi->prodlpah as $row)
                                                    @if ($row->berat > 0)
                                                        <tr>
                                                            <td class="text-center">
                                                                {{ number_format($row->berat, 2) }}</td>
                                                            <td class="text-center text-capitalize">
                                                                {{ $row->type }}</td>
                                                            @if(count($ns) < 1)
                                                                @if(Auth::user()->account_role == 'superadmin' or User::setijin(33))
                                                                <td class="text-center">
                                                                    <a href="{{ url('admin/produksi/'.$row->id.'/edit') }}" class="btn btn-primary btn-sm p-0 px-1 editLpah" data-toggle="modal" data-target="#modalLpah" title="Edit Timbang LPAH" data-key= "editlpah" data-id="{{ $row->id }}" data-jenis="lpah">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>
                                                                    @php
                                                                        $cek = App\Models\Adminedit::where('table_id',$row->id)->where('table_name','lpah')->count();
                                                                    @endphp
                                                                    @if ($cek > 0)
                                                                        
                                                                    <a class="btn btn-warning btn-sm p-0 px-1 history_timbang_checker" title="History Data Timbang" data-toggle="modal" data-target="#modalTimbang" data-id ="{{$row->id}}"><i class="fa fa-eye"></i></a>
                                                                    {{-- <button class="btn btn-warning btn-sm p-0 px-1" data-id ="{{$row->id}}" onclick="history_timbang_checker(`{{$row->id}}`)">
                                                                        <i class="fa fa-eye"></i>
                                                                    </button> --}}
                                                                    @endif
                                                                </td>
                                                                @endif
                                                            @else
                                                                @if(Auth::user()->account_role == 'superadmin')
                                                                <td class="text-center">
                                                                    <a href="{{ url('admin/produksi/'.$row->id.'/edit') }}" class="btn btn-primary btn-sm p-0 px-1 editLpah" data-toggle="modal" data-target="#modalLpah" title="Edit Timbang LPAH" data-key= "editlpah" data-id="{{ $row->id }}" data-jenis="lpah">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>
                                                                    @php
                                                                        $cek = App\Models\Adminedit::where('table_id',$row->id)->where('table_name','lpah')->count();
                                                                    @endphp
                                                                    @if ($cek > 0)
                                                                        
                                                                    <a class="btn btn-warning btn-sm p-0 px-1 history_timbang_checker" title="History Data Timbang" data-toggle="modal" data-target="#modalTimbang" data-id ="{{$row->id}}"><i class="fa fa-eye"></i></a>
                                                                    {{-- <button class="btn btn-warning btn-sm p-0 px-1" data-id ="{{$row->id}}" onclick="history_timbang_checker(`{{$row->id}}`)">
                                                                        <i class="fa fa-eye"></i>
                                                                    </button> --}}
                                                                    @endif
                                                                </td>
                                                                @endif
                                                            @endif
                    

                                                        </tr>
                                                        {{-- <div class="modal fade" id="modal{{ $row->id }}"
                                                            tabindex="-1" aria-labelledby="modal{{ $row->id }}Label"
                                                            aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-body">
                                                                        <div class="form-group text-center">
                                                                            <b>EDIT DATA</b>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <div class="form-group">
                                                                                <div class="small">Tipe
                                                                                </div>
                                                                                <select name="tipe_timbang"
                                                                                    class="form-control"
                                                                                    id="tipe_timbang{{ $row->id }}">
                                                                                    <option value="isi"
                                                                                        {{ $row->type == 'isi' ? 'selected' : '' }}>
                                                                                        Isi</option>
                                                                                    <option value="kosong"
                                                                                        {{ $row->type == 'kosong' ? 'selected' : '' }}>
                                                                                        Kosong</option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col">
                                                                                    <div class="form-group">
                                                                                        Berat
                                                                                        <input type="number"
                                                                                            id="berat{{ $row->id }}"
                                                                                            name="berat"
                                                                                            class="form-control"
                                                                                            value="{{ $row->berat }}"
                                                                                            autocomplete="off">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group text-center">
                                                                            <button type="button" class="btn btn-secondary"
                                                                                data-dismiss="modal">Close</button>
                                                                            <button type="button"
                                                                                data-id="{{ $row->id }}"
                                                                                class="edit_cart btn btn-primary">Save
                                                                                changes</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div> --}}

                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>

                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="card mt-3">
                            <div class="card-body">
                                <div class="mb-3">

                                    @php
                                        $rendemen_data  = App\Models\Production::where('id', $produksi->id)->first();
                                    @endphp

                                    @php
                                        $grading_berat = 0;
                                        $grading_item = 0;
                                        $evis_berat = 0;
                                        $evis_ekor = 0;
                                    @endphp

                                    @php
                                        $summary = \App\Models\Grading::where('trans_id', $produksi->id)
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
                                        $evis = \App\Models\Evis::where('production_id', $produksi->id)->get();
                                        $evis_berat = 0;
                                        $evis_ekor = 0;

                                        foreach ($evis as $evs) {
                                            $evis_ekor += $evs->stock_item;
                                            $evis_berat += $evs->berat_stock;
                                        }
                                    @endphp
                                    

                                    <h5> <span class="status status-info mt-2">Benchmark Yield Produksi Ukuran : {{ $produksi->prodpur->ukuran_ayam }}</span>  <br> <span class="status status-info mt-2">Kategori : {{ $produksi->prodpur->purchasing_item[0]->jenis_ayam ?? '' }}  </span> </h5>
                                    @php
                                        $getDataYield = App\Models\Adminedit::where('activity', 'input_yield')->where('content', $produksi->prodpur->purchasing_item[0]->jenis_ayam )->where('type', $produksi->prodpur->ukuran_ayam)->first();
                                        if ($getDataYield) {
                                            $decodeYield = json_decode($getDataYield->data);
                                        }

                                    @endphp
                                    @if ($getDataYield) 
                                        <div class="alert alert-success">Yield Karkas:  {{ $decodeYield->yield_karkas ?? '-' }}</div>
                                        <div class="alert alert-info">Yield Evis:  {{ $decodeYield->yield_evis ?? '-' }}</div>
                                    @else
                                        <div class="alert alert-danger"><b>Benchmark belum diinput, silahkan input terlebih dahulu </b></div>

                                    @endif

                                    @if ($getDataYield) 

                                        @php
                                        if ($getDataYield) {
                                            $dataPecahYieldKarkas = (explode(" - ", $decodeYield->yield_karkas));
                                            $dataPecahYieldEvis   = (explode(" - ", $decodeYield->yield_evis));
                                        }
                                        @endphp
                                        @if ($dataPecahYieldEvis[0] && $dataPecahYieldEvis[1] && $dataPecahYieldKarkas[0] && $dataPecahYieldKarkas[1])
                                        <h5>Informasi Yield Produksi</h5>
                                        
                                        <div class="card mb-2">
                                            <div class="card-header">
                                                Mobil {{ $produksi->no_urut }}
                                                @if($produksi->grading_status==1 && $produksi->evis_status==1)
                                                <span class="status status-info"> Selesai </span>
                                                @else
                                                <span class="status status-danger"> Proses </span>
                                                @endif
                                            </div>
                                            <div class="card-body p-2">
                                                <div class="border-bottom p-1">
                                                    <span class="float-right font-weight-bold">
                                                        @if ($produksi->ekoran_seckle != 0)
                                                            {{ number_format($produksi->lpah_berat_terima / $produksi->ekoran_seckle, 2) ?? '###' }}
                                                        @endif
                                                    </span>
                                                    Rataan Terima
                                                </div>
                                                <div class="border-bottom p-1">
                                                    @php
                                                        $yield_produksi = $produksi->prod_yield_produksi ;
                                                    @endphp


                                                    {{-- PISAH BENCHMARK DARI ADMIN EDIT --}}
                                                    @if ($getDataYield)

                                                        @if (env('NET_SUBSIDIARY') == 'EBA')
                                                        <span class="float-right font-weight-bold @if($yield_produksi>= str_replace('%','', $dataPecahYieldKarkas[0]) && $yield_produksi<= str_replace('%','', $dataPecahYieldKarkas[1])) green @else red @endif">{{ number_format($yield_produksi, 2) }} %</span>
                                                        {{-- <span class="float-right font-weight-bold @if($yield_produksi>=73 && $yield_produksi<=75) green @else red @endif">{{ number_format($yield_produksi, 2) }} %</span> --}}
                                                        @else
                                                        <span class="float-right font-weight-bold @if($yield_produksi>= str_replace('%','', $dataPecahYieldKarkas[0]) && $yield_produksi<= str_replace('%','', $dataPecahYieldKarkas[1])) green @else red @endif">{{ number_format($yield_produksi, 2) }} %</span>
                                                        @endif
                                                        Yield Produksi
                                                    @endif
                                                </div>

                                                <div class="border-bottom p-1">
                                                    @php
                                                        if($produksi->lpah_berat_terima != 0){
                                                            $yield_evis = ($evis_berat / $produksi->lpah_berat_terima) * 100;
                                                        } else {
                                                            $yield_evis = 0 ;
                                                        };
                                                    @endphp
                                                    @if ($getDataYield)
                                                    {{-- {{dd($dataPecahYieldEvis, $geTd)}} --}}
                                                        @if ($dataPecahYieldEvis[0] && $dataPecahYieldEvis[1])
                                                        <span class="float-right font-weight-bold @if($yield_evis>= str_replace('%','', $dataPecahYieldEvis[0]) && $yield_evis<= str_replace('%','', $dataPecahYieldEvis[1])) green @else red @endif ">{{ number_format($yield_evis, 2) }} %</span>
                                                        {{-- <span class="float-right font-weight-bold @if($yield_evis>=20 && $yield_evis<=22) green @else red @endif ">{{ number_format($yield_evis, 2) }} %</span> --}}
                                                        Yield Evis
                                                        @else
                                                        <div class="alert alert-danger"><b>Benchmark Yield Evis belum diinput, silahkan input terlebih dahulu </b></div>
                                                        @endif
                                                    @endif
                                                </div>
                                                <div class="border-bottom p-1">
                                                    <span class="float-right font-weight-bold">
                                                        @if (env('NET_SUBSIDIARY') == 'EBA')
                                                            @if ($produksi->prodpur->type_ekspedisi == 'tangkap')
                                                                {{ number_format($produksi->sc_berat_do != 0 ? ($grading_berat / $produksi->sc_berat_do) * 100 : '0', 2) }}
                                                            @else
                                                                {{ number_format($produksi->lpah_berat_terima != 0 ? ($grading_berat / $produksi->lpah_berat_terima) * 100 : '0', 2) }}
                                                            @endif
                                                            %
                                                        @else
                                                            @if ($produksi->prodpur->type_ekspedisi == 'tangkap')
                                                                <span class="float-right font-weight-bold @if($produksi->sc_berat_do != 0 ? ($grading_berat / $produksi->sc_berat_do) * 100 : '0' >= 72) green @else red @endif">{{ number_format($produksi->sc_berat_do != 0 ? ($grading_berat / $produksi->sc_berat_do) * 100 : '0',2) }} %</span>
                                                            @else
                                                                <span class="float-right font-weight-bold @if($produksi->lpah_berat_terima != 0 ? ($grading_berat / $produksi->lpah_berat_terima) * 100 : '0' >= 68) green @else red @endif">{{ number_format($produksi->lpah_berat_terima != 0 ? ($grading_berat / $produksi->lpah_berat_terima) * 100 : '0', 2) }} %</span>
                                                            @endif
                                                        @endif
                                                    </span>
                                                    Rendemen
                                                </div>
                                            </div>
                                        </div>
                                        @else
                                        <div class="alert alert-danger"><b>Benchmark Karkas/Evis belum diinput, silahkan input terlebih dahulu </b></div>
                                        @endif
                                    @endif
                                </div>
                                @php
                                    $isTrueKeterangan = true;
                                @endphp
                                @if ($getDataYield)
                                    @if ($dataPecahYieldEvis[0] && $dataPecahYieldEvis[1] && $dataPecahYieldKarkas[0] && $dataPecahYieldKarkas[1])
                                        @if (env('NET_SUBSIDIARY') == 'EBA')
                                        
                                            @if($yield_produksi>= str_replace('%','', $dataPecahYieldKarkas[0]) && $yield_produksi<= str_replace('%','', $dataPecahYieldKarkas[1]) && $yield_evis>=str_replace('%','', $dataPecahYieldEvis[0]) && $yield_evis<=str_replace('%','', $dataPecahYieldEvis[1]))
                                                <div class="alert alert-success">BENCHMARK SUDAH SESUAI</div>
                                            @else
                                                {{-- JIKA BENCHMARK SUDAH DIISI --}}
                                                @if($produksi->keterangan_benchmark != NULL)
                                                <div class="alert alert-success">BENCHMARK SUDAH DIISI</div>
                                                @else
                                                <div class="alert alert-danger"><b>BENCHMARK TIDAK SESUAI, SILAHKAN INPUT KETERANGAN</b></div>
                                                @endif
                                            @php
                                                $isTrueKeterangan = false;
                                            @endphp
                                            <form action="{{ route('checker.create_itemreceipt_wo1') }}" method="POST">
                                                @csrf
                                                <div class="form-group">
                                                    Keterangan
                                                    <textarea name="keterangan_benchmark" class="form-control background-grey-2" placeholder="Tulis Keterangan" cols="3">{{ $produksi->keterangan_benchmark }}</textarea>
                                                </div>
                                                <input name="id" value="{{ $produksi->id }}" type="hidden">
                                                <input name="jenis" value="keterangan_benchmark" type="hidden">
                                                <button type="submit" class="btn btn-success btn-block"
                                                    onclick="return confirm('Submit keterangan benchmark??')">Submit </button>
                                            </form>
                                            @endif
                                        @else
                                            @if($yield_produksi>= str_replace('%','', $dataPecahYieldKarkas[0]) && $yield_produksi<= str_replace('%','', $dataPecahYieldKarkas[1]) && $yield_evis>=str_replace('%','', $dataPecahYieldEvis[0]) && $yield_evis<=str_replace('%','', $dataPecahYieldEvis[1]))
                                                <div class="alert alert-success">BENCHMARK SUDAH SESUAI</div>
                                            @else
                                                {{-- JIKA BENCHMARK SUDAH DIISI --}}
                                                @if($produksi->keterangan_benchmark != NULL)
                                                <div class="alert alert-success">BENCHMARK SUDAH DIISI</div>
                                                @else
                                                <div class="alert alert-danger"><b>BENCHMARK TIDAK SESUAI, SILAHKAN INPUT KETERANGAN</b></div>
                                                @endif
                                            @php
                                                $isTrueKeterangan = false;
                                            @endphp
                                            <form action="{{ route('checker.create_itemreceipt_wo1') }}" method="POST">
                                                @csrf
                                                <div class="form-group">
                                                    Keterangan
                                                    <textarea name="keterangan_benchmark" class="form-control background-grey-2" placeholder="Tulis Keterangan" cols="3">{{ $produksi->keterangan_benchmark }}</textarea>
                                                </div>
                                                <input name="id" value="{{ $produksi->id }}" type="hidden">
                                                <input name="jenis" value="keterangan_benchmark" type="hidden">
                                                <button type="submit" class="btn btn-success btn-block"
                                                    onclick="return confirm('Submit keterangan benchmark??')">Submit </button>
                                            </form>

                                            @endif
                                        @endif
                                        @endif
                                @else
                                <div class="alert alert-danger"><b>Benchmark belum diinput, silahkan input terlebih dahulu </b></div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5>Grading</h5>

                                @if ($produksi->grading_status == 2)
                                    <div class="alert alert-info">Proses Input Belum Diselesaikan</div>
                                @elseif ($produksi->grading_status == 1)
                                    <div class="alert alert-success">Proses Grading Selesai</div>
                                @endif

                                @if(count($ns) < 1)
                                    @if(Auth::user()->account_role == 'superadmin' or User::setijin(33))
                                        <div class="form-group">
                                            <button type="button" class="btn btn-primary btn-sm p-0 px-1" data-toggle="modal"
                                                data-target="#tambah">Tambah Item
                                            </button>
                                        </div>
                                    @endif
                                @else
                                    @if(Auth::user()->account_role == 'superadmin')
                                        <div class="form-group">
                                            <button type="button" class="btn btn-primary btn-sm p-0 px-1" data-toggle="modal"
                                                data-target="#tambah">Tambah Item
                                            </button>
                                        </div>
                                    @endif
                                @endif

                                <div class="modal fade" id="tambah" data-backdrop="tambah" data-keyboard="false"
                                    tabindex="-1" aria-labelledby="tambahLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Tambah Data Grading</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="{{ route('checker.addgrading') }}" method="post">
                                                <div class="modal-body">
                                                    @csrf <input type="hidden" name="idproduksi"
                                                        value="{{ $produksi->id }}"">
                                                        <div class="  form-group">
                                                    <label for="">Item</label>
                                                    <select name="item" class="form-control select2" id="item"
                                                        data-placeholder="Pilih Item" data-width="100%">
                                                        <option value=""></option>
                                                        @foreach ($item as $it)
                                                            <option value="{{ $it->id }}">{{ $it->nama }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label for="">Ekor/Pcs/Pack</label>
                                                    <input type="text" name="qty" class="form-control" id="qty"
                                                        placeholder="Tuliskan " value="" autocomplete="off">
                                                </div>
                                                <div class="form-group">
                                                    <label for="">Berat</label>
                                                    <input type="text" name="berat" class="form-control" id="berat"
                                                        placeholder="Tuliskan " value="" autocomplete="off">
                                                </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <table class="table table-sm default-table" id="tablegrading">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Nama</th>
                                        <th class="text-center">Ekor/Pcs/Pack</th>
                                        <th class="text-center">Berat</th>
                                        <th class="text-center">Rataan</th>
                                        <th class="text-center">Ket</th>
                                        @if(count($ns) < 1)
                                            @if($produksi->grading_status == 2 or Auth::user()->account_role == 'superadmin' or User::setijin(33))
                                                <th style="width: 70px" class="text-center">Aksi</th>
                                            @endif
                                        @else
                                            @if(Auth::user()->account_role == 'superadmin')
                                                <th style="width: 70px" class="text-center">Aksi</th>
                                            @endif
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $ekor = 0;
                                        $berat = 0;
                                    @endphp
                                    @foreach ($produksi->prodgrad as $i => $row)
                                        @php
                                            $ekor += $row->total_item;
                                            $berat += $row->berat_item;
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ ++$i }}</td>
                                            <td class="text-center">{{ $row->graditem->nama ?? '###' }}</td>
                                            <td class="text-center">{{ number_format($row->total_item) }}</td>
                                            <td class="text-center">{{ number_format($row->berat_item, 2) }}</td>
                                            <td class="text-center">
                                            @if (($row->berat_item > 0) AND ($row->total_item > 0))
                                            @php
                                                $hitung_rerata  =   ($row->berat_item / $row->total_item) ;
                                            @endphp
                                                {{ number_format($hitung_rerata, 2) }}

                                                @if (substr($row->graditem->nama, -5) == '03-04')
                                                    @if (($hitung_rerata >= 0.30) && ($hitung_rerata <= 0.40))
                                                        <span class='status status-success'>Sesuai</span>
                                                    @else
                                                        <span class='status status-danger'>Cek Kembali</span>
                                                    @endif
                                                @endif
                                                @if (substr($row->graditem->nama, -5) == '04-05')
                                                    @if (($hitung_rerata >= 0.40) && ($hitung_rerata <= 0.50))
                                                        <span class='status status-success'>Sesuai</span>
                                                    @else
                                                        <span class='status status-danger'>Cek Kembali</span>
                                                    @endif
                                                @endif
                                                @if (substr($row->graditem->nama, -5) == '05-06')
                                                    @if (($hitung_rerata >= 0.50) && ($hitung_rerata <= 0.60))
                                                        <span class='status status-success'>Sesuai</span>
                                                    @else
                                                        <span class='status status-danger'>Cek Kembali</span>
                                                    @endif
                                                @endif
                                                @if (substr($row->graditem->nama, -5) == '06-07')
                                                    @if (($hitung_rerata >= 0.60) && ($hitung_rerata <= 0.70))
                                                        <span class='status status-success'>Sesuai</span>
                                                    @else
                                                        <span class='status status-danger'>Cek Kembali</span>
                                                    @endif
                                                @endif
                                                @if (substr($row->graditem->nama, -5) == '07-08')
                                                    @if (($hitung_rerata >= 0.70) && ($hitung_rerata <= 0.80))
                                                        <span class='status status-success'>Sesuai</span>
                                                    @else
                                                        <span class='status status-danger'>Cek Kembali</span>
                                                    @endif
                                                @endif
                                                @if (substr($row->graditem->nama, -5) == '08-09')
                                                    @if (($hitung_rerata >= 0.80) && ($hitung_rerata <= 0.90))
                                                        <span class='status status-success'>Sesuai</span>
                                                    @else
                                                        <span class='status status-danger'>Cek Kembali</span>
                                                    @endif
                                                @endif
                                                @if (substr($row->graditem->nama, -5) == '09-10')
                                                    @if (($hitung_rerata >= 0.90) && ($hitung_rerata <= 1.00))
                                                        <span class='status status-success'>Sesuai</span>
                                                    @else
                                                        <span class='status status-danger'>Cek Kembali</span>
                                                    @endif
                                                @endif
                                                @if (substr($row->graditem->nama, -5) == '10-11')
                                                    @if (($hitung_rerata >= 1.00) && ($hitung_rerata <= 1.10))
                                                        <span class='status status-success'>Sesuai</span>
                                                    @else
                                                        <span class='status status-danger'>Cek Kembali</span>
                                                    @endif
                                                @endif
                                                @if (substr($row->graditem->nama, -5) == '11-12')
                                                    @if (($hitung_rerata >= 1.10) && ($hitung_rerata <= 1.20))
                                                        <span class='status status-success'>Sesuai</span>
                                                    @else
                                                        <span class='status status-danger'>Cek Kembali</span>
                                                    @endif
                                                @endif
                                                @if (substr($row->graditem->nama, -5) == '12-13')
                                                    @if (($hitung_rerata >= 1.20) && ($hitung_rerata <= 1.30))
                                                        <span class='status status-success'>Sesuai</span>
                                                    @else
                                                        <span class='status status-danger'>Cek Kembali</span>
                                                    @endif
                                                @endif
                                                @if (substr($row->graditem->nama, -5) == '13-14')
                                                    @if (($hitung_rerata >= 1.30) && ($hitung_rerata <= 1.40))
                                                        <span class='status status-success'>Sesuai</span>
                                                    @else
                                                        <span class='status status-danger'>Cek Kembali</span>
                                                    @endif
                                                @endif
                                                @if (substr($row->graditem->nama, -5) == '14-15')
                                                    @if (($hitung_rerata >= 1.40) && ($hitung_rerata <= 1.50))
                                                        <span class='status status-success'>Sesuai</span>
                                                    @else
                                                        <span class='status status-danger'>Cek Kembali</span>
                                                    @endif
                                                @endif
                                                @if (substr($row->graditem->nama, -5) == '15-16')
                                                    @if (($hitung_rerata >= 1.50) && ($hitung_rerata <= 1.60))
                                                        <span class='status status-success'>Sesuai</span>
                                                    @else
                                                        <span class='status status-danger'>Cek Kembali</span>
                                                    @endif
                                                @endif
                                                @if (substr($row->graditem->nama, -5) == '16-17')
                                                    @if (($hitung_rerata >= 1.60) && ($hitung_rerata <= 1.70))
                                                        <span class='status status-success'>Sesuai</span>
                                                    @else
                                                        <span class='status status-danger'>Cek Kembali</span>
                                                    @endif
                                                @endif
                                                @if (substr($row->graditem->nama, -5) == '17-18')
                                                    @if (($hitung_rerata >= 1.70) && ($hitung_rerata <= 1.80))
                                                        <span class='status status-success'>Sesuai</span>
                                                    @else
                                                        <span class='status status-danger'>Cek Kembali</span>
                                                    @endif
                                                @endif
                                                @if (substr($row->graditem->nama, -5) == '18-19')
                                                    @if (($hitung_rerata >= 1.80) && ($hitung_rerata <= 1.90))
                                                        <span class='status status-success'>Sesuai</span>
                                                    @else
                                                        <span class='status status-danger'>Cek Kembali</span>
                                                    @endif
                                                @endif
                                                @if (substr($row->graditem->nama, -5) == '19-20')
                                                    @if (($hitung_rerata >= 1.90) && ($hitung_rerata <= 2.00))
                                                        <span class='status status-success'>Sesuai</span>
                                                    @else
                                                        <span class='status status-danger'>Cek Kembali</span>
                                                    @endif
                                                @endif
                                                @if (substr($row->graditem->nama, -5) == '20-21')
                                                    @if (($hitung_rerata >= 2.00) && ($hitung_rerata <= 2.10))
                                                        <span class='status status-success'>Sesuai</span>
                                                    @else
                                                        <span class='status status-danger'>Cek Kembali</span>
                                                    @endif
                                                @endif
                                                @if (substr($row->graditem->nama, -5) == '21-22')
                                                    @if (($hitung_rerata >= 2.10) && ($hitung_rerata <= 2.20))
                                                        <span class='status status-success'>Sesuai</span>
                                                    @else
                                                        <span class='status status-danger'>Cek Kembali</span>
                                                    @endif
                                                @endif
                                                @if (substr($row->graditem->nama, -5) == '22-23')
                                                    @if (($hitung_rerata >= 2.20) && ($hitung_rerata <= 2.30))
                                                        <span class='status status-success'>Sesuai</span>
                                                    @else
                                                        <span class='status status-danger'>Cek Kembali</span>
                                                    @endif
                                                @endif
                                                @if (substr($row->graditem->nama, -5) == '23-24')
                                                    @if (($hitung_rerata >= 2.30) && ($hitung_rerata <= 2.40))
                                                        <span class='status status-success'>Sesuai</span>
                                                    @else
                                                        <span class='status status-danger'>Cek Kembali</span>
                                                    @endif
                                                @endif
                                                @if (substr($row->graditem->nama, -5) == '24-25')
                                                    @if (($hitung_rerata >= 2.40) && ($hitung_rerata <= 2.50))
                                                        <span class='status status-success'>Sesuai</span>
                                                    @else
                                                        <span class='status status-danger'>Cek Kembali</span>
                                                    @endif
                                                @endif
                                            @endif
                                            </td>
                                            <td class="text-center">{{ $row->keterangan }}</td>
                                            @if ($produksi->grading_status == 2 or Auth::user()->account_role == 'superadmin' or User::setijin(33))
                                                @if(count($ns) < 1)
                                                    @if(Auth::user()->account_role == 'superadmin' or User::setijin(33))
                                                    <td>
                                                        <div class="text-center">
                                                            <a href="{{ url('admin/produksi/'.$row->id.'/edit')}}" class="btn btn-primary btn-sm p-0 px-1 editGrading" title='Edit'
                                                                data-toggle="modal" data-target="#modalgrading" data-produksi="{{$produksi->id}}" data-id="{{$row->id}}" data-key="editgrading" data-jenis="grading">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                            
                                                            <button class="btn btn-danger btn-sm p-0 px-1 hapus_cart"
                                                            data-id="{{ $row->id }}"><i
                                                                class="fa fa-trash"></i></button>
                                                        </div>
                                                    </td>
                                                    @endif
                                                @else
                                                    @if(Auth::user()->account_role == 'superadmin')
                                                    <td>
                                                        <div class="text-center">
                                                            <a href="{{ url('admin/produksi/'.$row->id.'/edit')}}" class="btn btn-primary btn-sm p-0 px-1 editGrading" title='Edit'
                                                                data-toggle="modal" data-target="#modalgrading" data-produksi="{{$produksi->id}}" data-id="{{$row->id}}" data-key="editgrading" data-jenis="grading">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                            <button class="btn btn-danger btn-sm p-0 px-1 hapus_cart"
                                                            data-id="{{ $row->id }}"><i
                                                                class="fa fa-trash"></i></button>
                                                        </div>
                                                    </td>
                                                    @endif
                                                @endif
                                            @else
                                            <td>
                                                <div class="text-center">
                                                    <div class="text-center">
                                                        <button type="button" class="btn btn-primary btn-sm p-0 px-1"
                                                            data-toggle="modal" data-target="#static{{ $row->id }}">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>   
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th class="text-center" colspan="2">Total</th>
                                        <th class="text-center">{{ number_format($ekor) }}</th>
                                        <th class="text-center">{{ number_format($berat, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                            @foreach ($produksi->prodgrad as $i => $row)
                                <div class="modal fade" id="static{{ $row->id }}" data-backdrop="static"
                                    data-keyboard="false" tabindex="-1" aria-labelledby="static{{ $row->id }}Label"
                                    aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Ubah Data Grading</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="{{ route('grading.ubah', $produksi->id) }}" method="post">
                                                @csrf @method('patch') <input type="hidden" name="x_code" value="{{ $row->id }}">
                                                <input type="hidden" name="checker" value="1">
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        Item
                                                        <div>{{ $row->graditem->nama ?? '###' }}</div>
                                                        {{ $row->id }}
                                                    </div>

                                                    <div class="row">
                                                        <div class="col pr-1">
                                                            <div class="form-group">
                                                                Ekor
                                                                <input type="number" value="{{ $row->total_item }}" name="ekor" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col pl-1">
                                                            <div class="form-group">
                                                                Berat
                                                                <input type="number" value="{{ $row->berat_item }}" name="berat" step="0.01" class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        Keterangan
                                                        <textarea name="keterangan" rows="2" required class="form-control">{{ $row->keterangan }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Ubah</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <hr>

                            <h5>Evis</h5>

                            @if ($produksi->evis_status == 2)
                                <div class="alert alert-info">Proses Input Belum Diselesaikan</div>
                            @elseif ($produksi->evis_status == 1)
                                <div class="alert alert-success">Proses Evis Selesai</div>
                            @endif

                            @if(count($ns) < 1)
                                @if(Auth::user()->account_role == 'superadmin' or User::setijin(33))
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary btn-sm p-0 px-1" data-toggle="modal"
                                        data-target="#tambahevis">Tambah Item Evis
                                    </button>
                                </div>
                                @endif
                            @else
                                @if(Auth::user()->account_role == 'superadmin')
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary btn-sm p-0 px-1" data-toggle="modal"
                                        data-target="#tambahevis">Tambah Item Evis
                                    </button>
                                </div>
                                @endif
                            @endif

                            <div>
                                <div class="modal fade" id="tambahevis" data-backdrop="tambahevis" data-keyboard="false"
                                    tabindex="-1" aria-labelledby="tambahLabelevis" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form action="{{ route('checker.addevis') }}" method="post">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Tambah Data Evis</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                @csrf <input type="hidden" name="idproduksi"
                                                    value="{{ $produksi->id }}"">
                                                <div class="form-group"> 

                                                    <label for="">Item</label>
                                                    <select name="item" class="form-control" id="item"
                                                    data-placeholder="Pilih Item" data-width="100%">
                                                    @php
                                                        if ($produksi->prodpur->purchasing_item[0]->keterangan == 'AYAM HIDUP PEJANTAN (RM)') {
                                                            $evis =DataOption::getOption('evis_pejantan');
                                                            $data_evis = explode(',', $evis);
                                                        } else if ($produksi->prodpur->purchasing_item[0]->keterangan == 'AYAM HIDUP PARENT (RM)') {
                                                            $evis =DataOption::getOption('evis_parent');
                                                            $data_evis = explode(',', $evis);
                                                        } else if ($produksi->prodpur->purchasing_item[0]->keterangan == 'AYAM HIDUP KAMPUNG (RM)') {
                                                            $evis =DataOption::getOption('evis_kampung');
                                                            $data_evis = explode(',', $evis);
                                                        } else {
                                                            $evis = DataOption::getOption('evis_' . ($produksi->prodpur->jenis_ayam ?? 'broiler'));
                                                            $data_evis = explode(',', $evis);
                                                        }
                                                    @endphp
                                                    @for ($i = 0; $i < count($data_evis); $i++)
                                                    @php
                                                            $item = App\Models\Item::where('sku', (int) $data_evis[$i])->first();
                                                            @endphp
                                                        <option value="{{ $item->id ?? '' }}">{{ $item->nama }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label for="">Ekor/Pcs/Pack</label>
                                                    <input type="text" name="qty" class="form-control" id="qtyEvis"
                                                        placeholder="Tuliskan " value="" autocomplete="off">
                                                </div>
                                                <div class="form-group">
                                                    <label for="">Berat</label>
                                                    <input type="text" name="berat" class="form-control" id="beratEvis"
                                                        placeholder="Tuliskan " value="" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary simpan_evis">Simpan</button>
                                            </div>
                                        </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <table class="table default-table" id="tableevis">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Nama</th>
                                        <th class="text-center">Ekor/Pcs/Pack</th>
                                        <th class="text-center">Berat Bersih</th>
                                        <th class="text-center">Hitung</th>
                                        @if(count($ns) < 1)
                                            @if($produksi->evis_status == 2 or Auth::user()->account_role == 'superadmin' or User::setijin(33))
                                            <th class="text-center">Aksi</th>
                                            @endif
                                        @else
                                            @if(Auth::user()->account_role == 'superadmin')
                                            <th class="text-center">Aksi</th>
                                            @endif
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $pcs    =   0 ;
                                        $bb     =   0 ;
                                    @endphp
                                    @foreach ($produksi->prodevis as $i => $row)
                                    @php
                                        $pcs    +=  $row->total_item ;
                                        $bb     +=  $row->berat_stock ;
                                    @endphp
                                        <tr>
                                            <td class="text-center">{{ ++$i }}</td>
                                            <td class="text-center">{{ $row->eviitem->nama }}</td>
                                            <td class="text-center">{{ $row->total_item }}</td>
                                            <td class="text-center">{{ $row->berat_stock }}</td>
                                            <td class="text-center">{{ $row->jenis_evis }}</td>
                                            {{-- @if ($produksi->evis_status == 2) --}}
                                            @if(count($ns) < 1)
                                                @if(Auth::user()->account_role == 'superadmin' or User::setijin(33))
                                                    <td class="text-center">
                                                        <div style="width:70px">
                                                            <a href="{{ url('admin/produksi/'.$row->id.'/edit') }}" class="btn btn-primary btn-sm p-0 px-1 editevis" data-toggle="modal" data-target="#modalEvis" data-jenis="evis" data-id="{{$row->id}}"  data-key="editevis">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                            <button class="btn btn-danger btn-sm p-0 ml-2 hapus_evis" data-id="{{ $row->id }}">
                                                                <div class="fa fa-trash-o"></div>
                                                            </button>
                                                        </div>
                                                    </td>
                                                @endif
                                            @else
                                            @if(Auth::user()->account_role == 'superadmin')
                                            <td class="text-center">
                                                <div style="width:70px">
                                                        <a href="{{ url('admin/produksi/'.$row->id.'/edit') }}" class="btn btn-primary btn-sm p-0 px-1 editevis" data-toggle="modal" data-target="#modalEvis" data-jenis="evis" data-id="{{$row->id}}"  data-key="editevis">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        <button class="btn btn-danger btn-sm p-0 ml-2 hapus_evis" data-id="{{ $row->id }}">
                                                            <div class="fa fa-trash-o"></div>
                                                        </button>
                                                    </div>
                                                </td>
                                                @endif
                                            @endif
                                            {{-- @elseif(User::setIjin('superadmin') or User::setijin(33))
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-primary btn-sm p-0 px-1" data-toggle="modal" data-target="#modal{{ $row->id }}">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                </td>
                                            @endif --}}
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2">Total</th>
                                        <th class="text-center">{{ number_format($pcs) }}</th>
                                        <th class="text-center">{{ number_format($bb, 2) }}</th>
                                        <th colspan="2"></th>
                                    </tr>
                                </tfoot>
                            </table>

                            {{-- @foreach ($produksi->prodevis as $i => $row)
                                <div class="modal fade" id="modal{{ $row->id }}" tabindex="-1"
                                    aria-labelledby="modal{{ $row->id }}Label" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form action="{{ route('evis.editevis') }}" method="post">
                                            @csrf <input type="hidden" name="key" value="checker">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modal{{ $row->id }}Label">EDIT
                                                        EVIS</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="idedit" value="{{ $row->id }}">
                                                    <label for="">Item : {{ $row->eviitem->nama }}</label>
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="form-group">
                                                                QTY
                                                                <input type="number" name="qty" class="form-control"
                                                                    value="{{ $row->total_item }}">
                                                            </div>
                                                        </div>

                                                        <div class="col">
                                                            <div class="form-group">
                                                                BERAT
                                                                <input type="number" name="berat" class="form-control"
                                                                    value="{{ $row->berat_item }}" step="0.01" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">OK</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endforeach --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (COUNT($track))
            <div class="card mb-4">
                <div class="card-header">
                    Riwayat Perubahan Checker
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table default-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tipe</th>
                                    <th>Aktivitas</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($track as $i => $row)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ $row->type }}</td>
                                        <td>{{ $row->content }}</td>
                                        <td><button class="btn btn-primary" data-toggle="modal"
                                                data-target="#data{{ $row->id }}">Data Json</button></td>
                                    </tr>
                                    <div class="modal fade" id="data{{ $row->id }}" data-backdrop="static"
                                        data-keyboard="false" tabindex="-1" aria-labelledby="data{{ $row->id }}Label"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="data{{ $row->id }}Label">Riwayat
                                                        Perubahan Checker</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-2"><b>{{ $row->content }}</b></div>
                                                    <div class="border p-2">
                                                        <pre id="beautified{{ $row->id }}"></pre>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <script>
                                        document.getElementById("beautified{{ $row->id }}").innerHTML = JSON.stringify({!! $row->data !!},
                                            undefined, 2);
                                    </script>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <h5>Netsuite Status</h5>
                @if ($produksi->lpah_netsuite_status == null)
                    <div class="alert alert-danger">Item receipt belum diproses</div>
                @else
                    <div class="alert alert-success">Item receipt telah terkirim</div>
                @endif

                @if ($produksi->wo_netsuite_status == null)
                    <div class="alert alert-danger">WO belum diproses</div>
                @else
                    <div class="alert alert-success">WO telah terkirim</div>
                @endif

                @if ($produksi->wo_netsuite_status == null)
                    <div class="alert alert-danger">NETSUITE BELUM ADA</div>
                @else
                    <div id="netsiutelog"></div>
                @endif

                <hr>
                @if($isTrueKeterangan == true || $produksi->keterangan_benchmark != NULL)
                    @if ($produksi->wo_netsuite_status == null || $produksi->lpah_netsuite_status == null)
                        <form action="{{ route('checker.create_itemreceipt_wo1') }}" method="POST">
                            @csrf
                            <input name="id" value="{{ $produksi->id }}" type="hidden">
                            <button type="submit" class="btn btn-success btn-block"
                                onclick="return confirm('Kirim data ke netsuite?')">Kirim Item receipt & WO1 </button>
                        </form>
                    @endif
                @endif



            </div>
        </div>
    </div>

    {{-- modal lpah edit --}}
    <div class="modal fade" id="modalgrading" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalgradingLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-body" id="content_modal"></div>
        </div>
    </div>

    {{-- modal history edit --}}
    <div class="modal fade" id="modalHistory" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalHistoryLabel" aria-hidden="false">
        <div class="modal-dialog modal-lg" style="width: 800px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group text-center">
                        <b>HISTORY EDIT DATA</b>
                    </div>
                    <div id="spinerhistory" class="text-center" style="display: none">
                        <img src="{{ asset('loading.gif') }}" width="30px">
                    </div>
                    <div id="content_modal_history"></div>
                </div>
            </div>
        </div>
    </div>
    {{-- modal HISTORY Timbang LPAH --}}
    <div class="modal fade" id="modalTimbang" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalTimbangLabel" aria-hidden="false">
        <div class="modal-dialog modal-lg" style="width: 800px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="spinerhistorytimbang" class="text-center" style="display: none">
                        <img src="{{ asset('loading.gif') }}" width="30px">
                    </div>
                    <div id="content_modal_timbang"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- modal EDIT Timbang LPAH --}}
    <div class="modal fade" id="modalLpah" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalLpahLabel" aria-hidden="false">
        <div class="modal-dialog">
            <div id="content_modal_lpah"></div>
        </div>
    </div>

    {{-- modal EDIT EVIS --}}
    <div class="modal fade" id="modalEvis" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalEvisLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div id="content_modal_evis"></div>
        </div>
    </div>


    <script>
        $('#netsiutelog').load("{{ route('checker.netsuite', $produksi->id) }}");
        $('.select2').select2({
            dropdownParent: $("#tambah"),
            theme: 'bootstrap4',
        })

        

        $(document).ready(function() {
            // Edit cart
            $(document).on('click', '.edit_cart', function() {
                var row_id = $(this).data('id');
                var tipe_timbang = $('#tipe_timbang' + row_id).val();
                var berat = $('#berat' + row_id).val();
                var keranjang = $('#keranjang' + row_id).val();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });


                $.ajax({
                    url: "{{ route('lpah.update', $produksi->id) }}",
                    method: "PATCH",
                    data: {
                        row_id: row_id,
                        berat: berat,
                        keranjang: keranjang,
                        tipe_timbang: tipe_timbang,
                        key: 'editkeranjang',
                        act: 'checker'
                    },
                    success: function(data) {
                        if (data.status == 400) {
                            showAlert(data.msg);
                        } else {
                            showNotif(data.msg);
                            location.reload();
                        }
                    }
                });

            });

            $(".editLpah").click(function (e) {
                e.preventDefault();
                var id      = $(this).data('id');
                var key     = $(this).data('key');
                var jenis   = $(this).data('jenis');
                var href    = $(this).attr('href');

                $.ajax({
                    url : href,
                    type: "GET",
                    data: {
                        id      : id,
                        key     : key,
                        jenis   : jenis
                    },
                    success: function(data){
                        $('#content_modal_lpah').html(data);
                    }
                });
            });
            $(".editGrading").click( function(e){
                e.preventDefault();
                var id       = $(this).data('id');
                var key      = $(this).data('key');
                var produksi = $(this).data('produksi');
                var jenis    = $(this).data('jenis');
                var href     = $(this).attr('href');

                $.ajax({
                    url : href,
                    type: "GET",
                    data: {
                        id          : id,
                        key         : key,
                        jenis       : jenis,
                        produksi    : produksi
                    },
                    success: function(data){
                        $('#content_modal').html(data);
                    }
                });
            });

            $(".editevis").click(function (e) {
                e.preventDefault();
                var id      = $(this).data('id');
                var key     = $(this).data('key');
                var jenis   = $(this).data('jenis');
                var href    = $(this).attr('href');

                $.ajax({
                    url : href,
                    type: "GET",
                    data: {
                        id      : id,
                        jenis   : jenis,
                        key     : 'editevis'
                    },
                    success: function(data){
                        $('#content_modal_evis').html(data);
                    }
                });
            
            });
        });

        $(document).ready(function() {
            $(document).on('click', '.hapus_cart', function() {
                var id = $(this).data('id');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "{{ route('grading.destroy', $produksi->id) }}",
                    method: "DELETE",
                    data: {
                        id: id,
                        key: 'checker'
                    },
                    success: function(data) {
                        window.location.reload()
                    }
                });
            });
        });

    </script>

    <script>
        $(".hapus_evis").on('click', function() {
            var id  =   $(this).data('id') ;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('evis.deleteitem', $produksi->id) }}",
                method: "DELETE",
                data: {
                    x_code: id
                },
                success: function(data) {
                    window.location.reload()
                    // console.log(data)

                }
            });
        });

        $(".history_timbang_checker").on('click',function(){
            var id = $(this).data('id');
            $.ajax({
                url: "{{route('lpah.index')}}",
                type: "GET",
                cache:false,
                data: {
                    id: id,
                    key: 'history_timbang_checker'
                },
                beforeSend:function(){
                    $("#spinerhistorytimbang").show();
                },
                success: function(data){
                    $('#content_modal_timbang').html(data);
                    $("#spinerhistorytimbang").hide();
                }
            });
        })
        $(".loghistory").on('click',function(){
            var id = $(this).data('id');
            $.ajax({
                url: "{{route('lpah.index')}}",
                type: "GET",
                cache:false,
                data: {
                    id: id,
                    key: 'history_lpah_cheker'
                },
                beforeSend:function(){
                    $("#spinerhistory").show();
                },
                success: function(data){
                    $('#content_modal_history').html(data);
                    $("#spinerhistory").hide();
                }
            });
        })

        $('.simpan_evis').on('click', function(e) {
            if ($('#beratEvis').val() == '' && $('#qtyEvis').val() == '') {
                e.preventDefault();
                showAlert('Lengkapi data');
            } else {
                $('.simpan_evis').submit();
                $('.simpan_evis').hide();
            }
        })
        $('input.cekform').keyup(function() {
            var empty = false;
            $('input.cekform').each(function() {
                if ($(this).val() == '' || $("#berat_do,#total_do").val().indexOf(" ") < 0 === false) {
                    empty = true;
                }
            });

            if (empty) {
                $('.updatedatalpah').attr('disabled', 'disabled');
            } else {
                $('.updatedatalpah').removeAttr('disabled');
            }
        });

        $(".updatedatalpah").on('click', function(){
            var id          = "{{ $produksi->id }}";
            var berat_do    = $("#berat_do").val();
            var total_do    = $("#total_do").val();
            var ekoran_seckle = $("#ekoran_seckle").val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            if(berat_do === '' || berat_do === undefined){
                showAlert('Berat harus di isi');
                return false;
            }
            if(total_do === '' || total_do === undefined){
                showAlert('Total DO harus di isi');
                return false;
            }
            if (ekoran_seckle === '' || ekoran_seckle === undefined) {
                showAlert('Ekoran LPAH harus di isi');
                return false;   
            }
            $.ajax({
                url : "{{ route('lpah.updatedata') }}",
                method: "POST",
                dataType: 'json',
                data: {
                    'id'        : id,
                    'berat_do'  : berat_do,
                    'total_do'  : total_do,
                    'ekoran_seckle': ekoran_seckle
                },
                beforeSend: function() {
                    if(berat_do ==='' && total_do === ''){
                        // $(".updatedatalpah").addClass('disabled');
                        $('.updatedatalpah').addAttr('disabled');
                        $(".spinerloading").show();
                        setTimeout(function(){ 
                            // $(".updatedatalpah").removeClass('disabled');
                            $(".updatedatalpah").removeAttr('disabled');
                            $(".spinerloading").show();
                        }, 2000);        
                    }
                    if(berat_do && total_do){
                        $('.updatedatalpah').attr('disabled');
                        // $(".updatedatalpah").addClass('disabled');
                        $(".spinerloading").show(); 
                    }    
                },
                success: function(data) {
                    setTimeout(function(){
                        if(data.status == 400){ 
                            showAlert(data.msg) ;
                            // $(".updatedatalpah").removeClass('disabled');
                            $(".updatedatalpah").attr('disabled');
                            $(".spinerloading").hide();
                        }else{
                            $(".updatedatalpah").attr('disabled');
                            $(".spinerloading").hide();
                            showNotif(data.msg) ;
                            setTimeout(function(){
                                window.location.reload();
                            },1000)
                        }
                    }, 2000);
                }
            })

        })
    </script>

@endsection
