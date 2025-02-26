<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Въвеждане на показания</h1>
            <div class="flex space-x-3">
                <a href="{{ route('readings.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    Всички показания
                </a>
                <a href="{{ route('water-meters.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    Водомери
                </a>
            </div>
        </div>
        
        <!-- Съобщения за успех/грешка -->
        @if (session()->has('success'))
            <div class="mt-4 bg-green-50 dark:bg-green-900 p-4 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif
        
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
        
        <div class="mt-6 bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                    Въвеждане на нови показания
                </h3>
                <div class="mt-2 max-w-xl text-sm text-gray-500 dark:text-gray-400">
                    <p>
                        Изберете апартамент и водомер, за който искате да въведете ново показание.
                    </p>
                </div>
                
                <form wire:submit.prevent="saveReadings" class="mt-5">
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                        <!-- Избор на апартамент -->
                        <div>
                            <label for="apartmentId" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Апартамент
                            </label>
                            <div class="mt-1">
                                <select wire:model.live="apartmentId" id="apartmentId" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                    <option value="">Изберете апартамент</option>
                                    @foreach($this->availableApartments as $apartment)
                                        <option value="{{ $apartment->id }}">Апартамент {{ $apartment->number }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <!-- Избор на водомер/и -->
                        @if($apartmentId)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Водомери
                                </label>
                                <div class="mt-1 space-y-2">
                                    @forelse($this->availableWaterMeters as $meter)
                                        <div class="flex items-center">
                                            <input 
                                                type="checkbox" 
                                                wire:model.live="selectedWaterMeters" 
                                                id="meter-{{ $meter->id }}" 
                                                value="{{ $meter->id }}"
                                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded"
                                            >
                                            <label for="meter-{{ $meter->id }}" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                                {{ $meter->serial_number }} 
                                                <span class="ml-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $meter->type === 'hot' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' }}">
                                                    {{ $meter->type === 'hot' ? 'Топла вода' : 'Студена вода' }}
                                                </span>
                                            </label>
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-500 dark:text-gray-400 py-2">
                                            Няма активни водомери за този апартамент.
                                        </p>
                                    @endforelse
                                </div>
                            </div>
                        @endif
                        
                        <!-- Дата на отчитане -->
                        <div class="sm:col-span-2">
                            <label for="readingDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Дата на отчитане
                            </label>
                            <div class="mt-1">
                                <input type="date" wire:model="readingDate" id="readingDate" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                            </div>
                            @error('readingDate')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Бележки -->
                        <div class="sm:col-span-2">
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Бележки
                            </label>
                            <div class="mt-1">
                                <textarea wire:model="notes" id="notes" rows="2" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"></textarea>
                            </div>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Таблица с показания -->
                    @if(count($readings) > 0)
                        <div class="mt-6">
                            <h4 class="text-base font-medium text-gray-900 dark:text-white">Въвеждане на показания</h4>
                            <div class="mt-2 overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Водомер
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Тип
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Старо показание
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Ново показание
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Консумация
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($this->availableWaterMeters as $meter)
                                            @if(in_array($meter->id, $selectedWaterMeters))
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $meter->serial_number }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $meter->type === 'hot' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' }}">
                                                            {{ $meter->type === 'hot' ? 'Топла вода' : 'Студена вода' }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">
                                                        {{ number_format($readings[$meter->id]['previous_reading'], 3, '.', ' ') }} м³
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                        <div class="flex items-center justify-end">
                                                            <input
                                                                type="number"
                                                                step="0.001"
                                                                wire:model="readings.{{ $meter->id }}.current_reading"
                                                                wire:change="calculateConsumption({{ $meter->id }})"
                                                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-24 text-right sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"
                                                                placeholder="0.000"
                                                            >
                                                            <span class="ml-1 text-gray-500 dark:text-gray-400">м³</span>
                                                        </div>
                                                        @error("readings.{$meter->id}.current_reading")
                                                            <p class="mt-1 text-sm text-right text-red-600 dark:text-red-500">{{ $message }}</p>
                                                        @enderror
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">
                                                        @if(isset($readings[$meter->id]['consumption']) && is_numeric($readings[$meter->id]['consumption']))
                                                            {{ number_format($readings[$meter->id]['consumption'], 3, '.', ' ') }} м³
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Запази показания
                            </button>
                        </div>
                    @endif
                </form>
                
                @if($savedReadings > 0)
                    <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <div class="bg-green-50 dark:bg-green-900 p-4 rounded-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-green-800 dark:text-green-200">
                                        Успешно въведени показания
                                    </h3>
                                    <div class="mt-2 text-sm text-green-700 dark:text-green-300">
                                        <p>
                                            Успешно въведохте {{ $savedReadings }} нови показания. Можете да въведете показания за друг апартамент или да се върнете към <a href="{{ route('readings.index') }}" class="font-medium underline">всички показания</a>.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
