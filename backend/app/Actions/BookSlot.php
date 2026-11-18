<?php

namespace App\Actions;

use App\Exceptions\SlotUnavailableException;
use App\Models\Booking;
use App\Models\Slot;
use Illuminate\Database\UniqueConstraintViolationException;

class BookSlot
{
    /**
     * @param  array{customer_name: string, customer_email: string}  $customer
     *
     * @throws SlotUnavailableException
     */
    public function handle(Slot $slot, array $customer): Booking
    {
        if ($slot->isPast()) {
            throw SlotUnavailableException::past();
        }

        try {
            return $slot->bookings()->create($customer);
        } catch (UniqueConstraintViolationException) {
            // Вторую активную бронь на слот не пропустил уникальный индекс.
            throw SlotUnavailableException::alreadyBooked();
        }
    }
}
