<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\Dashboard;
use App\Livewire\Apartments;
use App\Livewire\WaterMeters;
use App\Livewire\Readings;
use App\Livewire\Users;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Маршрути, защитени с аутентикация
Route::middleware(['auth'])->group(function () {
    // Табло за управление
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    
    // Апартаменти
    Route::get('/apartments', Apartments\Index::class)->name('apartments.index');
    Route::get('/apartments/create', Apartments\Create::class)->name('apartments.create');
    Route::get('/apartments/{apartment}/edit', Apartments\Edit::class)->name('apartments.edit');
    
    // Водомери
    Route::get('/water-meters', WaterMeters\Index::class)->name('water-meters.index');
    Route::get('/water-meters/create', WaterMeters\Create::class)->name('water-meters.create');
    Route::get('/water-meters/{waterMeter}/edit', WaterMeters\Edit::class)->name('water-meters.edit');
    
    // Показания
    Route::get('/readings', Readings\Index::class)->name('readings.index');
    Route::get('/readings/create', Readings\Create::class)->name('readings.create');
    
    // Потребители - достъпни само за администратори и домоуправители
    Route::middleware(['can:manage-users'])->group(function () {
        Route::get('/users', Users\Index::class)->name('users.index');
        Route::get('/users/create', Users\Create::class)->name('users.create');
        Route::get('/users/{user}/edit', Users\Edit::class)->name('users.edit');
    });
    
    // Настройки на профила
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
