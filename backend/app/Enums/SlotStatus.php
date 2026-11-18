<?php

namespace App\Enums;

enum SlotStatus: string
{
    case Free = 'free';
    case Booked = 'booked';
    case Past = 'past';
}
