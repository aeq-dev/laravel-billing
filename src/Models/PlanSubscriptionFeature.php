<?php

declare(strict_types=1);

namespace Bkfdev\Billing\Models;

use Bkfdev\Billing\Traits\HasResetDate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

/**
 * Class PlanSubscriptionFeature
 * @package Bkfdev\Billing\Models
 */
class PlanSubscriptionFeature extends Model
{
    use HasResetDate;

    protected $guarded = [];

    protected $casts = [
        'tag' => 'string',
        'value' => 'string',
        'resettable_period' => 'integer',
        'resettable_interval' => 'string',
        'sort_order' => 'integer',
    ];

    /**
     * The subscription feature belongs to one subscription.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subscription()
    {
        return $this->belongsTo(config('billing.models.plan_subscription'), 'plan_subscription_id', 'id');
    }

    /**
     * The subscription feature belongs to one plan feature.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function feature()
    {
        return $this->belongsTo(config('billing.models.plan_feature'), 'plan_feature_id', 'id');
    }

    /**
     * The subscription feature has one usage
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function usage()
    {
        return $this->hasOne(config('billing.models.plan_subscription_usage'), 'plan_subscription_feature_id', 'id');
    }

    /**
     * Show features that are not inherited by subscription's plan relation
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithoutPlan(Builder $query)
    {
        return $query->whereHas('feature', function (Builder $query) {
            $query->whereNull('plan_id');
        })
            ->orWhereNull('plan_feature_id');
    }

    /**
     * Sync feature with subscription related plan
     * @return $this
     */
    public function syncPlanSubscription()
    {
        $planFeature = $this->subscription->plan->getFeatureByTag($this->tag);
        $this->syncPlanFeature($planFeature);

        return $this;
    }

    /**
     * Sync feature with related plan feature
     * @param PlanFeature|null $planFeature
     * @return PlanSubscriptionFeature
     */
    public function syncPlanFeature(PlanFeature $planFeature = null): PlanSubscriptionFeature
    {
        if (!$planFeature && $this->plan_feature_id) {
            // If no Plan Feature specified, use plan in related feature (feature_id)
            $planFeature = $this->feature;
        } elseif (!$planFeature && !$this->plan_feature_id) {
            // There is no way to synchronize with a plan
            throw new InvalidArgumentException('Feature is not related to a plan.');
        }

        $this->plan_feature_id = $planFeature->id;
        $this->name = $planFeature->name;
        $this->description = $planFeature->description;
        $this->price = $planFeature->price;
        $this->value = $planFeature->value;
        $this->resettable_period = $planFeature->resettable_period;
        $this->resettable_interval = $planFeature->resettable_interval;
        $this->sort_order = $planFeature->sort_order;

        $this->save();

        return $this;
    }
}
