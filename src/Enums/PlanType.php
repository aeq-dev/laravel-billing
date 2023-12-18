<?php

namespace Bkfdev\Billing\Enums;

enum PlanType: string
{
    case FREE = 'free';
    case FIXED = 'fixed'; // Pay fixed price
    case PAY_AS_YOU_GO = 'pay as you go'; // Pay what you buy
    case PAY_AS_YOU_USE = 'pay as you use'; // Pay what you use
}
