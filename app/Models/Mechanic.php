<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

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
        'user_id','phone1','phone2','availability', 'services','total_rating','rating','image'
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

    public function setImageAttribute($value)
    {
        $attribute_name = "image";
        $disk = "public";
        $destination_path = "mechanics";

        // if the image was erased
        if ($value == null) {
            // delete the image from disk
            Storage::disk($disk)->delete($this->{$attribute_name});

            // set null in the database column
            $this->attributes[$attribute_name] = null;
        }

        // if a base64 was sent, store it in the db
        if (starts_with($value, 'data:image')) {
            // 0. Make the image
            $image = Image::make($value)->encode('jpg', 90);
            // 1. Generate a filename.
            $filename = md5($value . time()) . '.jpg';
            // 2. Store the image on disk.
            Storage::disk($disk)->put($destination_path . '/' . $filename, $image->stream());
            // 3. Save the path to the database
            $this->attributes[$attribute_name] = $destination_path . '/' . $filename;

        }
    }
}
