<?php

declare(strict_types=1);

namespace Bkfdev\Billing\Models;

use Bkfdev\Billing\Traits\BelongsToPlan;
use Bkfdev\Billing\Traits\HasResetDate;
use Illuminate\Database\Eloquent\Model;

class PlanFeature extends Model
{
    use BelongsToPlan, HasResetDate;
    protected $guarded = [];

    protected $casts = [
        'tag' => 'string',
        'value' => 'string',
        'resettable_period' => 'integer',
        'resettable_interval' => 'string',
        'sort_order' => 'integer',
    ];
}
