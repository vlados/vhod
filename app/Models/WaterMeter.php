<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WaterMeter extends Model
{
    use HasFactory;

    /**
     * Атрибути, които могат да бъдат масово присвоявани.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'serial_number',
        'type',
        'apartment_id',
        'installation_date',
        'initial_reading',
        'is_active',
    ];

    /**
     * Атрибути, които трябва да бъдат трансформирани.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'installation_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Връзка с апартамента, към който принадлежи водомерът.
     */
    public function apartment(): BelongsTo
    {
        return $this->belongsTo(Apartment::class);
    }

    /**
     * Връзка с показанията на водомера.
     */
    public function readings(): HasMany
    {
        return $this->hasMany(Reading::class);
    }

    /**
     * Връща последното показание на водомера.
     */
    public function latestReading()
    {
        return $this->readings()->latest('reading_date')->first();
    }

    /**
     * Връща стойността на последното показание (или началното, ако няма показания).
     */
    public function latestReadingValue()
    {
        $lastReading = $this->latestReading();
        return $lastReading ? $lastReading->current_reading : $this->initial_reading;
    }

    /**
     * Връща общата консумация на водомера.
     */
    public function totalConsumption()
    {
        return $this->readings()->sum('consumption');
    }
}
