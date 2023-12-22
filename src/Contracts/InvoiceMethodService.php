<?php


namespace Bkfdev\Billing\Contracts;


interface InvoiceMethodService
{
    const TRIES = 3;
    const TIMEOUT = 120;

    /**
     * Logic for charging the payment amount
     */
    public function generate();
}
