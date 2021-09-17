<?php
$aValidation = [
    'poll_max_upload_size' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Max file size for poll photos" must be greater than or equal to 0'),
    ],
    'points_poll' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Activity points" must be greater than or equal to 0'),
    ],
    'maximum_answers_count' => [
        'def' => 'int:required',
        'min' => '2',
        'title' => _p('"Maximum answers" must be greater than or equal to 2'),
    ],
    'poll_flood_control' => [
        'def' => 'int',
        'min' => '0',
        'title' => _p('"Waiting time to add new poll" must be greater than or equal to 0'),
    ],
    'poll_sponsor_price' => [
        'def' => 'currency',
        'min' => '0',
        'title' => _p('"Sponsor poll price" must be greater than or equal to 0'),
    ],
];
