<?php

namespace Bkfdev\Billing\Exceptions;

class UsageDenied extends LaravelBillingException
{
    public function __construct($featureTag = '')
    {
        $message = "Usage of '{$featureTag}' has been denied.";

        parent::__construct($message);
    }
}
