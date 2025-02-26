<?php

namespace App\Livewire\Readings;

use App\Models\Apartment;
use App\Models\Reading;
use App\Models\WaterMeter;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Create extends Component
{
    public $apartmentId;
    public $selectedWaterMeters = [];
    public $readings = [];
    public $readingDate;
    public $notes;
    public $savedReadings = 0;
    
    public function mount($meter = null)
    {
        // Задаване на днешната дата като дата на отчитане по подразбиране
        $this->readingDate = Carbon::now()->format('Y-m-d');
        
        // Ако е подаден конкретен водомер, предварително го избираме
        if ($meter) {
            $waterMeter = WaterMeter::find($meter);
            if ($waterMeter && $waterMeter->is_active) {
                // Проверка дали потребителят има достъп до този водомер
                $user = Auth::user();
                $hasAccess = false;
                
                if ($user->isAdmin() || $user->isManager()) {
                    $hasAccess = true;
                } else {
                    $userApartmentIds = $user->apartments()->pluck('apartments.id')->toArray();
                    $hasAccess = in_array($waterMeter->apartment_id, $userApartmentIds);
                }
                
                if ($hasAccess) {
                    $this->apartmentId = $waterMeter->apartment_id;
                    $this->selectedWaterMeters = [$waterMeter->id];
                    $this->updateReadingsForm();
                }
            }
        }
    }
    
    public function updatedApartmentId()
    {
        $this->selectedWaterMeters = [];
        $this->readings = [];
    }
    
    public function updatedSelectedWaterMeters()
    {
        $this->updateReadingsForm();
    }
    
    public function updateReadingsForm()
    {
        if (empty($this->selectedWaterMeters)) {
            $this->readings = [];
            return;
        }
        
        $waterMeters = WaterMeter::whereIn('id', $this->selectedWaterMeters)
                                ->where('is_active', true)
                                ->get();
                                
        foreach ($waterMeters as $waterMeter) {
            // Проверка дали вече имаме запис за този водомер
            if (!isset($this->readings[$waterMeter->id])) {
                $lastReading = $waterMeter->latestReading();
                $previousReading = $lastReading ? $lastReading->current_reading : $waterMeter->initial_reading;
                
                $this->readings[$waterMeter->id] = [
                    'water_meter_id' => $waterMeter->id,
                    'previous_reading' => $previousReading,
                    'current_reading' => null,
                    'consumption' => null,
                ];
            }
        }
        
        // Премахваме записи за водомери, които вече не са избрани
        foreach ($this->readings as $id => $reading) {
            if (!in_array($id, $this->selectedWaterMeters)) {
                unset($this->readings[$id]);
            }
        }
    }
    
    public function calculateConsumption($waterMeterId)
    {
        if (isset($this->readings[$waterMeterId]) && 
            is_numeric($this->readings[$waterMeterId]['current_reading']) && 
            is_numeric($this->readings[$waterMeterId]['previous_reading'])) {
            
            $current = floatval($this->readings[$waterMeterId]['current_reading']);
            $previous = floatval($this->readings[$waterMeterId]['previous_reading']);
            
            if ($current >= $previous) {
                $this->readings[$waterMeterId]['consumption'] = $current - $previous;
            } else {
                $this->readings[$waterMeterId]['consumption'] = null;
            }
        } else {
            $this->readings[$waterMeterId]['consumption'] = null;
        }
    }
    
    public function saveReadings()
    {
        $this->validate([
            'readingDate' => 'required|date|before_or_equal:today',
            'readings.*.current_reading' => 'required|numeric|min:0',
            'readings.*.previous_reading' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:255',
        ], [], [
            'readings.*.current_reading' => 'текущо показание',
            'readings.*.previous_reading' => 'предишно показание',
            'readingDate' => 'дата на отчитане',
            'notes' => 'бележки',
        ]);
        
        // Допълнителна валидация - текущото показание трябва да е по-голямо от предишното
        foreach ($this->readings as $id => $reading) {
            if ($reading['current_reading'] < $reading['previous_reading']) {
                $this->addError("readings.{$id}.current_reading", 'Текущото показание трябва да е по-голямо или равно на предишното показание.');
                return;
            }
        }
        
        // Записваме показанията
        $savedCount = 0;
        
        foreach ($this->readings as $id => $reading) {
            Reading::create([
                'water_meter_id' => $reading['water_meter_id'],
                'previous_reading' => $reading['previous_reading'],
                'current_reading' => $reading['current_reading'],
                'consumption' => $reading['current_reading'] - $reading['previous_reading'],
                'reading_date' => $this->readingDate,
                'user_id' => Auth::id(),
                'notes' => $this->notes,
            ]);
            
            $savedCount++;
        }
        
        $this->savedReadings = $savedCount;
        
        // Нулиране на формата
        $this->selectedWaterMeters = [];
        $this->readings = [];
        $this->notes = null;
        
        session()->flash('success', "Успешно записахте {$savedCount} показания!");
    }
    
    public function getAvailableApartmentsProperty()
    {
        $user = Auth::user();
        
        if ($user->isAdmin() || $user->isManager()) {
            return Apartment::orderBy('number')->get();
        } else {
            return $user->apartments()->orderBy('number')->get();
        }
    }
    
    public function getAvailableWaterMetersProperty()
    {
        if (!$this->apartmentId) {
            return collect();
        }
        
        return WaterMeter::where('apartment_id', $this->apartmentId)
                        ->where('is_active', true)
                        ->get();
    }
    
    public function render()
    {
        return view('livewire.readings.create');
    }
}
