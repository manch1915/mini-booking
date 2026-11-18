<?php

namespace Database\Factories;

use App\Models\Slot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Slot>
 */
class SlotFactory extends Factory
{
    public function definition(): array
    {
        $startsAt = now()->addDay()->startOfHour();

        return [
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->copy()->addHour(),
        ];
    }
}
