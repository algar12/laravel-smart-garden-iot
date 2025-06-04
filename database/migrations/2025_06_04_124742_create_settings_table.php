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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();        // Key untuk setting (contoh: 'pump_command')
            $table->text('value');                  // Value setting (contoh: 'PUMP_ON', 'PUMP_OFF')
            $table->string('description')->nullable(); // Deskripsi setting
            $table->timestamps();
        });
        
        // Insert default pump command
        DB::table('settings')->insert([
            'key' => 'pump_command',
            'value' => 'PUMP_OFF',
            'description' => 'Remote pump control command',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};