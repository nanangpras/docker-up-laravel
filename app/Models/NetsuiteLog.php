<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NetsuiteLog extends Model
{
    //
    protected $table    =   'netsuite_log';
    protected $guarded = [];

    public function data_purchasing(){
        return $this->hasMany(NetsuitePurchasing::class, 'netsuite_log_id', 'id');
    }
    public function data_po_item_receipt(){
        return $this->hasMany(NetsuitePOItemReceipt::class, 'netsuite_log_id', 'id');
    }
    public function data_salesorder(){
        return $this->hasMany(NetsuiteSalesOrder::class, 'netsuite_log_id', 'id');
    }
    public function data_bom(){
        return $this->hasMany(NetsuiteBom::class, 'netsuite_log_id', 'id');
    }
    public function data_location(){
        return $this->hasMany(NetsuiteLocation::class, 'netsuite_log_id', 'id');
    }
    public function data_vendor(){
        return $this->hasMany(NetsuiteVendor::class, 'netsuite_log_id', 'id');
    }
    public function data_customer(){
        return $this->hasMany(NetsuiteCustomer::class, 'netsuite_log_id', 'id');
    }
    public function data_item(){
        return $this->hasMany(NetsuiteItem::class, 'netsuite_log_id', 'id');
    }
}
