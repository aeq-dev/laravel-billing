<?php


namespace Bkfdev\Billing\Traits;


use Bkfdev\Billing\Services\Period;
use Carbon\Carbon;

trait HasResetDate
{
    /**
     * Get feature's reset date.
     *
     * @param Carbon|null $dateFrom
     *
     * @return \Carbon\Carbon
     * @throws \Exception
     */
    public function getResetDate(?Carbon $dateFrom = null): Carbon
    {
        $today = Carbon::now();

        do {
            $period = new Period($this->resettable_interval, $this->resettable_period, $dateFrom ?? $today);
            $dateFrom = $period->getEndDate();
        } while ($period->getEndDate()->lt($today));

        return $period->getEndDate();
    }
}
