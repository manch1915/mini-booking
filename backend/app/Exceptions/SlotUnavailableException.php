<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class SlotUnavailableException extends RuntimeException
{
    public static function past(): self
    {
        return new self('Этот слот уже прошёл, выберите другое время.');
    }

    public static function alreadyBooked(): self
    {
        return new self('Этот слот уже занят, выберите другое время.');
    }

    public function render(): JsonResponse
    {
        return response()->json(['message' => $this->getMessage()], Response::HTTP_CONFLICT);
    }
}
