<?php

namespace Bkfdev\Billing\Jobs;

use Bkfdev\Billing\Models\PlanSubscriptionSchedule;
use http\Exception\InvalidArgumentException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

class SubscriptionScheduleInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $service;
    private $planSubscriptionSchedule;

    public $tries = 1;
    public $timeout = 120;

    /**
     * Create a new job instance.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct($planSubscriptionScheduleId)
    {
        $this->planSubscriptionSchedule = PlanSubscriptionSchedule::find($planSubscriptionScheduleId);

        // Retrieve service name from config
        $invoiceMethod = config('billing.services.invoice_methods.' . $this->planSubscription->invoice_method);

        // Check if service exists in config file
        if (empty($invoiceMethod)) {
            throw new InvalidArgumentException('Selected invoice method does not exist', 401);
        }

        // Make service
        $this->service = app()->make($invoiceMethod);

        // Set options from service constants
        $this->tries = $this->service::TRIES;
        $this->timeout = $this->service::TIMEOUT;
    }

    // Avoid overlapping jobs to avoid any double invoice issues
    public function middleware()
    {
        return [(new WithoutOverlapping('subscription-invoice-' . $this->planSubscriptionSchedule->subscription_id))->dontRelease()];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->service
            ->schedule($this->planSubscriptionSchedule)
            ->amount($this->planSubscriptionSchedule->scheduleable->price)
            ->currency($this->planSubscriptionSchedule->scheduleable->currency)
            ->execute();
    }
}
