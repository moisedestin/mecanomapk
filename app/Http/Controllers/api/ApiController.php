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
        $detailvehicule->year = $array["year"];
        $detailvehicule->transmission = $array["transmission"];
        $detailvehicule->save();

        $location = new Location;
        $location->adress =$incomming_loc["adress"];
        $location->save();

        $requestEmergency = new RequestEmergency;
        $requestEmergency->vehicule_id= $detailvehicule->id;
        $requestEmergency->location_id= $location->id;
        $requestEmergency->trouble = $array["trouble"];
        $requestEmergency->save();

        $notification = new Notification;
        $notification->date = $array["date"];

        $notification->driver_id = $arrayUserData["id"];
        $notification->mechanic_id = $request->mechanic_id;
        $notification->principal_id = $request->mechanic_id;
        $notification->request_emergency_id = $requestEmergency->id;



        $driverName = User::where('id', $arrayUserData["id"])
            ->select('email')
            ->first()->email;
        $notification->body =  $driverName." a un probleme,cliquez ici pour voir plus d info";
        $notification->save();


         $destination_token = User::find($request->mechanic_id)->fbtoken;

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

    public function getAllNotif(Request $request) {


        $notifications = Notification::where('principal_id',$request->id)->get()->all();


        return response()->json($notifications);
    }
    public function getAllHisto(Request $request) {

        if($request->isMechanic == 0)
            $notifications = Notification::where('principal_id', '!=' ,$request->id)->get()->all();
        else
            $notifications = Notification::where('principal_id',$request->id)->get()->all();



        return response()->json($notifications);
    }
    public function getNotifInfo(Request $request) {
        $notif_id = $request->notif_id;


        $notification =  Notification::find($notif_id);

        $notification->mechanic_name = User::find($notification->mechanic_id)->email;
        $notification->driver_name = User::find($notification->driver_id)->email;
        $user =  User::find($notification->mechanic_id);

        $mechanic = Mechanic::where("user_id",$user->id)->first();

        $garage =  Garage::where("mechanic_id",$mechanic->id)->first();
         $notification->garage_name = $garage->name;

        $notification->garage_address = $garage->addresse;
         $notificationInfos = User::find($notification->driver_id);


        $notificationInfos->addHidden(["password","token"]);
        $requestEmergency = RequestEmergency::find($notification->request_emergency_id);

        $vehiculeDetail = Vehicle::find($requestEmergency->vehicule_id);
        $location = Location::find($requestEmergency->location_id);


        $arrayDV  = array();
        $arrayDV["detailVehicule"] = $vehiculeDetail  ;

        $arrayDV["trouble"] =  $requestEmergency->trouble ;
        $arrayLoc = array("locations" =>$location) ;
        $arrayNotif = array_merge($arrayDV, $arrayLoc,$notification->toArray());

        $arrayNotification = array("notifications" =>$arrayNotif) ;



        $arrayAllData = array_merge($arrayNotification, $notificationInfos->toArray() );




        return response()->json($arrayAllData);
    }

    public function notifRequestFromCancel(Request $request) {

        $notification = new Notification;
        $notification->status = 1;
        $notification->driver_id =$request->driver_id;
        $notification->mechanic_id = $request->mechanic_id;
        $notification->principal_id = $request->driver_id;
        $notification->request_emergency_id = $request->request_emergency_id;
        $notification->date = $request->date;


        $driverName = User::where('id', $request->mechanic_id)
            ->select('name')
            ->first()->name;
        $notification->body = $driverName." a annulé la demande";
        $notification->save();

        $updateNotif = Notification::where("request_emergency_id",$request->request_emergency_id)
            ->where("principal_id",$request->mechanic_id)
            ->first();
        $updateNotif->status = 1;
        $updateNotif->save();

        $destination_token = User::find($request->driver_id)->fbtoken;

        if($this->pushnotification($destination_token,"mecanom","nouvelle notification")){
            return response()->json( $this->successStatus);

        }
        else
            return response()->json( 405);
    }

    public function notifRequestFromAccept(Request $request) {


        $notification = new Notification;
        $notification->driver_id =$request->driver_id;
        $notification->mechanic_id = $request->mechanic_id;
        $notification->delay = $request->delay;
        $notification->status = 1;
        $notification->principal_id = $request->driver_id;
        $notification->request_emergency_id = $request->request_emergency_id;
        $notification->date = $request->date;

        $driverName = User::where('id', $request->mechanic_id)
            ->select('name')
            ->first()->name;
        $notification->body = $driverName." a accepté la demande et sera la dans ".$request->delay;
        $notification->save();

        $updateNotif = Notification::where("request_emergency_id",$request->request_emergency_id)
            ->where("principal_id",$request->mechanic_id)
            ->first();
        $updateNotif->status = 1;
        $updateNotif->save();


        $destination_token = User::find($request->driver_id)->fbtoken;

        if($this->pushnotification($destination_token,"mecanom","nouvelle notification")){
            return response()->json( $this->successStatus);

        }
        else
            return response()->json( 405);
    }

    public function setRating(Request $request) {

        $mechanic = Mechanic::where("user_id",$request->mechanic_id)->first();
        $notifications = Notification::where("mechanic_id",$mechanic->user->id)->first();

        $notification = Notification::find($request->notif_id);
//        $notification->is_rate = 1;
//        $notification->save();

        $notification_qty = 0;

//
        if($notifications){
            $notification_qty = Notification::where("mechanic_id",$mechanic->user->id)
                ->where("is_rate",1)
                ->count();
         }

//        if($mechanic->rating != null){
//            $mechanic->rating = ($mechanic->rating + $request->rating)/$notification_qty;
//
//        }
//        else{
//            $mechanic->rating = (0 + $request->rating)/$notification_qty;
//
//        }

        $mechanic->save();
        return response()->json($this->successStatus);
    }



}
