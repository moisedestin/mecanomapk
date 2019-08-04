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


    public function getEmergenciesMechanic(Request $request) {


        $request_emergencies = RequestEmergency::where('mechanic_user_id',$request->id)
            ->where('is_mechanic_agree',true)
            ->where('process_success' , false)
            ->where('process_success' , false)
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
            ->where('process_success' , false)
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

        if($notification->pushnotification($destination_token,"mecanom","nouvelle notification")){
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

        if($notification->pushnotification($destination_token,"mecanom","nouvelle notification")){
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




}
