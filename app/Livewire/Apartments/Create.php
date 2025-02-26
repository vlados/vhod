<?php

namespace App\Livewire\Apartments;

use App\Models\Apartment;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Create extends Component
{
    public $number;
    public $floor;
    public $area;
    public $rooms;
    public $status = 'occupied';
    
    protected $rules = [
        'number' => 'required|string|max:10|unique:apartments,number',
        'floor' => 'required|integer',
        'area' => 'required|numeric|min:1',
        'rooms' => 'required|integer|min:1',
        'status' => 'required|in:occupied,vacant',
    ];
    
    protected $validationAttributes = [
        'number' => 'номер',
        'floor' => 'етаж',
        'area' => 'площ',
        'rooms' => 'брой стаи',
        'status' => 'статус',
    ];
    
    public function mount()
    {
        // Проверка дали потребителят може да създава апартаменти
        if (!Gate::allows('manage')) {
            return redirect()->route('apartments.index')
                ->with('error', 'Нямате права за създаване на апартаменти.');
        }
    }
    
    public function saveApartment()
    {
        if (!Gate::allows('manage')) {
            session()->flash('error', 'Нямате права за създаване на апартаменти.');
            return;
        }
        
        $this->validate();
        
        Apartment::create([
            'number' => $this->number,
            'floor' => $this->floor,
            'area' => $this->area,
            'rooms' => $this->rooms,
            'status' => $this->status,
        ]);
        
        session()->flash('success', 'Апартаментът беше създаден успешно!');
        
        return redirect()->route('apartments.index');
    }
    
    public function render()
    {
        return view('livewire.apartments.create');
    }
}
