<?php

// File: app/Filament/Widgets/MoistureChartWidget.php

namespace App\Filament\Widgets;

use App\Models\SensorReading;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class MoistureChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Soil Moisture Levels';
    
    protected static ?int $sort = 1;
    
    // Mengatur column span ke full width
    protected int | string | array $columnSpan = 'full';
    
    // Menambahkan tinggi chart yang lebih besar
    protected static ?string $maxHeight = '800px'; // Tinggi maksimum diperbesar
    
    public ?string $filter = 'today';
    
    protected function getData(): array
    {
        // Pastikan menggunakan filter yang aktif
        $filter = $this->filter ?? 'today';
        
        $query = SensorReading::query();
        
        switch ($filter) {
            case 'today':
                $query->whereDate('created_at', Carbon::today());
                break;
            case 'week':
                $query->whereBetween('created_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ]);
                break;
            case 'month':
                $query->whereBetween('created_at', [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth()
                ]);
                break;
            case 'year':
                $query->whereBetween('created_at', [
                    Carbon::now()->startOfYear(),
                    Carbon::now()->endOfYear()
                ]);
                break;
            default:
                $query->whereDate('created_at', Carbon::today());
                break;
        }
        
        $readings = $query->orderBy('created_at')->get();
        
        // Debug: uncomment untuk melihat hasil query
        // dd($filter, $readings->count(), $readings->first()?->created_at);
        
        return [
            'datasets' => [
                [
                    'label' => 'Moisture Level (%)',
                    'data' => $readings->pluck('moisture')->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'fill' => true,
                ],
                [
                    'label' => 'Pump Status',
                    'data' => $readings->map(function ($reading) {
                        return $reading->pump_status === 'ON' ? 100 : 0;
                    })->toArray(),
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                    'type' => 'bar',
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $readings->map(function ($reading) {
                // Format label berdasarkan filter
                switch ($this->filter) {
                    case 'today':
                        return $reading->created_at->format('H:i');
                    case 'week':
                        return $reading->created_at->format('D H:i');
                    case 'month':
                        return $reading->created_at->format('M d');
                    case 'year':
                        return $reading->created_at->format('M Y');
                    default:
                        return $reading->created_at->format('H:i');
                }
            })->toArray(),
        ];
    }
    
    protected function getType(): string
    {
        return 'line';
    }
    
    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'This Week',
            'month' => 'This Month',
            'year' => 'This Year',
        ];
    }
    
    // Method untuk memastikan filter dapat diakses
    public function getFilter(): ?string
    {
        return $this->filter;
    }
    
    // Method untuk set filter programmatically jika diperlukan
    public function setFilter(?string $filter): void
    {
        $this->filter = $filter;
    }
    
    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false, // Penting: set ke false agar bisa mengatur tinggi custom
            'aspectRatio' => 1.5, // Rasio lebar:tinggi diperkecil untuk chart lebih tinggi
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'max' => 100,
                    'title' => [
                        'display' => true,
                        'text' => 'Moisture (%)',
                    ],
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'beginAtZero' => true,
                    'max' => 100,
                    'title' => [
                        'display' => true,
                        'text' => 'Pump Status',
                    ],
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
        ];
    }
    
    // Method tambahan untuk mengatur tinggi container
    public function getHeading(): ?string
    {
        return static::$heading;
    }
    
    // Override method ini jika perlu styling khusus
    protected function getExtraBodyAttributes(): array
    {
        return [
            'style' => 'height: 800px; min-height: 800px;', // Tinggi container chart diperbesar
        ];
    }
    
    // Method untuk mengatur header attributes
    protected function getHeaderWidgetAttributes(): array
    {
        return [
            'class' => 'fi-wi-chart-widget',
            'style' => 'height: 100%; min-height: 800px;'
        ];
    }
    
    // Method untuk styling tambahan
    protected function getExtraAttributes(): array
    {
        return [
            'style' => 'height: 900px; width: 100%;'
        ];
    }
}