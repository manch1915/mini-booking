<?php

namespace Tests\Feature;

use App\Models\Slot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_free_future_slot_can_be_booked(): void
    {
        $slot = Slot::factory()->create();

        $response = $this->book($slot);

        $response->assertCreated()->assertJsonStructure(['id']);
        $this->assertDatabaseHas('bookings', [
            'id' => $response->json('id'),
            'slot_id' => $slot->id,
            'cancelled_at' => null,
        ]);
    }

    public function test_booked_slot_rejects_second_booking(): void
    {
        $slot = Slot::factory()->create();
        $this->book($slot)->assertCreated();

        $response = $this->book($slot);

        $response->assertConflict()->assertJsonStructure(['message']);
        $this->assertSame(1, $slot->bookings()->active()->count());
    }

    private function book(Slot $slot): TestResponse
    {
        return $this->postJson('/api/bookings', [
            'slot_id' => $slot->id,
            'customer_name' => 'Анна Петрова',
            'customer_email' => 'anna@example.com',
        ]);
    }
}
