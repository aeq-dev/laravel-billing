<?php

declare(strict_types=1);

namespace Bkfdev\Billing\Services\InvoiceMethods;

use Bkfdev\Billing\Traits\IsInvoiceMethod;
use Bkfdev\Billing\Contracts\InvoiceMethodService;


class Free implements InvoiceMethodService
{
    use IsInvoiceMethod;

    /**
     * Charge desired amount
     * @return void
     */
    public function generate()
    {
        // Nothing is charged, no exception is raised
    }
}
