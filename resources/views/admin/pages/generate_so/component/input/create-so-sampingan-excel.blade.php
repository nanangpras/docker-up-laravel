<section class="panel">
    <div class="card-body">
        @if (Session::get('subsidiary') == 'CGL')
        <div class="mb-3"><b>Upload Excel SO Sampingan</b></div>
        @else
        <div class="mb-3"><b>Upload Excel SO Karyawan</b></div>
        @endif
        <form action="{{ route('buatso.store', ['key' => 'excelSOSampingan']) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('post')
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <input type="file" name="file">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Submit</button>
                    </div>
                </div>
            </div>
        </form><hr>
        {{-- <a href="https://docs.google.com/spreadsheets/d/135fPFUYIvee0scK6agtp843LRbZFU8-GtkCp7QYJVDk/edit#gid=0" target="_blank">Download Template Excel <span class="fa fa-download"></span></a> --}}
    </div>
</section>