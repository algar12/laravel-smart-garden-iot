<?php

// File: app/Filament/Widgets/SystemStatsWidget.php

namespace App\Filament\Widgets;

use App\Models\SensorReading;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class SystemStatsWidget extends BaseWidget
{
    protected static ?int $sort = 0;
    
    protected function getStats(): array
    {
        $latestReading = SensorReading::latest()->first();
        $todayReadings = SensorReading::whereDate('created_at', Carbon::today())->count();
        $avgMoisture = SensorReading::whereDate('created_at', Carbon::today())->avg('moisture');
        $pumpOnTime = SensorReading::whereDate('created_at', Carbon::today())
            ->where('pump_status', 'ON')
            ->count();
        
        return [
            Stat::make('Current Moisture', $latestReading?->moisture ? $latestReading->moisture . '%' : 'No Data')
                ->description($latestReading?->created_at?->diffForHumans() ?? 'Never updated')
                ->descriptionIcon('heroicon-o-clock')
                ->color($latestReading && $latestReading->moisture < 30 ? 'danger' : 'success'),
            
            Stat::make('Pump Status', $latestReading?->pump_status ?? 'Unknown')
                ->description('Current pump state')
                ->descriptionIcon('heroicon-o-cog')
                ->color($latestReading?->pump_status === 'ON' ? 'success' : 'gray'),
            
            Stat::make('Today\'s Readings', $todayReadings)
                ->description('Data points collected')
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color('info'),
            
            Stat::make('Avg Moisture Today', $avgMoisture ? round($avgMoisture, 1) . '%' : 'No Data')
                ->description('Daily average')
                ->descriptionIcon('heroicon-o-beaker')
                ->color('warning'),
        ];
    }
}
