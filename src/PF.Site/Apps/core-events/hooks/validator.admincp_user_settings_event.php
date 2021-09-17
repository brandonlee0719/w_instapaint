<?php

$aValidation = [
    'max_upload_size_event' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Max file size for event photos" must be greater or equal to 0'),
    ],
    'total_mass_emails_per_hour' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Waiting time to send out another mass email" must be greater or equal to 0'),
    ],
    'points_event' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Activity points" must be greater or equal to 0'),
    ],
    'flood_control_events' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Waiting time to add new event" must be greater or equal to 0'),
    ],
    'event_sponsor_price' => [
        'def' => 'currency',
        'min' => '0',
        'title' => _p('"Sponsor event price" must be greater than or equal to 0'),
    ],
];