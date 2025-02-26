<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reading extends Model
{
    use HasFactory;

    /**
     * Атрибути, които могат да бъдат масово присвоявани.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'water_meter_id',
        'previous_reading',
        'current_reading',
        'consumption',
        'reading_date',
        'user_id',
        'notes',
    ];

    /**
     * Атрибути, които трябва да бъдат трансформирани.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'reading_date' => 'date',
    ];

    /**
     * Връзка с водомера, за който е показанието.
     */
    public function waterMeter(): BelongsTo
    {
        return $this->belongsTo(WaterMeter::class);
    }

    /**
     * Връзка с потребителя, въвел показанието.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Изчислява консумацията преди записване на модела.
     */
    protected static function booted()
    {
        static::creating(function ($reading) {
            if (!isset($reading->consumption)) {
                $reading->consumption = $reading->current_reading - $reading->previous_reading;
            }
        });
    }
}
