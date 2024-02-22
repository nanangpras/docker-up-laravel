<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingSO extends Model
{
    //
    protected $table = 'marketing_so';
    protected $guarded = [];
    protected $appends  =   ['jumlah_rute'];

    public function listItem()
    {
        return $this->hasMany(MarketingSOList::class, 'marketing_so_id', 'id')->withTrashed();
    }

    public function itemActual()
    {
        return $this->hasMany(MarketingSOList::class, 'marketing_so_id', 'id');
    }

    public function socustomer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function souser()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    
    public function marketingekpedisirute()
    {
        return $this->hasMany(Ekspedisi_rute::class, 'no_so', 'no_so');
    }

    public function getJumlahRuteAttribute()
    {
        return Ekspedisi_rute::where('no_so', $this->no_so)
        ->groupBy('no_so')
        ->count();
    }

    public static function getDataFromMarketingSO($id,$data){
        $query          = MarketingSO::select($data)->where('id',$id)->get();
        if($query->count() > 0){
            foreach($query as $value){
                $hasil  = $value->$data;
            }
        }else{
            $hasil      = '';
        }

        return $hasil;
    }

    public static function getMemoAutocomplete($search,$subsidiary){
        $query          = MarketingSO::select('memo')
                                    ->where('subsidiary',$subsidiary)
                                    ->where(function($q) use ($search){
                                        if($search != ''){
                                            $q->where('memo','LIKE', '%'.$search.'%');
                                        }else{
                                            $q->where('memo','LIKE', '%%');
                                        }
                                    })
                                    ->groupBy('memo','subsidiary')
                                    ->orderBy('id','DESC')
                                    ->take(10)
                                    ->get();
        $array          = array();
        if($query->count() > 0){
            foreach($query as $value){
                $array[]  = $value->memo;
            }
        }else{
            $array = '';
        }

        return $array;
    }
}
