<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

class Mechanic extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'mechanics';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
//availability_field
    const AVAILABLE = 1;
    const NOT_AVAILABLE = 0;

    //haveScanner_field
    const HAVE_SCANNER = 1;
    const HAVENOt_SCANNER = 1;

    //haveTug_field
    const HAVE_TUG = 1;
    const HAVENOt_TUG = 1;

    protected $fillable = [
        'user_id','phone1','phone2','availability', 'services'
    ];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public function getUserName(){
       return User::find($this->user_id)->name;
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function user() {
        return $this->belongsTo(User::class);

    }

    public function garage() {
        return $this->hasOne(Garage::class);
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
