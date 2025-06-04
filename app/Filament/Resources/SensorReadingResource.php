<?php

// File: app/Filament/Resources/SensorReadingResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\SensorReadingResource\Pages;
use App\Models\SensorReading;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class SensorReadingResource extends Resource
{
    protected static ?string $model = SensorReading::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    
    protected static ?string $navigationLabel = 'Sensor Data';
    
    protected static ?string $modelLabel = 'Sensor Reading';
    
    protected static ?string $pluralModelLabel = 'Sensor Readings';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Sensor Data')
                    ->schema([
                        Forms\Components\TextInput::make('moisture')
                            ->label('Moisture (%)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%'),
                        
                        Forms\Components\Select::make('pump_status')
                            ->label('Pump Status')
                            ->options([
                                'ON' => 'ON',
                                'OFF' => 'OFF',
                            ])
                            ->default('OFF'),
                        
                        Forms\Components\TextInput::make('sensor_raw')
                            ->label('Raw Sensor Value')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(1023),
                        
                        Forms\Components\TextInput::make('device_id')
                            ->label('Device ID')
                            ->default('arduino_garden_01'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                
                TextColumn::make('moisture')
                    ->label('Moisture')
                    ->suffix('%')
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        $state < 30 => 'danger',
                        $state < 60 => 'warning',
                        default => 'success',
                    })
                    ->sortable(),
                
                BadgeColumn::make('pump_status')
                    ->label('Pump Status')
                    ->color(fn (string $state): string => match ($state) {
                        'ON' => 'success',
                        'OFF' => 'secondary',
                        default => 'secondary',
                    })
                    ->sortable(),
                
                TextColumn::make('sensor_raw')
                    ->label('Raw Value')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('device_id')
                    ->label('Device')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('created_at')
                    ->label('Recorded At')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->since()
                    ->tooltip(fn ($record) => $record->created_at->format('F j, Y \a\t g:i A')),
            ])
            ->filters([
                SelectFilter::make('pump_status')
                    ->label('Pump Status')
                    ->options([
                        'ON' => 'ON',
                        'OFF' => 'OFF',
                    ]),
                
                Filter::make('moisture_low')
                    ->label('Low Moisture (<30%)')
                    ->query(fn (Builder $query): Builder => $query->where('moisture', '<', 30)),
                
                Filter::make('moisture_high')
                    ->label('High Moisture (>70%)')
                    ->query(fn (Builder $query): Builder => $query->where('moisture', '>', 70)),
                
                Filter::make('today')
                    ->label('Today')
                    ->query(fn (Builder $query): Builder => $query->whereDate('created_at', Carbon::today())),
                
                Filter::make('last_hour')
                    ->label('Last Hour')
                    ->query(fn (Builder $query): Builder => $query->where('created_at', '>=', Carbon::now()->subHour())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('10s'); // Auto refresh setiap 10 detik
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSensorReadings::route('/'),
            'create' => Pages\CreateSensorReading::route('/create'),
            'edit' => Pages\EditSensorReading::route('/{record}/edit'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}