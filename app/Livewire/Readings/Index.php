<?php

namespace App\Livewire\Readings;

use App\Models\Apartment;
use App\Models\Reading;
use App\Models\WaterMeter;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    
    public $search = '';
    public $type = '';
    public $apartment = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $meter = '';
    public $sortField = 'reading_date';
    public $sortDirection = 'desc';
    
    protected $queryString = [
        'search' => ['except' => ''],
        'type' => ['except' => ''],
        'apartment' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'meter' => ['except' => ''],
        'sortField' => ['except' => 'reading_date'],
        'sortDirection' => ['except' => 'desc'],
    ];
    
    public function mount($meter = null)
    {
        if ($meter) {
            $this->meter = $meter;
            
            // Получаване на апартамента за този водомер
            $waterMeter = WaterMeter::find($meter);
            if ($waterMeter) {
                $this->apartment = $waterMeter->apartment_id;
                $this->type = $waterMeter->type;
            }
        }
        
        // По подразбиране показваме последните 30 дни
        $this->dateFrom = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
    }
    
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }
    
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingType()
    {
        $this->resetPage();
    }
    
    public function updatingApartment()
    {
        $this->resetPage();
        // Нулираме филтъра за водомер при смяна на апартамента
        $this->meter = '';
    }
    
    public function updatingDateFrom()
    {
        $this->resetPage();
    }
    
    public function updatingDateTo()
    {
        $this->resetPage();
    }
    
    public function updatingMeter()
    {
        $this->resetPage();
    }
    
    public function deleteReading($readingId)
    {
        if (Gate::allows('manage')) {
            $reading = Reading::findOrFail($readingId);
            $reading->delete();
            
            session()->flash('success', 'Показанието беше изтрито успешно.');
        } else {
            session()->flash('error', 'Нямате права за изтриване на показания.');
        }
    }
    
    public function getApartmentsProperty()
    {
        $user = Auth::user();
        
        if ($user->isAdmin() || $user->isManager()) {
            return Apartment::orderBy('number')->get();
        } else {
            return $user->apartments()->orderBy('number')->get();
        }
    }
    
    public function getWaterMetersProperty()
    {
        if (!$this->apartment) {
            return collect();
        }
        
        $query = WaterMeter::where('apartment_id', $this->apartment);
        
        if ($this->type) {
            $query->where('type', $this->type);
        }
        
        return $query->orderBy('serial_number')->get();
    }
    
    public function render()
    {
        $user = Auth::user();
        $canManage = Gate::allows('manage');
        
        $query = Reading::with(['waterMeter', 'waterMeter.apartment', 'user']);
        
        // Филтриране според правата на потребителя
        if (!$canManage) {
            $apartmentIds = $user->apartments()->pluck('apartments.id');
            $waterMeterIds = WaterMeter::whereIn('apartment_id', $apartmentIds)->pluck('id');
            $query->whereIn('water_meter_id', $waterMeterIds);
        }
        
        // Прилагане на филтри
        if ($this->apartment) {
            if ($this->meter) {
                $query->where('water_meter_id', $this->meter);
            } else {
                $waterMeterIds = WaterMeter::where('apartment_id', $this->apartment)->pluck('id');
                $query->whereIn('water_meter_id', $waterMeterIds);
            }
        }
        
        if ($this->type && !$this->meter) {
            $query->whereHas('waterMeter', function ($q) {
                $q->where('type', $this->type);
            });
        }
        
        if ($this->dateFrom) {
            $query->whereDate('reading_date', '>=', $this->dateFrom);
        }
        
        if ($this->dateTo) {
            $query->whereDate('reading_date', '<=', $this->dateTo);
        }
        
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('waterMeter', function ($wm) {
                    $wm->where('serial_number', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('waterMeter.apartment', function ($apt) {
                    $apt->where('number', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('user', function ($u) {
                    $u->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhere('notes', 'like', '%' . $this->search . '%');
            });
        }
        
        // Сортиране
        if ($this->sortField === 'apartment') {
            $query->orderBy(
                Apartment::select('number')
                    ->whereColumn('apartments.id', 'water_meters.apartment_id')
                    ->limit(1),
                $this->sortDirection
            );
        } elseif ($this->sortField === 'water_meter') {
            $query->orderBy(
                WaterMeter::select('serial_number')
                    ->whereColumn('water_meters.id', 'readings.water_meter_id')
                    ->limit(1),
                $this->sortDirection
            );
        } elseif ($this->sortField === 'type') {
            $query->orderBy(
                WaterMeter::select('type')
                    ->whereColumn('water_meters.id', 'readings.water_meter_id')
                    ->limit(1),
                $this->sortDirection
            );
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }
        
        $readings = $query->paginate(15);
        
        // Статистики
        $totalConsumption = [
            'hot' => 0,
            'cold' => 0,
        ];
        
        if ($readings->count() > 0) {
            // Сума на консумация за горещата вода 
            $totalConsumption['hot'] = $readings->filter(function ($reading) {
                return $reading->waterMeter->type === 'hot';
            })->sum('consumption');
            
            // Сума на консумация за студената вода 
            $totalConsumption['cold'] = $readings->filter(function ($reading) {
                return $reading->waterMeter->type === 'cold';
            })->sum('consumption');
        }
        
        return view('livewire.readings.index', [
            'readings' => $readings, 
            'canManage' => $canManage,
            'totalConsumption' => $totalConsumption,
        ]);
    }
}
