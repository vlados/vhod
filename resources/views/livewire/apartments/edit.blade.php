<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-water-management.layout heading="Редактиране на апартамент" subheading="Актуализиране на информацията за апартамент">
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
            
            <form wire:submit="updateApartment" class="my-6 space-y-6">
                <flux:field class="md:max-w-lg">
                    <flux:input wire:model="number" label="Номер на апартамент" type="text" name="number" required autofocus placeholder="Номер на апартамент" />
                </flux:field>
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <flux:field>
                        <flux:input wire:model="floor" label="Етаж" type="number" name="floor" required placeholder="Етаж" />
                    </flux:field>
                    
                    <flux:field>
                        <flux:input wire:model="rooms" label="Брой стаи" type="number" name="rooms" required placeholder="Брой стаи" />
                    </flux:field>
                </div>
                
                <flux:field class="md:max-w-lg">
                    <flux:input wire:model="area" label="Площ (м²)" type="number" step="0.01" name="area" required placeholder="Площ в квадратни метри" />
                </flux:field>
                
                <flux:field class="md:max-w-lg">
                    <flux:label for="status">Статус</flux:label>
                    <flux:select wire:model="status" name="status">
                        <option value="occupied">Обитаем</option>
                        <option value="vacant">Необитаем</option>
                    </flux:select>
                </flux:field>
                
                <!-- Информация за водомери -->
                @if($apartment->waterMeters->count() > 0)
                    <flux:fieldset>
                        <flux:legend>Водомери към апартамента</flux:legend>
                        <flux:description>
                            Този апартамент има {{ $apartment->waterMeters->count() }} водомера. Можете да управлявате водомерите от раздел "Водомери".
                        </flux:description>
                        
                        <div class="mt-3 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Сериен номер
                                        </th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Тип
                                        </th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Дата на инсталация
                                        </th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Статус
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($apartment->waterMeters as $meter)
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $meter->serial_number }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $meter->type === 'hot' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' }}">
                                                {{ $meter->type === 'hot' ? 'Топла вода' : 'Студена вода' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $meter->installation_date->format('d.m.Y') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $meter->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                                                {{ $meter->is_active ? 'Активен' : 'Неактивен' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </flux:fieldset>
                @endif
                
                <div class="flex items-center justify-end gap-4 pt-4">
                    <flux:button href="{{ route('apartments.index') }}" variant="secondary" wire:navigate>Отказ</flux:button>
                    <flux:button variant="primary" type="submit">Запази промените</flux:button>
                </div>
            </form>
        </x-water-management.layout>
    </div>
</div>
