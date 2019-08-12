<?php

namespace App\Models;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

class RequestEmergency extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'request_emergencies';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ["mechanic_user_id","driver_user_id",
        "trouble",
        "place_details",
        "telephone",
        "is_rate",
        "is_mechanic_agree",
        "is_mechanic_arrived",
        "driver_check_arrived",
        "mechanic_decline",
        "driver_decline",
        "created_at"];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public function getMechanician( )
    {
        return  User::find($this->mechanic_user_id)->name."<br>".User::find($this->mechanic_user_id)->email;
    }




    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function notifications() {
        return $this->hasMany(Notification::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
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
