<?php

namespace Bkfdev\Billing\Traits;

trait MorphsSchedules
{
    /**
     * Get all schedules.
     */
    public function schedules()
    {
        return $this->morphMany(config('billing.models.plan_subscription_schedule'), 'scheduleable');
    }
}
