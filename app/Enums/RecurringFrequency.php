<?php

namespace App\Enums;

enum RecurringFrequency: string
{
    case Weekly    = 'weekly';
    case Biweekly  = 'biweekly';
    case Monthly   = 'monthly';
    case Yearly    = 'yearly';

    public function nextDate(\Carbon\Carbon $from): \Carbon\Carbon
    {
        return match($this) {
            self::Weekly   => $from->addWeek(),
            self::Biweekly => $from->addWeeks(2),
            self::Monthly  => $from->addMonth(),
            self::Yearly   => $from->addYear(),
        };
    }

    public function label(): string
    {
        return match($this) {
            self::Weekly   => 'Setiap minggu',
            self::Biweekly => 'Setiap 2 minggu',
            self::Monthly  => 'Setiap bulan',
            self::Yearly   => 'Setiap tahun',
        };
    }
}
