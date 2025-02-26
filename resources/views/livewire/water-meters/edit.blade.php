<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Редактиране на водомер</h1>
            <a href="{{ route('water-meters.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                Назад
            </a>
        </div>
        
        @if (session()->has('error'))
            <div class="mt-4 bg-red-50 dark:bg-red-900 p-4 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif
        
        <div class="mt-6 bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-md">
            <form wire:submit="updateWaterMeter">
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Сериен номер -->
                        <div>
                            <label for="serial_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Сериен номер
                            </label>
                            <div class="mt-1">
                                <input type="text" wire:model="serial_number" id="serial_number" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" placeholder="Сериен номер">
                            </div>
                            @error('serial_number')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Тип -->
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Тип
                            </label>
                            <div class="mt-1">
                                <select wire:model="type" id="type" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                    <option value="cold">Студена вода</option>
                                    <option value="hot">Топла вода</option>
                                </select>
                            </div>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Апартамент -->
                        <div>
                            <label for="apartment_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Апартамент
                            </label>
                            <div class="mt-1">
                                <select wire:model="apartment_id" id="apartment_id" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                    <option value="">Изберете апартамент</option>
                                    @foreach($apartments as $apartment)
                                        <option value="{{ $apartment->id }}">Апартамент {{ $apartment->number }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('apartment_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Дата на инсталация -->
                        <div>
                            <label for="installation_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Дата на инсталация
                            </label>
                            <div class="mt-1">
                                <input type="date" wire:model="installation_date" id="installation_date" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                            </div>
                            @error('installation_date')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Начално показание -->
                        <div>
                            <label for="initial_reading" class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                                Начално показание (м³)
                                @if($readingsCount > 0)
                                    <span class="ml-2 px-2.5 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        Заключено
                                    </span>
                                @endif
                            </label>
                            <div class="mt-1">
                                <input type="number" step="0.001" wire:model="initial_reading" id="initial_reading" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" placeholder="0.000" {{ $readingsCount > 0 ? 'disabled' : '' }}>
                            </div>
                            @if($readingsCount > 0)
                                <p class="mt-1 text-xs text-yellow-600 dark:text-yellow-500">
                                    Началното показание не може да бъде променено, тъй като вече има въведени показания.
                                </p>
                            @endif
                            @error('initial_reading')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Статус -->
                        <div>
                            <div class="flex items-center mt-4">
                                <input type="checkbox" wire:model="is_active" id="is_active" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded">
                                <label for="is_active" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                    Активен
                                </label>
                            </div>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Когато деактивирате водомер, той вече няма да може да получава нови показания.
                            </p>
                            @error('is_active')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Информация за показания -->
                        <div class="sm:col-span-2 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Информация за показания</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Този водомер има {{ $readingsCount }} въведени показания.
                                @if($readingsCount > 0)
                                    <a href="{{ route('readings.index', ['meter' => $waterMeter->id]) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                        Преглед на всички показания
                                    </a>
                                @endif
                            </p>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                Последно показание: 
                                @if($waterMeter->latestReading())
                                    <span class="font-medium text-gray-900 dark:text-white">
                                        {{ number_format($waterMeter->latestReading()->current_reading, 3, '.', ' ') }} м³
                                    </span>
                                    от {{ $waterMeter->latestReading()->reading_date->format('d.m.Y') }}
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">Няма въведени показания</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 text-right sm:px-6">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Запази промените
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
