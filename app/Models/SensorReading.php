<?php

// File: app/Models/SensorReading.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'moisture',
        'pump_status',
        'sensor_raw',
        'device_id',
        'timestamp'
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'moisture' => 'integer',
        'sensor_raw' => 'integer'
    ];

    // Scope untuk data terbaru
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Scope untuk device tertentu
    public function scopeByDevice($query, $deviceId)
    {
        return $query->where('device_id', $deviceId);
    }
}