<div class="" id="topbar-wrapper">
    <a href="javascript:void(0)" class="btn btn-neutral" id="mobile-menu">Menu <span class="fa fa-bars"></span></a>
    <div class="list-group list-group-flush">

        <ul class="nav topbar-column">

            @if (User::setIjin('superadmin') || User::setIjin(7) || User::setIjin(33))
            <li class="nav-item">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <img src="{{ asset('Icons/dashboard.png') }}" class="img-responsive topbar-icon-png" width="40">
                    Home <span class="sr-only">(current)</span>
                </a>
            </li>
            @endif


            @if (User::setIjin(1) || User::setIjin(2) || User::setIjin(3) || User::setIjin(5) || User::setIjin(6) || User::setIjin(36))
            <li class="nav-item">
                <a href="javascript:void(0)" class="toggle-custom nav-link" id="btn-1" data-toggle="collapse" data-target="#submenu1" aria-expanded="false">
                    <img src="{{ asset('Icons/lpah.png') }}" class="img-responsive topbar-icon-png" width="40">
                    Penerimaan  <span class="fa fa-chevron-down pull-right" aria-hidden="true"></span>
                </a>

                <ul class="collapse sub-menu" id="submenu1" role="menu" aria-labelledby="btn-5">
                    @if (User::setIjin(1)) <li class="nav-sub-item"><a href="{{ route('purchasing.index') }}">Purchasing</a></li> @endif
                    @if (Str::contains(url()->current(), 'http://8.219.1.73:8082/')) @if (User::setIjin(36)) <li class="nav-sub-item"><a href="{{ url('admin/pembelian-barang/purchase') }}">Purhcase Order</a></li> @endif @endif
                    @if (User::setIjin(2)) <li class="nav-sub-item"><a href="{{ route('security.index') }}">Security</a></li> @endif
                    @if (User::setIjin(3)) <li class="nav-sub-item"><a href="{{ route('lpah.index') }}">LPAH</a></li> @endif
                    @if (User::setIjin(33))<li class="nav-sub-item"><a href="{{ route('nonkarkas.index') }}">Penerimaan Non LB</a></li>@endif
                    @if (User::setIjin(6)) <li class="nav-sub-item"><a href="{{ route('evis.index') }}">Evis</a></li> @endif
                    @if (User::setIjin(5)) <li class="nav-sub-item"><a href="{{ route('grading.index') }}">Grading</a></li> @endif
                </ul>
            </li>
            @endif

            @if (User::setIjin(1) || User::setIjin(36) || User::setIjin(35))
            <li class="nav-item">
                <a href="javascript:void(0)" class="toggle-custom nav-link" id="btn-80" data-toggle="collapse" data-target="#submenu80" aria-expanded="false">
                    <img src="{{ asset('Icons/shopping-chart.png') }}" class="img-responsive topbar-icon-png" width="40">
                    Purchasing <span class="fa fa-chevron-down pull-right" aria-hidden="true"></span>
                </a>

                <ul class="collapse sub-menu" id="submenu80" role="menu" aria-labelledby="btn-80">
                    @if (User::setIjin(1)) <li class="nav-sub-item"><a href="{{ route('purchasing.index') }}">Purchasing</a></li> @endif
                    @if (Str::contains(url()->current(), 'http://8.219.1.73:8082/'))
                        @if (Session::get('subsidiary') == "EBA")
                            <li class="nav-sub-item"><a href="javascript:;" data-href="{{ route('authenticate-from-netsuite') }}" data-token="LbFMmcNeGOHXiTHMAarQMSDAafVhGILWqe3C2qeDKK7BOAwgRAVXdFB4KZCy.1db55a1e47f0154d8eb0b61c29de0904701c84ee89971576fad2efdc27c07710" data-subsidiary="EBA" id="progresspurchasingeba"> Progress LPAH</a></li>
                        @elseif(Session::get('subsidiary') == "CGL")
                            <li class="nav-sub-item"><a href="javascript:;" data-href="{{ route('authenticate-from-netsuite') }}" data-token="LbFMmcNeGOHXiTHMAarQMSDAafVhGILWqe3C2qeDKK7BOAwgRAVXdFB4KZCy.1db55a1e47f0154d8eb0b61c29de0904701c84ee89971576fad2efdc27c07710" data-subsidiary="CGL" id="progresspurchasingcgl"> Progress LPAH</a></li>
                        @else
                            <li class="nav-sub-item"><a href="javascript:;"> Progress LPAH</a></li>
                        @endif
                    @endif
                    @if (Str::contains(url()->current(), 'http://8.219.1.73:8082/')) @if (User::setIjin(36)) <li class="nav-sub-item"><a href="{{ url('admin/pembelian-barang/purchase') }}">Purhcase Order</a></li> @endif @endif
                    @if (Str::contains(url()->current(), 'http://8.219.1.73:8082/')) @if (User::setIjin(35)) <li class="nav-sub-item"><a href="{{ route('pembelian.index') }}">Purchase Request</a></li> @endif @endif
                </ul>
            </li>
            @endif


            @if (User::setIjin(7) || User::setIjin(8) || User::setIjin(9) || User::setIjin(10) || User::setIjin(11) || User::setIjin(12) || User::setIjin(13) || User::setIjin(14) ||  User::setIjin(15) || User::setIjin(24) || User::setIjin(27) || User::setIjin(30) || User::setIjin(31) || User::setIjin(33) || User::setIjin(5))
            <li class="nav-item">
                <a href="javascript:void(0)" class="toggle-custom nav-link" id="btn-2" data-toggle="collapse" data-target="#submenu2" aria-expanded="false">
                    <img src="{{asset('Icons/hasil-produksi.png')}}" class="img-responsive topbar-icon-png" width="40">
                    Produksi <span class="fa fa-chevron-down pull-right" aria-hidden="true"></span>
                </a>

                <ul class="collapse sub-menu" id="submenu2" role="menu" aria-labelledby="btn-5">
                    @if (User::setIjin(13) || User::setIjin(5)) <li class="nav-sub-item"><a href="{{ route('chiller.index') }}">Chiller Bahan Baku</a></li> @endif
                    @if (User::setIjin(14)) <li class="nav-sub-item"><a href="{{ route('hasilproduksi.index') }}">Chiller Finished Good</a></li> @endif
                    @if (User::setIjin(14)) <li class="nav-sub-item"><a href="{{ route('customer.stock') }}">Customer Stock</a></li> @endif
                    @if (User::setIjin(14)) <li class="nav-sub-item"><a href="{{ route('laporan.chillerdatastock') }}">Chiller Data Stock</a></li> @endif
                    @if (User::setIjin(13) || User::setIjin(14)) <li class="nav-sub-item"><a href="{{ route('chiller.soh') }}">Chiller SOH</a></li> @endif
                    {{-- @if (User::setIjin(24)) <li class="nav-sub-item"><a href="{{ route('ppic.index') }}">PPIC</a></li> @endif --}}
                    {{-- @if (User::setIjin(7)) <li class="nav-sub-item"><a href="{{ route('kepalaproduksi.index') }}">Kepala Produksi</a></li> @endif --}}

                    @if (User::setIjin(8) || User::setIjin(9) || User::setIjin(10) || User::setIjin(11) || User::setIjin(12)) <li class="nav-sub-item"><a href="{{ route('regu.index') }}">Kepala Regu</a></li> @endif

                    @if(env('NET_SUBSIDIARY', 'CGL')=='CGL') @if (User::setIjin(27)) <li class="nav-sub-item"><a href="{{ route('fulfillment.index') }}">Siap Kirim</a></li> @endif @endif
                    @if(env('NET_SUBSIDIARY', 'CGL')=='CGL') @if (User::setIjin(31)) <li class="nav-sub-item"><a href="{{ route('penyiapan.index', ['search' => 'meyer']) }}">Siap Kirim Meyer</a></li> @endif @endif
                    @if(env('NET_SUBSIDIARY', 'CGL')=='CGL') @if (User::setIjin(30)) <li class="nav-sub-item"><a href="{{ route('ekspedisi.index') }}">Ekspedisi</a></li> @endif @endif
                    @if ((env('NET_SUBSIDIARY', 'EBA') == "EBA") && User::setIjin(26)) <li class="nav-sub-item"><a href="{{ route('fulfillment.index') }}?tanggal={{date('Y-m-d')}}&customer=&search=&key=&divisi=sampingan&jenis=&tanggalkirimfulfillment=0&urutan=ASC">Jual Sampingan Efran</a></li> @endif
                    @if (User::setIjin(33)) <li class="nav-sub-item"><a href="{{ route('syncprod.index') }}">Tracing Produksi</a></li> @endif
                    @if (User::setIjin(33)) <li class="nav-sub-item"><a href="{{ route('syncabf.index') }}">Tracing ABF</a></li> @endif
                </ul>
            </li>
            @endif



            @if (User::setIjin(4))
            <li class="nav-item">
                <a href="javascript:void(0)" class="toggle-custom nav-link" id="btn-9" data-toggle="collapse" data-target="#submenu9" aria-expanded="false">
                    <img src="{{asset('Icons/qc.png')}}" class="img-responsive topbar-icon-png" width="40">
                    QC <span class="fa fa-chevron-down pull-right" aria-hidden="true"></span>
                </a>

                <ul class="collapse sub-menu" id="submenu9" role="menu" aria-labelledby="btn-9">
                    <li class="nav-sub-item"><a href="{{ route('qc.index') }}">QC</a></li>
                    <li class="nav-sub-item"><a href="{{ route('retur.index') }}">Retur</a></li>
                    <li class="nav-sub-item"><a href="{{ route('purchasing.index') }}">Purchasing</a></li>
                    <li class="nav-sub-item"><a href="{{ route('musnahkan.index') }}">Musnahkan</a></li>
                    <li class="nav-sub-item"><a href="{{ route('index.thawing') }}">Request Thawing</a></li>
                </ul>
            </li>
            @endif



            @if (User::setIjin(15) || User::setIjin(16) || User::setIjin(35))
            <li class="nav-item">
                <a href="javascript:void(0)" class="toggle-custom nav-link" id="btn-3" data-toggle="collapse" data-target="#submenu3" aria-expanded="false">
                    <img src="{{asset('Icons/warehouse.png')}}" class="img-responsive topbar-icon-png" width="40">
                    Gudang <span class="fa fa-chevron-down pull-right" aria-hidden="true"></span>
                </a>

                <ul class="collapse sub-menu" id="submenu3" role="menu" aria-labelledby="btn-5">
                    <li class="nav-sub-item"><a href="{{ route('warehouse_dash.dashboard') }}">Dashboard</a></li>

                    @if (User::setIjin(15)) <li class="nav-sub-item"><a href="{{ route('warehouse.index') }}">Warehouse</a></li> @endif
                    @if (Auth::user()->name != 'gudang') <li class="nav-sub-item"><a href="{{ route('nonkarkas.index') }}">Penerimaan Non LB</a></li>@endif

                    @if (User::setIjin(16) || User::setIjin(15)) <li class="nav-sub-item"><a href="{{ route('thawingproses.index') }}">Thawing</a></li> @endif

                    @if (User::setIjin(16) || User::setIjin(15)) <li class="nav-sub-item"><a href="{{ route('abf.index') }}">Storage ABF</a></li> @endif
                    @if ((env('NET_SUBSIDIARY', 'EBA') == "CGL")) @if(User::setIjin(15)) <li class="nav-sub-item"><a href="{{ route('fulfillment.index') }}?divisi=frozen&jenis=frozen">Siap Kirim Frozen</a></li> @endif @endif
                    @if ((env('NET_SUBSIDIARY', 'EBA') == "EBA") && User::setIjin(15)) <li class="nav-sub-item"><a href="{{ route('fulfillment.index') }}">Siap Kirim Efran</a></li> @endif
                    @if(env('NET_SUBSIDIARY', 'CGL')=='EBA') @if (User::setIjin(15)) <li class="nav-sub-item"><a href="{{ route('ekspedisi.index') }}">Ekspedisi</a></li> @endif @endif
                    @if (User::setIjin(15))
                    <li class="nav-sub-item"><a href="{{ route('pindah.index') }}">Pindah Gudang</a></li>
                    <li class="nav-sub-item"><a href="{{ route('repack.index') }}">Repack</a></li>
                    @endif
                    @if (User::setIjin(15) || User::setIjin(44))
                    <li class="nav-sub-item"><a href="{{route('bumbu.index',['key' => 'gudang'])}}">Bumbu</a></li>
                    @endif
                    <li class="nav-sub-item">
                        <a href="{{route('abf.index',['key' => 'grading-ulang'])}}" >Grading Ulang</a>
                    </li>

                </ul>
            </li>
            @endif

            @if (User::setIjin(17) || User::setIjin(18) || User::setIjin(19) || User::setIjin(44))
            <li class="nav-item">
                <a href="javascript:void(0)" class="toggle-custom nav-link" id="btn-4" data-toggle="collapse" data-target="#submenu4" aria-expanded="false">
                    <img src="{{asset('Icons/users.png')}}" class="img-responsive topbar-icon-png" width="40">
                    Database <span class="fa fa-chevron-down pull-right" aria-hidden="true"></span>
                </a>

                <ul class="collapse sub-menu" id="submenu4" role="menu" aria-labelledby="btn-5">
                    @if (User::setIjin(17))
                    <li class="nav-sub-item"><a href="{{ route('bom.index') }}">Bom</a></li>
                    <li class="nav-sub-item"><a href="{{ route('item.index') }}">Item</a></li>
                    <li class="nav-sub-item"><a href="{{ route('item.index', ['key' => 'yield_benchmark']) }}">Input Yield Benchmark</a></li>
                    @endif

                    @if (User::setIjin(44)) <li class="nav-sub-item"><a href="{{ route('gudang.index') }}">Gudang</a></li> @endif
                    @if (User::setIjin(18)) <li class="nav-sub-item"><a href="{{ route('supplier.index') }}">Supplier</a></li> @endif
                    @if (User::setIjin(19)) <li class="nav-sub-item"><a href="{{ route('driver.index') }}">Driver</a></li> @endif

                    {{-- @if (User::setIjin(20))
                    <li class="nav-sub-item"><a href="{{url('/admin/customers')}}" class="@if(Request::segment(2)=='customers') {{'active'}} @endif">Customers</a></li>
                    <li class="nav-sub-item"><a href="{{url('/admin/images')}}" class="@if(Request::segment(2)=='images') {{'active'}} @endif">Images</a></li>
                    @endif --}}

                    @if (User::setIjin(17))
                    <li class="nav-sub-item"><a href="{{ route('category.index') }}">Category</a></li>
                    <li class="nav-sub-item"><a href="{{ route('openbalance.index') }}">Input Open Balance</a></li>
                    <li class="nav-sub-item"><a href="{{ route('openbalance.data') }}">Data Open Balance</a></li>
                    @endif
                    <li class="nav-sub-item"><a href="{{ route('marketing.index', ['key' => 'marketinglist']) }}">Marketing</a></li>
                    <li class="nav-sub-item"><a href="{{ route('retur.alasan') }}">Alasan Retur</a></li>
                    @if (User::setIjin(15))
                        <li class="nav-sub-item"><a href="{{ route('bumbu.index') }}">Bumbu</a></li>
                    @endif
                    @if(User::setIjin(33))
                        <li class="nav-sub-item"><a href="{{ route('netsuite.index') }}">JSON NS</a></li>
                    @endif
                </ul>
            </li>
            @endif



            @if (User::setIjin(1) || User::setIjin(19) || User::setIjin(26) || User::setIjin(33) || User::setIjin(23) || User::setIjin(20) || User::setIjin(39) || User::setIjin(46))
            <li class="nav-item">
                <a href="javascript:void(0)" class="toggle-custom nav-link" id="btn-5" data-toggle="collapse" data-target="#submenu5" aria-expanded="false">
                    <img src="{{asset('Icons/laporan.jpeg')}}" class="img-responsive topbar-icon-png" width="40">
                    Laporan <span class="fa fa-chevron-down pull-right" aria-hidden="true"></span>
                </a>

                <ul class="collapse sub-menu" id="submenu5" role="menu" aria-labelledby="btn-5">
                    <li class="nav-sub-item"><a href="{{ route('tracing.index') }}">Tracing</a></li>
                    @if(User::setIjin(23))
                    <li class="nav-sub-item"><a href="{{ route('weekly.index') }}">Weekly Meeting</a></li>
                    <li class="nav-sub-item"><a href="{{ route('fulfillment.laporan') }}">Fulfillment</a></li>
                    <li class="nav-sub-item"><a href="{{ route('datastock.laporan') }}">Data Stock</a></li>
                    @endif

                    @if(User::setIjin(1)) <li class="nav-sub-item"><a href="{{ route('purchasing.laporan') }}">Purchasing</a></li> @endif
                    @if(User::setIjin(23)) <li class="nav-sub-item"><a href="{{ route('salesorder.laporan') }}">Sales Order</a></li> @endif
                    @if(User::setIjin(19)) <li class="nav-sub-item"><a href="{{ route('driver.laporan') }}">Driver</a></li> @endif
                    {{-- @if ((env('NET_SUBSIDIARY', 'EBA') == "EBA") && User::setIjin(15)) <li class="nav-sub-item"><a href="{{ route('fulfillment.index') }}?tanggal={{date('Y-m-d')}}&customer=&search=&key=&divisi=sampingan&jenis=&tanggalkirimfulfillment=0&urutan=ASC">Jual Sampingan Efran</a></li> @endif --}}

                    @if(User::setIjin(33))
                    <li class="nav-sub-item"><a href="{{ route('evis.laporan') }}">Evis</a></li>
                    <li class="nav-sub-item"><a href="{{ route('bukubesar.index') }}">Rendemen</a></li>
                    <li class="nav-sub-item"><a href="{{ route('laporan.lpah') }}">LPAH</a></li>
                    <li class="nav-sub-item"><a href="{{ route('laporan.qc') }}">QC</a></li>
                    @endif

                    @if(User::setIjin(23)) <li class="nav-sub-item"><a href="{{ route('marketing.index') }}">Marketing</a></li> @endif
                    @if(User::setIjin(20)) <li class="nav-sub-item"><a href="{{ route('customer.index') }}">Customer</a></li> @endif
                    @if(User::setIjin(33)) <li class="nav-sub-item"><a href="{{ route('laporanadmin.index') }}">Laporan Admin</a></li> @endif
                    @if(User::setIjin(39)) <li class="nav-sub-item"><a href="{{ route('laporanaccounting.index') }}">Laporan Accounting</a></li> @endif
                    @if(User::setIjin(39)) <li class="nav-sub-item"><a href="{{ route('laporanproduksi.index') }}">Laporan Produksi</a></li> @endif
                    @if(User::setIjin(39)) <li class="nav-sub-item"><a href="{{ route('laporanproduksi.hasilbbfg') }}">Laporan BB vs FG</a></li> @endif
                </ul>
            </li>
            @endif
            @if (User::setIjin(38) || User::setIjin(40) || User::setIjin(41) || User::setIjin(45))
            <li class="nav-item">
                <a href="javascript:void(0)" class="toggle-custom nav-link" id="btn-11" data-toggle="collapse" data-target="#submenu11" aria-expanded="false">
                    <img src="{{asset('Icons/purchasing.png')}}" class="img-responsive topbar-icon-png" width="40">
                    Marketing <span class="fa fa-chevron-down pull-right" aria-hidden="true"></span>
                </a>

                <ul class="collapse sub-menu" id="submenu11" role="menu" aria-labelledby="btn-11">
                    <!-- @if(User::setIjin(41))<li class="nav-sub-item"><a href="{{ route('marketing.dashboard') }}">Laporan Meeting</a></li>@endif -->
                    @if (Session::get('subsidiary') == "EBA")
                        @if (Str::contains(url()->current(), 'http://8.219.1.73:8082/'))
                            @if(User::setIjin(41))
                                <li class="nav-sub-item"><a href="javascript:;" data-href="{{ route('authenticate-from-netsuite') }}" data-token="LbFMmcNeGOHXiTHMAarQMSDAafVhGILWqe3C2qeDKK7BOAwgRAVXdFB4KZCy.1db55a1e47f0154d8eb0b61c29de0904701c84ee89971576fad2efdc27c07710" data-subsidiary="EBA" id="progressreporteba"> Fulfillment</a></li>
                            @endif
                        @endif
                    @elseif (Session::get('subsidiary') == "CGL")
                        @if (Str::contains(url()->current(), 'http://8.219.1.73:8082/'))
                            @if(User::setIjin(41))
                                <li class="nav-sub-item"><a href="javascript:;" data-href="{{ route('authenticate-from-netsuite') }}" data-token="LbFMmcNeGOHXiTHMAarQMSDAafVhGILWqe3C2qeDKK7BOAwgRAVXdFB4KZCy.1db55a1e47f0154d8eb0b61c29de0904701c84ee89971576fad2efdc27c07710" data-subsidiary="CGL" id="progressreportcgl"> Fulfillment</a></li>
                            @endif
                        @endif
                    @else
                            <li class="nav-sub-item"><a href="javascript:;"> Fulfillment</a></li>
                    @endif
                    {{-- @if(User::setIjin(41))<li class="nav-sub-item"><a href="{{ route('marketing.fulfillment') }}">Fulfillment</a></li>@endif --}}

                    @if(User::setIjin(41))<li class="nav-sub-item"><a href="{{ route('marketing.stock') }}">Data Stock</a></li>@endif
                    @if(env('NET_SUBSIDIARY', 'CGL')=='CGL') @if(User::setIjin(26)) <li class="nav-sub-item"><a href="{{ route('fulfillment.index') }}">Jual Sampingan</a></li> @endif @endif

                    @if(User::setIjin(46)) <li class="nav-sub-item"><a href="{{ route('laporanmarketing.index') }}">Laporan Marketing</a></li> @endif
                    @if (Str::contains(url()->current(), 'http://8.219.1.73:8082/')) @if(User::setIjin(38)) <li class="nav-sub-item"><a href="{{ route('buatso.index') }}">Buat Sales Order</a></li> @endif @endif
                    @if (Str::contains(url()->current(), 'http://8.219.1.73:8082/'))
                        @if (Session::get('subsidiary') == "CGL")
                            @if(User::setIjin(38))
                                <li class="nav-sub-item"><a href="javascript:;" data-href="{{ route('authenticate-from-netsuite') }}" data-token="LbFMmcNeGOHXiTHMAarQMSDAafVhGILWqe3C2qeDKK7BOAwgRAVXdFB4KZCy.1db55a1e47f0154d8eb0b61c29de0904701c84ee89971576fad2efdc27c07710" data-subsidiary="CGL" id="progressqcretur">QC Retur</a></li>
                            @endif
                        @endif
                    @endif
                    @if(User::setIjin(20)) <li class="nav-sub-item"><a href="{{ route('customer.index') }}">Customer</a></li> @endif
                    {{-- @if (Str::contains(url()->current(), 'http://8.219.1.73:8082/'))  @endif --}}

                    @if (Str::contains(url()->current(), 'http://8.219.1.73:8082/'))
                        @if(User::setIjin(45))
                            @if (Session::get('subsidiary') == "CGL")
                                <li class="nav-sub-item"> <a href="{{ route('hargakontrak.index', ['key' => 'customerSampingan']) }}">Customer Sampingan</a></li>
                            @else
                                <li class="nav-sub-item"><a href="{{ route('hargakontrak.index') }}">Harga Kontrak</a></li>
                            @endif
                        @endif

                    @endif


                    {{-- @if(User::setIjin(45)) <li class="nav-sub-item"><a href="{{ route('hargakontrak.index', ['key' => 'customerSampingan']) }}">Customer Sampingan</a></li> @endif --}}

                </ul>
            </li>
            @endif

            <li class="nav-item">
                <a href="javascript:void(0)" class="toggle-custom nav-link" id="btn-6" data-toggle="collapse" data-target="#submenu6" aria-expanded="false">
                    <img src="{{asset('Icons/setting.png')}}" class="img-responsive topbar-icon-png" width="40">
                    Settings <span class="fa fa-chevron-down pull-right" aria-hidden="true"></span>
                </a>

                <ul class="collapse sub-menu" id="submenu6" role="menu" aria-labelledby="btn-6">
                    <li class="nav-sub-item"><a href="{{ url('admin/switch-color') }}">Switch Color</a></li>

                    @if (User::setIjin(21))
                    <li class="nav-sub-item"><a href="{{ route('users.index') }}">Admin</a></li>
                    <li class="nav-sub-item"><a href="{{ url('/admin/hak-akses') }}">Hak Akses</a></li>
                    @endif

                    @if (User::setIjin('superadmin'))
                    <li class="nav-sub-item"><a href="{{ url('/admin/options') }}">Options</a></li>
                    <li class="nav-sub-item"><a href="{{ route('logs.index') }}">Logs Data</a></li>
                    <li class="nav-sub-item"><a href="{{ route('chiller.indexstock') }}">Chiller Stock</a></li>
                    <li class="nav-sub-item"><a href="{{ route('hasilproduksi.edit') }}">Edit Produksi</a></li>
                    <li class="nav-sub-item"><a href="{{ route('supplieredit.index') }}">Edit Supllier</a></li>
                    @endif
                </ul>
            </li>

            @if (User::setIjin(25))
            <li class="nav-item">
                <a href="javascript:void(0)" class="toggle-custom nav-link" id="btn-8" data-toggle="collapse" data-target="#submenu8" aria-expanded="false">
                    <img src="{{asset('Icons/netsuite.png')}}" class="img-responsive topbar-icon-png" width="40">
                    Netsuite <span class="fa fa-chevron-down pull-right" aria-hidden="true"></span>
                </a>
                <ul class="collapse sub-menu" id="submenu8" role="menu" aria-labelledby="btn-6">
                    <li class="nav-sub-item"><a href="{{ route('report.netsuite.index') }}">NS to Apps</a></li>
                    <li class="nav-sub-item"><a href="{{ route('sync.index') }}">Apps to NS</a></li>
                    <li class="nav-sub-item"><a href="{{ route('generateti.index') }}">Generate TI</a></li>
                    <li class="nav-sub-item"><a href="{{ route('generatewowob.index') }}">Generate WO-WOB</a></li>
                    <li class="nav-sub-item"><a href="{{ url('admin/wo/so-list?tanggal=') }}">Tracing SO</a></li>
                    <li class="nav-sub-item"><a href="{{ url('admin/wo/retur-list?tanggal=') }}">Tracing Retur</a></li>
                    <li class="nav-sub-item"><a href="{{ url('admin/wo/wo-1-list?tanggal=') }}">Tracing WO-1</a></li>
                    <li class="nav-sub-item"><a href="{{ url('admin/wo/wo-2-list?tanggal=') }}">Tracing WO-2</a></li>
                    <li class="nav-sub-item"><a href="{{ url('admin/wo/wo-3-list?tanggal=') }}">Tracing WO-3</a></li>
                    <li class="nav-sub-item"><a href="{{ url('admin/wo/wo-4-list?tanggal=') }}">Tracing WO-4</a></li>
                </ul>
            </li>
            @endif

            <li class="nav-item">
                <a href="javascript:void(0)" class="toggle-custom nav-link" id="btn-profile" data-toggle="collapse" data-target="#submenuprofile" aria-expanded="false">
                    <img src="{{asset('Icons/users.png')}}" class="img-responsive topbar-icon-png" width="40">
                    Profile <span class="fa fa-chevron-down pull-right" aria-hidden="true"></span>
                </a>
                <ul class="collapse sub-menu" id="submenuprofile" role="menu" aria-labelledby="btn-9">
                    <li class="nav-sub-item"><a href="{{ route('profile') }}">Edit Profile</a></li>
                </ul>
            </li>

        </ul>

    </div>
  </div>

