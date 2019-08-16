<?php

namespace App\Http\Controllers\api;

 use App\Models\Garage;
use App\Models\Location;
use App\Models\Mechanic;
 use App\User;
 use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
 use Illuminate\Support\Facades\Log;

 class MechanicController extends Controller
{
    public $successStatus = 200;

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


    public function getAllMechanic(Request $request) {

        if(!empty($request->mechanic_id)){
            $mechanics_list = Mechanic::where('id','!=',$request->input("mechanic_id"))->get();
            foreach ($mechanics_list as $mechanic){
                $mechanic->user = $mechanic->user;
                $mechanic->location = $mechanic->user->location;
                $mechanic->garage = Garage::where("mechanic_id",$mechanic->id)->first() ;

            }

            $mechanics = $mechanics_list->toArray();

            $result_mechanic = Mechanic::find($request->input("mechanic_id"));

            $result_mechanic->user = $result_mechanic->user;
            $result_mechanic->location = $result_mechanic->user->location;
            $result_mechanic->garage = Garage::where("mechanic_id",$result_mechanic->id)->first() ;

            array_unshift($mechanics , $result_mechanic);
        }
        else{
            $mechanics = Mechanic::all();
            foreach ($mechanics as $mechanic){
                $mechanic->user = $mechanic->user;
                $mechanic->location = $mechanic->user->location;
                $mechanic->garage = Garage::where("mechanic_id",$mechanic->id)->first() ;

            }
        }


        return response()->json($mechanics);
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

}
