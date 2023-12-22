<?php

namespace Bkfdev\Billing\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Bkfdev\Billing\Models\PlanSubscription;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Bkfdev\Billing\Models\PlanSubscriptionSchedule;
use Bkfdev\Billing\Services\PendingInvoiceCollector;
use Bkfdev\Billing\Jobs\SubscriptionRenewalInvoiceJob;
use Bkfdev\Billing\Jobs\SubscriptionScheduleInvoiceJob;

class SubscriptionInvoiceQueuerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $processUntil;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(?Carbon $processUntil = null)
    {
        $this->processUntil = ($processUntil) ?? Carbon::now();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $pendingInvoiceCollector = new PendingInvoiceCollector();
        $pendingInvoices = $pendingInvoiceCollector->onDate($this->processUntil)->collectAllInvoices();

        foreach ($pendingInvoices as $pendingInvoice) {
            switch ($pendingInvoice['collectable_type']) {
                case PlanSubscription::class:
                    SubscriptionRenewalInvoiceJob::dispatch($pendingInvoice['collectable_id']);
                    break;
                case PlanSubscriptionSchedule::class:
                    SubscriptionScheduleInvoiceJob::dispatch($pendingInvoice['collectable_id']);
                    break;
            }
        }
    }
}
