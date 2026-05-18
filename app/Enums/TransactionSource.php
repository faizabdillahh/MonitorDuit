<?php

namespace App\Enums;

enum TransactionSource: string
{
    case AI = 'ai';
    case Manual = 'manual';
}
