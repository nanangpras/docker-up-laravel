@extends('admin.layout.template')

@section('title', 'Stock Chiller')


@section('content')
<div class="row mb-4">
    <div class="col text-primary"><a href="{{ route('purchasing.index') }}"><i class="fa fa-arrow-left"></i> Kembali</a>
    </div>
    <div class="col text-center"><b>Mutasi Stock Chiller</b></div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                PERIODE LAPORAN

                <div class="panel">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label>Mulai</label>
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif id="laporan_mulai" value="{{ date('Y-m-d') }}"
                                    class="form-control">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>Sampai</label>
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif id="laporan_sampai" value="{{ date('Y-m-d') }}"
                                    class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div id="chiller_data"></div>
            </div>
        </div>

    </div>
</section>
<form method="post" action="{{route('weekly.export')}}">
    @csrf
    <input name="filename" type="hidden" value="export-bonus-supir.xls">
    <textarea name="html" style="display: none" id="html-export-form"></textarea>
    <button type="submit" id="export-button" class="btn btn-blue">Export</button>
</form>

@endsection

@section('footer')
<script>
    $('#chiller_data').load("{{route('mutasistock.chiller')}}?type=data");   

        var mulai = $('#laporan_mulai').val();
        var sampai = $('#laporan_sampai').val();

        $('#laporan_mulai').on('change', function(){
            loadData();
        })

        $('#laporan_sampai').on('change', function(){
            loadData();
        })

        function loadData(){
            mulai = $('#laporan_mulai').val();
            sampai = $('#laporan_sampai').val();
            $('#chiller_data').load("{{route('mutasistock.chiller')}}?type=data&mulai="+mulai+"&sampai="+sampai);   
        }
</script>
@endsection