<style>
    img.topbar-icon-png{
        float: left;
        margin-right: 15px;
        width: 20px;
    }

    .toggle-custom[aria-expanded='true'] .fa-chevron-down:before {
        content: "\f077";
    }
</style>

<script>
    $('.toggle-custom').on('click', function(){
        $('.toggle-custom').siblings('.sub-menu').collapse('hide');
    })


    $("#mobile-menu").click(function() {
        if($("#top-navbar").css("left") == "0px"){
            $("#top-navbar").animate({"left": "165px"},"fast");
            $("#mobile-menu").html('Close <span class="fa fa-close"></span>')
        }
        else{
            $("#top-navbar").animate({"left": "0px"},"fast");
            $("#mobile-menu").html('Menu <span class="fa fa-bars"></span>')
            $('.toggle-custom').siblings('.sub-menu').collapse('hide');
        }
    });

    $(document).on('click', function(e){
        if($(e.target).closest(".sub-menu").length===0){
            $('.toggle-custom').siblings('.sub-menu').collapse('hide');
        }
    })

    $(document).ready(function(){
        $("#progressreporteba,#progressreportcgl").click(function(){
            let token       = $(this).attr("data-token");
            let href        = $(this).attr("data-href");
            let subsidiary  = $(this).attr("data-subsidiary");
            $.ajax({
                url: href,
                method :'GET',
                data:{
                    '_token'        : token,
                    'href'          : href,
                    'subsidiary'    : subsidiary,
                    'role'          : "marketing",
                    'key'           : "marketing"
                },
                success: function(data){
                    setTimeout(function(){
                        if(data.status == 1){
                            window.open(data.url, '_blank');
                        }
                        else {

                        }
                    }, 200);
                },
            })
        })
    })

    $(document).ready(function(){
        $("#progresspurchasingeba,#progresspurchasingcgl").click(function(){
            let token       = $(this).attr("data-token");
            let href        = $(this).attr("data-href");
            let subsidiary  = $(this).attr("data-subsidiary");
            $.ajax({
                url: href,
                method :'GET',
                data:{
                    '_token'        : token,
                    'href'          : href,
                    'subsidiary'    : subsidiary,
                    'role'          : "purchasing",
                    'key'           : "purchasing"
                },
                success: function(data){
                    setTimeout(function(){
                        if(data.status == 1){
                            window.open(data.url, '_blank');
                        }
                        else {

                        }
                    }, 200);
                },
            })
        })
    })
    $(document).ready(function(){
        $("#progressqcretur").click(function(){
            let token       = $(this).attr("data-token");
            let href        = $(this).attr("data-href");
            let subsidiary  = $(this).attr("data-subsidiary");
            $.ajax({
                url     : href,
                method  :'GET',
                data    :{
                    '_token'        : token,
                    'href'          : href,
                    'subsidiary'    : subsidiary,
                    'role'          : "marketing",
                    'key'           : "retur"
                },
                success: function(data){
                    setTimeout(function(){
                        if(data.status == 1){
                            window.open(data.url, '_blank');
                        }
                        else {

                        }
                    }, 200);
                },
            })
        })
    })
</script>
