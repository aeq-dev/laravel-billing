<?php

declare(strict_types=1);

namespace Bkfdev\Billing\Services\PaymentMethods;

use Bkfdev\Billing\Contracts\PaymentMethodService;
use Bkfdev\Billing\Traits\IsPaymentMethod;

class Free implements PaymentMethodService
{
    use IsPaymentMethod;

    /**
     * Charge desired amount
     * @return void
     */
    public function charge()
    {
        // Nothing is charged, no exception is raised
    }
}
