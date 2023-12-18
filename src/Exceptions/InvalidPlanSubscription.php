<?php

namespace Bkfdev\Billing\Exceptions;

class InvalidPlanSubscription extends LaravelBillingException
{
    public function __construct($subscriptionTag = "")
    {
        $message = "Subscription '{$subscriptionTag}' not found.";

        parent::__construct($message);
    }
}
