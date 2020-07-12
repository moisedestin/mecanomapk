<?php

namespace App\Http\Controllers\api;

use App\Driver;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;

class VehicleController extends Controller
{
    public function getAllVehicles(Request $request) {
        $vehicles = auth()->user()->driver->vehicles;
        return $vehicles->toJson();
    }
}
