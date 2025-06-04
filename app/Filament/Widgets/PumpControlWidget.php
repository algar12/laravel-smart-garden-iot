<?php

namespace App\Filament\Widgets;

use App\Models\Setting;
use App\Models\SensorReading;
use Filament\Widgets\Widget;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;

class PumpControlWidget extends Widget implements HasForms
{
    use InteractsWithForms;
    
    protected static string $view = 'filament.widgets.pump-control';
    
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 1;
    
    public ?array $data = [];
    
    public function mount(): void
    {
        $this->form->fill([
            'pump_command' => Setting::getValue('pump_command', 'PUMP_OFF'),
            'auto_mode' => Setting::getValue('auto_mode', 'ON') === 'ON',
            'moisture_threshold' => Setting::getValue('moisture_threshold', 54),
        ]);
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Toggle::make('pump_command')
                    ->label('Manual Pump Control')
                    ->helperText('Override automatic pump control')
                    ->onColor('success')
                    ->offColor('danger')
                    ->formatStateUsing(fn ($state) => $state === 'PUMP_ON')
                    ->dehydrateStateUsing(fn ($state) => $state ? 'PUMP_ON' : 'PUMP_OFF'),
                
                Toggle::make('auto_mode')
                    ->label('Automatic Mode')
                    ->helperText('Enable automatic watering based on moisture level')
                    ->onColor('success')
                    ->offColor('gray')
                    ->dehydrateStateUsing(fn ($state) => $state ? 'ON' : 'OFF'),
                
                TextInput::make('moisture_threshold')
                    ->label('Moisture Threshold (%)')
                    ->helperText('Pump will turn ON when moisture is below this level')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->suffix('%')
                    ->default(54),
            ])
            ->statePath('data');
    }
    
    public function save(): void
    {
        $data = $this->form->getState();
        
        Setting::setValue('pump_command', $data['pump_command']);
        Setting::setValue('auto_mode', $data['auto_mode']);
        Setting::setValue('moisture_threshold', $data['moisture_threshold']);
        
        Notification::make()
            ->title('Settings Updated')
            ->body('Pump control settings have been saved successfully.')
            ->success()
            ->send();
    }
    
    public function getCurrentStatus(): array
    {
        $latestReading = SensorReading::latest()->first();
        
        return [
            'moisture' => $latestReading?->moisture ?? 0,
            'pump_status' => $latestReading?->pump_status ?? 'OFF',
            'last_update' => $latestReading?->created_at?->diffForHumans() ?? 'Never',
            'device_online' => $latestReading && $latestReading->created_at->gt(now()->subMinutes(5)),
        ];
    }
}