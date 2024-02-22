<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Thawinglist extends Model
{
    protected $table = 'thawing_requestlist';

    public function gudang()
    {
        return $this->belongsTo(Product_gudang::class, 'item_id', 'id')->withTrashed();
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }
    
    public function relasi_thawing(){
        return $this->hasOne(Thawing::class,'id','thawing_id');
    }

    public static function tanggal_request_thawing($id){
        $sql = Thawing::find($id);
        if($sql->count() > 0){
                $hasil  = $sql->tanggal_request;
        }else{
            $hasil      = "";
        }
        return $hasil;
    }
}
