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

    public function saveVehicle(Request $request) {
        if (!auth()->check()) {
            return response()->json([
                'message' => "Vous n'avez pas accès à cette ressource"
            ], 401);
        }
        if (!auth()->user()->driver) {
            return response()->json([
                'message' => "Vous devez avoir un compte chauffeur pour accéder à cetter ressource"
            ], 401);
        }

        $vehicle = new Vehicle();
        $vehicle->mark = $request->mark;
        $vehicle->model = $request->model;
        $vehicle->color = $request->color;
        $vehicle->country = $request->country;
        $vehicle->year = $request->year;
        $vehicle->transmission = $request->transmission;
        $vehicle->driver_id = auth()->user()->driver->id;
        $vehicle->save();

        return $vehicle;
    }
}
