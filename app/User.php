<?php

namespace App;

use App\Models\Location;
use App\Models\Mechanic;
use App\Models\RequestEmergency;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','location_id', 'email', 'password','token','fbtoken','authId'
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function driver() {
        return $this->hasOne(Driver::class);

    }

    public function mechanic() {
        return $this->hasOne(Mechanic::class);

    }

    public function location() {
        return $this->belongsTo(Location::class);

    }

    public function has_service_in_progress()
    {

        $mechanic = Mechanic::where("user_id", $this->id)->first();

        $request_emergencies = [];

        if ($mechanic)
            $request_emergencies = RequestEmergency::where("mechanic_user_id", $this->id)
                ->where('is_mechanic_agree',true)
                ->where('driver_check_arrived' , false)
                ->where('mechanic_decline' , false)
                ->where('driver_decline' , false)
                ->where('driver_check_notarrived' , false)
                ->get();
        else
            $request_emergencies = RequestEmergency::where("driver_user_id", $this->id)
                ->where('is_mechanic_agree',true)
                ->where('driver_check_arrived' , false)
                ->where('mechanic_decline' , false)
                ->where('driver_decline' , false)
                ->where('driver_check_notarrived' , false)
                ->get();



        if(count($request_emergencies) != 0)
            return true;
        else
            return false;


    }

}
