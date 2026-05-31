<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'user_id',
        'slot',
        'start_time',
        'end_time',
        'customer_name',
        'plate_number',
        'vehicle_type',
        'vehicle_model',
        'total_price',
        'total_duration_minutes',
        'status',
        'notes',
        'cancel_reason',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'total_price' => 'integer',
            'total_duration_minutes' => 'integer',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class)
            ->withPivot(['price_snapshot', 'duration_snapshot'])
            ->withTimestamps();
    }

    public function scopeConflicting(
        Builder $query,
        string $slot,
        mixed $startTime,
        mixed $endTime,
    ): Builder {
        return $query
            ->whereIn('status', ['pending', 'diproses'])
            ->where('slot', $slot)
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime);
    }
}
