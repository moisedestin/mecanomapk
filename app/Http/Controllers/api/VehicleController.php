<?php

namespace App\Http\Controllers\api;

use App\Models\OfflineVehicle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VehicleController extends Controller
{
    public function getAllOfflineVehicles(Request $request) {
        $vehicles = auth()->user()->driver->offlineVehicles;
        return $vehicles->toJson();
    }

    public function saveOfflineVehicle(Request $request) {
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

        $vehicle = new OfflineVehicle();
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

    public function deleteOfflineVehicle(Request $request) {
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

        OfflineVehicle::where([
            'driver_id' => $driver->id,
            'mark' => $vehicle_to_del_data['mark'],
            'model' => $vehicle_to_del_data['model'],
            'transmission' => $vehicle_to_del_data['transmission'],
            'color' => $vehicle_to_del_data['color'],
            'year' => $vehicle_to_del_data['year']
        ])->delete();

        return $this->getAllOfflineVehicles($request);
    }
}
