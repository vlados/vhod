<?php

namespace App\Livewire\Apartments;

use App\Models\Apartment;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Edit extends Component
{
    public Apartment $apartment;
    
    public $number;
    public $floor;
    public $area;
    public $rooms;
    public $status;
    
    protected function rules()
    {
        return [
            'number' => 'required|string|max:10|unique:apartments,number,' . $this->apartment->id,
            'floor' => 'required|integer',
            'area' => 'required|numeric|min:1',
            'rooms' => 'required|integer|min:1',
            'status' => 'required|in:occupied,vacant',
        ];
    }
    
    protected $validationAttributes = [
        'number' => 'номер',
        'floor' => 'етаж',
        'area' => 'площ',
        'rooms' => 'брой стаи',
        'status' => 'статус',
    ];
    
    public function mount(Apartment $apartment)
    {
        // Проверка дали потребителят може да редактира апартаменти
        if (!Gate::allows('manage')) {
            return redirect()->route('apartments.index')
                ->with('error', 'Нямате права за редактиране на апартаменти.');
        }
        
        $this->apartment = $apartment;
        $this->number = $apartment->number;
        $this->floor = $apartment->floor;
        $this->area = $apartment->area;
        $this->rooms = $apartment->rooms;
        $this->status = $apartment->status;
    }
    
    public function updateApartment()
    {
        if (!Gate::allows('manage')) {
            session()->flash('error', 'Нямате права за редактиране на апартаменти.');
            return;
        }
        
        $this->validate();
        
        $this->apartment->update([
            'number' => $this->number,
            'floor' => $this->floor,
            'area' => $this->area,
            'rooms' => $this->rooms,
            'status' => $this->status,
        ]);
        
        session()->flash('success', 'Апартаментът беше актуализиран успешно!');
        
        return redirect()->route('apartments.index');
    }
    
    public function render()
    {
        return view('livewire.apartments.edit');
    }
}
