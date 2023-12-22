<?php

declare(strict_types=1);

namespace Bkfdev\Billing\Services;

use Bkfdev\Billing\Models\PlanSubscription;
use Bkfdev\Billing\Models\PlanSubscriptionSchedule;
use Carbon\Carbon;

class PendingInvoiceCollector
{
    private $processUntilDate;

    public function __construct(?Carbon $date = null)
    {
        $this->processUntilDate = $date ?? now();
    }

    /**
     * Set date to do the collection
     * @param Carbon $date
     * @return $this
     */
    public function onDate(Carbon $date)
    {
        $this->processUntilDate = $date;

        return $this;
    }

    /**
     * Collect regular renewal invoices into array
     * @return mixed
     */
    public function collectInvoices()
    {
        return $this->getInvoices()->sortBy('date', SORT_ASC)->all();
    }

    /**
     * Collect scheduled invoices
     * @return mixed
     */
    public function collectScheduledInvoices()
    {
        return $this->getScheduledInvoices()->sortBy('date', SORT_ASC)->all();
    }

    /**
     * Collect all pending invoices
     * This function collects regular renewals and scheduled, it prioritizes scheduled over regular renewals
     * @return mixed
     */
    public function collectAllInvoices()
    {
        $regular = $this->getInvoices();
        $scheduled = $this->getScheduledInvoices();

        // Get only regular pending invoices of subscriptions that are not already scheduled
        $regularDiff = $regular->whereNotIn('subscription_id', $scheduled->pluck('subscription_id'));

        // Sort by date and retrieve unique, to avoid messing up multiple pending subscription schedule changes

        // To Check when we have scheduled subscriptions
        //return $scheduled->merge($regularDiff)->sortBy('date', SORT_ASC)->unique('subscription_id')->all();

        return $regularDiff->merge($scheduled)->sortBy('date', SORT_ASC)->unique('subscription_id')->all();

    }

    /**
     * Get regular renewal invoices
     * @return mixed
     */
    private function getInvoices()
    {
        $pending = PlanSubscription::findPendingPayment($this->processUntilDate)->get();

        return $pending->map(function ($subscription) {
            return [
                'subscription_id' => $subscription->id,
                'collectable_type' => PlanSubscription::class,
                'collectable_id' => $subscription->id,
                'date' => $subscription->ends_at
            ];
        });
    }

    /**
     * Get scheduled invoices
     * @return mixed
     */
    private function getScheduledInvoices()
    {
        $pending = PlanSubscriptionSchedule::pending($this->processUntilDate)
            ->orderBy('scheduled_at', 'ASC')
            ->get();

        return $pending->map(function ($subscriptionSchedule) {
            return [
                'subscription_id' => $subscriptionSchedule->subscription_id,
                'collectable_type' => PlanSubscriptionSchedule::class,
                'collectable_id' => $subscriptionSchedule->id,
                'date' => $subscriptionSchedule->scheduled_at
            ];
        });
    }
}
