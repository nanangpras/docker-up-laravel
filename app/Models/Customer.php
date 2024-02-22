<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Markdown;
use App\Models\Marketing;
use App\Models\Customer_address;
use App\Models\Sales_invoice;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Order;

class Customer extends Model
{
    //

    use SoftDeletes;
    protected $table = 'customers';

    public function customermarketing()
    {
        return $this->belongsTo(Marketing::class, 'marketing_id', 'id');
    }

    public function customeraddress()
    {
        return $this->hasMany(Customer_address::class, 'id', 'customer_id')->withTrashed();
    }

    public function customersales()
    {
        return $this->hasMany(Sales_invoice::class, 'id', 'customer_id')->withTrashed();
    }

    public function customerorder()
    {
        return $this->hasMany(Order::class, 'id', 'customer_id')->withTrashed();
    }

    public function konsumen()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function customersampingan()
    {
        return $this->hasMany(Hargakontrak::class, 'customer_id', 'id')->orderByDesc('id')->where('keterangan', 'Customer Sampingan')->withTrashed();
    }

    public function harga_kontrak()
    {
        return $this->hasMany(Hargakontrak::class, 'customer_id', 'id')->orderByDesc('id') ;
    }

    public function customer_bumbu()
    {
        return $this->hasMany(CustomerBumbu::class, 'customer_id', 'id');
    }

    // Di dalam model Bumbu
    public function bumbu()
    {
        return $this->belongsToMany(Customer::class, 'customer_bumbu', 'bumbu_id', 'customer_id');
    }

    public static function logsocustomer($id){
        $data   =   Customer::find($id) ;
        return $data->nama ;
    }
    public static function getDataFromCustomer($id,$data){
        $query          = Customer::select($data)->where('id',$id)->get();
        if($query->count() > 0){
            foreach($query as $value){
                $hasil  = $value->$data;
            }
        }else{
            $hasil      = '';
        }

        return $hasil;
    }
}
