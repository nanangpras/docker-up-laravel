@extends('admin.layout.template')

@section('title', 'ABF')

@section('content')

<div class="row mb-4">
    <div class="col">
        <a href="{{url('admin/abf#custom-tabs-diterima')}}" class="btn btn-outline btn-sm btn-back"> <i
                class="fa fa-arrow-left"></i> Back</a>
    </div>
    <div class="col-7 pt-2 text-center">
        <b>KONFIRMASI DATA TIMBANG ABF</b>
    </div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">
        <form method="POST" action="{{route('abf.abf_gabung_item')}}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="key" value="gabung">
            <table class="table default-table" width="100%" id="LBabfTable">
                <thead>
                    <tr>

                        <th>ID</th>
                        <th>Item</th>
                        <th>Jenis</th>
                        <th>Packaging</th>
                        <th>Asal</th>
                        <th>Tanggal</th>
                        <th>Qty</th>
                        <th>Berat</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data_abf as $i => $row)
                    <tr>
                        <td>{{++$i}}. AF-{{ $row->id }}
                            <input type="hidden" name="selected_id[]" value="{{$row->id}}">
                        </td>
                        <td>
                            {{ $row->item_name }} @if($row->grade_item) <span
                                class="text-primary pl-2 font-weight-bold uppercase"> // Grade B </span> @endif
                            @if ($row->selonjor)
                            <br><span class="text-danger font-weight-bold">SELONJOR</span>
                            @endif
                            @if ($row->table_name == 'chiller')
                            @php
                            $exp = json_decode($row->abf_chiller->label ?? false);
                            @endphp

                            @if ($row->customer_id)<br><span class="text-info">Customer : {{ $row->konsumen->nama ?? '' }}</span> @endif

                            @if ($exp)<br>

                            @if ($exp)
                            @if (isset($exp->additional))
                            {{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }} {{ $exp->additional->lemak ? 'Tanpa
                            Lemak, ' : '' }} {{ $exp->additional->maras ? 'Tanpa Maras' : '' }}
                            @endif
                            @endif
                            <div class="row mt-1 text-info">
                                <div class="col pr-1">@if ($exp->sub_item ?? '') Keterangan : {{ $exp->sub_item }}
                                    @endif</div>
                                <div class="col-auto pl-1">@if ($exp->parting->qty ?? '') Parting : {{ $exp->parting->qty }} @endif</div>
                            </div>
                            @endif
                            @endif

                            @if ($row->table_name == 'free_stocktemp')
                            @php
                            $exp = json_decode($row->abf_freetemp->label ?? false);
                            @endphp

                            @if ($row->customer_id)<br><span class="text-info">Customer : {{ $row->konsumen->nama ?? '' }}</span> @endif

                            <div class="status status-success">
                                <div class="row">
                                    <div class="col pr-1">
                                        {{ $row->abf_chiller->plastik_nama }}
                                    </div>
                                    <div class="col-auto pl-1">
                                        <span class="float-right">// {{ $row->abf_chiller->plastik_qty }} Pcs</span>
                                    </div>
                                </div>
                            </div>

                            @if ($exp)<br>
                            @if ($exp->additional) {{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }} {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }} {{ $exp->additional->maras ? 'Tanpa Maras' : '' }} @endif
                            <div class="row mt-1 text-info">
                                <div class="col pr-1">@if ($exp->sub_item ?? '') Keterangan : {{ $exp->sub_item }}
                                    @endif</div>
                                <div class="col-auto pl-1">@if ($exp->parting->qty ?? '') Parting : {{ $exp->parting->qty }} @endif</div>
                            </div>
                            @endif
                            @endif
                        </td>
                        <td>
                            @if (strpos($row->item_name, 'FROZEN') !== false)
                            <span class="status status-danger">FROZEN</span>
                            @else
                            <span class="status status-info">FRESH</span>
                            @endif
                        </td>
                        <td>{{ $row->packaging }}</td>
                        <td>
                            @if($row->asal_tujuan=="kepala_produksi")
                            <span class="status status-warning">Produksi</span>
                            @elseif($row->asal_tujuan=="free_stock")
                            <span class="status status-danger">ReguFrozen</span>
                            @else
                            <span class="status status-info">{{$row->asal_tujuan}}</span>
                            @endif
                        </td>
                        <td>{{ date('d/m/Y', strtotime($row->tanggal_masuk)) }}</td>
                        <td>{{ number_format($row->qty_item > 0 ? $row->qty_item : '0') }}</td>
                        <td class="text-right">{{ number_format(($row->berat_item > 0 ? $row->berat_item : '0'), 2) }}
                        </td>
                        <td>
                            <span class="status status-other">Proses Gabung</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <hr>

            <div class="row">
                {{-- <div class="col">
                    <div class="form-group">
                        <label for="tanggal">Tanggal baru</label>
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif id="tanggal" name="tanggal" class="form-control"
                            value="{{date('Y-m-d')}}">
                    </div>
                </div> --}}
                {{-- <div class="col">
                    <div class="form-group">
                        <label for="tanggal">Keterangan</label>
                        <input type="text" id="keterangan" name="keterangan" class="form-control" value=""
                            placeholder="Keterangan">
                    </div>
                </div> --}}
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <button type="submit" class="btn btn-blue" id="btnGabungAbf">Gabungkan</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
    $('#btnGabungAbf').on('click', (e) => {
        $('#btnGabungAbf').hide();
    })
</script>

@endsection