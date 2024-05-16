<?php

namespace App\Enums;

enum SubscriptionStatus: int
{
    use EnumTrait;

    case Running   = 1;
    case Requested = 2;
    case Expired   = 3;
    case Inactive  = 4;
    case Renewed   = 5;

}