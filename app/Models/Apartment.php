<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Apartment extends Model
{
    use HasFactory;

    /**
     * Атрибути, които могат да бъдат масово присвоявани.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'number',
        'floor',
        'area',
        'rooms',
        'status',
    ];

    /**
     * Връзка с потребители, асоциирани с този апартамент.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('is_owner')
            ->withTimestamps();
    }

    /**
     * Връзка с водомери, асоциирани с този апартамент.
     */
    public function waterMeters(): HasMany
    {
        return $this->hasMany(WaterMeter::class);
    }

    /**
     * Връща собствениците на апартамента.
     */
    public function owners()
    {
        return $this->users()->wherePivot('is_owner', true);
    }

    /**
     * Връща наемателите на апартамента.
     */
    public function tenants()
    {
        return $this->users()->wherePivot('is_owner', false);
    }

    /**
     * Връща активни водомери за топла вода.
     */
    public function hotWaterMeters()
    {
        return $this->waterMeters()->where('type', 'hot')->where('is_active', true);
    }

    /**
     * Връща активни водомери за студена вода.
     */
    public function coldWaterMeters()
    {
        return $this->waterMeters()->where('type', 'cold')->where('is_active', true);
    }
}
