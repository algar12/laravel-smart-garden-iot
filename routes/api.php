<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\SensorReading;
use App\Models\Setting;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/sensor', function (Request $request) {
    // Tambah CORS header
    header('Access-Control-Allow-Origin: *');
    
    $validated = $request->validate([
        'moisture' => 'required|integer|min:0|max:100',
        'pump_status' => 'nullable|string',
        'sensor_raw' => 'nullable|integer',
        'device_id' => 'nullable|string',
        'timestamp' => 'nullable|integer',
    ]);

    // Convert timestamp dari millis ke datetime jika ada
    if (isset($validated['timestamp'])) {
        $validated['timestamp'] = now(); // Atau convert dari millis
    }

    SensorReading::create($validated);
    
    \Log::info('Sensor data saved', $validated);

    return response()->json([
        'status' => 'success',
        'message' => 'Data saved successfully'
    ]);
});

Route::get('/command', function () {
    header('Access-Control-Allow-Origin: *');
    
    $command = Setting::getValue('pump_command', 'PUMP_OFF');
    
    \Log::info('Command requested', ['command' => $command]);
    
    return response($command)->header('Content-Type', 'text/plain');
});