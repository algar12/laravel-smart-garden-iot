<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sensor_readings', function (Blueprint $table) {
            $table->id();
            $table->integer('moisture');                    // Kelembaban tanah (0-100%)
            $table->string('pump_status')->nullable();      // Status pompa (ON/OFF)
            $table->integer('sensor_raw')->nullable();      // Raw sensor value (0-1023)
            $table->string('device_id')->nullable();        // ID device Arduino
            $table->timestamp('timestamp')->nullable();     // Timestamp dari ESP8266
            $table->timestamps();                           // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_readings');
    }
};