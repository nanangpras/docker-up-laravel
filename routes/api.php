<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['api_auth']], function () {
    Route::post('/netsuite/receive-po', "API\NetsuiteController@receivePO");
    Route::post('/netsuite/receive-po-item-receipt', "API\NetsuiteController@receivePOItemReceipt");
    Route::post('/netsuite/receive-so', "API\NetsuiteController@receiveSO");
    Route::post('/netsuite/receive-location', "API\NetsuiteController@receiveLocation");
    Route::post('/netsuite/receive-bom', "API\NetsuiteController@receiveBOM");

    Route::post('/netsuite/receive-item', "API\NetsuiteController@receiveItem");
    Route::post('/netsuite/receive-vendor', "API\NetsuiteController@receiveVendor");
    Route::post('/netsuite/receive-customer', "API\NetsuiteController@receiveCustomer");
    Route::post('/netsuite/receive-items', "API\NetsuiteController@receiveItemNS");
    
    Route::get('/netsuite/get-po', "API\NetsuiteController@getDataPO");
    Route::get('/netsuite/get-so', "API\NetsuiteController@getDataSO");
    Route::get('/netsuite/get-bom', "API\NetsuiteController@getDataBom");
    Route::get('/netsuite/get-location', "API\NetsuiteController@getDataLocation");
    Route::get('/netsuite/get-item', "API\NetsuiteController@getDataItem");
    Route::get('/netsuite/get-vendor', "API\NetsuiteController@getDataVendor");
    Route::get('/netsuite/get-customer', "API\NetsuiteController@getDataCustomer");
    Route::get('/netsuite/get-po-item-receipt', "API\NetsuiteController@getDataPOItermReceipt");

    Route::get('/marketing/get-marketing-so', "API\MarketingSOController@getDataMarketingSO");

    Route::get('/master/get-user', "API\UserAdminController@getDataUser");
    
});

Route::post('/cloudreport/receive_laporan_local', "API\CloudReceiveData@receive_laporan_local");

// Sudah tidak digunakan
// Route::get('/netsuite/list', "API\NetsuiteController@list");
// Route::get('/netsuite', "API\NetsuiteController@index");

// Route::post('/supplier', "API\SupplierController@store");
// Route::get('/purchasing', "API\PurchasingController@getData");
// Route::post('/purchasing', "API\PurchasingController@store");
// Route::get('/crawl-purchasing', "API\PurchasingController@crawlPurchasing");
// Route::get('/crawl-item', "API\ItemController@crawlItem");

// Route::post('/items', "API\ItemController@store");
// Route::post('/driver', "API\DriverAPIController@store");
// // Route::post('/customer', "API\CustmoerController@store");
// Route::get('/sales-order', "API\SalesOrderController@getData");
// Route::post('/sales-order', "API\SalesOrderController@store");
// Route::get('/item-receipt', "API\PurchasingController@itemReceipt");

// Route::get('/rendemen', "API\RendemenController@store_rendemen");
// Route::get('/sebaran-karkas', "API\SebaranKarkasController@store_sebaran");
// Route::get('/laporan-evis', "API\LaporanEvisController@store");

// Route::group(['prefix' => 'notification'], function () {
//     Route::post('send', ['as' => 'notification.all', 'uses' => 'General\NotificationController@sendNotification']);
// });

// Route::group(['prefix' => 'report'], function () {
//     Route::get('/get-chiller', 'API\ReportController@getChillerData')->name('api.report.chiller');
// });
