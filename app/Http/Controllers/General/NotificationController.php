<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use App\Models\Log;
use App\User;
use Illuminate\Http\Request;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use Illuminate\Support\Facades\Validator;
use FCM;

class NotificationController extends Controller
{
    //
    public function sendNotification(Request $request)
    {
        $activity = $request->get('activity');
        $table_relation = $request->get('table_name');
        $id_relation = $request->get('table_id');
        $from_user = $request->get('from_user');

        
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);

        $notificationBuilder = new PayloadNotificationBuilder();
        $notificationBuilder
            ->setTitle('Citra Guna Lestari')
            ->setBody("#".$id_relation." - ".ucwords($activity));

        $message = "#".$id_relation." - ".ucwords($activity);
        
        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'type' => 'production',
            'order_id' => $id_relation,
            'message' => $message
            ]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        // $downstreamResponse = FCM::sendTo($device_token, $option, $notification, $data);

        $user_device_app = User::where('fcm_token', '!=', '')->get();

        try {
            //code...
            foreach($user_device_app as $app_notif){
                $status = FCM::sendTo($app_notif->fcm_token, $option, $notification, $data);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return $th;
        }
        

        $status = 200;
        $msg = "send notification success";

        return response()->json(compact('status','msg'), 200);
    }
    
}
