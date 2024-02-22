<?php

use App\Http\Controllers\Admin\CheckerController;
use App\Http\Controllers\Admin\WarehouseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect(route('dashboard'));
});

Auth::routes();

Route::get('/server-status', 'HomeController@localServer');
Route::get('/cloud-status', 'HomeController@cloudServer');
Route::get('/netsuite-status', 'HomeController@netsuiteServer');
Route::get('/crawl-so-eba', 'HomeController@crawlSoEba');

Route::get('/login-from-netstsuite', 'API\NetsuiteController@loginFromNetsuite');
Route::get('/authenticate-from-netsuite', 'API\NetsuiteController@AuthLoginFromNetsuite')->name('authenticate-from-netsuite');

// Route::group(['middleware' => ['revalidate']], function () {

    Route::group(['prefix' => 'report', 'middleware' => 'admin'], function () {
        Route::get('/laporan-dashboard', 'Cloud\ReportController@report_dashboard')->name('cloud.report.dashboard');
        Route::get('/laporan-produksi', 'Cloud\ReportController@report_produksi')->name('cloud.report.produksi');
        Route::get('/netsuite', "Cloud\ReportController@ns_index")->name('report.netsuite.index');
        Route::get('/netsuite/list', "Cloud\ReportController@ns_list");
        Route::get('/netsuite/location', "Cloud\ReportController@ns_location")->name('report.netsuite.location');
        Route::get('/netsuite/bom', "Cloud\ReportController@ns_bom")->name('report.netsuite.bom');
        Route::get('/netsuite/po', "Cloud\ReportController@ns_po")->name('report.netsuite.po');
        Route::get('/netsuite/so', "Cloud\ReportController@ns_so")->name('report.netsuite.so');
        Route::get('/netsuite/raw', "Cloud\ReportController@ns_raw")->name('report.netsuite.raw');
    });


    Route::get('admin/dashboard/chat', 'Admin\DashboardController@chat')->name('dashboard.chat');
    Route::get('admin/dashboard/new_chat', 'Admin\DashboardController@new_chat')->name('dashboard.new_chat');
    Route::get('admin/dashboard/read_chat/{id}', 'Admin\DashboardController@read_chat')->name('dashboard.read_chat');
    Route::post('admin/send-chat', 'Admin\DashboardController@sendchat')->name('sendchat');

    Route::group(['middleware' => ['user_activity']], function () {

        Route::group(['prefix' => 'admin', 'middleware' => 'admin', 'namespace' => 'Admin'], function () {

            // Dashboard
            Route::get('/', 'DashboardController@index')->name('index');
            Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
            Route::get('/dashboardatas', 'DashboardController@dashboardatas')->name('dashboard.atas');
            Route::get('/dashboard/datastock', 'DashboardController@datastock')->name('dashboard.datastock');
            Route::get('/dashboard/detailproduksi', 'DashboardController@detailproduksi')->name('dashboard.detailproduksi');
            Route::get('/dashboard/konsumenorder', 'DashboardController@konsumenorder')->name('dashboard.konsumenorder');
            Route::get('/dashboard/konsumenretur', 'DashboardController@konsumenretur')->name('dashboard.konsumenretur');
            Route::get('/dashboard/produksiplastik', 'DashboardController@produksiplastik')->name('dashboard.produksiplastik');
            Route::get('/dashboard/itempending', 'DashboardController@itempending')->name('dashboard.itempending');
            Route::get('/dashboard/itemalokasi', 'DashboardController@itemalokasi')->name('dashboard.itemalokasi');
            Route::get('/dashboard/cashonhand', 'DashboardController@cashonhand')->name('dashboard.cashonhand');
            Route::get('/dashboard/cashonhand-gudang', 'DashboardController@cashonhandgudang')->name('dashboard.cashonhandgudang');
            Route::get('/dashboard/saleschannel', 'DashboardController@saleschannel')->name('dashboard.saleschannel');
            Route::get('/dashboard/mingguan', 'DashboardController@mingguan')->name('dashboard.mingguan');

            // Purchasing
            Route::get('purchasing', 'PurchasingController@index')->name('purchasing.index');
            Route::get('purchasing/purch', 'PurchasingController@purch')->name('purchasing.purch');
            Route::get('purchasing/target', 'PurchasingController@target')->name('purchasing.target');
            Route::get('purchasing/bonus', 'PurchasingController@bonus')->name('purchasing.bonus');
            Route::get('purchasing/supplier', 'PurchasingController@supplier')->name('purchasing.supplier');
            Route::get('purchasing/lpah', 'PurchasingController@laporanlpah')->name('purchasing.lpah');
            Route::get('purchasing/penerimaan', 'PurchasingController@penerimaan')->name('purchasing.penerimaan');
            Route::post('purchasing/target', 'PurchasingController@targetstore')->name('purchasing.targetstore');
            Route::patch('purchasing/target', 'PurchasingController@targetupdate')->name('purchasing.targetupdate');
            Route::delete('purchasing/target', 'PurchasingController@targetdestroy')->name('purchasing.targetdestroy');
            Route::get('purchasing/retur/{id}', 'PurchasingController@retur')->name('purchasing.retur');
            Route::post('purchasing/retur/{id}', 'PurchasingController@returstore')->name('purchasing.returstore');
            Route::get('purchasing/{id}', 'PurchasingController@show')->name('purchasing.show');
            Route::post('purchasing/{id}', 'PurchasingController@store')->name('purchasing.store');

            // Security
            Route::get('security', 'SecurityController@index')->name('security.index');
            Route::post('security/getdataautocomplete', 'SecurityController@AutoCompleteData')->name('security.autocomplete');
            Route::post('security', 'SecurityController@store')->name('security.store');
            Route::patch('security', 'SecurityController@update')->name('security.update');
            Route::put('security', 'SecurityController@edit')->name('security.edit');
            Route::delete('security', 'SecurityController@reset')->name('security.reset');
            Route::get('search-driver', 'SecurityController@searchDriver')->name('security.searchDriver');
            // Data Penerimaan Ayam Hidup
            // Route::resource('lpah', 'LpahController', ['as' => 'admin']);
            Route::get('lpah', 'LpahController@index')->name('lpah.index');
            Route::post('lpah', 'LpahController@store')->name('lpah.store');
            Route::get('lpah/inject-susut', 'LpahController@inject_susut');
            Route::get('lpah/inject-rerata', 'LpahController@inject_rerata');
            Route::get('lpah/inject-ayammerah', 'LpahController@inject_ayammerah');
            // Route::post('lpah/selesai', 'LpahController@selesai')->name('lpah.selesai');

            Route::get('lpah/{id}', 'LpahController@show')->name('lpah.show');
            Route::put('lpah/{id}', 'LpahController@updateDo')->name('lpah.updatedo');
            Route::post('lpah/{id}', 'LpahController@add')->name('lpah.add');
            Route::patch('lpah/{id}', 'LpahController@update')->name('lpah.update');
            Route::delete('lpah/{id}', 'LpahController@rollback')->name('lpah.rollback');
            Route::post('lpah/update/editlpah', 'LpahController@updatedata')->name('lpah.updatedata');

            Route::get('lpah/{id}/keranjang', 'LpahController@cart')->name('lpah.cart');
            Route::get('lpah/{id}/susut', 'LpahController@susut')->name('lpah.susut');
            Route::post('lpah/{id}/susut', 'LpahController@updatesusut')->name('lpah.updatesusut');
            Route::patch('lpah/{id}/susut', 'LpahController@jambongkar')->name('lpah.jambongkar');
            // Route::post('lpah/{id}/update_urut', 'LpahController@updateUrut')->name('lpah.updateurut');
            // Route::post('lpah/{id}/editkeranjang', 'LpahController@edit')->name('lpah.edit');
            // Route::get('lpah/{id}/info', 'LpahController@info')->name('lpah.info');
            // Route::get('lpah/{id}/isi', 'LpahController@timbangisi')->name('lpah.isi');
            // Route::get('lpah/{id}/kosong', 'LpahController@timbangkosong')->name('lpah.kosong');

            Route::get('produksi/{id}', 'CheckerController@produksi')->name('checker.produksi');
            Route::get('produksi/{id}/edit', 'CheckerController@produksiEdit')->name('checker.produksiEdit');
            Route::get('produksi/netsuite/{id}', 'CheckerController@netsuite')->name('checker.netsuite');
            Route::post('produksi/create_itemreceipt_wo1', 'CheckerController@create_itemreceipt_wo1')->name('checker.create_itemreceipt_wo1');

            // Evis
            Route::get('evis/summary', 'EvisController@summary')->name('evis.summary');
            Route::get('evis/gabung', 'EvisController@gabung')->name('evis.gabung');
            Route::get('evis/peruntukan', 'EvisController@peruntukan')->name('evis.peruntukan');
            Route::patch('evis/peruntukan', 'EvisController@updateperuntukan')->name('evis.updateperuntukan');
            Route::get('evis/peruntukan/bb', 'EvisController@bbperuntukan')->name('evis.bbperuntukan');
            Route::get('evis/hasil-peruntukan', 'EvisController@hasil_peruntukan')->name('evis.hasilperuntukan');
            Route::get('evis/hasilproduksi', 'EvisController@hasilproduksi')->name('evis.hasilproduksi');
            Route::post('evis/peruntukan/delete', 'EvisController@delete')->name('evis.delete');
            Route::post('evis/peruntukan/deleteproduksi', 'EvisController@put')->name('evis.put');
            Route::post('evis/peruntukanselesai', 'EvisController@peruntukanselesai')->name('evis.peruntukanselesai');
            Route::post('evis/simpanbahanbaku', 'EvisController@simpanbahanbaku')->name('evis.simpanbahanbaku');
            Route::post('evis/evisfreestockstore', 'EvisController@evisfreestockstore')->name('evis.evisfreestockstore');
            Route::post('evis/gabung/edit', 'EvisController@editgabung')->name('evis.gabungedit');
            Route::post('evis/gabung', 'EvisController@addgabung')->name('evis.addgabung');
            Route::get('evis/gabung/cart', 'EvisController@cartgabung')->name('evis.cartgabung');
            Route::get('evis/gabung/bahanbaku', 'EvisController@bbevis')->name('evis.cartbahanbaku');
            Route::patch('evis/gabung/update', 'EvisController@updategabung')->name('evis.updategabung');

            Route::get('evis', 'EvisController@index')->name('evis.index');
            Route::post('evis', 'EvisController@store')->name('evis.store');
            Route::post('evis/editevis', 'EvisController@editevis')->name('evis.editevis');
            Route::get('evis/orders', 'EvisController@salesOrder')->name('evis.orders');
            Route::get('evis/show/order', 'EvisController@order')->name('evis.order');


            Route::get('evis/inputorder', 'EvisController@inputorder')->name('evis.inputorder');


            Route::get('evis/{id}', 'EvisController@show')->name('evis.show');
            Route::post('evis/{id}', 'EvisController@add')->name('evis.add');
            Route::delete('evis/{id}', 'EvisController@deleteitem')->name('evis.deleteitem');
            Route::get('evis/{id}/keranjang', 'EvisController@cart')->name('evis.cart');
            Route::post('evis/{id}/editkeranjang', 'EvisController@edit')->name('evis.edit');
            Route::patch('evis/{id}', 'EvisController@update')->name('evis.update');
            Route::get('evis/{id}/result', 'EvisController@result')->name('evis.result');





            //Grading
            Route::resource('grading', 'GradingController', ['as' => 'admin']);
            Route::get('grading', 'GradingController@index')->name('grading.index');
            Route::post('grading', 'GradingController@store')->name('grading.store');
            Route::patch('grading/ubah/{id}', 'GradingController@ubah')->name('grading.ubah');
            Route::get('grading/{id}', 'GradingController@show')->name('grading.show');
            Route::post('grading/{id}', 'GradingController@add')->name('grading.add');
            Route::patch('grading/{id}', 'GradingController@update')->name('grading.update');
            Route::delete('grading/{id}', 'GradingController@destroy')->name('grading.destroy');
            Route::get('grading/{id}/keranjang', 'GradingController@cart')->name('grading.cart');
            Route::get('grading/{id}/kalkulasi', 'GradingController@kalkulasi')->name('grading.kalkulasi');
            Route::get('grading/{id}/result', 'GradingController@result')->name('grading.result');
            Route::post('grading/{id}/editkeranjang', 'GradingController@edit')->name('grading.edit');
            Route::get('grading/memar/{id}', 'GradingController@memar')->name('grading.memar');
            Route::get('grading/normal/{id}', 'GradingController@normal')->name('grading.normal');
            Route::get('grading/utuh/{id}', 'GradingController@utuh')->name('grading.utuh');
            Route::get('grading/pejantan/{id}', 'GradingController@pejantan')->name('grading.pejantan');
            Route::get('grading/parent/{id}', 'GradingController@parent')->name('grading.parent');
            Route::patch('grading/injectGradingIR/{id}', 'GradingController@injectGradingIR')->name('grading.injectGradingIR');


            //PPIC
            Route::get('ppic', 'PPICController@index')->name('ppic.index');
            Route::get('ppic/show', 'PPICController@kepala')->name('ppic.kepalashow');
            Route::get('ppic/chiller-fg', 'PPICController@chiller_fg')->name('ppic.chiller_fg');
            Route::get('ppic/chiller-penyiapan', 'PPICController@chiller_penyiapan')->name('ppic.chiller_penyiapan');
            Route::post('ppic', 'PPICController@store')->name('ppic.store');
            Route::patch('ppic', 'PPICController@storefreestock')->name('ppic.storefreestock');
            Route::get('ppic/pending', 'PPICController@cartpending')->name('ppic.cartpending');
            Route::post('ppic/chiller', 'PPICController@sendchiller')->name('ppic.sendchiller');
            Route::post('ppic/abf', 'PPICController@sendabf')->name('ppic.sendabf');
            Route::get('ppic/orderpendingshow', 'PPICController@orderpendingshow')->name('ppic.orderpendingshow');
            Route::get('ppic/evaluasi', 'PPICController@evaluasi')->name('ppic.evaluasi');
            Route::get('ppic/ukuran', 'PPICController@ukuran')->name('ppic.ukuran');
            Route::post('ppic/ukuran', 'PPICController@prosesukuran')->name('ppic.prosesukuran');
            Route::post('ppic/ukuranbatal', 'PPICController@prosesukuranbatal')->name('ppic.prosesukuranbatal');

            Route::get('ppic/nonlb', 'PPICController@nonlb')->name('ppic.nonlb');
            Route::get('ppic/lb', 'PPICController@lb')->name('ppic.lb');
            Route::post('ppic/nonlb', 'PPICController@ppic_acc')->name('ppic.ppic_acc');
            Route::post('ppic/nonlb/batal', 'PPICController@ppic_batal')->name('ppic.ppic_batal');

            Route::post('ppic/chiller/abf', 'PPICController@toabf_fg')->name('ppic.toabf_fg');

            // Kepala Produksi
            Route::get('kepala-produksi', 'KepalaProduksiController@index')->name('kepalaproduksi.index');
            Route::get('kepala-produksi/show', 'KepalaProduksiController@kepala')->name('kepalaproduksi.kepalashow');
            Route::post('kepala-produksi', 'KepalaProduksiController@store')->name('kepalaproduksi.store');
            Route::patch('kepala-produksi', 'KepalaProduksiController@storefreestock')->name('kepalaproduksi.storefreestock');
            Route::get('kepala-produksi/pending', 'KepalaProduksiController@cartpending')->name('kepalaproduksi.cartpending');
            Route::post('kepala-produksi/chiller', 'KepalaProduksiController@sendchiller')->name('kepalaproduksi.sendchiller');
            Route::post('kepala-produksi/abf', 'KepalaProduksiController@sendabf')->name('kepalaproduksi.sendabf');
            Route::get('kepala-produksi/orderpendingshow', 'KepalaProduksiController@orderpendingshow')->name('kepalaproduksi.orderpendingshow');
            Route::get('kepala-produksi/evaluasi', 'KepalaProduksiController@evaluasi')->name('kepalaproduksi.evaluasi');
            Route::get('kepala-produksi/ukuran', 'KepalaProduksiController@ukuran')->name('kepalaproduksi.ukuran');
            Route::post('kepala-produksi/ukuran', 'KepalaProduksiController@prosesukuran')->name('kepalaproduksi.prosesukuran');
            Route::post('kepala-produksi/ukuranbatal', 'KepalaProduksiController@prosesukuranbatal')->name('kepalaproduksi.prosesukuranbatal');

            Route::get('kepala-produksi/bahanbaku/show', 'KepalaProduksiController@bahanbakushow')->name('kepalaproduksi.bahanbakushow');
            Route::get('kepala-produksi/bahanbaku/request', 'KepalaProduksiController@requestdetail')->name('kepalaproduksi.requestdetail');
            Route::post('kepala-produksi/bahanbaku/selesai', 'KepalaProduksiController@selesai')->name('kepalaproduksi.selesai');
            Route::post('kepala-produksi/bahanbaku/selesaiproses', 'KepalaProduksiController@selesaiproses')->name('kepalaproduksi.selesaiproses');

            Route::get('kepala-produksi/bahanbaku/requestbonless', 'KepalaProduksiController@requestbonless')->name('kepalaproduksi.requestbonless');
            Route::post('kepala-produksi/bahanbaku/addbonles', 'KepalaProduksiController@storeboneles')->name('kepalaproduksi.storeboneles');
            Route::get('kepala-produksi/bahanbaku/bonless/bb', 'KepalaProduksiController@bahanbakubonless')->name('kepalaproduksi.bahanbakubonless');
            Route::post('kepala-produksi/bahanbaku/bonless', 'KepalaProduksiController@prosesbonless')->name('kepalaproduksi.prosesbonless');

            Route::get('kepala-produksi/summary', 'KepalaProduksiController@summary')->name('kepalaproduksi.summary');
            Route::get('kepala-produksi/hasilpotong', 'KepalaProduksiController@hasilpotong')->name('kepalaproduksi.hasilpotong');

            Route::post('kepala-produksi/chiller/abf', 'KepalaProduksiController@toabfchiller')->name('kepalaproduksi.toabfchiller');

            Route::get('kepala-produksi/lainnya', 'KepalaProduksiController@lainnya')->name('kepalaproduksi.lainnya');
            Route::post('kepala-produksi/tograding', 'KepalaProduksiController@laingrading')->name('kepalaproduksi.storelaingrading');
            Route::post('kepala-produksi/tochiller', 'KepalaProduksiController@lainchiller')->name('kepalaproduksi.storelainchiller');
            Route::post('kepala-produksi/togudang', 'KepalaProduksiController@laingudang')->name('kepalaproduksi.storelaingudang');

            Route::get('kepala-produksi/inventory', 'KepalaProduksiController@inventory')->name('kepalaproduksi.inventory');
            Route::get('kepala-produksi/inventoryshow', 'KepalaProduksiController@showinventory')->name('kepalaproduksi.inventoryshow');
            Route::post('kepala-produksi/inventory', 'KepalaProduksiController@storeinventory')->name('kepalaproduksi.inventorystore');
            // Hasil Produksi

            Route::get('kepala-produksi/regu', 'ProduksiController@regu')->name('kepalaproduksi.regu');

            Route::get('hasil-produksi', 'HasilProduksiController@index')->name('hasilproduksi.index');
            Route::get('hasil-produks-non-lb', 'HasilProduksiController@nonlb')->name('hasilproduksi.nonlb');
            Route::get('customer-stock', 'HasilProduksiController@customer_stock')->name('customer.stock');

            Route::get('hasil-produks/edit', 'HasilProduksiController@edit')->name('hasilproduksi.edit');
            Route::get('hasil-produks/show', 'HasilProduksiController@editshow')->name('hasilproduksi.show');
            Route::post('hasil-produks/store', 'HasilProduksiController@editstore')->name('hasilproduksi.store');


            // Kepala Regu
            Route::get('kepala-regu', 'KepalaReguController@index')->name('kepalaregu.index');
            Route::get('kepala-regu/boneles', 'KepalaReguController@boneles')->name('kepalaregu.boneles');
            Route::post('kepala-regu/boneles/proses', 'KepalaReguController@bonelesproses')->name('kepalaregu.storeproses');
            //Route::post('kepala-regu/boneles', 'KepalaReguController@storefreeboneles')->name('kepalaregu.storeboneles');
            Route::get('kepala-regu/boneles/show', 'KepalaReguController@bonelesshow')->name('kepalaregu.bonelesshow');
            Route::get('kepala-regu/boneles/pending', 'KepalaReguController@bonelespending')->name('kepalaregu.bonelespending');
            Route::get('kepala-regu/bahanbaku/bonless', 'KepalaReguController@bahanbakubonless')->name('kepalaregu.bahanbakubonless');

            Route::get('kepala-regu/parting', 'KepalaReguController@parting')->name('kepalaregu.parting');
            Route::get('kepala-regu/parting/show', 'KepalaReguController@partingshow')->name('kepalaregu.partingshow');
            Route::get('kepala-regu/parting/pending', 'KepalaReguController@partingpending')->name('kepalaregu.partingpending');
            Route::get('kepala-regu/parting/freestock', 'KepalaReguController@partingfreestock')->name('kepalaregu.partingfreestock');
            Route::post('kepala-regu/parting/freestock', 'KepalaReguController@partingfreestockstore')->name('kepalaregu.partingfreestockstore');
            Route::post('kepala-regu/parting/freestockend', 'KepalaReguController@partingfreestockselesai')->name('kepalaregu.partingfreestockselesai');
            Route::post('kepala-regu/parting/freestok', 'KepalaReguController@partingfreestockdelete')->name('kepalaregu.partingfreestockdelete');
            Route::get('kepala-regu/partdetail', 'KepalaReguController@partingdetail')->name('kepalaregu.partingdetail');
            Route::post('kepala-regu/partdetail', 'KepalaReguController@storeparting')->name('kepalaregu.storeparting');

            Route::get('kepala-regu/marinasi', 'KepalaReguController@marinasi')->name('kepalaregu.marinasi');
            Route::get('kepala-regu/marinasi/show', 'KepalaReguController@marinasishow')->name('kepalaregu.marinasishow');
            Route::get('kepala-regu/marinasi/pending', 'KepalaReguController@marinasipending')->name('kepalaregu.marinasipending');
            Route::get('kepala-regu/marinasi/detail', 'KepalaReguController@marinasidetail')->name('kepalaregu.marinasidetail');
            Route::get('kepala-regu/marinasi/freestock', 'KepalaReguController@marinasifreestock')->name('kepalaregu.marinasifreestock');
            Route::post('kepala-regu/marinasi/freestock', 'KepalaReguController@marinasifreestockstore')->name('kepalaregu.marinasifreestockstore');
            Route::post('kepala-regu/marinasi/freestockend', 'KepalaReguController@marinasifreestockselesai')->name('kepalaregu.marinasifreestockselesai');
            Route::post('kepala-regu/marinasi/freestok', 'KepalaReguController@marinasifreestockdelete')->name('kepalaregu.marinasifreestockdelete');
            Route::post('kepala-regu/marinasi', 'KepalaReguController@storemarinasi')->name('kepalaregu.storemarinasi');

            Route::get('kepala-regu/whole', 'KepalaReguController@whole')->name('kepalaregu.whole');
            Route::get('kepala-regu/whole/show', 'KepalaReguController@wholeshow')->name('kepalaregu.wholeshow');
            Route::get('kepala-regu/whole/pending', 'KepalaReguController@wholespending')->name('kepalaregu.wholepending');
            Route::get('kepala-regu/whole/detail', 'KepalaReguController@wholedetail')->name('kepalaregu.wholedetail');
            Route::get('kepala-regu/whole/freestock', 'KepalaReguController@wholefreestock')->name('kepalaregu.wholefreestock');
            Route::post('kepala-regu/whole/freestock', 'KepalaReguController@wholefreestockstore')->name('kepalaregu.wholefreestockstore');
            Route::post('kepala-regu/whole/freestockend', 'KepalaReguController@wholefreestockselesai')->name('kepalaregu.wholefreestockselesai');
            Route::post('kepala-regu/whole/freestok', 'KepalaReguController@wholefreestockdelete')->name('kepalaregu.wholefreestockdelete');
            Route::post('kepala-regu/whole', 'KepalaReguController@storewhole')->name('kepalaregu.storewhole');


            Route::get('kepala-regu/frozen', 'KepalaReguController@frozen')->name('kepalaregu.frozen');
            Route::get('kepala-regu/frozen/show', 'KepalaReguController@frozenshow')->name('kepalaregu.frozenshow');
            Route::get('kepala-regu/frozen/pending', 'KepalaReguController@frozenpending')->name('kepalaregu.frozenpending');
            Route::get('kepala-regu/frozen/detail', 'KepalaReguController@frozendetail')->name('kepalaregu.frozendetail');
            Route::get('kepala-regu/frozen/freestock', 'KepalaReguController@frozenfreestock')->name('kepalaregu.frozenfreestock');
            Route::post('kepala-regu/frozen/freestock', 'KepalaReguController@frozenfreestockstore')->name('kepalaregu.frozenfreestockstore');
            Route::post('kepala-regu/frozen/freestockend', 'KepalaReguController@frozenfreestockselesai')->name('kepalaregu.frozenfreestockselesai');
            Route::post('kepala-regu/frozen/freestok', 'KepalaReguController@frozenfreestockdelete')->name('kepalaregu.frozenfreestockdelete');
            Route::post('kepala-regu/frozen', 'KepalaReguController@storefrozen')->name('kepalaregu.storefrozen');

            Route::post('kepala-regu/store', 'KepalaReguController@store')->name('kepalaregu.store');
            Route::post('kepala-regu/storeall', 'KepalaReguController@storeall')->name('kepalaregu.storeall');
            Route::post('kepala-regu/selesai', 'KepalaReguController@selesai')->name('kepalaregu.selesai');
            Route::post('kepala-regu/abf', 'KepalaReguController@sendabf')->name('kepalaregu.sendabf');
            Route::post('kepala-regu/chiller', 'KepalaReguController@sendchiller')->name('kepalaregu.sendchiller');
            Route::get('kepala-regu/bahanbaku/request', 'KepalaReguController@requestdetail')->name('kepalaregu.requestdetail');
            Route::post('kepala-regu/bahanbaku/request/store', 'KepalaReguController@storerequestbahanbaku')->name('kepalaregu.storerequestbahanbaku');
            Route::get('kepala-regu/bahanbaku/bonless/bb', 'KepalaReguController@requestdetailboneles')->name('kepalaregu.bbbonless');
            Route::get('kepala-regu/bahanbaku/bonless/ambil/bb', 'KepalaReguController@ambilbbbonless')->name('kepalaregu.ambilbbbonless');
            Route::post('kepala-regu/bahanbaku/addbonles', 'KepalaReguController@storefreeboneles')->name('kepalaregu.storeboneles');
            Route::post('kepala-regu/bahanbaku/bonless', 'KepalaReguController@prosesbonless')->name('kepalaregu.prosesbonless');

            // CALON DIHAPUS
            Route::get('kepala-regu/broiler', 'KepalaReguController@broiler')->name('kepalaregu.broiler');
            Route::get('kepala-regu/pejantan', 'KepalaReguController@pejantan')->name('kepalaregu.pejantan');
            Route::get('kepala-regu/kampung', 'KepalaReguController@kampung')->name('kepalaregu.kampung');
            Route::get('kepala-regu/parent', 'KepalaReguController@parent')->name('kepalaregu.parent');
            // CALON DIHAPUS

            Route::get('kepala-regu/temporary', 'KepalaReguController@temporary')->name('kepalaregu.temporary');
            Route::get('kepala-regu/databahan', 'KepalaReguController@databahan')->name('kepalaregu.databahan');
            Route::get('kepala-regu/hasilproduksibonless', 'KepalaReguController@hasilproduksibonless')->name('kepalaregu.hasilproduksibonless');
            Route::post('kepala-regu/bbbonles', 'KepalaReguController@bonelessreestockstore')->name('kepalaregu.bonelessreestockstore');
            Route::post('kepala-regu/bbbonles/freestok', 'KepalaReguController@bonelessfreestockdelete')->name('kepalaregu.bonelessfreestockdelete');




            Route::post('kepala-regu/free', 'KepalaReguController@freestock')->name('kepalaregu.freestock');

            Route::post('produksi', 'ProduksiController@store')->name('produksi.store');
            Route::patch('produksi', 'ProduksiController@ambilBB')->name('produksi.ambilBB');
            Route::delete('produksi', 'ProduksiController@destroy')->name('produksi.delete');
            Route::get('produksi-sales-order', 'ProduksiController@salesOrder')->name('produksi.salesorder');
            Route::get('produksi-summary-prod', 'ProduksiController@summaryprod')->name('produksi.summaryprod');
            Route::get('produksi-summary', 'ProduksiController@summary')->name('produksi.summary');
            Route::get('produksi-alokasi', 'ProduksiController@alokasi')->name('produksi.alokasi');
            Route::post('produksi/prosesorder', 'ProduksiController@storeprosesorder')->name('produksi.prosesorder');



            // ===============================================================================
            // BONGKARAN PRODUKSI
            // ===============================================================================
            Route::get('produksi-regu', 'ReguController@index')->name('regu.index');
            Route::post('produksi-regu', 'ReguController@store')->name('regu.store');
            Route::patch('produksi-regu', 'ReguController@ambilbb')->name('regu.ambilbb');
            Route::put('produksi-regu', 'ReguController@editproduksi')->name('regu.editproduksi');
            Route::get('produksi-regu/bahanbaku', 'ReguController@bahanbaku')->name('regu.bahanbaku');
            Route::get('produksi-regu/viewmodaledit', 'ReguController@viewmodaledit')->name('regu.viewmodaledit');
            Route::delete('produksi-regu', 'ReguController@destroy')->name('regu.delete');
            Route::get('produksi-regu/inject-konsumen', 'ReguController@inject')->name('regu.inject');
            Route::get('produksi-regu/inject-plastik', 'ReguController@injectplastik')->name('regu.injectplastik');

            Route::get('produksi-regu/request_order', 'ReguController@request_order')->name('regu.request_order');
            Route::get('produksi-regu/request_order/{id}', 'ReguController@request_view')->name('regu.request_view');

            Route::get('produksi-siap-kirim-export', 'ProduksiController@siap_kirim_export')->name('produksi.siap_kirim_export');

            Route::get('history/{id}/kepalaregu', 'ReguController@bahanBakuHistory')->name('history.produksi');
            Route::get('produksi-order','ReguController@order_produksi')->name('regu.order_produksi');
            // ===============================================================================
            // BONGKARAN PRODUKSI
            // ===============================================================================

            Route::get('custom-produksi/', 'CustomProduksi@index')->name('customproduksi.index');
            Route::get('custom-produksi/{client}', 'CustomProduksi@produksiDetail')->name('customproduksi.detail');
            Route::get('custom-produksi/summary/{client}', 'CustomProduksi@summary')->name('customproduksi.summary');

            Route::resource('options', 'OptionController', ['as' => 'option']);

            // Route::resource('warehouse', 'WarehouseController', ['as' => 'admin']);
            Route::get('warehouse', 'WarehouseController@index')->name('warehouse.index');
            Route::get('warehouse/show', 'WarehouseController@show')->name('warehouse.show');
            Route::post('warehouse', 'WarehouseController@store')->name('warehouse.store');

            Route::get('warehouse-stock', 'WarehouseController@warehouse_stock')->name('warehouse.stock');
            Route::get('warehouse-keluar', 'WarehouseController@warehouse_keluar')->name('warehouse.keluar');
            Route::get('warehouse-abf', 'WarehouseController@warehouse_abf')->name('warehouse.abf');
            Route::get('warehouse-masuk', 'WarehouseController@warehouse_masuk')->name('warehouse.masuk');
            Route::get('warehouse-edit-inout', 'WarehouseController@edit_warehouse_inout')->name('warehouse.edit_inout');
            Route::get('warehouse-inout', 'WarehouseController@warehouse_inout')->name('warehouse.inout');
            Route::get('warehouse-thawing', 'WarehouseController@warehouse_thawing')->name('warehouse.thawing');
            Route::get('warehouse-thawingfg', 'WarehouseController@thawingfg')->name('warehouse.thawingfg');
            Route::post('warehouse-thawingfg', 'WarehouseController@storethawingfg')->name('warehouse.postthawingfg');
            Route::get('warehouse-requestthawing', 'WarehouseController@warehouse_requestthawing')->name('warehouse.requestthawing');
            Route::get('warehouse-order', 'WarehouseController@warehouse_order')->name('warehouse.order');
            Route::get('warehouse-nonlb', 'WarehouseController@warehouse_nonlb')->name('warehouse.nonlb');
            Route::get('warehouse-export-csv', 'WarehouseController@warehouse_export')->name('warehouse.export');
            Route::get('warehouse/tracing/{id}', 'WarehouseController@data_tracing')->name('warehouse.tracing');
            Route::get('warehouse/inject', 'WarehouseController@inject')->name('warehouse.inject');
            Route::patch('warehouse/update', 'WarehouseController@update_stock')->name('warehouse.update_stock');
            Route::get('dashboard-warehouse', 'WarehouseDashboardController@index')->name('warehouse_dash.index');
            Route::get('dashboard-gudang', 'WarehouseDashboardController@dashboard')->name('warehouse_dash.dashboard');
            Route::get('dashboard/filter-supplier-lb', 'WarehouseDashboardController@filter_lb')->name('warehouse_dash.filter_lb');
            Route::get('dashboard/export-supplier-lb', 'WarehouseDashboardController@export_supplier_lb')->name('warehouse_dash.export_supplier_lb');
            // Route::get('dashboard/view-filter-lb', 'WarehouseDashboardController@view_filter_lb')->name('warehouse_dash.view_filter');
            Route::get('weekly-export-soh', 'WarehouseDashboardController@index')->name('export.allsoh');
            Route::get('/thawing-download', 'WarehouseController@downloadThawing')->name('thawingproses.download');
            Route::post('/adjustment-warehouse/{id}', 'WarehouseController@productAdjustment')->name('product.adjust');

            Route::get('inject-plastik', 'WarehouseController@inject_plastik_group')->name('warehouse.inject_plastik_group');
            Route::get('wh-soh', 'WarehouseController@wh_soh')->name('warehouse.wh_soh');
            Route::post('soh-ia', 'WarehouseController@soh_ia')->name('warehouse.soh_ia');
            Route::get('soh-detail', 'WarehouseController@soh_detail')->name('warehouse.soh_detail');
            Route::get('soh-edit/{id}','WarehouseController@soh_edit')->name('warehouse.soh_edit');
            Route::patch('soh-update/{id}','WarehouseController@soh_update')->name('warehouse.soh_update');


            // Warehouse per tanggal sekian
            Route::get('warehouse-showstock', 'WarehouseController@stock')->name('warehouse.showstock');

            // Category
            Route::get('category', 'CategoryController@index')->name('category.index');
            Route::get('category/show', 'CategoryController@show')->name('category.show');

            //Ekspedisi
            Route::get('ekspedisi', 'EkspedisiController@index')->name('ekspedisi.index');
            Route::post('ekspedisi', 'EkspedisiController@store')->name('ekspedisi.store');
            Route::get('ekspedisi/riwayat', 'EkspedisiController@riwayat')->name('ekspedisi.riwayat');
            Route::post('ekspedisi/riwayat/update', 'EkspedisiController@update')->name('ekspedisi.update');
            Route::get('ekspedisi/{id}', 'EkspedisiController@show')->name('ekspedisi.show');


            //Penyiapan
            Route::get('penyiapan', 'PenyiapanController@index')->name('penyiapan.index');
            Route::get('penyiapan-export', 'PenyiapanController@siapKirimCsv')->name('penyiapan.siapKirimExport');
            Route::get('penyiapan-data', 'PenyiapanController@siapKirimData')->name('penyiapan.siapKirimData');
            Route::get('penyiapan/order', 'PenyiapanController@penyiapanOrder')->name('penyiapan.order');
            Route::post('penyiapan/prosesorder', 'PenyiapanController@storeprosesorder')->name('penyiapan.prosesorder');
            Route::get('penyiapan-chiller', 'PenyiapanController@penyiapanChiller')->name('penyiapan.chiller');
            Route::post('penyiapan-simpan-alokasi', 'PenyiapanController@simpanAlokasi')->name('penyiapan.simpanalokasi');
            Route::get('penyiapan-delete-alokasi', 'PenyiapanController@deleteAlokasi')->name('penyiapan.deletealokasi');
            Route::get('penyiapan-fulfill-item', 'PenyiapanController@fulfillItem')->name('penyiapan.fulfillitem');
            Route::get('penyiapan-close-order', 'PenyiapanController@closeOrder')->name('penyiapan.closeorder');
            Route::get('penyiapan-pemenuhan', 'PenyiapanController@pemenuhanAlokasi')->name('penyiapan.pemenuhan');
            Route::post('penyiapan/batalorder','PenyiapanController@batalorder')->name('penyiapan.batalorder');
            Route::post('penyiapan-keterangan', 'PenyiapanController@simpanketerangan')->name('penyiapan.simpanketerangan');

            Route::get('fulfillment', 'FulfillmentController@index')->name('fulfillment.index');
            Route::get('fulfillment/order', 'FulfillmentController@orderList')->name('fulfillment.order');
            Route::get('fulfillment/orderitem', 'FulfillmentController@orderItem')->name('fulfillment.orderitem');
            Route::get('fulfillment/pemenuhan', 'FulfillmentController@pemenuhanAlokasi')->name('fulfillment.pemenuhan');
            Route::get('fulfillment-delete-alokasi', 'FulfillmentController@deleteAlokasi')->name('fulfillment.deletealokasi');

            Route::post('fulfillment/prosesorder', 'FulfillmentController@storeprosesorder')->name('fulfillment.prosesorder');
            Route::post('fulfillment-simpan-alokasi', 'FulfillmentController@simpanAlokasi')->name('fulfillment.simpanalokasi');
            Route::get('fulfillment-fulfill-item', 'FulfillmentController@fulfillItem')->name('fulfillment.fulfillitem');
            Route::get('fulfillment-selesaikan', 'FulfillmentController@selesaikan')->name('fulfillment.selesaikan');
            Route::get('fulfillment-pemenuhan', 'FulfillmentController@pemenuhanAlokasi')->name('fulfillment.pemenuhan');

            // data chiller dan gudang
            Route::get('fulfillment-data-product-gudang', 'FulfillmentController@data_product_gudang')->name('fulfillment.data_product_gudang');
            Route::get('fulfillment-data-chiller-fg', 'FulfillmentController@data_chiller_fg')->name('fulfillment.data_chiller_fg');
            Route::get('fulfillment-data-chiller-bb', 'FulfillmentController@data_chiller_bb')->name('fulfillment.data_chiller_bb');

            //Pengalokasian
            // Route::get('pengalokasian', 'PengalokasianController@index')->name('pengalokasian.index');
            // Route::get('pengalokasian-export', 'PengalokasianController@siapKirimExport')->name('pengalokasian.siapKirimExport');
            // Route::get('pengalokasian/order', 'PengalokasianController@pengalokasianOrder')->name('pengalokasian.order');
            // Route::post('pengalokasian/prosesorder', 'PengalokasianController@storeprosesorder')->name('pengalokasian.prosesorder');
            // Route::get('pengalokasian-chiller', 'PengalokasianController@pengalokasianChiller')->name('pengalokasian.chiller');
            // Route::post('pengalokasian-simpan-alokasi', 'PengalokasianController@simpanAlokasi')->name('pengalokasian.simpanalokasi');
            // Route::get('pengalokasian-delete-alokasi', 'PengalokasianController@deleteAlokasi')->name('pengalokasian.deletealokasi');
            // Route::get('pengalokasian-fulfill-item', 'PengalokasianController@fulfillItem')->name('pengalokasian.fulfillitem');
            // Route::get('pengalokasian-close-order', 'PengalokasianController@closeOrder')->name('pengalokasian.closeorder');
            // Route::get('pengalokasian-pemenuhan', 'PengalokasianController@pemenuhanAlokasi')->name('pengalokasian.pemenuhan');
            // Route::post('pengalokasian/batalorder','PengalokasianController@batalorder')->name('pengalokasian.batalorder');

            //Penyiapan
            Route::get('penyiapanfrozen', 'PenyiapanFrozenController@index')->name('penyiapanfrozen.index');
            Route::get('penyiapanfrozen/order', 'PenyiapanFrozenController@penyiapanfrozenOrder')->name('penyiapanfrozen.order');
            Route::post('penyiapanfrozen/prosesorder', 'PenyiapanFrozenController@storeprosesorder')->name('penyiapanfrozen.prosesorder');
            Route::get('penyiapanfrozen-storage', 'PenyiapanFrozenController@penyiapanfrozenStorage')->name('penyiapanfrozen.storage');
            Route::post('penyiapanfrozen-simpan-alokasi', 'PenyiapanFrozenController@simpanAlokasi')->name('penyiapanfrozen.simpanalokasi');
            Route::get('penyiapanfrozen-delete-alokasi', 'PenyiapanFrozenController@deleteAlokasi')->name('penyiapanfrozen.deletealokasi');
            Route::get('penyiapanfrozen-fulfill-item', 'PenyiapanFrozenController@fulfillItem')->name('penyiapanfrozen.fulfillitem');
            Route::get('penyiapanfrozen-close-order', 'PenyiapanFrozenController@closeOrder')->name('penyiapanfrozen.closeorder');
            Route::get('penyiapanfrozen-pemenuhan', 'PenyiapanFrozenController@pemenuhanAlokasi')->name('penyiapanfrozen.pemenuhan');

            //Driver
            Route::get('driver', 'DriverController@index')->name('driver.index');
            Route::post('driver', 'DriverController@store')->name('driver.store');
            Route::patch('driver', 'DriverController@update')->name('driver.update');

            // Stock mutasi
            Route::get('mutasi-chiller', 'MutasiStockController@chiller_index')->name('mutasistock.chiller');

            Route::get('driver/{id}', 'DriverController@show')->name('driver.show');
            Route::post('driver/{id}', 'DriverController@addekspedisi')->name('driver.addekspedisi');
            Route::patch('driver/{id}', 'DriverController@ready')->name('driver.ready');
            Route::put('driver/{id}', 'DriverController@complete')->name('driver.complete');
            Route::delete('driver/{id}', 'DriverController@destroy')->name('driver.destroy');
            Route::get('driver/{id}/order', 'DriverController@order')->name('driver.order');
            Route::post('driver/{id}/order', 'DriverController@addorder')->name('driver.addorder');
            Route::get('driver/{id}/route', 'DriverController@route')->name('driver.route');
            Route::post('driver/{id}/route', 'DriverController@batalroute')->name('driver.batalroute');
            Route::get('driver/{id}/result', 'DriverController@result')->name('driver.result');
            Route::get('driver/retur/{id}', 'DriverController@retur')->name('driver.retur');
            Route::post('driver/retur/add', 'DriverController@returadd')->name('driver.returadddriver');
            Route::get('delivery_order', 'ReportController@deliveryblank');
            Route::get('delivery_order/{id}', 'ReportController@delivery')->name('delivery');

            //QC
            Route::post('qc/export', 'QcController@export')->name('qc.export');
            Route::get('qc', 'QcController@index')->name('qc.index');
            Route::post('qc', 'QcController@update')->name('qc.update');
            Route::get('qc/nekropsi', 'QcController@nekropsi')->name('qc.nekropsi');
            Route::get('qc/nekropsi/{id}', 'QcController@nekropsi_show')->name('qc.nekropsi_show');
            Route::post('qc/nekropsi/{id}', 'QcController@nekropsi_post')->name('qc.nekropsi_post');
            Route::get('qc/{id}', 'QcController@show')->name('qc.show');
            Route::post('qc/{id}', 'QcController@antem')->name('qc.antem');
            Route::post('qc/{id}/post', 'QcController@post')->name('qc.post');
            Route::post('qc/{id}/add', 'QcController@add')->name('uniform.add');
            Route::post('qc/{id}/delete', 'QcController@delete')->name('uniform.delete');
            Route::get('qc/{id}/keranjang', 'QcController@cart')->name('uniform.cart');
            Route::get('qc/{id}/ker/summary/', 'QcController@summary')->name('uniform.summary');
            Route::get('qc/purchasing_retur/{id}', 'QcController@purchasing_retur')->name('qc.purchasing_retur');
            Route::resource('loading', 'LoadingController', ['as' => 'admin']);
            Route::get('qc-laporan/lpah', 'QcController@laporanlpah')->name('qc.lpah');
            Route::get('qc-siap-kirim-export', 'QcController@siap_kirim_export')->name('qc.siap_kirim_export');
            Route::get('qc_inject_uniformity/{id}', 'QcController@inject_uniformity')->name('qc.inject_uniformity');


            Route::resource('chiller', 'ChillerController', ['as' => 'admin']);

            Route::get('chillerstock/index', 'ChillerController@stockindex')->name('chiller.indexstock');
            Route::get('chillerstock/stock', 'ChillerController@stockchiller')->name('chiller.showstock');
            Route::post('chillerstock/edit', 'ChillerController@stockedit')->name('chiller.editstock');

            Route::get('chiller', 'ChillerController@index')->name('chiller.index');
            Route::post('chiller', 'ChillerController@store')->name('chiller.store');
            Route::get('chiller-export-csv', 'ChillerController@chiller_export')->name('chiller.export');
            Route::get('chiller-stock', 'ChillerController@chiller_stock')->name('chiller.stock');
            // Route::get('chiller-stock/kosong', 'ChillerController@stockkosong')->name('chiller.stockkosong');
            Route::get('chiller-masuk', 'ChillerController@chiller_masuk')->name('chiller.masuk');
            Route::get('chiller-keluar', 'ChillerController@chiller_keluar')->name('chiller.keluar');
            Route::get('injectdatachiller', 'ChillerController@injectevis');
            Route::get('inject_chiller', 'ChillerController@inject_chiller');
            Route::post('chiller/storetukar/{id}', 'ChillerController@tukarstore')->name('chiller.storetukar');
            Route::get('chiller/tukaritem/{id}', 'ChillerController@tukarindex')->name('chiller.tukar');
            Route::get('chiller/{id}', 'ChillerController@show')->name('chiller.show');
            Route::get('chiller-recalculate/{id}', 'ChillerController@recalculate')->name('chiller.recalculate');
            Route::post('chiller/{id}', 'ChillerController@update')->name('chiller.update');
            Route::get('chiller-customer', 'ChillerController@recustomer')->name('chiller.recustomer');

            Route::get('chiller-soh', 'ChillerController@soh')->name('chiller.soh');
            Route::post('chiller-adjustment', 'ChillerController@chilleradjustment')->name('chiller.adjustment');

            Route::get('tracing_bahan_baku', 'ChillerController@tracing_bahan_baku')->name('chiller.tracing_bahan_baku');

            // Route::resource('users', 'UserController', ['as' => 'admin']);
            Route::get('users', 'UserController@index')->name('users.index');
            Route::post('users', 'UserController@store')->name('users.store');
            Route::post('storehakakses', 'UserController@storehakakses')->name('users.storehakakses');
            Route::patch('users', 'UserController@update')->name('users.update');
            Route::put('users', 'UserController@akses')->name('users.akses');
            Route::get('/switch-color', 'UserController@switchcolor');

            // Route edit Profile
            Route::get('profile', 'UserController@profile')->name('profile');
            Route::patch('profile', 'UserController@profile')->name('profile.update');

            // Update item
            Route::patch('item', 'PembelianbarangController@updateItem')->name('item.update');
            // Customer
            // Route::get('customers', 'CustomerController@index')->name('customers.index');
            // Route::get('customers/{id}', 'CustomerController@show')->name('customers.show');
            // Route::post('customers', 'CustomerController@store')->name('customers.store');

            // Laporan
            Route::get('laporan/purchasing', 'PurchasingController@laporan')->name('purchasing.laporan');
            Route::get('laporan/sales-order', 'SalesOrderController@laporan')->name('salesorder.laporan');
            Route::get('laporan/datastock', 'DashboardController@laporan_stock')->name('datastock.laporan');
            Route::get('laporan/fulfillment', 'DashboardController@fulfillment')->name('fulfillment.laporan');
            Route::get('laporan/sales-order-add', 'SalesOrderController@salesadd')->name('salesorder.add');
            Route::get('laporan/sales-order/{id}', 'SalesOrderController@detail')->name('salesorder.detail');
            Route::get('laporan/export-sales-order/{id}', 'SalesOrderController@so_export')->name('salesorder.export');
            Route::get('laporan/sales-order/retur/{id}', 'SalesOrderController@retur')->name('salesorder.retur');
            Route::post('laporan/sales-order/retur', 'SalesOrderController@returadd')->name('salesorder.returadd');
            Route::post('upload-so-excel', 'SalesOrderController@uploadSOExcel');
            Route::post('upload-so-excel-meyer-global', 'SalesOrderController@uploadSOExcelMeyerGlobal');
            Route::post('upload_line_idso', 'SalesOrderController@upload_line_idso');
            Route::post('sales-order-edit/{id}', 'SalesOrderController@editSo')->name('salesorder.edit');
            Route::get('laporan/evis', 'EvisController@laporan')->name('evis.laporan');
            Route::get('laporan/driver', 'DriverController@laporan')->name('driver.laporan');
            Route::get('laporan/loaddriver', 'DriverController@loadlaporan')->name('driver.loadlaporan');
            Route::get('laporan/cari-driver', 'DriverController@cari_driver')->name('driver.cari_driver');
            Route::get('laporan/driver/{id}', 'DriverController@detail_laporan')->name('driver.detail_laporan');
            Route::get('laporan/prosentase/{id}', 'PurchasingController@hitung_prosentase')->name('laporan.prosentase');

            Route::get('customer-report', 'CustomerController@index')->name('customer.index');
            Route::get('customer-report/{id}', 'CustomerController@show')->name('customer.show');

            Route::get('harga-kontrak', 'HargaKontrakController@index')->name('hargakontrak.index');
            Route::post('harga-kontrak', 'HargaKontrakController@store')->name('hargakontrak.store');

            Route::get('laporan/lpah', 'LpahController@laporan')->name('laporan.lpah');
            Route::get('laporan/penerimaan-ayam-merah', 'LpahController@laporanayammerah')->name('laporan.laporanayammerah');
            Route::get('laporan/qc', 'QcController@laporan')->name('laporan.qc');
            Route::get('laporan/qc-retur', 'QcController@laporanRetur')->name('laporan.qc-retur');
            Route::get('laporan/qc-retur/where', 'QcController@retursummary')->name('laporan.qc-retur-where');
            Route::get('laporan/qc-export-retur', 'QcController@exportRetur')->name('laporan.qcexportretur');
            Route::get('laporan/qc-kualitas-karkas', 'QcController@kualitaskarkas')->name('laporan.qc-kualitas-karkas');

            // Item
            Route::get('item', 'ItemController@index')->name('item.index');
            Route::patch('item/{id}', 'ItemController@update')->name('update.item');
            Route::post('upload-item-excel', 'ItemController@uploadItemExcel');
            Route::post('update/akses/{id}', 'ItemController@updateAccess')->name('access.item');

            // Bumbbu
            Route::get('bumbu', 'BumbuController@index')->name('bumbu.index');
            Route::post('bumbu/store', 'BumbuController@store')->name('bumbu.store');
            Route::get('bumbu/{id}/edit', 'BumbuController@edit')->name('bumbu.edit');
            Route::patch('bumbu/{id}', 'BumbuController@update')->name('bumbu.update');
            Route::get('bumbu/{id}', 'BumbuController@show')->name('bumbu.show');
            Route::delete('bumbu/{id}', 'BumbuController@destroy')->name('bumbu.delete');
            Route::get('bumbu/create/baru', 'BumbuController@create')->name('bumbu.create');
            Route::get('get-bumbu/{bumbu_id}', 'BumbuController@getBumbu');
            Route::get('/download-bumbu', 'BumbuController@download')->name('bumbu.download');

            
            // Jual Sampingan

            Route::get('sampingan', 'SampinganController@index')->name('sampingan.index');
            Route::get('sampingan/show', 'SampinganController@datashow')->name('sampingan.datashow');
            Route::get('sampingan/show/proses', 'SampinganController@bahanbaku')->name('sampingan.proses');
            Route::get('/sampingan/show/order', 'SampinganController@order')->name('sampingan.order');
            Route::get('sampingan/show/summary', 'SampinganController@summary')->name('sampingan.summary');
            Route::post('sampingan', 'SampinganController@store')->name('sampingan.store');
            Route::post('sampingan/jualsampingan', 'SampinganController@storejualsampingan')->name('sampingan.jualsampingan');

            Route::get('sampingan-chiller', 'SampinganController@sampinganChiller')->name('sampingan.chiller');
            Route::post('sampingan-simpan-alokasi', 'SampinganController@simpanAlokasi')->name('sampingan.simpanalokasi');
            Route::get('sampingan-delete-alokasi', 'SampinganController@deleteAlokasi')->name('sampingan.deletealokasi');
            Route::get('sampingan-fulfill-item', 'SampinganController@fulfillItem')->name('sampingan.fulfillitem');
            Route::get('sampingan-close-order', 'SampinganController@closeOrder')->name('sampingan.closeorder');
            Route::get('sampingan-pemenuhan', 'SampinganController@pemenuhanAlokasi')->name('sampingan.pemenuhan');
            Route::post('sampingan/batalorder', 'SampinganController@batalorder')->name('sampingan.batalorder');

            Route::get('supplier', 'SupplierController@index')->name('supplier.index');
            Route::get('supplier/tukar', 'SupplierController@tukar')->name('supplier.tukar');
            Route::get('supplier/{id}', 'SupplierController@show')->name('supplier.show');
            Route::get('invoice', 'ReportController@invoiceblank');
            Route::get('invoice/{id}', 'ReportController@invoice')->name('invoice');

            Route::get('list-gudang', 'GudangController@index')->name('gudang.index');
            Route::get('list-gudang/show', 'GudangController@show')->name('gudang.show');
            Route::post('list-gudang/store', 'GudangController@store')->name('gudang.store');
            Route::patch('list-gudang/update/{id}', 'GudangController@update')->name('gudang.update');
            Route::delete('list-gudang/delete/{id}', 'GudangController@delete')->name('gudang.delete');

            // ABF
            Route::get('abf', 'AbfController@index')->name('abf.index');
            Route::post('abf', 'AbfController@store')->name('abf.togudang');
            Route::delete('abf', 'AbfController@destroy')->name('abf.destroy');
            Route::get('abf/show', 'AbfController@show')->name('abf.datashow');
            Route::get('abf/netsuite', 'AbfController@netsuite')->name('abf.netsuite');
            Route::get('abf/timbang/{id}', 'AbfController@timbang')->name('abf.timbang');
            Route::get('abf/bataltimbang/{id}', 'AbfController@batalkan')->name('abf.batalkan');
            Route::post('abf/timbang/{id}', 'AbfController@selesai')->name('abf.selesai');
            Route::delete('abf/timbang/{id}', 'AbfController@hapustimbang')->name('abf.hapustimbang');
            Route::post('/abf/simpan', 'AbfController@storetimbang')->name('abf.storetimbang');
            Route::post('/abf/terima', 'AbfController@terima')->name('abf.terima');
            Route::get('abf/inject', 'AbfController@inject');
            Route::get('abf/tracing/{id}', 'AbfController@data_tracing_abf')->name('abf.tracing');

            Route::get('abf-export-csv', 'AbfController@abf_export')->name('abf.export');
            Route::get('abf-stock', 'AbfController@abf_stock')->name('abf.stock');

            Route::get('abf-nonlb', 'AbfController@abf_nonlb')->name('abf.nonlb');

            // ABF tab pertama
            Route::post('/abf/chiller_kirim_abf', 'AbfController@chiller_kirim_abf')->name('abf.chiller_kirim_abf');
            Route::get('/abf/chiller_kirim_abf_acc/{id}', 'AbfController@chiller_kirim_abf_acc')->name('abf.chiller_kirim_abf_acc');
            Route::get('/abf/chiller_abf_stock', 'AbfController@chiller_abf_stock')->name('abf.chiller_abf_stock');
            // ABF tab kedua
            Route::get('/abf/abf_diterima', 'AbfController@abf_diterima')->name('abf.abf_diterima');
            // ABF gabung item
            // Route::get('/abf/abf_gabung_item', 'AbfController@abf_gabung_item')->name('abf.abf_gabung_item');
            Route::post('/abf/abf_gabung_item', 'AbfController@abf_gabung_item')->name('abf.abf_gabung_item');

            // Images
            Route::get('/image-lists', 'ImageController@getImages');
            Route::post('/store-image', 'ImageController@storeImageAjax');
            Route::resource('images', 'ImageController', ['as' => 'admin']);

            // Buku Besar

            Route::get('/bukubesar', 'BukubesarController@index')->name('bukubesar.index');
            Route::get('/bukubesar/export', 'BukubesarController@export')->name('bukubesar.export');
            Route::get('/bukubesar/export-lpah', 'BukubesarController@export_lpah')->name('bukubesar.exportlpah');
            Route::get('/bukubesar/export-qc', 'BukubesarController@export_qc')->name('bukubesar.exportqc');

            // Marketing
            Route::get('/marketing', 'MarketingController@index')->name('marketing.index');
            Route::get('/marketing/detail/{id}', 'MarketingController@detail')->name('marketing.detail');
            Route::resource('hak-akses', 'HakaksesController', ['as' => 'admin']);
            Route::get('/marketing/dashboard', 'MarketingController@dashboard')->name('marketing.dashboard');
            Route::get('/marketing/fulfillment', 'MarketingController@fulfillment')->name('marketing.fulfillment');
            Route::get('/marketing/stock', 'MarketingController@stock')->name('marketing.stock');


            // CRUD MarketingList
            Route::post('/marketing/store', 'MarketingController@store')->name('marketing.store');
            Route::get('/marketing/edit/', 'MarketingController@edit')->name('marketing.edit');
            Route::post('/marketing/update', 'MarketingController@update')->name('marketing.update');

            // Settings
            Route::get('/switch-color', 'UserController@switchcolor')->name('setting.color');
            Route::get('/logs', 'LogController@index')->name('logs.index');
            Route::get('/sync', 'LogController@indexSync')->name('sync.index');
            Route::get('/sync/wo-2', 'LogController@indexwo2')->name('sync.wo2');
            Route::get('/sync/wo-control', 'LogController@wo_control')->name('sync.wo_control');
            Route::get('/sync/wo-total', 'LogController@wo_total')->name('sync.wo_total');
            Route::get('/sync/{id}', 'LogController@showSync')->name('sync.showsync');
            Route::post('/sync/{id}', 'LogController@postSync')->name('sync.postsync');
            Route::get('/sync-detail/{id}', 'LogController@detail')->name('sync.detail');
            Route::get('/export-sync', 'LogController@exportSync')->name('sync.export');
            Route::get('/custom-export-sync', 'LogController@customExportSync')->name('sync.customexport');
            Route::get('/custom-download-sync', 'LogController@customDownloadSync')->name('sync.download-custom.view');
            Route::post('/custom-download-sync', 'LogController@customDownloadSync')->name('sync.download-custom');
            Route::post('/sync-cancel', 'LogController@cancelSync')->name('sync.cancel');
            Route::post('/sync/approve', 'LogController@approveSync')->name('sync.approve');
            Route::get('/sync-show', 'LogController@indexSyncShow')->name('sync.show');
            Route::get('/sync-process', 'LogController@syncProcess')->name('sync.process');
            Route::get('/sync-process-id', 'LogController@syncProcessID')->name('sync.process_id');
            Route::get('/sync-process-custom', 'LogController@syncProcessCustom')->name('sync.process_custom');
            Route::get('/notification', 'LogController@notification')->name('notification');
            Route::get('/list-notification', 'LogController@showNotification')->name('listnotification');
            Route::get('/count-notification', 'LogController@countNotification')->name('countnotification');
            Route::get('/sync-status', 'LogController@syncStatus')->name('syncstatus');
            Route::get('/sync-process', 'LogController@syncProcessApproval')->name('syncProcessApproval');
            Route::get('/delete-process/{id}', 'LogController@deleteNetsuite')->name('sync.deleteNetsuite');
            Route::post('/delete-process-array', 'LogController@deleteNetsuiteArray')->name('sync.deleteNetsuiteArray');
            Route::get('/status', 'UserController@userOnlineStatus')->name('useronline');

            Route::get('/inject-sync', 'LogController@injectSync')->name('sync.injectSync');

            Route::get('/report-purchdev', 'ReportController@purchasedelivered')->name('dashboard.purchdev');
            Route::get('/laporan', 'ReportController@lap')->name('dashboard.laporan');

            Route::get('/thawing', 'ThawingController@index')->name('index.thawing');
            Route::post('/thawing', 'ThawingController@store')->name('thawing.store');
            Route::patch('/thawing', 'ThawingController@update')->name('thawing.update');
            Route::get('/thawing/show', 'ThawingController@show')->name('show.thawing');
            Route::get('/thawing/keluar', 'ThawingController@keluar')->name('keluar.thawing');
            Route::get('thawing/request', 'ThawingController@requestthawing')->name('thawing.requestthawing');
            Route::post('/thawing/editproses', 'WarehouseController@editthawing')->name('thawing.editproses');

            Route::get('/thawing-proses', 'ThawingController@proses')->name('thawingproses.index');
            Route::delete('/thawing-proses', 'ThawingController@delete')->name('thawingproses.delete');
            Route::get('thawing-proses/request/{id}', 'WarehouseController@request_thawing')->name('warehouse.request_thawing');
            Route::post('thawing-proses/request/{id}', 'WarehouseController@request_thawingstore')->name('warehouse.request_thawingstore');
            Route::patch('thawing-proses/request/{id}', 'WarehouseController@request_thawingproses')->name('warehouse.request_thawingproses');
            Route::delete('thawing-proses/request/{id}', 'WarehouseController@request_thawingdestroy')->name('warehouse.request_thawingdestroy');
            Route::get('thawing-proses/{id}', 'WarehouseController@edit')->name('warehouse.edit');
            Route::post('thawing-proses/{id}', 'WarehouseController@timbang')->name('warehouse.timbang');
            Route::get('gudang/inject', 'WarehouseController@injectGudang')->name('warehouse.inject');
            
            Route::get('retur', 'ReturController@index')->name('retur.index');
            Route::post('retur', 'ReturController@store')->name('retur.store');
            Route::delete('retur', 'ReturController@destroy')->name('retur.destroy');
            Route::get('returcustomer', 'ReturController@customer')->name('retur.customer');
            Route::get('retur-by-customer', 'ReturController@returByCustomer')->name('retur.bycustomer');
            Route::get('returitem', 'ReturController@itemretur')->name('retur.itemretur');
            Route::post('returcustomer', 'ReturController@storecustomer')->name('retur.storecustomer');
            Route::post('retur/edit', 'ReturController@edit')->name('retur.edit');
            Route::post('retur/selesaikan', 'ReturController@selesaikan')->name('retur.selesaikan');
            Route::get('retur/custom-selesaikan', 'ReturController@custom_selesaikan')->name('retur.custom_selesaikan');
            Route::post('retur/delete', 'ReturController@deletecus')->name('retur.deletecus');
            Route::post('retur/deleteitem', 'ReturController@deleteitem')->name('retur.deleteitem');
            Route::post('retur/deleteitem', 'ReturController@deleteitem')->name('retur.deleteitem');
            Route::get('retur/summary', 'ReturController@retursummary')->name('retur.summary');
            Route::get('retur/summary-list', 'ReturController@retursummarylist')->name('retur.summary-list');
            Route::post('retur/so-submit', 'ReturController@returSoSubmit')->name('retur.sosubmit');
            Route::post('retur/nonso-submit', 'ReturController@returNonSoSubmit')->name('retur.nonsosubmit');
            Route::get('retur/retur-do', 'ReturController@returdo')->name('retur.returdo');
            Route::post('retur/retur-do', 'ReturController@returDoSoSubmit')->name('retur.returdosubmit');
            Route::get('retur/detail/{id}', 'ReturController@returDetail')->name('retur.detail');
            Route::get('retur/nonso', 'ReturController@returNonSo')->name('retur.nonso');
            Route::get('retur/meyer', 'ReturController@returmeyer')->name('retur.meyer');
            Route::get('retur/alasan', 'ReturController@alasanRetur')->name('retur.alasan');

            Route::post('retur/tanggal', 'ReturController@tanggal')->name('retur.tanggal');

            Route::get('/penerimaan-non-karkas', 'TerimaNonKarkas@index')->name('nonkarkas.index');
            Route::post('/penerimaan-non-karkas', 'TerimaNonKarkas@store')->name('nonkarkas.store');
            Route::get('/penerimaan-non-karkas/{id}', 'TerimaNonKarkas@show')->name('nonkarkas.show');
            Route::patch('/penerimaan-non-karkas/update', 'TerimaNonKarkas@update')->name('nonkarkas.update');

            Route::get('/gudang', 'PindahController@index')->name('pindah.index');
            Route::get('/gudang/show', 'PindahController@show')->name('pindah.show');
            Route::patch('/gudang/update/{id}', 'PindahController@update')->name('pindah.update');
            Route::post('/gudang', 'PindahController@store')->name('pindah.store');
            Route::delete('/gudang/delete/{id}', 'PindahController@delete')->name('pindah.delete');

            Route::get('/open-balance', 'OpenBalance@index')->name('openbalance.index');
            Route::get('/open-balance-list', 'OpenBalance@data')->name('openbalance.data');
            Route::post('/open-balance', 'OpenBalance@store')->name('openbalance.store');
            Route::patch('/open-balance/update', 'OpenBalance@update')->name('openbalance.update');
            Route::delete('/open-balance/delete/{id}', 'OpenBalance@destroy')->name('openbalance.delete');
            Route::patch('/open-balance', 'OpenBalance@import')->name('openbalance.import');
            Route::post('/upload-stock-chiller-fg', 'OpenBalance@upload_stock_chiller_fg')->name('openbalance.upload_stock_chiller_fg');
            Route::post('/upload-stock-chiller-bb', 'OpenBalance@upload_stock_chiller_bb')->name('openbalance.upload_stock_chiller_bb');
            Route::post('/upload-stock-cs', 'OpenBalance@upload_stock_cs')->name('openbalance.upload_stock_cs');
            Route::post('/upload-stock-opname', 'OpenBalance@upload_stock_opname')->name('openbalance.upload_stock_opname');
            Route::post('/upload-abf-cs', 'OpenBalance@upload_abf_cs')->name('openbalance.upload_abf_cs');
            Route::post('/upload-wo-thawing', 'OpenBalance@upload_wo_thawing')->name('openbalance.upload_wo_thawing');
            Route::post('/generate-ti-custom', 'OpenBalance@generate_ti_custom')->name('openbalance.generate_ti_custom');
            Route::post('/upload_abf_cs_wo', 'OpenBalance@upload_abf_cs_wo')->name('openbalance.upload_abf_cs_wo');
            Route::post('/upload_abf_cs_ti', 'OpenBalance@upload_abf_cs_ti')->name('openbalance.upload_abf_cs_ti');
            Route::post('/upload_wb3_recreate', 'OpenBalance@upload_wb3_recreate')->name('openbalance.upload_wb3_recreate');
            Route::post('/upload_customer', 'OpenBalance@upload_customer')->name('openbalance.upload_customer');
            Route::post('/upload_vendor', 'OpenBalance@upload_vendor')->name('openbalance.upload_vendor');
            Route::post('/upload_item', 'OpenBalance@upload_item')->name('openbalance.upload_item');
            Route::post('/upload_wo2_regu', 'OpenBalance@upload_wo2_regu')->name('openbalance.upload_wo2_regu');

            Route::get('musnahkan', 'MusnahkanController@index')->name('musnahkan.index');
            Route::post('musnahkan', 'MusnahkanController@store')->name('musnahkan.store');
            Route::get('musnahkan/riwayat', 'MusnahkanController@riwayat')->name('musnahkan.riwayat');
            // Route::get('musnahkan/item', 'MusnahkanController@item')->name('musnahkan.item');

            Route::get('repack', 'RepackController@index')->name('repack.index');
            Route::post('repack', 'RepackController@store')->name('repack.store');

            Route::get('pdf', 'PDFController@index')->name('pdf');
            Route::get('pdfnekropsi/{id}', 'PDFController@pdfnekropsi')->name('pdfnekropsi');

            Route::get('bom', 'BomController@index')->name('bom.index');


            Route::get('netsuite', 'NetsuiteController@index')->name('netsuite.index');


            // Laporan DATA STOCK
            Route::get('chiller-data-stock','LaporanController@datastock')->name('laporan.chillerdatastock');
            // Laporan Admin
            Route::get('laporanadmin','LaporanController@index')->name('laporanadmin.index');
            Route::get('laporanadmin/laporan','LaporanController@laporan')->name('laporanadmin.laporan');
            Route::get('laporanadmin/export','LaporanController@export')->name('laporanadmin.getexport');
            Route::post('laporanadmin/export','LaporanController@export')->name('laporanadmin.export');

            Route::get('laporanadmin/showDataTableAbf','LaporanController@showDataTableAbf')->name('laporanadmin.showDataTableAbf');
            Route::get('laporanadmin/showDataTableLpah','LaporanController@showDataTableLpah')->name('laporanadmin.showDataTableLpah');
            Route::get('laporanadmin/showDataTableEvis','LaporanController@showDataTableEvis')->name('laporanadmin.showDataTableEvis');
            Route::get('laporanadmin/showDataTableGrading','LaporanController@showDataTableGrading')->name('laporanadmin.showDataTableGrading');
            Route::get('laporanadmin/showDataTableFG','LaporanController@showDataTableFG')->name('laporanadmin.showDataTableFG');
            Route::get('laporanadmin/showDataTableBB','LaporanController@showDataTableBB')->name('laporanadmin.showDataTableBB');
            Route::get('laporanadmin/showDataTableSiapKirim','LaporanController@showDataTableSiapKirim')->name('laporanadmin.showDataTableSiapKirim');
            Route::get('laporanadmin/showDataTableSisaChiller','LaporanController@showDataTableSisaChiller')->name('laporanadmin.showDataTableSisaChiller');
            Route::get('laporanadmin/showDataTableGudang','LaporanController@showDataTableGudang')->name('laporanadmin.showDataTableGudang');
            Route::get('laporanadmin/showDataTableRetur','LaporanController@showDataTableRetur')->name('laporanadmin.showDataTableRetur');

            Route::get('wo/create','WOController@create')->name('wo.create');
            Route::post('wo/create','WOController@store')->name('wo.store');
            Route::post('wo/export','WOController@export')->name('wo.export');

            // Checker WO - 2
            Route::get('wo/wo-2-list','WOController@wo_2_list')->name('wo.wo_2_list');

            // Checker WO - 3
            Route::get('wo/wo-3-list','WOController@wo_3_list')->name('wo.wo_3_list');
            Route::get('wo/wo-3-process','WOController@wo_3_process')->name('wo.wo_3_process');
            Route::get('wo/wo-3-process-id/{id}','WOController@generate_wo3')->name('wo.generate_wo3');
            // Checker WO - 4
            Route::get('wo/wo-4-list','WOController@wo_4_list')->name('wo.wo_4_list');
            Route::get('wo/wo-4-process','WOController@wo_4_process')->name('wo.wo_4_process');
            Route::get('wo/wo-4-process/{id}','WOController@generate_wo4')->name('wo.generate_wo4');
            // Checker WO - 1
            Route::get('wo/wo-1-list','WOController@wo_1_list')->name('wo.wo_1_list');
            Route::get('wo/wo-1-process','WOController@wo_1_process')->name('wo.wo_1_process');
            // Checker SO
            Route::get('wo/so-list','WOController@so_list')->name('wo.so_list');
            Route::get('wo/so-process','WOController@so_process')->name('wo.so_process');
            // Checker Retur
            Route::get('wo/retur-list','WOController@retur_list')->name('wo.retur_list');
            Route::get('wo/retur-process','WOController@retur_process')->name('wo.retur_process');

            // Tracing item
            Route::get('tracing', 'TracingController@index')->name('tracing.index');

            Route::get('wo/chiller-fg','WOController@chiller_fg')->name('wo.chiller_fg');
            Route::post('wo/create_wo3','WOController@create_wo3')->name('wo.create_wo3');

            Route::get('supplier/edit', 'SettingController@supplier')->name('supplieredit.index');
            Route::post('supplier/edit', 'SettingController@supplierstore')->name('supplieredit.store');
            Route::post('transaksi/tutup', 'SettingController@tutupTransaksi')->name('tutup.transaksi');
            Route::post('transaksi/open', 'SettingController@bukaTransaksi')->name('open.transaksi');

            Route::post('checker/addgrading', 'CheckerController@addgrading')->name('checker.addgrading');
            Route::post('checker/addevis', 'CheckerController@addevis')->name('checker.addevis');

            Route::get('editso/{id}','EditSoController@index')->name('editso.index');
            Route::get('editso-get-integrasi-netsuite','EditSoController@getIntegrasiNetsuite')->name('editso.getintegrasinetsuite');
            Route::get('editsosampingan/{id}','EditSoController@sampingan')->name('editsosampingan.index');
            Route::get('editso-pemenuhan', 'EditSoController@pemenuhanAlokasi')->name('editso.pemenuhan');
            Route::get('editso-pemenuhan-sampingan', 'EditSoController@pemenuhanAlokasiSampingan')->name('editso.pemenuhansampingan');
            Route::get('editso-delete-alokasi', 'EditSoController@deleteAlokasi')->name('editso.deletealokasi');
            Route::get('editso-fulfill-item', 'EditSoController@fulfillItem')->name('editso.fulfillitem');
            Route::post('editso-simpan-alokasi', 'EditSoController@simpanAlokasi')->name('editso.simpanalokasi');
            Route::get('editso-batalkan/{id}', 'EditSoController@batalkan')->name('editso.batalkan');
            Route::post('editso-orderbahanbaku', 'EditSoController@editSoOrderBahanBaku')->name('editso.orderbahanbaku');
            Route::get('editso/{id}/riwayat', 'EditSoController@soHistory');

            Route::get('editso-kirimti', 'EditSoController@kirimti')->name('editso.kirimti');
            Route::get('editso-kirimfulfill', 'EditSoController@kirimfulfill')->name('editso.kirimfulfill');


            // ===============================================================================
            // LAPORAN WEEKLY
            // ===============================================================================
            Route::get('weekly/', 'WeeklyController@index')->name('weekly.index');
            Route::post('weekly-export/', 'WeeklyController@export_weekly')->name('weekly.export');

            Route::get('sync-produksi/', 'SyncProduksi@index')->name('syncprod.index');
            Route::get('sync-abf/', 'SyncAbf@index')->name('syncabf.index');


            // Pembelian Barang
            Route::get('pembelian-barang', 'PembelianbarangController@index')->name('pembelian.index');
            Route::post('pembelian-barang', 'PembelianbarangController@store')->name('pembelian.store');
            Route::get('pembelian-barang/riwayat', 'PembelianbarangController@riwayat')->name('pembelian.riwayat');
            Route::get('pembelian-barang/purchase', 'PembelianbarangController@purchase')->name('pembelian.purchase');
            Route::post('pembelian-barang/purchase', 'PembelianbarangController@purchasestore')->name('pembelian.purchasestore');
            Route::post('pembelian-barang/po-lb', 'PembelianbarangController@purchaselb')->name('pembelian.polb');
            Route::post('pembelian-barang/pononkarkas', 'PembelianbarangController@pononkarkas')->name('pembelian.pononkarkas');
            Route::post('pembelian-barang/pokarkas', 'PembelianbarangController@pokarkas')->name('pembelian.pokarkas');
            Route::post('pembelian-barang/destroy', 'PembelianbarangController@destroy')->name('pembelian.destroy');
            Route::get('pembelian-barang/inject', 'PembelianbarangController@inject')->name('pembelian.inject');

            Route::get('approve-pembelian', 'PembelianbarangController@riwayat')->name('pembelianapv.index');

            Route::get('generate-transferinventory', 'GenerateTI@index')->name('generateti.index');
            Route::post('generate-transferinventory', 'GenerateTI@store')->name('generateti.store');

            Route::get('generate-wo', 'GenerateWO@index')->name('generatewowob.index');
            Route::post('generate-wo', 'GenerateWO@store')->name('generatewowob.store');

            Route::get('buat-salesorder', 'GenerateSO@index')->name('buatso.index');
            Route::post('buat-salesorder', 'GenerateSO@store')->name('buatso.store');
            Route::post('update-salesorder', 'GenerateSO@update')->name('buatso.update');
            Route::post('delete-so-list', 'GenerateSO@destroy')->name('buatso.destroy');
            Route::get('so-netsuite-retry/{id}', 'GenerateSO@netsuite_retry')->name('buatso.netsuite_retry');

            Route::get('laporan-accounting', 'LaporanAccounting@index')->name('laporanaccounting.index');
            Route::get('laporan-marketing', 'LaporanMarketing@index')->name('laporanmarketing.index');
            Route::get('laporan-produksi', 'LaporanProduksiController@index')->name('laporanproduksi.index');
            Route::get('laporan-produksi/bbfg', 'LaporanProduksiController@hasilbbfg')->name('laporanproduksi.hasilbbfg');

            Route::post("/injectRecalculate", 'ChillerController@injectrecalculate')->name('injectRecalculate');
        });
    });
    Route::get('/progress_report/view', 'ReportProgressController@view_progress')->name('progress_report');
    Route::get('/progress_report/view_data', 'ReportProgressController@view_data')->name('view_progress');
    Route::get('/inject_tanggal_so', 'HomeController@inject_tanggal_so');
    Route::get('/inject_tanggal_potong', 'HomeController@inject_tanggal_potong');
    Route::get('/custom_function', 'HomeController@custom_function');
    Route::get('/inject_regu_kondisibb', 'HomeController@inject_regu_kondisibb');
    Route::get('/inject_regu_kondisibb_evis', 'HomeController@inject_regu_kondisibb_evis');
    Route::get('/inject_netsuite', 'Admin\LogController@inject_netsuite');
// });

Route::get('routes', function () {
    $routeCollection = Route::getRoutes();

    echo "<table style='width:100%'>";
    echo "<tr>";
    echo "<td width='10%'><h4>HTTP Method</h4></td>";
    echo "<td width='10%'><h4>Route</h4></td>";
    echo "<td width='10%'><h4>Name</h4></td>";
    echo "<td width='70%'><h4>Corresponding Action</h4></td>";
    echo "</tr>";
    foreach ($routeCollection as $value) {
        echo "<tr>";
        echo "<td>" . $value->methods()[0] . "</td>";
        echo "<td>" . $value->uri() . "</td>";
        echo "<td>" . $value->getName() . "</td>";
        echo "<td>" . $value->getActionName() . "</td>";
        echo "</tr>";
    }
    echo "</table>";
});
