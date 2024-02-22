@extends('admin.layout.template')

@section('title', 'Daftar BOM')

@section('content')
<div class="my-3 font-weight-bold text-center">Daftar BOM</div>

<section class="panel">
    <div class="card-body">
        <div class="accordion" id="accordionBOM">
            @foreach ($data as $row)
                @if ($row->bom_name)
                <div class="card">
                    <div class="card-header" id="headingOne">
                        <div data-toggle="collapse" data-target="#collapse{{ $row->id }}" aria-expanded="true" aria-controls="collapse{{ $row->id }}">
                            {{ $row->netsuite_internal_id }} || {{ $row->bom_name }}
                        </div>
                    </div>

                    <div id="collapse{{ $row->id }}" class="collapse" aria-labelledby="headingOne" data-parent="#accordionBOM">
                    <div class="card-body p-2">
                        <div class="border-bottom p-1">
                            <table class="table default-table">
                                <thead>
                                    <tr>
                                        <th>SKU</th>
                                        <th>Item</th>
                                        <th>Kategori</th>
                                        <th>Netsuite ID</th>
                                        <th>Qty Assembly</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($row->bomlist as $item)
                                    <tr>
                                        <td>{{ $item->sku ?? '' }}</td>
                                        <td>{{ $item->item->nama ?? ''}}</td>
                                        <td>{{ $item->kategori ?? ''}}</td>
                                        <td>{{ $item->item->netsuite_internal_id ?? ''}}</td>
                                        <td>{{ $item->bom_qty_per_assembly ?? ''}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
</section>
@endsection
