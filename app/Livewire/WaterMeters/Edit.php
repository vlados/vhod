<?php

namespace App\Livewire\WaterMeters;

use App\Models\Apartment;
use App\Models\WaterMeter;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Edit extends Component
{
    public WaterMeter $waterMeter;
    
    public $serial_number;
    public $type;
    public $apartment_id;
    public $installation_date;
    public $initial_reading;
    public $is_active;
    
    protected function rules()
    {
        return [
            'serial_number' => 'required|string|max:50|unique:water_meters,serial_number,' . $this->waterMeter->id,
            'type' => 'required|in:hot,cold',
            'apartment_id' => 'required|exists:apartments,id',
            'installation_date' => 'required|date|before_or_equal:today',
            'initial_reading' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ];
    }
    
    protected $validationAttributes = [
        'serial_number' => 'сериен номер',
        'type' => 'тип',
        'apartment_id' => 'апартамент',
        'installation_date' => 'дата на инсталация',
        'initial_reading' => 'начално показание',
        'is_active' => 'статус',
    ];
    
    public function mount(WaterMeter $waterMeter)
    {
        // Проверка дали потребителят може да редактира водомери
        if (!Gate::allows('manage')) {
            return redirect()->route('water-meters.index')
                ->with('error', 'Нямате права за редактиране на водомери.');
        }
        
        $this->waterMeter = $waterMeter;
        $this->serial_number = $waterMeter->serial_number;
        $this->type = $waterMeter->type;
        $this->apartment_id = $waterMeter->apartment_id;
        $this->installation_date = $waterMeter->installation_date->format('Y-m-d');
        $this->initial_reading = $waterMeter->initial_reading;
        $this->is_active = $waterMeter->is_active;
    }
    
    public function updateWaterMeter()
    {
        if (!Gate::allows('manage')) {
            session()->flash('error', 'Нямате права за редактиране на водомери.');
            return;
        }
        
        $this->validate();
        
        // Проверка за промяна на началното показание, когато вече има въведени показания
        if ($this->waterMeter->readings()->count() > 0 && $this->waterMeter->initial_reading != $this->initial_reading) {
            session()->flash('error', 'Не можете да променяте началното показание на водомер, който вече има въведени показания.');
            return;
        }
        
        $this->waterMeter->update([
            'serial_number' => $this->serial_number,
            'type' => $this->type,
            'apartment_id' => $this->apartment_id,
            'installation_date' => $this->installation_date,
            'initial_reading' => $this->initial_reading,
            'is_active' => $this->is_active,
        ]);
        
        session()->flash('success', 'Водомерът беше актуализиран успешно!');
        
        return redirect()->route('water-meters.index');
    }
    
    public function render()
    {
        $apartments = Apartment::orderBy('number')->get();
        $readingsCount = $this->waterMeter->readings()->count();
        
        return view('livewire.water-meters.edit', [
            'apartments' => $apartments,
            'readingsCount' => $readingsCount,
        ]);
    }
}
