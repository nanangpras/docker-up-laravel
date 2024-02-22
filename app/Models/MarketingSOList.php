<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MarketingSOList extends Model
{
    //
    use SoftDeletes;
    protected $table    =   'marketing_so_list';
    protected $guarded  = [];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function so_marketing()
    {
        return $this->belongsTo(MarketingSO::class, 'marketing_so_id', 'id') ;
    }

    public function item_plastik()
    {
        return $this->belongsTo(Item::class, 'plastik', 'id') ;
    }

    public static function cekItemByProduct($id){
        return MarketingSOList::select('category_id')->leftJoin('items','marketing_so_list.item_id','items.id')->where('item_id',$id)->pluck('category_id');
    }


}
