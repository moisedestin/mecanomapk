<?php

namespace App\Http\Controllers\api;

use App\Models\Garage;
use App\Models\Location;
use App\Models\Mechanic;
use App\Models\Notification;
use App\Models\RequestEmergency;
use App\Models\Vehicle;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class NotificationController extends Controller
{

    public $successStatus = 200;

    public function getAllNotif(Request $request) {


        $notifications = Notification::where('recipient_id',$request->id)->orderByDesc('created_at')->get();


        foreach ($notifications as $notification){

            $notification->mechanic_decline = $notification->request_emergency->mechanic_decline;
            $notification->driver_decline = $notification->request_emergency->driver_decline;
            $notification->driver_check_arrived = $notification->request_emergency->driver_check_arrived;
            $notification->is_mechanic_arrived = $notification->request_emergency->is_mechanic_arrived;
            $notification->driver_check_notarrived = $notification->request_emergency->driver_check_notarrived;



            $notification->is_rate = $notification->request_emergency->is_rate;
            $notification->mechanic_user_id = $notification->request_emergency->mechanic_user_id;
            $notification->driver_user_id = $notification->request_emergency->driver_user_id;

            $delay = $notification->delay;
            $date_start = Carbon::parse($notification->created_at);
            $date_now =  Carbon::now();


            $minutes = 0;

            if (strpos($delay, 'hr') !== false) {
                $delay = substr($delay,0,-3);
                $minutes = (int)$delay * 60;
            }
            if (strpos($delay, 'min') !== false) {
                $delay = substr($delay,0,-4);
                $minutes = (int)$delay;
            }
            $date_start->addMinutes( $minutes);


            if( $date_start->greaterThan($date_now))
                $notification->end_time = false;

            else
                $notification->end_time = true;




        }

        $notifications_put_read = Notification::where('recipient_id',$request->id)->orderByDesc('created_at')->get();

        foreach ($notifications_put_read as $notification_put_read) {
            $notification_put_read->read = 1;
            $notification_put_read->save();
        }

        return response()->json($notifications);
    }

    public function getAllHisto(Request $request) {

        if($request->isMechanic == 0)
            $notifications = Notification::where('recipient_id', '!=' ,$request->id)->orderByDesc('created_at')->get()->all();
        else
            $notifications = Notification::where('recipient_id',$request->id)->orderByDesc('created_at')->get()->all();

        foreach ($notifications as $notification){
            $notification->mechanic_decline = $notification->request_emergency->mechanic_decline;
            $notification->driver_decline = $notification->request_emergency->driver_decline;
            $notification->driver_check_arrived = $notification->request_emergency->driver_check_arrived;
            $notification->driver_check_notarrived = $notification->request_emergency->driver_check_notarrived;
            $notification->is_mechanic_arrived = $notification->request_emergency->is_mechanic_arrived;
            $notification->is_rate = $notification->request_emergency->is_rate;
            $notification->mechanic_user_id = $notification->request_emergency->mechanic_user_id;
            $notification->driver_user_id = $notification->request_emergency->driver_user_id;
            $notification->mechanic_name = User::find($notification->request_emergency->mechanic_user_id)->name;


        }

        return response()->json($notifications);
    }

    public function getNotifInfo(Request $request) {


        $notif_id = $request->notif_id;


        $notification =  Notification::find($notif_id);

        $notification->mechanic_name = User::find($notification->request_emergency->mechanic_user_id)->name;
        $notification->driver_name = User::find($notification->request_emergency->driver_user_id)->email;
        $user =  User::find($notification->request_emergency->mechanic_user_id);

        $mechanic = Mechanic::where("user_id",$user->id)->first();

        $garage =  Garage::where("mechanic_id",$mechanic->id)->first();
        $notification->garage_name = $garage->name;

        $notification->garage_address = $garage->addresse;
        $notificationInfos = User::find($notification->request_emergency->driver_user_id);


        $notificationInfos->addHidden(["password","token"]);
        $requestEmergency = RequestEmergency::find($notification->request_emergency_id);
        $notification->mechanic_decline = $requestEmergency->mechanic_decline;
        $notification->driver_decline = $requestEmergency->driver_decline;
        $notification->is_mechanic_arrived = $requestEmergency->is_mechanic_arrived;
        $notification->driver_check_arrived = $requestEmergency->driver_check_arrived;
        $notification->mechanic_user_id = $requestEmergency->mechanic_user_id;
        $notification->driver_user_id = $requestEmergency->driver_user_id;

        $vehiculeDetail = Vehicle::find($requestEmergency->vehicule_id);

        $vehiculeDetail["place_details"] = $requestEmergency->place_details;
        $vehiculeDetail["telephone"] = $requestEmergency->telephone;



        $location = Location::find($requestEmergency->location_id);


        $arrayDV  = array();
        $arrayDV["detailVehicule"] = $vehiculeDetail  ;
        $arrayDV["remainingTime"] = $this->remainingTime($notification) ;

        $arrayDV["trouble"] =  $requestEmergency->trouble ;
        $arrayLoc = array("locations" =>$location) ;
        $arrayNotif = array_merge($arrayDV, $arrayLoc,$notification->toArray());

        $arrayNotification = array("notifications" =>$arrayNotif) ;



        $arrayAllData = array_merge($arrayNotification, $notificationInfos->toArray() );




        return response()->json($arrayAllData);
    }

    public function remainingTime($notification)
    {
        $delay = $notification->delay;
        $date_start = Carbon::parse($notification->created_at);
        $date_now = Carbon::now();


        $minutes = 0;
        $seconds_total = 0;

        if (strpos($delay, 'hr') !== false) {
            $delay = substr($delay, 0, -3);
            $minutes = (int)$delay * 60;
            $seconds_total = $minutes * 60;
        }
        if (strpos($delay, 'min') !== false) {
            $delay = substr($delay, 0, -4);
            $minutes = (int)$delay;
            $seconds_total = $minutes * 60;

        }
        $date_start->addMinutes($minutes);


        if ($date_start->greaterThan($date_now)) {
            $date_diff = $date_start->diff($date_now);
            $minutes = $date_diff->days * 24 * 60;
            $minutes += $date_diff->h * 60;
            $minutes += $date_diff->i;
            $seconds_remaining = $date_diff->s + ($minutes * 60);
            $arrayAllData = [
                "seconds_remaining" => $seconds_remaining,
                "seconds_total" => $seconds_total,
                "end" => false
            ];
            return $arrayAllData;

        } else {
            $arrayAllData = [
                "seconds" => 0,
                "end" => true
            ];
            return $arrayAllData;
        }
    }

    public function notifRequestFromCancel(Request $request) {



        $notification = new Notification;
        $notification->status = 1;
        $notification->recipient_id = $request->driver_user_id;
        $notification->request_emergency_id = $request->request_emergency_id;
//        $notification->date = $request->date;
        $notification->date = date("j-m-y H:i");


        $driverName = User::where('id', $request->mechanic_user_id)
            ->select('name')
            ->first()->name;
        $notification->body = $driverName." a décliné la demande";
        $notification->save();

        $request_emergency = RequestEmergency::find($request->request_emergency_id);
        $request_emergency->mechanic_decline = 1;
        $request_emergency->save();

        $updateNotif = Notification::where("request_emergency_id",$request->request_emergency_id)
            ->where("recipient_id",$request->mechanic_user_id)
            ->first();
        $updateNotif->status = 1;
        $updateNotif->save();

        $destination_token = User::find($request->driver_user_id)->fbtoken;

        $notification_array = [
            'title' => "mecanom",
            'sound' => true,
            'body' => $notification->body
        ];

        if($notification->pushnotification($destination_token,$notification_array)){
            return response()->json( $this->successStatus);

        }
        else
            return response()->json( 405);
    }

    public function notifRequestFromAccept(Request $request) {

        //note that mechanic_id and user_id in these case is not id from mechanictable
        //this is the user id for both

        $notification = new Notification;
        $notification->delay = $request->delay;
        $notification->status = 1;
        $notification->recipient_id = $request->driver_user_id;
        $notification->request_emergency_id = $request->request_emergency_id;
//        $notification->date = $request->date;
        $notification->date = date("j-m-y H:i");

        $driverName = User::where('id', $request->mechanic_user_id)
            ->select('name')
            ->first()->name;
        $notification->body = $driverName." a accepté la demande et sera la dans ".$request->delay;
        $notification->save();

        $request_emergency = RequestEmergency::find($request->request_emergency_id);
        $request_emergency->is_mechanic_agree = true;
        $request_emergency->save();



        $updateNotif = Notification::where("request_emergency_id",$request->request_emergency_id)
            ->where("recipient_id",$request->mechanic_user_id)
            ->first();
        $updateNotif->status = 1;
        $updateNotif->save();


        $destination_token = User::find($request->driver_user_id)->fbtoken;

        $notification_array = [
            'title' => "mecanom",
            'sound' => true,
            'body' => $notification->body
        ];

        if($notification->pushnotification($destination_token,$notification_array)){
            return response()->json( $this->successStatus);

        }
        else
            return response()->json( 405);
    }
}
