<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Chat extends Model
{
    use SoftDeletes ;

    protected $table    =   'messages';

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id', 'id');
    }

    public static function show_chat($send, $receiver)
    {

        $chat = array();
        $data = Chat::where('sender_id', $send)
                ->where('receiver_id', $receiver)
                ->orderBy('id', 'DESC')
                ->get();

        foreach($data as $row){
            $chat[] = $row;
        }

        $data2 = Chat::where('receiver_id', $send)
                ->where('sender_id', $receiver)
                ->orderBy('id', 'DESC')
                ->get();

        foreach($data2 as $row){
            $chat[] = $row;
        }


        $time = array();
        foreach ($chat as $key => $val) {
            $time[$key] = $val->created_at;
        }
        
        array_multisort($time, SORT_ASC, $chat);

        return $chat;
    }
}
