<?php

namespace App\Http\Controllers\api;

 use App\Models\Driver;
 use App\Models\Garage;
use App\Models\Location;
use App\Models\Mechanic;
use App\Models\Notification;
use App\Models\RequestEmergency;
use App\Models\Vehicle;
use App\User;
 use Carbon\Carbon;
 use DateInterval;
 use DateTime;
 use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    public $successStatus = 200;

    public function login(Request $request) {
        if(Auth::attempt([ 'email' => request('email'), 'password' => request('password')  ])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('kopilot')->accessToken;
//            return response()->json([
//                'user' => $user,
//                'token' => $success['token']
//            ], $this->successStatus);
            $user->token = $success['token'] ;
            $mechanic = Mechanic::where("user_id",$user->id)->first();
            if($mechanic)
                $user->ismechanic = 1 ;


            return $user->toJson();
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:users,email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $error_code = 401;
            if($validator->errors()->has('email')) {
                $error_code = 409;
            }
            return response()->json(['error'=>$validator->errors()], $error_code);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('mecanom')->accessToken;
        $user->token = $success['token'] ;
        $user->fbtoken = $input['fbtoken'];

        $user->save();

        //insert driver
        Driver::create([
            "user_id" => $user->id
        ]);

        return response()->json($user,$this->successStatus);
    }

    public function getAllMaker(Request $request) {
//        $mechanicList = array();

        $mechanics = Mechanic::all();
        foreach ($mechanics as $mechanic){
//               $mechanic->user->makeHidden(["password","token"]);
//               $location = Location::find($mechanic->location_id);
//
//               $arrayFullMechanicModel = array_merge(array("locations" =>$location), $mechanic->toArray());
//               $arrayFullMechanicModel1 = array_merge(array("user" =>$mechanic->user), $arrayFullMechanicModel);
//
//               array_push($mechanicList,$arrayFullMechanicModel1) ;
            $mechanic->location = $mechanic->user->location;
        }

        return $mechanics->toJson();
    }

    public function refreshFbToken(Request $request) {
        $user = User::find($request->id);

        $user->fbtoken = $request->fbtoken;
        $user->save();

        return response()->json( $this->successStatus);
    }

    public function sendMechanicLocation(Request $request) {

        $location = Location::create($request->location);

        $user = User::find($request->user_id);
        $user->location_id = $location->id;
        $user->save();

        return response()->json( $this->successStatus);
    }

    public function changeMechanicLocation(Request $request) {

        $incom_loc = $request->location;
        $user = User::find($request->user_id);



        $location = Location::find($user->location_id);

        $location->latitude = $incom_loc["latitude"];
        $location->longitude = $incom_loc["longitude"];

        $location->adress = $incom_loc["adress"];

        $location->save();


        return response()->json( $this->successStatus);
    }
    public function pushnotification($token, $title,$message)
    {
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

        $notification = [
            'title' => $title,
            'sound' => true,
            'body' => $message
        ];

        $extraNotificationData = ["message" => $notification ];

        $fcmNotification = [
            //'registration_ids' => $tokenList, //multple token array
            'to'        => $token, //single token
            'notification' => $notification,
            'data' => $extraNotificationData
        ];

        $headers = [
            'Authorization: key=AAAAv1r4rVo:APA91bGegIMyOQapHwCKiPa8bXkgYnKa4mZc_LlVfkJbIdTe8nWO8qWXX1lUnmNvnGSto6_xsWOaE_1a2n1i1DwMt2-6cjYi9FmRSVzOSs3UC5VKQHGNtVOUMXne9bXZ4_j4-VfWyali',
            'Content-Type: application/json'
        ];


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);

        return true;

    }

    public function saveNotifClientMainRequest(Request $request) {

        $detailvehicule = new Vehicle;

        $arrayUserData = $request->userDriver;
        $incomming_loc = $request->locations;
        $array = $request->detailVehicule;
        $detailvehicule->mark = $array["mark"];
        $detailvehicule->model = $array["model"];
        $detailvehicule->color = $array["color"];
        $detailvehicule->year = $array["year"];
        $detailvehicule->transmission = $array["transmission"];
        $detailvehicule->save();

        $location = new Location;
        $location->adress =$incomming_loc["adress"];
        $location->longitude =$incomming_loc["longitude"];
        $location->latitude =$incomming_loc["latitude"];
        $location->save();

        $requestEmergency = new RequestEmergency;
        $requestEmergency->vehicule_id= $detailvehicule->id;
        $requestEmergency->location_id= $location->id;
        $requestEmergency->trouble = $array["trouble"];
        $requestEmergency->mechanic_user_id = $request->mechanic_user_id;
        $requestEmergency->driver_user_id = $arrayUserData["id"];
        $requestEmergency->save();

        $notification = new Notification;
        $notification->date = $array["date"];

        $notification->recipient_id = $request->mechanic_user_id;
        $notification->request_emergency_id = $requestEmergency->id;



        $driverName = User::where('id', $arrayUserData["id"])
            ->select('email')
            ->first()->email;
        $notification->body =  $driverName." a un probleme,cliquez ici pour voir plus d info";
        $notification->save();


         $destination_token = User::find($request->mechanic_user_id)->fbtoken;

         if($this->pushnotification($destination_token,"mecanom","nouvelle notification")){
             return response()->json( $this->successStatus);

         }
         else
             return response()->json( 405);


    }

    public function getMechanicInfo(Request $request) {

        $mechanic_id = $request->input("mechanic_id");
         $mechanic = Mechanic::find($mechanic_id);
         $mechanic->user = $mechanic->user;
        $mechanic->garage = Garage::where("mechanic_id",$mechanic->id)->first() ;
        $mechanic->user->location = $mechanic->user->location;

//        $location = Location::find($mechanic->location_id);
//
//        $arrayFullMechanicModel = array_merge(array("locations" =>$location), $mechanic->toArray());


        return $mechanic->toJson();
    }

    public function getEmergenciesMechanic(Request $request) {


        $request_emergencies = RequestEmergency::where('mechanic_user_id',$request->id)
            ->where('is_mechanic_agree',true)
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

    public function getAllNotif(Request $request) {


        $notifications = Notification::where('recipient_id',$request->id)->get();


        foreach ($notifications as $notification){
            $notification->process_fail = $notification->request_emergency->process_fail;
            $notification->process_success = $notification->request_emergency->process_success;
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

        return response()->json($notifications);
    }
    public function getAllHisto(Request $request) {

        if($request->isMechanic == 0)
            $notifications = Notification::where('recipient_id', '!=' ,$request->id)->get()->all();
        else
            $notifications = Notification::where('recipient_id',$request->id)->get()->all();

        foreach ($notifications as $notification){
            $notification->process_fail = $notification->request_emergency->process_fail;
            $notification->process_success = $notification->request_emergency->process_success;
            $notification->is_rate = $notification->request_emergency->is_rate;
            $notification->mechanic_user_id = $notification->request_emergency->mechanic_user_id;
            $notification->driver_user_id = $notification->request_emergency->driver_user_id;


        }

        return response()->json($notifications);
    }
    public function getNotifInfo(Request $request) {


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
        $arrayDV["remainingTime"] = $this->remainingTime($notification) ;

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

        return response()->json( $this->successStatus);

    }
    public function getRemainingTime(Request $request) {
        $notif_id = $request->notif_id;


        $notification =  Notification::find($notif_id);

        $arrayAllData = $this->remainingTime($notification);


        return response()->json($arrayAllData);
    }


    public function notifRequestFromCancel(Request $request) {



        $notification = new Notification;
        $notification->status = 1;
        $notification->recipient_id = $request->driver_user_id;
        $notification->request_emergency_id = $request->request_emergency_id;
        $notification->date = $request->date;


        $driverName = User::where('id', $request->mechanic_user_id)
            ->select('name')
            ->first()->name;
        $notification->body = $driverName." a décliné la demande";
        $notification->save();

        $request_emergency = RequestEmergency::find($request->request_emergency_id);
        $request_emergency->process_fail = 1;
        $request_emergency->save();

        $updateNotif = Notification::where("request_emergency_id",$request->request_emergency_id)
            ->where("recipient_id",$request->mechanic_user_id)
            ->first();
        $updateNotif->status = 1;
        $updateNotif->save();

        $destination_token = User::find($request->driver_user_id)->fbtoken;

        if($this->pushnotification($destination_token,"mecanom","nouvelle notification")){
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
        $notification->date = $request->date;

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

        if($this->pushnotification($destination_token,"mecanom","nouvelle notification")){
            return response()->json( $this->successStatus);

        }
        else
            return response()->json( 405);
    }

    public function setRating(Request $request) {

        $mechanic = Mechanic::where("user_id",$request->mechanic_id)->first();


        $request_emergency = RequestEmergency::find($request->request_emergency_id);
        $request_emergency->is_rate = 1;
        $request_emergency->save();

        $notification_qty = 0;


        $mechanic->total_rating =  $mechanic->total_rating+$request->rating;


        $request_emergencies = RequestEmergency::where("mechanic_user_id",$mechanic->user->id)
            ->where("is_rate",1)
            ->first();

        if($request_emergencies){
            $request_emergencies = RequestEmergency::where("mechanic_user_id",$mechanic->user->id)
                ->where("is_rate",1)
                ->get();
            $notification_qty = count($request_emergencies) + 1;
         }

        if(!empty($mechanic->rating)){

            if($notification_qty == 0)
                $mechanic->rating = ($mechanic->total_rating+$request->rating);
            else
                $mechanic->rating = ($mechanic->total_rating+$request->rating)/$notification_qty;

        }
        else{
            if($notification_qty == 0)
                $mechanic->rating = $request->rating ;
            else
                $mechanic->rating =  $request->rating/$notification_qty;

        }

        $mechanic->save();
        return response()->json($this->successStatus);
    }

    public function getRemainingSeconds($delay){

    }

    /**
     * @param $notification
     * @return array
     */
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


}
