<?php

declare(strict_types=1);

return [
    'main_subscription_tag' => 'main',
    'fallback_plan_tag' => null,
    // Database Tables
    'tables' => [
        'plans' => 'plans',
        'plan_combinations' => 'plan_combinations',
        'plan_features' => 'plan_features',
        'plan_subscriptions' => 'plan_subscriptions',
        'plan_subscription_features' => 'plan_subscription_features',
        'plan_subscription_schedules' => 'plan_subscription_schedules',
        'plan_subscription_usage' => 'plan_subscription_usage',
    ],

    // Models
    'models' => [
        'plan' => \Bkfdev\Billing\Models\Plan::class,
        'plan_combination' => \Bkfdev\Billing\Models\PlanCombination::class,
        'plan_feature' => \Bkfdev\Billing\Models\PlanFeature::class,
        'plan_subscription' => \Bkfdev\Billing\Models\PlanSubscription::class,
        'plan_subscription_feature' => \Bkfdev\Billing\Models\PlanSubscriptionFeature::class,
        'plan_subscription_schedule' => \Bkfdev\Billing\Models\PlanSubscriptionSchedule::class,
        'plan_subscription_usage' => \Bkfdev\Billing\Models\PlanSubscriptionUsage::class,
    ],

    'services' => [
        'payment_methods' => [
            'free' => \Bkfdev\Billing\Services\PaymentMethods\Free::class
        ]
    ]
];
