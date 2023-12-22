<?php

declare(strict_types=1);

namespace Bkfdev\Billing\Models;

use Bkfdev\Billing\Traits\BelongsToPlan;
use Bkfdev\Billing\Traits\MorphsSchedules;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Plan Combination
 * @package Bkfdev\Billing\Models
 */
class PlanCombination extends Model
{
    use BelongsToPlan, MorphsSchedules;
    protected $guarded = [];

    protected $casts = [
        'tag' => 'string',
        'country' => 'string',
        'currency' => 'string',
        'price' => 'float',
        'signup_fee' => 'float',
        'invoice_period' => 'integer',
        'invoice_interval' => 'string'
    ];

    /**
     * Get plan combination by the given tag.
     *
     * @param string $tag
     * @return null|$this
     */
    static public function getByTag(string $tag): ?PlanCombination
    {
        return static::where('tag', $tag)->first();
    }
}
