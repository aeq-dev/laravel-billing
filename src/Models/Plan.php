<?php

declare(strict_types=1);

namespace Bkfdev\Billing\Models;

use Bkfdev\Billing\Traits\HasFeatures;
use Bkfdev\Billing\Traits\HasGracePeriod;
use Bkfdev\Billing\Traits\HasPricing;
use Bkfdev\Billing\Traits\HasSubscriptionPeriod;
use Bkfdev\Billing\Traits\HasTrialPeriod;
use Bkfdev\Billing\Traits\MorphsSchedules;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Illuminate\Database\Eloquent\Builder;

/**
 * Class Plan
 * @package Bkfdev\Billing\Models
 */
class Plan extends Model
{
    use SoftDeletes, HasFeatures, HasPricing, HasTrialPeriod, HasSubscriptionPeriod, HasGracePeriod, MorphsSchedules;
    protected $guarded = [];

    protected $casts = [
        'tag' => 'string',
        'is_active' => 'boolean',
        'price' => 'float',
        'signup_fee' => 'float',
        'currency' => 'string',
        'trial_period' => 'integer',
        'trial_interval' => 'string',
        'trial_mode' => 'string',
        'grace_period' => 'integer',
        'grace_interval' => 'string',
        'invoice_period' => 'integer',
        'invoice_interval' => 'string',
        'tier' => 'integer',
        'deleted_at' => 'datetime',
    ];


    /**
     * Get plan by the given tag.
     *
     * @param string $tag
     * @return null|$this
     */
    static public function getByTag(string $tag): ?Plan
    {
        return static::where('tag', $tag)->first();
    }

    /**
     * The plan may have many combinations.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function combinations(): HasMany
    {
        return $this->hasMany(config('billing.models.plan_combination'), 'plan_id', 'id');
    }

    /**
     * The plan may have many features.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function features(): HasMany
    {
        return $this->hasMany(config('billing.models.plan_feature'), 'plan_id', 'id');
    }

    /**
     * The plan may have many subscriptions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(config('billing.models.plan_subscription'), 'plan_id', 'id');
    }

    /**
     * Activate the plan.
     *
     * @return $this
     */
    public function activate()
    {
        $this->update(['is_active' => true]);

        return $this;
    }

    /**
     * Deactivate the plan.
     *
     * @return $this
     */
    public function deactivate()
    {
        $this->update(['is_active' => false]);

        return $this;
    }

    public function scopeActive(Builder $builder, $bool = true)
    {
        return $builder->whereIsActive($bool);
    }
}
