<?php

$aValidation = [
    'days_to_notify_expire' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Days to Notify Expiring Listing" must be greater or equal to 0'),
    ],
    'days_to_expire_listing' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Days to Expire" must be greater or equal to 0'),
    ],
];
