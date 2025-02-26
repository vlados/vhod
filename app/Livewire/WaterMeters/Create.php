<?php

namespace App\Livewire\WaterMeters;

use App\Models\Apartment;
use App\Models\WaterMeter;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Create extends Component
{
    public $serial_number;
    public $type = 'cold';
    public $apartment_id;
    public $installation_date;
    public $initial_reading;
    public $is_active = true;
    
    protected $rules = [
        'serial_number' => 'required|string|max:50|unique:water_meters,serial_number',
        'type' => 'required|in:hot,cold',
        'apartment_id' => 'required|exists:apartments,id',
        'installation_date' => 'required|date|before_or_equal:today',
        'initial_reading' => 'required|numeric|min:0',
        'is_active' => 'boolean',
    ];
    
    protected $validationAttributes = [
        'serial_number' => 'сериен номер',
        'type' => 'тип',
        'apartment_id' => 'апартамент',
        'installation_date' => 'дата на инсталация',
        'initial_reading' => 'начално показание',
        'is_active' => 'статус',
    ];
    
    public function mount()
    {
        // Проверка дали потребителят може да създава водомери
        if (!Gate::allows('manage')) {
            return redirect()->route('water-meters.index')
                ->with('error', 'Нямате права за създаване на водомери.');
        }
        
        // Задаване на днешната дата като дата на инсталация по подразбиране
        $this->installation_date = Carbon::now()->format('Y-m-d');
    }
    
    public function saveWaterMeter()
    {
        if (!Gate::allows('manage')) {
            session()->flash('error', 'Нямате права за създаване на водомери.');
            return;
        }
        
        $this->validate();
        
        WaterMeter::create([
            'serial_number' => $this->serial_number,
            'type' => $this->type,
            'apartment_id' => $this->apartment_id,
            'installation_date' => $this->installation_date,
            'initial_reading' => $this->initial_reading,
            'is_active' => $this->is_active,
        ]);
        
        session()->flash('success', 'Водомерът беше създаден успешно!');
        
        return redirect()->route('water-meters.index');
    }
    
    public function render()
    {
        $apartments = Apartment::orderBy('number')->get();
        
        return view('livewire.water-meters.create', [
            'apartments' => $apartments,
        ]);
    }
}
