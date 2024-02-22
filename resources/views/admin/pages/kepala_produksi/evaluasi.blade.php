<div class="nav nav-tabs" id="nav-tab" role="tablist">
    <a class="nav-item nav-link active" id="custom-tabs-three-boneless-tab" data-toggle="pill" href="#custom-tabs-three-boneless" role="tab" aria-controls="custom-tabs-three-boneless" aria-selected="true">
        Boneless
    </a>
    <a class="nav-item nav-link" id="custom-tabs-three-parting-tab" data-toggle="pill" href="#custom-tabs-three-parting" role="tab" aria-controls="custom-tabs-three-parting" aria-selected="false">
        Parting
    </a>
    <a class="nav-item nav-link" id="custom-tabs-three-marinasi-tab" data-toggle="pill" href="#custom-tabs-three-marinasi" role="tab" aria-controls="custom-tabs-three-marinasi" aria-selected="false">
        M
    </a>
    <a class="nav-item nav-link" id="custom-tabs-three-whole-tab" data-toggle="pill" href="#custom-tabs-three-whole" role="tab" aria-controls="custom-tabs-three-whole" aria-selected="false">
        Whole Chicken
    </a>
    <a class="nav-item nav-link" id="custom-tabs-three-frozen-tab" data-toggle="pill" href="#custom-tabs-three-frozen" role="tab" aria-controls="custom-tabs-three-frozen" aria-selected="false">
        Frozen
    </a>
    <a class="nav-item nav-link" id="custom-tabs-three-evis-tab" data-toggle="pill" href="#custom-tabs-three-evis" role="tab" aria-controls="custom-tabs-three-evis" aria-selected="false">
        Evis
    </a>
</div>
<div class="tab-content" id="custom-tabs-three-tabContent">
    <div class="tab-pane fade show active" id="custom-tabs-three-boneless" role="tabpanel" aria-labelledby="custom-tabs-three-boneless-tab">
        <div class="row">
            <div class="col-md-3">
                <div class="box-border">
                    <div class="card-body">
                        Total QTY
                        <h2>{{  number_format($total['bonlessitem'],0) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="box-border">
                    <div class="card-body">
                        Total Berat
                        <h2>{{  number_format($total['bonlessberat'], 2) }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="custom-tabs-three-parting" role="tabpanel" aria-labelledby="custom-tabs-three-parting-tab">
        <div class="row">
            <div class="col-md-3">
                <div class="box-border">
                    <div class="card-body">
                        Total QTY
                        <h2>{{  number_format($total['partitem'],0) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="box-border">
                    <div class="card-body">
                        Total Berat
                        <h2>{{  number_format($total['partberat'], 2) }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="custom-tabs-three-marinasi" role="tabpanel" aria-labelledby="custom-tabs-three-marinasi-tab">
        <div class="row">
            <div class="col-md-3">
                <div class="box-border">
                    <div class="card-body">
                        Total QTY
                        <h2>{{  number_format($total['marinasiitem'],0) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="box-border">
                    <div class="card-body">
                        Total Berat
                        <h2>{{  number_format($total['marinasiberat'], 2) }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="custom-tabs-three-whole" role="tabpanel" aria-labelledby="custom-tabs-three-whole-tab">
        <div class="row">
            <div class="col-md-3">
                <div class="box-border">
                    <div class="card-body">
                        Total QTY
                        <h2>{{  number_format($total['wholeitem'],0) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="box-border">
                    <div class="card-body">
                        Total Berat
                        <h2>{{  number_format($total['wholeberat'], 2) }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="custom-tabs-three-frozen" role="tabpanel" aria-labelledby="custom-tabs-three-frozen-tab">
        <div class="row">
            <div class="col-md-3">
                <div class="box-border">
                    <div class="card-body">
                        Total QTY
                        <h2>{{  number_format($total['frozenitem'],0) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="box-border">
                    <div class="card-body">
                        Total Berat
                        <h2>{{  number_format($total['frozenberat'], 2) }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="custom-tabs-three-evis" role="tabpanel" aria-labelledby="custom-tabs-three-evis-tab">
        <div class="row">
            <div class="col-md-3">
                <div class="box-border">
                    <div class="card-body">
                        Total QTY
                        <h2>{{  number_format($total['evisitem'],0) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="box-border">
                    <div class="card-body">
                        Total Berat
                        <h2>{{ number_format($total['evisberat'], 2) }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
