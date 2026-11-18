<?php

namespace App\Http\Controllers;

use App\Actions\BookSlot;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Slot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class BookingController extends Controller
{
    public function store(StoreBookingRequest $request, BookSlot $bookSlot): JsonResponse
    {
        $slot = Slot::findOrFail($request->integer('slot_id'));
        $customer = $request->safe()->only(['customer_name', 'customer_email']);

        $booking = $bookSlot->handle($slot, $customer);

        return response()->json(['id' => $booking->id], Response::HTTP_CREATED);
    }

    public function destroy(Booking $booking): Response
    {
        $booking->cancel();

        return response()->noContent();
    }
}
