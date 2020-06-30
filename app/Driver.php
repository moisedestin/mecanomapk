<?php

namespace App;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function vehicle() {
        return $this->belongsTo(Vehicle::class,'vehicule_id');
    }
}
