<?php

$aValidation = [
    'points_marketplace' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Activity points" must be greater than or equal to 0'),
    ],
    'flood_control_marketplace' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Waiting time to add new listing" must be greater than or equal to 0'),
    ],
    'max_upload_size_listing' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Max file size for listing photos" must be greater than or equal to 0'),
    ],
    'total_photo_upload_limit' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Total photos can upload to each listing" must be greater than or equal to 0'),
    ],
    'marketplace_sponsor_price' => [
        'def' => 'currency',
        'min' => '0',
        'title' => _p('"Sponsor listing price" must be greater than or equal to 0'),
    ],
];