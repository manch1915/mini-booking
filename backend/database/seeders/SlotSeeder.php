<?php

namespace Database\Seeders;

use App\Models\Slot;
use Illuminate\Database\Seeder;

class SlotSeeder extends Seeder
{
    private const DAYS_AHEAD = 7;

    private const OPENS_AT = 9;

    private const CLOSES_AT = 18;

    public function run(): void
    {
        for ($day = 0; $day < self::DAYS_AHEAD; $day++) {
            $date = today()->addDays($day);

            for ($hour = self::OPENS_AT; $hour < self::CLOSES_AT; $hour++) {
                $startsAt = $date->copy()->setTime($hour, 0);

                Slot::firstOrCreate(
                    ['starts_at' => $startsAt],
                    ['ends_at' => $startsAt->copy()->addHour()],
                );
            }
        }
    }
}
