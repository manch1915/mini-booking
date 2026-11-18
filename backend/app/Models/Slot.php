<?php

namespace App\Models;

use App\Enums\SlotStatus;
use Database\Factories\SlotFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Slot extends Model
{
    /** @use HasFactory<SlotFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['starts_at', 'ends_at'];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function activeBooking(): HasOne
    {
        return $this->hasOne(Booking::class)->active();
    }

    public function isPast(): bool
    {
        return $this->starts_at->lessThanOrEqualTo(now());
    }

    public function status(): SlotStatus
    {
        return match (true) {
            $this->isPast() => SlotStatus::Past,
            $this->activeBooking !== null => SlotStatus::Booked,
            default => SlotStatus::Free,
        };
    }
}
