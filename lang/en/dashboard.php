<?php

return [
    'title'         => 'Dashboard',
    'meta'          => 'Manage your subscriptions and top up credits on the Calendize Dashboard. Choose from Beginner, Classic, and Power plans to fit your needs. Modify, upgrade, or downgrade your subscription effortlessly.',
    'subscriptions' => [
        'title'                 => 'Subscription plans',
        'beginner'              => 'Beginner',
        'classic'               => 'Classic',
        'power'                 => 'Power',
        'credits-per-month'     => ':credits credits / month',
        'revolving-credits'     => ':credits revolving credits',
        'price'                 => ':price € / month',
        'subscribe'             => 'Subscribe',
        'modify'                => 'Modify',
        'upgrade'               => 'Upgrade',
        'downgrade'             => 'Downgrade',
        'current-subscription'  => 'Your subscription: :subscription',
        'change-payment-method' => 'Change payment method',
        'cancel'                => 'Cancel subscription',
        'renewal-date'          => 'Renewal date:',
        'end-date'              => 'End date:',
        'on-renewal'            => 'on renewal date',
        'now'                   => 'now',
    ],
    'top-up' => [
        'title'       => 'Top up credits',
        'buy-credits' => 'Buy credits',
        'top-up'      => 'Top up',
        'get-credits' => 'Get :credits credits',
        'expiration'  => 'expires in 1 year',
    ],
    'cancel-modal' => [
        'title'           => "Oh, you aren't happy with Calendize?",
        'tagline'         => "Are you really-really sure? I'll be so sad to see you go...",
        'downgrade-first' => 'How about downgrading first?',
        'come-back'       => 'Please come back soon!',
        'fill-in-email'   => 'If you still want to cancel - please fill in your email to confirm.',
        'lets-downgrade'  => "Yes, let's downgrade!",
    ],
    'popup' => [
        'beginner' => [
            'title'   => 'Your payment was successful!',
            'content' => 'Thanks! Beginner subscription is now active.',
        ],
        'classic' => [
            'title'   => 'Your payment was successful!',
            'content' => 'Thanks! Classic subscription is now active.',
        ],
        'power' => [
            'title'   => 'Your payment was successful!',
            'content' => 'Thanks! Power subscription is now active. We\'ve got a lot to do, right?',
        ],
        'credits' => [
            'title'   => 'Your payment was successful!',
            'content' => "Thanks! I'm looking forward to working with you!",
        ],
    ],
];
