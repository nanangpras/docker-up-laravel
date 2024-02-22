<div class="form-group">
    <div>Item</div>
    <strong>{{ $namaitem }}</strong>
</div>
<div class="row">
    @if($kategori == '1')
        <table class="table default-table">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Kode Chiller</th>
                    <th class="text-center">Kode ABF</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $IDchiller      = \App\Models\FreestockTemp::getKodeChiller($id,$regu);
                    $codeAbf        = \App\Models\FreestockTemp::getKodeABF($IDchiller);
                @endphp
                @if(count($codeAbf) > 0)
                    @foreach($codeAbf as $kode)
                    <tr>
                        <td class="text-center">{{ $loop->iteration}}</td>
                        <td class="text-center"><a href="{{ url('admin/chiller/'.$IDchiller) }}" target="_blank">#CHILL-{{ $IDchiller }}</a></td>
                        <td class="text-center"><a href="{{ url('admin/abf/timbang/'.$kode->id) }}" target="_blank"> #ABF-{{ $kode->id}}</a></td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="text-center">#</td>
                        <td class="text-center"><a href="{{ url('admin/chiller/'.$IDchiller) }}" target="_blank">#CHILL-{{ $IDchiller }}</a></td>
                        <td class="text-center">Belum Digunakan</td>
                    </tr>
                @endif
            </tbody>
        </table>
    @elseif($kategori == '0' || $kategori == '3' || $kategori == '' || $kategori == NULL)
        <table class="table default-table">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Kode Chiller</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $IDchiller      = \App\Models\FreestockTemp::getKodeChiller($id,$regu);
                @endphp
                @if($IDchiller != '')
                    <tr>
                        <td class="text-center">1</td>
                        <td class="text-center"><a href="{{ url('admin/chiller/'.$IDchiller) }}" target="_blank">#CHILL-{{ $IDchiller }}</a></td>
                    </tr>
                @else
                    <tr>
                        <td colspan="3" class="text-center">Data belum digunakan</td>
                    </tr>
                @endif
            </tbody>
        </table>
    @endif
</div>