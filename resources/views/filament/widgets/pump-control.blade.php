<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-cog class="h-5 w-5" />
                Pump Control Panel
            </div>
        </x-slot>
        
        <div class="space-y-6">
            <!-- Current Status -->
            <div class="grid grid-cols-2 gap-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                @php
                    $status = $this->getCurrentStatus();
                @endphp
                
                <div>
                    <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Current Moisture</div>
                    <div class="text-2xl font-bold {{ $status['moisture'] < 30 ? 'text-red-600' : 'text-green-600' }}">
                        {{ $status['moisture'] }}%
                    </div>
                </div>
                
                <div>
                    <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Pump Status</div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full {{ $status['pump_status'] === 'ON' ? 'bg-green-500' : 'bg-gray-400' }}"></div>
                        <span class="text-lg font-semibold">{{ $status['pump_status'] }}</span>
                    </div>
                </div>
                
                <div class="col-span-2">
                    <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Device Status</div>
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full {{ $status['device_online'] ? 'bg-green-500' : 'bg-red-500' }}"></div>
                        <span class="text-sm">
                            {{ $status['device_online'] ? 'Online' : 'Offline' }} - 
                            Last update: {{ $status['last_update'] }}
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Control Form -->
            <form wire:submit="save">
                {{ $this->form }}
                
                <div class="mt-6 flex justify-end">
                    <x-filament::button type="submit" color="primary">
                        Save Settings
                    </x-filament::button>
                </div>
            </form>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>