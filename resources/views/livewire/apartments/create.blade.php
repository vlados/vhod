<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-water-management.layout heading="Нов апартамент" subheading="Добавяне на нов апартамент в системата">
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
            
            <form wire:submit="saveApartment" class="my-6 space-y-6">
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
                
                <div class="flex items-center justify-end gap-4 pt-4">
                    <flux:button href="{{ route('apartments.index') }}" variant="secondary" wire:navigate>Отказ</flux:button>
                    <flux:button variant="primary" type="submit">Създай апартамент</flux:button>
                </div>
            </form>
        </x-water-management.layout>
    </div>
</div>
