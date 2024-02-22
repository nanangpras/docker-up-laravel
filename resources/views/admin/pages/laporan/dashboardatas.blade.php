<div class="row">
<div class="col-md-3 col-sm-3 col-xs-6 col-xxs-6">
    <div class="box-border">
        <div class="card-body">
            Total PO
            <h5>{{ $count_purchase }}</h5>
        </div>
    </div>
</div>
<div class="col-md-3 col-sm-3 col-xs-6 col-xxs-6">
    <div class="box-border">
        <div class="card-body">
            Total DO
            <h5>{{ $count_production }}</h5>
        </div>
    </div>
</div>
<div class="col-md-3 col-sm-3 col-xs-6 col-xxs-6">
    <div class="box-border">
        <div class="card-body">
            Customer
            <h5>{{ $count_customer }}</h5>
        </div>
    </div>
</div>
<div class="col-md-3 col-sm-3 col-xs-6 col-xxs-6">
    <div class="box-border">
        <div class="card-body">
            Supplier
            <h5>{{ $count_supplier }}</h5>
        </div>
    </div>
</div>

{{-- <div class="col-md-6">
    <div class="box-border">
        <div class="card-header">Rekap Top Item</div>
        <div class="card-body">

            @foreach ($top_item as $row)
                <div class="radio-toolbar">
                    <label>
                        {{ $row->nama }}
                        <div class="pull-right">
                            <span class="label label-rounded-grey">{{ $row->total_qty }} </span>
                        </div>
                    </label>
                </div>
            @endforeach

        </div>
    </div>
</div>

<div class="col-md-6">
    <div class="box-border">
        <div class="card-header">Rekap Sales Order</div>
        <div class="card-body">

            <div class="radio-toolbar">
                <label>
                    Jumlah Order
                    <div class="pull-right">
                        <span class="label label-rounded-grey">{{ $count_so }} </span>
                    </div>
                </label>
            </div>
            <div class="radio-toolbar">
                <label>
                    Pending
                    <div class="pull-right">
                        <span class="label label-rounded-grey">{{ $count_so_pending }} </span>
                    </div>
                </label>
            </div>
            <div class="radio-toolbar">
                <label>
                    Proses Kepala Produksi
                    <div class="pull-right">
                        <span class="label label-rounded-grey">{{ $count_so_kp }} </span>
                    </div>
                </label>
            </div>
            <div class="radio-toolbar">
                <label>
                    Proses Kepala Regu
                    <div class="pull-right">
                        <span class="label label-rounded-grey">{{ $count_so_kr }} </span>
                    </div>
                </label>
            </div>
            <div class="radio-toolbar">
                <label>
                    Produksi Chiller
                    <div class="pull-right">
                        <span class="label label-rounded-grey">{{ $count_so_chiller }} </span>
                    </div>
                </label>
            </div>
            <div class="radio-toolbar">
                <label>
                    Produksi Ekspedisi
                    <div class="pull-right">
                        <span class="label label-rounded-grey">{{ $count_so_ekspedisi }} </span>
                    </div>
                </label>
            </div>
            <div class="radio-toolbar">
                <label>
                    Produksi Loading
                    <div class="pull-right">
                        <span class="label label-rounded-grey">{{ $count_so_loading }} </span>
                    </div>
                </label>
            </div>
            <div class="radio-toolbar">
                <label>
                    Produksi Pengantaran
                    <div class="pull-right">
                        <span class="label label-rounded-grey">{{ $count_so_pengantaran }} </span>
                    </div>
                </label>
            </div>
            <div class="radio-toolbar">
                <label>
                    Produksi QR Retur
                    <div class="pull-right">
                        <span class="label label-rounded-grey">{{ $count_so_qc }} </span>
                    </div>
                </label>
            </div>
            <div class="radio-toolbar">
                <label>
                    Order Selesai
                    <div class="pull-right">
                        <span class="label label-rounded-grey">{{ $count_so_selesai }} </span>
                    </div>
                </label>
            </div>

        </div>
    </div>
</div> --}}
