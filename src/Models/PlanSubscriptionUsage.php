<?php

declare(strict_types=1);

namespace Bkfdev\Billing\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanSubscriptionUsage extends Model
{
    protected $guarded = [];

    /**
     * Subscription usage always belongs to a plan subscription feature.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function feature(): BelongsTo
    {
        return $this->belongsTo(config('billing.models.plan_subscription_feature'), 'plan_subscription_feature_id', 'id', 'feature');
    }

    /**
     * Scope subscription usage by feature tag.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string $featureTag
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByFeatureTag(Builder $builder, string $featureTag): Builder
    {
        return $builder->whereHas('feature', function (Builder $query) use ($featureTag) {
            $query->where('tag', $featureTag);
        });
    }

    /**
     * Check whether usage has been expired or not.
     *
     * @return bool
     */
    public function hasExpired(): bool
    {
        if (is_null($this->valid_until)) {
            return false;
        }

        return Carbon::now()->gte($this->valid_until);
    }
}
