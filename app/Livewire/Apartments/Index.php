<?php

namespace App\Livewire\Apartments;

use App\Models\Apartment;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    
    public $search = '';
    public $status = '';
    public $sortField = 'number';
    public $sortDirection = 'asc';
    
    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'sortField' => ['except' => 'number'],
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
    
    public function updatingStatus()
    {
        $this->resetPage();
    }
    
    public function deleteApartment($apartmentId)
    {
        if (Gate::allows('manage')) {
            $apartment = Apartment::findOrFail($apartmentId);
            $apartment->delete();
            
            session()->flash('success', 'Апартаментът беше изтрит успешно.');
        } else {
            session()->flash('error', 'Нямате права за изтриване на апартаменти.');
        }
    }
    
    public function toggleStatus($apartmentId)
    {
        if (Gate::allows('manage')) {
            $apartment = Apartment::findOrFail($apartmentId);
            $apartment->status = $apartment->status === 'occupied' ? 'vacant' : 'occupied';
            $apartment->save();
            
            session()->flash('success', 'Статусът на апартамента беше променен успешно.');
        } else {
            session()->flash('error', 'Нямате права за промяна на статуса на апартаменти.');
        }
    }
    
    public function render()
    {
        // Проверка дали потребителят може да управлява апартаменти
        $canManage = Gate::allows('manage');
        
        $apartments = Apartment::query()
            ->when($this->search, function ($query) {
                $query->where('number', 'like', '%' . $this->search . '%')
                    ->orWhere('floor', 'like', '%' . $this->search . '%');
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->withCount('waterMeters')
            ->paginate(10);
            
        return view('livewire.apartments.index', [
            'apartments' => $apartments,
            'canManage' => $canManage,
        ]);
    }
}
