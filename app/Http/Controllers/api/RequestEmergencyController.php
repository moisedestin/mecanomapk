<?php

namespace App\Http\Controllers\api;

use App\Models\Garage;
use App\Models\Location;
use App\Models\Mechanic;
use App\Models\Notification;
use App\Models\RequestEmergency;
use App\Models\Vehicle;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RequestEmergencyController extends Controller
{
    public $successStatus = 200;

    public function saveNotifClientMainRequest(Request $request) {

        $array = $request->detailVehicule;
        $detailvehicule = Vehicle::create($array);


        $location = Location::create($request->locations);



        $requestEmergency = new RequestEmergency;
        $requestEmergency->vehicule_id= $detailvehicule->id;
        $requestEmergency->location_id= $location->id;
        $requestEmergency->trouble = $array["trouble"];
        $requestEmergency->place_details = $array["place_details"];
        $requestEmergency->telephone = $array["telephone"];
        $requestEmergency->mechanic_user_id = $request->mechanic_user_id;
        $requestEmergency->driver_user_id = auth('api')->user()->id;
        $requestEmergency->save();

        $notification = new Notification;
        $notification->date = $array["date"];

        $notification->recipient_id = $request->mechanic_user_id;
        $notification->request_emergency_id = $requestEmergency->id;


        $driverName = User::where('id', auth('api')->user()->id)
            ->select('email')
            ->first()->email;
        $notification->body =  $driverName." a un probleme,cliquez ici pour voir plus d info";
        $notification->save();


        $destination_token = User::find($request->mechanic_user_id)->fbtoken;


//        $notification->pushnotification($destination_token,"mecanom","nouvelle notification");
//
//        //note that mechanic_id and user_id in these case is not id from mechanictable
//        //this is the user id for both
//
//        $notification2 = new Notification;
//        $notification2->delay = "30 min";
//        $notification2->status = 1;
//        $notification2->recipient_id = auth('api')->user()->id;
//        $notification2->request_emergency_id = $requestEmergency->id;
//        $notification2->date = $array["date"];
//
//        $driverName = User::where('id', $request->mechanic_user_id)
//            ->select('name')
//            ->first()->name;
//        $notification2->body = $driverName." a accepté la demande et sera la dans 10 min" ;
//        $notification2->save();
//
//        $request_emergency = RequestEmergency::find($requestEmergency->id);
//        $request_emergency->is_mechanic_agree = true;
//        $request_emergency->save();
//
//
//
//        $updateNotif = Notification::where("request_emergency_id",$requestEmergency->id)
//            ->where("recipient_id",$request->mechanic_user_id)
//            ->first();
//        $updateNotif->status = 1;
//        $updateNotif->save();
//
//
//        $destination_token = auth('api')->user()->fbtoken;
//
//        if($notification2->pushnotification($destination_token,"mecanom","nouvelle notification")){
//            return response()->json( $this->successStatus);
//
//        }
//        else
//            return response()->json( 405);


         if($notification->pushnotification($destination_token,"mecanom","nouvelle notification")){
             return response()->json( $this->successStatus);

         }
         else
             return response()->json( 405);


    }

    public function getRemainingTime(Request $request) {
        $notif_id = $request->notif_id;


        $notification =  Notification::find($notif_id);
        $arrayAllData = $notification->request_emergency->remainingTime($notification);





        $notif_id = $request->notif_id;


        $notification =  Notification::find($notif_id);

        $notification->mechanic_name = User::find($notification->request_emergency->mechanic_user_id)->email;
        $notification->driver_name = User::find($notification->request_emergency->driver_user_id)->email;
        $user =  User::find($notification->request_emergency->mechanic_user_id);

        $mechanic = Mechanic::where("user_id",$user->id)->first();

        $garage =  Garage::where("mechanic_id",$mechanic->id)->first();
        $notification->garage_name = $garage->name;

        $notification->garage_address = $garage->addresse;
        $notificationInfos = User::find($notification->request_emergency->driver_user_id);


        $notificationInfos->addHidden(["password","token"]);
        $requestEmergency = RequestEmergency::find($notification->request_emergency_id);
        $notification->process_success = $requestEmergency->process_success;
        $notification->process_fail = $requestEmergency->process_fail;
        $notification->mechanic_user_id = $requestEmergency->mechanic_user_id;
        $notification->driver_user_id = $requestEmergency->driver_user_id;

        $vehiculeDetail = Vehicle::find($requestEmergency->vehicule_id);
        $location = Location::find($requestEmergency->location_id);


        $arrayDV  = array();
        $arrayDV["detailVehicule"] = $vehiculeDetail  ;
        $arrayDV["remainingTime"] = $requestEmergency->remainingTime($notification) ;

        $arrayDV["trouble"] =  $requestEmergency->trouble ;
        $arrayLoc = array("locations" =>$location) ;
        $arrayNotif = array_merge($arrayDV, $arrayLoc,$notification->toArray());

        $arrayNotification = array("notifications" =>$arrayNotif) ;



        $arrayAllData = array_merge($arrayNotification, $notificationInfos->toArray() );






        return response()->json($arrayAllData);
    }


    public function sendProcessStatus(Request $request)
    {
        $request_emergency_id = $request->request_emergency_id;
        $success = $request->success;


        $requestEmergency = RequestEmergency::find($request_emergency_id);


        if($success == 0){
            $requestEmergency->process_fail = 1;
        }
        if($success == 1){
            $requestEmergency->process_success = 1;
        }

        $requestEmergency->save();

        if($success == 0){

            $notification = new Notification;
            $notification->status = 1;
            $notification->request_emergency_id = $requestEmergency->id;
            $notification->date = date("Y-m-d H:i:s");


            $mechanic = Mechanic::where("user_id",auth('api')->user()->id)->first();

            if($mechanic){

                $notification->body = auth('api')->user()->name." a annulé l'opération" ;
                $notification->recipient_id = $requestEmergency->driver_user_id;
                $notification->save();

                $destination_token = User::find($requestEmergency->driver_user_id)->fbtoken;

                if($notification->pushnotification($destination_token,"mecanom","nouvelle notification")){
                    return response()->json( $this->successStatus);

                }
                else
                    return response()->json( 405);
            }
            else{
                $notification->body = auth('api')->user()->email." a annulé l'opération" ;
                $notification->recipient_id =   $requestEmergency->mechanic_user_id;
                $notification->save();

                $destination_token = User::find($requestEmergency->mechanic_user_id)->fbtoken;

                if($notification->pushnotification($destination_token,"mecanom","nouvelle notification")){
                    return response()->json( $this->successStatus);

                }
                else
                    return response()->json( 405);
            }


        }

        else{

            $notification = new Notification;
            $notification->status = 1;
            $notification->request_emergency_id = $requestEmergency->id;
            $notification->date = date("Y-m-d H:i:s");


            $mechanic = Mechanic::where("user_id",auth('api')->user()->id)->first();

            if($mechanic){

                $notification->body = auth('api')->user()->name." a indiqué qu'il est arrivé" ;
                $notification->recipient_id = $requestEmergency->driver_user_id;
                $notification->save();

                $destination_token = User::find($requestEmergency->driver_user_id)->fbtoken;

                if($notification->pushnotification($destination_token,"mecanom","nouvelle notification")){
                    return response()->json( $this->successStatus);

                }
                else
                    return response()->json( 405);
            }


            return response()->json( $this->successStatus);

        }




    }


    public function getEmergenciesMechanic(Request $request) {


        $request_emergencies = RequestEmergency::where('mechanic_user_id',$request->id)
            ->where('is_mechanic_agree',true)
            ->where('process_success' , false)
            ->where('process_fail' , false)
            ->orderByDesc('created_at')
            ->get();



        $array_final_notif = [];

        foreach($request_emergencies as $request_emergency){
            foreach ($request_emergency->notifications as $notification){
                $notification->process_fail = $notification->request_emergency->process_fail;
                $notification->process_success = $notification->request_emergency->process_success;
                $notification->is_rate = $notification->request_emergency->is_rate;
                $notification->mechanic_user_id = $notification->request_emergency->mechanic_user_id;
                $notification->driver_user_id = $notification->request_emergency->driver_user_id;

                $delay = $notification->delay;
                $date_start = Carbon::parse($notification->created_at);
                $date_now =  Carbon::now();


                $minutes = 0;
                $seconds_total = 0;

                if (strpos($delay, 'hr') !== false) {
                    $delay = substr($delay,0,-3);
                    $minutes = (int)$delay * 60;
                    $seconds_total = $minutes * 60;

                }
                if (strpos($delay, 'min') !== false) {
                    $delay = substr($delay,0,-4);
                    $minutes = (int)$delay;
                    $seconds_total = $minutes * 60;

                }
                $date_start->addMinutes( $minutes);


                if( $date_start->greaterThan($date_now)){
                    $date_diff = $date_start->diff($date_now);
                    $minutes = $date_diff->days * 24 * 60;
                    $minutes += $date_diff->h * 60;
                    $minutes += $date_diff->i;
                    $seconds_remaining = $date_diff->s + ($minutes * 60);
                    $notification->end_time = false;
                    $notification->seconds_remaining = $seconds_remaining;
                    $notification->seconds_total = $seconds_total;
                    array_push($array_final_notif,$notification);
                }

                else{

                    $notification->end_time = true;
                }
            }
        }




        return response()->json($array_final_notif);
    }

    public function getEmergenciesDriver(Request $request) {


        $request_emergencies = RequestEmergency::where('driver_user_id',$request->id)
            ->where('is_mechanic_agree',true)
            ->where('process_success' , false)
            ->where('process_fail' , false)
            ->orderByDesc('created_at')
            ->get();



        $array_final_notif = [];

        foreach($request_emergencies as $request_emergency){
            foreach ($request_emergency->notifications as $notification){
                $notification->process_fail = $notification->request_emergency->process_fail;
                $notification->process_success = $notification->request_emergency->process_success;
                $notification->is_rate = $notification->request_emergency->is_rate;
                $notification->mechanic_user_id = $notification->request_emergency->mechanic_user_id;
                $notification->driver_user_id = $notification->request_emergency->driver_user_id;

                $delay = $notification->delay;
                $date_start = Carbon::parse($notification->created_at);
                $date_now =  Carbon::now();


                $minutes = 0;
                $seconds_total = 0;

                if (strpos($delay, 'hr') !== false) {
                    $delay = substr($delay,0,-3);
                    $minutes = (int)$delay * 60;
                    $seconds_total = $minutes * 60;

                }
                if (strpos($delay, 'min') !== false) {
                    $delay = substr($delay,0,-4);
                    $minutes = (int)$delay;
                    $seconds_total = $minutes * 60;

                }
                $date_start->addMinutes( $minutes);


                if( $date_start->greaterThan($date_now)){
                    $date_diff = $date_start->diff($date_now);
                    $minutes = $date_diff->days * 24 * 60;
                    $minutes += $date_diff->h * 60;
                    $minutes += $date_diff->i;
                    $seconds_remaining = $date_diff->s + ($minutes * 60);
                    $notification->end_time = false;
                    $notification->seconds_remaining = $seconds_remaining;
                    $notification->seconds_total = $seconds_total;
                    array_push($array_final_notif,$notification);
                }

                else{

                    $notification->end_time = true;
                }
            }
        }




        return response()->json($array_final_notif);
    }

}
