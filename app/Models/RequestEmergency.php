<?php

namespace App\Models;

use App\User;
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
    protected $fillable = ["mechanic_user_id","driver_user_id","trouble", "is_rate","process_success","process_fail","is_mechanic_agree","created_at"];
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

    public function getProcess( )
    {
        $success = '';

        if($this->process_success == 0)
            $success = '<span class="label label-default">No</span>';
        else
            $success = '<span class="label label-success">Yes</span>';

        $fail = '';

        if($this->process_fail == 0)
            $fail = '<span class="label label-default">No</span>';
        else
            $fail = '<span class="label label-danger">Yes</span>';

        return   '<a class="label label-default">No</a>'.'<a class="label label-default">No</span>';
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
}
