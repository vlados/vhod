<?php

namespace App\Livewire;

use App\Models\Apartment;
use App\Models\Reading;
use App\Models\WaterMeter;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public $totalApartments;
    public $totalWaterMeters;
    public $totalActiveWaterMeters;
    public $hotWaterMeters;
    public $coldWaterMeters;
    public $userApartments;
    public $latestReadings;
    public $monthlyConsumption;
    
    public function mount()
    {
        $user = Auth::user();
        
        // Зареждане на статистика според ролята на потребителя
        if ($user->isAdmin() || $user->isManager()) {
            // Общ брой апартаменти
            $this->totalApartments = Apartment::count();
            
            // Общ брой водомери
            $this->totalWaterMeters = WaterMeter::count();
            $this->totalActiveWaterMeters = WaterMeter::where('is_active', true)->count();
            
            // Брой водомери по тип
            $this->hotWaterMeters = WaterMeter::where('type', 'hot')->where('is_active', true)->count();
            $this->coldWaterMeters = WaterMeter::where('type', 'cold')->where('is_active', true)->count();
            
            // Последни 10 показания
            $this->latestReadings = Reading::with(['waterMeter', 'user', 'waterMeter.apartment'])
                ->latest('reading_date')
                ->take(10)
                ->get();
                
            // Месечна консумация за последните 6 месеца
            $this->monthlyConsumption = $this->getMonthlyConsumption();
        } else {
            // За собственици и наематели - информация само за техните апартаменти
            $this->userApartments = $user->apartments()->with(['waterMeters' => function ($query) {
                $query->where('is_active', true);
            }])->get();
            
            // Последни 10 показания за водомерите на потребителя
            $apartmentIds = $user->apartments()->pluck('apartments.id');
            $waterMeterIds = WaterMeter::whereIn('apartment_id', $apartmentIds)->pluck('id');
            
            $this->latestReadings = Reading::with(['waterMeter', 'user', 'waterMeter.apartment'])
                ->whereIn('water_meter_id', $waterMeterIds)
                ->latest('reading_date')
                ->take(10)
                ->get();
        }
    }
    
    private function getMonthlyConsumption()
    {
        $data = [];
        
        // Последните 6 месеца
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->format('F Y');
            
            $hotConsumption = Reading::whereHas('waterMeter', function ($query) {
                    $query->where('type', 'hot');
                })
                ->whereYear('reading_date', $date->year)
                ->whereMonth('reading_date', $date->month)
                ->sum('consumption');
                
            $coldConsumption = Reading::whereHas('waterMeter', function ($query) {
                    $query->where('type', 'cold');
                })
                ->whereYear('reading_date', $date->year)
                ->whereMonth('reading_date', $date->month)
                ->sum('consumption');
                
            $data[] = [
                'month' => $month,
                'hot' => $hotConsumption,
                'cold' => $coldConsumption,
            ];
        }
        
        return $data;
    }
    
    public function render()
    {
        return view('livewire.dashboard');
    }
}
