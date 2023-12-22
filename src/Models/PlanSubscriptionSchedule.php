<?php

namespace Bkfdev\Billing\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PlanSubscriptionSchedule
 * @package Bkfdev\Billing\Models
 *
 * @property integer $id
 * @property integer $subscription_id
 * @property integer $plan_id;
 * @property \Carbon\Carbon|null $scheduled_at
 * @property \Carbon\Carbon|null $failed_at
 * @property \Carbon\Carbon|null $succeeded_at
 */
class PlanSubscriptionSchedule extends Model
{
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'scheduleable_type' => 'string',
        'scheduled_at' => 'datetime',
        'failed_at' => 'datetime',
        'succeeded_at' => 'datetime'
    ];

    /**
     * Subscription Schedule belongs to Subscription
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subscription()
    {
        return $this->belongsTo(config('billing.models.plan_subscription'), 'subscription_id', 'id');
    }

    /**
     * Get the parent scheduleable model (plan or plan combination).
     */
    public function scheduleable()
    {
        return $this->morphTo();
    }

    /**
     * Pending subscription changes
     *
     * @param $query
     * @param Carbon|null $date
     * @return mixed
     */
    public function scopePending($query, ?Carbon $date = null)
    {
        if (!$date) {
            $date = Carbon::now();
        }

        return $query->where('scheduled_at', '<=', $date)->unprocessed();
    }

    /**
     * Not processed schedules
     * @param $query
     *
     * @return mixed
     */
    public function scopeUnprocessed($query)
    {
        return $query->whereNull('succeeded_at')->whereNull('failed_at');
    }

    /**
     * Change Subscription plan
     * @param bool $clearUsage Clear subscription usage
     * @param bool $syncInvoicing Synchronize billing frequency or leave it unchanged
     * @return PlanSubscriptionSchedule
     */
    public function changeSubscriptionPlan(bool $clearUsage = true, bool $syncInvoicing = true)
    {
        $this->subscription->changePlan($this->scheduleable, $clearUsage, $syncInvoicing);
        $this->failed_at = null;
        $this->succeeded_at = Carbon::now();
        $this->save();

        return $this;
    }

    /**
     * Flag the schedule as failed
     * @return PlanSubscriptionSchedule
     */
    public function fail()
    {
        $this->failed_at = Carbon::now();
        $this->succeeded_at = null;
        $this->save();

        return $this;
    }
}
