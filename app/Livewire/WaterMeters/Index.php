<?php

namespace App\Livewire\WaterMeters;

use App\Models\WaterMeter;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    
    public $search = '';
    public $type = '';
    public $active = '';
    public $apartment = '';
    public $sortField = 'serial_number';
    public $sortDirection = 'asc';
    
    protected $queryString = [
        'search' => ['except' => ''],
        'type' => ['except' => ''],
        'active' => ['except' => ''],
        'apartment' => ['except' => ''],
        'sortField' => ['except' => 'serial_number'],
        'sortDirection' => ['except' => 'asc'],
    ];
    
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
    
    public function updatingActive()
    {
        $this->resetPage();
    }
    
    public function updatingApartment()
    {
        $this->resetPage();
    }
    
    public function toggleActive($waterMeterId)
    {
        if (Gate::allows('manage')) {
            $waterMeter = WaterMeter::findOrFail($waterMeterId);
            $waterMeter->is_active = !$waterMeter->is_active;
            $waterMeter->save();
            
            session()->flash('success', 'Статусът на водомера беше променен успешно.');
        } else {
            session()->flash('error', 'Нямате права за промяна на статуса на водомери.');
        }
    }
    
    public function deleteWaterMeter($waterMeterId)
    {
        if (Gate::allows('manage')) {
            $waterMeter = WaterMeter::findOrFail($waterMeterId);
            
            // Проверяваме дали водомерът има показания
            if ($waterMeter->readings()->count() > 0) {
                session()->flash('error', 'Не можете да изтриете водомер, който има въведени показания.');
                return;
            }
            
            $waterMeter->delete();
            
            session()->flash('success', 'Водомерът беше изтрит успешно.');
        } else {
            session()->flash('error', 'Нямате права за изтриване на водомери.');
        }
    }
    
    public function render()
    {
        // Проверка дали потребителят може да управлява водомери
        $canManage = Gate::allows('manage');
        
        $query = WaterMeter::query()
            ->with('apartment')
            ->withCount('readings');
            
        // Филтриране по апартамент в зависимост от правата
        if (!$canManage) {
            // За обикновени потребители - само техните апартаменти
            $user = auth()->user();
            $apartmentIds = $user->apartments()->pluck('apartments.id');
            $query->whereIn('apartment_id', $apartmentIds);
        } else if ($this->apartment) {
            // Филтриране по конкретен апартамент
            $query->where('apartment_id', $this->apartment);
        }
        
        // Филтриране по тип, статус и търсене
        if ($this->type) {
            $query->where('type', $this->type);
        }
        
        if ($this->active !== '') {
            $query->where('is_active', $this->active === 'active');
        }
        
        if ($this->search) {
            $query->where('serial_number', 'like', '%' . $this->search . '%');
        }
        
        // Сортиране
        $query->orderBy($this->sortField, $this->sortDirection);
        
        // Пагинация
        $waterMeters = $query->paginate(10);
        
        // Вземане на апартаменти за филтъра
        $apartments = collect();
        if ($canManage) {
            $apartments = \App\Models\Apartment::orderBy('number')->get(['id', 'number']);
        }
        
        return view('livewire.water-meters.index', [
            'waterMeters' => $waterMeters,
            'canManage' => $canManage,
            'apartments' => $apartments,
        ]);
    }
}
