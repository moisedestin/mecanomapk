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
 use Illuminate\Support\Facades\Log;
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



    public function refreshFbToken(Request $request) {
        $user = User::find($request->id);

        $user->fbtoken = $request->fbtoken;
        $user->save();

        return response()->json( $this->successStatus);
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



    /**
     * @param $notification
     * @return array
     */




}
