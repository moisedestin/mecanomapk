<?php

namespace App\Http\Controllers\api;

use App\Driver;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

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

    public function deleteMechanicVehicle(Request $request) {
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

        $driver = auth()->user()->driver;
        $vehicle_to_del_data = $request->vehicle;

        Vehicle::where([
            'driver_id' => $driver->id,
            'mark' => $vehicle_to_del_data['mark'],
            'model' => $vehicle_to_del_data['model'],
            'transmission' => $vehicle_to_del_data['transmission'],
            'color' => $vehicle_to_del_data['color'],
            'year' => $vehicle_to_del_data['year']
        ])->delete();

        return $this->getAllVehicles($request);
    }
}
